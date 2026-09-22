<?php

namespace Modules\AuditLog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\AuditLog\Models\ActivityLog;
use Modules\AuditLog\Services\AuditLogService;
use Yajra\DataTables\Facades\DataTables;

class AuditLogController extends Controller
{
    public function __construct(
        protected AuditLogService $auditService
    ) {}

    /**
     * Display activity log listing page.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        if (auth()->check() && !auth()->user()->can('audit.view')) {
            abort(403, __('ليس لديك صلاحية للوصول إلى سجل الأنشطة والتدقيق.'));
        }

        $data = $this->auditService->getIndexData($request->all());

        return view('auditlog::index', $data);
    }

    /**
     * Return Server-side DataTables JSON response.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function data(Request $request): JsonResponse
    {
        if (auth()->check() && !auth()->user()->can('audit.view')) {
            return response()->json(['error' => __('غير مصرح')], 403);
        }

        $search = $request->input('search');
        if (is_array($search)) {
            $search = $search['value'] ?? null;
        }

        $filters = [
            'user_id'      => $request->input('user_id'),
            'event'        => $request->input('event'),
            'subject_type' => $request->input('subject_type'),
            'start_date'   => $request->input('start_date'),
            'end_date'     => $request->input('end_date'),
            'search'       => is_string($search) ? trim($search) : null,
        ];

        $query = $this->auditService->getLogsQuery($filters);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('date_formatted', function (ActivityLog $log) {
                $date = $log->created_at ? $log->created_at->format('Y-m-d') : '-';
                $time = $log->created_at ? $log->created_at->format('H:i:s') : '';
                $diff = $log->created_at ? $log->created_at->diffForHumans() : '';
                return '
                    <div class="fw-semibold text-dark">' . e($date) . '</div>
                    <small class="text-muted font-monospace">' . e($time) . ' (' . e($diff) . ')</small>
                ';
            })
            ->addColumn('causer_info', function (ActivityLog $log) {
                if ($log->causer) {
                    $initial = mb_substr($log->causer->name ?? 'U', 0, 1);
                    return '
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-xs bg-label-primary rounded-circle">
                            <span class="avatar-initial">' . e($initial) . '</span>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark">' . e($log->causer->name) . '</div>
                            <small class="text-muted font-monospace">' . e($log->causer->email) . '</small>
                        </div>
                    </div>';
                }
                return '<span class="badge bg-label-secondary">' . __('نظام / تلقائي') . '</span>';
            })
            ->addColumn('event_badge', function (ActivityLog $log) {
                return '<span class="badge ' . e($log->event_badge_color) . '">' . e($log->event_label) . '</span>';
            })
            ->addColumn('subject_info', function (ActivityLog $log) {
                $badge = '<span class="badge bg-label-dark">' . e($log->subject_type_label) . '</span>';
                $title = '<div class="text-truncate text-dark fw-semibold small mt-1" style="max-width: 170px;" title="' . e($log->subject_title) . '">' . e($log->subject_title) . '</div>';
                return $badge . $title;
            })
            ->addColumn('description_formatted', function (ActivityLog $log) {
                return '<div class="text-dark small">' . e($log->description) . '</div>';
            })
            ->addColumn('actions', function (ActivityLog $log) {
                return '<button type="button" class="btn btn-sm btn-icon btn-label-primary view-diff-btn" data-id="' . $log->id . '" title="' . __('عرض الفروقات والتفاصيل') . '">
                    <i class="bx bx-show"></i>
                </button>';
            })
            ->rawColumns(['date_formatted', 'causer_info', 'event_badge', 'subject_info', 'description_formatted', 'actions'])
            ->order(function ($query) {
                $query->orderBy('id', 'desc');
            })
            ->toJson();
    }

    /**
     * Get details and field diffs for AJAX modal popup.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        if (auth()->check() && !auth()->user()->can('audit.view')) {
            return response()->json(['error' => __('غير مصرح')], 403);
        }

        $data = $this->auditService->getLogDetails($id);
        $html = view('auditlog::show_modal', $data)->render();

        return response()->json([
            'success' => true,
            'html'    => $html,
            'log'     => $data['log'],
        ]);
    }
}

