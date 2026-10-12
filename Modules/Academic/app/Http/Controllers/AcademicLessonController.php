<?php

namespace Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Modules\Academic\Http\Requests\StoreAcademicLessonRequest;
use Modules\Academic\Http\Requests\UpdateAcademicLessonRequest;
use Modules\Academic\Models\AcademicLesson;
use Modules\Academic\Models\AcademicLessonAttachment;
use Modules\Academic\Models\AcademicStage;
use Modules\Academic\Models\AcademicSubject;
use Modules\Academic\Services\AcademicLessonService;
use Modules\Nursery\Models\Nursery;
use Yajra\DataTables\Facades\DataTables;

class AcademicLessonController extends Controller
{
    public function __construct(
        protected AcademicLessonService $service
    ) {}

    public function index(): View
    {
        $institutions = Nursery::with('translations')->get();
        $academicStages = AcademicStage::with('translations')->get();
        $academicSubjects = AcademicSubject::with('translations')->where('status', AcademicSubject::STATUS_ACTIVE)->get();
        $statuses = AcademicLesson::statuses();

        return view('academic::academic-lessons.index', compact(
            'institutions',
            'academicStages',
            'academicSubjects',
            'statuses'
        ));
    }

    public function data(Request $request): JsonResponse
    {
        return DataTables::eloquent($this->service->getDataTableQuery($request->all()))
            ->addIndexColumn()
            ->addColumn('title', function (AcademicLesson $item) {
                $title = $item->translate(app()->getLocale())?->title
                    ?? $item->translate('ar')?->title
                    ?? $item->translate('en')?->title
                    ?? '-';
                $imageUrl = $item->image_url;

                if ($imageUrl) {
                    $avatar = '<div class="avatar avatar-sm me-2 rounded flex-shrink-0" style="width: 38px; height: 38px;">
                        <img src="' . e($imageUrl) . '" alt="' . e($title) . '" class="w-100 h-100 rounded object-fit-cover border">
                    </div>';
                } else {
                    $avatar = '<div class="avatar avatar-sm me-2 bg-label-info rounded d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                        <i class="icon-base bx bx-book-reader font-20 text-info"></i>
                    </div>';
                }

                $subtitle = $item->translate(app()->getLocale() === 'ar' ? 'en' : 'ar')?->title;
                $subtitleHtml = $subtitle ? '<small class="text-muted d-block text-truncate" style="max-width: 220px;">' . e($subtitle) . '</small>' : '';

                return '<div class="d-flex align-items-center">' . $avatar . '<div><button type="button" class="btn btn-link p-0 text-start fw-semibold text-heading text-decoration-none btn-edit-academic-lesson d-block" data-id="' . $item->id . '" title="' . e(__('Edit')) . '">' . e($title) . '</button>' . $subtitleHtml . '</div></div>';
            })
            ->addColumn('institution_name', function (AcademicLesson $item) {
                return e(
                    $item->institution?->translate(app()->getLocale())?->name
                        ?? $item->institution?->translate('ar')?->name
                        ?? $item->institution?->name
                        ?? '-'
                );
            })
            ->addColumn('stage_name', function (AcademicLesson $item) {
                $stageName = $item->academicStage?->translate(app()->getLocale())?->name
                    ?? $item->academicStage?->translate('ar')?->name
                    ?? $item->academicStage?->translate('en')?->name
                    ?? '-';
                return '<span class="badge bg-label-primary font-11 py-1 px-2">' . e($stageName) . '</span>';
            })
            ->addColumn('subject_name', function (AcademicLesson $item) {
                $subjectName = $item->academicSubject?->translate(app()->getLocale())?->name
                    ?? $item->academicSubject?->translate('ar')?->name
                    ?? $item->academicSubject?->translate('en')?->name
                    ?? '-';
                return '<span class="badge bg-label-secondary font-11 py-1 px-2"><i class="icon-base bx bx-book me-1"></i>' . e($subjectName) . '</span>';
            })
            ->addColumn('dates_formatted', function (AcademicLesson $item) {
                if (!$item->start_date && !$item->end_date) {
                    return '<span class="text-muted">-</span>';
                }
                $start = $item->start_date ? $item->start_date->format('Y-m-d') : '...';
                $end = $item->end_date ? $item->end_date->format('Y-m-d') : '...';
                return '<small class="text-nowrap"><i class="icon-base bx bx-calendar me-1 text-primary"></i>' . e($start) . ' <br><i class="icon-base bx bx-calendar-check me-1 text-success"></i>' . e($end) . '</small>';
            })
            ->addColumn('order_badge', function (AcademicLesson $item) {
                return '<span class="badge bg-label-dark font-monospace px-2 py-1">#' . $item->order . '</span>';
            })
            ->addColumn('attachments_count_badge', function (AcademicLesson $item) {
                $count = $item->attachments_count ?? $item->attachments->count();
                if ($count > 0) {
                    return '<span class="badge bg-label-info"><i class="icon-base bx bx-paperclip me-1"></i>' . $count . '</span>';
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('status_badge', function (AcademicLesson $item) {
                $isActive = $item->isActive();
                $badgeClass = $isActive ? 'bg-label-success' : 'bg-label-danger';
                $label = $isActive ? __('Active') : __('Inactive');

                return '<button type="button" class="btn btn-xs ' . $badgeClass . ' btn-toggle-academic-lesson-status" data-id="' . $item->id . '">'
                    . e($label) .
                '</button>';
            })
            ->addColumn('actions', function (AcademicLesson $item) {
                $title = $item->translate(app()->getLocale())?->title
                    ?? $item->translate('ar')?->title
                    ?? $item->translate('en')?->title;

                $viewTitle = __('View Details');
                $editTitle = __('Edit');
                $deleteTitle = __('Delete');

                return '
                    <div class="d-inline-flex align-items-center gap-1">
                        <button type="button" class="btn btn-icon btn-sm btn-outline-info btn-view-academic-lesson"
                            data-id="' . $item->id . '"
                            title="' . e($viewTitle) . '">
                            <i class="icon-base bx bx-show"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-academic-lesson"
                            data-id="' . $item->id . '"
                            title="' . e($editTitle) . '">
                            <i class="icon-base bx bx-edit"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-academic-lesson"
                            data-id="' . $item->id . '"
                            data-name="' . e($title) . '"
                            title="' . e($deleteTitle) . '">
                            <i class="icon-base bx bx-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['title', 'stage_name', 'subject_name', 'dates_formatted', 'order_badge', 'attachments_count_badge', 'status_badge', 'actions'])
            ->make(true);
    }

    public function store(StoreAcademicLessonRequest $request): JsonResponse
    {
        $lesson = $this->service->createAcademicLesson(
            $request->validated(),
            $request->user()?->id,
            $request->file('attachments', [])
        );

        return response()->json([
            'success' => true,
            'message' => __('Academic lesson created successfully.'),
            'data'    => $lesson,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $lesson = $this->service->getAcademicLesson($id);

        if (!$lesson) {
            return response()->json([
                'success' => false,
                'message' => __('Academic lesson not found.'),
            ], 404);
        }

        $data = $lesson->toArray();
        $data['title_ar'] = $lesson->translate('ar')?->title ?? '';
        $data['title_en'] = $lesson->translate('en')?->title ?? '';
        $data['content_ar'] = $lesson->translate('ar')?->content ?? '';
        $data['content_en'] = $lesson->translate('en')?->content ?? '';
        $data['start_date'] = $lesson->start_date ? $lesson->start_date->format('Y-m-d') : null;
        $data['end_date'] = $lesson->end_date ? $lesson->end_date->format('Y-m-d') : null;
        $data['institution_name'] = $lesson->institution?->name ?? '-';
        $data['stage_name'] = $lesson->academicStage?->translate(app()->getLocale())?->name
            ?? $lesson->academicStage?->translate('ar')?->name
            ?? '-';
        $data['subject_name'] = $lesson->academicSubject?->translate(app()->getLocale())?->name
            ?? $lesson->academicSubject?->translate('ar')?->name
            ?? '-';

        $data['attachments'] = $lesson->attachments->map(fn ($att) => [
            'id'             => $att->id,
            'file_name'      => $att->file_name,
            'file_url'       => $att->file_url,
            'formatted_size' => $att->formatted_size,
            'file_type'      => $att->file_type,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    public function update(UpdateAcademicLessonRequest $request, int $id): JsonResponse
    {
        $lesson = $this->service->updateAcademicLesson(
            $id,
            $request->validated(),
            $request->file('attachments', [])
        );

        return response()->json([
            'success' => true,
            'message' => __('Academic lesson updated successfully.'),
            'data'    => $lesson,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->deleteAcademicLesson($id);

        return response()->json([
            'success' => true,
            'message' => __('Academic lesson deleted successfully.'),
        ]);
    }

    public function toggleStatus(int $id): JsonResponse
    {
        $lesson = $this->service->toggleStatus($id);

        return response()->json([
            'success' => true,
            'message' => __('Academic lesson status updated successfully.'),
            'data'    => [
                'id'        => $lesson->id,
                'status'    => $lesson->status,
                'is_active' => $lesson->isActive(),
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

    public function downloadAttachment(int $id)
    {
        $attachment = AcademicLessonAttachment::findOrFail($id);

        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, __('File not found.'));
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }

    public function getStages(int $institutionId): JsonResponse
    {
        $stages = AcademicStage::where('institution_id', $institutionId)
            ->where('status', AcademicStage::STATUS_ACTIVE)
            ->with('translations')
            ->get()
            ->map(fn ($stage) => [
                'id'   => $stage->id,
                'name' => $stage->translate(app()->getLocale())?->name ?? $stage->translate('ar')?->name ?? $stage->translate('en')?->name ?? $stage->code,
            ]);

        return response()->json([
            'success' => true,
            'data'    => $stages,
        ]);
    }

    public function getSubjects(Request $request): JsonResponse
    {
        $query = AcademicSubject::query()->where('status', AcademicSubject::STATUS_ACTIVE);

        if ($request->has('institution_id')) {
            $query->where('institution_id', $request->institution_id);
        }

        if ($request->has('stage_id')) {
            $query->whereHas('academicStages', function ($q) use ($request) {
                $q->where('academic_stages.id', $request->stage_id);
            });
        }

        $subjects = $query->with('translations')->get()->map(fn ($subject) => [
            'id'   => $subject->id,
            'name' => $subject->translate(app()->getLocale())?->name ?? $subject->translate('ar')?->name ?? $subject->translate('en')?->name,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $subjects,
        ]);
    }
}
