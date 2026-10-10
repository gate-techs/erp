<?php

namespace Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Academic\Http\Requests\StoreAcademicFeePaymentRequest;
use Modules\Academic\Http\Requests\StoreAcademicStudentFeeRequest;
use Modules\Academic\Http\Requests\UpdateAcademicFeePaymentRequest;
use Modules\Academic\Http\Requests\UpdateAcademicStudentFeeRequest;
use Modules\Academic\Models\AcademicFeePayment;
use Modules\Academic\Models\AcademicStudent;
use Modules\Academic\Models\AcademicStudentFee;
use Modules\Academic\Services\AcademicStudentFeeService;
use Yajra\DataTables\Facades\DataTables;

class AcademicStudentFeeController extends Controller
{
    //------------------------------------------
    public function __construct(
        protected AcademicStudentFeeService $service
    ) {}

    //------------------------------------------
    public function index()
    {
        $students = AcademicStudent::with('translations')
            ->whereIn('status', [AcademicStudent::STATUS_ACTIVE, AcademicStudent::STATUS_PENDING])
            ->latest('id')
            ->get();
        $fees = \Modules\Academic\Models\AcademicFee::with(['translations', 'feeType.translations'])
            ->where('status', 'active')
            ->latest('id')
            ->get();

        return view('academic::student-fees.index', compact('students', 'fees'));
    }

    public function paymentsIndex(Request $request)
    {
        $studentFeeId = $request->integer('student_fee_id');
        $studentFee = $studentFeeId ? $this->service->find($studentFeeId) : null;
        $studentFees = $this->service->all();

        return view('academic::student-fee-payments.index', compact('studentFee', 'studentFeeId', 'studentFees'));
    }

    public function paymentsData(Request $request): JsonResponse
    {
        $filters = [
            'academic_student_fee_id' => $request->integer('academic_student_fee_id') ?: $request->integer('student_fee_id'),
            'status' => $request->input('status'),
        ];

        return DataTables::eloquent($this->service->getPaymentsQuery($filters))
            ->addIndexColumn()
            ->addColumn('student_name', function ($payment) {
                $name = $payment->studentFee?->student?->translate(app()->getLocale())?->name
                    ?? $payment->studentFee?->student?->translate('ar')?->name
                    ?? $payment->studentFee?->student?->name
                    ?? '-';
                return e($name);
            })
            ->addColumn('fee_name', function ($payment) {
                return e($payment->studentFee?->fee?->translate(app()->getLocale())?->name
                    ?? $payment->studentFee?->fee?->translate('ar')?->name
                    ?? $payment->studentFee?->fee?->name
                    ?? '-');
            })
            ->addColumn('installment_name', fn ($payment) => $payment->installment
                ? e(__('دفعة') . ' #' . $payment->installment->installment_number)
                : '-')
            ->addColumn('raw_amount', fn ($payment) => (float) $payment->amount)
            ->editColumn('amount', fn ($payment) => '<span class="fw-bold text-heading">' . number_format((float) $payment->amount, 2) . '</span>')
            ->editColumn('payment_date', fn ($payment) => $payment->payment_date?->format('Y-m-d') ?? '-')
            ->addColumn('status_badge', function ($payment) {
                $map = [
                    'paid' => ['Paid', 'bg-label-success'],
                    'unpaid' => ['غير مدفوع', 'bg-label-warning'],
                    'pending' => ['غير مدفوع', 'bg-label-warning'],
                ];
                [$label, $class] = $map[$payment->status] ?? [ucfirst($payment->status), 'bg-label-secondary'];
                return '<button type="button" class="btn btn-sm ' . $class . ' btn-toggle-payment-status" data-id="' . $payment->id . '">'
                    . e(__($label)) . '</button>';
            })
            ->addColumn('actions', fn ($payment) => '<div class="d-inline-flex gap-1">'
                . '<button type="button" class="btn btn-icon btn-sm btn-outline-info btn-payment-details" data-id="' . $payment->id . '" title="' . e(__('Details')) . '"><i class="icon-base bx bx-show"></i></button>'
                . '<button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-payment" data-id="' . $payment->id . '" title="' . e(__('Edit')) . '"><i class="icon-base bx bx-edit"></i></button>'
                . '<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-payment" data-id="' . $payment->id . '" title="' . e(__('Delete')) . '"><i class="icon-base bx bx-trash"></i></button>'
                . '</div>')
            ->rawColumns(['amount', 'status_badge', 'actions'])
            ->toJson();
    }

    //------------------------------------------
    public function data(Request $request): JsonResponse
    {
        $query = $this->service->getFilteredQuery($request->only([
            'academic_student_id',
            'academic_fee_id',
            'status',
        ]));

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('student_name', function (AcademicStudentFee $assignment) {
                $name = $assignment->student?->translate(app()->getLocale())?->name
                    ?? $assignment->student?->translate('ar')?->name
                    ?? $assignment->student?->name
                    ?? '-';
                return '<a href="' . route('academic.students.show', $assignment->academic_student_id) . '" class="fw-semibold text-heading">' . e($name) . '</a>';
            })
            ->addColumn('fee_name', function (AcademicStudentFee $assignment) {
                $name = $assignment->fee?->translate(app()->getLocale())?->name
                    ?? $assignment->fee?->translate('ar')?->name
                    ?? $assignment->fee?->name
                    ?? '-';
                return e($name);
            })
            ->addColumn('installments_summary', function (AcademicStudentFee $assignment) {
                return '<a href="' . route('academic.student-fees.payments.index', ['student_fee_id' => $assignment->id]) . '" class="btn btn-sm btn-label-info">'
                    . '<i class="icon-base bx bx-list-ol me-1"></i>' . (int) $assignment->installments_count . ' ' . e(__('المدفوعات')) . '</a>';
            })
            ->addColumn('amount_summary', function (AcademicStudentFee $assignment) {
                $paid = (float) $assignment->payments->where('status', AcademicFeePayment::STATUS_PAID)->sum('amount');
                $remaining = max(0, (float) $assignment->total_amount - $paid);
                return '<a href="' . route('academic.student-fees.payments.index', ['student_fee_id' => $assignment->id]) . '" class="btn btn-link p-0 text-decoration-none text-start">'
                    . '<span class="fw-bold text-heading">' . number_format((float) $assignment->total_amount, 2) . '</span>'
                    . '<span class="d-block small text-muted">' . e(__('Remaining')) . ': ' . number_format($remaining, 2) . '</span>'
                    . '</a>';
            })
            ->addColumn('start_date', fn (AcademicStudentFee $assignment) => $assignment->start_date?->format('Y-m-d') ?? '-')
            ->addColumn('status_badge', function (AcademicStudentFee $assignment) {
                $map = [
                    AcademicStudentFee::STATUS_ACTIVE => ['Active', 'bg-label-primary'],
                    AcademicStudentFee::STATUS_COMPLETED => ['Completed', 'bg-label-success'],
                    AcademicStudentFee::STATUS_CANCELLED => ['Cancelled', 'bg-label-danger'],
                ];
                [$label, $class] = $map[$assignment->status] ?? [ucfirst($assignment->status), 'bg-label-secondary'];
                return '<span class="badge ' . $class . '">' . e(__($label)) . '</span>';
            })
            ->addColumn('actions', function (AcademicStudentFee $assignment) {
                return '<div class="d-inline-flex align-items-center gap-1">'
                    . '<button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-student-fee" data-id="' . $assignment->id . '" title="' . e(__('Edit')) . '"><i class="icon-base bx bx-edit"></i></button>'
                    . '<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-student-fee" data-id="' . $assignment->id . '" title="' . e(__('Delete')) . '"><i class="icon-base bx bx-trash"></i></button>'
                    . '</div>';
            })
            ->rawColumns(['student_name', 'installments_summary', 'amount_summary', 'status_badge', 'actions'])
            ->toJson();
    }

    //------------------------------------------
    public function store(StoreAcademicStudentFeeRequest $request): JsonResponse
    {
        $assignment = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => __('Student fee assigned successfully.'),
            'data' => $assignment,
        ], 201);
    }

    //------------------------------------------
    public function show(int $id): JsonResponse
    {
        $assignment = $this->service->find($id);
        abort_unless($assignment, 404, __('Student fee assignment not found.'));

        return response()->json(['success' => true, 'data' => $assignment]);
    }

    public function update(UpdateAcademicStudentFeeRequest $request, int $id): JsonResponse
    {
        $assignment = $this->service->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => __('Student fee updated successfully.'),
            'data' => $assignment,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => __('Student fee deleted successfully.'),
        ]);
    }

    //------------------------------------------
    public function storePayment(StoreAcademicFeePaymentRequest $request, int $id): JsonResponse
    {
        $payment = $this->service->addPayment($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => __('Payment recorded successfully.'),
            'data' => $payment,
        ], 201);
    }

    public function updatePayment(UpdateAcademicFeePaymentRequest $request, int $paymentId): JsonResponse
    {
        $payment = $this->service->updatePayment($paymentId, $request->validated());

        return response()->json([
            'success' => true,
            'message' => __('Payment updated successfully.'),
            'data' => $payment,
        ]);
    }

    public function destroyPayment(int $paymentId): JsonResponse
    {
        $this->service->deletePayment($paymentId);

        return response()->json([
            'success' => true,
            'message' => __('Payment deleted successfully.'),
        ]);
    }

    public function togglePaymentStatus(int $paymentId): JsonResponse
    {
        $payment = $this->service->togglePaymentStatus($paymentId);

        return response()->json([
            'success' => true,
            'message' => __('Payment status updated successfully.'),
            'data' => $payment,
        ]);
    }
}
