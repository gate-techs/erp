<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Settings\Http\Requests\UpdateSettingRequest;
use Modules\Settings\Services\SettingService;

class SettingsController extends Controller
{
    public function __construct(
        protected SettingService $settingService
    ) {}

    /**
     * Display the settings edit form.
     */
    public function index(): View
    {
        $settings = $this->settingService->getSettings();

        return view('settings::index', compact('settings'));
    }

    /**
     * Update the system settings.
     */
    public function update(UpdateSettingRequest $request): RedirectResponse
    {
        $this->settingService->updateSettings(
            $request->validated(),
            $request->file('logo'),
            $request->file('favicon')
        );

        return redirect()
            ->route('settings.index')
            ->with('success', __('تم حفظ وتحديث إعدادات النظام بنجاح'));
    }
}
