<?php

namespace Modules\Academic\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Academic\Models\AcademicGuardian;
use Modules\Academic\Models\AcademicStudent;
use Modules\Academic\Models\AcademicStudentAttendance;
use Modules\Academic\Repositories\Contracts\AcademicStudentAttendanceRepositoryInterface;

class AcademicStudentAttendanceRepository implements AcademicStudentAttendanceRepositoryInterface
{
    //------------------------------------------
    /**
     * Get attendance records for a specific class on a given date.
     */
    public function getByClassAndDate(int $classId, string $date, ?int $institutionId = null): Collection
    {
        $query = AcademicStudentAttendance::with(['student.translations', 'class.translations'])
            ->where('academic_class_id', $classId)
            ->whereDate('date', $date);

        if ($institutionId) {
            $query->where('institution_id', $institutionId);
        }

        return $query->get();
    }

    //------------------------------------------
    /**
     * Get all active students of a class with their attendance status for a given date.
     * (عرض طلاب الفصل وحالة حضورهم اليومية)
     */
    public function getClassStudentsWithAttendance(int $classId, string $date): Collection
    {
        $students = AcademicStudent::with(['translations', 'class.translations'])
            ->where('academic_class_id', $classId)
            ->where('status', AcademicStudent::STATUS_ACTIVE)
            ->orderBy('id', 'asc')
            ->get();

        $attendances = AcademicStudentAttendance::where('academic_class_id', $classId)
            ->whereDate('date', $date)
            ->get()
            ->keyBy('academic_student_id');

        return $students->map(function ($student) use ($attendances) {
            $att = $attendances->get($student->id);
            $student->attendance   = $att;
            $student->daily_status = $att ? $att->status : null;
            $student->is_approved  = $att ? $att->approved : false;
            $student->notes        = $att?->notes;

            return $student;
        });
    }

    //------------------------------------------
    /**
     * Bulk record attendance for class students.
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
        return DB::transaction(function () use ($classId, $date, $records, $recordedBy, $institutionId, $branchId) {
            $saved = [];

            foreach ($records as $item) {
                $studentId = $item['student_id'] ?? $item['academic_student_id'] ?? null;
                if (! $studentId) {
                    continue;
                }

                $status = isset($item['status']) ? (int) $item['status'] : AcademicStudentAttendance::STATUS_ATTEND;
                $notes  = $item['notes'] ?? null;

                $attendance = AcademicStudentAttendance::updateOrCreate(
                    [
                        'academic_student_id' => $studentId,
                        'date'                => $date,
                    ],
                    [
                        'academic_class_id' => $classId,
                        'institution_id'    => $institutionId,
                        'branch_id'         => $branchId,
                        'status'            => $status,
                        'notes'             => $notes,
                        'recorded_by'       => $recordedBy,
                    ]
                );

                $saved[] = $attendance;
            }

            return $saved;
        });
    }

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
    ): AcademicStudentAttendance {
        return AcademicStudentAttendance::updateOrCreate(
            [
                'academic_student_id' => $studentId,
                'date'                => $date,
            ],
            [
                'academic_class_id' => $classId,
                'institution_id'    => $institutionId,
                'branch_id'         => $branchId,
                'status'            => $status,
                'notes'             => $notes,
                'recorded_by'       => $recordedBy,
            ]
        );
    }

    //------------------------------------------
    /**
     * Get students attendance report query for DataTables.
     * (تقرير حضور الطلاب)
     */
    public function getStudentsAttendanceReportQuery(array $filters = []): Builder
    {
        $startDate = $filters['start_date'] ?? $filters['from_date'] ?? null;
        $endDate   = $filters['end_date'] ?? $filters['to_date'] ?? null;

        $query = AcademicStudent::with([
            'translations',
            'class.translations',
            'academicYear.translations',
            'branch.translations',
            'institution.translations',
        ])
        ->withCount([
            'attendances as total_days' => function ($q) use ($startDate, $endDate) {
                if ($startDate && $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate]);
                } elseif ($startDate) {
                    $q->whereDate('date', '>=', $startDate);
                } elseif ($endDate) {
                    $q->whereDate('date', '<=', $endDate);
                }
            },
            'attendances as present_days' => function ($q) use ($startDate, $endDate) {
                $q->whereIn('status', [AcademicStudentAttendance::STATUS_ATTEND, AcademicStudentAttendance::STATUS_LATE]);
                if ($startDate && $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate]);
                } elseif ($startDate) {
                    $q->whereDate('date', '>=', $startDate);
                } elseif ($endDate) {
                    $q->whereDate('date', '<=', $endDate);
                }
            },
            'attendances as absent_days' => function ($q) use ($startDate, $endDate) {
                $q->where('status', AcademicStudentAttendance::STATUS_ABSENT);
                if ($startDate && $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate]);
                } elseif ($startDate) {
                    $q->whereDate('date', '>=', $startDate);
                } elseif ($endDate) {
                    $q->whereDate('date', '<=', $endDate);
                }
            },
        ])
        ->orderBy('id', 'asc');

        if (! empty($filters['institution_id'])) {
            $query->where('institution_id', $filters['institution_id']);
        }

        if (! empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (! empty($filters['academic_class_id'])) {
            $query->where('academic_class_id', $filters['academic_class_id']);
        }

        $search = $filters['search'] ?? null;
        if (is_array($search)) {
            $search = $search['value'] ?? null;
        }

        if (! empty($search) && is_string($search)) {
            $term = trim($search);
            if ($term !== '') {
                $query->where(function ($q) use ($term) {
                    $q->where('student_code', 'like', "%{$term}%")
                        ->orWhere('civil_id', 'like', "%{$term}%")
                        ->orWhereTranslationLike('name', "%{$term}%");
                });
            }
        }

        return $query;
    }

    //------------------------------------------
    /**
     * Get filtered attendance query.
     * (استعراض سجلات الحضور والفلاتر)
     */
    public function getFilteredQuery(array $filters = []): Builder
    {
        $query = AcademicStudentAttendance::with([
            'student.translations',
            'class.translations',
            'approver',
            'recorder',
        ])->latest('date');

        if (! empty($filters['institution_id'])) {
            $query->where('institution_id', $filters['institution_id']);
        }

        if (! empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (! empty($filters['academic_class_id'])) {
            $query->where('academic_class_id', $filters['academic_class_id']);
        } elseif (! empty($filters['classroom_id'])) {
            $query->where('academic_class_id', $filters['classroom_id']);
        }

        if (! empty($filters['academic_student_id'])) {
            $query->where('academic_student_id', $filters['academic_student_id']);
        } elseif (! empty($filters['student_id'])) {
            $query->where('academic_student_id', $filters['student_id']);
        }

        if (! empty($filters['date'])) {
            $query->whereDate('date', $filters['date']);
        }

        if (! empty($filters['start_date']) && ! empty($filters['end_date'])) {
            $query->whereBetween('date', [$filters['start_date'], $filters['end_date']]);
        } elseif (! empty($filters['start_date'])) {
            $query->whereDate('date', '>=', $filters['start_date']);
        } elseif (! empty($filters['end_date'])) {
            $query->whereDate('date', '<=', $filters['end_date']);
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', (int) $filters['status']);
        }

        if (isset($filters['approved']) && $filters['approved'] !== '') {
            $query->where('approved', filter_var($filters['approved'], FILTER_VALIDATE_BOOLEAN));
        }

        $search = $filters['search'] ?? null;
        if (is_array($search)) {
            $search = $search['value'] ?? null;
        }

        if (! empty($search) && is_string($search)) {
            $term = trim($search);
            if ($term !== '') {
                $query->whereHas('student', function ($q) use ($term) {
                    $q->where('student_code', 'like', "%{$term}%")
                        ->orWhere('civil_id', 'like', "%{$term}%")
                        ->orWhereTranslationLike('name', "%{$term}%");
                });
            }
        }

        return $query;
    }

    //------------------------------------------
    /**
     * Get paginated filtered attendance records.
     */
    public function getFilteredPaginated(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        return $this->getFilteredQuery($filters)->paginate($perPage);
    }

    //------------------------------------------
    /**
     * Approve attendance records.
     * (اعتماد الحضور - Approve)
     */
    public function approveAttendance(array $ids, int $approvedBy, ?string $notes = null): int
    {
        $updateData = [
            'approved'         => true,
            'approved_by'      => $approvedBy,
            'approved_at'      => now(),
            'rejection_reason' => null,
        ];

        if ($notes) {
            $updateData['notes'] = DB::raw("CONCAT(COALESCE(notes, ''), ' " . addslashes($notes) . "')");
        }

        return AcademicStudentAttendance::whereIn('id', $ids)->update($updateData);
    }

    //------------------------------------------
    /**
     * Reject or un-approve attendance records.
     * (رفض الحضور - Not Approved)
     */
    public function rejectAttendance(array $ids, int $rejectedBy, ?string $reason = null): int
    {
        return AcademicStudentAttendance::whereIn('id', $ids)->update([
            'approved'         => false,
            'approved_by'      => $rejectedBy,
            'approved_at'      => now(),
            'rejection_reason' => $reason,
        ]);
    }

    //------------------------------------------
    /**
     * Get student attendance report summary (total_days, present_days, absent_days, attendance_rate).
     * (تقرير الطالب)
     */
    public function getStudentAttendanceSummary(int $studentId, ?string $startDate = null, ?string $endDate = null): array
    {
        $student = AcademicStudent::with(['translations', 'class.translations'])->find($studentId);
        if (! $student) {
            return [];
        }

        $query = AcademicStudentAttendance::where('academic_student_id', $studentId);

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->whereDate('date', '>=', $startDate);
        } elseif ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        }

        $records = $query->orderBy('date', 'desc')->get();

        $totalDays    = $records->count();
        $presentDays  = $records->whereIn('status', [AcademicStudentAttendance::STATUS_ATTEND, AcademicStudentAttendance::STATUS_LATE])->count();
        $absentDays   = $records->where('status', AcademicStudentAttendance::STATUS_ABSENT)->count();
        $lateDays     = $records->where('status', AcademicStudentAttendance::STATUS_LATE)->count();
        $excusedDays  = $records->where('status', AcademicStudentAttendance::STATUS_EXCUSED)->count();
        $approvedDays = $records->where('approved', true)->count();

        $attendanceRate = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0;

        return [
            'student'          => [
                'id'           => $student->id,
                'name'         => $student->name,
                'student_code' => $student->student_code,
                'class_name'   => $student->class?->name ?? '-',
                'photo_url'    => $student->photo_url,
            ],
            'period'           => [
                'start_date' => $startDate,
                'end_date'   => $endDate,
            ],
            'total_days'       => $totalDays,
            'present_days'     => $presentDays,
            'absent_days'      => $absentDays,
            'late_days'        => $lateDays,
            'excused_days'     => $excusedDays,
            'approved_days'    => $approvedDays,
            'attendance_rate'  => $attendanceRate,
            'records'          => $records,
        ];
    }

    //------------------------------------------
    /**
     * Get guardian's students attendance report.
     * (تقرير ولي الأمر)
     */
    public function getGuardianStudentsAttendanceSummary(int $guardianId, ?string $startDate = null, ?string $endDate = null): array
    {
        $guardian = AcademicGuardian::with(['translations', 'students'])->find($guardianId);
        if (! $guardian) {
            return [];
        }

        $studentsReports = [];
        $totalDaysAll    = 0;
        $presentDaysAll  = 0;
        $absentDaysAll   = 0;

        foreach ($guardian->students as $student) {
            $report = $this->getStudentAttendanceSummary($student->id, $startDate, $endDate);
            if (! empty($report)) {
                $studentsReports[] = $report;
                $totalDaysAll     += $report['total_days'];
                $presentDaysAll   += $report['present_days'];
                $absentDaysAll    += $report['absent_days'];
            }
        }

        $overallRate = $totalDaysAll > 0 ? round(($presentDaysAll / $totalDaysAll) * 100, 1) : 0;

        return [
            'guardian' => [
                'id'            => $guardian->id,
                'name'          => $guardian->name,
                'guardian_code' => $guardian->guardian_code,
                'phone'         => $guardian->phone,
            ],
            'overall'  => [
                'total_days'      => $totalDaysAll,
                'present_days'    => $presentDaysAll,
                'absent_days'     => $absentDaysAll,
                'attendance_rate' => $overallRate,
            ],
            'students' => $studentsReports,
        ];
    }

    //------------------------------------------
    /**
     * Find single record by ID.
     */
    public function find(int $id): ?AcademicStudentAttendance
    {
        return AcademicStudentAttendance::with(['student.translations', 'class.translations'])->find($id);
    }

    //------------------------------------------
    /**
     * Delete record by ID.
     */
    public function delete(int $id): bool
    {
        $record = AcademicStudentAttendance::find($id);

        return $record ? (bool) $record->delete() : false;
    }
}
