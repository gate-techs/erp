<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Settings\Services\PrintTemplateService;

class PrintTemplateController extends Controller
{
    public function __construct(
        private readonly PrintTemplateService $service
    ) {}

    /**
     * Display a listing of print templates.
     */
    public function index(): View
    {
        abort_unless(auth()->user()->can('settings.print-templates.view'), 403);

        $templates = $this->service->getAllTemplates();

        return view('settings::print_templates.index', compact('templates'));
    }

    /**
     * Show the form for editing the specified template.
     */
    public function edit(int $id): View
    {
        abort_unless(auth()->user()->can('settings.print-templates.edit'), 403);

        $template = $this->service->getTemplateById($id);
        abort_unless($template, 404);

        return view('settings::print_templates.edit', compact('template'));
    }

    /**
     * Update the specified print template.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        abort_unless(auth()->user()->can('settings.print-templates.edit'), 403);

        $template = $this->service->getTemplateById($id);
        abort_unless($template, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'page_size' => ['required', 'string', 'in:A4,A5,Letter'],
            'orientation' => ['required', 'string', 'in:portrait,landscape'],
            'margins.top' => ['required', 'numeric', 'min:0', 'max:50'],
            'margins.right' => ['required', 'numeric', 'min:0', 'max:50'],
            'margins.bottom' => ['required', 'numeric', 'min:0', 'max:50'],
            'margins.left' => ['required', 'numeric', 'min:0', 'max:50'],
            'margins.unit' => ['required', 'string', 'in:mm,cm,in'],
            'header_settings.show_header' => ['nullable', 'boolean'],
            'header_settings.show_logo' => ['nullable', 'boolean'],
            'header_settings.logo_position' => ['nullable', 'string', 'in:right,center,left'],
            'header_settings.show_company_name' => ['nullable', 'boolean'],
            'header_settings.show_cr' => ['nullable', 'boolean'],
            'header_settings.show_tax' => ['nullable', 'boolean'],
            'header_settings.show_phone' => ['nullable', 'boolean'],
            'header_settings.show_email' => ['nullable', 'boolean'],
            'header_settings.show_report_title' => ['nullable', 'boolean'],
            'footer_settings.show_footer' => ['nullable', 'boolean'],
            'footer_settings.show_page_number' => ['nullable', 'boolean'],
            'footer_settings.show_timestamp' => ['nullable', 'boolean'],
            'footer_settings.show_printed_by' => ['nullable', 'boolean'],
            'footer_settings.show_qr_code' => ['nullable', 'boolean'],
            'footer_settings.footer_text' => ['nullable', 'string', 'max:500'],
            'signatures' => ['nullable', 'array'],
            'signatures.*.title' => ['nullable', 'string'],
            'signatures.*.title_en' => ['nullable', 'string'],
            'signatures.*.name' => ['nullable', 'string'],
            'signatures.*.show' => ['nullable', 'boolean'],
            'custom_css' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data = [
            'name' => $validated['name'],
            'name_ar' => $validated['name_ar'] ?? $template->name_ar,
            'description' => $validated['description'] ?? null,
            'page_size' => $validated['page_size'],
            'orientation' => $validated['orientation'],
            'margins' => [
                'top' => (int) $validated['margins']['top'],
                'right' => (int) $validated['margins']['right'],
                'bottom' => (int) $validated['margins']['bottom'],
                'left' => (int) $validated['margins']['left'],
                'unit' => $validated['margins']['unit'],
            ],
            'header_settings' => [
                'show_header' => (bool) $request->input('header_settings.show_header', false),
                'show_logo' => (bool) $request->input('header_settings.show_logo', false),
                'logo_position' => $request->input('header_settings.logo_position', 'right'),
                'show_company_name' => (bool) $request->input('header_settings.show_company_name', false),
                'show_cr' => (bool) $request->input('header_settings.show_cr', false),
                'show_tax' => (bool) $request->input('header_settings.show_tax', false),
                'show_phone' => (bool) $request->input('header_settings.show_phone', false),
                'show_email' => (bool) $request->input('header_settings.show_email', false),
                'show_report_title' => (bool) $request->input('header_settings.show_report_title', false),
            ],
            'footer_settings' => [
                'show_footer' => (bool) $request->input('footer_settings.show_footer', false),
                'show_page_number' => (bool) $request->input('footer_settings.show_page_number', false),
                'show_timestamp' => (bool) $request->input('footer_settings.show_timestamp', false),
                'show_printed_by' => (bool) $request->input('footer_settings.show_printed_by', false),
                'show_qr_code' => (bool) $request->input('footer_settings.show_qr_code', false),
                'footer_text' => $request->input('footer_settings.footer_text'),
            ],
            'signature_settings' => array_values(array_filter($request->input('signatures', []), fn($s) => !empty($s['title']))),
            'custom_css' => $validated['custom_css'] ?? null,
            'is_active' => (bool) $request->input('is_active', true),
        ];

        $this->service->updateTemplate($id, $data);

        return redirect()->route('settings.print-templates.edit', $id)->with('success', __('Print template updated successfully.'));
    }

    /**
     * Preview template with sample data.
     */
    public function preview(int $id): View
    {
        abort_unless(auth()->user()->can('settings.print-templates.view'), 403);

        $template = $this->service->getTemplateById($id);
        abort_unless($template, 404);

        return view('settings::print_templates.preview', compact('template'));
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(int $id): JsonResponse
    {
        abort_unless(auth()->user()->can('settings.print-templates.edit'), 403);

        $status = $this->service->toggleStatus($id);

        return response()->json([
            'success' => true,
            'is_active' => $status,
            'message' => $status ? __('Template activated.') : __('Template deactivated.'),
        ]);
    }
}
