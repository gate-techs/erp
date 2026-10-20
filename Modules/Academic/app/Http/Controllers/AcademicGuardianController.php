<?php

namespace Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Academic\Http\Requests\StoreAcademicGuardianRequest;
use Modules\Academic\Http\Requests\UpdateAcademicGuardianRequest;
use Modules\Academic\Models\AcademicGuardian;
use Modules\Academic\Services\AcademicGuardianService;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;
use Yajra\DataTables\Facades\DataTables;

class AcademicGuardianController extends Controller
{
    public function __construct(
        protected AcademicGuardianService $service
    ) {}

    //------------------------------------------
    public function index()
    {
        abort_if(! auth()->user()->can('academic.guardians.view'), 403);

        $institutions = Nursery::with('translations')->where('status', 'active')->get();
        $branches = NurseryBranch::with('translations')->where('status', 'active')->get();
        $statuses = AcademicGuardian::statuses();
        $employees = $this->service->getAvailableEmployees();

        return view('academic::guardians.index', compact(
            'institutions',
            'branches',
            'statuses',
            'employees'
        ));
    }

    //------------------------------------------
    public function data(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.guardians.view'), 403);

        $filters = $request->only([
            'institution_id',
            'branch_id',
            'status',
            'search',
        ]);

        $query = $this->service->getFilteredQuery($filters);

        $canEdit = auth()->user()->can('academic.guardians.edit');
        $canDelete = auth()->user()->can('academic.guardians.delete');
        $canToggle = auth()->user()->can('academic.guardians.toggle-status');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('name', function (AcademicGuardian $guardian) use ($canEdit) {
                $locale = app()->getLocale();
                $name = $guardian->translate($locale)?->name ?? $guardian->translate('ar')?->name ?? $guardian->name ?? '-';
                $nameSafe = e($name);

                $html = $canEdit 
                    ? '<button type="button" class="btn btn-link p-0 text-start fw-semibold text-heading text-decoration-none guardian-name-text edit-record-btn" data-id="' . $guardian->id . '" title="' . e(__('Edit')) . '">' . $nameSafe . '</button>'
                    : '<span class="fw-semibold text-heading guardian-name-text">' . $nameSafe . '</span>';

                if ($guardian->user && $guardian->user->employee) {
                    $html .= ' <span class="badge bg-label-warning font-11 ms-1"><i class="icon-base bx bx-briefcase font-12 me-1"></i>' . e(__('Staff')) . '</span>';
                }

                return $html;
            })
            ->addColumn('email', function (AcademicGuardian $guardian) {
                $email = $guardian->email ?? ($guardian->user?->email ?? '');
                return $email ? '<span class="text-body">' . e($email) . '</span>' : '<span class="text-muted">-</span>';
            })
            ->addColumn('phone_formatted', function (AcademicGuardian $guardian) {
                return $guardian->phone ? '<span dir="ltr" class="badge bg-label-info">' . e($guardian->full_phone) . '</span>' : '<span class="text-muted">-</span>';
            })
            ->addColumn('institution_name', function (AcademicGuardian $guardian) {
                $locale = app()->getLocale();
                return e($guardian->institution?->translate($locale)?->name ?? $guardian->institution?->translate('ar')?->name ?? '-');
            })
            ->addColumn('branch_name', function (AcademicGuardian $guardian) {
                if (! $guardian->branch) {
                    return '<span class="text-muted">' . e(__('All Branches')) . '</span>';
                }
                $locale = app()->getLocale();
                return e($guardian->branch->translate($locale)?->name ?? $guardian->branch->translate('ar')?->name ?? '-');
            })
            ->addColumn('students_count', function (AcademicGuardian $guardian) {
                $url = route('academic.students.index', ['guardian_id' => $guardian->id]);
                return '<a href="' . e($url) . '" class="badge bg-label-primary text-decoration-none" title="' . e(__('View Students')) . '">' .
                    '<i class="icon-base bx bx-group me-1"></i>' . (int) $guardian->students_count .
                    '</a>';
            })
            ->addColumn('status_badge', function (AcademicGuardian $guardian) use ($canToggle) {
                $isActive = $guardian->status === AcademicGuardian::STATUS_ACTIVE;
                $label = $isActive ? __('Active') : __('Inactive');
                $class = $isActive ? 'bg-label-success' : 'bg-label-danger';
                $cursor = $canToggle ? 'cursor-pointer toggle-status-btn' : '';

                return '<span class="badge ' . $class . ' ' . $cursor . '" data-id="' . $guardian->id . '">' . e($label) . '</span>';
            })
            ->addColumn('actions', function (AcademicGuardian $guardian) use ($canEdit, $canDelete) {
                $id = $guardian->id;
                $name = e($guardian->translate(app()->getLocale())?->name ?? $guardian->translate('ar')?->name ?? $guardian->name ?? '');

                $buttons = '';
                if ($canEdit) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-primary edit-record-btn" data-id="' . $id . '" title="' . e(__('Edit')) . '"><i class="icon-base bx bx-edit"></i></button>';
                }
                if ($canDelete) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-danger delete-record-btn" data-id="' . $id . '" data-name="' . $name . '" title="' . e(__('Delete')) . '"><i class="icon-base bx bx-trash"></i></button>';
                }

                return $buttons ? '<div class="d-inline-flex align-items-center gap-1">' . $buttons . '</div>' : '<span class="text-muted">-</span>';
            })
            ->rawColumns(['name', 'email', 'phone_formatted', 'institution_name', 'branch_name', 'students_count', 'status_badge', 'actions'])
            ->toJson();
    }

    //------------------------------------------
    public function store(StoreAcademicGuardianRequest $request): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.guardians.create'), 403);

        $guardian = $this->service->create($request->validated(), $request->file('photo'));

        return response()->json([
            'success' => true,
            'message' => __('Guardian created successfully.'),
            'data'    => $guardian,
        ]);
    }

    //------------------------------------------
    public function show(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.guardians.view'), 403);

        $guardian = $this->service->find($id);

        if (! $guardian) {
            return response()->json([
                'success' => false,
                'message' => __('Guardian not found.'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $guardian,
        ]);
    }

    //------------------------------------------
    public function edit(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.guardians.edit'), 403);

        $guardian = $this->service->find($id);

        if (! $guardian) {
            return response()->json([
                'success' => false,
                'message' => __('Guardian not found.'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'               => $guardian->id,
                'guardian_code'    => $guardian->guardian_code,
                'civil_id'         => $guardian->civil_id ?? '',
                'user_id'          => $guardian->user_id,
                'institution_id'   => $guardian->institution_id,
                'branch_id'        => $guardian->branch_id,
                'phone_code'       => $guardian->phone_code,
                'phone'            => $guardian->phone,
                'email'            => $guardian->email ?? ($guardian->user?->email ?? ''),
                'photo_url'        => $guardian->photo_url,
                'status'           => $guardian->status,
                'name_ar'          => $guardian->translate('ar')?->name ?? '',
                'name_en'          => $guardian->translate('en')?->name ?? '',
                'is_employee'      => (bool) ($guardian->user && $guardian->user->employee),
            ],
        ]);
    }

    //------------------------------------------
    public function update(UpdateAcademicGuardianRequest $request, int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.guardians.edit'), 403);

        $guardian = $this->service->update($id, $request->validated(), $request->file('photo'));

        if (! $guardian) {
            return response()->json([
                'success' => false,
                'message' => __('Guardian not found.'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => __('Guardian updated successfully.'),
            'data'    => $guardian,
        ]);
    }

    //------------------------------------------
    public function destroy(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.guardians.delete'), 403);

        $deleted = $this->service->delete($id);

        if (! $deleted) {
            return response()->json([
                'success' => false,
                'message' => __('Guardian not found or cannot be deleted.'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => __('Guardian deleted successfully.'),
        ]);
    }

    //------------------------------------------
    public function toggleStatus(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.guardians.toggle-status'), 403);

        $guardian = $this->service->toggleStatus($id);

        if (! $guardian) {
            return response()->json([
                'success' => false,
                'message' => __('Guardian not found.'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => __('Status changed successfully.'),
            'status'  => $guardian->status,
        ]);
    }

    //------------------------------------------
    public function getAvailableEmployees(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.guardians.view') && ! auth()->user()->can('academic.guardians.create') && ! auth()->user()->can('academic.guardians.edit'), 403);

        $branchId = $request->input('branch_id');
        $employees = $this->service->getAvailableEmployees($branchId ? (int)$branchId : null);

        $formatted = $employees->map(function ($emp) {
            $name = $emp->translate(app()->getLocale())?->name ?? $emp->translate('ar')?->name ?? $emp->employee_code;
            return [
                'id'       => $emp->user_id,
                'name'     => $name . ' (' . ($emp->employee_code ?? '') . ')',
                'civil_id' => $emp->national_id ?? '',
                'phone'    => $emp->phone,
                'email'    => $emp->email,
                'photo'    => $emp->avatar_url,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $formatted,
        ]);
    }

    //------------------------------------------
    public function getBranches(int $institutionId): JsonResponse
    {
        $branches = NurseryBranch::with('translations')
            ->where('nursery_id', $institutionId)
            ->where('status', 'active')
            ->get()
            ->map(function ($branch) {
                return [
                    'id'   => $branch->id,
                    'name' => $branch->translate(app()->getLocale())?->name ?? $branch->translate('ar')?->name ?? '-',
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $branches,
        ]);
    }
}
