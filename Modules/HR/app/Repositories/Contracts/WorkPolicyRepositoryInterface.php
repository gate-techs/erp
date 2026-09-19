<?php

namespace Modules\HR\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface WorkPolicyRepositoryInterface
{
    /**
     * Get all policy records grouped by their group identifier.
     */
    public function getGroupedPolicies(): array;

    /**
     * Get a single policy value.
     */
    public function getPolicyValue(string $key, mixed $default = null): mixed;

    /**
     * Update or set a batch of policies.
     */
    public function updatePoliciesBatch(array $policies): void;

    /**
     * Get all sick leave tiers ordered by sort_order.
     */
    public function getSickLeaveTiers(): Collection;

    /**
     * Synchronize sick leave tiers.
     */
    public function syncSickLeaveTiers(array $tiers): void;

    /**
     * Get all End of Service (EOS) calculation rules.
     */
    public function getEosRules(?string $type = null): Collection;

    /**
     * Synchronize EOS calculation rules.
     */
    public function syncEosRules(array $rules): void;

    /**
     * Reset policies and tiers to system defaults.
     */
    public function resetToDefaults(): void;
}
