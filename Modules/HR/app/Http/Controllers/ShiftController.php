<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\HR\Http\Requests\StoreShiftRequest;
use Modules\HR\Http\Requests\UpdateShiftRequest;
use Modules\HR\Models\Shift;
use Modules\HR\Services\ShiftService;
use Yajra\DataTables\Facades\DataTables;

class ShiftController extends Controller
{
    //======================
    public function __construct(
        protected ShiftService $shiftService
    ) {}

    //======================
    /**
     * Display listing view for Shifts in HR Settings.
     */
    public function index(): View
    {
        abort_if(! auth()->user()->can('hr.settings.shifts.view'), 403);

        $formData = $this->shiftService->getFormData();

        return view('hr::settings.shifts.index', [
            'statuses' => $formData['statuses'],
        ]);
    }

    //======================
    /**
     * Server-side DataTables endpoint for Shifts.
     */
    public function data(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.settings.shifts.view'), 403);

        $query = $this->shiftService->getDataTableQuery($request->all());

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('name_ar', fn(Shift $item) => $item->translate('ar')?->name ?? '-')
            ->addColumn('name_en', fn(Shift $item) => $item->translate('en')?->name ?? '-')
            ->editColumn('code', fn(Shift $item) => $item->code ?? '-')
            ->addColumn('time_range', function (Shift $item) {
                $start = Carbon::parse($item->start_time)->format('h:i A');
                $end = Carbon::parse($item->end_time)->format('h:i A');
                $hours = $item->total_hours ? $item->total_hours . ' ' . __('hrs') : '';

                return '<div class="d-flex flex-column">
                            <span class="fw-semibold text-heading dir-ltr">' . $start . ' - ' . $end . '</span>
                            <small class="text-muted">' . $hours . '</small>
                        </div>';
            })
            ->addColumn('grace_periods', function (Shift $item) {
                $in = $item->grace_period_in . ' ' . __('min');
                $out = $item->grace_period_out . ' ' . __('min');

                return '<div class="d-flex flex-column small">
                            <span><span class="text-muted">' . __('In:') . '</span> ' . $in . '</span>
                            <span><span class="text-muted">' . __('Out:') . '</span> ' . $out . '</span>
                        </div>';
            })
            ->editColumn('is_overnight', function (Shift $item) {
                if ($item->is_overnight) {
                    return '<span class="badge bg-label-dark"><i class="icon-base bx bx-moon me-1"></i>' . __('Yes') . '</span>';
                }
                return '<span class="badge bg-label-secondary">' . __('No') . '</span>';
            })
            ->addColumn('color_badge', function (Shift $item) {
                $color = $item->color ?? '#7367f0';
                return '<div class="d-flex align-items-center gap-2">
                            <span class="rounded-circle shadow-xs" style="display:inline-block; width: 18px; height: 18px; background-color: ' . e($color) . ';"></span>
                            <small class="text-muted">' . e($color) . '</small>
                        </div>';
            })
            ->editColumn('status', function (Shift $item) {
                $statusEnum = $item->status;
                $badgeClass = $statusEnum ? $statusEnum->badgeColor() : 'bg-label-secondary';
                $label = $statusEnum ? $statusEnum->label() : ($item->status ?? '-');

                return '<span class="badge ' . $badgeClass . '">' . $label . '</span>';
            })
            ->addColumn('status_raw', fn(Shift $item) => is_object($item->status) ? $item->status->value : $item->status)
            ->addColumn('description_ar', fn(Shift $item) => $item->translate('ar')?->description ?? '')
            ->addColumn('description_en', fn(Shift $item) => $item->translate('en')?->description ?? '')
            ->addColumn('start_time_raw', fn(Shift $item) => Carbon::parse($item->start_time)->format('H:i'))
            ->addColumn('end_time_raw', fn(Shift $item) => Carbon::parse($item->end_time)->format('H:i'))
            ->addColumn('checkin_start_raw', fn(Shift $item) => $item->checkin_start ? Carbon::parse($item->checkin_start)->format('H:i') : '')
            ->addColumn('checkin_end_raw', fn(Shift $item) => $item->checkin_end ? Carbon::parse($item->checkin_end)->format('H:i') : '')
            ->addColumn('checkout_start_raw', fn(Shift $item) => $item->checkout_start ? Carbon::parse($item->checkout_start)->format('H:i') : '')
            ->addColumn('checkout_end_raw', fn(Shift $item) => $item->checkout_end ? Carbon::parse($item->checkout_end)->format('H:i') : '')
            ->editColumn('created_at', fn(Shift $item) => $item->created_at ? $item->created_at->format('Y-m-d') : '-')
            ->addColumn('actions', function (Shift $item) {
                $user = auth()->user();

                $rowJson = htmlspecialchars(json_encode([
                    'id'               => $item->id,
                    'code'             => $item->code ?? '',
                    'name_ar'          => $item->translate('ar')?->name ?? '',
                    'name_en'          => $item->translate('en')?->name ?? '',
                    'description_ar'   => $item->translate('ar')?->description ?? '',
                    'description_en'   => $item->translate('en')?->description ?? '',
                    'start_time'       => Carbon::parse($item->start_time)->format('H:i'),
                    'end_time'         => Carbon::parse($item->end_time)->format('H:i'),
                    'checkin_start'    => $item->checkin_start ? Carbon::parse($item->checkin_start)->format('H:i') : '',
                    'checkin_end'      => $item->checkin_end ? Carbon::parse($item->checkin_end)->format('H:i') : '',
                    'checkout_start'   => $item->checkout_start ? Carbon::parse($item->checkout_start)->format('H:i') : '',
                    'checkout_end'     => $item->checkout_end ? Carbon::parse($item->checkout_end)->format('H:i') : '',
                    'grace_period_in'  => $item->grace_period_in ?? 15,
                    'grace_period_out' => $item->grace_period_out ?? 0,
                    'is_overnight'     => (bool) $item->is_overnight,
                    'color'            => $item->color ?? '#7367f0',
                    'status'           => is_object($item->status) ? $item->status->value : $item->status,
                ]), ENT_QUOTES, 'UTF-8');

                $editBtn = '';
                if ($user->can('hr.settings.shifts.edit')) {
                    $editBtn = '<button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-shift" 
                        data-row="' . $rowJson . '" 
                        title="' . __('Edit') . '">
                        <i class="icon-base bx bx-edit-alt"></i>
                    </button>';
                }

                $deleteBtn = '';
                if ($user->can('hr.settings.shifts.delete')) {
                    $deleteBtn = '<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-shift" 
                        data-id="' . $item->id . '" 
                        data-name="' . e($item->translate('ar')?->name ?? $item->translate('en')?->name ?? '') . '" 
                        title="' . __('Delete') . '">
                        <i class="icon-base bx bx-trash"></i>
                    </button>';
                }

                $buttons = $editBtn . $deleteBtn;
                return $buttons ? '<div class="d-inline-flex gap-1">' . $buttons . '</div>' : '<span class="text-muted">---</span>';
            })
            ->rawColumns(['time_range', 'grace_periods', 'is_overnight', 'color_badge', 'status', 'actions'])
            ->filterColumn('name_ar', function ($query, $keyword) {
                $query->whereTranslationLike('name', "%{$keyword}%", 'ar');
            })
            ->filterColumn('name_en', function ($query, $keyword) {
                $query->whereTranslationLike('name', "%{$keyword}%", 'en');
            })
            ->make(true);
    }

    //======================
    /**
     * Store a newly created shift.
     */
    public function store(StoreShiftRequest $request): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.settings.shifts.create'), 403);

        try {
            $this->shiftService->createShift($request->validated(), auth()->id());

            return redirect()->route('hr.settings.shifts.index')
                ->with('success', __('Shift created successfully.'));
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('An error occurred while creating the shift: ') . $e->getMessage());
        }
    }

    //======================
    /**
     * Update the specified shift.
     */
    public function update(UpdateShiftRequest $request, int $id): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.settings.shifts.edit'), 403);

        try {
            $shift = $this->shiftService->getShiftById($id);
            if (!$shift) {
                return redirect()->route('hr.settings.shifts.index')
                    ->with('error', __('Shift not found.'));
            }

            $this->shiftService->updateShift($shift, $request->validated());

            return redirect()->route('hr.settings.shifts.index')
                ->with('success', __('Shift updated successfully.'));
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('An error occurred while updating the shift: ') . $e->getMessage());
        }
    }

    //======================
    /**
     * Remove the specified shift.
     */
    public function destroy(int $id): RedirectResponse
    {
        abort_if(! auth()->user()->can('hr.settings.shifts.delete'), 403);

        try {
            $shift = $this->shiftService->getShiftById($id);
            if (!$shift) {
                return redirect()->route('hr.settings.shifts.index')
                    ->with('error', __('Shift not found.'));
            }

            $this->shiftService->deleteShift($shift);

            return redirect()->route('hr.settings.shifts.index')
                ->with('success', __('Shift deleted successfully.'));
        } catch (Exception $e) {
            return redirect()->route('hr.settings.shifts.index')
                ->with('error', __('An error occurred while deleting the shift: ') . $e->getMessage());
        }
    }
}
