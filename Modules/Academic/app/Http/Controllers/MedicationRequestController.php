<?php
namespace Modules\Academic\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Academic\Http\Requests\StoreMedicationRequest;
use Modules\Academic\Http\Requests\UpdateMedicationRequest;
use Modules\Academic\Models\MedicationRequest;
use Modules\Academic\Services\MedicationRequestService;
use Yajra\DataTables\Facades\DataTables;
class MedicationRequestController extends Controller
{
    public function __construct(protected MedicationRequestService $service){}
    public function index(): View
    {
        abort_if(! auth()->user()->can('academic.medication-requests.view'), 403);
        return view('academic::medication-requests.index', $this->service->formData());
    }

    public function data(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.medication-requests.view'), 403);
        $canEdit = auth()->user()->can('academic.medication-requests.edit');
        $canDelete = auth()->user()->can('academic.medication-requests.delete');
        $canDone = auth()->user()->can('academic.medication-requests.done');

        return DataTables::eloquent($this->service->query($request->all()))
            ->addIndexColumn()
            ->addColumn('student_name', fn(MedicationRequest $r) => $r->student?->name ?? '-')
            ->addColumn('parent_name', fn(MedicationRequest $r) => $r->parent?->name ?? '-')
            ->addColumn('nurse_name', fn(MedicationRequest $r) => $r->nurse?->name ?? '-')
            ->editColumn('date', function (MedicationRequest $r) use ($canEdit) {
                $date = $r->date ? $r->date->format('Y-m-d') : '-';
                if ($canEdit) {
                    return '<a href="javascript:void(0);" class="fw-semibold text-heading text-hover-primary edit-medication" data-id="' . $r->id . '" title="' . e(__('Edit')) . '">' . e($date) . '</a>';
                }
                return '<span class="fw-semibold text-heading">' . e($date) . '</span>';
            })
            ->addColumn('done_badge', fn(MedicationRequest $r) => '<span class="badge bg-label-' . ($r->done ? 'success' : 'warning') . '">' . e($r->done ? __('Done') : __('Pending')) . '</span>')
            ->addColumn('actions', function (MedicationRequest $r) use ($canEdit, $canDone, $canDelete) {
                $buttons = [];
                if ($canEdit) {
                    $buttons[] = '<button type="button" class="btn btn-sm btn-icon btn-label-primary edit-medication" data-id="' . $r->id . '" title="' . e(__('Edit')) . '"><i class="bx bx-edit"></i></button>';
                }
                if ($canDone) {
                    $buttons[] = '<button type="button" class="btn btn-sm btn-icon btn-label-success complete-medication" data-id="' . $r->id . '" ' . ($r->done ? 'disabled' : '') . ' title="' . e(__('Done')) . '"><i class="bx bx-check"></i></button>';
                }
                if ($canDelete) {
                    $buttons[] = '<button type="button" class="btn btn-sm btn-icon btn-label-danger delete-medication" data-id="' . $r->id . '" title="' . e(__('Delete')) . '"><i class="bx bx-trash"></i></button>';
                }
                if (empty($buttons)) {
                    return '<span class="text-muted">-</span>';
                }
                return '<div class="d-flex justify-content-center gap-1">' . implode('', $buttons) . '</div>';
            })
            ->rawColumns(['date', 'done_badge', 'actions'])
            ->make(true);
    }

    public function store(StoreMedicationRequest $request): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.medication-requests.create'), 403);
        return response()->json([
            'success' => true,
            'message' => __('Medication request created successfully.'),
            'data' => $this->service->create($request->validated(), auth()->id()),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.medication-requests.view'), 403);
        $item = $this->service->find($id);
        abort_unless($item, 404, __('Medication request not found.'));
        return response()->json(['success' => true, 'data' => $item]);
    }

    public function update(UpdateMedicationRequest $request, int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.medication-requests.edit'), 403);
        $item = $this->service->update($id, $request->validated(), auth()->id());
        abort_unless($item, 404);
        return response()->json([
            'success' => true,
            'message' => __('Medication request updated successfully.'),
            'data' => $item,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.medication-requests.delete'), 403);
        abort_unless($this->service->delete($id, auth()->id()), 404);
        return response()->json(['success' => true, 'message' => __('Medication request deleted successfully.')]);
    }

    public function done(Request $request, int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.medication-requests.done'), 403);
        $item = $this->service->markDone($id, $request->validate([
            'nurse_id' => ['nullable', 'exists:users,id'],
            'nurse_note' => ['nullable', 'string'],
        ]), auth()->id());
        abort_unless($item, 404);
        return response()->json(['success' => true, 'message' => __('Medication marked as done successfully.')]);
    }
}
