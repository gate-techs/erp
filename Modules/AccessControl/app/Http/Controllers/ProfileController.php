<?php

namespace Modules\AccessControl\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\HR\Models\Employee;
use Modules\HR\Services\EmployeeService;
use Modules\Settings\Models\PrintTemplate;
use Modules\Settings\Models\Setting;

class ProfileController extends Controller
{
    public function __construct(
        private readonly EmployeeService $employeeService,
    ) {}

    /**
     * Display the authenticated user's profile.
     * If the user is linked to an employee, displays the comprehensive employee profile.
     * Otherwise, displays the dedicated user profile.
     */
    public function show(): View|RedirectResponse
    {
        $user = Auth::user();

        $printTemplates = PrintTemplate::where('is_active', true)->get();

        // Check if user is linked to an employee (by user_id or email)
        $employee = $user->employee ?? Employee::where('email', $user->email)->first();

        if ($employee) {
            $profileData = $this->employeeService->getProfileData($employee->id);
            if ($profileData) {
                return view('hr::employees.show', array_merge($profileData, [
                    'isOwnProfile'   => true,
                    'printTemplates' => $printTemplates,
                ]));
            }
        }

        $user->load(['roles.permissions']);

        return view('accesscontrol::profile.show', compact('user', 'printTemplates'));
    }

    /**
     * Render the official printable profile using active print templates.
     */
    public function print(Request $request): View
    {
        $user = Auth::user();
        $employee = $user->employee ?? Employee::where('email', $user->email)->first();

        $templateId = $request->filled('template_id') ? (int) $request->template_id : null;
        $availableTemplates = PrintTemplate::where('is_active', true)->get();
        $template = null;

        if ($templateId) {
            $template = $availableTemplates->where('id', $templateId)->first();
        }

        $template = $template
            ?? $availableTemplates->where('code', 'general_report')->first()
            ?? $availableTemplates->where('is_default', true)->first()
            ?? $availableTemplates->first();

        $globalSetting = Setting::first();

        if ($employee) {
            $profileData = $this->employeeService->getProfileData($employee->id);
            return view('hr::employees.print_profile', array_merge($profileData, [
                'template'           => $template,
                'availableTemplates' => $availableTemplates,
                'globalSetting'      => $globalSetting,
                'isOwnProfile'       => true,
            ]));
        }

        return view('accesscontrol::profile.print', [
            'user'               => $user,
            'template'           => $template,
            'availableTemplates' => $availableTemplates,
            'globalSetting'      => $globalSetting,
        ]);
    }

    /**
     * Update profile details (Name, Username, Phone, Avatar).
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:100', Rule::unique('users', 'username')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        return redirect()->route('profile.show')->with('success', __('Profile updated successfully.'));
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.show')->with('success', __('Password changed successfully.'));
    }
}
