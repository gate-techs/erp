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

        return view('settings::translations.index', compact('modules', 'activeModule'));
    }

    /**
     * Serve translation rows to DataTables without rendering the full JSON files in the page.
     */
    public function data(Request $request)
    {
        $module = $request->query('module', 'Settings');
        $moduleIds = array_column($this->translationService->getModules(), 'id');
        if (!in_array($module, $moduleIds, true)) {
            $module = in_array('Settings', $moduleIds, true) ? 'Settings' : ($moduleIds[0] ?? 'core');
        }

        $page = $this->translationService->getTranslationsPage(
            $module,
            (int) $request->input('start', 0),
            (int) $request->input('length', 25),
            (string) $request->input('search.value', ''),
            $request->boolean('missing')
        );

        return response()->json([
            'draw' => (int) $request->input('draw', 0),
            'recordsTotal' => $page['recordsTotal'],
            'recordsFiltered' => $page['recordsFiltered'],
            'data' => $page['data'],
        ]);
    }

    /**
     * Update one row from the server-side DataTable.
     */
    public function updateKey(Request $request)
    {
        $validated = $request->validate([
            'module' => 'required|string',
            'key' => 'required|string',
            'ar' => 'nullable|string',
            'en' => 'nullable|string',
        ]);

        $updated = $this->translationService->updateTranslation(
            $validated['module'],
            $validated['key'],
            $validated['ar'] ?? null,
            $validated['en'] ?? null
        );

        if (!$updated) {
            return response()->json(['message' => __('Translation key not found.')], 404);
        }

        return response()->json(['message' => __('Translation saved successfully.')]);
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
