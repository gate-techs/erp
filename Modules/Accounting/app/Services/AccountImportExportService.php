<?php

namespace Modules\Accounting\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Modules\Accounting\Enums\AccountType;
use Modules\Accounting\Models\Account;
use Modules\Accounting\Models\Entity;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountImportExportService
{
    /**
     * Download a styled standard Excel template (.xlsx) for Chart of Accounts.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();

        // ----------------------------------------------------
        // Sheet 1: Template Data
        // ----------------------------------------------------
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(__('Accounts Template'));
        $sheet->setRightToLeft(app()->getLocale() === 'ar');

        $headers = [
            'A1' => ['title' => 'code', 'label' => 'كود الحساب (Code) *', 'width' => 18],
            'B1' => ['title' => 'name_ar', 'label' => 'اسم الحساب بالعربية (Arabic Name) *', 'width' => 34],
            'C1' => ['title' => 'name_en', 'label' => 'اسم الحساب بالإنجليزية (English Name)', 'width' => 34],
            'D1' => ['title' => 'parent_code', 'label' => 'كود الحساب الأب (Parent Code)', 'width' => 22],
            'E1' => ['title' => 'account_type', 'label' => 'نوع الحساب (Account Type) *', 'width' => 24],
            'F1' => ['title' => 'is_group', 'label' => 'حساب رئيسي/تجميعي (1=نعم, 0=لا) *', 'width' => 28],
            'G1' => ['title' => 'reconcile', 'label' => 'يقبل مطابقة بنك (1=نعم, 0=لا)', 'width' => 26],
            'H1' => ['title' => 'description_ar', 'label' => 'الوصف بالعربية (Description AR)', 'width' => 32],
            'I1' => ['title' => 'description_en', 'label' => 'الوصف بالإنجليزية (Description EN)', 'width' => 32],
        ];

        // Format Header Row
        foreach ($headers as $cell => $info) {
            $sheet->setCellValue($cell, $info['label']);
            $colLetter = substr($cell, 0, 1);
            $sheet->getColumnDimension($colLetter)->setWidth($info['width']);
        }

        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2B4360'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(32);

        // Realistic Sample Data
        $sampleRows = [
            ['1', 'الأصول', 'Assets', '', 'asset', 1, 0, 'الأصول والممتلكات', 'Assets and property'],
            ['11', 'الأصول المتداولة', 'Current Assets', '1', 'asset', 1, 0, 'الأصول قصيرة الأجل', 'Short term assets'],
            ['1101', 'النقدية وما في حكمها', 'Cash & Cash Equivalents', '11', 'asset', 1, 0, 'النقدية بالصناديق والبنوك', 'Cash in hand & banks'],
            ['110101', 'الصندوق الرئيسي (الخزينة)', 'Main Cash Safe', '1101', 'asset', 0, 1, 'خزينة المؤسسة الرئيسية', 'Main office safe'],
            ['110102', 'الحساب البنكي التجاري', 'Commercial Bank Account', '1101', 'asset', 0, 1, 'الحساب البنكي الجاري', 'Commercial checking account'],
            ['1102', 'العملاء والمدينون', 'Accounts Receivable', '11', 'asset', 0, 1, 'حسابات أولياء الأمور والعملاء', 'Receivables from parents'],
            ['12', 'الأصول غير المتداولة (الثابتة)', 'Non-Current Assets', '1', 'asset', 1, 0, 'الأصول والمعدات الثابتة', 'Fixed assets and equipment'],
            ['1201', 'الأثاث والتجهيزات', 'Furniture and Fixtures', '12', 'asset', 0, 0, 'أثاث الفصول والمكاتب', 'Classroom & office furniture'],
            ['2', 'الالتزامات والخصوم', 'Liabilities', '', 'liability', 1, 0, 'الالتزامات والديون', 'Liabilities and payables'],
            ['21', 'الالتزامات المتداولة', 'Current Liabilities', '2', 'liability', 1, 0, 'الالتزامات قصيرة الأجل', 'Current payables'],
            ['2101', 'الموردون والدائنون التجاريون', 'Accounts Payable', '21', 'liability', 0, 1, 'مستحقات الموردين والشركات', 'Trade payables'],
            ['2102', 'الرواتب والأجور المستحقة', 'Accrued Salaries and Wages', '21', 'liability', 0, 0, 'أجور مستحقة لم تدفع', 'Accrued unpaid salaries'],
            ['3', 'حقوق الملكية', 'Equity', '', 'equity', 1, 0, 'حقوق الشركاء ورأس المال', 'Owner equity & capital'],
            ['31', 'رأس المال المدفوع', 'Paid-in Capital', '3', 'equity', 0, 0, 'رأس مال الكيان المعتمد', 'Authorized paid capital'],
            ['4', 'الإيرادات', 'Revenue', '', 'income', 1, 0, 'إيرادات النشاط التشغيلي', 'Revenues and turnover'],
            ['41', 'إيرادات الرسوم والاشتراكات', 'Tuition & Subscription Fees', '4', 'income', 0, 0, 'رسوم واشتراكات الطلاب', 'Student tuition fees'],
            ['5', 'المصروفات', 'Expenses', '', 'expense', 1, 0, 'المصروفات التشغيلية والإدارية', 'Operating & general expenses'],
            ['51', 'المصروفات التشغيلية', 'Operating Expenses', '5', 'expense', 1, 0, 'مصاريف التشغيل اليومية', 'Day to day operations'],
            ['5101', 'رواتب وأجور المعلمات والموظفين', 'Staff Salaries and Wages', '51', 'expense', 0, 0, 'رواتب الكادر التعليمي والإداري', 'Staff payroll expenses'],
        ];

        $rowIndex = 2;
        foreach ($sampleRows as $row) {
            $sheet->setCellValueExplicit("A{$rowIndex}", (string) $row[0], DataType::TYPE_STRING);
            $sheet->setCellValue("B{$rowIndex}", $row[1]);
            $sheet->setCellValue("C{$rowIndex}", $row[2]);
            $sheet->setCellValueExplicit("D{$rowIndex}", (string) $row[3], DataType::TYPE_STRING);
            $sheet->setCellValue("E{$rowIndex}", $row[4]);
            $sheet->setCellValue("F{$rowIndex}", $row[5]);
            $sheet->setCellValue("G{$rowIndex}", $row[6]);
            $sheet->setCellValue("H{$rowIndex}", $row[7]);
            $sheet->setCellValue("I{$rowIndex}", $row[8]);

            $sheet->getStyle("A{$rowIndex}:I{$rowIndex}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A{$rowIndex}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$rowIndex}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$rowIndex}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$rowIndex}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G{$rowIndex}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getRowDimension($rowIndex)->setRowHeight(22);
            $rowIndex++;
        }

        // Add Light Grid Borders
        $sheet->getStyle('A1:I' . ($rowIndex - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'D0D7DE'],
                ],
            ],
        ]);

        // Configure Native Excel Dropdown Lists (Data Validation)
        $typeValidation = new DataValidation();
        $typeValidation->setType(DataValidation::TYPE_LIST);
        $typeValidation->setErrorStyle(DataValidation::STYLE_STOP);
        $typeValidation->setAllowBlank(false);
        $typeValidation->setShowInputMessage(true);
        $typeValidation->setShowErrorMessage(true);
        $typeValidation->setShowDropDown(true);
        $typeValidation->setErrorTitle('نوع حساب غير صالح');
        $typeValidation->setError('يرجى اختيار نوع الحساب من القائمة المنسدلة فقط.');
        $typeValidation->setPromptTitle('نوع الحساب (Account Type)');
        $typeValidation->setPrompt('اختر أحد الأنواع: asset, liability, equity, income, expense, off_balance');
        $typeValidation->setFormula1('"asset,liability,equity,income,expense,off_balance"');

        $groupValidation = new DataValidation();
        $groupValidation->setType(DataValidation::TYPE_LIST);
        $groupValidation->setErrorStyle(DataValidation::STYLE_STOP);
        $groupValidation->setAllowBlank(false);
        $groupValidation->setShowInputMessage(true);
        $groupValidation->setShowErrorMessage(true);
        $groupValidation->setShowDropDown(true);
        $groupValidation->setErrorTitle('قيمة غير صالحة');
        $groupValidation->setError('يرجى اختيار 1 (حساب رئيسي) أو 0 (حساب فرعي).');
        $groupValidation->setPromptTitle('حساب رئيسي / تجميعي');
        $groupValidation->setPrompt('1 = رئيسي (تجميعي)، 0 = فرعي (يقبل قيود)');
        $groupValidation->setFormula1('"1,0"');

        $reconcileValidation = new DataValidation();
        $reconcileValidation->setType(DataValidation::TYPE_LIST);
        $reconcileValidation->setErrorStyle(DataValidation::STYLE_STOP);
        $reconcileValidation->setAllowBlank(true);
        $reconcileValidation->setShowInputMessage(true);
        $reconcileValidation->setShowErrorMessage(true);
        $reconcileValidation->setShowDropDown(true);
        $reconcileValidation->setErrorTitle('قيمة غير صالحة');
        $reconcileValidation->setError('يرجى اختيار 1 (يقبل مطابقة) أو 0 (لا).');
        $reconcileValidation->setPromptTitle('مطابقة القيود');
        $reconcileValidation->setPrompt('1 = نعم يقبل مطابقة، 0 = لا');
        $reconcileValidation->setFormula1('"1,0"');

        // Apply dropdowns to cells from row 2 up to row 300
        for ($r = 2; $r <= 300; $r++) {
            $sheet->setDataValidation("E{$r}", clone $typeValidation);
            $sheet->setDataValidation("F{$r}", clone $groupValidation);
            $sheet->setDataValidation("G{$r}", clone $reconcileValidation);
        }

        // ----------------------------------------------------
        // Sheet 2: Reference Guide & Allowed Values
        // ----------------------------------------------------
        $guideSheet = $spreadsheet->createSheet();
        $guideSheet->setTitle(__('Allowed Values & Guide'));
        $guideSheet->setRightToLeft(app()->getLocale() === 'ar');

        $guideHeaders = ['العمود / الحقل', 'القيمة المقبولة', 'المعنى والتوضيح'];
        $guideSheet->fromArray([$guideHeaders], null, 'A1');
        $guideSheet->getStyle('A1:C1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '405189']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $guideSheet->getRowDimension(1)->setRowHeight(26);

        $guideRows = [
            ['account_type', 'asset', 'أصول (Assets) - طبيعة مدينة'],
            ['account_type', 'liability', 'التزامات وخصوم (Liabilities) - طبيعة دائنة'],
            ['account_type', 'equity', 'حقوق ملكية ورأس مال (Equity) - طبيعة دائنة'],
            ['account_type', 'income', 'إيرادات (Income / Revenue) - طبيعة دائنة'],
            ['account_type', 'expense', 'مصروفات (Expenses) - طبيعة مدينة'],
            ['account_type', 'off_balance', 'حسابات نظامية / خارج الميزانية (Off-balance)'],
            ['is_group', '1', 'حساب رئيسي (تجميعي) يتفرع منه حسابات أخرى ولا يقبل ترحيل قيود مباشرة'],
            ['is_group', '0', 'حساب فرعي يقبل ترحيل القيود اليومية والحركات المالية المباشرة'],
            ['reconcile', '1', 'يقبل مطابقة الحساب والقيود مع كشوف الحسابات البنكية'],
            ['reconcile', '0', 'لا يتطلب مطابقة دورية'],
            ['parent_code', 'كود الحساب الأب', 'يوضع كود الحساب الرئيسي الذي ينتمي له هذا الحساب. يترك فارغاً للحسابات الرئيسية (أصول، خصوم...)'],
        ];

        $gRow = 2;
        foreach ($guideRows as $r) {
            $guideSheet->fromArray([$r], null, "A{$gRow}");
            $gRow++;
        }
        $guideSheet->getColumnDimension('A')->setWidth(20);
        $guideSheet->getColumnDimension('B')->setWidth(24);
        $guideSheet->getColumnDimension('C')->setWidth(65);

        $spreadsheet->setActiveSheetIndex(0);

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 'chart_of_accounts_template.xlsx', [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="chart_of_accounts_template.xlsx"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /**
     * Export all accounts of an entity to a styled Excel (.xlsx) file.
     */
    public function exportEntityAccounts(int $entityId): StreamedResponse
    {
        $entity = Entity::with('translations')->findOrFail($entityId);
        $entityName = $entity->translate(app()->getLocale())?->name ?? $entity->code;

        $accounts = Account::where('entity_id', $entityId)
            ->with(['translations', 'parent'])
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(__('Chart of Accounts'));
        $sheet->setRightToLeft(app()->getLocale() === 'ar');

        // Header Row
        $headers = [
            'A1' => ['title' => 'code', 'label' => 'كود الحساب (Code)', 'width' => 18],
            'B1' => ['title' => 'name_ar', 'label' => 'اسم الحساب بالعربية (Arabic Name)', 'width' => 36],
            'C1' => ['title' => 'name_en', 'label' => 'اسم الحساب بالإنجليزية (English Name)', 'width' => 36],
            'D1' => ['title' => 'parent_code', 'label' => 'كود الحساب الأب (Parent Code)', 'width' => 22],
            'E1' => ['title' => 'account_type', 'label' => 'نوع الحساب (Account Type)', 'width' => 22],
            'F1' => ['title' => 'is_group', 'label' => 'حساب رئيسي (is_group)', 'width' => 22],
            'G1' => ['title' => 'reconcile', 'label' => 'مطابقة (reconcile)', 'width' => 20],
            'H1' => ['title' => 'description_ar', 'label' => 'الوصف بالعربية', 'width' => 32],
            'I1' => ['title' => 'description_en', 'label' => 'الوصف بالإنجليزية', 'width' => 32],
        ];

        foreach ($headers as $cell => $info) {
            $sheet->setCellValue($cell, $info['label']);
            $colLetter = substr($cell, 0, 1);
            $sheet->getColumnDimension($colLetter)->setWidth($info['width']);
        }

        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1B3B6F'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $rowIndex = 2;
        foreach ($accounts as $account) {
            $sheet->setCellValueExplicit("A{$rowIndex}", (string) $account->code, DataType::TYPE_STRING);
            $sheet->setCellValue("B{$rowIndex}", $account->translate('ar')?->name ?? '');
            $sheet->setCellValue("C{$rowIndex}", $account->translate('en')?->name ?? '');
            $sheet->setCellValueExplicit("D{$rowIndex}", (string) ($account->parent?->code ?? ''), DataType::TYPE_STRING);
            $sheet->setCellValue("E{$rowIndex}", $account->account_type instanceof AccountType ? $account->account_type->value : (string) $account->account_type);
            $sheet->setCellValue("F{$rowIndex}", $account->is_group ? 1 : 0);
            $sheet->setCellValue("G{$rowIndex}", $account->reconcile ? 1 : 0);
            $sheet->setCellValue("H{$rowIndex}", $account->translate('ar')?->description ?? '');
            $sheet->setCellValue("I{$rowIndex}", $account->translate('en')?->description ?? '');

            // Formatting
            $sheet->getStyle("A{$rowIndex}:I{$rowIndex}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A{$rowIndex}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$rowIndex}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$rowIndex}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$rowIndex}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G{$rowIndex}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Light highlight for group accounts
            if ($account->is_group) {
                $sheet->getStyle("A{$rowIndex}:I{$rowIndex}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F4F6F9'],
                    ],
                ]);
            }

            $sheet->getRowDimension($rowIndex)->setRowHeight(22);
            $rowIndex++;
        }

        if ($rowIndex > 2) {
            $sheet->getStyle('A1:I' . ($rowIndex - 1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'E0E4E8'],
                    ],
                ],
            ]);
        }

        // Apply dropdown validations to exported sheet rows
        $typeValidation = new DataValidation();
        $typeValidation->setType(DataValidation::TYPE_LIST);
        $typeValidation->setErrorStyle(DataValidation::STYLE_STOP);
        $typeValidation->setAllowBlank(false);
        $typeValidation->setShowDropDown(true);
        $typeValidation->setFormula1('"asset,liability,equity,income,expense,off_balance"');

        $groupValidation = new DataValidation();
        $groupValidation->setType(DataValidation::TYPE_LIST);
        $groupValidation->setErrorStyle(DataValidation::STYLE_STOP);
        $groupValidation->setShowDropDown(true);
        $groupValidation->setFormula1('"1,0"');

        $reconcileValidation = new DataValidation();
        $reconcileValidation->setType(DataValidation::TYPE_LIST);
        $reconcileValidation->setErrorStyle(DataValidation::STYLE_STOP);
        $reconcileValidation->setShowDropDown(true);
        $reconcileValidation->setFormula1('"1,0"');

        $maxValRow = max(100, $rowIndex + 50);
        for ($r = 2; $r <= $maxValRow; $r++) {
            $sheet->setDataValidation("E{$r}", clone $typeValidation);
            $sheet->setDataValidation("F{$r}", clone $groupValidation);
            $sheet->setDataValidation("G{$r}", clone $reconcileValidation);
        }

        $filename = 'chart_of_accounts_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $entity->code) . '_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /**
     * Import accounts from an uploaded spreadsheet (.xlsx, .xls, .csv).
     *
     * @param UploadedFile $file
     * @param int $entityId
     * @param int|null $userId
     * @return array<string, mixed>
     * @throws Exception
     */
    public function importAccounts(UploadedFile $file, int $entityId, ?int $userId = null): array
    {
        $entity = Entity::findOrFail($entityId);

        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (empty($rows) || count($rows) < 2) {
            throw new Exception(__('The uploaded file does not contain any data rows.'));
        }

        // Identify Header Column Indexes
        $headerRow = array_shift($rows);
        $headerMap = [];

        foreach ($headerRow as $col => $value) {
            $cleaned = strtolower(trim((string) $value));
            if (str_contains($cleaned, 'parent')) {
                $headerMap['parent_code'] = $col;
            } elseif (str_contains($cleaned, 'code') || str_contains($cleaned, 'كود')) {
                $headerMap['code'] = $col;
            } elseif (str_contains($cleaned, 'desc') || str_contains($cleaned, 'وصف')) {
                if (str_contains($cleaned, 'en') || str_contains($cleaned, 'إنجل') || str_contains($cleaned, 'engl')) {
                    $headerMap['description_en'] = $col;
                } else {
                    $headerMap['description_ar'] = $col;
                }
            } elseif (str_contains($cleaned, 'name') || str_contains($cleaned, 'اسم')) {
                if (str_contains($cleaned, 'en') || str_contains($cleaned, 'إنجل') || str_contains($cleaned, 'engl')) {
                    $headerMap['name_en'] = $col;
                } else {
                    $headerMap['name_ar'] = $col;
                }
            } elseif (str_contains($cleaned, 'type') || str_contains($cleaned, 'نوع')) {
                $headerMap['account_type'] = $col;
            } elseif (str_contains($cleaned, 'group') || str_contains($cleaned, 'رئيسي') || str_contains($cleaned, 'تجميعي')) {
                $headerMap['is_group'] = $col;
            } elseif (str_contains($cleaned, 'reconcile') || str_contains($cleaned, 'مطابق')) {
                $headerMap['reconcile'] = $col;
            }
        }

        // Fallback default column mapping if header text matching missed: A=code, B=name_ar, C=name_en, D=parent_code, E=account_type, F=is_group, G=reconcile, H=desc_ar, I=desc_en
        $headerMap['code']           = $headerMap['code'] ?? 'A';
        $headerMap['name_ar']        = $headerMap['name_ar'] ?? 'B';
        $headerMap['name_en']        = $headerMap['name_en'] ?? 'C';
        $headerMap['parent_code']    = $headerMap['parent_code'] ?? 'D';
        $headerMap['account_type']   = $headerMap['account_type'] ?? 'E';
        $headerMap['is_group']       = $headerMap['is_group'] ?? 'F';
        $headerMap['reconcile']      = $headerMap['reconcile'] ?? 'G';
        $headerMap['description_ar'] = $headerMap['description_ar'] ?? 'H';
        $headerMap['description_en'] = $headerMap['description_en'] ?? 'I';

        $parsedRows = [];
        $lineNum = 1;

        foreach ($rows as $row) {
            $lineNum++;
            $code = trim((string) ($row[$headerMap['code']] ?? ''));
            $nameAr = trim((string) ($row[$headerMap['name_ar']] ?? ''));

            // Skip empty rows
            if ($code === '' && $nameAr === '') {
                continue;
            }

            if ($code === '') {
                throw new Exception(__("Row :line: Account Code is required.", ['line' => $lineNum]));
            }

            if ($nameAr === '') {
                throw new Exception(__("Row :line: Arabic Name is required for account ':code'.", ['line' => $lineNum, 'code' => $code]));
            }

            $nameEn = trim((string) ($row[$headerMap['name_en']] ?? ''));
            $parentCode = trim((string) ($row[$headerMap['parent_code']] ?? ''));
            $rawType = strtolower(trim((string) ($row[$headerMap['account_type']] ?? 'asset')));

            // Normalize account type
            $accountType = match ($rawType) {
                'asset', 'أصول', 'أصل', 'الأصول'                                         => AccountType::ASSET,
                'liability', 'خصوم', 'التزامات', 'التزام', 'الخصوم', 'الالتزامات'         => AccountType::LIABILITY,
                'equity', 'حقوق ملكية', 'رأس مال', 'حقوق الملكية'                       => AccountType::EQUITY,
                'income', 'revenue', 'إيرادات', 'إيراد', 'الإيرادات'                     => AccountType::INCOME,
                'expense', 'مصروفات', 'مصاريف', 'مصروف', 'المصروفات'                    => AccountType::EXPENSE,
                'off_balance', 'خارج الميزانية', 'نظامي'                                => AccountType::OFF_BALANCE,
                default                                                                  => AccountType::tryFrom($rawType) ?? AccountType::ASSET,
            };

            $isGroupRaw = trim((string) ($row[$headerMap['is_group']] ?? '0'));
            $isGroup = in_array(strtolower($isGroupRaw), ['1', 'true', 'yes', 'نعم', 'رئيسي', 'تجميعي'], true);

            $reconcileRaw = trim((string) ($row[$headerMap['reconcile']] ?? '0'));
            $reconcile = in_array(strtolower($reconcileRaw), ['1', 'true', 'yes', 'نعم'], true);

            $descAr = trim((string) ($row[$headerMap['description_ar']] ?? ''));
            $descEn = trim((string) ($row[$headerMap['description_en']] ?? ''));

            $parsedRows[] = [
                'line'           => $lineNum,
                'code'           => $code,
                'name_ar'        => $nameAr,
                'name_en'        => $nameEn !== '' ? $nameEn : $nameAr,
                'parent_code'    => $parentCode !== '' ? $parentCode : null,
                'account_type'   => $accountType,
                'is_group'       => $isGroup,
                'reconcile'      => $reconcile,
                'description_ar' => $descAr !== '' ? $descAr : null,
                'description_en' => $descEn !== '' ? $descEn : null,
            ];
        }

        if (empty($parsedRows)) {
            throw new Exception(__('No valid account records found in the uploaded file.'));
        }

        // Topological Sort: Ensure parent accounts are created before their children
        usort($parsedRows, function ($a, $b) {
            $lenA = strlen($a['code']);
            $lenB = strlen($b['code']);
            if ($lenA === $lenB) {
                return strcmp($a['code'], $b['code']);
            }
            return $lenA <=> $lenB;
        });

        $createdCount = 0;
        $updatedCount = 0;

        DB::transaction(function () use ($parsedRows, $entityId, $userId, &$createdCount, &$updatedCount) {
            // Pre-load existing accounts map for this entity: code => Account
            $accountsMap = Account::where('entity_id', $entityId)->get()->keyBy('code');

            foreach ($parsedRows as $item) {
                $code = $item['code'];
                $parentCode = $item['parent_code'];
                $parentId = null;

                if (!empty($parentCode)) {
                    if (isset($accountsMap[$parentCode])) {
                        $parentId = $accountsMap[$parentCode]->id;
                    } else {
                        // Look up in database if not in memory
                        $parentAccount = Account::where('entity_id', $entityId)->where('code', $parentCode)->first();
                        if ($parentAccount) {
                            $parentId = $parentAccount->id;
                            $accountsMap[$parentCode] = $parentAccount;
                        }
                    }
                }

                $parentAccount = $parentId ? ($accountsMap[$parentCode] ?? null) : null;
                $level = $parentAccount ? ($parentAccount->level + 1) : 1;
                $isGroup = $item['is_group'];
                $accountType = $item['account_type'];

                $accountData = [
                    'entity_id'      => $entityId,
                    'parent_id'      => $parentId,
                    'code'           => $code,
                    'account_type'   => $accountType->value,
                    'normal_balance' => $accountType->normalBalance(),
                    'level'          => $level,
                    'is_group'       => $isGroup,
                    'allow_posting'  => !$isGroup,
                    'reconcile'      => $item['reconcile'],
                    'is_active'      => true,
                    'is_deprecated'  => false,
                    'sort_order'     => is_numeric($code) ? (int) $code : 0,
                    'created_by'     => $userId,
                ];

                if (isset($accountsMap[$code])) {
                    // Update existing
                    $account = $accountsMap[$code];
                    $account->update($accountData);
                    $updatedCount++;
                } else {
                    // Create new
                    $account = Account::create($accountData);
                    $createdCount++;
                    $accountsMap[$code] = $account;
                }

                // Update translations
                $transAr = $account->translateOrNew('ar');
                $transAr->name = $item['name_ar'];
                if ($item['description_ar'] !== null) {
                    $transAr->description = $item['description_ar'];
                }

                $transEn = $account->translateOrNew('en');
                $transEn->name = $item['name_en'];
                if ($item['description_en'] !== null) {
                    $transEn->description = $item['description_en'];
                }

                $account->save();
            }
        });

        $totalCount = $createdCount + $updatedCount;

        return [
            'success' => true,
            'created' => $createdCount,
            'updated' => $updatedCount,
            'total'   => $totalCount,
            'message' => __(':count accounts processed successfully (:created created, :updated updated).', [
                'count'   => $totalCount,
                'created' => $createdCount,
                'updated' => $updatedCount,
            ]),
        ];
    }
}
