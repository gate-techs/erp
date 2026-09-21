<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Models\JobOffer;

interface JobOfferRepositoryInterface
{
    public function getDataTableQuery(array $filters = []): Builder;

    public function findById(int $id): ?JobOffer;

    public function create(array $data): JobOffer;

    public function update(JobOffer $offer, array $data): bool;

    public function delete(JobOffer $offer): bool;
}
