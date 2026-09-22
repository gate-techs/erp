<?php

namespace Modules\HR\Services;

use Carbon\Carbon;
use Modules\HR\Enums\EmployeeStatus;
use Modules\HR\Enums\EmploymentType;
use Modules\HR\Enums\Gender;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\HR\Models\HrSetting;
use Modules\HR\Models\JobTitle;
use Modules\HR\Repositories\Contracts\EmployeeReportRepositoryInterface;
use Modules\Nursery\Models\NurseryBranch;
use Modules\Settings\Models\PrintTemplate;
use Modules\Settings\Models\Setting;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployeeReportService
{
    public function __construct(
        protected EmployeeReportRepositoryInterface $reportRepository
    ) {}

    /**
     * Get all available report columns based on report type.
     *
     * @param string $reportType
     * @return array<string, array<string, mixed>>
     */
    public function getAvailableColumns(string $reportType = 'master'): array
    {
        if ($reportType === 'expiring_docs') {
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
                    'width'   => '90px',
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
                'document_type' => [
                    'label'   => __('Document Type'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '130px',
                ],
                'document_number' => [
                    'label'   => __('Document Number'),
                    'default' => true,
                    'class'   => 'text-center font-monospace',
                    'width'   => '120px',
                ],
                'issue_date' => [
                    'label'   => __('Issue Date'),
                    'default' => false,
                    'class'   => 'text-center',
                    'width'   => '95px',
                ],
                'expiry_date' => [
                    'label'   => __('Expiry Date'),
                    'default' => true,
                    'class'   => 'text-center font-monospace',
                    'width'   => '95px',
                ],
                'days_remaining' => [
                    'label'   => __('Days Remaining'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '100px',
                ],
                'status' => [
                    'label'   => __('Document Status'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '110px',
                ],
                'action' => [
                    'label'   => __('File Attachment'),
                    'default' => false,
                    'class'   => 'text-center',
                    'width'   => '80px',
                ],
            ];
        }

        // Default: 'master' (Employee Master List)
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
                'width'   => '90px',
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
            'job_title' => [
                'label'   => __('Job Title'),
                'default' => true,
                'class'   => '',
                'width'   => 'auto',
            ],
            'nationality' => [
                'label'   => __('Nationality'),
                'default' => true,
                'class'   => 'text-center',
                'width'   => '90px',
            ],
            'national_id' => [
                'label'   => __('Civil ID / National ID'),
                'default' => true,
                'class'   => 'text-center font-monospace',
                'width'   => '110px',
            ],
            'phone' => [
                'label'   => __('Phone'),
                'default' => true,
                'class'   => 'text-center font-monospace',
                'width'   => '105px',
            ],
            'email' => [
                'label'   => __('Email'),
                'default' => false,
                'class'   => '',
                'width'   => 'auto',
            ],
            'employment_type' => [
                'label'   => __('Employment Type'),
                'default' => true,
                'class'   => 'text-center',
                'width'   => '95px',
            ],
            'hire_date' => [
                'label'   => __('Hire Date'),
                'default' => true,
                'class'   => 'text-center font-monospace',
                'width'   => '95px',
            ],
            'basic_salary' => [
                'label'   => __('Basic Salary'),
                'default' => true,
                'class'   => 'text-center font-monospace',
                'width'   => '95px',
            ],
            'status' => [
                'label'   => __('Work Status'),
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
    public function resolveSelectedColumns(string $reportType = 'master', ?array $requestedColumns = null): array
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
        $reportType = $filters['report_type'] ?? 'master';
        $availableColumns = $this->getAvailableColumns($reportType);
        $selectedColumns  = $this->resolveSelectedColumns($reportType, $filters['columns'] ?? null);

        $records = null;
        $kpis    = [];
        $demographics = [];

        if ($reportType === 'expiring_docs') {
            $records = $this->reportRepository->getExpiringDocumentsPaginated($filters, $perPage);
            $kpis    = $this->reportRepository->getExpiringDocumentsKPIs($filters);
        } elseif ($reportType === 'demographics') {
            $demographics = $this->reportRepository->getWorkforceDemographics($filters);
        } else {
            // Default: 'master'
            $records = $this->reportRepository->getEmployeeMasterPaginated($filters, $perPage);
            $kpis    = $this->reportRepository->getEmployeeMasterKPIs($filters);
        }

        $departments     = Department::all();
        $branches        = NurseryBranch::all();
        $jobTitles       = JobTitle::all();
        $employeeStatuses = EmployeeStatus::cases();
        $employmentTypes = EmploymentType::cases();
        $genders         = Gender::cases();
        $documentTypes   = HrSetting::where('type', 'document_type')->get();

        $nationalities   = Employee::select('nationality')->distinct()->whereNotNull('nationality')->pluck('nationality');

        $printTemplates = PrintTemplate::where('is_active', true)->get();
        $defaultTemplate = $printTemplates->where('is_default', true)->first()
            ?? $printTemplates->where('code', 'general_report')->first()
            ?? $printTemplates->first();

        $globalSetting = Setting::first();

        return compact(
            'reportType',
            'records',
            'kpis',
            'demographics',
            'availableColumns',
            'selectedColumns',
            'departments',
            'branches',
            'jobTitles',
            'employeeStatuses',
            'employmentTypes',
            'genders',
            'documentTypes',
            'nationalities',
            'printTemplates',
            'defaultTemplate',
            'globalSetting'
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
        $reportType = $filters['report_type'] ?? 'master';
        $availableColumns = $this->getAvailableColumns($reportType);
        $selectedColumns  = $this->resolveSelectedColumns($reportType, $filters['columns'] ?? null);

        $records = null;
        $kpis    = [];
        $demographics = [];

        if ($reportType === 'expiring_docs') {
            $records = $this->reportRepository->getExpiringDocumentsRecords($filters);
            $kpis    = $this->reportRepository->getExpiringDocumentsKPIs($filters);
        } elseif ($reportType === 'demographics') {
            $demographics = $this->reportRepository->getWorkforceDemographics($filters);
        } else {
            $records = $this->reportRepository->getEmployeeMasterRecords($filters);
            $kpis    = $this->reportRepository->getEmployeeMasterKPIs($filters);
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
            'kpis',
            'demographics',
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
        $reportType = $filters['report_type'] ?? 'master';
        $availableColumns = $this->getAvailableColumns($reportType);
        $selectedColumns  = $this->resolveSelectedColumns($reportType, $requestedColumns);

        $fileName = "employee_{$reportType}_report_" . Carbon::now()->format('Y_m_d_His') . '.csv';

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

            if ($reportType === 'expiring_docs') {
                // Header
                $headerRow = [];
                foreach ($availableColumns as $colKey => $colDef) {
                    if ($colKey === 'action') continue;
                    if (!empty($selectedColumns[$colKey])) {
                        $headerRow[] = $colDef['label'];
                    }
                }
                fputcsv($output, $headerRow);

                $records = $this->reportRepository->getExpiringDocumentsRecords($filters);
                foreach ($records as $index => $row) {
                    $dataRow = [];
                    if (!empty($selectedColumns['index'])) $dataRow[] = $index + 1;
                    if (!empty($selectedColumns['code'])) $dataRow[] = $row->employee->employee_code ?? '-';
                    if (!empty($selectedColumns['employee'])) $dataRow[] = $row->employee->name ?? '-';
                    if (!empty($selectedColumns['department'])) $dataRow[] = $row->employee->department->name ?? '-';
                    if (!empty($selectedColumns['branch'])) $dataRow[] = $row->employee->primaryBranch->name ?? '-';
                    if (!empty($selectedColumns['document_type'])) $dataRow[] = $row->document_type;
                    if (!empty($selectedColumns['document_number'])) $dataRow[] = $row->document_number;
                    if (!empty($selectedColumns['issue_date'])) $dataRow[] = $row->issue_date;
                    if (!empty($selectedColumns['expiry_date'])) $dataRow[] = $row->expiry_date;
                    if (!empty($selectedColumns['days_remaining'])) $dataRow[] = $row->days_remaining . ' ' . __('يوم');
                    if (!empty($selectedColumns['status'])) $dataRow[] = $row->status_label;

                    fputcsv($output, $dataRow);
                }
            } elseif ($reportType === 'demographics') {
                $demographics = $this->reportRepository->getWorkforceDemographics($filters);

                // Section 1: Department Breakdown
                fputcsv($output, [__('--- توزيع الموظفين حسب الأقسام والتكلفة ---')]);
                fputcsv($output, [__('القسم'), __('عدد الموظفين'), __('النسبة (%)'), __('إجمالي الرواتب'), __('متوسط الراتب')]);
                foreach ($demographics['byDepartment'] as $row) {
                    fputcsv($output, [$row->name, $row->count, $row->percentage . '%', $row->total_salary, $row->avg_salary]);
                }

                fputcsv($output, []); // Empty row

                // Section 2: Nationality Breakdown
                fputcsv($output, [__('--- توزيع الموظفين حسب الجنسيات والتكلفة ---')]);
                fputcsv($output, [__('الجنسية'), __('عدد الموظفين'), __('النسبة (%)'), __('إجمالي الرواتب'), __('متوسط الراتب')]);
                foreach ($demographics['byNationality'] as $row) {
                    fputcsv($output, [$row->name, $row->count, $row->percentage . '%', $row->total_salary, $row->avg_salary]);
                }

                fputcsv($output, []);

                // Section 3: Branch Breakdown
                fputcsv($output, [__('--- توزيع الموظفين حسب الفروع والتكلفة ---')]);
                fputcsv($output, [__('الفرع'), __('عدد الموظفين'), __('النسبة (%)'), __('إجمالي الرواتب'), __('متوسط الراتب')]);
                foreach ($demographics['byBranch'] as $row) {
                    fputcsv($output, [$row->name, $row->count, $row->percentage . '%', $row->total_salary, $row->avg_salary]);
                }
            } else {
                // Employee Master List
                $headerRow = [];
                foreach ($availableColumns as $colKey => $colDef) {
                    if (!empty($selectedColumns[$colKey])) {
                        $headerRow[] = $colDef['label'];
                    }
                }
                fputcsv($output, $headerRow);

                $records = $this->reportRepository->getEmployeeMasterRecords($filters);
                foreach ($records as $index => $row) {
                    $dataRow = [];
                    if (!empty($selectedColumns['index'])) $dataRow[] = $index + 1;
                    if (!empty($selectedColumns['code'])) $dataRow[] = $row->employee_code ?? '-';
                    if (!empty($selectedColumns['employee'])) $dataRow[] = $row->name ?? '-';
                    if (!empty($selectedColumns['department'])) $dataRow[] = $row->department->name ?? '-';
                    if (!empty($selectedColumns['branch'])) $dataRow[] = $row->primaryBranch->name ?? '-';
                    if (!empty($selectedColumns['job_title'])) $dataRow[] = $row->jobTitle->name ?? '-';
                    if (!empty($selectedColumns['nationality'])) $dataRow[] = $row->nationality ?? '-';
                    if (!empty($selectedColumns['national_id'])) $dataRow[] = $row->national_id ?? '-';
                    if (!empty($selectedColumns['phone'])) $dataRow[] = $row->phone ?? '-';
                    if (!empty($selectedColumns['email'])) $dataRow[] = $row->email ?? '-';
                    if (!empty($selectedColumns['employment_type'])) $dataRow[] = $row->employment_type?->label() ?? '-';
                    if (!empty($selectedColumns['hire_date'])) $dataRow[] = $row->hire_date ? Carbon::parse($row->hire_date)->format('Y-m-d') : '-';
                    if (!empty($selectedColumns['basic_salary'])) $dataRow[] = $row->basic_salary ?? 0;
                    if (!empty($selectedColumns['status'])) $dataRow[] = $row->status?->label() ?? '-';

                    fputcsv($output, $dataRow);
                }
            }

            fclose($output);
        }, 200, $headers);
    }
}
