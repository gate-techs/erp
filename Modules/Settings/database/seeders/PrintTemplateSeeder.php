<?php

namespace Modules\Settings\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Settings\Models\PrintTemplate;

class PrintTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'General Reports Template',
                'name_ar' => 'قالب التقارير العامة',
                'code' => 'general_report',
                'type' => 'report',
                'description' => 'القالب الأساسي المعتمد لكافة تقارير الجداول والكشوفات والبيانات الإدارية والموارد البشرية في النظام.',
                'page_size' => 'A4',
                'orientation' => 'portrait',
                'margins' => [
                    'top' => 12,
                    'right' => 12,
                    'bottom' => 12,
                    'left' => 12,
                    'unit' => 'mm',
                ],
                'header_settings' => [
                    'show_header' => true,
                    'show_logo' => true,
                    'logo_position' => 'right',
                    'show_company_name' => true,
                    'show_cr' => true,
                    'show_tax' => true,
                    'show_phone' => true,
                    'show_email' => true,
                    'show_report_title' => true,
                ],
                'footer_settings' => [
                    'show_footer' => true,
                    'show_page_number' => true,
                    'show_timestamp' => true,
                    'show_printed_by' => true,
                    'show_qr_code' => true,
                    'footer_text' => 'تم إصدار هذا التقرير آلياً من نظام إدارة الموارد المتكامل - جيت تك',
                ],
                'signature_settings' => [
                    ['title' => 'إعداد / مسؤول الإدخال', 'title_en' => 'Prepared By', 'name' => '', 'show' => true],
                    ['title' => 'مراجعة / رئيس القسم', 'title_en' => 'Reviewed By', 'name' => '', 'show' => true],
                    ['title' => 'اعتماد / المدير العام', 'title_en' => 'Approved By', 'name' => '', 'show' => true],
                ],
                'custom_css' => "/* Custom Print Styles */\n.report-table th { background-color: #f1f3f5 !important; font-weight: 700; }\n.report-table td, .report-table th { padding: 6px 10px; font-size: 12px; border: 1px solid #dee2e6; }",
                'is_active' => true,
                'is_default' => true,
            ],
            [
                'name' => 'Financial & Payslips Template',
                'name_ar' => 'قالب المسيرات والسندات المالية',
                'code' => 'payslip_voucher',
                'type' => 'financial',
                'description' => 'مخصص لمفردات الراتب (Payslip) وسندات القبض والصرف، مع أماكن مخصصة للأختام والاعتماد المالي.',
                'page_size' => 'A4',
                'orientation' => 'portrait',
                'margins' => [
                    'top' => 10,
                    'right' => 10,
                    'bottom' => 10,
                    'left' => 10,
                    'unit' => 'mm',
                ],
                'header_settings' => [
                    'show_header' => true,
                    'show_logo' => true,
                    'logo_position' => 'right',
                    'show_company_name' => true,
                    'show_cr' => true,
                    'show_tax' => true,
                    'show_phone' => false,
                    'show_email' => false,
                    'show_report_title' => true,
                ],
                'footer_settings' => [
                    'show_footer' => true,
                    'show_page_number' => true,
                    'show_timestamp' => true,
                    'show_printed_by' => true,
                    'show_qr_code' => true,
                    'footer_text' => 'يعتبر هذا المستند رسمياً وسارياً بموجب الأنظمة واللوائح المالية المعتمدة.',
                ],
                'signature_settings' => [
                    ['title' => 'المحاسب المسؤول', 'title_en' => 'Accountant', 'name' => '', 'show' => true],
                    ['title' => 'المدير المالي', 'title_en' => 'Financial Manager', 'name' => '', 'show' => true],
                    ['title' => 'توقيع المستلم', 'title_en' => 'Receiver Signature', 'name' => '', 'show' => true],
                ],
                'custom_css' => "/* Financial Slip Styles */\n.financial-card { border: 2px solid #2b3548; padding: 12px; border-radius: 6px; }\n.amount-box { background: #f8f9fa; font-weight: bold; font-size: 14px; }",
                'is_active' => true,
                'is_default' => false,
            ],
            [
                'name' => 'Official Letters & Certificates Template',
                'name_ar' => 'قالب الخطابات والشهادات الرسمية',
                'code' => 'official_letter',
                'type' => 'letter',
                'description' => 'مخصص لشهادات التعريف بالراتب، خطابات التعيين، إخلاء الطرف، بنمط الورق المروس مع دعم العلامة المائية.',
                'page_size' => 'A4',
                'orientation' => 'portrait',
                'margins' => [
                    'top' => 18,
                    'right' => 18,
                    'bottom' => 18,
                    'left' => 18,
                    'unit' => 'mm',
                ],
                'header_settings' => [
                    'show_header' => true,
                    'show_logo' => true,
                    'logo_position' => 'center',
                    'show_company_name' => true,
                    'show_cr' => true,
                    'show_tax' => true,
                    'show_phone' => true,
                    'show_email' => true,
                    'show_report_title' => true,
                ],
                'footer_settings' => [
                    'show_footer' => true,
                    'show_page_number' => true,
                    'show_timestamp' => true,
                    'show_printed_by' => false,
                    'show_qr_code' => true,
                    'footer_text' => 'هذا الخطاب موثق رسمياً وصادر إلكترونياً ولا يعتد به دون التوقيع والختم الرسمي.',
                ],
                'signature_settings' => [
                    ['title' => 'مدير الموارد البشرية', 'title_en' => 'HR Director', 'name' => '', 'show' => true],
                    ['title' => 'الختم الرسمي للمنشأة', 'title_en' => 'Official Stamp', 'name' => '', 'show' => true],
                ],
                'custom_css' => "/* Official Letterhead Styles */\n.letter-body { line-height: 2.2; font-size: 14px; text-align: justify; margin: 30px 10px; }\n.letter-watermark { position: fixed; top: 35%; left: 25%; opacity: 0.05; transform: rotate(-30deg); font-size: 80px; font-weight: bold; pointer-events: none; }",
                'is_active' => true,
                'is_default' => false,
            ],
        ];

        foreach ($templates as $tplData) {
            PrintTemplate::updateOrCreate(
                ['code' => $tplData['code']],
                $tplData
            );
        }
    }
}
