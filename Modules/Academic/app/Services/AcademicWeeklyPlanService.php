<?php

namespace Modules\Academic\Services;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Academic\Models\AcademicWeeklyPlan;
use Modules\Academic\Models\AcademicWeeklyPlanAttachment;
use Modules\Academic\Repositories\Contracts\AcademicWeeklyPlanRepositoryInterface;

class AcademicWeeklyPlanService
{
    public function __construct(
        protected AcademicWeeklyPlanRepositoryInterface $repository,
        protected DatabaseManager $database
    ) {}

    public function getAcademicWeeklyPlans(array $filters = []): Collection
    {
        return $this->repository->all($filters);
    }

    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->repository->getQuery($filters);
    }

    public function getAcademicWeeklyPlan(int $id): ?AcademicWeeklyPlan
    {
        return $this->repository->findById($id);
    }

    public function createAcademicWeeklyPlan(array $data, ?int $userId = null, array $files = []): AcademicWeeklyPlan
    {
        return $this->database->transaction(function () use ($data, $userId, $files) {
            unset($data['attachments']);

            $data['created_by'] = $userId ?? auth()->id();
            $data['status'] = $data['status'] ?? AcademicWeeklyPlan::STATUS_ACTIVE;

            $preparedData = $this->prepareTranslationData($data);
            $plan = $this->repository->create($preparedData);

            if (!empty($files)) {
                $this->storeAttachments($plan, $files, $userId);
            }

            return $plan->fresh([
                'translations',
                'institution',
                'branch',
                'academicStage',
                'academicClass',
                'attachments',
                'creator',
            ]);
        });
    }

    public function updateAcademicWeeklyPlan(int $id, array $data, array $files = []): AcademicWeeklyPlan
    {
        $plan = $this->findOrFail($id);

        return $this->database->transaction(function () use ($plan, $data, $files) {
            unset($data['attachments']);

            $preparedData = $this->prepareTranslationData($data);
            $updatedPlan = $this->repository->update($plan->id, $preparedData);

            if (!empty($files)) {
                $this->storeAttachments($updatedPlan, $files, auth()->id());
            }

            return $updatedPlan->fresh([
                'translations',
                'institution',
                'branch',
                'academicStage',
                'academicClass',
                'attachments',
                'creator',
            ]);
        });
    }

    public function deleteAcademicWeeklyPlan(int $id): bool
    {
        $plan = $this->findOrFail($id);

        foreach ($plan->attachments as $attachment) {
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }
        }

        return $this->repository->delete($plan->id);
    }

    public function toggleStatus(int $id): AcademicWeeklyPlan
    {
        return $this->repository->toggleStatus($id);
    }

    public function deleteAttachment(int $attachmentId): bool
    {
        $attachment = AcademicWeeklyPlanAttachment::findOrFail($attachmentId);

        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        return (bool) $attachment->delete();
    }

    public function storeAttachments(AcademicWeeklyPlan $plan, array $files, ?int $userId = null): void
    {
        foreach ($files as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = time() . '_' . uniqid() . '.' . $extension;
                $filePath = $file->storeAs('academic-weekly-plans/attachments/' . $plan->id, $fileName, 'public');

                AcademicWeeklyPlanAttachment::create([
                    'academic_weekly_plan_id' => $plan->id,
                    'file_name'               => $originalName,
                    'file_path'               => $filePath,
                    'file_type'               => $file->getClientMimeType(),
                    'file_size'               => $file->getSize(),
                    'created_by'              => $userId ?? auth()->id(),
                ]);
            }
        }
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

    protected function findOrFail(int $id): AcademicWeeklyPlan
    {
        $plan = $this->repository->findById($id);

        if (!$plan) {
            abort(404, __('Academic weekly plan not found.'));
        }

        return $plan;
    }
}
