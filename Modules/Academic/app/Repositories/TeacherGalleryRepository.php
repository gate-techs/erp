<?php

namespace Modules\Academic\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Academic\Models\AcademicClass;
use Modules\Academic\Models\AcademicStudent;
use Modules\Academic\Models\TeacherGallery;
use Modules\Academic\Models\TeacherGalleryImage;
use Modules\Academic\Repositories\Contracts\TeacherGalleryRepositoryInterface;
use Modules\HR\Models\Employee;

class TeacherGalleryRepository implements TeacherGalleryRepositoryInterface
{
    //------------------------------------------
    public function getFilteredQuery(array $filters = []): Builder
    {
        $query = TeacherGallery::with([
            'teacher.user',
            'classroom.academicStage',
            'classroom.branch',
            'classroom.institution',
            'student',
            'students',
            'images',
            'creator',
        ]);

        // Filter by Institution
        if (! empty($filters['institution_id'])) {
            $query->whereHas('classroom', fn ($q) => $q->where('institution_id', $filters['institution_id']));
        }

        // Filter by Branch
        if (! empty($filters['branch_id'])) {
            $query->whereHas('classroom', fn ($q) => $q->where('branch_id', $filters['branch_id']));
        }

        // Filter by Academic Stage
        if (! empty($filters['academic_stage_id'])) {
            $query->whereHas('classroom', fn ($q) => $q->where('academic_stage_id', $filters['academic_stage_id']));
        }

        // Filter by Classroom
        if (! empty($filters['class_room_id']) || ! empty($filters['classroomId'])) {
            $classId = $filters['class_room_id'] ?? $filters['classroomId'];
            $query->where('class_room_id', $classId);
        }

        // Filter by Teacher
        if (! empty($filters['teacher_id']) || ! empty($filters['teacherId'])) {
            $teacherId = $filters['teacher_id'] ?? $filters['teacherId'];
            $query->where('teacher_id', $teacherId);
        }

        // Filter by Student
        if (! empty($filters['student_id']) || ! empty($filters['studentId'])) {
            $studentId = (int) ($filters['student_id'] ?? $filters['studentId']);
            $query->where(function (Builder $q) use ($studentId) {
                $q->where('student_id', $studentId)
                    ->orWhereHas('students', function (Builder $sq) use ($studentId) {
                        $sq->where('academic_students.id', $studentId);
                    });
            });
        }

        // Filter by Sharing Scope
        if (! empty($filters['sharing_scope'])) {
            if ($filters['sharing_scope'] === 'class') {
                $query->whereNull('student_id')->whereDoesntHave('students');
            } elseif ($filters['sharing_scope'] === 'single') {
                $query->where(function (Builder $q) {
                    $q->whereNotNull('student_id')
                        ->orWhereHas('students', null, '=', 1);
                });
            } elseif ($filters['sharing_scope'] === 'selected') {
                $query->has('students', '>', 1);
            }
        }

        // Filter by Date
        if (! empty($filters['date'])) {
            $query->whereDate('date', $filters['date']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('date', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('date', '<=', $filters['date_to']);
        }

        // Search Keyword
        if (! empty($filters['search'])) {
            $search = is_array($filters['search']) ? trim($filters['search']['value'] ?? '') : trim((string) $filters['search']);
            if ($search !== '') {
                $query->where(function (Builder $q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('classroom.translations', function (Builder $cq) use ($search) {
                            $cq->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('student', function (Builder $sq) use ($search) {
                            $sq->where('first_name', 'like', "%{$search}%")
                                ->orWhere('family_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('students', function (Builder $sq) use ($search) {
                            $sq->where('first_name', 'like', "%{$search}%")
                                ->orWhere('family_name', 'like', "%{$search}%");
                        });
                });
            }
        }

        return $query->latest('date')->latest('id');
    }

    //------------------------------------------
    public function find(int $id): ?TeacherGallery
    {
        return TeacherGallery::with([
            'teacher.user',
            'classroom.stage',
            'student',
            'students',
            'images',
            'creator',
        ])->find($id);
    }

    //------------------------------------------
    public function createGallery(array $data, array $imageFiles = []): TeacherGallery
    {
        return DB::transaction(function () use ($data, $imageFiles) {
            $gallery = TeacherGallery::create([
                'teacher_id'    => $data['teacher_id'] ?? ($data['teacherId'] ?? null),
                'class_room_id' => $data['class_room_id'] ?? $data['classroomId'],
                'student_id'    => $data['student_id'] ?? ($data['studentId'] ?? null),
                'title'         => $data['title'] ?? null,
                'description'   => $data['description'] ?? null,
                'date'          => $data['date'] ?? now()->toDateString(),
                'created_by'    => auth()->id(),
            ]);

            // Sync Students list
            $studentIds = $data['student_ids'] ?? ($data['students'] ?? []);
            if (! empty($studentIds) && is_array($studentIds)) {
                $gallery->students()->sync($studentIds);

                // For backward compatibility, if exactly one student selected and student_id is empty
                if (count($studentIds) === 1 && empty($gallery->student_id)) {
                    $gallery->update(['student_id' => $studentIds[0]]);
                }
            } elseif (! empty($gallery->student_id)) {
                // If single student provided, sync in pivot as well for consistency
                $gallery->students()->sync([$gallery->student_id]);
            }

            // Upload and attach images
            if (! empty($imageFiles)) {
                $this->uploadAndAttachFiles($gallery, $imageFiles);
            }

            return $gallery->load(['students', 'images']);
        });
    }

    //------------------------------------------
    public function updateGallery(int $id, array $data, array $imageFiles = []): bool
    {
        return DB::transaction(function () use ($id, $data, $imageFiles) {
            $gallery = TeacherGallery::findOrFail($id);

            $updatePayload = [
                'class_room_id' => $data['class_room_id'] ?? ($data['classroomId'] ?? $gallery->class_room_id),
                'title'         => $data['title'] ?? $gallery->title,
                'description'   => $data['description'] ?? $gallery->description,
                'updated_by'    => auth()->id(),
            ];

            if (array_key_exists('teacher_id', $data) || array_key_exists('teacherId', $data)) {
                $updatePayload['teacher_id'] = $data['teacher_id'] ?? ($data['teacherId'] ?? null);
            }

            if (array_key_exists('date', $data)) {
                $updatePayload['date'] = $data['date'];
            }

            if (array_key_exists('student_id', $data) || array_key_exists('studentId', $data)) {
                $updatePayload['student_id'] = $data['student_id'] ?? ($data['studentId'] ?? null);
            }

            $gallery->update($updatePayload);

            // Sync Students list
            if (isset($data['student_ids']) || isset($data['students'])) {
                $studentIds = $data['student_ids'] ?? ($data['students'] ?? []);
                $gallery->students()->sync($studentIds);

                if (count($studentIds) === 1 && empty($gallery->student_id)) {
                    $gallery->update(['student_id' => $studentIds[0]]);
                } elseif (empty($studentIds)) {
                    $gallery->update(['student_id' => null]);
                }
            }

            // Upload additional images if any
            if (! empty($imageFiles)) {
                $this->uploadAndAttachFiles($gallery, $imageFiles);
            }

            return true;
        });
    }

    //------------------------------------------
    public function deleteGallery(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $gallery = TeacherGallery::with('images')->findOrFail($id);

            // Delete physical files
            foreach ($gallery->images as $image) {
                if (Storage::disk('public')->exists($image->file_path)) {
                    Storage::disk('public')->delete($image->file_path);
                }
            }

            $gallery->images()->delete();
            $gallery->students()->detach();
            $gallery->update(['deleted_by' => auth()->id()]);

            return (bool) $gallery->delete();
        });
    }

    //------------------------------------------
    public function deleteImage(int $imageId): bool
    {
        $image = TeacherGalleryImage::find($imageId);
        if (! $image) {
            return false;
        }

        if (Storage::disk('public')->exists($image->file_path)) {
            Storage::disk('public')->delete($image->file_path);
        }

        return (bool) $image->delete();
    }

    //------------------------------------------
    public function getStudentGalleries(int $studentId): Collection
    {
        return TeacherGallery::with(['images', 'classroom', 'teacher.user'])
            ->forStudent($studentId)
            ->latest('date')
            ->get();
    }

    //------------------------------------------
    public function getClassStudents(int $classroomId): Collection
    {
        return AcademicStudent::with('translations')
            ->where('academic_class_id', $classroomId)
            ->where('status', AcademicStudent::STATUS_ACTIVE)
            ->orderBy('id', 'asc')
            ->get();
    }

    //------------------------------------------
    public function getTeachers(): Collection
    {
        return Employee::where('is_teacher', 1)
            ->where('status', 'active')
            ->orderBy('id', 'asc')
            ->get();
    }

    //------------------------------------------
    public function getClasses(): Collection
    {
        return AcademicClass::with(['academicStage', 'translations'])
            ->orderBy('id', 'asc')
            ->get();
    }

    //------------------------------------------
    protected function uploadAndAttachFiles(TeacherGallery $gallery, array $files): void
    {
        $subFolder = 'uploads/teacher_galleries/' . date('Y/m');

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs($subFolder, $fileName, 'public');

                $mimeType = $file->getClientMimeType();
                $fileType = str_starts_with($mimeType, 'video/') ? 'video' : 'image';

                TeacherGalleryImage::create([
                    'teacher_gallery_id' => $gallery->id,
                    'file_path'          => $filePath,
                    'file_name'          => $file->getClientOriginalName(),
                    'file_type'          => $fileType,
                    'file_size'          => $file->getSize(),
                    'created_by'         => auth()->id(),
                ]);
            }
        }
    }
}
