<?php

namespace Modules\Settings\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Settings\Models\Bank;
use Modules\Settings\Repositories\Contracts\BankRepositoryInterface;

class BankService
{
    public function __construct(
        protected BankRepositoryInterface $bankRepository
    ) {}

    /**
     * Get DataTable query for Main Banks.
     */
    public function getMainBanksDataTableQuery(array $filters = []): Builder
    {
        $filters['type'] = 'main';
        return $this->bankRepository->getDataTableQuery($filters)->with(['translations', 'creator'])->withCount('branches');
    }

    /**
     * Get DataTable query for Bank Branches.
     */
    public function getBankBranchesDataTableQuery(array $filters = []): Builder
    {
        $filters['type'] = 'branch';
        return $this->bankRepository->getDataTableQuery($filters)->with(['translations', 'parent.translations', 'creator']);
    }

    /**
     * Get main banks for dropdown selection.
     */
    public function getMainBanksList(bool $onlyActive = false): Collection
    {
        return $this->bankRepository->getMainBanks($onlyActive);
    }

    /**
     * Get Bank by ID.
     */
    public function getById(int $id, array $with = ['translations', 'parent', 'branches']): ?Bank
    {
        return $this->bankRepository->findById($id, $with);
    }

    /**
     * Create a new Bank or Branch.
     */
    public function createBank(array $validatedData, ?int $userId = null): Bank
    {
        $data = $this->prepareData($validatedData, $userId);

        return $this->bankRepository->create($data);
    }

    /**
     * Update an existing Bank or Branch.
     */
    public function updateBank(int|Bank $bank, array $validatedData): Bank
    {
        $data = $this->prepareData($validatedData);

        return $this->bankRepository->update($bank, $data);
    }

    /**
     * Delete Bank or Branch.
     */
    public function deleteBank(int|Bank $bank): bool
    {
        return $this->bankRepository->delete($bank);
    }

    /**
     * Format payload for Astrotomic Translatable and relation attributes.
     */
    protected function prepareData(array $input, ?int $userId = null): array
    {
        $data = [];

        // Base fields
        if (array_key_exists('parent_id', $input)) {
            $data['parent_id'] = !empty($input['parent_id']) ? (int) $input['parent_id'] : null;
        }

        if (isset($input['code'])) {
            $data['code'] = $input['code'];
        }

        if (isset($input['swift_code'])) {
            $data['swift_code'] = $input['swift_code'];
        }

        if (isset($input['status'])) {
            $data['status'] = $input['status'];
        }

        if ($userId && !isset($input['created_by'])) {
            $data['created_by'] = $userId;
        }

        // Arabic translation
        if (isset($input['name_ar']) || isset($input['address_ar'])) {
            $data['ar'] = [
                'name'    => $input['name_ar'] ?? '',
                'address' => $input['address_ar'] ?? null,
            ];
        }

        // English translation
        if (isset($input['name_en']) || isset($input['address_en'])) {
            $data['en'] = [
                'name'    => $input['name_en'] ?? '',
                'address' => $input['address_en'] ?? null,
            ];
        }

        return $data;
    }
}
