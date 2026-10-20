<?php

namespace Modules\Nursery\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Nursery\Http\Requests\StoreNurseryRequest;
use Modules\Nursery\Http\Requests\UpdateNurseryRequest;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Services\NurseryService;
use Yajra\DataTables\Facades\DataTables;

class NurseryController extends Controller
{
    //======================
    public function __construct(
        protected NurseryService $nurseryService
    ) {}

    //======================
    /**
     * Display listing view with lookup data for the drawer form.
     */
    public function index(Request $request): View
    {
        abort_if(!auth()->user()->can('nursery.view'), 403);

        $formData = $this->nurseryService->getFormData();

        return view('nursery::index', [
            'employees' => $formData['employees'],
            'governments' => $formData['governments'],
        ]);
    }

    //======================
    /**
     * Get Server-Side DataTables JSON response for nurseries.
     */
    public function data(Request $request): JsonResponse
    {
        abort_if(!auth()->user()->can('nursery.view'), 403);

        $query = Nursery::with(['translations', 'government.translations', 'employee', 'workDays'])
            ->latest('id');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('image_url', fn(Nursery $item) => $item->image_url)
            ->addColumn('name_ar', fn(Nursery $item) => $item->translateOrNew('ar')->name ?? '-')
            ->addColumn('name_en', fn(Nursery $item) => $item->translateOrNew('en')->name ?? '-')
            ->addColumn('description_ar', fn(Nursery $item) => $item->translateOrNew('ar')->description ?? '')
            ->addColumn('description_en', fn(Nursery $item) => $item->translateOrNew('en')->description ?? '')
            ->addColumn('government_name', fn(Nursery $item) => $item->government?->name ?? '-')
            ->addColumn('employee_name', fn(Nursery $item) => $item->employee?->name ?? '-')
            ->addColumn('status_label', fn(Nursery $item) => $item->status->label())
            ->addColumn('status_badge', fn(Nursery $item) => $item->status->badgeColor())
            ->editColumn('created_at', fn(Nursery $item) => $item->created_at ? $item->created_at->format('d-m-Y') : '-')
            ->filterColumn('name_ar', function ($query, $keyword) {
                $query->whereTranslationLike('name', "%{$keyword}%", 'ar');
            })
            ->filterColumn('name_en', function ($query, $keyword) {
                $query->whereTranslationLike('name', "%{$keyword}%", 'en');
            })
            ->filter(function ($query) {
                if ($search = request('search.value')) {
                    $query->where(function ($q) use ($search) {
                        $q->whereTranslationLike('name', "%{$search}%")
                          ->orWhereTranslationLike('description', "%{$search}%")
                          ->orWhere('whatsapp', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
                }
            })
            ->make(true);
    }

    //======================
    /**
     * Show the form for creating a new nursery.
     */
    public function create(): View
    {
        abort_if(!auth()->user()->can('nursery.create'), 403);

        $formData = $this->nurseryService->getFormData();

        return view('nursery::create', [
            'employees' => $formData['employees'],
            'governments' => $formData['governments'],
        ]);
    }

    //======================
    /**
     * Store a newly created nursery in database.
     */
    public function store(StoreNurseryRequest $request): RedirectResponse|JsonResponse
    {
        abort_if(!auth()->user()->can('nursery.create'), 403);

        $nursery = $this->nurseryService->createNursery(
            $request->validated(),
            $request->file('image'),
            auth()->id()
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Nursery created successfully.'),
                'data' => $nursery,
            ], 201);
        }

        return redirect()
            ->route('nursery.index')
            ->with('success', __('Nursery created successfully.'));
    }

    //======================
    /**
     * Show the specified nursery details with its work days and translations.
     */
    public function show(int $id): View|JsonResponse
    {
        abort_if(!auth()->user()->can('nursery.view'), 403);

        $nursery = $this->nurseryService->getNurseryById($id);

        if (!$nursery) {
            abort(404, __('Nursery not found.'));
        }

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'data' => $nursery,
            ]);
        }

        return view('nursery::show', compact('nursery'));
    }

    //======================
    /**
     * Show the form for editing the specified nursery.
     */
    public function edit(int $id): View
    {
        abort_if(!auth()->user()->can('nursery.edit'), 403);

        $nursery = $this->nurseryService->getNurseryById($id);

        if (!$nursery) {
            abort(404, __('Nursery not found.'));
        }

        $formData = $this->nurseryService->getFormData();

        return view('nursery::edit', [
            'nursery' => $nursery,
            'employees' => $formData['employees'],
            'governments' => $formData['governments'],
        ]);
    }

    //======================
    /**
     * Update the specified nursery in database.
     */
    public function update(UpdateNurseryRequest $request, int $id): RedirectResponse|JsonResponse
    {
        abort_if(!auth()->user()->can('nursery.edit'), 403);

        $nursery = $this->nurseryService->updateNursery(
            $id,
            $request->validated(),
            $request->file('image')
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Nursery updated successfully.'),
                'data' => $nursery,
            ]);
        }

        return redirect()
            ->route('nursery.index')
            ->with('success', __('Nursery updated successfully.'));
    }

    //======================
    /**
     * Remove the specified nursery from storage (Soft Delete).
     */
    public function destroy(int $id): RedirectResponse|JsonResponse
    {
        abort_if(!auth()->user()->can('nursery.delete'), 403);

        $this->nurseryService->deleteNursery($id);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Nursery deleted successfully.'),
            ]);
        }

        return redirect()
            ->route('nursery.index')
            ->with('success', __('Nursery deleted successfully.'));
    }

    //======================
    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(int $id): JsonResponse
    {
        abort_if(!auth()->user()->can('nursery.toggle-status'), 403);

        $nursery = $this->nurseryService->toggleStatus($id);

        return response()->json([
            'success' => true,
            'message' => __('Status updated successfully.'),
            'status' => $nursery->status->value,
            'badge' => $nursery->status->badgeColor(),
        ]);
    }
}
