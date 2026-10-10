<?php
namespace Modules\Academic\Repositories\Contracts;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Academic\Models\DailyReport;
interface DailyReportRepositoryInterface { public function query(array $filters = []): Builder; public function find(int $id): ?DailyReport; public function create(array $data): DailyReport; public function update(int $id, array $data): ?DailyReport; public function delete(int $id): bool; public function institutions(): Collection; public function branches(?int $institutionId = null): Collection; }
