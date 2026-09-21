<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\HR\Http\Requests\StoreOfficialHolidayRequest;
use Modules\HR\Http\Requests\UpdateOfficialHolidayRequest;
use Modules\HR\Models\OfficialHoliday;
use Modules\HR\Services\OfficialHolidayService;
use Yajra\DataTables\Facades\DataTables;

class OfficialHolidayController extends Controller
{
    //======================
    public function __construct(
        protected OfficialHolidayService $officialHolidayService
    ) {}

    //======================
    /**
     * Display listing view for Official Holidays in HR Settings.
     */
    public function index(): View
    {
        $formData = $this->officialHolidayService->getFormData();

        return view('hr::settings.official_holidays.index', $formData);
    }

    //======================
    /**
     * Server-side DataTables endpoint for Official Holidays.
     */
    public function data(Request $request): JsonResponse
    {
        $query = $this->officialHolidayService->getDataTableQuery($request->all());

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('name_ar', fn(OfficialHoliday $item) => $item->translate('ar')?->name ?? '-')
            ->addColumn('name_en', fn(OfficialHoliday $item) => $item->translate('en')?->name ?? '-')
            ->addColumn('date_range', function (OfficialHoliday $item) {
                $start = Carbon::parse($item->start_date)->format('Y-m-d');
                $end = Carbon::parse($item->end_date)->format('Y-m-d');
                $days = $item->days_count . ' ' . ($item->days_count === 1 ? __('day') : __('days'));

                return '<div class="d-flex flex-column">
                            <span class="fw-semibold text-heading dir-ltr">' . $start . ' &rarr; ' . $end . '</span>
                            <small class="text-primary fw-bold">' . $days . '</small>
                        </div>';
            })
            ->editColumn('year', function (OfficialHoliday $item) {
                return '<span class="badge bg-label-info">' . $item->year . '</span>';
            })
            ->editColumn('is_recurring', function (OfficialHoliday $item) {
                if ($item->is_recurring) {
                    return '<span class="badge bg-label-success"><i class="icon-base bx bx-repeat me-1"></i>' . __('Yes') . '</span>';
                }
                return '<span class="badge bg-label-secondary">' . __('No') . '</span>';
            })
            ->addColumn('scope_info', function (OfficialHoliday $item) {
                $branch = $item->branch?->translate('ar')?->name ?? $item->branch?->translate('en')?->name;
                if ($branch) {
                    return '<div class="d-flex align-items-center gap-1"><i class="icon-base bx bx-store text-info font-16"></i><span>' . e($branch) . '</span></div>';
                }
                return '<span class="badge bg-label-secondary">' . __('All Branches (Central)') . '</span>';
            })
            ->addColumn('color_badge', function (OfficialHoliday $item) {
                $color = $item->color ?? '#e83e8c';
                return '<div class="d-flex align-items-center gap-2">
                            <span class="rounded-circle shadow-xs" style="display:inline-block; width: 18px; height: 18px; background-color: ' . e($color) . ';"></span>
                            <small class="text-muted">' . e($color) . '</small>
                        </div>';
            })
            ->editColumn('status', function (OfficialHoliday $item) {
                $statusEnum = $item->status;
                $badgeClass = $statusEnum ? $statusEnum->badgeColor() : 'bg-label-secondary';
                $label = $statusEnum ? $statusEnum->label() : ($item->status ?? '-');

                return '<span class="badge ' . $badgeClass . '">' . $label . '</span>';
            })
            ->addColumn('actions', function (OfficialHoliday $item) {
                $rowJson = htmlspecialchars(json_encode([
                    'id'             => $item->id,
                    'name_ar'        => $item->translate('ar')?->name ?? '',
                    'name_en'        => $item->translate('en')?->name ?? '',
                    'description_ar' => $item->translate('ar')?->description ?? '',
                    'description_en' => $item->translate('en')?->description ?? '',
                    'start_date'     => Carbon::parse($item->start_date)->format('Y-m-d'),
                    'end_date'       => Carbon::parse($item->end_date)->format('Y-m-d'),
                    'is_recurring'   => (bool) $item->is_recurring,
                    'branch_id'      => $item->branch_id,
                    'color'          => $item->color ?? '#e83e8c',
                    'status'         => is_object($item->status) ? $item->status->value : $item->status,
                ]), ENT_QUOTES, 'UTF-8');

                return '
                    <div class="d-inline-flex gap-1">
                        <button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-holiday" 
                            data-row="' . $rowJson . '" 
                            title="' . __('Edit') . '">
                            <i class="icon-base bx bx-edit-alt"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-holiday" 
                            data-id="' . $item->id . '" 
                            data-name="' . e($item->translate('ar')?->name ?? $item->translate('en')?->name ?? '') . '" 
                            title="' . __('Delete') . '">
                            <i class="icon-base bx bx-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['date_range', 'year', 'is_recurring', 'scope_info', 'color_badge', 'status', 'actions'])
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
     * Store a newly created official holiday.
     */
    public function store(StoreOfficialHolidayRequest $request): RedirectResponse
    {
        try {
            $this->officialHolidayService->createHoliday($request->validated(), auth()->id());

            return redirect()->route('hr.settings.official-holidays.index')
                ->with('success', __('Official holiday created successfully.'));
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('An error occurred while creating the official holiday: ') . $e->getMessage());
        }
    }

    //======================
    /**
     * Update the specified official holiday.
     */
    public function update(UpdateOfficialHolidayRequest $request, int $id): RedirectResponse
    {
        try {
            $holiday = $this->officialHolidayService->getHolidayById($id);
            if (!$holiday) {
                return redirect()->route('hr.settings.official-holidays.index')
                    ->with('error', __('Official holiday not found.'));
            }

            $this->officialHolidayService->updateHoliday($holiday, $request->validated());

            return redirect()->route('hr.settings.official-holidays.index')
                ->with('success', __('Official holiday updated successfully.'));
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('An error occurred while updating the official holiday: ') . $e->getMessage());
        }
    }

    //======================
    /**
     * Remove the specified official holiday.
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $holiday = $this->officialHolidayService->getHolidayById($id);
            if (!$holiday) {
                return redirect()->route('hr.settings.official-holidays.index')
                    ->with('error', __('Official holiday not found.'));
            }

            $this->officialHolidayService->deleteHoliday($holiday);

            return redirect()->route('hr.settings.official-holidays.index')
                ->with('success', __('Official holiday deleted successfully.'));
        } catch (Exception $e) {
            return redirect()->route('hr.settings.official-holidays.index')
                ->with('error', __('An error occurred while deleting the official holiday: ') . $e->getMessage());
        }
    }
}
