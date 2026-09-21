<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Settings\Http\Requests\StoreBankBranchRequest;
use Modules\Settings\Http\Requests\StoreBankRequest;
use Modules\Settings\Http\Requests\UpdateBankBranchRequest;
use Modules\Settings\Http\Requests\UpdateBankRequest;
use Modules\Settings\Models\Bank;
use Modules\Settings\Services\BankService;
use Yajra\DataTables\Facades\DataTables;

class BankController extends Controller
{
    public function __construct(
        protected BankService $bankService
    ) {}

    // =========================================================================
    // 1. Banks (البنوك الرئيسية)
    // =========================================================================

    /**
     * Display the banks listing view.
     */
    public function index(): View
    {
        return view('settings::general.banks.index');
    }

    /**
     * Server-side DataTables endpoint for Banks.
     */
    public function data(Request $request): JsonResponse
    {
        $query = $this->bankService->getMainBanksDataTableQuery($request->all());

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('name_ar', fn(Bank $bank) => $bank->translate('ar')?->name ?? '-')
            ->addColumn('name_en', fn(Bank $bank) => $bank->translate('en')?->name ?? '-')
            ->addColumn('branches_count', fn(Bank $bank) => $bank->branches_count ?? 0)
            ->editColumn('code', fn(Bank $bank) => $bank->code ?: '-')
            ->editColumn('swift_code', fn(Bank $bank) => $bank->swift_code 
                ? '<span class="badge bg-label-info">' . e($bank->swift_code) . '</span>' 
                : '<span class="text-muted">-</span>'
            )
            ->editColumn('status', function (Bank $bank) {
                if ($bank->status === 'active') {
                    return '<span class="badge bg-label-success">' . __('Active') . '</span>';
                }
                return '<span class="badge bg-label-danger">' . __('Inactive') . '</span>';
            })
            ->addColumn('status_raw', fn(Bank $bank) => $bank->status)
            ->addColumn('address_ar', fn(Bank $bank) => $bank->translate('ar')?->address ?? '')
            ->addColumn('address_en', fn(Bank $bank) => $bank->translate('en')?->address ?? '')
            ->editColumn('created_at', fn(Bank $bank) => $bank->created_at ? $bank->created_at->format('Y-m-d') : '-')
            ->addColumn('actions', function (Bank $bank) {
                return '
                    <div class="d-inline-flex gap-1">
                        <button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-bank"
                                title="' . __('Edit') . '"
                                data-id="' . $bank->id . '"
                                data-name-ar="' . e($bank->translate('ar')?->name ?? '') . '"
                                data-name-en="' . e($bank->translate('en')?->name ?? '') . '"
                                data-code="' . e($bank->code ?? '') . '"
                                data-swift-code="' . e($bank->swift_code ?? '') . '"
                                data-status="' . $bank->status . '"
                                data-address-ar="' . e($bank->translate('ar')?->address ?? '') . '"
                                data-address-en="' . e($bank->translate('en')?->address ?? '') . '">
                            <i class="icon-base bx bx-edit-alt"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-bank"
                                title="' . __('Delete') . '"
                                data-id="' . $bank->id . '"
                                data-name="' . e($bank->name ?? '') . '">
                            <i class="icon-base bx bx-trash"></i>
                        </button>
                    </div>';
            })
            ->rawColumns(['swift_code', 'status', 'actions'])
            ->filterColumn('name_ar', function ($query, $keyword) {
                $query->whereTranslationLike('name', "%{$keyword}%", 'ar');
            })
            ->filterColumn('name_en', function ($query, $keyword) {
                $query->whereTranslationLike('name', "%{$keyword}%", 'en');
            })
            ->make(true);
    }

    /**
     * Store a newly created Bank.
     */
    public function store(StoreBankRequest $request): RedirectResponse
    {
        try {
            $this->bankService->createBank($request->validated(), auth()->id());

            return redirect()->route('settings.general.banks.index')
                ->with('success', __('Bank created successfully.'));
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('An error occurred: ') . $e->getMessage());
        }
    }

    /**
     * Update an existing Bank.
     */
    public function update(UpdateBankRequest $request, int $id): RedirectResponse
    {
        try {
            $bank = $this->bankService->getById($id);
            if (!$bank) {
                return redirect()->route('settings.general.banks.index')
                    ->with('error', __('Bank not found.'));
            }

            $this->bankService->updateBank($bank, $request->validated());

            return redirect()->route('settings.general.banks.index')
                ->with('success', __('Bank updated successfully.'));
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('An error occurred: ') . $e->getMessage());
        }
    }

    /**
     * Delete Bank.
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $bank = $this->bankService->getById($id);
            if (!$bank) {
                return redirect()->route('settings.general.banks.index')
                    ->with('error', __('Bank not found.'));
            }

            $this->bankService->deleteBank($bank);

            return redirect()->route('settings.general.banks.index')
                ->with('success', __('Bank deleted successfully.'));
        } catch (Exception $e) {
            return redirect()->route('settings.general.banks.index')
                ->with('error', __('An error occurred: ') . $e->getMessage());
        }
    }

    // =========================================================================
    // 2. Bank Branches (فروع البنوك)
    // =========================================================================

    /**
     * Display the bank branches listing view.
     */
    public function branches(): View
    {
        $mainBanks = $this->bankService->getMainBanksList(false);

        return view('settings::general.bank_branches.index', [
            'mainBanks' => $mainBanks,
        ]);
    }

    /**
     * Server-side DataTables endpoint for Bank Branches.
     */
    public function branchesData(Request $request): JsonResponse
    {
        $query = $this->bankService->getBankBranchesDataTableQuery($request->all());

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('bank_name', fn(Bank $branch) => $branch->parent?->name ?? '<span class="text-muted">' . __('Unknown Bank') . '</span>')
            ->addColumn('parent_id', fn(Bank $branch) => $branch->parent_id)
            ->addColumn('name_ar', fn(Bank $branch) => $branch->translate('ar')?->name ?? '-')
            ->addColumn('name_en', fn(Bank $branch) => $branch->translate('en')?->name ?? '-')
            ->editColumn('code', fn(Bank $branch) => $branch->code ?: '-')
            ->editColumn('swift_code', fn(Bank $branch) => $branch->swift_code 
                ? '<span class="badge bg-label-info">' . e($branch->swift_code) . '</span>' 
                : '<span class="text-muted">-</span>'
            )
            ->editColumn('status', function (Bank $branch) {
                if ($branch->status === 'active') {
                    return '<span class="badge bg-label-success">' . __('Active') . '</span>';
                }
                return '<span class="badge bg-label-danger">' . __('Inactive') . '</span>';
            })
            ->addColumn('status_raw', fn(Bank $branch) => $branch->status)
            ->addColumn('address_ar', fn(Bank $branch) => $branch->translate('ar')?->address ?? '')
            ->addColumn('address_en', fn(Bank $branch) => $branch->translate('en')?->address ?? '')
            ->editColumn('created_at', fn(Bank $branch) => $branch->created_at ? $branch->created_at->format('Y-m-d') : '-')
            ->addColumn('actions', function (Bank $branch) {
                return '
                    <div class="d-inline-flex gap-1">
                        <button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-branch"
                                title="' . __('Edit') . '"
                                data-id="' . $branch->id . '"
                                data-parent-id="' . ($branch->parent_id ?? '') . '"
                                data-name-ar="' . e($branch->translate('ar')?->name ?? '') . '"
                                data-name-en="' . e($branch->translate('en')?->name ?? '') . '"
                                data-code="' . e($branch->code ?? '') . '"
                                data-swift-code="' . e($branch->swift_code ?? '') . '"
                                data-status="' . $branch->status . '"
                                data-address-ar="' . e($branch->translate('ar')?->address ?? '') . '"
                                data-address-en="' . e($branch->translate('en')?->address ?? '') . '">
                            <i class="icon-base bx bx-edit-alt"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-branch"
                                title="' . __('Delete') . '"
                                data-id="' . $branch->id . '"
                                data-name="' . e($branch->name ?? '') . '">
                            <i class="icon-base bx bx-trash"></i>
                        </button>
                    </div>';
            })
            ->rawColumns(['bank_name', 'swift_code', 'status', 'actions'])
            ->filterColumn('name_ar', function ($query, $keyword) {
                $query->whereTranslationLike('name', "%{$keyword}%", 'ar');
            })
            ->filterColumn('name_en', function ($query, $keyword) {
                $query->whereTranslationLike('name', "%{$keyword}%", 'en');
            })
            ->make(true);
    }

    /**
     * Store a newly created Bank Branch.
     */
    public function storeBranch(StoreBankBranchRequest $request): RedirectResponse
    {
        try {
            $this->bankService->createBank($request->validated(), auth()->id());

            return redirect()->route('settings.general.bank-branches.index')
                ->with('success', __('Bank Branch created successfully.'));
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('An error occurred: ') . $e->getMessage());
        }
    }

    /**
     * Update an existing Bank Branch.
     */
    public function updateBranch(UpdateBankBranchRequest $request, int $id): RedirectResponse
    {
        try {
            $branch = $this->bankService->getById($id);
            if (!$branch) {
                return redirect()->route('settings.general.bank-branches.index')
                    ->with('error', __('Bank Branch not found.'));
            }

            $this->bankService->updateBank($branch, $request->validated());

            return redirect()->route('settings.general.bank-branches.index')
                ->with('success', __('Bank Branch updated successfully.'));
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('An error occurred: ') . $e->getMessage());
        }
    }

    /**
     * Delete Bank Branch.
     */
    public function destroyBranch(int $id): RedirectResponse
    {
        try {
            $branch = $this->bankService->getById($id);
            if (!$branch) {
                return redirect()->route('settings.general.bank-branches.index')
                    ->with('error', __('Bank Branch not found.'));
            }

            $this->bankService->deleteBank($branch);

            return redirect()->route('settings.general.bank-branches.index')
                ->with('success', __('Bank Branch deleted successfully.'));
        } catch (Exception $e) {
            return redirect()->route('settings.general.bank-branches.index')
                ->with('error', __('An error occurred: ') . $e->getMessage());
        }
    }
}
