<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\HR\Enums\AttendanceSource;
use Modules\HR\Enums\AttendanceStatus;
use Modules\HR\Enums\PunchType;
use Modules\HR\Http\Requests\ImportAttendanceExcelRequest;
use Modules\HR\Http\Requests\RecordPunchRequest;
use Modules\HR\Http\Requests\SaveManualAttendanceRequest;
use Modules\HR\Models\Attendance;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\HR\Services\AttendanceService;
use Modules\Nursery\Models\NurseryBranch;
use Yajra\DataTables\Facades\DataTables;

class AttendanceController extends Controller
{
    //======================
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    //======================
    /**
     * Display the Attendance & Absence Matrix Grid and List View.
     */
    public function index(Request $request): View
    {
        abort_if(! auth()->user()->can('hr.attendances.view'), 403);

        $selectedYear = (int)($request->get('year', now()->year));
        $selectedMonth = (int)($request->get('month', now()->month));
        $selectedEmployee = $request->get('employee_id');
        $selectedBranch = $request->get('branch_id');
        $selectedDepartment = $request->get('department_id');

        $filters = [
            'year'          => $selectedYear,
            'month'         => $selectedMonth,
            'employee_id'   => $selectedEmployee,
            'branch_id'     => $selectedBranch,
            'department_id' => $selectedDepartment,
        ];

        $matrixData = $this->attendanceService->getMonthlyMatrixData($filters);

        $employees = Employee::with('translations')->where('status', 'active')->get();
        $branches = NurseryBranch::all();
        $departments = Department::with('translations')->where('status', 'active')->get();

        $months = [
            1  => __('January'),
            2  => __('February'),
            3  => __('March'),
            4  => __('April'),
            5  => __('May'),
            6  => __('June'),
            7  => __('July'),
            8  => __('August'),
            9  => __('September'),
            10 => __('October'),
            11 => __('November'),
            12 => __('December'),
        ];

        $years = range(now()->year - 2, now()->year + 2);

        return view('hr::attendances.index', compact(
            'matrixData',
            'employees',
            'branches',
            'departments',
            'months',
            'years',
            'selectedYear',
            'selectedMonth',
            'selectedEmployee',
            'selectedBranch',
            'selectedDepartment'
        ));
    }

    //======================
    /**
     * AJAX endpoint for Monthly Matrix reload with filters.
     */
    public function matrixData(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.attendances.view'), 403);

        try {
            $data = $this->attendanceService->getMonthlyMatrixData($request->all());
            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    //======================
    /**
     * Server-side DataTables endpoint for Attendance List View.
     */
    public function data(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.attendances.view'), 403);

        $repo = app(\Modules\HR\Repositories\Contracts\AttendanceRepositoryInterface::class);
        $query = $repo->getDataTableQuery($request->all());

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('employee_name', function (Attendance $item) {
                $avatar = $item->employee?->avatar ? asset('storage/' . $item->employee->avatar) : asset('assets/img/avatars/1.png');
                $code = $item->employee?->employee_code ?? '-';
                $name = $item->employee?->name ?? '-';
                $dept = $item->employee?->department?->name ?? '-';

                return '<div class="d-flex align-items-center gap-2">
                            <div class="avatar avatar-sm">
                                <img src="' . $avatar . '" alt="avatar" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                            </div>
                            <div class="d-flex flex-column">
                                <span class="fw-semibold text-heading">' . e($name) . '</span>
                                <small class="text-muted">' . e($code) . ' • ' . e($dept) . '</small>
                            </div>
                        </div>';
            })
            ->editColumn('date', fn(Attendance $item) => Carbon::parse($item->date)->format('Y-m-d'))
            ->addColumn('check_in_badge', function (Attendance $item) {
                if (!$item->check_in) {
                    return '<span class="text-muted">-</span>';
                }
                $time = Carbon::parse($item->check_in)->format('h:i A');
                $src = $item->check_in_source ? '<small class="text-muted ms-1">(' . $item->check_in_source->label() . ')</small>' : '';
                return '<span class="badge bg-label-success">' . $time . '</span>' . $src;
            })
            ->addColumn('check_out_badge', function (Attendance $item) {
                if (!$item->check_out) {
                    return '<span class="text-muted">-</span>';
                }
                $time = Carbon::parse($item->check_out)->format('h:i A');
                $src = $item->check_out_source ? '<small class="text-muted ms-1">(' . $item->check_out_source->label() . ')</small>' : '';
                return '<span class="badge bg-label-primary">' . $time . '</span>' . $src;
            })
            ->editColumn('total_working_hours', fn(Attendance $item) => $item->total_working_hours . ' ' . __('hrs'))
            ->addColumn('late_early', function (Attendance $item) {
                $html = '';
                if ($item->late_minutes > 0) {
                    $html .= '<span class="badge bg-label-warning me-1">' . __('Late:') . ' ' . $item->late_minutes . 'm</span>';
                }
                if ($item->early_leave_minutes > 0) {
                    $html .= '<span class="badge bg-label-danger">' . __('Early:') . ' ' . $item->early_leave_minutes . 'm</span>';
                }
                return $html ?: '<span class="text-success small"><i class="bx bx-check"></i> ' . __('On Time') . '</span>';
            })
            ->editColumn('status', function (Attendance $item) {
                $badgeClass = $item->status ? $item->status->badgeClass() : 'bg-label-secondary';
                $label = $item->status ? $item->status->label() : '-';
                return '<span class="badge ' . $badgeClass . '">' . $label . '</span>';
            })
            ->addColumn('actions', function (Attendance $item) {
                $user = auth()->user();
                $canEdit = $user->can('hr.attendances.edit');
                $canDelete = $user->can('hr.attendances.delete');

                if (! $canEdit && ! $canDelete) {
                    return '<span class="text-muted">---</span>';
                }

                $rowJson = htmlspecialchars(json_encode([
                    'id'                  => $item->id,
                    'employee_id'         => $item->employee_id,
                    'employee_name'       => $item->employee?->name,
                    'date'                => Carbon::parse($item->date)->format('Y-m-d'),
                    'check_in'            => $item->check_in ? Carbon::parse($item->check_in)->format('H:i') : '',
                    'check_out'           => $item->check_out ? Carbon::parse($item->check_out)->format('H:i') : '',
                    'status'              => is_object($item->status) ? $item->status->value : $item->status,
                    'total_working_hours' => $item->total_working_hours,
                    'late_minutes'        => $item->late_minutes,
                    'early_leave_minutes' => $item->early_leave_minutes,
                    'overtime_minutes'    => $item->overtime_minutes,
                    'adjustment_reason'   => $item->adjustment_reason ?? '',
                ]), ENT_QUOTES, 'UTF-8');

                $buttons = '<div class="d-inline-flex gap-1">';
                if ($canEdit) {
                    $buttons .= '
                        <button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-attendance" 
                            data-row="' . $rowJson . '" 
                            title="' . __('Edit') . '">
                            <i class="icon-base bx bx-edit-alt"></i>
                        </button>';
                }
                if ($canDelete) {
                    $buttons .= '
                        <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-attendance" 
                            data-id="' . $item->id . '" 
                            data-name="' . e($item->employee?->name . ' (' . $item->date . ')') . '" 
                            title="' . __('Delete') . '">
                            <i class="icon-base bx bx-trash"></i>
                        </button>';
                }
                $buttons .= '</div>';
                return $buttons;
            })
            ->rawColumns(['employee_name', 'check_in_badge', 'check_out_badge', 'late_early', 'status', 'actions'])
            ->make(true);
    }

    //======================
    /**
     * Record a quick punch (Check In / Check Out modal).
     */
    public function recordPunch(RecordPunchRequest $request): JsonResponse|RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.attendances.create'), 403);

        try {
            $attendance = $this->attendanceService->recordPunch($request->validated());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Punch recorded successfully.'),
                    'data'    => $attendance,
                ]);
            }

            return redirect()->back()->with('success', __('Punch recorded successfully.'));
        } catch (Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    //======================
    /**
     * Store manual single attendance.
     */
    public function store(SaveManualAttendanceRequest $request): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.attendances.create'), 403);

        try {
            $this->attendanceService->saveManualAttendance($request->validated());

            return redirect()->route('hr.attendances.index')
                ->with('success', __('Attendance record saved successfully.'));
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('Error saving attendance: ') . $e->getMessage());
        }
    }

    //======================
    /**
     * Update attendance record with HR manual adjustment.
     */
    public function update(SaveManualAttendanceRequest $request, int $id): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.attendances.edit'), 403);

        try {
            $this->attendanceService->updateAttendance($id, $request->validated());

            return redirect()->route('hr.attendances.index')
                ->with('success', __('Attendance record updated successfully.'));
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('Error updating attendance: ') . $e->getMessage());
        }
    }

    //======================
    /**
     * Remove attendance record.
     */
    public function destroy(int $id): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.attendances.delete'), 403);

        try {
            $this->attendanceService->deleteAttendance($id);

            return redirect()->route('hr.attendances.index')
                ->with('success', __('Attendance record deleted successfully.'));
        } catch (Exception $e) {
            return redirect()->route('hr.attendances.index')
                ->with('error', __('Error deleting attendance: ') . $e->getMessage());
        }
    }

    //======================
    /**
     * Import attendance from Excel / CSV file.
     */
    public function importExcel(ImportAttendanceExcelRequest $request): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.attendances.import'), 403);

        try {
            $result = $this->attendanceService->importExcel($request->file('file'));

            $msg = __('Successfully imported :count attendance records.', ['count' => $result['imported_count']]);
            if (!empty($result['errors'])) {
                $msg .= ' ' . __('Warnings: ') . implode(', ', array_slice($result['errors'], 0, 3));
            }

            return redirect()->route('hr.attendances.index')->with('success', $msg);
        } catch (Exception $e) {
            return redirect()->back()->with('error', __('Failed to import sheet: ') . $e->getMessage());
        }
    }

    //======================
    /**
     * Download Excel template sample.
     */
    public function downloadTemplate()
    {
        abort_if(! auth()->user()->can('hr.attendances.import'), 403);

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendance_template.csv"',
        ];

        $columns = ['employee_code', 'date', 'check_in', 'check_out', 'status'];
        $sampleRow = ['EMP-0001', date('Y-m-d'), '08:00', '16:00', 'present'];

        $callback = function () use ($columns, $sampleRow) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, $sampleRow);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
