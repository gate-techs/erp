<?php

namespace Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Academic\Http\Requests\StoreTeacherGalleryRequest;
use Modules\Academic\Http\Requests\UpdateTeacherGalleryRequest;
use Modules\Academic\Models\TeacherGallery;
use Modules\Academic\Services\TeacherGalleryService;
use Yajra\DataTables\Facades\DataTables;

class TeacherGalleryController extends Controller
{
    //------------------------------------------
    public function __construct(
        protected TeacherGalleryService $service
    ) {}

    //------------------------------------------
    /**
     * Display the teacher galleries management view.
     */
    public function index(Request $request)
    {
        abort_if(! auth()->user()->can('academic.galleries.view'), 403);

        $institutions = \Modules\Nursery\Models\Nursery::with('translations')->get();
        $branches = \Modules\Nursery\Models\NurseryBranch::with('translations')->get();
        $academicStages = \Modules\Academic\Models\AcademicStage::with('translations')->get();
        $academicClasses = $this->service->getClasses();
        $teachers = $this->service->getTeachers();

        return view('academic::galleries.index', compact(
            'institutions',
            'branches',
            'academicStages',
            'academicClasses',
            'teachers'
        ));
    }

    //------------------------------------------
    /**
     * DataTables AJAX endpoint.
     */
    public function data(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.galleries.view'), 403);

        $query = $this->service->getFilteredQuery($request->all());

        $canEdit = auth()->user()->can('academic.galleries.edit');
        $canDelete = auth()->user()->can('academic.galleries.delete');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('record_id', function (TeacherGallery $gallery) {
                return '<span class="fw-bold text-heading">#' . $gallery->id . '</span>';
            })
            ->addColumn('gallery_images', function (TeacherGallery $gallery) {
                $images = $gallery->images;
                if ($images->isEmpty()) {
                    return '<div class="avatar avatar-sm bg-label-secondary rounded d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="icon-base bx bx-image font-18 text-secondary"></i>
                    </div>';
                }

                $html = '<div class="d-inline-flex align-items-center gap-1">';
                foreach ($images->take(3) as $img) {
                    $html .= '<a href="' . e($img->url) . '" target="_blank" class="d-inline-block rounded border overflow-hidden shadow-xs text-decoration-none" style="width: 38px; height: 38px; flex-shrink: 0;">';
                    if ($img->is_video) {
                        $html .= '<div class="position-relative w-100 h-100 bg-dark d-flex align-items-center justify-content-center">
                            <i class="icon-base bx bx-play text-white font-14"></i>
                        </div>';
                    } else {
                        $html .= '<img src="' . e($img->url) . '" alt="img" class="w-100 h-100" style="object-fit: cover;" onerror="this.src=\'data:image/svg+xml;utf8,<svg xmlns=\\\'http://www.w3.org/2000/svg\\\' width=\\\'38\\\' height=\\\'38\\\'><rect width=\\\'38\\\' height=\\\'38\\\' fill=\\\'%23eee\\\'/><text x=\\\'50%\\\' y=\\\'50%\\\' fill=\\\'%23aaa\\\' text-anchor=\\\'middle\\\' dy=\\\'.3em\\\' font-size=\\\'9\\\'>img</text></svg>\';">';
                    }
                    $html .= '</a>';
                }

                if ($images->count() > 3) {
                    $html .= '<span class="badge bg-label-secondary font-11 py-1 px-1 ms-1">+' . ($images->count() - 3) . '</span>';
                }
                $html .= '</div>';

                return $html;
            })
            ->addColumn('student_name', function (TeacherGallery $gallery) {
                $scope = $gallery->sharing_scope;
                if ($scope === 'single' && $gallery->student) {
                    $name = trim($gallery->student->first_name . ' ' . $gallery->student->family_name);
                    return '<span class="badge font-12 py-1 px-3 text-nowrap" style="background-color: #e0f7fa; color: #00838f; border-radius: 6px;">' . e($name) . '</span>';
                }

                if ($scope === 'selected') {
                    $count = $gallery->students->count();
                    $firstStudent = $gallery->students->first();
                    $name = $firstStudent ? trim($firstStudent->first_name . ' ' . $firstStudent->family_name) : '';
                    return '<span class="badge font-12 py-1 px-3 text-nowrap" style="background-color: #e0f7fa; color: #00838f; border-radius: 6px;" title="' . e($name) . '">'
                        . e(mb_strimwidth($name, 0, 16, '...')) . ' <small class="text-muted">(+' . ($count - 1) . ')</small></span>';
                }

                return '<span class="badge font-12 py-1 px-3 text-nowrap" style="background-color: #f1f3f5; color: #495057; border-radius: 6px;"><i class="icon-base bx bx-group me-1"></i>' . e(__('All Class Students')) . '</span>';
            })
            ->addColumn('stage_name', function (TeacherGallery $gallery) {
                $stage = $gallery->classroom?->academicStage;
                $stageName = $stage?->translate(app()->getLocale())?->name ?? $stage?->translate('ar')?->name ?? $stage?->name ?? '-';
                return '<span class="badge font-12 py-1 px-3 text-nowrap" style="background-color: #fff3e0; color: #e65100; border-radius: 6px;">' . e($stageName) . '</span>';
            })
            ->addColumn('class_name', function (TeacherGallery $gallery) {
                $className = $gallery->classroom?->translate(app()->getLocale())?->name ?? $gallery->classroom?->translate('ar')?->name ?? $gallery->classroom?->name ?? '-';
                return '<span class="badge font-12 py-1 px-3 text-nowrap" style="background-color: #e8f5e9; color: #2e7d32; border-radius: 6px;">' . e($className) . '</span>';
            })
            ->addColumn('branch_name', function (TeacherGallery $gallery) {
                $branch = $gallery->classroom?->branch;
                $branchName = $branch?->translate(app()->getLocale())?->name ?? $branch?->translate('ar')?->name ?? $branch?->name ?? '-';
                return '<span class="badge font-12 py-1 px-3 text-nowrap" style="background-color: #ffebee; color: #c62828; border-radius: 6px;">' . e($branchName) . '</span>';
            })
            ->addColumn('added_by', function (TeacherGallery $gallery) {
                $name = '';
                if ($gallery->teacher) {
                    $name = $gallery->teacher->translate(app()->getLocale())?->name ?? $gallery->teacher->translate('ar')?->name ?? $gallery->teacher->name;
                } elseif ($gallery->creator) {
                    $name = $gallery->creator->name;
                } else {
                    $name = __('Admin / Management');
                }
                return '<span class="badge font-12 py-1 px-2 border text-nowrap" style="background-color: #f8f9fa; color: #495057; border-radius: 6px;">
                    <i class="icon-base bx bx-mobile-alt me-1 text-muted font-14"></i>' . e($name) . '
                </span>';
            })
            ->addColumn('institution_name', function (TeacherGallery $gallery) {
                $inst = $gallery->classroom?->institution;
                $instName = $inst?->translate(app()->getLocale())?->name ?? $inst?->translate('ar')?->name ?? $inst?->name ?? '-';
                return '<span class="badge font-12 py-1 px-3 text-nowrap" style="background-color: #e3f2fd; color: #1565c0; border-radius: 6px;">' . e($instName) . '</span>';
            })
            ->addColumn('date_formatted', function (TeacherGallery $gallery) {
                $date = $gallery->date ?: $gallery->created_at;
                if (!$date) {
                    return '<span class="text-muted">-</span>';
                }
                return '<span class="text-nowrap text-secondary font-13">' . e($date->format('d-m-Y')) . '</span>';
            })
            ->addColumn('actions', function (TeacherGallery $gallery) use ($canEdit, $canDelete) {
                $id = $gallery->id;
                $title = e($gallery->title ?: ('#' . $gallery->id));

                $buttons = '';
                if ($canEdit) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-primary edit-gallery-btn" data-id="' . $id . '" title="' . e(__('Edit')) . '"><i class="icon-base bx bx-edit"></i></button>';
                }
                if ($canDelete) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-danger delete-gallery-btn" data-id="' . $id . '" data-title="' . $title . '" title="' . e(__('Delete')) . '"><i class="icon-base bx bx-trash"></i></button>';
                }

                return $buttons ? '<div class="d-inline-flex align-items-center gap-1">' . $buttons . '</div>' : '<span class="text-muted">-</span>';
            })
            ->rawColumns(['record_id', 'gallery_images', 'student_name', 'stage_name', 'class_name', 'branch_name', 'added_by', 'institution_name', 'date_formatted', 'actions'])
            ->make(true);
    }

    //------------------------------------------
    /**
     * Store a newly created gallery.
     */
    public function store(StoreTeacherGalleryRequest $request): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.galleries.create'), 403);

        try {
            $gallery = $this->service->createGallery(
                $request->validated(),
                $request->file('images', [])
            );

            return response()->json([
                'success' => true,
                'message' => __('Gallery created successfully.'),
                'data'    => $gallery,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    //------------------------------------------
    /**
     * Display the specified gallery.
     */
    public function show(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.galleries.view'), 403);

        $gallery = $this->service->find($id);

        if (!$gallery) {
            return response()->json([
                'success' => false,
                'message' => __('Gallery not found.'),
            ], 404);
        }

        $data = [
            'id'             => $gallery->id,
            'teacher_id'     => $gallery->teacher_id,
            'class_room_id'  => $gallery->class_room_id,
            'student_id'     => $gallery->student_id,
            'title'          => $gallery->title,
            'description'    => $gallery->description,
            'date'           => $gallery->date ? $gallery->date->format('Y-m-d') : null,
            'sharing_scope'  => $gallery->sharing_scope,
            'selected_student_ids' => $gallery->students->pluck('id')->toArray(),
            'teacher_name'   => $gallery->teacher ? ($gallery->teacher->translate(app()->getLocale())?->name ?? $gallery->teacher->name) : __('Admin / Management'),
            'class_name'     => $gallery->classroom ? ($gallery->classroom->translate(app()->getLocale())?->name ?? $gallery->classroom->name) : '-',
            'images'         => $gallery->images->map(fn ($img) => [
                'id'         => $img->id,
                'file_name'  => $img->file_name,
                'file_path'  => $img->file_path,
                'url'        => $img->url,
                'file_type'  => $img->file_type,
                'is_video'   => $img->is_video,
                'file_size'  => $img->file_size,
            ]),
        ];

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    //------------------------------------------
    /**
     * Update the specified gallery.
     */
    public function update(UpdateTeacherGalleryRequest $request, int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.galleries.edit'), 403);

        try {
            $updated = $this->service->updateGallery(
                $id,
                $request->validated(),
                $request->file('images', [])
            );

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => __('Gallery not found or could not be updated.'),
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => __('Gallery updated successfully.'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    //------------------------------------------
    /**
     * Delete the specified gallery.
     */
    public function destroy(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.galleries.delete'), 403);

        try {
            $deleted = $this->service->deleteGallery($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => __('Gallery not found.'),
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => __('Gallery deleted successfully.'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    //------------------------------------------
    /**
     * Delete an individual image/media item from a gallery.
     */
    public function deleteImage(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.galleries.delete'), 403);

        try {
            $deleted = $this->service->deleteImage($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => __('Image not found.'),
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => __('Image deleted successfully.'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    //------------------------------------------
    /**
     * Fetch students of a specific classroom.
     */
    public function getClassStudents(int $classroomId): JsonResponse
    {
        abort_if(! auth()->user()->can('academic.galleries.view') && ! auth()->user()->can('academic.galleries.create') && ! auth()->user()->can('academic.galleries.edit'), 403);

        $students = $this->service->getClassStudents($classroomId);

        $formatted = $students->map(function ($student) {
            $name = trim($student->first_name . ' ' . $student->family_name);
            return [
                'id'   => $student->id,
                'name' => $name ?: ('#' . $student->id),
                'code' => $student->student_code ?? '',
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $formatted,
        ]);
    }
}
