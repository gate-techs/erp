<?php

namespace Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Academic\Http\Requests\StoreIncidentReportRequest;
use Modules\Academic\Http\Requests\UpdateIncidentReportRequest;
use Modules\Academic\Models\IncidentReport;
use Modules\Academic\Services\IncidentReportService;
use Yajra\DataTables\Facades\DataTables;

class IncidentReportController extends Controller
{
    public function __construct(
        protected IncidentReportService $service
    ) {}

    /**
     * Display a listing of incident reports.
     */
    public function index(): View
    {
        return view('academic::incident-reports.index', $this->service->formData());
    }

    /**
     * Provide DataTables JSON data for incident reports.
     */
    public function data(Request $request): JsonResponse
    {
        $query = $this->service->query($request->all());

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('student_html', function (IncidentReport $r) {
                $student = $r->student;
                if (! $student) {
                    return '<span class="text-muted">-</span>';
                }

                $name = $student->translate(app()->getLocale())?->name
                    ?? $student->translate('ar')?->name
                    ?? $student->name
                    ?? '-';

                $avatarHtml = $student->photo_url
                    ? '<img src="' . e($student->photo_url) . '" alt="" class="rounded-circle" width="34" height="34" style="object-fit: cover;">'
                    : '<div class="avatar-initial rounded-circle bg-label-primary d-inline-flex align-items-center justify-content-center fw-bold" style="width:34px; height:34px; font-size:12px;">' . mb_substr($name, 0, 1) . '</div>';

                $className = $student->class?->translate(app()->getLocale())?->name
                    ?? $student->class?->translate('ar')?->name
                    ?? $student->class?->name;

                $classHtml = $className ? '<small class="text-muted d-block">' . e($className) . '</small>' : '';

                return '<div class="d-flex align-items-center gap-2">
                    ' . $avatarHtml . '
                    <div>
                        <a href="' . route('academic.students.show', $student->id) . '" class="fw-semibold text-heading text-truncate d-inline-block" style="max-width: 170px;">' . e($name) . '</a>
                        ' . $classHtml . '
                    </div>
                </div>';
            })
            ->addColumn('incident_type_badge', function (IncidentReport $r) {
                $type = $r->incident_type_name;
                return '<span class="badge bg-label-warning px-2 py-1"><i class="bx bx-error-circle me-1"></i>' . e($type) . '</span>';
            })
            ->addColumn('incident_datetime', function (IncidentReport $r) {
                $date = $r->date ? $r->date->format('Y-m-d') : '-';
                $time = $r->time ? '<span class="badge bg-label-secondary ms-1"><i class="bx bx-time-five me-1 font-12"></i>' . e($r->time) . '</span>' : '';
                return '<span class="fw-medium text-heading">' . e($date) . '</span>' . $time;
            })
            ->addColumn('location_html', function (IncidentReport $r) {
                if (empty($r->location)) {
                    return '<span class="text-muted">-</span>';
                }
                return '<span class="badge bg-label-info text-truncate" style="max-width: 130px;"><i class="bx bx-map-pin me-1"></i>' . e($r->location) . '</span>';
            })
            ->addColumn('officer_name', function (IncidentReport $r) {
                return $r->user?->name ? e($r->user->name) : '<span class="text-muted">-</span>';
            })
            ->addColumn('witnesses_badge', function (IncidentReport $r) {
                $count = is_array($r->persons) ? count(array_filter($r->persons)) : 0;
                if ($count === 0) {
                    return '<span class="text-muted">-</span>';
                }
                $names = e($r->persons_names);
                return '<span class="badge bg-label-primary" data-bs-toggle="tooltip" title="' . $names . '"><i class="bx bx-group me-1"></i>' . $count . ' ' . __('شخص') . '</span>';
            })
            ->addColumn('actions', function (IncidentReport $r) {
                $showUrl  = route('academic.incident-reports.show', $r->id);
                $editUrl  = route('academic.incident-reports.edit', $r->id);
                $printUrl = route('academic.incident-reports.print', $r->id);

                return '<div class="d-flex gap-1 justify-content-center">
                    <a class="btn btn-sm btn-icon btn-label-info" href="' . $showUrl . '" title="' . e(__('عرض التقرير')) . '">
                        <i class="bx bx-show font-16"></i>
                    </a>
                    <a class="btn btn-sm btn-icon btn-label-secondary" href="' . $printUrl . '" target="_blank" title="' . e(__('طباعة')) . '">
                        <i class="bx bx-printer font-16"></i>
                    </a>
                    <a class="btn btn-sm btn-icon btn-label-primary" href="' . $editUrl . '" title="' . e(__('تعديل')) . '">
                        <i class="bx bx-edit font-16"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-delete-incident" data-id="' . $r->id . '" title="' . e(__('حذف')) . '">
                        <i class="bx bx-trash font-16"></i>
                    </button>
                </div>';
            })
            ->filterColumn('student_html', function ($query, $keyword) {
                $query->whereHas('student', function ($q) use ($keyword) {
                    $q->whereHas('translations', function ($t) use ($keyword) {
                        $t->where('name', 'like', "%{$keyword}%");
                    })->orWhere('student_code', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('incident_type_badge', function ($query, $keyword) {
                $query->whereHas('incidentType', function ($q) use ($keyword) {
                    $q->whereHas('translations', function ($t) use ($keyword) {
                        $t->where('name', 'like', "%{$keyword}%");
                    });
                });
            })
            ->filterColumn('officer_name', function ($query, $keyword) {
                $query->whereHas('user', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['student_html', 'incident_type_badge', 'incident_datetime', 'location_html', 'officer_name', 'witnesses_badge', 'actions'])
            ->make(true);
    }

    /**
     * Show form for creating a new incident report.
     */
    public function create(Request $request): View
    {
        $selectedStudentId = $request->query('student_id');
        $data = $this->service->formData();
        $data['selectedStudentId'] = $selectedStudentId;

        return view('academic::incident-reports.create', $data);
    }

    /**
     * Store a newly created incident report in storage.
     */
    public function store(StoreIncidentReportRequest $request): RedirectResponse
    {
        $report = $this->service->create($request->validated(), auth()->id());

        return redirect()->route('academic.incident-reports.show', $report->id)
            ->with('success', __('تم تسجيل تقرير الحادث بنجاح.'));
    }

    /**
     * Display the specified incident report.
     */
    public function show(int $id): View
    {
        $report = $this->service->find($id);
        abort_unless($report, 404, __('تقرير الحادث غير موجود.'));

        return view('academic::incident-reports.show', compact('report'));
    }

    /**
     * Show form for editing an incident report.
     */
    public function edit(int $id): View
    {
        $report = $this->service->find($id);
        abort_unless($report, 404, __('تقرير الحادث غير موجود.'));

        $formData = $this->service->formData($report->branch_id, $report->institution_id);

        return view('academic::incident-reports.edit', array_merge($formData, compact('report')));
    }

    /**
     * Update the specified incident report in storage.
     */
    public function update(UpdateIncidentReportRequest $request, int $id): RedirectResponse
    {
        $report = $this->service->update($id, $request->validated(), auth()->id());
        abort_unless($report, 404, __('تقرير الحادث غير موجود.'));

        return redirect()->route('academic.incident-reports.show', $report->id)
            ->with('success', __('تم تحديث تقرير الحادث بنجاح.'));
    }

    /**
     * Remove the specified incident report from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->service->delete($id, auth()->id());
        abort_unless($deleted, 404);

        return response()->json([
            'success' => true,
            'message' => __('تم حذف تقرير الحادث بنجاح.'),
        ]);
    }

    /**
     * Printable formal view of the incident report.
     */
    public function print(int $id): View
    {
        $report = $this->service->find($id);
        abort_unless($report, 404, __('تقرير الحادث غير موجود.'));

        return view('academic::incident-reports.print', compact('report'));
    }
}
