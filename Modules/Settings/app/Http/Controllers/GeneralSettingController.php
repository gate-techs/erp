<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Settings\Enums\GeneralSettingType;
use Modules\Settings\Models\GeneralSetting;
use Modules\Settings\Services\GeneralSettingService;
use Yajra\DataTables\Facades\DataTables;

class GeneralSettingController extends Controller
{
    public function __construct(
        protected GeneralSettingService $generalSettingService
    ) {}

    /**
     * Entry point for General Settings, redirects to Governments.
     */
    public function index(): RedirectResponse
    {
        return redirect()->route('settings.general.governments.index');
    }

    // =========================================================================
    // 1. Governments (المحافظات)
    // =========================================================================

    public function governments(): View
    {
        return view('settings::general.governments.index');
    }

    public function governmentsData(): JsonResponse
    {
        return $this->getSettingDataQuery(GeneralSettingType::GOVERNMENT);
    }

    public function storeGovernment(Request $request): RedirectResponse
    {
        return $this->handleStore(
            $request,
            GeneralSettingType::GOVERNMENT,
            'settings.general.governments.index',
            __('تمت إضافة المحافظة بنجاح.')
        );
    }

    public function updateGovernment(Request $request, int $id): RedirectResponse
    {
        return $this->handleUpdate(
            $request,
            $id,
            'settings.general.governments.index',
            __('تم تحديث بيانات المحافظة بنجاح.')
        );
    }

    public function destroyGovernment(int $id): RedirectResponse
    {
        return $this->handleDestroy(
            $id,
            'settings.general.governments.index',
            __('تم حذف المحافظة بنجاح.')
        );
    }

    // =========================================================================
    // 2. Nationalities (الجنسيات)
    // =========================================================================

    public function nationalities(): View
    {
        return view('settings::general.nationalities.index');
    }

    public function nationalitiesData(): JsonResponse
    {
        return $this->getSettingDataQuery(GeneralSettingType::NATIONALITY);
    }

    public function storeNationality(Request $request): RedirectResponse
    {
        return $this->handleStore(
            $request,
            GeneralSettingType::NATIONALITY,
            'settings.general.nationalities.index',
            __('تمت إضافة الجنسية بنجاح.')
        );
    }

    public function updateNationality(Request $request, int $id): RedirectResponse
    {
        return $this->handleUpdate(
            $request,
            $id,
            'settings.general.nationalities.index',
            __('تم تحديث بيانات الجنسية بنجاح.')
        );
    }

    public function destroyNationality(int $id): RedirectResponse
    {
        return $this->handleDestroy(
            $id,
            'settings.general.nationalities.index',
            __('تم حذف الجنسية بنجاح.')
        );
    }

    // =========================================================================
    // 3. Religions (الديانات)
    // =========================================================================

    public function religions(): View
    {
        return view('settings::general.religions.index');
    }

    public function religionsData(): JsonResponse
    {
        return $this->getSettingDataQuery(GeneralSettingType::RELIGION);
    }

    public function storeReligion(Request $request): RedirectResponse
    {
        return $this->handleStore(
            $request,
            GeneralSettingType::RELIGION,
            'settings.general.religions.index',
            __('تمت إضافة الديانة بنجاح.')
        );
    }

    public function updateReligion(Request $request, int $id): RedirectResponse
    {
        return $this->handleUpdate(
            $request,
            $id,
            'settings.general.religions.index',
            __('تم تحديث بيانات الديانة بنجاح.')
        );
    }

    public function destroyReligion(int $id): RedirectResponse
    {
        return $this->handleDestroy(
            $id,
            'settings.general.religions.index',
            __('تم حذف الديانة بنجاح.')
        );
    }

    // =========================================================================
    // 4. Marital Statuses (الحالة الاجتماعية)
    // =========================================================================

    public function maritalStatuses(): View
    {
        return view('settings::general.marital_statuses.index');
    }

    public function maritalStatusesData(): JsonResponse
    {
        return $this->getSettingDataQuery(GeneralSettingType::MARITAL_STATUS);
    }

    public function storeMaritalStatus(Request $request): RedirectResponse
    {
        return $this->handleStore(
            $request,
            GeneralSettingType::MARITAL_STATUS,
            'settings.general.marital-statuses.index',
            __('تمت إضافة الحالة الاجتماعية بنجاح.')
        );
    }

    public function updateMaritalStatus(Request $request, int $id): RedirectResponse
    {
        return $this->handleUpdate(
            $request,
            $id,
            'settings.general.marital-statuses.index',
            __('تم تحديث بيانات الحالة الاجتماعية بنجاح.')
        );
    }

    public function destroyMaritalStatus(int $id): RedirectResponse
    {
        return $this->handleDestroy(
            $id,
            'settings.general.marital-statuses.index',
            __('تم حذف الحالة الاجتماعية بنجاح.')
        );
    }

    // =========================================================================
    // Common Helper Methods
    // =========================================================================

    protected function getSettingDataQuery(GeneralSettingType $type): JsonResponse
    {
        $query = GeneralSetting::with('translations')
            ->ofType($type);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('image_url', fn(GeneralSetting $item) => $item->image_url)
            ->addColumn('name_ar', fn(GeneralSetting $item) => $item->translate('ar')?->name ?? '-')
            ->addColumn('name_en', fn(GeneralSetting $item) => $item->translate('en')?->name ?? '-')
            ->editColumn('value', fn(GeneralSetting $item) => $item->value ?? '-')
            ->editColumn('created_at', fn(GeneralSetting $item) => $item->created_at ? $item->created_at->format('Y-m-d') : '-')
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
                          ->orWhere('value', 'like', "%{$search}%")
                          ->orWhere('id', 'like', "%{$search}%");
                    });
                }
            })
            ->make(true);
    }

    protected function handleStore(Request $request, GeneralSettingType $type, string $redirectRoute, string $successMessage): RedirectResponse
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'value' => 'nullable|string|max:50',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'type' => $type,
            'value' => $request->input('value'),
            'ar' => [
                'name' => $request->input('name_ar'),
            ],
            'en' => [
                'name' => $request->input('name_en'),
            ],
        ];

        $this->generalSettingService->createSetting($data, auth()->id(), $request->file('image'));

        return redirect()->route($redirectRoute)->with('success', $successMessage);
    }

    protected function handleUpdate(Request $request, int $id, string $redirectRoute, string $successMessage): RedirectResponse
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'value' => 'nullable|string|max:50',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'value' => $request->input('value'),
            'ar' => [
                'name' => $request->input('name_ar'),
            ],
            'en' => [
                'name' => $request->input('name_en'),
            ],
        ];

        $this->generalSettingService->updateSetting($id, $data, $request->file('image'));

        return redirect()->route($redirectRoute)->with('success', $successMessage);
    }

    protected function handleDestroy(int $id, string $redirectRoute, string $successMessage): RedirectResponse
    {
        $this->generalSettingService->deleteSetting($id);

        return redirect()->route($redirectRoute)->with('success', $successMessage);
    }
}
