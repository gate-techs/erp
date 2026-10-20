<?php
namespace Modules\Academic\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Academic\Http\Requests\StoreDailyReportRequest;
use Modules\Academic\Http\Requests\UpdateDailyReportRequest;
use Modules\Academic\Models\DailyReport;
use Modules\Academic\Services\DailyReportService;
use Yajra\DataTables\Facades\DataTables;

class DailyReportController extends Controller
{
    public function __construct(protected DailyReportService $service) {}
    public function index(): View
    {
        abort_if(! auth()->user()->can('academic.daily-reports.view'), 403);
        return view('academic::daily-reports.index', $this->service->formData());
    }

    public function create(): View
    {
        abort_if(! auth()->user()->can('academic.daily-reports.create'), 403);
        return view('academic::daily-reports.create', $this->service->formData());
    }

    public function edit(int $id): View
    {
        abort_if(! auth()->user()->can('academic.daily-reports.edit'), 403);
        $report = $this->service->find($id);
        abort_unless($report, 404, __('Daily report not found.'));
        return view('academic::daily-reports.edit', array_merge($this->service->formData(), compact('report')));
    }

    public function show(int $id): View
    {
        abort_if(! auth()->user()->can('academic.daily-reports.view'), 403);
        $report = $this->service->find($id);
        abort_unless($report, 404, __('Daily report not found.'));
        return view('academic::daily-reports.show', compact('report'));
    }

    public function data(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.daily-reports.view'), 403);
        $canEdit = auth()->user()->can('academic.daily-reports.edit');
        $canDelete = auth()->user()->can('academic.daily-reports.delete');
        $canView = auth()->user()->can('academic.daily-reports.view');

        return DataTables::eloquent($this->service->query($request->all()))
            ->addIndexColumn()
            ->addColumn('institution_name', fn(DailyReport $r) => $r->institution?->name ?? '-')
            ->addColumn('branch_name', fn(DailyReport $r) => $r->branch?->name ?? '-')
            ->editColumn('report_date', function (DailyReport $r) use ($canEdit) {
                $date = $r->report_date ? $r->report_date->format('Y-m-d') : '-';
                if ($canEdit) {
                    return '<a href="' . route('academic.daily-reports.edit', $r) . '" class="fw-semibold text-heading text-hover-primary" title="' . e(__('Edit')) . '">' . e($date) . '</a>';
                }
                return '<span class="fw-semibold text-heading">' . e($date) . '</span>';
            })
            ->addColumn('actions', function (DailyReport $r) use ($canView, $canEdit, $canDelete) {
                $buttons = [];
                if ($canView) {
                    $buttons[] = '<a class="btn btn-sm btn-icon btn-label-info" href="' . route('academic.daily-reports.show', $r) . '" title="' . e(__('View')) . '"><i class="bx bx-show"></i></a>';
                }
                if ($canEdit) {
                    $buttons[] = '<a class="btn btn-sm btn-icon btn-label-primary" href="' . route('academic.daily-reports.edit', $r) . '" title="' . e(__('Edit')) . '"><i class="bx bx-edit"></i></a>';
                }
                if ($canDelete) {
                    $buttons[] = '<button type="button" class="btn btn-sm btn-icon btn-label-danger delete-report" data-id="' . $r->id . '" title="' . e(__('Delete')) . '"><i class="bx bx-trash"></i></button>';
                }
                if (empty($buttons)) {
                    return '<span class="text-muted">-</span>';
                }
                return '<div class="d-flex gap-1 justify-content-center">' . implode('', $buttons) . '</div>';
            })
            ->rawColumns(['report_date', 'actions'])
            ->make(true);
    }

    public function store(StoreDailyReportRequest $request): RedirectResponse
    {
        abort_if(! auth()->user()->can('academic.daily-reports.create'), 403);
        $this->service->create($request->validated(), auth()->id());
        return redirect()->route('academic.daily-reports.index')->with('success', __('Daily report created successfully.'));
    }

    public function update(UpdateDailyReportRequest $request, int $id): RedirectResponse
    {
        abort_if(! auth()->user()->can('academic.daily-reports.edit'), 403);
        $this->service->update($id, $request->validated());
        return redirect()->route('academic.daily-reports.index')->with('success', __('Daily report updated successfully.'));
    }

    public function destroy(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.daily-reports.delete'), 403);
        abort_unless($this->service->delete($id), 404);
        return response()->json(['success' => true, 'message' => __('Daily report deleted successfully.')]);
    }

    public function branches(int $institutionId): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.daily-reports.view'), 403);
        return response()->json($this->service->branches($institutionId)->map(fn($b) => ['id' => $b->id, 'name' => $b->name]));
    }
}
