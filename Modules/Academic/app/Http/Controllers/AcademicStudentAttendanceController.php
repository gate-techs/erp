<?php

namespace Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Academic\Models\AcademicClass;
use Modules\Academic\Models\AcademicStudent;
use Modules\Academic\Services\AcademicStudentAttendanceService;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;
use Yajra\DataTables\Facades\DataTables;

class AcademicStudentAttendanceController extends Controller
{
    //------------------------------------------
    public function __construct(
        protected AcademicStudentAttendanceService $service
    ) {}

    //------------------------------------------
    /**
     * Display student attendance report view.
     * (تقرير حضور الطلاب)
     */
    public function index(Request $request)
    {
        $institutions = Nursery::with('translations')->where('status', 'active')->get();
        if ($institutions->isEmpty()) {
            $institutions = Nursery::with('translations')->get();
        }

        $branches = NurseryBranch::with('translations')->where('status', 'active')->get();
        if ($branches->isEmpty()) {
            $branches = NurseryBranch::with('translations')->get();
        }

        $classes = AcademicClass::with('translations')->where('status', 'active')->get();
        if ($classes->isEmpty()) {
            $classes = AcademicClass::with('translations')->get();
        }

        $defaultStartDate = now()->subMonth()->toDateString();
        $defaultEndDate   = now()->toDateString();

        return view('academic::attendance.index', compact(
            'institutions',
            'branches',
            'classes',
            'defaultStartDate',
            'defaultEndDate'
        ));
    }

    //------------------------------------------
    /**
     * DataTables AJAX endpoint for student attendance report.
     * (تقرير حضور الطلاب - بيانات الجدول)
     */
    public function data(Request $request): JsonResponse
    {
        $filters = [
            'institution_id'    => $request->input('institution_id'),
            'branch_id'         => $request->input('branch_id'),
            'academic_class_id' => $request->input('academic_class_id'),
            'start_date'        => $request->input('start_date') ?: now()->subMonth()->toDateString(),
            'end_date'          => $request->input('end_date') ?: now()->toDateString(),
            'search'            => $request->input('search.value') ?? $request->input('search'),
        ];

        $query = $this->service->getStudentsAttendanceReportQuery($filters);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('identifier', function (AcademicStudent $student) {
                return '<span class="font-monospace fw-semibold text-body">#' . e($student->student_code ?? $student->id) . '</span>';
            })
            ->addColumn('student', function (AcademicStudent $student) {
                $locale = app()->getLocale();
                $name   = $student->translate($locale)?->name
                    ?? $student->translate('ar')?->name
                    ?? $student->name
                    ?? '-';
                $photo  = e($student->photo_url);

                return '
                    <div class="d-flex align-items-center gap-3">
                        <img src="' . $photo . '" alt="' . e($name) . '" class="rounded-circle shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                        <div>
                            <span class="fw-semibold text-heading d-block">' . e($name) . '</span>
                        </div>
                    </div>';
            })
            ->addColumn('civil_id', function (AcademicStudent $student) {
                return $student->civil_id
                    ? '<span class="font-monospace fw-medium text-body">' . e($student->civil_id) . '</span>'
                    : '<span class="text-muted">-</span>';
            })
            ->addColumn('class_name', function (AcademicStudent $student) {
                $locale = app()->getLocale();
                $className = $student->class?->translate($locale)?->name ?? $student->class?->name ?? '-';
                return '<span class="badge bg-label-success">' . e($className) . '</span>';
            })
            ->addColumn('academic_year_name', function (AcademicStudent $student) {
                $locale = app()->getLocale();
                $yearName = $student->academicYear?->translate($locale)?->name ?? $student->academicYear?->name ?? '-';
                return '<span class="badge bg-label-secondary">' . e($yearName) . '</span>';
            })
            ->addColumn('branch_name', function (AcademicStudent $student) {
                $locale = app()->getLocale();
                $branchName = $student->branch?->translate($locale)?->name ?? $student->branch?->name ?? '-';
                return '<span class="badge bg-label-danger">' . e($branchName) . '</span>';
            })
            ->addColumn('institution_name', function (AcademicStudent $student) {
                $locale = app()->getLocale();
                $institutionName = $student->institution?->translate($locale)?->name ?? $student->institution?->name ?? '-';
                return '<span class="badge bg-label-primary">' . e($institutionName) . '</span>';
            })
            ->addColumn('total_days', function (AcademicStudent $student) {
                return '<span class="badge bg-label-info fw-bold">' . (int) $student->total_days . '</span>';
            })
            ->addColumn('present_days', function (AcademicStudent $student) {
                return '<span class="badge bg-label-success fw-bold">' . (int) $student->present_days . '</span>';
            })
            ->addColumn('absent_days', function (AcademicStudent $student) {
                return '<span class="badge bg-label-danger fw-bold">' . (int) $student->absent_days . '</span>';
            })
            ->addColumn('actions', function (AcademicStudent $student) {
                $locale = app()->getLocale();
                $name   = $student->translate($locale)?->name ?? $student->name ?? '';
                return '
                    <div class="d-flex align-items-center justify-content-center">
                        <button type="button" class="btn btn-icon btn-sm btn-label-primary btn-view-history"
                                data-student-id="' . $student->id . '"
                                data-student-name="' . e($name) . '"
                                title="' . __('Attendance Log') . '">
                            <i class="icon-base bx bx-history font-18"></i>
                        </button>
                    </div>';
            })
            ->rawColumns(['identifier', 'student', 'civil_id', 'class_name', 'academic_year_name', 'branch_name', 'institution_name', 'total_days', 'present_days', 'absent_days', 'actions'])
            ->make(true);
    }

    //------------------------------------------
    /**
     * Get class roster with daily attendance status.
     * (عرض طلاب الفصل وحالة حضورهم اليومية)
     */
    public function classRoster(Request $request, int $classId): JsonResponse
    {
        $date = $request->input('date', now()->toDateString());

        $data = $this->service->getClassDailyRoster($classId, $date);

        return response()->json($data);
    }

    //------------------------------------------
    /**
     * Bulk record attendance for class.
     * (تسجيل الحضور الجماعي للفصل - Multi Attendance)
     */
    public function storeMulti(Request $request): JsonResponse
    {
        $request->validate([
            'classroom_id'                  => ['nullable', 'integer', 'exists:academic_classes,id'],
            'academic_class_id'             => ['nullable', 'integer', 'exists:academic_classes,id'],
            'date'                          => ['required', 'date'],
            'records'                       => ['required', 'array'],
            'records.*.student_id'          => ['nullable', 'integer', 'exists:academic_students,id'],
            'records.*.academic_student_id' => ['nullable', 'integer', 'exists:academic_students,id'],
            'records.*.status'              => ['required', 'integer', 'in:0,1,2,3'],
            'records.*.notes'               => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $classId = (int) ($request->input('academic_class_id') ?? $request->input('classroom_id'));
            $date    = $request->input('date');
            $records = $request->input('records');

            $result = $this->service->recordMultiAttendance(
                classId: $classId,
                date: $date,
                records: $records,
                recordedBy: auth()->id(),
                institutionId: $request->input('institution_id'),
                branchId: $request->input('branch_id')
            );

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error recording attendance: ') . $e->getMessage(),
            ], 500);
        }
    }

    //------------------------------------------
    /**
     * Record single attendance / absence.
     * (تسجيل غياب فردي)
     */
    public function storeSingle(Request $request): JsonResponse
    {
        $request->validate([
            'student_id'          => ['nullable', 'integer', 'exists:academic_students,id'],
            'academic_student_id' => ['nullable', 'integer', 'exists:academic_students,id'],
            'classroom_id'        => ['nullable', 'integer', 'exists:academic_classes,id'],
            'academic_class_id'   => ['nullable', 'integer', 'exists:academic_classes,id'],
            'date'                => ['required', 'date'],
            'status'              => ['required', 'integer', 'in:0,1,2,3'],
            'notes'               => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $studentId = (int) ($request->input('academic_student_id') ?? $request->input('student_id'));
            $classId   = (int) ($request->input('academic_class_id') ?? $request->input('classroom_id'));
            $date      = $request->input('date');
            $status    = (int) $request->input('status');
            $notes     = $request->input('notes');

            $result = $this->service->recordSingleAttendance(
                studentId: $studentId,
                classId: $classId,
                date: $date,
                status: $status,
                notes: $notes,
                recordedBy: auth()->id(),
                institutionId: $request->input('institution_id'),
                branchId: $request->input('branch_id')
            );

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error recording single attendance: ') . $e->getMessage(),
            ], 500);
        }
    }

    //------------------------------------------
    /**
     * Approve attendance records.
     * (اعتماد الحضور - Approve)
     */
    public function approve(Request $request): JsonResponse
    {
        $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer', 'exists:academic_student_attendances,id'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $ids   = $request->input('ids');
            $notes = $request->input('notes');

            $result = $this->service->approve($ids, auth()->id() ?? 1, $notes);

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error approving attendance: ') . $e->getMessage(),
            ], 500);
        }
    }

    //------------------------------------------
    /**
     * Reject or revoke approval of attendance records.
     * (رفض الحضور - Not Approved)
     */
    public function reject(Request $request): JsonResponse
    {
        $request->validate([
            'ids'    => ['required', 'array'],
            'ids.*'  => ['integer', 'exists:academic_student_attendances,id'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $ids    = $request->input('ids');
            $reason = $request->input('reason');

            $result = $this->service->reject($ids, auth()->id() ?? 1, $reason);

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error rejecting attendance: ') . $e->getMessage(),
            ], 500);
        }
    }

    //------------------------------------------
    /**
     * Student attendance report.
     * (تقرير الطالب: إجمالي الأيام، إجمالي أيام الحضور، وإجمالي أيام الغياب)
     */
    public function studentReport(Request $request, int $studentId): JsonResponse
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        $report = $this->service->getStudentReport($studentId, $startDate, $endDate);

        return response()->json($report);
    }

    //------------------------------------------
    /**
     * Guardian students attendance report.
     * (تقرير ولي الأمر)
     */
    public function guardianReport(Request $request, int $guardianId): JsonResponse
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        $report = $this->service->getGuardianReport($guardianId, $startDate, $endDate);

        return response()->json($report);
    }
}
