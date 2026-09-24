<?php

namespace Modules\Accounting\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Modules\Accounting\Models\LockDate;
use Modules\Accounting\Models\LockException;

interface LockDateRepositoryInterface
{
    //===============================
    public function getByEntity(int $entityId): ?LockDate;

    //===============================
    public function saveDates(int $entityId, array $data): LockDate;

    //===============================
    public function getExceptions(int $entityId, bool $withRevoked = true): Collection;

    //===============================
    public function createException(array $data): LockException;

    //===============================
    public function findException(int $id): ?LockException;

    //===============================
    public function revokeException(int|LockException $exception, int $userId): LockException;
}
