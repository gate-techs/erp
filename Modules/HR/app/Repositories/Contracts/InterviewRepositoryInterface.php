<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Models\Interview;

interface InterviewRepositoryInterface
{
    /**
     * Get DataTable query with applied filters.
     */
    public function getDataTableQuery(array $filters = []): Builder;

    /**
     * Get upcoming scheduled interviews.
     */
    public function getUpcomingInterviews(int $limit = 5): array|\Illuminate\Support\Collection;

    /**
     * Get interview KPIs.
     */
    public function getInterviewKpis(?int $jobOpeningId = null): array;

    /**
     * Find interview with full relations.
     */
    public function findWithRelations(int $id): ?Interview;
}
