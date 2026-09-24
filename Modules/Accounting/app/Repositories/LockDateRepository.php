<?php

namespace Modules\Accounting\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Accounting\Models\LockDate;
use Modules\Accounting\Models\LockException;
use Modules\Accounting\Repositories\Contracts\LockDateRepositoryInterface;

class LockDateRepository implements LockDateRepositoryInterface
{
    //===============================
    public function getByEntity(int $entityId): ?LockDate
    {
        return LockDate::with(['entity.translations', 'updatedBy'])->where('entity_id', $entityId)->first();
    }

    //===============================
    public function saveDates(int $entityId, array $data): LockDate
    {
        return LockDate::updateOrCreate(
            ['entity_id' => $entityId],
            $data
        )->load(['entity.translations', 'updatedBy']);
    }

    //===============================
    public function getExceptions(int $entityId, bool $withRevoked = true): Collection
    {
        return LockException::with(['user', 'creator', 'revoker'])
            ->where('entity_id', $entityId)
            ->when(!$withRevoked, fn ($query) => $query->whereNull('revoked_at'))
            ->latest('id')
            ->get();
    }

    //===============================
    public function createException(array $data): LockException
    {
        return LockException::create($data)->load(['user', 'creator']);
    }

    //===============================
    public function findException(int $id): ?LockException
    {
        return LockException::find($id);
    }

    //===============================
    public function revokeException(int|LockException $exception, int $userId): LockException
    {
        $model = $exception instanceof LockException ? $exception : LockException::findOrFail($exception);
        $model->update([
            'revoked_at' => now(),
            'revoked_by' => $userId,
        ]);

        return $model->fresh(['user', 'creator', 'revoker']);
    }
}
