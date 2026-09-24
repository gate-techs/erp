<?php

namespace Modules\Accounting\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Accounting\Models\LockDate;
use Modules\Accounting\Models\LockException;
use Modules\Accounting\Repositories\Contracts\LockDateRepositoryInterface;

class LockDateService
{
    public const LOCK_TYPES = [
        LockException::TYPE_LOCK_ALL,
        LockException::TYPE_TAX,
        LockException::TYPE_FISCAL_YEAR,
    ];

    //===============================
    public function __construct(
        protected LockDateRepositoryInterface $repository
    ) {}

    //===============================
    public function getLockDates(int $entityId): ?LockDate
    {
        return $this->repository->getByEntity($entityId);
    }

    //===============================
    public function getExceptions(int $entityId, bool $withRevoked = true): Collection
    {
        return $this->repository->getExceptions($entityId, $withRevoked);
    }

    //===============================
    public function saveLockDates(int $entityId, array $data, int $userId): LockDate
    {
        $dates = [];

        foreach (['lock_all_date', 'tax_lock_date', 'fiscal_year_lock_date', 'hard_lock_date'] as $field) {
            $dates[$field] = !empty($data[$field]) ? $data[$field] : null;
        }

        $dates['updated_by'] = $userId;

        return DB::transaction(fn () => $this->repository->saveDates($entityId, $dates));
    }

    //===============================
    public function createException(array $data, int $createdBy): LockException
    {
        if (!in_array($data['lock_type'], self::LOCK_TYPES, true)) {
            throw ValidationException::withMessages(['lock_type' => __('This lock type does not support exceptions.')]);
        }

        $from = Carbon::parse($data['valid_from']);
        $until = Carbon::parse($data['valid_until']);

        if ($until->lessThanOrEqualTo($from)) {
            throw ValidationException::withMessages([
                'valid_until' => __('The exception end must be after the start date.'),
            ]);
        }

        return DB::transaction(fn () => $this->repository->createException([
            'entity_id' => (int) $data['entity_id'],
            'user_id' => (int) $data['user_id'],
            'lock_type' => $data['lock_type'],
            'valid_from' => $from,
            'valid_until' => $until,
            'reason' => $data['reason'],
            'created_by' => $createdBy,
        ]));
    }

    //===============================
    public function revokeException(int $id, int $userId): LockException
    {
        $exception = $this->repository->findException($id);

        if (!$exception) {
            throw ValidationException::withMessages(['exception' => __('Lock exception not found.')]);
        }

        if ($exception->revoked_at) {
            throw ValidationException::withMessages(['exception' => __('This lock exception has already been revoked.')]);
        }

        return DB::transaction(fn () => $this->repository->revokeException($exception, $userId));
    }
}
