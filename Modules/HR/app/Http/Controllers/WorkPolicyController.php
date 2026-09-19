<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\HR\Models\Department;
use Modules\HR\Models\HrSetting;
use Modules\HR\Models\HrWorkPolicy;
use Modules\HR\Models\JobTitle;
use Modules\HR\Models\OfficialHoliday;
use Modules\HR\Models\Shift;
use Modules\HR\Models\WorkSchedule;
use Modules\HR\Services\WorkPolicyService;

class WorkPolicyController extends Controller
{
    //======================
    public function __construct(
        protected WorkPolicyService $workPolicyService
    ) {}

    //======================
    /**
     * Display the HR Work Policies configuration screen.
     */
    public function index(): View
    {
        $viewData = $this->workPolicyService->getSettingsViewData();
        $counts   = $this->getCounts();

        return view('hr::settings.policies.index', array_merge($viewData, [
            'counts' => $counts,
        ]));
    }

    //======================
    /**
     * Update HR Work Policies, Sick Leave Tiers, and EOS Rules.
     */
    public function update(Request $request): JsonResponse|RedirectResponse
    {
        try {
            $payload = $request->all();

            $this->workPolicyService->saveWorkPolicies($payload);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Work policies updated successfully.'),
                ]);
            }

            return redirect()->route('hr.settings.policies.index')
                ->with('success', __('Work policies updated successfully.'));
        } catch (Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Failed to update work policies: ') . $e->getMessage(),
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('Failed to update work policies: ') . $e->getMessage());
        }
    }

    //======================
    /**
     * Reset HR Work Policies to default settings.
     */
    public function resetDefaults(Request $request): JsonResponse|RedirectResponse
    {
        try {
            $this->workPolicyService->resetToDefaults();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Policies reset to default values successfully.'),
                ]);
            }

            return redirect()->route('hr.settings.policies.index')
                ->with('success', __('Policies reset to default values successfully.'));
        } catch (Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Failed to reset policies: ') . $e->getMessage(),
                ], 422);
            }

            return redirect()->back()
                ->with('error', __('Failed to reset policies: ') . $e->getMessage());
        }
    }

    //======================
    /**
     * Sidebar counts helper.
     */
    protected function getCounts(): array
    {
        return [
            'administrations'   => Department::whereNull('parent_id')->count(),
            'departments'       => Department::whereNotNull('parent_id')->count(),
            'job_titles'        => JobTitle::count(),
            'allowance_types'   => HrSetting::where('type', 'allowance-types')->count(),
            'document_types'    => HrSetting::where('type', 'document-types')->count(),
            'shifts'            => Shift::count(),
            'work_schedules'    => WorkSchedule::count(),
            'official_holidays' => OfficialHoliday::count(),
            'policies'          => HrWorkPolicy::count(),
        ];
    }
}
