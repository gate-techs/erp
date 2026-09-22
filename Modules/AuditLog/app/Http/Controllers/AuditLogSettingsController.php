<?php

namespace Modules\AuditLog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\AuditLog\Models\AuditLogSetting;
use Modules\AuditLog\Services\AuditConnectionService;

class AuditLogSettingsController extends Controller
{
    public function __construct(
        protected AuditConnectionService $connectionService
    ) {}

    /**
     * Check that current authenticated user is a developer.
     */
    protected function authorizeDeveloper(): void
    {
        $user = auth()->user();
        if (!$user || (!$user->isDeveloper() && $user->user_type?->value !== 'developer')) {
            abort(403, __('هذه الإعدادات مخصصة لمطور النظام فقط (Developer Only).'));
        }
    }

    /**
     * Show audit log settings page.
     */
    public function index(): View
    {
        $this->authorizeDeveloper();

        $settings = AuditLogSetting::getOrCreate();

        return view('auditlog::settings', compact('settings'));
    }

    /**
     * Update audit log settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $this->authorizeDeveloper();

        $validated = $request->validate([
            'is_external_db_enabled' => 'nullable|boolean',
            'driver'                 => 'required|string|in:mysql,pgsql,mariadb,sqlsrv',
            'host'                   => 'nullable|string|max:255',
            'port'                   => 'nullable|string|max:10',
            'database'               => 'nullable|string|max:255',
            'username'               => 'nullable|string|max:255',
            'password'               => 'nullable|string|max:255',
            'fallback_to_local'      => 'nullable|boolean',
            'is_queue_enabled'       => 'nullable|boolean',
        ]);

        $settings = AuditLogSetting::getOrCreate();

        $settings->is_external_db_enabled = (bool) $request->has('is_external_db_enabled');
        $settings->driver                 = $validated['driver'] ?? 'mysql';
        $settings->host                   = $validated['host'] ?? null;
        $settings->port                   = $validated['port'] ?? '3306';
        $settings->database               = $validated['database'] ?? null;
        $settings->username               = $validated['username'] ?? null;
        $settings->fallback_to_local      = (bool) $request->has('fallback_to_local');
        $settings->is_queue_enabled       = (bool) $request->has('is_queue_enabled');

        // Only update password if provided
        if ($request->filled('password')) {
            $settings->password = $request->input('password');
        }

        $settings->save();

        // If external DB is enabled, automatically run migration/table sync
        if ($settings->is_external_db_enabled && !empty($settings->host) && !empty($settings->database)) {
            $syncResult = $this->connectionService->syncExternalSchema($settings);
            
            $settings->last_tested_at    = now();
            $settings->last_test_status  = $syncResult['success'] ? 'success' : 'failed';
            $settings->last_test_message = $syncResult['message'];
            $settings->save();

            if ($syncResult['success']) {
                return redirect()->route('audit-logs.settings.index')
                    ->with('success', __('تم حفظ الإعدادات وإنشاء/مزامنة جدول السجلات (activity_log) بنجاح على قاعدة البيانات الخارجية!'));
            } else {
                return redirect()->route('audit-logs.settings.index')
                    ->with('warning', __('تم حفظ الإعدادات، ولكن تعذر إنشاء الجدول تلقائياً: ') . $syncResult['message']);
            }
        }

        return redirect()->route('audit-logs.settings.index')
            ->with('success', __('تم حفظ وتحديث إعدادات سجل الأنشطة بنجاح!'));
    }

    /**
     * AJAX Test Connection.
     */
    public function testConnection(Request $request): JsonResponse
    {
        $this->authorizeDeveloper();

        $settings = AuditLogSetting::getOrCreate();

        $params = [
            'driver'   => $request->input('driver', $settings->driver),
            'host'     => $request->input('host', $settings->host),
            'port'     => $request->input('port', $settings->port),
            'database' => $request->input('database', $settings->database),
            'username' => $request->input('username', $settings->username),
            'password' => $request->filled('password') ? $request->input('password') : ($settings->getDecryptedPassword() ?? ''),
        ];

        $result = $this->connectionService->testConnection($params);

        // Update last test status in DB
        $settings->last_tested_at   = now();
        $settings->last_test_status = $result['success'] ? 'success' : 'failed';
        $settings->last_test_message = $result['message'];
        $settings->save();

        return response()->json($result);
    }

    /**
     * AJAX Sync / Create activity_log table on external database.
     */
    public function syncSchema(Request $request): JsonResponse
    {
        $this->authorizeDeveloper();

        $settings = AuditLogSetting::getOrCreate();

        if ($request->filled('host')) {
            $settings->host = $request->input('host');
        }
        if ($request->filled('port')) {
            $settings->port = $request->input('port');
        }
        if ($request->filled('database')) {
            $settings->database = $request->input('database');
        }
        if ($request->filled('username')) {
            $settings->username = $request->input('username');
        }
        if ($request->filled('password')) {
            $settings->password = $request->input('password');
        }
        if ($request->filled('driver')) {
            $settings->driver = $request->input('driver');
        }

        $result = $this->connectionService->syncExternalSchema($settings);

        return response()->json($result);
    }
}
