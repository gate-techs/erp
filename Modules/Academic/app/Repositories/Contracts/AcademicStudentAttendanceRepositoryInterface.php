<?php

namespace Modules\Academic\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\AcademicStudentAttendance;

interface AcademicStudentAttendanceRepositoryInterface
{
    //------------------------------------------
    /**
     * Get attendance records for a specific class on a given date.
     */
    public function getByClassAndDate(int $classId, string $date, ?int $institutionId = null): Collection;

    //------------------------------------------
    /**
     * Get all active students of a class with their attendance status for a given date.
     * (عرض طلاب الفصل وحالة حضورهم اليومية)
     */
    public function getClassStudentsWithAttendance(int $classId, string $date): Collection;

    //------------------------------------------
    /**
     * Bulk record attendance for class students.
     * (تسجيل الحضور الجماعي للفصل - Multi Attendance)
     *
     * @param array $records Each item: ['student_id' => int, 'status' => int, 'notes' => ?string]
     */
    public function recordMultiAttendance(int $classId, string $date, array $records, ?int $recordedBy = null, ?int $institutionId = null, ?int $branchId = null): array;

    //------------------------------------------
    /**
     * Record or update single student attendance / absence.
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
    ): AcademicStudentAttendance;

    //------------------------------------------
    /**
     * Get students attendance report query for DataTables.
     * (تقرير حضور الطلاب)
     */
    public function getStudentsAttendanceReportQuery(array $filters = []): Builder;

    //------------------------------------------
    /**
     * Get filtered attendance query.
     * (استعراض سجلات الحضور والفلاتر)
     */
    public function getFilteredQuery(array $filters = []): Builder;

    //------------------------------------------
    /**
     * Get paginated filtered attendance records.
     */
    public function getFilteredPaginated(array $filters = [], int $perPage = 25): LengthAwarePaginator;

    //------------------------------------------
    /**
     * Approve attendance records.
     * (اعتماد الحضور - Approve)
     *
     * @param int[] $ids
     */
    public function approveAttendance(array $ids, int $approvedBy, ?string $notes = null): int;

    //------------------------------------------
    /**
     * Reject or un-approve attendance records.
     * (رفض الحضور - Not Approved)
     *
     * @param int[] $ids
     */
    public function rejectAttendance(array $ids, int $rejectedBy, ?string $reason = null): int;

    //------------------------------------------
    /**
     * Get student attendance report summary (total_days, present_days, absent_days, attendance_rate).
     * (تقرير الطالب)
     */
    public function getStudentAttendanceSummary(int $studentId, ?string $startDate = null, ?string $endDate = null): array;

    //------------------------------------------
    /**
     * Get guardian's students attendance report.
     * (تقرير ولي الأمر)
     */
    public function getGuardianStudentsAttendanceSummary(int $guardianId, ?string $startDate = null, ?string $endDate = null): array;

    //------------------------------------------
    /**
     * Find single record by ID.
     */
    public function find(int $id): ?AcademicStudentAttendance;

    //------------------------------------------
    /**
     * Delete record by ID.
     */
    public function delete(int $id): bool;
}
