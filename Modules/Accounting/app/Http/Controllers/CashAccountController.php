<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Accounting\Enums\CashAccountType;
use Modules\Accounting\Http\Requests\StoreCashAccountRequest;
use Modules\Accounting\Http\Requests\UpdateCashAccountRequest;
use Modules\Accounting\Models\Account;
use Modules\Accounting\Models\CashAccount;
use Modules\Accounting\Models\Entity;
use Modules\Accounting\Models\Journal;
use Modules\Accounting\Services\CashAccountService;
use Yajra\DataTables\Facades\DataTables;

class CashAccountController extends Controller
{
    //--------------------------------------------------
    public function __construct(
        protected CashAccountService $service
    ) {}

    //--------------------------------------------------
    public function index(Request $request): View
    {
        return view('accounting::cash-accounts.index', [
            'entities' => Entity::with('translations')->orderBy('id')->get(),
            'types' => CashAccountType::cases(),
        ]);
    }

    //--------------------------------------------------
    public function create(): View
    {
        return view('accounting::cash-accounts.create', [
            'entities' => Entity::with('translations')->orderBy('id')->get(),
            'accounts' => Account::with('translations')->orderBy('entity_id')->orderBy('code')->get(),
            'journals' => Journal::with('translations')->orderBy('entity_id')->orderBy('code')->get(),
            'users' => User::orderBy('name')->get(),
            'types' => CashAccountType::cases(),
        ]);
    }

    //--------------------------------------------------
    public function data(Request $request): JsonResponse
    {
        $query = $this->service->getDataTableQuery($request->all())
            ->with(['translations', 'entity.translations', 'account.translations', 'journal.translations', 'responsibleUser']);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->filterColumn('account_info', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('cash_accounts.code', 'like', "%{$keyword}%")
                        ->orWhere('cash_accounts.bank_name', 'like', "%{$keyword}%")
                        ->orWhere('cash_accounts.account_number', 'like', "%{$keyword}%")
                        ->orWhereTranslationLike('name', "%{$keyword}%");
                });
            })
            ->orderColumn('account_info', fn ($query, $order) => $query->orderBy('cash_accounts.code', $order))
            ->addColumn('account_info', function (CashAccount $item) {
                $locale = app()->getLocale();
                $name = $item->translate($locale)?->name
                    ?? $item->translate('ar')?->name
                    ?? $item->translate('en')?->name
                    ?? '-';

                $iconClass = $item->type === CashAccountType::CASH ? 'bx-wallet' : 'bx-building';
                $badgeColor = $item->type === CashAccountType::CASH ? 'success' : 'primary';

                $bankSub = '';
                if ($item->type === CashAccountType::BANK && $item->bank_name) {
                    $bankSub = '<small class="text-muted d-block">' . e($item->bank_name) . ($item->account_number ? ' &bull; ' . e($item->account_number) : '') . '</small>';
                }

                return '<div class="d-flex align-items-center">
                    <div class="avatar avatar-sm bg-label-' . $badgeColor . ' me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded">
                        <i class="icon-base bx ' . $iconClass . ' font-18"></i>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="text-heading fw-bold fs-6">' . e($name) . '</span>
                        <small class="text-muted font-monospace fw-semibold">' . e($item->code) . '</small>' . $bankSub . '
                    </div>
                </div>';
            })
            ->addColumn('entity_name', function (CashAccount $item) {
                $entityName = $item->entity?->translate(app()->getLocale())?->name
                    ?? $item->entity?->translate('ar')?->name
                    ?? $item->entity?->code
                    ?? '-';

                return '<span class="badge bg-label-secondary fw-semibold">' . e($entityName) . '</span>';
            })
            ->addColumn('type_badge', function (CashAccount $item) {
                if ($item->type === CashAccountType::CASH) {
                    return '<span class="badge bg-label-success d-inline-flex align-items-center"><i class="icon-base bx bx-wallet me-1 font-12"></i>' . __('Cash Register') . '</span>';
                }
                return '<span class="badge bg-label-primary d-inline-flex align-items-center"><i class="icon-base bx bx-building me-1 font-12"></i>' . __('Bank Account') . '</span>';
            })
            ->addColumn('gl_account', function (CashAccount $item) {
                if (!$item->account) {
                    return '<span class="text-muted">-</span>';
                }
                $accName = $item->account->translate(app()->getLocale())?->name
                    ?? $item->account->translate('ar')?->name
                    ?? '-';
                return '<span class="fw-bold font-monospace text-heading">' . e($item->account->code) . '</span> <small class="text-muted d-block">' . e($accName) . '</small>';
            })
            ->addColumn('responsible_person', function (CashAccount $item) {
                return $item->responsibleUser?->name ?? '<span class="text-muted">-</span>';
            })
            ->addColumn('currency_display', function (CashAccount $item) {
                return '<span class="badge bg-label-dark font-monospace">' . e($item->currency ?? 'SAR') . '</span>';
            })
            ->addColumn('status_badge', function (CashAccount $item) {
                if ($item->is_active) {
                    return '<span class="badge bg-label-success d-inline-flex align-items-center"><i class="icon-base bx bx-check-circle me-1 font-12"></i>' . __('Active') . '</span>';
                }
                return '<span class="badge bg-label-danger d-inline-flex align-items-center"><i class="icon-base bx bx-x-circle me-1 font-12"></i>' . __('Inactive') . '</span>';
            })
            ->addColumn('actions', function (CashAccount $item) {
                $user = auth()->user();
                $locale = app()->getLocale();
                $name = $item->translate($locale)?->name ?? $item->code;
                $editUrl = route('accounting.cash-accounts.edit', $item->id);
                $showUrl = route('accounting.cash-accounts.show', $item->id);

                $showBtn = '';
                if ($user->can('accounting.cash-accounts.view')) {
                    $showBtn = '<a href="' . $showUrl . '" class="btn btn-icon btn-sm btn-outline-info" title="' . __('View') . '"><i class="icon-base bx bx-show"></i></a>';
                }

                $editBtn = '';
                if ($user->can('accounting.cash-accounts.edit')) {
                    $editBtn = '<a href="' . $editUrl . '" class="btn btn-icon btn-sm btn-outline-primary" title="' . __('Edit') . '"><i class="icon-base bx bx-edit"></i></a>';
                }

                $toggleBtn = '';
                if ($user->can('accounting.cash-accounts.toggle-status')) {
                    $toggleBtn = '<button type="button" class="btn btn-icon btn-sm btn-outline-warning btn-toggle-status" data-id="' . $item->id . '" title="' . __('Change Status') . '"><i class="icon-base bx bx-power-off"></i></button>';
                }

                $deleteBtn = '';
                if ($user->can('accounting.cash-accounts.delete')) {
                    $deleteBtn = '<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete" data-id="' . $item->id . '" data-name="' . e($name) . '" title="' . __('Delete') . '"><i class="icon-base bx bx-trash"></i></button>';
                }

                $buttons = $showBtn . $editBtn . $toggleBtn . $deleteBtn;
                return $buttons ? '<div class="d-inline-flex gap-1">' . $buttons . '</div>' : '<span class="text-muted">---</span>';
            })
            ->rawColumns(['account_info', 'entity_name', 'type_badge', 'gl_account', 'responsible_person', 'currency_display', 'status_badge', 'actions'])
            ->make(true);
    }

    //--------------------------------------------------
    public function store(StoreCashAccountRequest $request): RedirectResponse
    {
        $cashAccount = $this->service->create($request->validated());

        return redirect()
            ->route('accounting.cash-accounts.index')
            ->with('success', __('Cash Account created successfully.'));
    }

    //--------------------------------------------------
    public function show(int $id): View
    {
        $cashAccount = $this->service->getById($id);

        return view('accounting::cash-accounts.show', [
            'cashAccount' => $cashAccount,
        ]);
    }

    //--------------------------------------------------
    public function edit(int $id): View
    {
        $cashAccount = $this->service->getById($id);

        return view('accounting::cash-accounts.edit', [
            'cashAccount' => $cashAccount,
            'entities' => Entity::with('translations')->orderBy('id')->get(),
            'accounts' => Account::with('translations')->orderBy('entity_id')->orderBy('code')->get(),
            'journals' => Journal::with('translations')->orderBy('entity_id')->orderBy('code')->get(),
            'users' => User::orderBy('name')->get(),
            'types' => CashAccountType::cases(),
        ]);
    }

    //--------------------------------------------------
    public function update(UpdateCashAccountRequest $request, int $id): RedirectResponse
    {
        $this->service->update($id, $request->validated());

        return redirect()
            ->route('accounting.cash-accounts.index')
            ->with('success', __('Cash Account updated successfully.'));
    }

    //--------------------------------------------------
    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => __('Cash Account deleted successfully.'),
        ]);
    }

    //--------------------------------------------------
    public function toggleStatus(int $id): JsonResponse
    {
        $cashAccount = $this->service->toggleStatus($id);

        return response()->json([
            'success' => true,
            'message' => __('Status updated successfully.'),
            'is_active' => $cashAccount->is_active,
        ]);
    }
}
