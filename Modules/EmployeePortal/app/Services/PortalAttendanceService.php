<?php

namespace Modules\EmployeePortal\Services;

use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\EmployeePortal\Repositories\Contracts\PortalAttendanceRepositoryInterface;
use Modules\EmployeePortal\Repositories\Contracts\PortalEmployeeRepositoryInterface;
use Modules\EmployeePortal\Services\Contracts\PortalAttendanceServiceInterface;
use Modules\HR\Enums\AttendanceSource;
use Modules\HR\Enums\AttendanceStatus;
use Modules\HR\Enums\PunchType;
use Modules\HR\Models\Employee;
use Modules\HR\Services\AttendanceService;

class PortalAttendanceService implements PortalAttendanceServiceInterface
{
    public function __construct(
        protected PortalAttendanceRepositoryInterface $attendanceRepo,
        protected PortalEmployeeRepositoryInterface $employeeRepo,
        protected ?AttendanceService $hrAttendanceService = null
    ) {}

    /**
     * Resolve the employee associated with the given user.
     *
     * @param User|null $user
     * @return Employee|null
     */
    public function resolveCurrentEmployee(?User $user = null): ?Employee
    {
        $user = $user ?? Auth::user();
        if (!$user) {
            return null;
        }

        $employee = $this->employeeRepo->findByUserId($user->id);

        if (!$employee) {
            $employee = $this->employeeRepo->getActiveFallback();
        }

        return $employee;
    }

    /**
     * Get data required to render the post-login punch dashboard.
     *
     * @param User|null $user
     * @return array
     */
    public function getDashboardData(?User $user = null): array
    {
        $employee = $this->resolveCurrentEmployee($user);
        $today = Carbon::today('Asia/Kuwait')->format('Y-m-d');
        $todayAttendance = null;

        if ($employee) {
            $todayAttendance = $this->attendanceRepo->findTodayAttendance($employee->id, $today);
        }

        $nowKuwait = Carbon::now('Asia/Kuwait');
        $portalCurrentDate = $nowKuwait->locale(app()->getLocale())->translatedFormat('l, d F Y') . ' • ' . __('Kuwait');

        $currentHour = $nowKuwait->hour;
        $greetingTime = $currentHour < 12 ? __('Good Morning') : __('Good Evening');

        $hasCheckIn = !empty($todayAttendance?->check_in);
        $hasCheckOut = !empty($todayAttendance?->check_out);
        $checkInTimeFormatted = $hasCheckIn ? Carbon::parse($todayAttendance->check_in)->format('h:i A') : null;
        $checkOutTimeFormatted = $hasCheckOut ? Carbon::parse($todayAttendance->check_out)->format('h:i A') : null;

        $shiftStatusText = __('Awaiting Punch');
        if ($hasCheckIn && $hasCheckOut) {
            $shiftStatusText = __("Today's shift completed ✓");
        } elseif ($hasCheckIn) {
            $shiftStatusText = __('Checked in') . ': ' . $checkInTimeFormatted;
        }

        // Generate day timeline for tracker
        $dayTimeline = null;
        if ($employee) {
            $hrService = $this->hrAttendanceService ?? app(AttendanceService::class);
            try {
                $dayTimeline = $hrService->getEmployeeDayTimeline($employee->id, $today);
            } catch (Exception $e) {
                $dayTimeline = null;
            }
        }

        return [
            'employee'               => $employee,
            'avatarUrl'              => $employee?->avatar_url ?? $user?->avatar_url ?? asset('assets/portal/teacher-avatar.png'),
            'todayAttendance'        => $todayAttendance,
            'portalCurrentDate'      => $portalCurrentDate,
            'greetingTime'           => $greetingTime,
            'hasCheckIn'             => $hasCheckIn,
            'hasCheckOut'            => $hasCheckOut,
            'checkInTimeFormatted'   => $checkInTimeFormatted,
            'checkOutTimeFormatted'  => $checkOutTimeFormatted,
            'shiftStatusText'        => $shiftStatusText,
            'dayTimeline'            => $dayTimeline,
        ];
    }

    /**
     * Record a mobile punch (check-in, check-out, or auto) with GPS coordinates.
     *
     * @param array $data
     * @param User|null $user
     * @return array
     */
    public function recordPunch(array $data, ?User $user = null): array
    {
        $employee = $this->resolveCurrentEmployee($user);

        if (!$employee) {
            return [
                'success' => false,
                'message' => __('Employee record not found for this account.'),
                'data'    => null,
                'status'  => 404,
            ];
        }

        try {
            $punchType = $data['punch_type'] ?? PunchType::AUTO->value;

            if ($this->hrAttendanceService) {
                $attendance = $this->hrAttendanceService->recordPunch([
                    'employee_id' => $employee->id,
                    'punch_time'  => now('Asia/Kuwait'),
                    'punch_type'  => $punchType,
                    'source'      => AttendanceSource::MOBILE_APP->value,
                    'latitude'    => $data['latitude'] ?? null,
                    'longitude'   => $data['longitude'] ?? null,
                    'device_info' => 'PWA Mobile App (' . request()->header('User-Agent') . ')',
                    'notes'       => $data['notes'] ?? null,
                ]);
            } else {
                $today = now('Asia/Kuwait')->format('Y-m-d');
                $nowTime = now('Asia/Kuwait')->format('H:i:s');

                $attendance = $this->attendanceRepo->findOrNew($employee->id, $today);

                if (!$attendance->exists) {
                    $attendance->shift_id = $employee->shift_id;
                    $attendance->required_hours = $employee->shift?->total_hours ?? 8.0;
                    $attendance->created_by = Auth::id() ?? $user?->id;
                }

                $isCheckIn = false;
                $isCheckOut = false;

                if ($punchType === 'check_in' || (!$attendance->check_in && $punchType === 'auto')) {
                    $attendance->check_in = $nowTime;
                    $attendance->check_in_source = AttendanceSource::MOBILE_APP->value;
                    $attendance->status = AttendanceStatus::PRESENT;
                    $isCheckIn = true;
                } else {
                    $attendance->check_out = $nowTime;
                    $attendance->check_out_source = AttendanceSource::MOBILE_APP->value;
                    $isCheckOut = true;
                }

                $this->attendanceRepo->save($attendance);
            }

            $timeNow = now('Asia/Kuwait');
            $period = $timeNow->format('A') === 'AM' ? __('AM') : __('PM');
            $formattedTime = $timeNow->format('h:i') . ' ' . $period;
            $empName = $employee->first_name ?? $employee->name ?? __('Teacher');

            if ($isCheckIn) {
                $greeting = __('Hello :name, have a great day!', ['name' => $empName]);
                $title = __('Your check-in was successfully recorded!');
                $statusLabel = __('Early & On-Time Check-In');
            } else {
                $greeting = __('Goodbye :name, your departure has been recorded.', ['name' => $empName]);
                $title = __('Your check-out was successfully recorded!');
                $statusLabel = __('Approved Evening Check-Out');
            }

            return [
                'success' => true,
                'message' => $title,
                'data'    => [
                    'attendance'           => $attendance,
                    'is_check_in'          => $isCheckIn,
                    'is_check_out'         => $isCheckOut,
                    'punch_time_formatted' => $formattedTime,
                    'status_label'         => $statusLabel,
                    'greeting'             => $greeting,
                    'title'                => $title,
                ],
                'status'  => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage() ?: __('Failed to record punch.'),
                'data'    => null,
                'status'  => 500,
            ];
        }
    }
}
