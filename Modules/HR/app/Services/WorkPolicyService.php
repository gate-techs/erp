<?php

namespace Modules\HR\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\HR\Repositories\Contracts\WorkPolicyRepositoryInterface;

class WorkPolicyService
{
    //======================
    public function __construct(
        protected WorkPolicyRepositoryInterface $repository
    ) {}

    //======================
    /**
     * Get all data needed for the Work Policies settings view.
     */
    public function getSettingsViewData(): array
    {
        $groupedPolicies = $this->repository->getGroupedPolicies();
        $sickLeaveTiers  = $this->repository->getSickLeaveTiers();
        $eosRules        = $this->repository->getEosRules();

        return [
            'policies'       => $groupedPolicies,
            'sickLeaveTiers' => $sickLeaveTiers,
            'eosRules'       => $eosRules,
        ];
    }

    //======================
    /**
     * Save/Update work policies, sick leave tiers, and EOS rules.
     */
    public function saveWorkPolicies(array $payload): bool
    {
        return DB::transaction(function () use ($payload) {
            // 1. Update general key-value policies
            if (isset($payload['policies']) && is_array($payload['policies'])) {
                $this->repository->updatePoliciesBatch($payload['policies']);
            }

            // 2. Synchronize sick leave tiers if provided
            if (isset($payload['sick_leave_tiers']) && is_array($payload['sick_leave_tiers'])) {
                $this->repository->syncSickLeaveTiers($payload['sick_leave_tiers']);
            }

            // 3. Synchronize EOS calculation rules if provided
            if (isset($payload['eos_rules']) && is_array($payload['eos_rules'])) {
                $this->repository->syncEosRules($payload['eos_rules']);
            }

            return true;
        });
    }

    //======================
    /**
     * Reset all HR work policies to factory defaults.
     */
    public function resetToDefaults(): void
    {
        $this->repository->resetToDefaults();
    }

    //======================
    /**
     * Convenient getter for any policy value.
     */
    public function getPolicy(string $key, mixed $default = null): mixed
    {
        return $this->repository->getPolicyValue($key, $default);
    }
}
