<?php

namespace Modules\AccessControl\Http\Controllers;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\User;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\AccessControl\Services\UserService;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function index(): View
    {
        $stats = $this->userService->getUserStats();
        $roles = $this->userService->getRoles();
        $userTypes = UserType::cases();

        return view('accesscontrol::users.index', compact('stats', 'roles', 'userTypes'));
    }

    public function data(Request $request): JsonResponse
    {
        $filters = [
            'user_type' => $request->input('user_type'),
            'status' => $request->input('status'),
            'role' => $request->input('role'),
        ];

        return DataTables::eloquent($this->userService->getUsersQuery($filters))
            ->addIndexColumn()
            ->addColumn('user_info', function (User $user) {
                $avatarUrl = null;
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    $avatarUrl = asset('storage/' . $user->avatar);
                }

                $initials = mb_substr($user->name ?: 'U', 0, 2);
                $stateColors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info'];
                $color = $stateColors[$user->id % count($stateColors)];

                $avatarHtml = $avatarUrl
                    ? '<img src="' . e($avatarUrl) . '" alt="Avatar" class="rounded-circle w-100 h-100" style="object-fit: cover;">'
                    : '<span class="avatar-initial rounded-circle bg-label-' . $color . ' fw-semibold">' . e($initials) . '</span>';

                return '
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-md flex-shrink-0">
                        ' . $avatarHtml . '
                    </div>
                    <div class="d-flex flex-column">
                        <span class="fw-semibold text-heading">' . e($user->name) . '</span>
                        <small class="text-muted">' . e($user->email) . '</small>
                    </div>
                </div>';
            })
            ->addColumn('username_formatted', function (User $user) {
                return $user->username
                    ? '<span class="badge bg-label-dark font-12"><i class="bx bx-at me-1"></i>' . e($user->username) . '</span>'
                    : '<span class="text-muted">-</span>';
            })
            ->addColumn('phone_formatted', function (User $user) {
                return $user->phone
                    ? '<span class="text-body d-flex align-items-center gap-1 font-13"><i class="bx bx-phone font-14 text-muted"></i>' . e($user->phone) . '</span>'
                    : '<span class="text-muted">-</span>';
            })
            ->addColumn('user_type_badge', function (User $user) {
                $type = $user->user_type;
                $val = $type instanceof UserType ? $type->value : (string)$type;
                $label = $type instanceof UserType ? $type->label() : __($val);

                $badgeClass = match ($val) {
                    'admin' => 'bg-label-danger',
                    'employee' => 'bg-label-primary',
                    'guardian' => 'bg-label-info',
                    'student' => 'bg-label-success',
                    default => 'bg-label-secondary',
                };

                return '<span class="badge ' . $badgeClass . '">' . e($label) . '</span>';
            })
            ->addColumn('roles_badges', function (User $user) {
                if ($user->roles->isEmpty()) {
                    return '<span class="badge bg-label-secondary">' . __('None') . '</span>';
                }

                $badges = [];
                foreach ($user->roles as $role) {
                    $translated = $role->display_name ?? __($role->name);
                    $badges[] = '<span class="badge bg-label-primary me-1">' . e($translated) . '</span>';
                }

                return implode(' ', $badges);
            })
            ->addColumn('status', function (User $user) {
                $statusText = $user->is_active ? __('Active') : __('Inactive');
                $badgeClass = $user->is_active ? 'bg-label-success' : 'bg-label-danger';
                $disabled = $user->id === auth()->id() ? 'disabled' : '';

                return '
                    <button type="button" class="btn btn-sm btn-toggle-status ' . $badgeClass . ' border-0" data-id="' . $user->id . '" ' . $disabled . ' title="' . __('Click to toggle') . '">
                        ' . e($statusText) . '
                    </button>';
            })
            ->editColumn('created_at', function (User $user) {
                return $user->created_at ? $user->created_at->format('Y-m-d') : '-';
            })
            ->addColumn('actions', function (User $user) {
                $isCurrent = $user->id === auth()->id();
                $avatarUrl = $user->avatar && Storage::disk('public')->exists($user->avatar) ? asset('storage/' . $user->avatar) : '';
                $roleName = $user->roles->first()?->name ?? '';
                $typeVal = $user->user_type instanceof UserType ? $user->user_type->value : (string)$user->user_type;

                $deleteBtn = $isCurrent
                    ? '<button type="button" class="btn btn-icon btn-sm btn-outline-secondary" disabled title="' . __('Cannot delete your own account') . '"><i class="icon-base bx bx-trash"></i></button>'
                    : '<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-user" data-id="' . $user->id . '" data-name="' . e($user->name) . '" title="' . __('Delete') . '"><i class="icon-base bx bx-trash"></i></button>';

                return '
                <div class="d-inline-flex gap-1">
                    <button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-user"
                            title="' . __('Edit') . '"
                            data-id="' . $user->id . '"
                            data-name="' . e($user->name) . '"
                            data-username="' . e($user->username ?? '') . '"
                            data-email="' . e($user->email) . '"
                            data-phone="' . e($user->phone ?? '') . '"
                            data-user-type="' . e($typeVal) . '"
                            data-role="' . e($roleName) . '"
                            data-status="' . ($user->is_active ? 'active' : 'inactive') . '"
                            data-avatar="' . e($avatarUrl) . '">
                        <i class="icon-base bx bx-edit-alt"></i>
                    </button>
                    ' . $deleteBtn . '
                </div>';
            })
            ->rawColumns(['DT_RowIndex', 'user_info', 'username_formatted', 'phone_formatted', 'user_type_badge', 'roles_badges', 'status', 'actions'])
            ->toJson();
    }

    public function show(int $id): JsonResponse
    {
        $user = $this->userService->findUser($id);

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
                'user_type' => $user->user_type instanceof UserType ? $user->user_type->value : (string)$user->user_type,
                'role' => $user->roles->first()?->name,
                'is_active' => (bool)$user->is_active,
                'avatar_url' => $user->avatar && Storage::disk('public')->exists($user->avatar) ? asset('storage/' . $user->avatar) : null,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:100', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:6'],
            'user_type' => ['required', Rule::enum(UserType::class)],
            'role' => ['required', 'string', 'exists:roles,name'],
            'is_active' => ['nullable'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $this->userService->createUser($validated, $request->file('avatar'));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('User created successfully.'),
            ]);
        }

        return redirect()->route('users.index')->with('success', __('User created successfully.'));
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:100', Rule::unique('users', 'username')->ignore($id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:6'],
            'user_type' => ['required', Rule::enum(UserType::class)],
            'role' => ['required', 'string', 'exists:roles,name'],
            'is_active' => ['nullable'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $this->userService->updateUser($id, $validated, $request->file('avatar'));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('User updated successfully.'),
            ]);
        }

        return redirect()->route('users.index')->with('success', __('User updated successfully.'));
    }

    public function destroy(Request $request, int $id)
    {
        try {
            $this->userService->deleteUser($id);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('User deleted successfully.'),
                ]);
            }

            return redirect()->route('users.index')->with('success', __('User deleted successfully.'));
        } catch (DomainException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 403);
            }

            return redirect()->route('users.index')->with('error', $e->getMessage());
        }
    }

    public function toggleStatus(Request $request, int $id): JsonResponse
    {
        try {
            $isActive = $this->userService->toggleUserStatus($id);

            return response()->json([
                'success' => true,
                'is_active' => $isActive,
                'status' => $isActive ? 'active' : 'inactive',
                'badge' => $isActive ? 'bg-label-success' : 'bg-label-danger',
                'message' => $isActive
                    ? __('User activated successfully.')
                    : __('User deactivated successfully.'),
            ]);
        } catch (DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        }
    }
}
