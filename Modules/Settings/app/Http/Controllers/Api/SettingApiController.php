<?php

namespace Modules\Settings\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Settings\Http\Requests\UpdateSettingRequest;
use Modules\Settings\Services\SettingService;

class SettingApiController extends Controller
{
    public function __construct(
        protected SettingService $settingService
    ) {}

    /**
     * Get system settings for Mobile / Frontend consumers.
     */
    public function index(Request $request): JsonResponse
    {
        $data = $this->settingService->getFormattedSettingsForApi($request->query('locale'));

        return response()->json([
            'success' => true,
            'message' => 'Settings retrieved successfully',
            'data' => $data,
        ]);
    }

    /**
     * Update system settings from API.
     */
    public function update(UpdateSettingRequest $request): JsonResponse
    {
        $setting = $this->settingService->updateSettings(
            $request->validated(),
            $request->file('logo'),
            $request->file('favicon')
        );

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
            'data' => $this->settingService->getFormattedSettingsForApi($request->query('locale')),
        ]);
    }
}
