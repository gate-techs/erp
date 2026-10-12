<?php

namespace Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Academic\Models\AcademicFee;
use Modules\Academic\Models\AcademicFeeInstallment;
use Modules\Academic\Models\AcademicFeePayment;
use Modules\Academic\Models\AcademicFeeType;
use Modules\Academic\Models\AcademicStudentFee;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;

class AcademicFeeStatisticsController extends Controller
{
    public function index()
    {
        $institutions = Nursery::with('translations')->where('status', 'active')->get();
        $branches = NurseryBranch::with('translations')->where('status', 'active')->get();
        $statuses = AcademicFeeType::statuses();
        $data = $this->calculateStatistics();

        return view('academic::fee-statistics.index', [
            'institutions' => $institutions,
            'branches' => $branches,
            'statuses' => $statuses,
            'stats' => $data['summary'],
            'feeTypesBreakdown' => $data['fee_types_breakdown'],
            'overdueList' => $data['overdue_list'],
            'recentPayments' => $data['recent_payments'],
            'chartData' => $data['chart_data'],
        ]);
    }

    public function stats(Request $request): JsonResponse
    {
        $filters = $request->only(['institution_id', 'branch_id', 'status']);
        $data = $this->calculateStatistics($filters);

        return response()->json([
            'success' => true,
            'data' => $data['summary'],
            'fee_types_breakdown' => $data['fee_types_breakdown'],
            'overdue_list' => $data['overdue_list'],
            'recent_payments' => $data['recent_payments'],
            'chart_data' => $data['chart_data'],
        ]);
    }

    protected function calculateStatistics(array $filters = []): array
    {
        $typeQuery = AcademicFeeType::query();

        if (! empty($filters['institution_id'])) {
            $typeQuery->where('institution_id', $filters['institution_id']);
        }
        if (! empty($filters['branch_id'])) {
            $typeQuery->where('branch_id', $filters['branch_id']);
        }
        if (! empty($filters['status'])) {
            $typeQuery->where('status', $filters['status']);
        }

        $typeIds = (clone $typeQuery)->pluck('id');
        $feeQuery = AcademicFee::query()->whereIn('academic_fee_type_id', $typeIds);
        $feeIds = (clone $feeQuery)->pluck('id');
        $studentFeeQuery = AcademicStudentFee::query()->whereIn('academic_fee_id', $feeIds);
        $studentFeeIds = (clone $studentFeeQuery)->select('id');

        $totalFees = (float) (clone $studentFeeQuery)->sum('total_amount');
        $paidFees = (float) AcademicFeePayment::query()
            ->whereIn('academic_student_fee_id', $studentFeeIds)
            ->where('status', AcademicFeePayment::STATUS_PAID)
            ->sum('amount');
        $paidPaymentsQuery = AcademicFeePayment::query()
            ->whereIn('academic_student_fee_id', $studentFeeIds)
            ->where('status', AcademicFeePayment::STATUS_PAID);
        $collectionRate = $totalFees > 0 ? round(($paidFees / $totalFees) * 100, 1) : 0;

        $overdueQuery = AcademicFeeInstallment::query()
            ->whereIn('academic_student_fee_id', $studentFeeIds)
            ->whereDate('due_date', '<', now()->toDateString())
            ->whereColumn('paid_amount', '<', 'amount');

        $overdueAmount = (float) (clone $overdueQuery)->selectRaw('COALESCE(SUM(amount - paid_amount), 0) as total')->value('total');
        $remainingFees = max(0, round($totalFees - $paidFees, 2));

        // Detailed breakdown by fee type
        $feeTypesBreakdown = (clone $typeQuery)
            ->with(['translations'])
            ->withCount(['fees as plans_count'])
            ->get()
            ->map(function ($type) {
                $sfQuery = AcademicStudentFee::whereHas('fee', function ($q) use ($type) {
                    $q->where('academic_fee_type_id', $type->id);
                });

                $typeTotal = (float) (clone $sfQuery)->sum('total_amount');
                $typeStudents = (clone $sfQuery)->distinct('academic_student_id')->count('academic_student_id');
                $typePaid = (float) AcademicFeePayment::whereHas('studentFee.fee', function ($q) use ($type) {
                    $q->where('academic_fee_type_id', $type->id);
                })->where('status', AcademicFeePayment::STATUS_PAID)->sum('amount');

                return [
                    'id' => $type->id,
                    'name' => $type->name ?? ($type->code ?: 'N/A'),
                    'code' => $type->code,
                    'status' => $type->status,
                    'plans_count' => $type->plans_count,
                    'students_count' => $typeStudents,
                    'total_amount' => round($typeTotal, 2),
                    'paid_amount' => round($typePaid, 2),
                    'remaining_amount' => max(0, round($typeTotal - $typePaid, 2)),
                    'collection_rate' => $typeTotal > 0 ? round(($typePaid / $typeTotal) * 100, 1) : 0,
                ];
            });

        // Top Overdue Installments
        $overdueList = (clone $overdueQuery)
            ->with([
                'studentFee.student.translations',
                'studentFee.fee.feeType.translations',
            ])
            ->orderBy('due_date', 'asc')
            ->take(6)
            ->get()
            ->map(function ($inst) {
                $student = $inst->studentFee?->student;
                $feeType = $inst->studentFee?->fee?->feeType;

                return [
                    'id' => $inst->id,
                    'installment_number' => $inst->installment_number,
                    'student_name' => $student?->name ?? __('Student') . ' #' . ($student?->id ?? '-'),
                    'student_code' => $student?->student_code ?? '',
                    'fee_type_name' => $feeType?->name ?? ($feeType?->code ?? '-'),
                    'due_date' => $inst->due_date ? $inst->due_date->format('Y-m-d') : '-',
                    'amount' => round((float) $inst->amount, 2),
                    'paid_amount' => round((float) $inst->paid_amount, 2),
                    'remaining' => max(0, round((float) ($inst->amount - $inst->paid_amount), 2)),
                ];
            });

        // Recent Payments / Transactions
        $recentPayments = AcademicFeePayment::query()
            ->whereIn('academic_student_fee_id', $studentFeeIds)
            ->with([
                'studentFee.student.translations',
                'studentFee.fee.feeType.translations',
            ])
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($payment) {
                $student = $payment->studentFee?->student;
                $feeType = $payment->studentFee?->fee?->feeType;

                return [
                    'id' => $payment->id,
                    'payment_number' => $payment->payment_number,
                    'student_name' => $student?->name ?? __('Student') . ' #' . ($student?->id ?? '-'),
                    'fee_type_name' => $feeType?->name ?? '-',
                    'amount' => round((float) $payment->amount, 2),
                    'payment_date' => $payment->payment_date ? $payment->payment_date->format('Y-m-d') : '-',
                    'status' => $payment->status,
                ];
            });

        // Chart Data
        $chartCategories = [];
        $chartTotals = [];
        $chartPaids = [];
        foreach ($feeTypesBreakdown as $item) {
            $chartCategories[] = $item['name'];
            $chartTotals[] = $item['total_amount'];
            $chartPaids[] = $item['paid_amount'];
        }

        $chartData = [
            'collection_chart' => [
                'series' => [
                    round($paidFees, 2),
                    max(0, round($remainingFees - $overdueAmount, 2)),
                    round($overdueAmount, 2),
                ],
                'labels' => [
                    __('Collected'),
                    __('Pending / Upcoming'),
                    __('Overdue'),
                ],
            ],
            'fee_types_chart' => [
                'categories' => $chartCategories,
                'total_series' => $chartTotals,
                'paid_series' => $chartPaids,
            ],
        ];

        return [
            'summary' => [
                'fee_types' => (clone $typeQuery)->count(),
                'active_fee_types' => (clone $typeQuery)->where('status', AcademicFeeType::STATUS_ACTIVE)->count(),
                'fee_plans' => (clone $feeQuery)->count(),
                'assigned_students' => (clone $studentFeeQuery)->distinct('academic_student_id')->count('academic_student_id'),
                'total_fees' => round($totalFees, 2),
                'paid_fees' => round($paidFees, 2),
                'remaining_fees' => $remainingFees,
                'collection_rate' => $collectionRate,
                'overdue_installments' => (clone $overdueQuery)->count(),
                'overdue_amount' => round($overdueAmount, 2),
                'unpaid_installments' => (clone $studentFeeQuery)->join('academic_fee_installments', 'academic_student_fees.id', '=', 'academic_fee_installments.academic_student_fee_id')->whereColumn('academic_fee_installments.paid_amount', '<', 'academic_fee_installments.amount')->count(),
                'paid_payments' => (clone $paidPaymentsQuery)->count(),
                'completed_assignments' => (clone $studentFeeQuery)->where('status', AcademicStudentFee::STATUS_COMPLETED)->count(),
            ],
            'fee_types_breakdown' => $feeTypesBreakdown,
            'overdue_list' => $overdueList,
            'recent_payments' => $recentPayments,
            'chart_data' => $chartData,
        ];
    }
}
