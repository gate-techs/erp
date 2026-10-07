<?php

namespace Modules\Academic\Services;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Academic\Models\AcademicLesson;
use Modules\Academic\Models\AcademicLessonAttachment;
use Modules\Academic\Repositories\Contracts\AcademicLessonRepositoryInterface;

class AcademicLessonService
{
    public function __construct(
        protected AcademicLessonRepositoryInterface $repository,
        protected DatabaseManager $database
    ) {}

    public function getAcademicLessons(array $filters = []): Collection
    {
        return $this->repository->getAll($filters);
    }

    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->repository->getDataTableQuery($filters);
    }

    public function getAcademicLesson(int $id): ?AcademicLesson
    {
        return $this->repository->findById($id);
    }

    public function createAcademicLesson(array $data, ?int $userId = null, array $files = []): AcademicLesson
    {
        return $this->database->transaction(function () use ($data, $userId, $files) {
            unset($data['attachments']);

            $data = $this->prepareCreateData($data, $userId);
            $preparedData = $this->prepareTranslationData($data);

            $lesson = $this->repository->create($preparedData);

            if (!empty($files)) {
                $this->storeAttachments($lesson, $files, $userId);
            }

            return $lesson->fresh(['translations', 'institution', 'academicStage', 'academicSubject', 'attachments', 'creator']);
        });
    }

    public function updateAcademicLesson(int $id, array $data, array $files = []): AcademicLesson
    {
        $lesson = $this->findOrFail($id);

        return $this->database->transaction(function () use ($lesson, $data, $files) {
            unset($data['attachments']);

            $data = $this->handleImageUpload($lesson, $data);
            $preparedData = $this->prepareTranslationData($data);

            $updatedLesson = $this->repository->update($lesson, $preparedData);

            if (!empty($files)) {
                $this->storeAttachments($updatedLesson, $files, auth()->id());
            }

            return $updatedLesson->fresh(['translations', 'institution', 'academicStage', 'academicSubject', 'attachments', 'creator']);
        });
    }

    public function deleteAcademicLesson(int $id): bool
    {
        $lesson = $this->findOrFail($id);

        if ($lesson->image && !str_starts_with($lesson->image, 'assets/')) {
            Storage::disk('public')->delete($lesson->image);
        }

        foreach ($lesson->attachments as $attachment) {
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }
        }

        return $this->repository->delete($lesson);
    }

    public function toggleStatus(int $id): AcademicLesson
    {
        $lesson = $this->findOrFail($id);
        $nextStatus = $lesson->isActive()
            ? AcademicLesson::STATUS_INACTIVE
            : AcademicLesson::STATUS_ACTIVE;

        return $this->repository->update($lesson, [
            'status' => $nextStatus,
        ]);
    }

    public function updateOrder(int $id, int $order): AcademicLesson
    {
        $lesson = $this->findOrFail($id);

        return $this->repository->update($lesson, [
            'order' => $order,
        ]);
    }

    public function deleteAttachment(int $attachmentId): bool
    {
        $attachment = AcademicLessonAttachment::findOrFail($attachmentId);

        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        return (bool) $attachment->delete();
    }

    public function storeAttachments(AcademicLesson $lesson, array $files, ?int $userId = null): void
    {
        foreach ($files as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = time() . '_' . uniqid() . '.' . $extension;
                $filePath = $file->storeAs('academic-lessons/attachments/' . $lesson->id, $fileName, 'public');

                AcademicLessonAttachment::create([
                    'academic_lesson_id' => $lesson->id,
                    'file_name'          => $originalName,
                    'file_path'          => $filePath,
                    'file_type'          => $file->getClientMimeType(),
                    'file_size'          => $file->getSize(),
                    'created_by'         => $userId ?? auth()->id(),
                ]);
            }
        }
    }

    protected function prepareCreateData(array $data, ?int $userId = null): array
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $data['image']->store('academic-lessons', 'public');
        }

        $data['created_by'] = $userId ?? auth()->id();
        $data['status'] = $data['status'] ?? AcademicLesson::STATUS_ACTIVE;
        $data['order'] = isset($data['order']) ? (int) $data['order'] : 0;

        return $data;
    }

    protected function handleImageUpload(AcademicLesson $lesson, array $data): array
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if ($lesson->image && !str_starts_with($lesson->image, 'assets/')) {
                Storage::disk('public')->delete($lesson->image);
            }
            $data['image'] = $data['image']->store('academic-lessons', 'public');
        } elseif (array_key_exists('image', $data) && empty($data['image'])) {
            unset($data['image']);
        }

        return $data;
    }

    protected function prepareTranslationData(array $data): array
    {
        foreach (['ar', 'en'] as $locale) {
            if (isset($data["title_{$locale}"])) {
                $data[$locale]['title'] = trim((string) $data["title_{$locale}"]);
                unset($data["title_{$locale}"]);
            }

            if (array_key_exists("content_{$locale}", $data)) {
                $data[$locale]['content'] = $data["content_{$locale}"] !== null
                    ? trim((string) $data["content_{$locale}"])
                    : null;
                unset($data["content_{$locale}"]);
            }
        }

        return $data;
    }

    protected function findOrFail(int $id): AcademicLesson
    {
        $lesson = $this->repository->findById($id);

        if (!$lesson) {
            abort(404, __('Academic lesson not found.'));
        }

        return $lesson;
    }
}
