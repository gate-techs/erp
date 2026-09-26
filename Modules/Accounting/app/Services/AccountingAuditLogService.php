<?php

namespace Modules\Accounting\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Accounting\Repositories\Contracts\AccountingAuditLogRepositoryInterface;
use Modules\AuditLog\Models\ActivityLog;

class AccountingAuditLogService
{
    public function __construct(
        protected AccountingAuditLogRepositoryInterface $repository
    ) {}

    /**
     * Get initial page view data.
     */
    public function getViewData(): array
    {
        return [
            'lookups' => $this->repository->getLookups(),
            'kpis'    => $this->repository->getKpiMetrics(),
        ];
    }

    /**
     * Process DataTables Ajax request.
     */
    public function getDataTableResponse(Request $request): JsonResponse
    {
        $draw    = (int) $request->input('draw', 1);
        $start   = (int) $request->input('start', 0);
        $length  = (int) $request->input('length', 15);
        $page    = ($start / max(1, $length)) + 1;

        $filters = [
            'search'       => $request->input('search.value') ?: $request->input('quick_search'),
            'user_id'      => $request->input('user_id'),
            'event'        => $request->input('event'),
            'subject_type' => $request->input('subject_type'),
            'date_from'    => $request->input('date_from'),
            'date_to'      => $request->input('date_to'),
        ];

        $paginator = $this->repository->getPaginated($filters, $length);

        $data = [];
        $index = 0;
        /** @var ActivityLog $log */
        foreach ($paginator->items() as $log) {
            $index++;
            $rowIndex = $start + $index;

            // Causer User
            $causerName = $log->causer?->name ?? __('System / Automated');
            $causerEmail = $log->causer?->email ?? '';
            $initials = mb_substr($causerName, 0, 1);

            $userHtml = '<div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-xs bg-label-primary rounded-circle d-flex align-items-center justify-content-center fw-bold font-12">'
                    . e($initials) .
                '</div>
                <div class="d-flex flex-column text-start">
                    <span class="fw-semibold text-heading">' . e($causerName) . '</span>
                    <small class="text-muted" style="font-size: 0.75rem;">' . e($causerEmail) . '</small>
                </div>
            </div>';

            // Event Badge
            $eventHtml = '<span class="badge ' . e($log->event_badge_color) . ' font-11 px-2 py-1">'
                . e($log->event_label) .
            '</span>';

            // Subject Tag
            $subjectLabel = $log->subject_type_label;
            $subjectTitle = $log->subject_title;
            $subjectHtml = '<div class="d-flex flex-column text-start">
                <span class="badge bg-label-secondary font-11 w-fit mb-1">' . e($subjectLabel) . '</span>
                <span class="small fw-semibold text-heading">' . e($subjectTitle) . '</span>
            </div>';

            // Description
            $descHtml = '<span class="small text-muted">' . e($log->description) . '</span>';

            // Timestamp
            $timeHtml = '<div class="d-flex flex-column text-start">
                <span class="small fw-semibold text-heading">' . $log->created_at->format('Y-m-d H:i') . '</span>
                <small class="text-muted" style="font-size: 0.725rem;">' . $log->created_at->diffForHumans() . '</small>
            </div>';

            // Action
            $hasDiffs = !empty($log->properties['old']) || !empty($log->properties['attributes']);
            $actionBtn = '';
            if ($hasDiffs) {
                $actionBtn = '<button type="button" class="btn btn-sm btn-icon btn-label-primary btn-view-diff" data-id="' . $log->id . '" title="' . __('View Change Details') . '">
                    <i class="bx bx-show font-16"></i>
                </button>';
            } else {
                $actionBtn = '<button type="button" class="btn btn-sm btn-icon btn-label-secondary btn-view-diff" data-id="' . $log->id . '" title="' . __('View Details') . '">
                    <i class="bx bx-info-circle font-16"></i>
                </button>';
            }

            $data[] = [
                'DT_RowIndex' => $rowIndex,
                'id'          => $log->id,
                'user'        => $userHtml,
                'subject'     => $subjectHtml,
                'event'       => $eventHtml,
                'description' => $descHtml,
                'created_at'  => $timeHtml,
                'actions'     => $actionBtn,
            ];
        }

        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => $paginator->total(),
            'recordsFiltered' => $paginator->total(),
            'data'            => $data,
        ]);
    }

    /**
     * Get detailed diff and attributes for modal.
     */
    public function getLogDetails(int $id): array
    {
        $log = $this->repository->findById($id);

        if (!$log) {
            abort(404, __('Record not found'));
        }

        return [
            'id'                 => $log->id,
            'description'        => $log->description,
            'event_label'        => $log->event_label,
            'event_badge_color'  => $log->event_badge_color,
            'subject_type_label' => $log->subject_type_label,
            'subject_title'      => $log->subject_title,
            'causer_name'        => $log->causer?->name ?? __('System / Automated'),
            'created_at'         => $log->created_at->format('Y-m-d H:i:s'),
            'diffs'              => $log->getFieldDiffs(),
            'raw_properties'     => $log->properties,
        ];
    }
}
