<?php

namespace Modules\HR\Services;

use Carbon\Carbon;
use Modules\HR\Enums\AttendanceStatus;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\HR\Models\Shift;
use Modules\HR\Repositories\Contracts\AttendanceReportRepositoryInterface;
use Modules\Nursery\Models\NurseryBranch;
use Modules\Settings\Models\PrintTemplate;
use Modules\Settings\Models\Setting;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceReportService
{
    public function __construct(
        protected AttendanceReportRepositoryInterface $reportRepository
    ) {}

    /**
     * Get all available report columns based on report type.
     *
     * @param string $reportType
     * @return array<string, array<string, mixed>>
     */
    public function getAvailableColumns(string $reportType = 'daily'): array
    {
        if ($reportType === 'monthly_summary') {
            return [
                'index' => [
                    'label'   => '#',
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '45px',
                ],
                'code' => [
                    'label'   => __('Employee Code'),
                    'default' => true,
                    'class'   => '',
                    'width'   => '100px',
                ],
                'employee' => [
                    'label'   => __('Employee Name'),
                    'default' => true,
                    'class'   => '',
                    'width'   => 'auto',
                ],
                'department' => [
                    'label'   => __('Department'),
                    'default' => true,
                    'class'   => '',
                    'width'   => 'auto',
                ],
                'branch' => [
                    'label'   => __('Branch'),
                    'default' => true,
                    'class'   => '',
                    'width'   => 'auto',
                ],
                'scheduled_days' => [
                    'label'   => __('Total Working Days'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '100px',
                ],
                'present_days' => [
                    'label'   => __('Present Days'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '90px',
                ],
                'absent_days' => [
                    'label'   => __('Absent Days'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '90px',
                ],
                'leave_days' => [
                    'label'   => __('Leave Days'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '90px',
                ],
                'rest_days' => [
                    'label'   => __('Rest Days'),
                    'default' => false,
                    'class'   => 'text-center',
                    'width'   => '85px',
                ],
                'late_minutes' => [
                    'label'   => __('Late (Mins)'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '95px',
                ],
                'early_leave' => [
                    'label'   => __('Early Leave'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '95px',
                ],
                'overtime' => [
                    'label'   => __('Overtime'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '90px',
                ],
                'total_working_hours' => [
                    'label'   => __('Net Work Hours'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '105px',
                ],
                'attendance_rate' => [
                    'label'   => __('Attendance Rate'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '95px',
                ],
            ];
        }

        if ($reportType === 'lateness') {
            return [
                'index' => [
                    'label'   => '#',
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '45px',
                ],
                'code' => [
                    'label'   => __('Employee Code'),
                    'default' => true,
                    'class'   => '',
                    'width'   => '100px',
                ],
                'employee' => [
                    'label'   => __('Employee Name'),
                    'default' => true,
                    'class'   => '',
                    'width'   => 'auto',
                ],
                'department' => [
                    'label'   => __('Department'),
                    'default' => true,
                    'class'   => '',
                    'width'   => 'auto',
                ],
                'branch' => [
                    'label'   => __('Branch'),
                    'default' => true,
                    'class'   => '',
                    'width'   => 'auto',
                ],
                'date' => [
                    'label'   => __('Date'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '95px',
                ],
                'shift' => [
                    'label'   => __('Shift'),
                    'default' => true,
                    'class'   => '',
                    'width'   => 'auto',
                ],
                'check_in' => [
                    'label'   => __('Check In'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '75px',
                ],
                'late_minutes' => [
                    'label'   => __('Late (Mins)'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '90px',
                ],
                'check_out' => [
                    'label'   => __('Check Out'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '75px',
                ],
                'early_leave' => [
                    'label'   => __('Early Leave'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '90px',
                ],
                'work_hours' => [
                    'label'   => __('Work Hours'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '85px',
                ],
                'status' => [
                    'label'   => __('Status'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '90px',
                ],
            ];
        }

        if ($reportType === 'absenteeism') {
            return [
                'index' => [
                    'label'   => '#',
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '45px',
                ],
                'code' => [
                    'label'   => __('Employee Code'),
                    'default' => true,
                    'class'   => '',
                    'width'   => '100px',
                ],
                'employee' => [
                    'label'   => __('Employee Name'),
                    'default' => true,
                    'class'   => '',
                    'width'   => 'auto',
                ],
                'department' => [
                    'label'   => __('Department'),
                    'default' => true,
                    'class'   => '',
                    'width'   => 'auto',
                ],
                'branch' => [
                    'label'   => __('Branch'),
                    'default' => true,
                    'class'   => '',
                    'width'   => 'auto',
                ],
                'date' => [
                    'label'   => __('Date'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '95px',
                ],
                'day' => [
                    'label'   => __('Day'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '85px',
                ],
                'shift' => [
                    'label'   => __('Shift'),
                    'default' => true,
                    'class'   => '',
                    'width'   => 'auto',
                ],
                'status' => [
                    'label'   => __('Absence Type'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '110px',
                ],
                'reason' => [
                    'label'   => __('Notes / Disciplinary Action'),
                    'default' => true,
                    'class'   => '',
                    'width'   => 'auto',
                ],
            ];
        }

        // Default: 'daily' / General Attendance Detailed Log
        return [
            'index' => [
                'label'   => '#',
                'default' => true,
                'class'   => 'text-center',
                'width'   => '45px',
            ],
            'code' => [
                'label'   => __('Employee Code'),
                'default' => true,
                'class'   => '',
                'width'   => '100px',
            ],
            'employee' => [
                'label'   => __('Employee Name'),
                'default' => true,
                'class'   => '',
                'width'   => 'auto',
            ],
            'department' => [
                'label'   => __('Department'),
                'default' => true,
                'class'   => '',
                'width'   => 'auto',
            ],
            'branch' => [
                'label'   => __('Branch'),
                'default' => true,
                'class'   => '',
                'width'   => 'auto',
            ],
            'date' => [
                'label'   => __('Date'),
                'default' => true,
                'class'   => 'text-center',
                'width'   => '95px',
            ],
            'day' => [
                'label'   => __('Day'),
                'default' => false,
                'class'   => 'text-center',
                'width'   => '80px',
            ],
            'shift' => [
                'label'   => __('Shift'),
                'default' => true,
                'class'   => '',
                'width'   => 'auto',
            ],
            'check_in' => [
                'label'   => __('Check In'),
                'default' => true,
                'class'   => 'text-center',
                'width'   => '75px',
            ],
            'check_out' => [
                'label'   => __('Check Out'),
                'default' => true,
                'class'   => 'text-center',
                'width'   => '75px',
            ],
            'work_hours' => [
                'label'   => __('Work Hours'),
                'default' => true,
                'class'   => 'text-center',
                'width'   => '85px',
            ],
            'late_minutes' => [
                'label'   => __('Late (Mins)'),
                'default' => true,
                'class'   => 'text-center',
                'width'   => '80px',
            ],
            'early_leave' => [
                'label'   => __('Early Leave'),
                'default' => false,
                'class'   => 'text-center',
                'width'   => '85px',
            ],
            'overtime' => [
                'label'   => __('Overtime'),
                'default' => true,
                'class'   => 'text-center',
                'width'   => '80px',
            ],
            'status' => [
                'label'   => __('Status'),
                'default' => true,
                'class'   => 'text-center',
                'width'   => '90px',
            ],
            'source' => [
                'label'   => __('Source'),
                'default' => false,
                'class'   => 'text-center',
                'width'   => '85px',
            ],
        ];
    }

    /**
     * Resolve boolean status for each column based on request or defaults.
     *
     * @param string $reportType
     * @param array<string>|null $requestedColumns
     * @return array<string, bool>
     */
    public function resolveSelectedColumns(string $reportType = 'daily', ?array $requestedColumns = null): array
    {
        $all = $this->getAvailableColumns($reportType);
        $selected = [];

        if (is_array($requestedColumns)) {
            foreach ($all as $key => $col) {
                $selected[$key] = in_array($key, $requestedColumns);
            }
        } else {
            foreach ($all as $key => $col) {
                $selected[$key] = (bool) $col['default'];
            }
        }

        return $selected;
    }

    /**
     * Get complete data needed for the interactive report index page.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return array<string, mixed>
     */
    public function getReportData(array $filters = [], int $perPage = 25): array
    {
        $reportType = $filters['report_type'] ?? 'daily';
        $availableColumns = $this->getAvailableColumns($reportType);
        $selectedColumns  = $this->resolveSelectedColumns($reportType, $filters['columns'] ?? null);

        $kpis = $this->reportRepository->getSummaryKPIs($filters);

        if ($reportType === 'monthly_summary') {
            $attendances = $this->reportRepository->getMonthlySummaryPaginated($filters, $perPage);
        } else {
            $attendances = $this->reportRepository->getPaginatedRecords($filters, $perPage);
        }

        $departments = Department::all();
        $branches    = NurseryBranch::all();
        $shifts      = Shift::all();
        $employees   = Employee::select('id', 'employee_code')->with('translations')->orderBy('id', 'desc')->get();
        $statuses    = AttendanceStatus::cases();

        $printTemplates = PrintTemplate::where('is_active', true)->get();
        $defaultTemplate = $printTemplates->where('is_default', true)->first() 
            ?? $printTemplates->where('code', 'general_report')->first() 
            ?? $printTemplates->first();

        return compact(
            'reportType',
            'attendances',
            'kpis',
            'availableColumns',
            'selectedColumns',
            'departments',
            'branches',
            'shifts',
            'employees',
            'statuses',
            'printTemplates',
            'defaultTemplate'
        );
    }

    /**
     * Get complete data needed for the print document.
     *
     * @param array<string, mixed> $filters
     * @param int|null $templateId
     * @return array<string, mixed>
     */
    public function getPrintData(array $filters = [], ?int $templateId = null): array
    {
        $reportType = $filters['report_type'] ?? 'daily';
        $availableColumns = $this->getAvailableColumns($reportType);
        $selectedColumns  = $this->resolveSelectedColumns($reportType, $filters['columns'] ?? null);

        $kpis = $this->reportRepository->getSummaryKPIs($filters);

        if ($reportType === 'monthly_summary') {
            $attendances = $this->reportRepository->getMonthlySummaryRecords($filters);
        } else {
            $attendances = $this->reportRepository->getFilteredRecords($filters);
        }

        $availableTemplates = PrintTemplate::where('is_active', true)->get();
        $template = null;

        if ($templateId) {
            $template = $availableTemplates->where('id', $templateId)->first();
        }

        $template = $template 
            ?? $availableTemplates->where('code', 'general_report')->first()
            ?? $availableTemplates->where('is_default', true)->first()
            ?? $availableTemplates->first();

        $globalSetting = Setting::first();

        return compact(
            'reportType',
            'attendances',
            'kpis',
            'availableColumns',
            'selectedColumns',
            'template',
            'availableTemplates',
            'globalSetting'
        );
    }

    /**
     * Stream CSV export for the filtered records.
     *
     * @param array<string, mixed> $filters
     * @param array<string>|null $requestedColumns
     * @return StreamedResponse
     */
    public function exportCsv(array $filters = [], ?array $requestedColumns = null): StreamedResponse
    {
        $reportType = $filters['report_type'] ?? 'daily';
        $availableColumns = $this->getAvailableColumns($reportType);
        $selectedColumns  = $this->resolveSelectedColumns($reportType, $requestedColumns);

        $fileName = "attendance_{$reportType}_report_" . Carbon::now()->format('Y_m_d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        if ($reportType === 'monthly_summary') {
            $records = $this->reportRepository->getMonthlySummaryRecords($filters);

            return response()->stream(function () use ($records, $availableColumns, $selectedColumns) {
                $output = fopen('php://output', 'w');
                fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM

                $headerRow = [];
                foreach ($availableColumns as $colKey => $colDef) {
                    if (!empty($selectedColumns[$colKey])) {
                        $headerRow[] = $colDef['label'];
                    }
                }
                fputcsv($output, $headerRow);

                foreach ($records as $index => $row) {
                    $dataRow = [];
                    if (!empty($selectedColumns['index'])) $dataRow[] = $index + 1;
                    if (!empty($selectedColumns['code'])) $dataRow[] = $row->employee->employee_code ?? '-';
                    if (!empty($selectedColumns['employee'])) $dataRow[] = $row->employee->name ?? '-';
                    if (!empty($selectedColumns['department'])) $dataRow[] = $row->employee->department->name ?? '-';
                    if (!empty($selectedColumns['branch'])) $dataRow[] = $row->employee->primaryBranch->name ?? '-';
                    if (!empty($selectedColumns['scheduled_days'])) $dataRow[] = $row->scheduled_days;
                    if (!empty($selectedColumns['present_days'])) $dataRow[] = $row->present_days;
                    if (!empty($selectedColumns['absent_days'])) $dataRow[] = $row->absent_days;
                    if (!empty($selectedColumns['leave_days'])) $dataRow[] = $row->leave_days;
                    if (!empty($selectedColumns['rest_days'])) $dataRow[] = $row->rest_days;
                    if (!empty($selectedColumns['late_minutes'])) $dataRow[] = $row->late_minutes;
                    if (!empty($selectedColumns['early_leave'])) $dataRow[] = $row->early_leave_minutes;
                    if (!empty($selectedColumns['overtime'])) $dataRow[] = $row->overtime_minutes;
                    if (!empty($selectedColumns['total_working_hours'])) $dataRow[] = $row->total_working_hours;
                    if (!empty($selectedColumns['attendance_rate'])) $dataRow[] = $row->attendance_rate . '%';

                    fputcsv($output, $dataRow);
                }

                fclose($output);
            }, 200, $headers);
        }

        $records = $this->reportRepository->getFilteredRecords($filters);

        return response()->stream(function () use ($records, $availableColumns, $selectedColumns, $reportType) {
            $output = fopen('php://output', 'w');
            
            // UTF-8 BOM for Arabic in Excel
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Write Header
            $headerRow = [];
            foreach ($availableColumns as $colKey => $colDef) {
                if (!empty($selectedColumns[$colKey])) {
                    $headerRow[] = $colDef['label'];
                }
            }
            fputcsv($output, $headerRow);

            // Write Records
            foreach ($records as $index => $row) {
                $dataRow = [];

                if (!empty($selectedColumns['index'])) {
                    $dataRow[] = $index + 1;
                }
                if (!empty($selectedColumns['code'])) {
                    $dataRow[] = $row->employee->employee_code ?? '-';
                }
                if (!empty($selectedColumns['employee'])) {
                    $dataRow[] = $row->employee->name ?? '-';
                }
                if (!empty($selectedColumns['department'])) {
                    $dataRow[] = $row->employee->department->name ?? '-';
                }
                if (!empty($selectedColumns['branch'])) {
                    $dataRow[] = $row->employee->primaryBranch->name ?? '-';
                }
                if (!empty($selectedColumns['date'])) {
                    $dataRow[] = $row->date ? $row->date->format('Y-m-d') : '-';
                }
                if (!empty($selectedColumns['day'])) {
                    $dataRow[] = $row->date ? $row->date->translatedFormat('l') : '-';
                }
                if (!empty($selectedColumns['shift'])) {
                    $dataRow[] = $row->shift->name ?? '-';
                }
                if (!empty($selectedColumns['check_in'])) {
                    $dataRow[] = $row->check_in ? Carbon::parse($row->check_in)->format('H:i') : '-';
                }
                if (!empty($selectedColumns['check_out'])) {
                    $dataRow[] = $row->check_out ? Carbon::parse($row->check_out)->format('H:i') : '-';
                }
                if (!empty($selectedColumns['work_hours'])) {
                    $dataRow[] = $row->total_working_hours ?? '0';
                }
                if (!empty($selectedColumns['late_minutes'])) {
                    $dataRow[] = $row->late_minutes ?? '0';
                }
                if (!empty($selectedColumns['early_leave'])) {
                    $dataRow[] = $row->early_leave_minutes ?? '0';
                }
                if (!empty($selectedColumns['overtime'])) {
                    $dataRow[] = $row->overtime_minutes ?? '0';
                }
                if (!empty($selectedColumns['status'])) {
                    $dataRow[] = $row->status ? $row->status->label() : '-';
                }
                if (!empty($selectedColumns['source'])) {
                    $dataRow[] = $row->is_manual_adjusted ? __('Manual') : __('Biometric');
                }
                if (!empty($selectedColumns['reason'])) {
                    $dataRow[] = $row->adjustment_reason ?? __('Unexcused Absence');
                }

                fputcsv($output, $dataRow);
            }

            fclose($output);
        }, 200, $headers);
    }
}
