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
        abort_unless($user && $user->can('profile.view'), 403, __('You do not have permission to view profile.'));

        // Check if user is linked to an employee (by user_id or email)
        $employee = $user->employee ?? Employee::where('email', $user->email)->first();

        if ($employee) {
            $profileData = $this->employeeService->getProfileData($employee->id);
            if ($profileData) {
                return view('hr::employees.show', array_merge($profileData, [
                    'isOwnProfile' => true,
                ]));
            }
        }

        $user->load(['roles.permissions']);

        return view('accesscontrol::profile.show', compact('user'));
    }

    /**
     * Update profile details (Name, Username, Phone, Avatar).
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user && $user->can('profile.edit'), 403, __('You do not have permission to edit profile.'));

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
        abort_unless($user && $user->can('profile.edit'), 403, __('You do not have permission to edit profile.'));

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
