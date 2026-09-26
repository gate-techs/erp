<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Accounting\Enums\BankReconciliationItemType;
use Modules\Accounting\Enums\BankReconciliationStatus;
use Modules\Accounting\Enums\CashAccountType;
use Modules\Accounting\Models\BankReconciliation;
use Modules\Accounting\Models\CashAccount;
use Modules\Accounting\Models\Entity;
use Modules\Accounting\Services\BankReconciliationService;
use Yajra\DataTables\Facades\DataTables;

class BankReconciliationController extends Controller
{
    //--------------------------------------------------
    public function __construct(
        protected BankReconciliationService $service
    ) {}

    //--------------------------------------------------
    public function index(Request $request): View
    {
        $entities = Entity::with('translations')->orderBy('id')->get();
        $bankAccounts = CashAccount::with('translations')
            ->where('type', CashAccountType::BANK)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('accounting::bank-reconciliations.index', [
            'entities'     => $entities,
            'bankAccounts' => $bankAccounts,
            'statuses'     => BankReconciliationStatus::cases(),
        ]);
    }

    //--------------------------------------------------
    public function data(Request $request): JsonResponse
    {
        $query = $this->service->getDataTableQuery($request->all())
            ->with(['entity.translations', 'cashAccount.translations', 'reconciledBy', 'creator']);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('rec_info', function (BankReconciliation $item) {
                return '<div class="d-flex align-items-center">
                    <div class="avatar avatar-sm bg-label-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded">
                        <i class="icon-base bx bx-file font-18"></i>
                    </div>
                    <div class="d-flex flex-column">
                        <a href="' . route('accounting.bank-reconciliations.show', $item->id) . '" class="text-heading fw-bold fs-6">
                            ' . e($item->reconciliation_number) . '
                        </a>
                        <small class="text-muted font-monospace"><i class="icon-base bx bx-calendar font-12 me-1"></i>' . e($item->statement_date->format('Y-m-d')) . '</small>
                    </div>
                </div>';
            })
            ->addColumn('entity_name', function (BankReconciliation $item) {
                $entityName = $item->entity?->translate(app()->getLocale())?->name
                    ?? $item->entity?->translate('ar')?->name
                    ?? $item->entity?->code
                    ?? '-';
                return '<span class="badge bg-label-secondary fw-semibold">' . e($entityName) . '</span>';
            })
            ->addColumn('bank_info', function (BankReconciliation $item) {
                $locale = app()->getLocale();
                $name = $item->cashAccount?->translate($locale)?->name
                    ?? $item->cashAccount?->translate('ar')?->name
                    ?? $item->cashAccount?->code
                    ?? '-';
                $bankSub = '';
                if ($item->cashAccount?->bank_name) {
                    $bankSub = '<small class="text-muted d-block">' . e($item->cashAccount->bank_name) . ($item->cashAccount->account_number ? ' &bull; ' . e($item->cashAccount->account_number) : '') . '</small>';
                }
                return '<div class="d-flex flex-column">
                    <span class="text-heading fw-bold">' . e($name) . '</span>
                    <small class="text-muted font-monospace">' . e($item->cashAccount?->code ?? '') . ' &bull; ' . e($item->cashAccount?->currency ?? '') . '</small>' . $bankSub . '
                </div>';
            })
            ->addColumn('statement_balance_fmt', function (BankReconciliation $item) {
                return '<span class="fw-bold font-monospace text-primary">' . number_format($item->statement_balance, 2) . '</span> <small class="text-muted">' . e($item->cashAccount?->currency ?? '') . '</small>';
            })
            ->addColumn('book_balance_fmt', function (BankReconciliation $item) {
                return '<span class="fw-semibold font-monospace">' . number_format($item->book_balance, 2) . '</span>';
            })
            ->addColumn('difference_fmt', function (BankReconciliation $item) {
                $diff = (float) $item->difference;
                if (abs($diff) < 0.0001) {
                    return '<span class="badge bg-label-success d-inline-flex align-items-center"><i class="icon-base bx bx-check-circle me-1 font-12"></i>0.00</span>';
                }
                return '<span class="badge bg-label-danger d-inline-flex align-items-center"><i class="icon-base bx bx-error me-1 font-12"></i>' . number_format($diff, 2) . '</span>';
            })
            ->addColumn('status_badge', function (BankReconciliation $item) {
                return '<span class="badge ' . $item->status->badgeClass() . ' d-inline-flex align-items-center">' . e($item->status->label()) . '</span>';
            })
            ->addColumn('actions', function (BankReconciliation $item) {
                $showUrl = route('accounting.bank-reconciliations.show', $item->id);
                $editUrl = route('accounting.bank-reconciliations.edit', $item->id);
                $printUrl = route('accounting.bank-reconciliations.print', $item->id);

                $actions = '<div class="d-inline-flex gap-1">';

                $actions .= '<a href="' . $printUrl . '" target="_blank" class="btn btn-icon btn-sm btn-outline-secondary" title="' . __('Print Statement') . '">
                    <i class="icon-base bx bx-printer"></i>
                </a>';

                $actions .= '<a href="' . $showUrl . '" class="btn btn-icon btn-sm btn-outline-info" title="' . __('View') . '">
                    <i class="icon-base bx bx-show"></i>
                </a>';

                if ($item->isDraft() || $item->status === BankReconciliationStatus::IN_PROGRESS) {
                    $actions .= '<a href="' . $editUrl . '" class="btn btn-icon btn-sm btn-outline-primary" title="' . __('Edit / Reconcile') . '">
                        <i class="icon-base bx bx-edit"></i>
                    </a>';

                    $actions .= '<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-rec" data-id="' . $item->id . '" data-number="' . e($item->reconciliation_number) . '" title="' . __('Delete') . '">
                        <i class="icon-base bx bx-trash"></i>
                    </button>';
                } else if ($item->isCompleted()) {
                    $actions .= '<button type="button" class="btn btn-icon btn-sm btn-outline-warning btn-reopen-rec" data-id="' . $item->id . '" title="' . __('Reopen to Draft') . '">
                        <i class="icon-base bx bx-lock-open"></i>
                    </button>';
                }

                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['rec_info', 'entity_name', 'bank_info', 'statement_balance_fmt', 'book_balance_fmt', 'difference_fmt', 'status_badge', 'actions'])
            ->make(true);
    }

    //--------------------------------------------------
    public function create(Request $request): View
    {
        $entities = Entity::with('translations')->orderBy('id')->get();
        $selectedEntityId = (int) ($request->integer('entity_id') ?: $entities->first()?->id);

        $bankAccounts = CashAccount::with('translations')
            ->where('entity_id', $selectedEntityId)
            ->where('type', CashAccountType::BANK)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $selectedBankAccountId = (int) ($request->integer('cash_account_id') ?: $bankAccounts->first()?->id);

        return view('accounting::bank-reconciliations.create', [
            'entities'              => $entities,
            'selectedEntityId'      => $selectedEntityId,
            'bankAccounts'          => $bankAccounts,
            'selectedBankAccountId' => $selectedBankAccountId,
            'reconciliation'        => null,
        ]);
    }

    //--------------------------------------------------
    public function edit(int $id): View
    {
        $reconciliation = $this->service->findById($id);

        if (!$reconciliation) {
            abort(404, __('Bank reconciliation not found.'));
        }

        if ($reconciliation->isCompleted()) {
            return view('accounting::bank-reconciliations.show', [
                'reconciliation' => $reconciliation,
            ]);
        }

        $entities = Entity::with('translations')->orderBy('id')->get();
        $bankAccounts = CashAccount::with('translations')
            ->where('entity_id', $reconciliation->entity_id)
            ->where('type', CashAccountType::BANK)
            ->orderBy('code')
            ->get();

        return view('accounting::bank-reconciliations.create', [
            'entities'         => $entities,
            'selectedEntityId' => $reconciliation->entity_id,
            'bankAccounts'     => $bankAccounts,
            'reconciliation'   => $reconciliation,
        ]);
    }

    //--------------------------------------------------
    public function workspaceData(Request $request): JsonResponse
    {
        $cashAccountId = $request->integer('cash_account_id');
        $statementDate = $request->input('statement_date', date('Y-m-d'));
        $statementBalance = (float) $request->input('statement_balance', 0);
        $reconciliationId = $request->integer('reconciliation_id') ?: null;

        if (!$cashAccountId) {
            return response()->json(['success' => false, 'message' => __('Bank account is required.')], 422);
        }

        $data = $this->service->getWorkspaceData($cashAccountId, $statementDate, $statementBalance, $reconciliationId);

        $unreconciledFormatted = $data['unreconciledLines']->map(function ($line) {
            return [
                'id'                   => $line->id,
                'entry_id'             => $line->entry?->id,
                'entry_number'         => $line->entry?->entry_number,
                'entry_date'           => $line->entry?->entry_date?->format('Y-m-d'),
                'journal_name'         => $line->entry?->journal?->translate(app()->getLocale())?->name ?? $line->entry?->journal?->name ?? '',
                'reference'            => $line->entry?->reference ?? '',
                'description'          => $line->description ?: $line->entry?->narration,
                'debit'                => (float) $line->debit,
                'credit'               => (float) $line->credit,
                'amount'               => (float) ($line->debit > 0 ? $line->debit : -$line->credit),
            ];
        });

        return response()->json([
            'success'            => true,
            'cash_account'       => [
                'id'             => $data['cashAccount']->id,
                'code'           => $data['cashAccount']->code,
                'name'           => $data['cashAccount']->translate(app()->getLocale())?->name ?? $data['cashAccount']->code,
                'bank_name'      => $data['cashAccount']->bank_name,
                'account_number' => $data['cashAccount']->account_number,
                'currency'       => $data['cashAccount']->currency,
            ],
            'book_balance'       => $data['bookBalance'],
            'statement_balance'  => $data['statementBalance'],
            'unreconciled_lines' => $unreconciledFormatted,
            'cleared_line_ids'   => $data['clearedLineIds'],
        ]);
    }

    //--------------------------------------------------
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'cash_account_id'   => ['required', 'integer', 'exists:cash_accounts,id'],
            'statement_date'    => ['required', 'date'],
            'statement_balance' => ['required', 'numeric'],
            'action'            => ['required', 'string', 'in:save_draft,complete'],
            'items'             => ['nullable', 'array'],
        ]);

        $complete = ($request->input('action') === 'complete');
        $reconciliation = $this->service->saveReconciliation($request->all(), $complete);

        $message = $complete
            ? __('Bank reconciliation completed and closed successfully.')
            : __('Bank reconciliation draft saved successfully.');

        return response()->json([
            'success'               => true,
            'message'               => $message,
            'reconciliation_id'     => $reconciliation->id,
            'reconciliation_number' => $reconciliation->reconciliation_number,
            'is_completed'          => $reconciliation->isCompleted(),
            'redirect_url'          => route('accounting.bank-reconciliations.show', $reconciliation->id),
        ]);
    }

    //--------------------------------------------------
    public function show(int $id): View
    {
        $reconciliation = $this->service->findById($id);

        if (!$reconciliation) {
            abort(404, __('Bank reconciliation not found.'));
        }

        return view('accounting::bank-reconciliations.show', [
            'reconciliation' => $reconciliation,
        ]);
    }

    //--------------------------------------------------
    public function print(int $id): View
    {
        $reconciliation = $this->service->findById($id);

        if (!$reconciliation) {
            abort(404, __('Bank reconciliation not found.'));
        }

        return view('accounting::bank-reconciliations.print', [
            'reconciliation' => $reconciliation,
        ]);
    }

    //--------------------------------------------------
    public function reopen(int $id): JsonResponse
    {
        $reconciliation = $this->service->reopenReconciliation($id);

        return response()->json([
            'success' => true,
            'message' => __('Bank reconciliation reopened to draft successfully.'),
        ]);
    }

    //--------------------------------------------------
    public function destroy(int $id): JsonResponse
    {
        $this->service->deleteReconciliation($id);

        return response()->json([
            'success' => true,
            'message' => __('Bank reconciliation deleted successfully.'),
        ]);
    }

    //--------------------------------------------------
    public function getBanksByEntity(Request $request): JsonResponse
    {
        $entityId = $request->integer('entity_id');

        $banks = CashAccount::with('translations')
            ->where('entity_id', $entityId)
            ->where('type', CashAccountType::BANK)
            ->where('is_active', true)
            ->orderBy('code')
            ->get()
            ->map(function ($acc) {
                return [
                    'id'        => $acc->id,
                    'code'      => $acc->code,
                    'name'      => $acc->translate(app()->getLocale())?->name ?? $acc->translate('ar')?->name ?? $acc->code,
                    'bank_name' => $acc->bank_name,
                    'currency'  => $acc->currency,
                ];
            });

        return response()->json(['success' => true, 'banks' => $banks]);
    }
}
