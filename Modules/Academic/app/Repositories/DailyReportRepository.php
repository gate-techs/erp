<?php
namespace Modules\Academic\Repositories;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Academic\Models\DailyReport;
use Modules\Academic\Repositories\Contracts\DailyReportRepositoryInterface;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;

class DailyReportRepository implements DailyReportRepositoryInterface
{
    public function query(array $filters = []): Builder { $q=DailyReport::with(['institution.translations','branch.translations'])->latest('report_date'); foreach(['institution_id','branch_id'] as $field) if(!empty($filters[$field])) $q->where($field,$filters[$field]); if(!empty($filters['report_date'])) $q->whereDate('report_date',$filters['report_date']); return $q; }
    public function find(int $id): ?DailyReport { return DailyReport::with(['institution.translations','branch.translations','entries'])->find($id); }
    public function create(array $data): DailyReport { return DB::transaction(function() use($data) { $entries=$data['entries']??[]; unset($data['entries']); $report=DailyReport::create($data); $this->syncEntries($report,$entries); return $this->find($report->id); }); }
    public function update(int $id, array $data): ?DailyReport { return DB::transaction(function() use($id,$data) { $entries=$data['entries']??[]; unset($data['entries']); $report=DailyReport::find($id); if(!$report) return null; $report->update($data); $report->entries()->delete(); $this->syncEntries($report,$entries); return $this->find($report->id); }); }
    public function delete(int $id): bool { $report=DailyReport::find($id); return $report ? (bool)$report->delete() : false; }
    public function institutions(): Collection { return Nursery::with('translations')->orderBy('id')->get(); }
    public function branches(?int $institutionId = null): Collection { return NurseryBranch::with('translations')->when($institutionId,fn($q)=>$q->where('nursery_id',$institutionId))->orderBy('id')->get(); }
    private function syncEntries(DailyReport $report,array $entries): void { foreach($entries as $category=>$rows) foreach(array_values($rows??[]) as $index=>$row) { if(empty($row['incident']) && empty($row['action_taken'])) continue; $report->entries()->create(['category'=>$category,'incident'=>$row['incident']??null,'action_taken'=>$row['action_taken']??null,'sort_order'=>$index]); } }
}
