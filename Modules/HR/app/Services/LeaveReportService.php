<?php

namespace Modules\HR\Services;

use Carbon\Carbon;
use Modules\HR\Enums\LeaveRequestStatus;
use Modules\HR\Enums\LeaveType;
use Modules\HR\Enums\PermissionRequestStatus;
use Modules\HR\Enums\PermissionType;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\HR\Repositories\Contracts\LeaveReportRepositoryInterface;
use Modules\Nursery\Models\NurseryBranch;
use Modules\Settings\Models\PrintTemplate;
use Modules\Settings\Models\Setting;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeaveReportService
{
    public function __construct(
        protected LeaveReportRepositoryInterface $reportRepository
    ) {}

    /**
     * Get all available report columns based on report type.
     *
     * @param string $reportType
     * @return array<string, array<string, mixed>>
     */
    public function getAvailableColumns(string $reportType = 'balances'): array
    {
        if ($reportType === 'leaves_log') {
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
                    'default' => false,
                    'class'   => '',
                    'width'   => 'auto',
                ],
                'leave_type' => [
                    'label'   => __('Leave Type'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '120px',
                ],
                'start_date' => [
                    'label'   => __('From Date'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '95px',
                ],
                'end_date' => [
                    'label'   => __('To Date'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '95px',
                ],
                'days_count' => [
                    'label'   => __('Days Count'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '80px',
                ],
                'replacement' => [
                    'label'   => __('Replacement Employee'),
                    'default' => false,
                    'class'   => '',
                    'width'   => 'auto',
                ],
                'status' => [
                    'label'   => __('Status'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '100px',
                ],
                'reason' => [
                    'label'   => __('Reason'),
                    'default' => true,
                    'class'   => '',
                    'width'   => 'auto',
                ],
                'created_at' => [
                    'label'   => __('Request Date'),
                    'default' => false,
                    'class'   => 'text-center',
                    'width'   => '95px',
                ],
            ];
        }

        if ($reportType === 'permissions') {
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
                    'default' => false,
                    'class'   => '',
                    'width'   => 'auto',
                ],
                'date' => [
                    'label'   => __('Date'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '95px',
                ],
                'permission_type' => [
                    'label'   => __('Permission Type'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '110px',
                ],
                'start_time' => [
                    'label'   => __('From Hour'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '80px',
                ],
                'end_time' => [
                    'label'   => __('To Hour'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '80px',
                ],
                'minutes' => [
                    'label'   => __('Duration (Mins)'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '95px',
                ],
                'hours' => [
                    'label'   => __('Duration (Hours)'),
                    'default' => false,
                    'class'   => 'text-center',
                    'width'   => '95px',
                ],
                'status' => [
                    'label'   => __('Status'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '100px',
                ],
                'reason' => [
                    'label'   => __('Reason'),
                    'default' => true,
                    'class'   => '',
                    'width'   => 'auto',
                ],
            ];
        }

        // Default: 'balances' (Leave Balances Report)
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
            'hire_date' => [
                'label'   => __('Hire Date'),
                'default' => true,
                'class'   => 'text-center',
                'width'   => '95px',
            ],
            'annual_entitled' => [
                'label'   => __('Annual Entitled'),
                'default' => true,
                'class'   => 'text-center',
                'width'   => '85px',
            ],
            'annual_consumed' => [
                'label'   => __('Annual Consumed'),
                'default' => true,
                'class'   => 'text-center',
                'width'   => '85px',
            ],
            'annual_pending' => [
                'label'   => __('Annual Pending'),
                'default' => true,
                'class'   => 'text-center',
                'width'   => '85px',
            ],
            'annual_remaining' => [
                'label'   => __('Annual Remaining'),
                'default' => true,
                'class'   => 'text-center fw-bold text-success',
                'width'   => '85px',
            ],
            'casual_consumed' => [
                'label'   => __('Casual Consumed'),
                'default' => true,
                'class'   => 'text-center',
                'width'   => '85px',
            ],
            'sick_consumed' => [
                'label'   => __('Sick Consumed'),
                'default' => true,
                'class'   => 'text-center',
                'width'   => '85px',
            ],
            'total_entitled' => [
                'label'   => __('Total Entitled'),
                'default' => false,
                'class'   => 'text-center',
                'width'   => '90px',
            ],
            'total_consumed' => [
                'label'   => __('Total Consumed'),
                'default' => false,
                'class'   => 'text-center',
                'width'   => '90px',
            ],
            'total_remaining' => [
                'label'   => __('Total Remaining'),
                'default' => false,
                'class'   => 'text-center fw-bold',
                'width'   => '90px',
            ],
            'consumption_rate' => [
                'label'   => __('Consumption Rate'),
                'default' => true,
                'class'   => 'text-center',
                'width'   => '90px',
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
    public function resolveSelectedColumns(string $reportType = 'balances', ?array $requestedColumns = null): array
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
        $reportType = $filters['report_type'] ?? 'balances';
        $availableColumns = $this->getAvailableColumns($reportType);
        $selectedColumns  = $this->resolveSelectedColumns($reportType, $filters['columns'] ?? null);

        if ($reportType === 'leaves_log') {
            $records = $this->reportRepository->getLeaveRequestsPaginated($filters, $perPage);
        } elseif ($reportType === 'permissions') {
            $records = $this->reportRepository->getPermissionsPaginated($filters, $perPage);
        } else {
            $records = $this->reportRepository->getLeaveBalancesPaginated($filters, $perPage);
        }

        $departments = Department::all();
        $branches    = NurseryBranch::all();
        $employees   = Employee::select('id', 'employee_code')->with('translations')->orderBy('id', 'desc')->get();
        $leaveTypes  = LeaveType::cases();
        $leaveStatuses = LeaveRequestStatus::cases();
        $permissionTypes = PermissionType::cases();
        $permissionStatuses = PermissionRequestStatus::cases();

        $printTemplates = PrintTemplate::where('is_active', true)->get();
        $defaultTemplate = $printTemplates->where('is_default', true)->first()
            ?? $printTemplates->where('code', 'general_report')->first()
            ?? $printTemplates->first();

        return compact(
            'reportType',
            'records',
            'availableColumns',
            'selectedColumns',
            'departments',
            'branches',
            'employees',
            'leaveTypes',
            'leaveStatuses',
            'permissionTypes',
            'permissionStatuses',
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
        $reportType = $filters['report_type'] ?? 'balances';
        $availableColumns = $this->getAvailableColumns($reportType);
        $selectedColumns  = $this->resolveSelectedColumns($reportType, $filters['columns'] ?? null);

        if ($reportType === 'leaves_log') {
            $records = $this->reportRepository->getLeaveRequestsRecords($filters);
        } elseif ($reportType === 'permissions') {
            $records = $this->reportRepository->getPermissionsRecords($filters);
        } else {
            $records = $this->reportRepository->getLeaveBalancesRecords($filters);
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
            'records',
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
        $reportType = $filters['report_type'] ?? 'balances';
        $availableColumns = $this->getAvailableColumns($reportType);
        $selectedColumns  = $this->resolveSelectedColumns($reportType, $requestedColumns);

        $fileName = "hr_{$reportType}_report_" . Carbon::now()->format('Y_m_d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($filters, $reportType, $availableColumns, $selectedColumns) {
            $output = fopen('php://output', 'w');
            // UTF-8 BOM for Arabic in Excel
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Write Header Row
            $headerRow = [];
            foreach ($availableColumns as $colKey => $colDef) {
                if (!empty($selectedColumns[$colKey])) {
                    $headerRow[] = $colDef['label'];
                }
            }
            fputcsv($output, $headerRow);

            if ($reportType === 'leaves_log') {
                $records = $this->reportRepository->getLeaveRequestsRecords($filters);

                foreach ($records as $index => $row) {
                    $dataRow = [];
                    if (!empty($selectedColumns['index'])) $dataRow[] = $index + 1;
                    if (!empty($selectedColumns['code'])) $dataRow[] = $row->employee->employee_code ?? '-';
                    if (!empty($selectedColumns['employee'])) $dataRow[] = $row->employee->name ?? '-';
                    if (!empty($selectedColumns['department'])) $dataRow[] = $row->employee->department->name ?? '-';
                    if (!empty($selectedColumns['branch'])) $dataRow[] = $row->employee->primaryBranch->name ?? '-';
                    if (!empty($selectedColumns['leave_type'])) $dataRow[] = $row->leave_type?->label() ?? '-';
                    if (!empty($selectedColumns['start_date'])) $dataRow[] = $row->start_date ? Carbon::parse($row->start_date)->format('Y-m-d') : '-';
                    if (!empty($selectedColumns['end_date'])) $dataRow[] = $row->end_date ? Carbon::parse($row->end_date)->format('Y-m-d') : '-';
                    if (!empty($selectedColumns['days_count'])) $dataRow[] = $row->days_count;
                    if (!empty($selectedColumns['replacement'])) $dataRow[] = $row->replacementEmployee->name ?? '-';
                    if (!empty($selectedColumns['status'])) $dataRow[] = $row->status?->label() ?? '-';
                    if (!empty($selectedColumns['reason'])) $dataRow[] = $row->reason ?? '-';
                    if (!empty($selectedColumns['created_at'])) $dataRow[] = $row->created_at ? $row->created_at->format('Y-m-d') : '-';

                    fputcsv($output, $dataRow);
                }
            } elseif ($reportType === 'permissions') {
                $records = $this->reportRepository->getPermissionsRecords($filters);

                foreach ($records as $index => $row) {
                    $dataRow = [];
                    if (!empty($selectedColumns['index'])) $dataRow[] = $index + 1;
                    if (!empty($selectedColumns['code'])) $dataRow[] = $row->employee->employee_code ?? '-';
                    if (!empty($selectedColumns['employee'])) $dataRow[] = $row->employee->name ?? '-';
                    if (!empty($selectedColumns['department'])) $dataRow[] = $row->employee->department->name ?? '-';
                    if (!empty($selectedColumns['branch'])) $dataRow[] = $row->employee->primaryBranch->name ?? '-';
                    if (!empty($selectedColumns['date'])) $dataRow[] = $row->date ? Carbon::parse($row->date)->format('Y-m-d') : '-';
                    if (!empty($selectedColumns['permission_type'])) $dataRow[] = $row->type?->label() ?? '-';
                    if (!empty($selectedColumns['start_time'])) $dataRow[] = $row->start_time ?? '-';
                    if (!empty($selectedColumns['end_time'])) $dataRow[] = $row->end_time ?? '-';
                    if (!empty($selectedColumns['minutes'])) $dataRow[] = $row->duration_minutes ?? '0';
                    if (!empty($selectedColumns['hours'])) $dataRow[] = round(($row->duration_minutes ?? 0) / 60, 2);
                    if (!empty($selectedColumns['status'])) $dataRow[] = $row->status?->label() ?? '-';
                    if (!empty($selectedColumns['reason'])) $dataRow[] = $row->reason ?? '-';

                    fputcsv($output, $dataRow);
                }
            } else {
                // Balances
                $records = $this->reportRepository->getLeaveBalancesRecords($filters);

                foreach ($records as $index => $row) {
                    $dataRow = [];
                    if (!empty($selectedColumns['index'])) $dataRow[] = $index + 1;
                    if (!empty($selectedColumns['code'])) $dataRow[] = $row->employee->employee_code ?? '-';
                    if (!empty($selectedColumns['employee'])) $dataRow[] = $row->employee->name ?? '-';
                    if (!empty($selectedColumns['department'])) $dataRow[] = $row->employee->department->name ?? '-';
                    if (!empty($selectedColumns['branch'])) $dataRow[] = $row->employee->primaryBranch->name ?? '-';
                    if (!empty($selectedColumns['hire_date'])) $dataRow[] = $row->hire_date ? Carbon::parse($row->hire_date)->format('Y-m-d') : '-';
                    if (!empty($selectedColumns['annual_entitled'])) $dataRow[] = $row->annual_entitled;
                    if (!empty($selectedColumns['annual_consumed'])) $dataRow[] = $row->annual_consumed;
                    if (!empty($selectedColumns['annual_pending'])) $dataRow[] = $row->annual_pending;
                    if (!empty($selectedColumns['annual_remaining'])) $dataRow[] = $row->annual_remaining;
                    if (!empty($selectedColumns['casual_consumed'])) $dataRow[] = $row->casual_consumed;
                    if (!empty($selectedColumns['sick_consumed'])) $dataRow[] = $row->sick_consumed;
                    if (!empty($selectedColumns['total_entitled'])) $dataRow[] = $row->total_entitled;
                    if (!empty($selectedColumns['total_consumed'])) $dataRow[] = $row->total_consumed;
                    if (!empty($selectedColumns['total_remaining'])) $dataRow[] = $row->total_remaining;
                    if (!empty($selectedColumns['consumption_rate'])) $dataRow[] = $row->consumption_rate . '%';

                    fputcsv($output, $dataRow);
                }
            }

            fclose($output);
        }, 200, $headers);
    }
}
