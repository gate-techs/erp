<?php

namespace Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Modules\Academic\Http\Requests\StoreAcademicSubjectRequest;
use Modules\Academic\Http\Requests\UpdateAcademicSubjectRequest;
use Modules\Academic\Models\AcademicStage;
use Modules\Academic\Models\AcademicSubject;
use Modules\Academic\Models\AcademicSubjectAttachment;
use Modules\Academic\Models\AcademicYear;
use Modules\Academic\Services\AcademicSubjectService;
use Modules\Nursery\Models\Nursery;
use Yajra\DataTables\Facades\DataTables;

class AcademicSubjectController extends Controller
{
    public function __construct(
        protected AcademicSubjectService $service
    ) {}

    public function index(): View
    {
        $institutions = Nursery::with('translations')->get();
        $academicYears = AcademicYear::with('translations')->get();
        $academicStages = AcademicStage::with('translations')->get();
        $statuses = AcademicSubject::statuses();

        return view('academic::academic-subjects.index', compact(
            'institutions',
            'academicYears',
            'academicStages',
            'statuses'
        ));
    }

    public function board(Request $request): View
    {
        $institutions = Nursery::with('translations')->get();
        $academicYears = AcademicYear::with('translations')->get();
        $selectedInstitutionId = $request->get('institution_id', $institutions->first()?->id);
        $selectedYearId = $request->get('academic_year_id');

        $stagesQuery = AcademicStage::with('translations');
        if ($selectedInstitutionId) {
            $stagesQuery->where('institution_id', $selectedInstitutionId);
        }
        $stages = $stagesQuery->get();

        $subjectsQuery = AcademicSubject::with(['translations', 'academicStages.translations', 'institution.translations', 'attachments'])
            ->where('status', AcademicSubject::STATUS_ACTIVE);
            
        if ($selectedInstitutionId) {
            $subjectsQuery->where('institution_id', $selectedInstitutionId);
        }
        if ($selectedYearId) {
            $subjectsQuery->where('academic_year_id', $selectedYearId);
        }
        $subjects = $subjectsQuery->get();

        return view('academic::academic-subjects.board', compact(
            'institutions',
            'academicYears',
            'stages',
            'subjects',
            'selectedInstitutionId',
            'selectedYearId'
        ));
    }

    public function syncSubjectStage(Request $request): JsonResponse
    {
        $request->validate([
            'subject_id' => 'required|exists:academic_subjects,id',
            'stage_id' => 'required|exists:academic_stages,id',
            'action' => 'required|in:attach,detach',
        ]);

        $subject = AcademicSubject::findOrFail($request->subject_id);
        $stageId = (int) $request->stage_id;

        if ($request->action === 'attach') {
            $subject->academicStages()->syncWithoutDetaching([$stageId]);
            $message = __('Subject added to academic stage successfully.');
        } else {
            $subject->academicStages()->detach([$stageId]);
            $message = __('Subject removed from academic stage successfully.');
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        return DataTables::eloquent($this->service->getDataTableQuery($request->all()))
            ->addIndexColumn()
            ->addColumn('name', function (AcademicSubject $item) {
                $name = $item->translate(app()->getLocale())?->name
                    ?? $item->translate('ar')?->name
                    ?? $item->translate('en')?->name
                    ?? '-';
                $imageUrl = $item->image_url;

                if ($imageUrl) {
                    $avatar = '<div class="avatar avatar-sm me-2 rounded flex-shrink-0" style="width: 36px; height: 36px;">
                        <img src="' . e($imageUrl) . '" alt="' . e($name) . '" class="w-100 h-100 rounded object-fit-cover border">
                    </div>';
                } else {
                    $avatar = '<div class="avatar avatar-sm me-2 bg-label-primary rounded d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                        <i class="icon-base bx bx-book font-20 text-primary"></i>
                    </div>';
                }

                return '<div class="d-flex align-items-center">' . $avatar . '<button type="button" class="btn btn-link p-0 text-start fw-semibold text-heading text-decoration-none btn-edit-academic-subject" data-id="' . $item->id . '" title="' . e(__('Edit')) . '">' . e($name) . '</button></div>';
            })
            ->addColumn('institution_name', fn (AcademicSubject $item) => e($item->institution?->name ?? '-'))
            ->addColumn('stages_badges', function (AcademicSubject $item) {
                if ($item->academicStages->isEmpty()) {
                    return '<span class="text-muted">' . __('All Stages') . '</span>';
                }

                $html = '<div class="d-flex flex-wrap gap-1">';
                foreach ($item->academicStages as $stage) {
                    $stageName = $stage->translate(app()->getLocale())?->name ?? $stage->translate('ar')?->name ?? $stage->name ?? $stage->code;
                    $html .= '<span class="badge bg-label-primary font-11 py-1 px-2">' . e($stageName) . '</span>';
                }
                $html .= '</div>';

                return $html;
            })
            ->addColumn('year_name', function (AcademicSubject $item) {
                return e(
                    $item->academicYear?->translate(app()->getLocale())?->name
                        ?? $item->academicYear?->translate('ar')?->name
                        ?? $item->academicYear?->translate('en')?->name
                        ?? $item->academicYear?->code
                        ?? '-'
                );
            })
            ->addColumn('attachments_count_badge', function (AcademicSubject $item) {
                $count = $item->attachments_count ?? $item->attachments->count();
                if ($count > 0) {
                    return '<span class="badge bg-label-info"><i class="icon-base bx bx-paperclip me-1"></i>' . $count . '</span>';
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('status_badge', function (AcademicSubject $item) {
                $isActive = $item->isActive();
                $badgeClass = $isActive ? 'bg-label-success' : 'bg-label-danger';
                $label = $isActive ? __('Active') : __('Inactive');

                return '<button type="button" class="btn btn-xs ' . $badgeClass . ' btn-toggle-academic-subject-status" data-id="' . $item->id . '">'
                    . e($label) .
                '</button>';
            })
            ->addColumn('actions', function (AcademicSubject $item) {
                $name = $item->translate(app()->getLocale())?->name
                    ?? $item->translate('ar')?->name
                    ?? $item->translate('en')?->name;

                return '
                    <div class="d-inline-flex align-items-center gap-1">
                        <button type="button" class="btn btn-icon btn-sm btn-outline-info btn-clone-academic-subject"
                            data-id="' . $item->id . '"
                            data-name="' . e($name) . '"
                            title="' . __('Clone Subject') . '">
                            <i class="icon-base bx bx-copy"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-academic-subject"
                            data-id="' . $item->id . '"
                            title="' . __('Edit') . '">
                            <i class="icon-base bx bx-edit"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-academic-subject"
                            data-id="' . $item->id . '"
                            data-name="' . e($name) . '"
                            title="' . __('Delete') . '">
                            <i class="icon-base bx bx-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['name', 'stages_badges', 'attachments_count_badge', 'status_badge', 'actions'])
            ->make(true);
    }

    public function store(StoreAcademicSubjectRequest $request): JsonResponse
    {
        $subject = $this->service->createAcademicSubject(
            $request->validated(),
            $request->user()?->id,
            $request->file('attachments', [])
        );

        return response()->json([
            'success' => true,
            'message' => __('Academic subject created successfully.'),
            'data' => $subject,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $subject = $this->service->getAcademicSubject($id);

        if (!$subject) {
            return response()->json([
                'success' => false,
                'message' => __('Academic subject not found.'),
            ], 404);
        }

        $data = $subject->toArray();
        $data['name_ar'] = $subject->translate('ar')?->name ?? '';
        $data['name_en'] = $subject->translate('en')?->name ?? '';
        $data['description_ar'] = $subject->translate('ar')?->description ?? '';
        $data['description_en'] = $subject->translate('en')?->description ?? '';
        $data['stage_ids'] = $subject->academicStages->pluck('id')->toArray();
        $data['attachments'] = $subject->attachments->map(fn ($att) => [
            'id' => $att->id,
            'file_name' => $att->file_name,
            'file_url' => $att->file_url,
            'formatted_size' => $att->formatted_size,
            'file_type' => $att->file_type,
        ]);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function update(UpdateAcademicSubjectRequest $request, int $id): JsonResponse
    {
        $subject = $this->service->updateAcademicSubject(
            $id,
            $request->validated(),
            $request->file('attachments', [])
        );

        return response()->json([
            'success' => true,
            'message' => __('Academic subject updated successfully.'),
            'data' => $subject,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->deleteAcademicSubject($id);

        return response()->json([
            'success' => true,
            'message' => __('Academic subject deleted successfully.'),
        ]);
    }

    public function toggleStatus(int $id): JsonResponse
    {
        $subject = $this->service->toggleStatus($id);

        return response()->json([
            'success' => true,
            'message' => __('Academic subject status updated successfully.'),
            'data' => [
                'id' => $subject->id,
                'status' => $subject->status,
                'is_active' => $subject->isActive(),
            ],
        ]);
    }

    public function deleteAttachment(int $id): JsonResponse
    {
        $this->service->deleteAttachment($id);

        return response()->json([
            'success' => true,
            'message' => __('Attachment deleted successfully.'),
        ]);
    }

    public function clone(Request $request, int $id): JsonResponse
    {
        $cloned = $this->service->cloneAcademicSubject($id, $request->user()?->id);

        return response()->json([
            'success' => true,
            'message' => __('Academic subject cloned successfully.'),
            'data' => $cloned,
        ]);
    }

    public function downloadAttachment(int $id)
    {
        $attachment = AcademicSubjectAttachment::findOrFail($id);

        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, __('File not found.'));
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }
}
