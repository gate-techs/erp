<?php

namespace Modules\Academic\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Academic\Models\AcademicMonthlyPlan;
use Modules\Academic\Models\AcademicMonthlyPlanAttachment;
use Modules\Academic\Repositories\Contracts\AcademicMonthlyPlanRepositoryInterface;

class AcademicMonthlyPlanService
{
    public function __construct(
        protected AcademicMonthlyPlanRepositoryInterface $repository
    ) {}

    public function getFilteredQuery(array $filters = []): Builder
    {
        return $this->repository->getFilteredQuery($filters);
    }

    public function all(array $filters = []): Collection
    {
        return $this->repository->all($filters);
    }

    public function findById(int $id, array $with = []): ?AcademicMonthlyPlan
    {
        return $this->repository->findById($id, $with);
    }

    public function create(array $data, ?UploadedFile $image = null, array $attachments = []): AcademicMonthlyPlan
    {
        return DB::transaction(function () use ($data, $image, $attachments) {
            if ($image) {
                $data['image'] = $image->store('academic/monthly-plans/images', 'public');
            }

            $payload = $this->formatTranslationData($data);
            $plan = $this->repository->create($payload);

            if (! empty($attachments)) {
                $this->uploadAttachments($plan->id, $attachments);
            }

            return $plan->load(['translations', 'classes', 'attachments']);
        });
    }

    public function update(int $id, array $data, ?UploadedFile $image = null, array $attachments = []): AcademicMonthlyPlan
    {
        return DB::transaction(function () use ($id, $data, $image, $attachments) {
            $plan = $this->findById($id);

            if ($image) {
                if ($plan->image && Storage::disk('public')->exists($plan->image)) {
                    Storage::disk('public')->delete($plan->image);
                }
                $data['image'] = $image->store('academic/monthly-plans/images', 'public');
            }

            $payload = $this->formatTranslationData($data);
            $updatedPlan = $this->repository->update($id, $payload);

            if (! empty($attachments)) {
                $this->uploadAttachments($updatedPlan->id, $attachments);
            }

            return $updatedPlan->load(['translations', 'classes', 'attachments']);
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $plan = $this->findById($id);
            if (! $plan) {
                return false;
            }

            // Soft-delete
            return $this->repository->delete($id);
        });
    }

    public function toggleStatus(int $id): AcademicMonthlyPlan
    {
        return $this->repository->toggleStatus($id);
    }

    public function uploadAttachments(int $planId, array $files): void
    {
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $path = $file->store('academic/monthly-plans/attachments', 'public');
                $this->repository->addAttachment($planId, [
                    'file_path'  => $path,
                    'file_name'  => $file->getClientOriginalName(),
                    'file_type'  => $file->getClientMimeType(),
                    'file_size'  => $file->getSize(),
                    'created_by' => auth()->id(),
                ]);
            }
        }
    }

    public function deleteAttachment(int $attachmentId): bool
    {
        $attachment = $this->repository->findAttachment($attachmentId);
        if ($attachment) {
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            return $this->repository->deleteAttachment($attachmentId);
        }

        return false;
    }

    public function findAttachment(int $attachmentId): ?AcademicMonthlyPlanAttachment
    {
        return $this->repository->findAttachment($attachmentId);
    }

    protected function formatTranslationData(array $data): array
    {
        $titleAr = $data['title_ar'] ?? null;
        $titleEn = $data['title_en'] ?? $titleAr;
        $descAr = $data['description_ar'] ?? $data['content_ar'] ?? null;
        $descEn = $data['description_en'] ?? $data['content_en'] ?? $descAr;

        $translations = [
            'ar' => [
                'title'       => $titleAr,
                'description' => $descAr,
            ],
            'en' => [
                'title'       => $titleEn,
                'description' => $descEn,
            ],
        ];

        unset(
            $data['title_ar'],
            $data['title_en'],
            $data['description_ar'],
            $data['description_en'],
            $data['content_ar'],
            $data['content_en']
        );

        return array_merge($data, $translations);
    }
}
