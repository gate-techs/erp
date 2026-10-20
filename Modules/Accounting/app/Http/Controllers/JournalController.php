<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Accounting\Enums\JournalType;
use Modules\Accounting\Http\Requests\StoreJournalRequest;
use Modules\Accounting\Http\Requests\UpdateJournalRequest;
use Modules\Accounting\Models\Account;
use Modules\Accounting\Models\Entity;
use Modules\Accounting\Models\Journal;
use Modules\Accounting\Services\JournalService;
use Yajra\DataTables\Facades\DataTables;

class JournalController extends Controller
{
    public function __construct(
        protected JournalService $service
    ) {}

    public function index(): View
    {
        return view('accounting::journals.index', [
            'entities' => Entity::with('translations')->orderBy('id')->get(),
            'accounts' => Account::with('translations')->orderBy('entity_id')->orderBy('code')->get(),
            'types' => JournalType::cases(),
        ]);
    }

    public function create(): View
    {
        return view('accounting::journals.create', [
            'entities' => Entity::with('translations')->orderBy('id')->get(),
            'accounts' => Account::with('translations')->orderBy('entity_id')->orderBy('code')->get(),
            'types' => JournalType::cases(),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = $this->service->getDataTableQuery($request->all())
            ->with(['translations', 'entity.translations', 'defaultAccount.translations']);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->filterColumn('journal_info', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('journals.code', 'like', "%{$keyword}%")
                        ->orWhereTranslationLike('name', "%{$keyword}%");
                });
            })
            ->orderColumn('journal_info', fn ($query, $order) => $query->orderBy('journals.code', $order))
            ->addColumn('journal_info', function (Journal $journal) {
                $name = $journal->translate(app()->getLocale())?->name
                    ?? $journal->translate('ar')?->name
                    ?? $journal->translate('en')?->name
                    ?? '-';

                return '<div class="d-flex align-items-center"><div class="avatar avatar-sm bg-label-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded"><i class="icon-base bx bx-book-open font-20"></i></div><div class="d-flex flex-column"><span class="text-heading fw-bold fs-6">' . e($name) . '</span><small class="text-muted">' . e($journal->code) . '</small></div></div>';
            })
            ->addColumn('default_account_info', function (Journal $journal) {
                if (!$journal->defaultAccount) {
                    return '<span class="badge bg-label-secondary font-italic text-muted">-</span>';
                }
                $acc = $journal->defaultAccount;
                $accName = $acc->translate(app()->getLocale())?->name
                    ?? $acc->translate('ar')?->name
                    ?? $acc->translate('en')?->name
                    ?? $acc->code;

                return '<div class="d-flex align-items-center gap-1">' .
                    '<span class="badge bg-label-primary font-monospace">' . e($acc->code) . '</span>' .
                    '<span class="text-truncate text-heading fw-medium" style="max-width: 220px;" title="' . e($accName) . '">' . e($accName) . '</span>' .
                    '</div>';
            })
            ->addColumn('entity_info', function (Journal $journal) {
                $name = $journal->entity?->translate(app()->getLocale())?->name
                    ?? $journal->entity?->translate('ar')?->name
                    ?? $journal->entity?->code
                    ?? '-';

                return '<span class="badge bg-label-secondary fw-semibold">' . e($name) . '</span>';
            })
            ->addColumn('type_badge', fn (Journal $journal) => '<span class="badge bg-label-info">' . e($journal->type->label()) . '</span>')
            ->addColumn('status_badge', function (Journal $journal) {
                $class = $journal->is_active ? 'success' : 'danger';
                $label = $journal->is_active ? __('Active') : __('Inactive');
                return '<span class="badge bg-label-' . $class . '">' . e($label) . '</span>';
            })
            ->addColumn('created_at', fn (Journal $journal) => $journal->created_at?->format('Y-m-d') ?? '-')
            ->addColumn('actions', function (Journal $journal) {
                $user = auth()->user();
                $name = $journal->translate(app()->getLocale())?->name ?? $journal->code;
                $editUrl = route('accounting.journals.edit', $journal->id);

                $editBtn = '';
                if ($user->can('accounting.journals.edit')) {
                    $editBtn = '<a href="' . $editUrl . '" class="btn btn-icon btn-sm btn-outline-primary" title="' . __('Edit') . '"><i class="icon-base bx bx-edit"></i></a>';
                }

                $toggleBtn = '';
                if ($user->can('accounting.journals.toggle-status')) {
                    $toggleBtn = '<button type="button" class="btn btn-icon btn-sm btn-outline-warning btn-toggle-journal" data-id="' . $journal->id . '" data-status="' . ($journal->is_active ? 'active' : 'inactive') . '" title="' . __('Change Status') . '"><i class="icon-base bx bx-power-off"></i></button>';
                }

                $deleteBtn = '';
                if ($user->can('accounting.journals.delete')) {
                    $deleteBtn = '<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-journal" data-id="' . $journal->id . '" data-name="' . e($name) . '" title="' . __('Delete') . '"><i class="icon-base bx bx-trash"></i></button>';
                }

                $buttons = $editBtn . $toggleBtn . $deleteBtn;
                return $buttons ? '<div class="d-inline-flex gap-1">' . $buttons . '</div>' : '<span class="text-muted">---</span>';
            })
            ->rawColumns(['journal_info', 'default_account_info', 'entity_info', 'type_badge', 'status_badge', 'actions'])
            ->make(true);
    }

    public function store(StoreJournalRequest $request): JsonResponse|RedirectResponse
    {
        $journal = $this->service->create($request->validated(), auth()->id());
        return $this->respond($request, __('Journal created successfully.'), $journal);
    }

    public function edit(int $id): View
    {
        $journal = $this->service->getById($id);
        $journal->load(['translations', 'entity']);

        return view('accounting::journals.edit', [
            'journal' => $journal,
            'entities' => Entity::with('translations')->orderBy('id')->get(),
            'accounts' => Account::with('translations')->orderBy('entity_id')->orderBy('code')->get(),
            'types' => JournalType::cases(),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $journal = $this->service->getById($id);
        return response()->json(['success' => true, 'data' => [
            'id' => $journal->id,
            'entity_id' => $journal->entity_id,
            'code' => $journal->code,
            'type' => $journal->type->value,
            'currency' => $journal->currency,
            'sequence_prefix' => $journal->sequence_prefix,
            'sequence_number_next' => $journal->sequence_number_next,
            'refund_sequence' => $journal->refund_sequence,
            'payment_sequence' => $journal->payment_sequence,
            'default_account_id' => $journal->default_account_id,
            'suspense_account_id' => $journal->suspense_account_id,
            'profit_account_id' => $journal->profit_account_id,
            'loss_account_id' => $journal->loss_account_id,
            'allow_manual_entries' => $journal->allow_manual_entries,
            'reconcile' => $journal->reconcile,
            'restrict_mode_hash_table' => $journal->restrict_mode_hash_table,
            'is_active' => $journal->is_active,
            'name_ar' => $journal->translate('ar')?->name ?? '',
            'name_en' => $journal->translate('en')?->name ?? '',
            'description_ar' => $journal->translate('ar')?->description ?? '',
            'description_en' => $journal->translate('en')?->description ?? '',
        ]]);
    }

    public function update(UpdateJournalRequest $request, int $id): JsonResponse|RedirectResponse
    {
        $journal = $this->service->update($id, $request->validated(), auth()->id());
        return $this->respond($request, __('Journal updated successfully.'), $journal);
    }

    public function destroy(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $this->service->delete($id);
        return $this->respond($request, __('Journal deleted successfully.'));
    }

    public function toggleStatus(int $id): JsonResponse
    {
        $journal = $this->service->toggleStatus($id);
        return response()->json(['success' => true, 'message' => __('Status updated successfully.'), 'data' => ['id' => $journal->id, 'is_active' => $journal->is_active]]);
    }

    protected function respond(Request $request, string $message, mixed $data = null): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $message, 'data' => $data]);
        }

        return redirect()->route('accounting.journals.index')->with('success', $message);
    }
}
