<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Models\Onboarding;

interface OnboardingRepositoryInterface
{
    public function getDataTableQuery(array $filters = []): Builder;

    public function findById(int $id): ?Onboarding;

    public function create(array $data): Onboarding;

    public function update(Onboarding $onboarding, array $data): bool;

    public function delete(Onboarding $onboarding): bool;
}
