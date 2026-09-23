<?php

namespace Modules\AI\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Modules\AI\Models\AIProvider;
use Modules\AI\Models\AITask;
use Modules\AI\Models\AIUsageLog;

class AISettingsController extends Controller
{
    public function index(): View
    {
        $this->authorizeDeveloper();

        $logUsageEnabled = (bool) config('ai.log_usage', false);

        $usageByModel = $logUsageEnabled ? AIUsageLog::query()
            ->selectRaw('
                model,
                driver,
                COUNT(*) as total_requests,
                SUM(prompt_tokens) as total_prompt_tokens,
                SUM(completion_tokens) as total_completion_tokens,
                SUM(total_tokens) as total_tokens,
                SUM(characters_count) as total_characters,
                MAX(created_at) as last_used_at
            ')
            ->groupBy('model', 'driver')
            ->orderByDesc('total_requests')
            ->get() : collect();

        $recentLogs = $logUsageEnabled ? AIUsageLog::query()
            ->with('user:id,name')
            ->latest()
            ->take(15)
            ->get() : collect();

        return view('ai::settings.index', [
            'providers' => AIProvider::with('tasks')->orderBy('name')->get(),
            'tasks' => AITask::with(['providers' => fn ($query) => $query->orderByPivot('priority')])->orderBy('name')->get(),
            'drivers' => array_keys(config('ai.providers', [])),
            'usageByModel' => $usageByModel,
            'recentLogs' => $recentLogs,
            'totalTokensAll' => $logUsageEnabled ? AIUsageLog::sum('total_tokens') : 0,
            'totalRequestsAll' => $logUsageEnabled ? AIUsageLog::count() : 0,
        ]);
    }

    public function storeProvider(Request $request): RedirectResponse
    {
        $this->authorizeDeveloper();

        $data = $this->validatedProvider($request);
        AIProvider::create($data);

        return back()->with('success', __('AI provider created successfully.'));
    }

    public function updateProvider(Request $request, AIProvider $provider): RedirectResponse
    {
        $this->authorizeDeveloper();

        $data = $this->validatedProvider($request, $provider->id);

        if (blank($data['api_key'] ?? null)) {
            unset($data['api_key']);
        }

        $provider->update($data);

        return back()->with('success', __('AI provider updated successfully.'));
    }

    public function destroyProvider(AIProvider $provider): RedirectResponse
    {
        $this->authorizeDeveloper();

        $provider->delete();

        return back()->with('success', __('AI provider deleted successfully.'));
    }

    public function assignProvider(Request $request, AITask $task): RedirectResponse
    {
        $this->authorizeDeveloper();

        $data = $request->validate([
            'provider_id' => ['required', 'integer', 'exists:ai_providers,id'],
            'priority' => ['required', 'integer', 'min:1', 'max:99'],
            'is_default' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($task, $data): void {
            if ((bool) ($data['is_default'] ?? false)) {
                $task->providers()->get()->each(function (AIProvider $provider) use ($task): void {
                    $task->providers()->updateExistingPivot($provider->id, ['is_default' => false]);
                });
            }

            $task->providers()->syncWithoutDetaching([
                $data['provider_id'] => [
                    'priority' => $data['priority'],
                    'is_default' => (bool) ($data['is_default'] ?? false),
                    'is_active' => (bool) ($data['is_active'] ?? true),
                ],
            ]);
        });

        return back()->with('success', __('AI provider assignment saved successfully.'));
    }

    public function removeAssignment(AITask $task, AIProvider $provider): RedirectResponse
    {
        $this->authorizeDeveloper();

        $task->providers()->detach($provider->id);

        return back()->with('success', __('AI provider assignment removed successfully.'));
    }

    private function validatedProvider(Request $request, ?int $providerId = null): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'alpha_dash', 'max:50', 'unique:ai_providers,code,' . ($providerId ?: 'NULL')],
            'name' => ['required', 'string', 'max:120'],
            'driver' => ['required', 'string', 'in:' . implode(',', array_keys(config('ai.providers', [])))],
            'api_key' => ['nullable', 'string', 'max:1000'],
            'model' => ['nullable', 'string', 'max:120'],
            'base_url' => ['nullable', 'url', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function authorizeDeveloper(): void
    {
        $user = auth()->user();

        if (!$user || (!$user->isDeveloper() && $user->user_type?->value !== 'developer')) {
            abort(403, __('AI settings are available to developers only.'));
        }
    }
}
