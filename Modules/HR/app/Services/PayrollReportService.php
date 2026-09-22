<?php

namespace Modules\HR\Services;

use Carbon\Carbon;
use Modules\HR\Enums\LoanStatus;
use Modules\HR\Enums\LoanType;
use Modules\HR\Enums\PayrollPaymentStatus;
use Modules\HR\Enums\PayrollStatus;
use Modules\HR\Enums\PenaltyStatus;
use Modules\HR\Enums\RewardStatus;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\HR\Models\Payroll;
use Modules\HR\Repositories\Contracts\PayrollReportRepositoryInterface;
use Modules\Nursery\Models\NurseryBranch;
use Modules\Settings\Models\PrintTemplate;
use Modules\Settings\Models\Setting;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PayrollReportService
{
    public function __construct(
        protected PayrollReportRepositoryInterface $reportRepository
    ) {}

    /**
     * Get all available report columns based on report type.
     *
     * @param string $reportType
     * @return array<string, array<string, mixed>>
     */
    public function getAvailableColumns(string $reportType = 'payroll_sheet'): array
    {
        if ($reportType === 'loans') {
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
                'loan_type' => [
                    'label'   => __('Loan Type'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '100px',
                ],
                'start_date' => [
                    'label'   => __('Start Date'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '95px',
                ],
                'amount' => [
                    'label'   => __('Total Loan Amount'),
                    'default' => true,
                    'class'   => 'text-center fw-bold',
                    'width'   => '100px',
                ],
                'installments_count' => [
                    'label'   => __('Installments'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '80px',
                ],
                'monthly_installment' => [
                    'label'   => __('Monthly Installment'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '100px',
                ],
                'total_paid' => [
                    'label'   => __('Total Paid'),
                    'default' => true,
                    'class'   => 'text-center text-success',
                    'width'   => '100px',
                ],
                'remaining_amount' => [
                    'label'   => __('Remaining Amount'),
                    'default' => true,
                    'class'   => 'text-center fw-bold text-danger',
                    'width'   => '100px',
                ],
                'status' => [
                    'label'   => __('Status'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '90px',
                ],
            ];
        }

        if ($reportType === 'rewards_penalties') {
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
                'item_kind' => [
                    'label'   => __('Item Type'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '110px',
                ],
                'date' => [
                    'label'   => __('Date'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '95px',
                ],
                'amount' => [
                    'label'   => __('Amount'),
                    'default' => true,
                    'class'   => 'text-center fw-bold',
                    'width'   => '95px',
                ],
                'type_name' => [
                    'label'   => __('Classification'),
                    'default' => true,
                    'class'   => '',
                    'width'   => 'auto',
                ],
                'reason' => [
                    'label'   => __('Reason'),
                    'default' => true,
                    'class'   => '',
                    'width'   => 'auto',
                ],
                'status' => [
                    'label'   => __('Status'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '90px',
                ],
                'approved_by' => [
                    'label'   => __('Approved By'),
                    'default' => false,
                    'class'   => '',
                    'width'   => 'auto',
                ],
            ];
        }

        if ($reportType === 'eos') {
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
                'hire_date' => [
                    'label'   => __('Hire Date'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '95px',
                ],
                'service_duration' => [
                    'label'   => __('Service Period'),
                    'default' => true,
                    'class'   => 'text-center font-monospace',
                    'width'   => '130px',
                ],
                'monthly_salary' => [
                    'label'   => __('Monthly Basic Wage'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '110px',
                ],
                'tier1_provision' => [
                    'label'   => __('Tier 1 (<= 5 Yrs)'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '110px',
                ],
                'tier2_provision' => [
                    'label'   => __('Tier 2 (> 5 Yrs)'),
                    'default' => true,
                    'class'   => 'text-center',
                    'width'   => '110px',
                ],
                'total_eos_provision' => [
                    'label'   => __('Total Cumulative EOS'),
                    'default' => true,
                    'class'   => 'text-center fw-bold text-primary fs-6',
                    'width'   => '130px',
                ],
            ];
        }

        // Default: 'payroll_sheet' (Monthly Payroll Sheet)
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
            'basic_salary' => [
                'label'   => __('Basic Salary'),
                'default' => true,
                'class'   => 'text-center',
                'width'   => '95px',
            ],
            'allowances' => [
                'label'   => __('Allowances'),
                'default' => true,
                'class'   => 'text-center',
                'width'   => '90px',
            ],
            'overtime' => [
                'label'   => __('Overtime'),
                'default' => true,
                'class'   => 'text-center',
                'width'   => '85px',
            ],
            'rewards' => [
                'label'   => __('Rewards'),
                'default' => true,
                'class'   => 'text-center',
                'width'   => '85px',
            ],
            'gross_salary' => [
                'label'   => __('Total Gross'),
                'default' => true,
                'class'   => 'text-center fw-bold text-dark',
                'width'   => '100px',
            ],
            'late_deduction' => [
                'label'   => __('Late Deduct'),
                'default' => false,
                'class'   => 'text-center',
                'width'   => '85px',
            ],
            'absence_deduction' => [
                'label'   => __('Absence Deduct'),
                'default' => false,
                'class'   => 'text-center',
                'width'   => '85px',
            ],
            'loans_deduction' => [
                'label'   => __('Loans Deduct'),
                'default' => true,
                'class'   => 'text-center text-danger',
                'width'   => '90px',
            ],
            'penalties_deduction' => [
                'label'   => __('Penalties'),
                'default' => false,
                'class'   => 'text-center text-danger',
                'width'   => '85px',
            ],
            'insurance_deduction' => [
                'label'   => __('Insurance'),
                'default' => false,
                'class'   => 'text-center',
                'width'   => '85px',
            ],
            'total_deductions' => [
                'label'   => __('Total Deductions'),
                'default' => true,
                'class'   => 'text-center fw-bold text-danger',
                'width'   => '100px',
            ],
            'net_salary' => [
                'label'   => __('Net Salary'),
                'default' => true,
                'class'   => 'text-center fw-bold text-success fs-6',
                'width'   => '110px',
            ],
            'payment_status' => [
                'label'   => __('Payment Status'),
                'default' => true,
                'class'   => 'text-center',
                'width'   => '95px',
            ],
            'actions' => [
                'label'   => __('Payslip'),
                'default' => true,
                'class'   => 'text-center',
                'width'   => '75px',
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
    public function resolveSelectedColumns(string $reportType = 'payroll_sheet', ?array $requestedColumns = null): array
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
        $reportType = $filters['report_type'] ?? 'payroll_sheet';
        $availableColumns = $this->getAvailableColumns($reportType);
        $selectedColumns  = $this->resolveSelectedColumns($reportType, $filters['columns'] ?? null);

        $kpis = [];

        if ($reportType === 'loans') {
            $records = $this->reportRepository->getLoansPaginated($filters, $perPage);
            $kpis    = $this->reportRepository->getLoansKPIs($filters);
        } elseif ($reportType === 'rewards_penalties') {
            $records = $this->reportRepository->getRewardsPenaltiesPaginated($filters, $perPage);
            $kpis    = $this->reportRepository->getRewardsPenaltiesKPIs($filters);
        } elseif ($reportType === 'eos') {
            $records = $this->reportRepository->getEosProvisionsPaginated($filters, $perPage);
            $kpis    = $this->reportRepository->getEosProvisionsKPIs($filters);
        } else {
            // Default: 'payroll_sheet'
            $records = $this->reportRepository->getPayrollSheetPaginated($filters, $perPage);
            $kpis    = $this->reportRepository->getPayrollSheetKPIs($filters);
        }

        $departments = Department::all();
        $branches    = NurseryBranch::all();
        $employees   = Employee::select('id', 'employee_code')->with('translations')->orderBy('id', 'desc')->get();
        $payrolls    = Payroll::orderBy('payroll_month', 'desc')->get();
        $loanStatuses = LoanStatus::cases();
        $loanTypes    = LoanType::cases();
        $paymentStatuses = PayrollPaymentStatus::cases();

        $printTemplates = PrintTemplate::where('is_active', true)->get();
        $defaultTemplate = $printTemplates->where('code', 'payslip_voucher')->first()
            ?? $printTemplates->where('is_default', true)->first()
            ?? $printTemplates->first();

        return compact(
            'reportType',
            'records',
            'kpis',
            'availableColumns',
            'selectedColumns',
            'departments',
            'branches',
            'employees',
            'payrolls',
            'loanStatuses',
            'loanTypes',
            'paymentStatuses',
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
        $reportType = $filters['report_type'] ?? 'payroll_sheet';
        $availableColumns = $this->getAvailableColumns($reportType);
        $selectedColumns  = $this->resolveSelectedColumns($reportType, $filters['columns'] ?? null);

        $kpis = [];

        if ($reportType === 'loans') {
            $records = $this->reportRepository->getLoansRecords($filters);
            $kpis    = $this->reportRepository->getLoansKPIs($filters);
        } elseif ($reportType === 'rewards_penalties') {
            $records = $this->reportRepository->getRewardsPenaltiesRecords($filters);
            $kpis    = $this->reportRepository->getRewardsPenaltiesKPIs($filters);
        } elseif ($reportType === 'eos') {
            $records = $this->reportRepository->getEosProvisionsRecords($filters);
            $kpis    = $this->reportRepository->getEosProvisionsKPIs($filters);
        } else {
            $records = $this->reportRepository->getPayrollSheetRecords($filters);
            $kpis    = $this->reportRepository->getPayrollSheetKPIs($filters);
        }

        $availableTemplates = PrintTemplate::where('is_active', true)->get();
        $template = null;

        if ($templateId) {
            $template = $availableTemplates->where('id', $templateId)->first();
        }

        $template = $template
            ?? $availableTemplates->where('code', 'payslip_voucher')->first()
            ?? $availableTemplates->where('code', 'general_report')->first()
            ?? $availableTemplates->where('is_default', true)->first()
            ?? $availableTemplates->first();

        $globalSetting = Setting::first();

        return compact(
            'reportType',
            'records',
            'kpis',
            'availableColumns',
            'selectedColumns',
            'template',
            'availableTemplates',
            'globalSetting'
        );
    }

    /**
     * Get data for a single employee payslip voucher print.
     *
     * @param int $itemId
     * @param int|null $templateId
     * @return array<string, mixed>
     */
    public function getSinglePayslipData(int $itemId, ?int $templateId = null): array
    {
        $item = $this->reportRepository->findPayrollItemWithDetails($itemId);
        if (!$item) {
            abort(404, __('Payroll item not found.'));
        }

        $availableTemplates = PrintTemplate::where('is_active', true)->get();
        $template = null;

        if ($templateId) {
            $template = $availableTemplates->where('id', $templateId)->first();
        }

        $template = $template
            ?? $availableTemplates->where('code', 'payslip_voucher')->first()
            ?? $availableTemplates->where('code', 'general_report')->first()
            ?? $availableTemplates->first();

        $globalSetting = Setting::first();

        return compact('item', 'template', 'availableTemplates', 'globalSetting');
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
        $reportType = $filters['report_type'] ?? 'payroll_sheet';
        $availableColumns = $this->getAvailableColumns($reportType);
        $selectedColumns  = $this->resolveSelectedColumns($reportType, $requestedColumns);

        $fileName = "payroll_{$reportType}_report_" . Carbon::now()->format('Y_m_d_His') . '.csv';

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

            // Write Header
            $headerRow = [];
            foreach ($availableColumns as $colKey => $colDef) {
                if ($colKey === 'actions') continue;
                if (!empty($selectedColumns[$colKey])) {
                    $headerRow[] = $colDef['label'];
                }
            }
            fputcsv($output, $headerRow);

            if ($reportType === 'loans') {
                $records = $this->reportRepository->getLoansRecords($filters);
                foreach ($records as $index => $row) {
                    $dataRow = [];
                    if (!empty($selectedColumns['index'])) $dataRow[] = $index + 1;
                    if (!empty($selectedColumns['code'])) $dataRow[] = $row->employee->employee_code ?? '-';
                    if (!empty($selectedColumns['employee'])) $dataRow[] = $row->employee->name ?? '-';
                    if (!empty($selectedColumns['department'])) $dataRow[] = $row->employee->department->name ?? '-';
                    if (!empty($selectedColumns['branch'])) $dataRow[] = $row->employee->primaryBranch->name ?? '-';
                    if (!empty($selectedColumns['loan_type'])) $dataRow[] = $row->type?->label() ?? '-';
                    if (!empty($selectedColumns['start_date'])) $dataRow[] = $row->start_date ? Carbon::parse($row->start_date)->format('Y-m-d') : '-';
                    if (!empty($selectedColumns['amount'])) $dataRow[] = $row->amount;
                    if (!empty($selectedColumns['installments_count'])) $dataRow[] = $row->installments_count;
                    if (!empty($selectedColumns['monthly_installment'])) $dataRow[] = $row->monthly_installment;
                    if (!empty($selectedColumns['total_paid'])) $dataRow[] = $row->total_paid;
                    if (!empty($selectedColumns['remaining_amount'])) $dataRow[] = $row->remaining_amount;
                    if (!empty($selectedColumns['status'])) $dataRow[] = $row->status?->label() ?? '-';

                    fputcsv($output, $dataRow);
                }
            } elseif ($reportType === 'rewards_penalties') {
                $records = $this->reportRepository->getRewardsPenaltiesRecords($filters);
                foreach ($records as $index => $row) {
                    $dataRow = [];
                    if (!empty($selectedColumns['index'])) $dataRow[] = $index + 1;
                    if (!empty($selectedColumns['code'])) $dataRow[] = $row->employee->employee_code ?? '-';
                    if (!empty($selectedColumns['employee'])) $dataRow[] = $row->employee->name ?? '-';
                    if (!empty($selectedColumns['department'])) $dataRow[] = $row->employee->department->name ?? '-';
                    if (!empty($selectedColumns['branch'])) $dataRow[] = $row->employee->primaryBranch->name ?? '-';
                    if (!empty($selectedColumns['item_kind'])) $dataRow[] = $row->item_kind_label;
                    if (!empty($selectedColumns['date'])) $dataRow[] = $row->date ? Carbon::parse($row->date)->format('Y-m-d') : '-';
                    if (!empty($selectedColumns['amount'])) $dataRow[] = ($row->item_kind === 'penalty' ? '-' : '+') . $row->amount;
                    if (!empty($selectedColumns['type_name'])) $dataRow[] = $row->type_name;
                    if (!empty($selectedColumns['reason'])) $dataRow[] = $row->reason ?? '-';
                    if (!empty($selectedColumns['status'])) $dataRow[] = $row->status?->label() ?? '-';
                    if (!empty($selectedColumns['approved_by'])) $dataRow[] = $row->approved_by;

                    fputcsv($output, $dataRow);
                }
            } elseif ($reportType === 'eos') {
                $records = $this->reportRepository->getEosProvisionsRecords($filters);
                foreach ($records as $index => $row) {
                    $dataRow = [];
                    if (!empty($selectedColumns['index'])) $dataRow[] = $index + 1;
                    if (!empty($selectedColumns['code'])) $dataRow[] = $row->employee->employee_code ?? '-';
                    if (!empty($selectedColumns['employee'])) $dataRow[] = $row->employee->name ?? '-';
                    if (!empty($selectedColumns['department'])) $dataRow[] = $row->employee->department->name ?? '-';
                    if (!empty($selectedColumns['branch'])) $dataRow[] = $row->employee->primaryBranch->name ?? '-';
                    if (!empty($selectedColumns['hire_date'])) $dataRow[] = $row->hire_date;
                    if (!empty($selectedColumns['service_duration'])) $dataRow[] = $row->service_duration_text;
                    if (!empty($selectedColumns['monthly_salary'])) $dataRow[] = $row->monthly_salary;
                    if (!empty($selectedColumns['tier1_provision'])) $dataRow[] = $row->tier1_provision;
                    if (!empty($selectedColumns['tier2_provision'])) $dataRow[] = $row->tier2_provision;
                    if (!empty($selectedColumns['total_eos_provision'])) $dataRow[] = $row->total_eos_provision;

                    fputcsv($output, $dataRow);
                }
            } else {
                // Payroll Sheet
                $records = $this->reportRepository->getPayrollSheetRecords($filters);
                foreach ($records as $index => $row) {
                    $dataRow = [];
                    if (!empty($selectedColumns['index'])) $dataRow[] = $index + 1;
                    if (!empty($selectedColumns['code'])) $dataRow[] = $row->employee->employee_code ?? '-';
                    if (!empty($selectedColumns['employee'])) $dataRow[] = $row->employee->name ?? '-';
                    if (!empty($selectedColumns['department'])) $dataRow[] = $row->employee->department->name ?? '-';
                    if (!empty($selectedColumns['branch'])) $dataRow[] = $row->employee->primaryBranch->name ?? '-';
                    if (!empty($selectedColumns['basic_salary'])) $dataRow[] = $row->basic_salary;
                    if (!empty($selectedColumns['allowances'])) $dataRow[] = $row->allowances_amount;
                    if (!empty($selectedColumns['overtime'])) $dataRow[] = $row->overtime_amount;
                    if (!empty($selectedColumns['rewards'])) $dataRow[] = $row->rewards_amount;
                    if (!empty($selectedColumns['gross_salary'])) $dataRow[] = $row->gross_salary;
                    if (!empty($selectedColumns['late_deduction'])) $dataRow[] = $row->late_deduction;
                    if (!empty($selectedColumns['absence_deduction'])) $dataRow[] = $row->absence_deduction;
                    if (!empty($selectedColumns['loans_deduction'])) $dataRow[] = $row->loans_deduction;
                    if (!empty($selectedColumns['penalties_deduction'])) $dataRow[] = $row->penalties_deduction;
                    if (!empty($selectedColumns['insurance_deduction'])) $dataRow[] = $row->insurance_deduction;
                    if (!empty($selectedColumns['total_deductions'])) $dataRow[] = $row->total_deductions;
                    if (!empty($selectedColumns['net_salary'])) $dataRow[] = $row->net_salary;
                    if (!empty($selectedColumns['payment_status'])) $dataRow[] = $row->payment_status?->label() ?? '-';

                    fputcsv($output, $dataRow);
                }
            }

            fclose($output);
        }, 200, $headers);
    }
}
