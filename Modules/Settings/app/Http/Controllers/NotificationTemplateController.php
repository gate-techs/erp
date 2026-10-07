<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Settings\Enums\NotificationType;
use Modules\Settings\Models\NotificationTemplate;
use Modules\Settings\Services\NotificationTemplateService;

class NotificationTemplateController extends Controller
{
    public function __construct(private readonly NotificationTemplateService $service) {}

    public function index(Request $request): View
    {
        $selectedModule = $request->string('module')->toString() ?: 'all';
        $templates = $this->service->all($selectedModule);
        $modules = $templates->pluck('module')->unique()->values();

        return view('settings::notification_templates.index', compact('templates', 'selectedModule', 'modules'));
    }

    public function edit(int $id): View
    {
        $template = $this->service->find($id);
        abort_unless($template, 404);

        return view('settings::notification_templates.edit', [
            'template' => $template,
            'types' => NotificationType::cases(),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $template = $this->service->find($id);
        abort_unless($template, 404);

        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:30'],
            'is_active' => ['nullable', 'boolean'],
            'title_ar' => ['required', 'string', 'max:255'],
            'message_ar' => ['required', 'string'],
            'action_url_ar' => ['nullable', 'string', 'max:500'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'message_en' => ['nullable', 'string'],
            'action_url_en' => ['nullable', 'string', 'max:500'],
        ]);

        $this->service->update($template, [
            'name_ar' => $validated['name_ar'],
            'name_en' => $validated['name_en'] ?? $validated['name_ar'],
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'color' => $validated['color'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ], [
            'ar' => [
                'title' => $validated['title_ar'],
                'message' => $validated['message_ar'],
                'action_url' => $validated['action_url_ar'] ?? null,
            ],
            'en' => [
                'title' => $validated['title_en'] ?? $validated['title_ar'],
                'message' => $validated['message_en'] ?? $validated['message_ar'],
                'action_url' => $validated['action_url_en'] ?? ($validated['action_url_ar'] ?? null),
            ],
        ]);

        return redirect()->route('settings.notification-templates.edit', $id)
            ->with('success', __('Notification template updated successfully.'));
    }

    public function toggleStatus(int $id): JsonResponse
    {
        $template = $this->service->find($id);
        abort_unless($template, 404);

        $status = $this->service->toggle($template);

        return response()->json([
            'success' => true,
            'is_active' => $status,
            'message' => $status ? __('Template activated.') : __('Template deactivated.'),
        ]);
    }
}
