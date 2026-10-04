<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Settings\Models\EmailTemplate;
use Modules\Settings\Services\EmailTemplateService;

class EmailTemplateController extends Controller
{
    public function __construct(
        private readonly EmailTemplateService $service
    ) {}

    /**
     * Display a listing of email templates with statistics and module filters.
     */
    public function index(Request $request): View
    {
        $selectedModule = $request->get('module', 'all');
        $templates = $this->service->getAllTemplates($selectedModule);
        $stats = $this->service->getModuleStats();

        return view('settings::email_templates.index', compact('templates', 'stats', 'selectedModule'));
    }

    /**
     * Show the form for creating a new custom email template.
     */
    public function create(): View
    {
        $modules = [
            'tasks'      => __('Tasks & Projects'),
            'hr'         => __('Human Resources'),
            'accounting' => __('Accounting & Finance'),
            'general'    => __('General System'),
        ];

        return view('settings::email_templates.create', compact('modules'));
    }

    /**
     * Store a newly created email template in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code'                  => ['required', 'string', 'alpha_dash', 'max:100', 'unique:settings_email_templates,code'],
            'module'                => ['required', 'string', 'max:50'],
            'name_ar'               => ['required', 'string', 'max:255'],
            'name'                  => ['nullable', 'string', 'max:255'],
            'description'           => ['nullable', 'string'],
            'subject_ar'            => ['required', 'string', 'max:255'],
            'subject_en'            => ['nullable', 'string', 'max:255'],
            'body_ar'               => ['required', 'string'],
            'body_en'               => ['nullable', 'string'],
            'action_button_text_ar' => ['nullable', 'string', 'max:100'],
            'action_button_text_en' => ['nullable', 'string', 'max:100'],
            'action_url_variable'   => ['nullable', 'string', 'max:100'],
            'variables'             => ['nullable', 'string'],
            'is_active'             => ['nullable', 'boolean'],
        ]);

        // Process variables list
        $varsArray = [];
        if (!empty($validated['variables'])) {
            $lines = explode("\n", str_replace("\r", "", $validated['variables']));
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $parts = explode(':', $line, 2);
                    $key = trim($parts[0]);
                    $desc = isset($parts[1]) ? trim($parts[1]) : $key;
                    $varsArray[$key] = $desc;
                }
            }
        }

        EmailTemplate::create([
            'code'                  => strtolower($validated['code']),
            'module'                => $validated['module'],
            'name_ar'               => $validated['name_ar'],
            'name'                  => $validated['name'] ?? $validated['name_ar'],
            'description'           => $validated['description'] ?? null,
            'subject_ar'            => $validated['subject_ar'],
            'subject_en'            => $validated['subject_en'] ?? $validated['subject_ar'],
            'body_ar'               => $validated['body_ar'],
            'body_en'               => $validated['body_en'] ?? $validated['body_ar'],
            'action_button_text_ar' => $validated['action_button_text_ar'] ?? null,
            'action_button_text_en' => $validated['action_button_text_en'] ?? null,
            'action_url_variable'   => $validated['action_url_variable'] ?? null,
            'variables'             => $varsArray,
            'is_active'             => (bool) $request->input('is_active', true),
            'is_system'             => false,
        ]);

        return redirect()->route('settings.email-templates.index')->with('success', __('Email template created successfully.'));
    }

    /**
     * Show the form for editing the specified email template.
     */
    public function edit(int $id): View
    {
        $template = $this->service->getTemplateById($id);
        abort_unless($template, 404);

        $sampleData = $this->service->generateSampleData($template);

        return view('settings::email_templates.edit', compact('template', 'sampleData'));
    }

    /**
     * Update the specified email template in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $template = $this->service->getTemplateById($id);
        abort_unless($template, 404);

        $validated = $request->validate([
            'name_ar'               => ['required', 'string', 'max:255'],
            'name'                  => ['nullable', 'string', 'max:255'],
            'description'           => ['nullable', 'string'],
            'subject_ar'            => ['required', 'string', 'max:255'],
            'subject_en'            => ['nullable', 'string', 'max:255'],
            'body_ar'               => ['required', 'string'],
            'body_en'               => ['nullable', 'string'],
            'action_button_text_ar' => ['nullable', 'string', 'max:100'],
            'action_button_text_en' => ['nullable', 'string', 'max:100'],
            'action_url_variable'   => ['nullable', 'string', 'max:100'],
            'is_active'             => ['nullable', 'boolean'],
        ]);

        $data = [
            'name_ar'               => $validated['name_ar'],
            'name'                  => $validated['name'] ?? $template->name,
            'description'           => $validated['description'] ?? null,
            'subject_ar'            => $validated['subject_ar'],
            'subject_en'            => $validated['subject_en'] ?? $validated['subject_ar'],
            'body_ar'               => $validated['body_ar'],
            'body_en'               => $validated['body_en'] ?? $validated['body_ar'],
            'action_button_text_ar' => $validated['action_button_text_ar'] ?? null,
            'action_button_text_en' => $validated['action_button_text_en'] ?? null,
            'action_url_variable'   => $validated['action_url_variable'] ?? null,
            'is_active'             => (bool) $request->input('is_active', true),
        ];

        $this->service->updateTemplate($id, $data);

        return redirect()->route('settings.email-templates.edit', $id)->with('success', __('Email template updated successfully.'));
    }

    /**
     * Remove the specified template from storage (only custom non-system templates).
     */
    public function destroy(int $id): RedirectResponse
    {
        $template = $this->service->getTemplateById($id);
        abort_unless($template, 404);

        if ($template->is_system) {
            return back()->with('error', __('System email templates cannot be deleted.'));
        }

        $template->delete();

        return redirect()->route('settings.email-templates.index')->with('success', __('Email template deleted successfully.'));
    }

    /**
     * Live preview endpoint with sample data.
     */
    public function preview(Request $request, int $id): JsonResponse
    {
        $template = $this->service->getTemplateById($id);
        abort_unless($template, 404);

        $locale = $request->get('locale', app()->getLocale());
        $sampleData = $this->service->generateSampleData($template);

        // Allow previewing unsaved body if sent in request
        if ($request->has('preview_body')) {
            if ($locale === 'ar') {
                $template->body_ar = $request->get('preview_body');
                $template->subject_ar = $request->get('preview_subject', $template->subject_ar);
                $template->action_button_text_ar = $request->get('preview_btn_text', $template->action_button_text_ar);
            } else {
                $template->body_en = $request->get('preview_body');
                $template->subject_en = $request->get('preview_subject', $template->subject_en);
                $template->action_button_text_en = $request->get('preview_btn_text', $template->action_button_text_en);
            }
        }

        $renderedHtml = $template->renderFullHtml($sampleData, $locale);
        $renderedSubject = $template->renderSubject($sampleData, $locale);

        return response()->json([
            'success' => true,
            'subject' => $renderedSubject,
            'html'    => $renderedHtml,
        ]);
    }

    /**
     * Send test email to specified recipient.
     */
    public function sendTest(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $template = $this->service->getTemplateById($id);
        abort_unless($template, 404);

        $success = $this->service->sendTest($template, $request->input('email'));

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => __('Test email sent successfully to :email', ['email' => $request->input('email')]),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('Failed to send test email. Please verify mail server settings.'),
        ], 500);
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(int $id): JsonResponse
    {
        $status = $this->service->toggleStatus($id);

        return response()->json([
            'success'   => true,
            'is_active' => $status,
            'message'   => $status ? __('Template activated.') : __('Template deactivated.'),
        ]);
    }
}
