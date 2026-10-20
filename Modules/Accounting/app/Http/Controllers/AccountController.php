<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Accounting\Enums\AccountType;
use Modules\Accounting\Http\Requests\ImportAccountsRequest;
use Modules\Accounting\Http\Requests\StoreAccountRequest;
use Modules\Accounting\Http\Requests\UpdateAccountRequest;
use Modules\Accounting\Models\Account;
use Modules\Accounting\Models\Entity;
use Modules\Accounting\Services\AccountImportExportService;
use Modules\Accounting\Services\AccountService;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Yajra\DataTables\Facades\DataTables;

class AccountController extends Controller
{
    public function __construct(
        protected AccountService $accountService
    ) {}

    //==================================================
    /**
     * Display a listing of the chart of accounts.
     */
    public function index(Request $request): View
    {
        $entities = Entity::with('translations')->where('status', 'active')->orderBy('id')->get();
        $rawEntityId = (int) $request->get('entity_id');
        $selectedEntity = ($rawEntityId > 0 ? $entities->firstWhere('id', $rawEntityId) : null) ?: $entities->first();
        $selectedEntityId = $selectedEntity?->id ?? 0;

        // Account statistics for KPI cards
        $stats = [
            'total'       => 0,
            'groups'      => 0,
            'postable'    => 0,
            'assets'      => 0,
            'liabilities' => 0,
            'equity'      => 0,
            'income'      => 0,
            'expenses'    => 0,
            'deprecated'  => 0,
        ];

        $accountTree = collect();
        $potentialParents = collect();

        if ($selectedEntityId) {
            $accounts = Account::where('entity_id', $selectedEntityId)->get();
            $stats['total']       = $accounts->count();
            $stats['groups']      = $accounts->where('is_group', true)->count();
            $stats['postable']    = $accounts->where('is_group', false)->count();
            $stats['assets']      = $accounts->where('account_type', AccountType::ASSET)->count();
            $stats['liabilities'] = $accounts->where('account_type', AccountType::LIABILITY)->count();
            $stats['equity']      = $accounts->where('account_type', AccountType::EQUITY)->count();
            $stats['income']      = $accounts->where('account_type', AccountType::INCOME)->count();
            $stats['expenses']    = $accounts->where('account_type', AccountType::EXPENSE)->count();
            $stats['deprecated']  = $accounts->where('is_deprecated', true)->count();

            $accountTree = $this->accountService->getAccountTree($selectedEntityId);
            $potentialParents = $this->accountService->getPotentialParents($selectedEntityId);
        }

        $accountTypes = AccountType::cases();

        return view('accounting::accounts.index', [
            'entities'         => $entities,
            'selectedEntity'   => $selectedEntity,
            'selectedEntityId' => $selectedEntityId,
            'stats'            => $stats,
            'accountTree'      => $accountTree,
            'potentialParents' => $potentialParents,
            'accountTypes'     => $accountTypes,
        ]);
    }

    //==================================================
    /**
     * JSON Tree data endpoint for dynamic tree reloading.
     */
    public function treeData(Request $request): JsonResponse
    {
        $entityId = (int) $request->get('entity_id');
        if (!$entityId) {
            $entityId = Entity::where('status', 'active')->orderBy('id')->first()?->id ?? 0;
        }
        if (!$entityId) {
            return response()->json(['success' => false, 'message' => __('Entity required')], 422);
        }

        $tree = $this->accountService->getAccountTree($entityId);

        return response()->json([
            'success' => true,
            'data'    => $tree,
        ]);
    }

    //==================================================
    /**
     * Server-side DataTables endpoint for flat table listing.
     */
    public function data(Request $request): JsonResponse
    {
        $entityId = (int) $request->get('entity_id');

        $query = Account::query()
            ->with(['translations', 'parent.translations'])
            ->when($entityId, fn($q) => $q->where('entity_id', $entityId))
            ->when($request->filled('account_type'), fn($q) => $q->where('account_type', $request->account_type))
            ->when($request->filled('is_group'), function ($q) use ($request) {
                if ($request->is_group !== '' && $request->is_group !== 'all') {
                    $q->where('is_group', filter_var($request->is_group, FILTER_VALIDATE_BOOLEAN));
                }
            })
            ->when($request->filled('is_active'), function ($q) use ($request) {
                if ($request->is_active !== '' && $request->is_active !== 'all') {
                    $q->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
                }
            })
            ->when($request->filled('is_deprecated'), function ($q) use ($request) {
                if ($request->is_deprecated !== '' && $request->is_deprecated !== 'all') {
                    $q->where('is_deprecated', filter_var($request->is_deprecated, FILTER_VALIDATE_BOOLEAN));
                }
            })
            ->orderBy('sort_order')
            ->orderBy('code');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('account_name', function (Account $item) {
                $locale = app()->getLocale();
                $name = $item->translate($locale)?->name 
                     ?? ($locale === 'ar' ? $item->translate('en')?->name : $item->translate('ar')?->name) 
                     ?? '-';
                return '<span class="fw-semibold text-heading">' . e($name) . '</span>';
            })
            ->addColumn('code_formatted', function (Account $item) {
                return '<span class="fw-bold text-heading font-monospace">' . e($item->code) . '</span>';
            })
            ->addColumn('parent_name', function (Account $item) {
                if (!$item->parent) {
                    return '<span class="text-muted">-</span>';
                }
                $locale = app()->getLocale();
                $parentName = $item->parent->translate($locale)?->name 
                           ?? ($locale === 'ar' ? $item->parent->translate('en')?->name : $item->parent->translate('ar')?->name) 
                           ?? '-';
                return '<span class="text-body">' . e($parentName) . '</span>';
            })
            ->addColumn('type_badge', function (Account $item) {
                $type = $item->account_type;
                $colors = [
                    'asset'       => 'primary',
                    'liability'   => 'danger',
                    'equity'      => 'warning',
                    'income'      => 'success',
                    'expense'     => 'info',
                    'off_balance' => 'secondary',
                ];
                $color = $colors[$type->value] ?? 'secondary';
                return '<span class="badge bg-label-' . $color . '">' . e($type->label()) . '</span>';
            })
            ->addColumn('balance_badge', function (Account $item) {
                if ($item->normal_balance === 'debit') {
                    return '<span class="badge bg-label-success">' . __('Debit') . '</span>';
                }
                return '<span class="badge bg-label-info">' . __('Credit') . '</span>';
            })
            ->addColumn('nature_badge', function (Account $item) {
                if ($item->is_group) {
                    return '<span class="badge bg-label-secondary d-inline-flex align-items-center"><i class="icon-base bx bx-folder me-1 font-12"></i>' . __('Group') . '</span>';
                }
                return '<span class="badge bg-label-primary d-inline-flex align-items-center"><i class="icon-base bx bx-receipt me-1 font-12"></i>' . __('Posting') . '</span>';
            })
            ->addColumn('status_badges', function (Account $item) {
                if ($item->is_deprecated) {
                    return '<span class="badge bg-label-secondary d-inline-flex align-items-center"><i class="icon-base bx bx-archive me-1 font-12"></i>' . __('Deprecated') . '</span>';
                }
                if ($item->is_active) {
                    return '<span class="badge bg-label-success d-inline-flex align-items-center"><i class="icon-base bx bx-check-circle me-1 font-12"></i>' . __('Active') . '</span>';
                }
                return '<span class="badge bg-label-danger d-inline-flex align-items-center"><i class="icon-base bx bx-x-circle me-1 font-12"></i>' . __('Inactive') . '</span>';
            })
            ->addColumn('actions', function (Account $item) {
                $user = auth()->user();

                $addSubBtn = '';
                if ($item->is_group && $user->can('accounting.accounts.create')) {
                    $addSubBtn = '<button type="button" class="btn btn-icon btn-sm btn-outline-success btn-quick-add-child" data-parent-id="' . $item->id . '" data-parent-code="' . e($item->code) . '" data-type="' . $item->account_type->value . '" title="' . __('Add Sub-Account') . '">
                        <i class="icon-base bx bx-plus"></i>
                    </button>';
                }

                $editBtn = '';
                if ($user->can('accounting.accounts.edit')) {
                    $editBtn = '<button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-account" data-id="' . $item->id . '" title="' . __('Edit') . '">
                        <i class="icon-base bx bx-edit"></i>
                    </button>';
                }

                $deprecateBtn = '';
                if ($user->can('accounting.accounts.toggle-status')) {
                    $deprecateIcon = $item->is_deprecated ? 'bx-undo' : 'bx-archive';
                    $deprecateTitle = $item->is_deprecated ? __('Reactivate Account') : __('Deprecate Account');
                    $deprecateBtn = '<button type="button" class="btn btn-icon btn-sm btn-outline-warning btn-deprecate-account" data-id="' . $item->id . '" data-deprecated="' . ($item->is_deprecated ? '1' : '0') . '" data-code="' . e($item->code) . '" data-name="' . e($item->translateOrNew(app()->getLocale())->name ?? '') . '" title="' . $deprecateTitle . '">
                        <i class="icon-base bx ' . $deprecateIcon . '"></i>
                    </button>';
                }

                $deleteBtn = '';
                if ($user->can('accounting.accounts.delete')) {
                    $deleteBtn = '<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-account" data-id="' . $item->id . '" data-has-children="' . ($item->children()->exists() ? '1' : '0') . '" data-code="' . e($item->code) . '" title="' . __('Delete') . '">
                        <i class="icon-base bx bx-trash"></i>
                    </button>';
                }

                $buttons = $addSubBtn . $editBtn . $deprecateBtn . $deleteBtn;
                return $buttons ? '<div class="d-inline-flex gap-1">' . $buttons . '</div>' : '<span class="text-muted">---</span>';
            })
            ->rawColumns(['account_name', 'code_formatted', 'parent_name', 'type_badge', 'balance_badge', 'nature_badge', 'status_badges', 'actions'])
            ->make(true);
    }

    //==================================================
    /**
     * Store a newly created account.
     */
    public function store(StoreAccountRequest $request): JsonResponse
    {
        try {
            $account = $this->accountService->createAccount($request->validated(), auth()->id());

            return response()->json([
                'success' => true,
                'message' => __('Account created successfully.'),
                'data'    => $account->load(['translations', 'parent']),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    //==================================================
    /**
     * Display the specified account data for view or edit.
     */
    public function show(Account $account): JsonResponse
    {
        $account->load(['translations', 'parent.translations', 'creator', 'children.translations']);

        $locale = app()->getLocale();
        $nameAr = $account->translateOrNew('ar')->name ?? '';
        $nameEn = $account->translateOrNew('en')->name ?? '';
        $descAr = $account->translateOrNew('ar')->description ?? '';
        $descEn = $account->translateOrNew('en')->description ?? '';

        // Build hierarchical path
        $path = [];
        $curr = $account;
        while ($curr) {
            $currName = $curr->translate($locale)?->name ?? $curr->translate('ar')?->name ?? $curr->code;
            array_unshift($path, ['code' => $curr->code, 'name' => $currName]);
            $curr = $curr->parent;
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'             => $account->id,
                'entity_id'      => $account->entity_id,
                'parent_id'      => $account->parent_id,
                'code'           => $account->code,
                'account_type'   => $account->account_type->value,
                'account_type_label' => $account->account_type->label(),
                'normal_balance' => $account->normal_balance,
                'level'          => $account->level,
                'is_group'       => (bool) $account->is_group,
                'allow_posting'  => (bool) $account->allow_posting,
                'reconcile'      => (bool) $account->reconcile,
                'is_active'      => (bool) $account->is_active,
                'is_deprecated'  => (bool) $account->is_deprecated,
                'sort_order'     => $account->sort_order,
                'name_ar'        => $nameAr,
                'name_en'        => $nameEn,
                'description_ar' => $descAr,
                'description_en' => $descEn,
                'created_by'     => $account->creator?->name ?? '-',
                'created_at'     => $account->created_at?->format('Y-m-d H:i') ?? '-',
                'children_count' => $account->children->count(),
                'path'           => $path,
            ],
        ]);
    }

    //==================================================
    /**
     * Update the specified account.
     */
    public function update(UpdateAccountRequest $request, Account $account): JsonResponse
    {
        try {
            $updated = $this->accountService->updateAccount($account, $request->validated());

            return response()->json([
                'success' => true,
                'message' => __('Account updated successfully.'),
                'data'    => $updated,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    //==================================================
    /**
     * Remove the specified account.
     */
    public function destroy(Account $account): JsonResponse
    {
        try {
            $deleted = $this->accountService->deleteAccount($account);

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => __('Account deleted successfully.'),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => __('Could not delete account.'),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    //==================================================
    /**
     * Toggle status (Active / Inactive).
     */
    public function toggleStatus(Account $account): JsonResponse
    {
        try {
            $updated = $this->accountService->toggleStatus($account);
            $msg = $updated->is_active ? __('Account activated.') : __('Account deactivated.');

            return response()->json([
                'success'   => true,
                'is_active' => $updated->is_active,
                'message'   => $msg,
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    //==================================================
    /**
     * Toggle deprecated status (for accounts with history).
     */
    public function toggleDeprecated(Account $account): JsonResponse
    {
        try {
            $updated = $this->accountService->toggleDeprecated($account);
            $msg = $updated->is_deprecated ? __('Account marked as deprecated.') : __('Account restored from deprecated.');

            return response()->json([
                'success'       => true,
                'is_deprecated' => $updated->is_deprecated,
                'message'       => $msg,
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    //==================================================
    /**
     * Suggest next logical code for account.
     */
    public function suggestCode(Request $request): JsonResponse
    {
        $entityId = (int) $request->get('entity_id');
        $parentId = $request->filled('parent_id') ? (int) $request->get('parent_id') : null;

        if (!$entityId) {
            return response()->json(['success' => false, 'message' => __('Entity required')], 422);
        }

        $code = $this->accountService->suggestCode($entityId, $parentId);

        return response()->json([
            'success' => true,
            'code'    => $code,
        ]);
    }

    //==================================================
    /**
     * Get group accounts list for parent dropdown.
     */
    public function parents(Request $request): JsonResponse
    {
        $entityId = (int) $request->get('entity_id');
        $excludeId = $request->filled('exclude_id') ? (int) $request->get('exclude_id') : null;

        if (!$entityId) {
            return response()->json(['success' => false, 'parents' => []]);
        }

        $parents = $this->accountService->getPotentialParents($entityId, $excludeId);

        $locale = app()->getLocale();
        $formatted = $parents->map(function (Account $item) use ($locale) {
            $name = $item->translate($locale)?->name ?? $item->translate('ar')?->name ?? '-';
            return [
                'id'           => $item->id,
                'code'         => $item->code,
                'level'        => $item->level,
                'name'         => $name,
                'account_type' => $item->account_type->value,
                'display'      => str_repeat('— ', max(0, $item->level - 1)) . $item->code . ' - ' . $name,
            ];
        });

        return response()->json([
            'success' => true,
            'parents' => $formatted,
        ]);
    }

    //==================================================
    /**
     * Download standard Excel (.xlsx) template for accounts import.
     */
    public function downloadTemplate(AccountImportExportService $importExportService): StreamedResponse
    {
        return $importExportService->downloadTemplate();
    }

    //==================================================
    /**
     * Export all accounts of the specified entity to Excel (.xlsx).
     */
    public function export(Request $request, AccountImportExportService $importExportService): StreamedResponse
    {
        $entityId = (int) $request->get('entity_id');
        if (!$entityId) {
            abort(404, __('Entity required.'));
        }

        return $importExportService->exportEntityAccounts($entityId);
    }

    //==================================================
    /**
     * Import accounts from an uploaded spreadsheet file (.xlsx, .xls, .csv).
     */
    public function import(ImportAccountsRequest $request, AccountImportExportService $importExportService): JsonResponse
    {
        try {
            $result = $importExportService->importAccounts(
                $request->file('file'),
                (int) $request->input('entity_id'),
                auth()->id()
            );

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
