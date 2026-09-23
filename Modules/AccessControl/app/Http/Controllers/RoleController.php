<?php

namespace Modules\AccessControl\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\AccessControl\Models\Role;
use Modules\AccessControl\Services\AccessControlService;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function __construct(
        private readonly AccessControlService $accessControlService,
    ) {}

    public function index(): View
    {
        return view('accesscontrol::roles.index');
    }

    public function data(Request $request): JsonResponse
    {
        return DataTables::eloquent($this->accessControlService->getRolesQuery([
            'guard_name' => 'web',
        ]))
            ->addIndexColumn()
            ->addColumn('translated_name', fn (Role $role) => $role->display_name)
            ->addColumn('is_system', fn (Role $role) => $role->isSystem())
            ->editColumn('created_at', fn (Role $role) => $role->created_at ? $role->created_at->format('Y-m-d') : '-')
            ->toJson();
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $role = $this->accessControlService->createRole($data);

        if ($request->has('permissions')) {
            $this->accessControlService->syncRolePermissions($role, (array) $request->input('permissions', []));
        }

        return redirect()->route('settings.roles.index')->with('success', __('Role created successfully.'));
    }

    public function edit(int $id): View
    {
        $role = Role::with('permissions')->findOrFail($id);
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $permissionMatrix = $this->accessControlService->getPermissionsMatrix();

        return view('accesscontrol::roles.edit', compact('role', 'rolePermissions', 'permissionMatrix'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $role = Role::findOrFail($id);
        $data = $this->validated($request, $id);

        $this->accessControlService->updateRole($role, $data);

        $permissions = (array) $request->input('permissions', []);
        $this->accessControlService->syncRolePermissions($role, $permissions);

        return redirect()->route('settings.roles.index')->with('success', __('Role updated successfully.'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $role = Role::findOrFail($id);
        if ($role->isSystem()) {
            return redirect()->route('settings.roles.index')->with('error', __('System roles cannot be deleted.'));
        }

        $this->accessControlService->deleteRole($id);

        return redirect()->route('settings.roles.index')->with('success', __('Role deleted successfully.'));
    }

    private function validated(Request $request, ?int $roleId = null): array
    {
        $nameRule = 'required|string|max:255|unique:roles,name,' . ($roleId ?? 'NULL') . ',id,guard_name,web';

        return $request->validate([
            'name' => $nameRule,
        ]) + ['guard_name' => 'web'];
    }
}
