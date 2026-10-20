<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\HR\Enums\HrSettingStatus;
use Modules\HR\Enums\HrSettingType;
use Modules\HR\Http\Requests\StoreHrSettingRequest;
use Modules\HR\Http\Requests\UpdateHrSettingRequest;
use Modules\HR\Models\HrSetting;
use Modules\HR\Services\HrSettingService;
use Yajra\DataTables\Facades\DataTables;

class HrGeneralSettingController extends Controller
{
    //======================
    public function __construct(
        protected HrSettingService $service
    ) {}

    // =========================================================================
    // 1. Allowance Types (أنواع البدلات)
    // =========================================================================

    //======================
    /**
     * Display listing view for Allowance Types.
     */
    public function allowanceTypes(): View
    {
        abort_if(! auth()->user()->can('hr.settings.allowance-types.view'), 403);

        $parents  = $this->service->getParentsList(HrSettingType::ALLOWANCE_TYPES->value);
        $statuses = HrSettingStatus::cases();

        return view('hr::settings.allowance_types.index', compact('parents', 'statuses'));
    }

    //======================
    /**
     * Server-side DataTables endpoint for Allowance Types.
     */
    public function allowanceTypesData(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.settings.allowance-types.view'), 403);

        $filters = array_merge($request->all(), ['type' => HrSettingType::ALLOWANCE_TYPES->value]);
        $query   = $this->service->getDataTableQuery($filters);

        return $this->buildDataTable($query);
    }

    //======================
    /**
     * Store a newly created Allowance Type.
     */
    public function storeAllowanceType(StoreHrSettingRequest $request): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.settings.allowance-types.create'), 403);

        try {
            $data = $request->validated();
            $data['type'] = HrSettingType::ALLOWANCE_TYPES->value;

            $this->service->createSetting($data, auth()->id());

            return redirect()->route('hr.settings.allowance-types.index')
                ->with('success', __('Allowance Type created successfully.'));
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('Failed to create Allowance Type: ') . $e->getMessage());
        }
    }

    //======================
    /**
     * Update the specified Allowance Type.
     */
    public function updateAllowanceType(UpdateHrSettingRequest $request, int $id): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.settings.allowance-types.edit'), 403);

        try {
            $setting = $this->service->getSettingById($id);

            if (!$setting || $setting->type !== HrSettingType::ALLOWANCE_TYPES) {
                return redirect()->back()->with('error', __('Allowance Type not found.'));
            }

            $data = $request->validated();
            $data['type'] = HrSettingType::ALLOWANCE_TYPES->value;

            $this->service->updateSetting($setting, $data);

            return redirect()->route('hr.settings.allowance-types.index')
                ->with('success', __('Allowance Type updated successfully.'));
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('Failed to update Allowance Type: ') . $e->getMessage());
        }
    }

    //======================
    /**
     * Remove the specified Allowance Type.
     */
    public function destroyAllowanceType(int $id): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.settings.allowance-types.delete'), 403);

        try {
            $setting = $this->service->getSettingById($id);

            if (!$setting || $setting->type !== HrSettingType::ALLOWANCE_TYPES) {
                return redirect()->back()->with('error', __('Allowance Type not found.'));
            }

            $this->service->deleteSetting($setting);

            return redirect()->route('hr.settings.allowance-types.index')
                ->with('success', __('Allowance Type deleted successfully.'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', __('Failed to delete Allowance Type: ') . $e->getMessage());
        }
    }

    // =========================================================================
    // 2. Document Types (أنواع المستندات)
    // =========================================================================

    //======================
    /**
     * Display listing view for Document Types.
     */
    public function documentTypes(): View
    {
        abort_if(! auth()->user()->can('hr.settings.document-types.view'), 403);

        $parents  = $this->service->getParentsList(HrSettingType::DOCUMENT_TYPES->value);
        $statuses = HrSettingStatus::cases();

        return view('hr::settings.document_types.index', compact('parents', 'statuses'));
    }

    //======================
    /**
     * Server-side DataTables endpoint for Document Types.
     */
    public function documentTypesData(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.settings.document-types.view'), 403);

        $filters = array_merge($request->all(), ['type' => HrSettingType::DOCUMENT_TYPES->value]);
        $query   = $this->service->getDataTableQuery($filters);

        return $this->buildDataTable($query);
    }

    //======================
    /**
     * Store a newly created Document Type.
     */
    public function storeDocumentType(StoreHrSettingRequest $request): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.settings.document-types.create'), 403);

        try {
            $data = $request->validated();
            $data['type'] = HrSettingType::DOCUMENT_TYPES->value;

            $this->service->createSetting($data, auth()->id());

            return redirect()->route('hr.settings.document-types.index')
                ->with('success', __('Document Type created successfully.'));
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('Failed to create Document Type: ') . $e->getMessage());
        }
    }

    //======================
    /**
     * Update the specified Document Type.
     */
    public function updateDocumentType(UpdateHrSettingRequest $request, int $id): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.settings.document-types.edit'), 403);

        try {
            $setting = $this->service->getSettingById($id);

            if (!$setting || $setting->type !== HrSettingType::DOCUMENT_TYPES) {
                return redirect()->back()->with('error', __('Document Type not found.'));
            }

            $data = $request->validated();
            $data['type'] = HrSettingType::DOCUMENT_TYPES->value;

            $this->service->updateSetting($setting, $data);

            return redirect()->route('hr.settings.document-types.index')
                ->with('success', __('Document Type updated successfully.'));
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('Failed to update Document Type: ') . $e->getMessage());
        }
    }

    //======================
    /**
     * Remove the specified Document Type.
     */
    public function destroyDocumentType(int $id): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.settings.document-types.delete'), 403);

        try {
            $setting = $this->service->getSettingById($id);

            if (!$setting || $setting->type !== HrSettingType::DOCUMENT_TYPES) {
                return redirect()->back()->with('error', __('Document Type not found.'));
            }

            $this->service->deleteSetting($setting);

            return redirect()->route('hr.settings.document-types.index')
                ->with('success', __('Document Type deleted successfully.'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', __('Failed to delete Document Type: ') . $e->getMessage());
        }
    }

    // =========================================================================
    // 3. Reward & Incentive Types (أنواع المكافآت والحوافز)
    // =========================================================================

    //======================
    /**
     * Display listing view for Reward Types.
     */
    public function rewardTypes(): View
    {
        abort_if(! auth()->user()->can('hr.settings.reward-types.view'), 403);

        $parents  = $this->service->getParentsList(HrSettingType::REWARD_TYPES->value);
        $statuses = HrSettingStatus::cases();

        return view('hr::settings.reward_types.index', compact('parents', 'statuses'));
    }

    //======================
    /**
     * Server-side DataTables endpoint for Reward Types.
     */
    public function rewardTypesData(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.settings.reward-types.view'), 403);

        $filters = array_merge($request->all(), ['type' => HrSettingType::REWARD_TYPES->value]);
        $query   = $this->service->getDataTableQuery($filters);

        return $this->buildDataTable($query);
    }

    //======================
    /**
     * Store a newly created Reward Type.
     */
    public function storeRewardType(StoreHrSettingRequest $request): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.settings.reward-types.create'), 403);

        try {
            $data = $request->validated();
            $data['type'] = HrSettingType::REWARD_TYPES->value;

            $this->service->createSetting($data, auth()->id());

            return redirect()->route('hr.settings.reward-types.index')
                ->with('success', __('Reward Type created successfully.'));
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('Failed to create Reward Type: ') . $e->getMessage());
        }
    }

    //======================
    /**
     * Update the specified Reward Type.
     */
    public function updateRewardType(UpdateHrSettingRequest $request, int $id): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.settings.reward-types.edit'), 403);

        try {
            $setting = $this->service->getSettingById($id);

            if (!$setting || $setting->type !== HrSettingType::REWARD_TYPES) {
                return redirect()->back()->with('error', __('Reward Type not found.'));
            }

            $data = $request->validated();
            $data['type'] = HrSettingType::REWARD_TYPES->value;

            $this->service->updateSetting($setting, $data);

            return redirect()->route('hr.settings.reward-types.index')
                ->with('success', __('Reward Type updated successfully.'));
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('Failed to update Reward Type: ') . $e->getMessage());
        }
    }

    //======================
    /**
     * Remove the specified Reward Type.
     */
    public function destroyRewardType(int $id): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.settings.reward-types.delete'), 403);

        try {
            $setting = $this->service->getSettingById($id);

            if (!$setting || $setting->type !== HrSettingType::REWARD_TYPES) {
                return redirect()->back()->with('error', __('Reward Type not found.'));
            }

            $this->service->deleteSetting($setting);

            return redirect()->route('hr.settings.reward-types.index')
                ->with('success', __('Reward Type deleted successfully.'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', __('Failed to delete Reward Type: ') . $e->getMessage());
        }
    }

    // =========================================================================
    // 5. Penalty Types (أنواع الجزاءات والمخالفات)
    // =========================================================================

    //======================
    /**
     * Display listing view for Penalty Types.
     */
    public function penaltyTypes(): View
    {
        abort_if(! auth()->user()->can('hr.settings.penalty-types.view'), 403);

        $parents  = $this->service->getParentsList(HrSettingType::PENALTY_TYPES->value);
        $statuses = HrSettingStatus::cases();

        return view('hr::settings.penalty_types.index', compact('parents', 'statuses'));
    }

    //======================
    /**
     * Server-side DataTables endpoint for Penalty Types.
     */
    public function penaltyTypesData(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.settings.penalty-types.view'), 403);

        $filters = array_merge($request->all(), ['type' => HrSettingType::PENALTY_TYPES->value]);
        $query   = $this->service->getDataTableQuery($filters);

        return $this->buildDataTable($query);
    }

    //======================
    /**
     * Store a newly created Penalty Type.
     */
    public function storePenaltyType(StoreHrSettingRequest $request): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.settings.penalty-types.create'), 403);

        try {
            $data = $request->validated();
            $data['type'] = HrSettingType::PENALTY_TYPES->value;

            $this->service->createSetting($data, auth()->id());

            return redirect()->route('hr.settings.penalty-types.index')
                ->with('success', __('Penalty Type created successfully.'));
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('Failed to create Penalty Type: ') . $e->getMessage());
        }
    }

    //======================
    /**
     * Update the specified Penalty Type.
     */
    public function updatePenaltyType(UpdateHrSettingRequest $request, int $id): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.settings.penalty-types.edit'), 403);

        try {
            $setting = $this->service->getSettingById($id);

            if (!$setting || $setting->type !== HrSettingType::PENALTY_TYPES) {
                return redirect()->back()->with('error', __('Penalty Type not found.'));
            }

            $data = $request->validated();
            $data['type'] = HrSettingType::PENALTY_TYPES->value;

            $this->service->updateSetting($setting, $data);

            return redirect()->route('hr.settings.penalty-types.index')
                ->with('success', __('Penalty Type updated successfully.'));
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('Failed to update Penalty Type: ') . $e->getMessage());
        }
    }

    //======================
    /**
     * Remove the specified Penalty Type.
     */
    public function destroyPenaltyType(int $id): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.settings.penalty-types.delete'), 403);

        try {
            $setting = $this->service->getSettingById($id);

            if (!$setting || $setting->type !== HrSettingType::PENALTY_TYPES) {
                return redirect()->back()->with('error', __('Penalty Type not found.'));
            }

            $this->service->deleteSetting($setting);

            return redirect()->route('hr.settings.penalty-types.index')
                ->with('success', __('Penalty Type deleted successfully.'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', __('Failed to delete Penalty Type: ') . $e->getMessage());
        }
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    //======================
    /**
     * Build DataTable response for HR Setting queries.
     */
    protected function buildDataTable($query): JsonResponse
    {
        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('name_ar', fn(HrSetting $item) => $item->translate('ar')?->name ?? '-')
            ->addColumn('name_en', fn(HrSetting $item) => $item->translate('en')?->name ?? '-')
            ->addColumn('description_ar', fn(HrSetting $item) => $item->translate('ar')?->description ?? '')
            ->addColumn('description_en', fn(HrSetting $item) => $item->translate('en')?->description ?? '')
            ->addColumn('parent_name', fn(HrSetting $item) => $item->parent?->name ?? '<span class="text-muted">' . __('None (Root)') . '</span>')
            ->addColumn('parent_id', fn(HrSetting $item) => $item->parent_id)
            ->addColumn('status', function (HrSetting $item) {
                $statusEnum = $item->status;
                if ($statusEnum instanceof HrSettingStatus) {
                    return '<span class="badge ' . $statusEnum->badgeColor() . '">' . $statusEnum->label() . '</span>';
                }

                return '<span class="badge bg-label-secondary">' . ($item->status ?? '-') . '</span>';
            })
            ->addColumn('status_raw', fn(HrSetting $item) => $item->status instanceof HrSettingStatus ? $item->status->value : $item->status)
            ->editColumn('created_at', fn(HrSetting $item) => $item->created_at ? $item->created_at->format('Y-m-d') : '-')
            ->rawColumns(['parent_name', 'status'])
            ->filterColumn('name_ar', function ($query, $keyword) {
                $query->whereTranslationLike('name', "%{$keyword}%", 'ar');
            })
            ->filterColumn('name_en', function ($query, $keyword) {
                $query->whereTranslationLike('name', "%{$keyword}%", 'en');
            })
            ->filter(function ($query) {
                if ($search = request('search.value')) {
                    $query->where(function ($q) use ($search) {
                        $q->whereTranslationLike('name', "%{$search}%")
                          ->orWhereTranslationLike('description', "%{$search}%");
                    });
                }
            })
            ->make(true);
    }
}
