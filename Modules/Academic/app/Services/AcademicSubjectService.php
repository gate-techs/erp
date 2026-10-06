<?php

namespace Modules\Academic\Services;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Academic\Models\AcademicSubject;
use Modules\Academic\Models\AcademicSubjectAttachment;
use Modules\Academic\Repositories\Contracts\AcademicSubjectRepositoryInterface;

class AcademicSubjectService
{
    public function __construct(
        protected AcademicSubjectRepositoryInterface $repository,
        protected DatabaseManager $database
    ) {}

    public function getAcademicSubjects(array $filters = []): Collection
    {
        return $this->repository->getAll($filters);
    }

    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->repository->getDataTableQuery($filters);
    }

    public function getAcademicSubject(int $id): ?AcademicSubject
    {
        return $this->repository->findById($id);
    }

    public function createAcademicSubject(array $data, ?int $userId = null, array $files = []): AcademicSubject
    {
        return $this->database->transaction(function () use ($data, $userId, $files) {
            $stageIds = $data['academic_stages'] ?? [];
            unset($data['academic_stages'], $data['attachments']);

            $data = $this->prepareCreateData($data, $userId);
            $preparedData = $this->prepareTranslationData($data);

            $subject = $this->repository->create($preparedData);

            if (!empty($stageIds)) {
                $this->repository->syncStages($subject, $stageIds);
            }

            if (!empty($files)) {
                $this->storeAttachments($subject, $files, $userId);
            }

            return $subject->fresh(['translations', 'institution', 'academicYear', 'academicStages', 'attachments']);
        });
    }

    public function updateAcademicSubject(int $id, array $data, array $files = []): AcademicSubject
    {
        $subject = $this->findOrFail($id);

        return $this->database->transaction(function () use ($subject, $data, $files) {
            $stageIds = $data['academic_stages'] ?? null;
            unset($data['academic_stages'], $data['attachments']);

            $data = $this->handleImageUpload($subject, $data);
            $preparedData = $this->prepareTranslationData($data);

            $updatedSubject = $this->repository->update($subject, $preparedData);

            if ($stageIds !== null) {
                $this->repository->syncStages($updatedSubject, $stageIds);
            }

            if (!empty($files)) {
                $this->storeAttachments($updatedSubject, $files, auth()->id());
            }

            return $updatedSubject->fresh(['translations', 'institution', 'academicYear', 'academicStages', 'attachments']);
        });
    }

    public function deleteAcademicSubject(int $id): bool
    {
        $subject = $this->findOrFail($id);

        return $this->repository->delete($subject);
    }

    public function toggleStatus(int $id): AcademicSubject
    {
        $subject = $this->findOrFail($id);
        $nextStatus = $subject->isActive()
            ? AcademicSubject::STATUS_INACTIVE
            : AcademicSubject::STATUS_ACTIVE;

        return $this->repository->update($subject, [
            'status' => $nextStatus,
        ]);
    }

    public function cloneAcademicSubject(int $id, ?int $userId = null): AcademicSubject
    {
        $original = $this->findOrFail($id);
        $original->load(['translations', 'academicStages', 'attachments']);

        return $this->database->transaction(function () use ($original, $userId) {
            // 1. Prepare base data
            $newSubject = AcademicSubject::create([
                'institution_id' => $original->institution_id,
                'academic_year_id' => $original->academic_year_id,
                'image' => $original->image,
                'status' => $original->status,
                'created_by' => $userId ?? auth()->id(),
            ]);

            // 2. Clone translations with (Copy) suffix
            $locales = ['ar' => ' (نسخة)', 'en' => ' (Copy)'];
            foreach ($original->translations as $translation) {
                $suffix = $locales[$translation->locale] ?? ' (Copy)';
                $newSubject->translateOrNew($translation->locale)->name = $translation->name . $suffix;
                $newSubject->translateOrNew($translation->locale)->description = $translation->description;
            }
            $newSubject->save();

            // 3. Clone stages
            $stageIds = $original->academicStages->pluck('id')->toArray();
            if (!empty($stageIds)) {
                $this->repository->syncStages($newSubject, $stageIds);
            }

            // 4. Clone attachments (copy files to new path)
            foreach ($original->attachments as $attachment) {
                $newFilePath = $attachment->file_path;
                if (Storage::disk('public')->exists($attachment->file_path)) {
                    $extension = pathinfo($attachment->file_path, PATHINFO_EXTENSION);
                    $newFileName = time() . '_' . uniqid() . '.' . $extension;
                    $newFilePath = 'academic-subjects/attachments/' . $newSubject->id . '/' . $newFileName;
                    Storage::disk('public')->copy($attachment->file_path, $newFilePath);
                }

                AcademicSubjectAttachment::create([
                    'academic_subject_id' => $newSubject->id,
                    'file_name' => $attachment->file_name,
                    'file_path' => $newFilePath,
                    'file_type' => $attachment->file_type,
                    'file_size' => $attachment->file_size,
                    'created_by' => $userId ?? auth()->id(),
                ]);
            }

            return $newSubject->fresh(['translations', 'institution', 'academicYear', 'academicStages', 'attachments']);
        });
    }

    public function deleteAttachment(int $attachmentId): bool
    {
        $attachment = AcademicSubjectAttachment::findOrFail($attachmentId);
        
        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        return (bool) $attachment->delete();
    }

    public function storeAttachments(AcademicSubject $subject, array $files, ?int $userId = null): void
    {
        foreach ($files as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = time() . '_' . uniqid() . '.' . $extension;
                $filePath = $file->storeAs('academic-subjects/attachments/' . $subject->id, $fileName, 'public');

                AcademicSubjectAttachment::create([
                    'academic_subject_id' => $subject->id,
                    'file_name' => $originalName,
                    'file_path' => $filePath,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'created_by' => $userId ?? auth()->id(),
                ]);
            }
        }
    }

    protected function prepareCreateData(array $data, ?int $userId = null): array
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $data['image']->store('academic-subjects', 'public');
        }

        $data['created_by'] = $userId ?? auth()->id();
        $data['status'] = $data['status'] ?? AcademicSubject::STATUS_ACTIVE;

        return $data;
    }

    protected function handleImageUpload(AcademicSubject $subject, array $data): array
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if ($subject->image && !str_starts_with($subject->image, 'assets/')) {
                Storage::disk('public')->delete($subject->image);
            }
            $data['image'] = $data['image']->store('academic-subjects', 'public');
        } elseif (array_key_exists('image', $data) && empty($data['image'])) {
            unset($data['image']);
        }

        return $data;
    }

    protected function prepareTranslationData(array $data): array
    {
        foreach (['ar', 'en'] as $locale) {
            if (isset($data["name_{$locale}"])) {
                $data[$locale]['name'] = trim((string) $data["name_{$locale}"]);
                unset($data["name_{$locale}"]);
            }

            if (array_key_exists("description_{$locale}", $data)) {
                $data[$locale]['description'] = $data["description_{$locale}"] !== null
                    ? trim((string) $data["description_{$locale}"])
                    : null;
                unset($data["description_{$locale}"]);
            }
        }

        return $data;
    }

    protected function findOrFail(int $id): AcademicSubject
    {
        $subject = $this->repository->findById($id);

        if (!$subject) {
            abort(404, __('Academic subject not found.'));
        }

        return $subject;
    }
}
