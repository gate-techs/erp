<?php

namespace Modules\Academic\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Modules\Academic\Models\AcademicClass;
use Modules\Academic\Models\AcademicStudentAttendance;
use Modules\Academic\Repositories\Contracts\AcademicStudentAttendanceRepositoryInterface;

class AcademicStudentAttendanceService
{
    //------------------------------------------
    public function __construct(
        protected AcademicStudentAttendanceRepositoryInterface $repository
    ) {}

    //------------------------------------------
    /**
     * Get class roster with daily attendance status.
     * (عرض طلاب الفصل وحالة حضورهم اليومية)
     */
    public function getClassDailyRoster(int $classId, string $date): array
    {
        $class = AcademicClass::with(['translations', 'institution', 'branch'])->find($classId);
        if (! $class) {
            return [
                'success' => false,
                'message' => __('Classroom not found.'),
            ];
        }

        $students = $this->repository->getClassStudentsWithAttendance($classId, $date);

        $presentCount = $students->filter(fn ($s) => $s->daily_status === AcademicStudentAttendance::STATUS_ATTEND || $s->daily_status === AcademicStudentAttendance::STATUS_LATE)->count();
        $absentCount  = $students->filter(fn ($s) => $s->daily_status === AcademicStudentAttendance::STATUS_ABSENT)->count();
        $unrecorded   = $students->filter(fn ($s) => $s->daily_status === null)->count();

        return [
            'success'   => true,
            'class'     => $class,
            'date'      => $date,
            'stats'     => [
                'total'      => $students->count(),
                'present'    => $presentCount,
                'absent'     => $absentCount,
                'unrecorded' => $unrecorded,
            ],
            'students'  => $students,
        ];
    }

    //------------------------------------------
    /**
     * Bulk record attendance for a class.
     * (تسجيل الحضور الجماعي للفصل - Multi Attendance)
     */
    public function recordMultiAttendance(
        int $classId,
        string $date,
        array $records,
        ?int $recordedBy = null,
        ?int $institutionId = null,
        ?int $branchId = null
    ): array {
        $class = AcademicClass::find($classId);
        $institutionId = $institutionId ?? $class?->institution_id;
        $branchId      = $branchId ?? $class?->branch_id;

        $saved = $this->repository->recordMultiAttendance($classId, $date, $records, $recordedBy, $institutionId, $branchId);

        return [
            'success' => true,
            'message' => __('Attendance recorded successfully.'),
            'count'   => count($saved),
            'records' => $saved,
        ];
    }

    //------------------------------------------
    /**
     * Record single student attendance / absence.
     * (تسجيل غياب فردي)
     */
    public function recordSingleAttendance(
        int $studentId,
        int $classId,
        string $date,
        int $status,
        ?string $notes = null,
        ?int $recordedBy = null,
        ?int $institutionId = null,
        ?int $branchId = null
    ): array {
        $record = $this->repository->recordSingleAttendance(
            $studentId,
            $classId,
            $date,
            $status,
            $notes,
            $recordedBy,
            $institutionId,
            $branchId
        );

        return [
            'success' => true,
            'message' => __('Student attendance recorded successfully.'),
            'record'  => $record,
        ];
    }

    //------------------------------------------
    /**
     * Get students attendance report query for DataTables.
     * (تقرير حضور الطلاب)
     */
    public function getStudentsAttendanceReportQuery(array $filters = []): Builder
    {
        return $this->repository->getStudentsAttendanceReportQuery($filters);
    }

    //------------------------------------------
    /**
     * Get paginated filtered attendance list.
     * (استعراض سجلات الحضور والفلاتر)
     */
    public function getFilteredList(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        return $this->repository->getFilteredPaginated($filters, $perPage);
    }

    //------------------------------------------
    /**
     * Approve selected attendance records.
     * (اعتماد الحضور - Approve)
     */
    public function approve(array $ids, int $approvedBy, ?string $notes = null): array
    {
        if (empty($ids)) {
            return [
                'success' => false,
                'message' => __('No attendance records selected.'),
            ];
        }

        $count = $this->repository->approveAttendance($ids, $approvedBy, $notes);

        return [
            'success' => true,
            'message' => __('Attendance approved successfully.'),
            'count'   => $count,
        ];
    }

    //------------------------------------------
    /**
     * Reject or revoke approval of attendance records.
     * (رفض الحضور - Not Approved)
     */
    public function reject(array $ids, int $rejectedBy, ?string $reason = null): array
    {
        if (empty($ids)) {
            return [
                'success' => false,
                'message' => __('No attendance records selected.'),
            ];
        }

        $count = $this->repository->rejectAttendance($ids, $rejectedBy, $reason);

        return [
            'success' => true,
            'message' => __('Attendance approval revoked/rejected successfully.'),
            'count'   => $count,
        ];
    }

    //------------------------------------------
    /**
     * Get student attendance report.
     * (تقرير الطالب: يسترجع إجمالي الأيام، إجمالي أيام الحضور، وإجمالي أيام الغياب)
     */
    public function getStudentReport(int $studentId, ?string $startDate = null, ?string $endDate = null): array
    {
        $report = $this->repository->getStudentAttendanceSummary($studentId, $startDate, $endDate);

        if (empty($report)) {
            return [
                'success' => false,
                'message' => __('Student not found or has no records.'),
            ];
        }

        return array_merge(['success' => true], $report);
    }

    //------------------------------------------
    /**
     * Get guardian attendance report for all linked students.
     * (تقرير ولي الأمر)
     */
    public function getGuardianReport(int $guardianId, ?string $startDate = null, ?string $endDate = null): array
    {
        $report = $this->repository->getGuardianStudentsAttendanceSummary($guardianId, $startDate, $endDate);

        if (empty($report)) {
            return [
                'success' => false,
                'message' => __('Guardian not found or has no linked students.'),
            ];
        }

        return array_merge(['success' => true], $report);
    }
}
