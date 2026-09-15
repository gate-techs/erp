<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Settings\Services\TranslationService;

class TranslationController extends Controller
{
    public function __construct(
        protected TranslationService $translationService
    ) {}

    /**
     * Display the translation manager dashboard.
     */
    public function index(Request $request): View
    {
        $modules = $this->translationService->getModules();
        $activeModule = $request->query('module', 'Settings');

        // Verify active module exists, fallback to Settings or first module
        $moduleIds = array_column($modules, 'id');
        if (!in_array($activeModule, $moduleIds, true)) {
            $activeModule = in_array('Settings', $moduleIds, true) ? 'Settings' : ($moduleIds[0] ?? 'core');
        }

        $translations = $this->translationService->getTranslations($activeModule);

        return view('settings::translations.index', compact('modules', 'activeModule', 'translations'));
    }

    /**
     * Update multiple translations at once.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'module' => 'required|string',
            'translations' => 'array',
        ]);

        $module = $request->input('module', 'core');
        $translations = $request->input('translations', []);

        $this->translationService->updateTranslations($module, $translations);

        return redirect()
            ->route('settings.translations.index', ['module' => $module])
            ->with('success', __('تم حفظ وتحديث الترجمات بنجاح.'));
    }

    /**
     * Add a single new translation key.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'module' => 'required|string',
            'key' => 'required|string|max:255',
            'ar' => 'nullable|string',
            'en' => 'nullable|string',
        ]);

        $module = $request->input('module');
        $key = $request->input('key');
        $ar = $request->input('ar');
        $en = $request->input('en');

        $this->translationService->addTranslation($module, $key, $ar, $en);

        return redirect()
            ->route('settings.translations.index', ['module' => $module])
            ->with('success', __('تمت إضافة مفتاح الترجمة الجديد بنجاح.'));
    }

    /**
     * Delete a translation key.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'module' => 'required|string',
            'key' => 'required|string',
        ]);

        $module = $request->input('module');
        $key = $request->input('key');

        $this->translationService->deleteTranslation($module, $key);

        return redirect()
            ->route('settings.translations.index', ['module' => $module])
            ->with('success', __('تم حذف مفتاح الترجمة بنجاح.'));
    }
}
