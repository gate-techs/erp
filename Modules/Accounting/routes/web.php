<?php

use Modules\Accounting\Http\Controllers\AccountingDashboardController;
use Modules\Accounting\Http\Controllers\AccountController;
use Modules\Accounting\Http\Controllers\EntityController;
use Modules\Accounting\Http\Controllers\FiscalYearController;
use Modules\Accounting\Http\Controllers\LockDateController;
use Modules\Accounting\Http\Controllers\JournalController;
use Modules\Accounting\Http\Controllers\JournalEntryController;

Route::middleware(['auth', 'verified'])->prefix('accounting')->as('accounting.')->group(function () {
    // Executive Dashboard (لوحة التحكم المحاسبية والمالية)
    Route::get('/', [AccountingDashboardController::class, 'index'])->name('index')->middleware('permission:accounting.dashboard.view');
    Route::get('dashboard', [AccountingDashboardController::class, 'index'])->name('dashboard')->middleware('permission:accounting.dashboard.view');
    Route::get('guide', [\Modules\Accounting\Http\Controllers\AccountingGuideController::class, 'index'])->name('guide')->middleware('permission:accounting.guide.view');

    // Entities (الكيانات المحاسبية)
    Route::middleware('permission:accounting.entities.view')->group(function () {
        Route::get('entities/data', [EntityController::class, 'data'])->name('entities.data');
        Route::get('entities', [EntityController::class, 'index'])->name('entities.index');
        Route::get('entities/{entity}', [EntityController::class, 'show'])->name('entities.show');
    });
    Route::middleware('permission:accounting.entities.create')->group(function () {
        Route::get('entities/create', [EntityController::class, 'create'])->name('entities.create');
        Route::post('entities', [EntityController::class, 'store'])->name('entities.store');
    });
    Route::middleware('permission:accounting.entities.edit')->group(function () {
        Route::get('entities/{entity}/edit', [EntityController::class, 'edit'])->name('entities.edit');
        Route::match(['put', 'patch'], 'entities/{entity}', [EntityController::class, 'update'])->name('entities.update');
    });
    Route::delete('entities/{entity}', [EntityController::class, 'destroy'])->name('entities.destroy')->middleware('permission:accounting.entities.delete');

    // Fiscal Years (السنوات المالية)
    Route::middleware('permission:accounting.fiscal-years.view')->group(function () {
        Route::get('fiscal-years/data', [FiscalYearController::class, 'data'])->name('fiscal-years.data');
        Route::get('fiscal-years', [FiscalYearController::class, 'index'])->name('fiscal-years.index');
        Route::get('fiscal-years/{fiscal_year}', [FiscalYearController::class, 'show'])->name('fiscal-years.show');
    });
    Route::middleware('permission:accounting.fiscal-years.create')->group(function () {
        Route::get('fiscal-years/create', [FiscalYearController::class, 'create'])->name('fiscal-years.create');
        Route::post('fiscal-years', [FiscalYearController::class, 'store'])->name('fiscal-years.store');
    });
    Route::middleware('permission:accounting.fiscal-years.edit')->group(function () {
        Route::get('fiscal-years/{fiscal_year}/edit', [FiscalYearController::class, 'edit'])->name('fiscal-years.edit');
        Route::match(['put', 'patch'], 'fiscal-years/{fiscal_year}', [FiscalYearController::class, 'update'])->name('fiscal-years.update');
    });
    Route::delete('fiscal-years/{fiscal_year}', [FiscalYearController::class, 'destroy'])->name('fiscal-years.destroy')->middleware('permission:accounting.fiscal-years.delete');
    Route::post('fiscal-years/{id}/toggle-status', [FiscalYearController::class, 'toggleStatus'])->name('fiscal-years.toggle-status')->middleware('permission:accounting.fiscal-years.toggle-status');

    // Lock Dates and Exceptions (تواريخ الإقفال وحماية الفترات)
    Route::get('lock-dates', [LockDateController::class, 'index'])->name('lock-dates.index')->middleware('permission:accounting.lock-dates.view');
    Route::put('lock-dates/{entityId}', [LockDateController::class, 'update'])->name('lock-dates.update')->middleware('permission:accounting.lock-dates.edit');
    Route::post('lock-dates/exceptions', [LockDateController::class, 'storeException'])->name('lock-dates.exceptions.store')->middleware('permission:accounting.lock-dates.exceptions.create');
    Route::post('lock-dates/exceptions/{id}/revoke', [LockDateController::class, 'revokeException'])->name('lock-dates.exceptions.revoke')->middleware('permission:accounting.lock-dates.exceptions.revoke');

    // Cost Centers (مراكز التكلفة)
    Route::middleware('permission:accounting.cost-centers.view')->group(function () {
        Route::get('cost-centers/data', [\Modules\Accounting\Http\Controllers\CostCenterController::class, 'data'])->name('cost-centers.data');
        Route::get('cost-centers/parents', [\Modules\Accounting\Http\Controllers\CostCenterController::class, 'getParentsByEntity'])->name('cost-centers.parents');
        Route::get('cost-centers/branches', [\Modules\Accounting\Http\Controllers\CostCenterController::class, 'getBranchesByEntity'])->name('cost-centers.branches');
        Route::get('cost-centers', [\Modules\Accounting\Http\Controllers\CostCenterController::class, 'index'])->name('cost-centers.index');
        Route::get('cost-centers/{cost_center}', [\Modules\Accounting\Http\Controllers\CostCenterController::class, 'show'])->name('cost-centers.show');
    });
    Route::middleware('permission:accounting.cost-centers.create')->group(function () {
        Route::get('cost-centers/create', [\Modules\Accounting\Http\Controllers\CostCenterController::class, 'create'])->name('cost-centers.create');
        Route::post('cost-centers', [\Modules\Accounting\Http\Controllers\CostCenterController::class, 'store'])->name('cost-centers.store');
    });
    Route::middleware('permission:accounting.cost-centers.edit')->group(function () {
        Route::get('cost-centers/{cost_center}/edit', [\Modules\Accounting\Http\Controllers\CostCenterController::class, 'edit'])->name('cost-centers.edit');
        Route::match(['put', 'patch'], 'cost-centers/{cost_center}', [\Modules\Accounting\Http\Controllers\CostCenterController::class, 'update'])->name('cost-centers.update');
    });
    Route::delete('cost-centers/{cost_center}', [\Modules\Accounting\Http\Controllers\CostCenterController::class, 'destroy'])->name('cost-centers.destroy')->middleware('permission:accounting.cost-centers.delete');
    Route::post('cost-centers/{id}/toggle-status', [\Modules\Accounting\Http\Controllers\CostCenterController::class, 'toggleStatus'])->name('cost-centers.toggle-status')->middleware('permission:accounting.cost-centers.toggle-status');

    // Journals (دفاتر اليومية)
    Route::middleware('permission:accounting.journals.view')->group(function () {
        Route::get('journals/data', [JournalController::class, 'data'])->name('journals.data');
        Route::get('journals', [JournalController::class, 'index'])->name('journals.index');
        Route::get('journals/{journal}', [JournalController::class, 'show'])->name('journals.show');
    });
    Route::middleware('permission:accounting.journals.create')->group(function () {
        Route::get('journals/create', [JournalController::class, 'create'])->name('journals.create');
        Route::post('journals', [JournalController::class, 'store'])->name('journals.store');
    });
    Route::middleware('permission:accounting.journals.edit')->group(function () {
        Route::get('journals/{journal}/edit', [JournalController::class, 'edit'])->name('journals.edit');
        Route::match(['put', 'patch'], 'journals/{journal}', [JournalController::class, 'update'])->name('journals.update');
    });
    Route::delete('journals/{journal}', [JournalController::class, 'destroy'])->name('journals.destroy')->middleware('permission:accounting.journals.delete');
    Route::post('journals/{id}/toggle-status', [JournalController::class, 'toggleStatus'])->name('journals.toggle-status')->middleware('permission:accounting.journals.toggle-status');

    // Cash & Bank Accounts (الخزائن والبنوك)
    Route::middleware('permission:accounting.cash-accounts.view')->group(function () {
        Route::get('cash-accounts/data', [\Modules\Accounting\Http\Controllers\CashAccountController::class, 'data'])->name('cash-accounts.data');
        Route::get('cash-accounts', [\Modules\Accounting\Http\Controllers\CashAccountController::class, 'index'])->name('cash-accounts.index');
        Route::get('cash-accounts/{cash_account}', [\Modules\Accounting\Http\Controllers\CashAccountController::class, 'show'])->name('cash-accounts.show');
    });
    Route::middleware('permission:accounting.cash-accounts.create')->group(function () {
        Route::get('cash-accounts/create', [\Modules\Accounting\Http\Controllers\CashAccountController::class, 'create'])->name('cash-accounts.create');
        Route::post('cash-accounts', [\Modules\Accounting\Http\Controllers\CashAccountController::class, 'store'])->name('cash-accounts.store');
    });
    Route::middleware('permission:accounting.cash-accounts.edit')->group(function () {
        Route::get('cash-accounts/{cash_account}/edit', [\Modules\Accounting\Http\Controllers\CashAccountController::class, 'edit'])->name('cash-accounts.edit');
        Route::match(['put', 'patch'], 'cash-accounts/{cash_account}', [\Modules\Accounting\Http\Controllers\CashAccountController::class, 'update'])->name('cash-accounts.update');
    });
    Route::delete('cash-accounts/{cash_account}', [\Modules\Accounting\Http\Controllers\CashAccountController::class, 'destroy'])->name('cash-accounts.destroy')->middleware('permission:accounting.cash-accounts.delete');
    Route::post('cash-accounts/{id}/toggle-status', [\Modules\Accounting\Http\Controllers\CashAccountController::class, 'toggleStatus'])->name('cash-accounts.toggle-status')->middleware('permission:accounting.cash-accounts.toggle-status');

    // Journal Entries (القيود اليومية)
    Route::middleware('permission:accounting.journal-entries.view')->group(function () {
        Route::get('journal-entries/data', [JournalEntryController::class, 'data'])->name('journal-entries.data');
        Route::get('journal-entries', [JournalEntryController::class, 'index'])->name('journal-entries.index');
        Route::get('journal-entries/{journal_entry}', [JournalEntryController::class, 'show'])->name('journal-entries.show');
    });
    Route::middleware('permission:accounting.journal-entries.create')->group(function () {
        Route::get('journal-entries/create', [JournalEntryController::class, 'create'])->name('journal-entries.create');
        Route::post('journal-entries', [JournalEntryController::class, 'store'])->name('journal-entries.store');
    });
    Route::middleware('permission:accounting.journal-entries.edit')->group(function () {
        Route::get('journal-entries/{journal_entry}/edit', [JournalEntryController::class, 'edit'])->name('journal-entries.edit');
        Route::match(['put', 'patch'], 'journal-entries/{journal_entry}', [JournalEntryController::class, 'update'])->name('journal-entries.update');
    });
    Route::delete('journal-entries/{journal_entry}', [JournalEntryController::class, 'destroy'])->name('journal-entries.destroy')->middleware('permission:accounting.journal-entries.delete');
    Route::post('journal-entries/{id}/post', [JournalEntryController::class, 'post'])->name('journal-entries.post')->middleware('permission:accounting.journal-entries.post');
    Route::post('journal-entries/{id}/cancel', [JournalEntryController::class, 'cancel'])->name('journal-entries.cancel')->middleware('permission:accounting.journal-entries.cancel');
    Route::post('journal-entries/{id}/reverse', [JournalEntryController::class, 'reverse'])->name('journal-entries.reverse')->middleware('permission:accounting.journal-entries.reverse');

    // Opening Balances (الأرصدة الافتتاحية)
    Route::prefix('opening-balances')->as('opening-balances.')->group(function () {
        Route::get('/', [\Modules\Accounting\Http\Controllers\OpeningBalanceController::class, 'index'])->name('index')->middleware('permission:accounting.opening-balances.view');
        Route::get('accounts', [\Modules\Accounting\Http\Controllers\OpeningBalanceController::class, 'getAccounts'])->name('accounts')->middleware('permission:accounting.opening-balances.view');
        Route::post('save', [\Modules\Accounting\Http\Controllers\OpeningBalanceController::class, 'save'])->name('save')->middleware('permission:accounting.opening-balances.create');
        Route::post('reopen', [\Modules\Accounting\Http\Controllers\OpeningBalanceController::class, 'reopen'])->name('reopen')->middleware('permission:accounting.opening-balances.reopen');
        Route::get('print', [\Modules\Accounting\Http\Controllers\OpeningBalanceController::class, 'print'])->name('print')->middleware('permission:accounting.opening-balances.print');
    });

    // Bank Reconciliations (التسويات البنكية)
    Route::middleware('permission:accounting.bank-reconciliations.view')->group(function () {
        Route::get('bank-reconciliations/data', [\Modules\Accounting\Http\Controllers\BankReconciliationController::class, 'data'])->name('bank-reconciliations.data');
        Route::get('bank-reconciliations/banks-by-entity', [\Modules\Accounting\Http\Controllers\BankReconciliationController::class, 'getBanksByEntity'])->name('bank-reconciliations.banks-by-entity');
        Route::get('bank-reconciliations/workspace-data', [\Modules\Accounting\Http\Controllers\BankReconciliationController::class, 'workspaceData'])->name('bank-reconciliations.workspace-data');
        Route::get('bank-reconciliations', [\Modules\Accounting\Http\Controllers\BankReconciliationController::class, 'index'])->name('bank-reconciliations.index');
        Route::get('bank-reconciliations/{bank_reconciliation}', [\Modules\Accounting\Http\Controllers\BankReconciliationController::class, 'show'])->name('bank-reconciliations.show');
    });
    Route::middleware('permission:accounting.bank-reconciliations.create')->group(function () {
        Route::get('bank-reconciliations/create', [\Modules\Accounting\Http\Controllers\BankReconciliationController::class, 'create'])->name('bank-reconciliations.create');
        Route::post('bank-reconciliations', [\Modules\Accounting\Http\Controllers\BankReconciliationController::class, 'store'])->name('bank-reconciliations.store');
    });
    Route::middleware('permission:accounting.bank-reconciliations.edit')->group(function () {
        Route::get('bank-reconciliations/{bank_reconciliation}/edit', [\Modules\Accounting\Http\Controllers\BankReconciliationController::class, 'edit'])->name('bank-reconciliations.edit');
        Route::match(['put', 'patch'], 'bank-reconciliations/{bank_reconciliation}', [\Modules\Accounting\Http\Controllers\BankReconciliationController::class, 'update'])->name('bank-reconciliations.update');
    });
    Route::delete('bank-reconciliations/{bank_reconciliation}', [\Modules\Accounting\Http\Controllers\BankReconciliationController::class, 'destroy'])->name('bank-reconciliations.destroy')->middleware('permission:accounting.bank-reconciliations.delete');
    Route::post('bank-reconciliations/{id}/reopen', [\Modules\Accounting\Http\Controllers\BankReconciliationController::class, 'reopen'])->name('bank-reconciliations.reopen')->middleware('permission:accounting.bank-reconciliations.reopen');
    Route::get('bank-reconciliations/{id}/print', [\Modules\Accounting\Http\Controllers\BankReconciliationController::class, 'print'])->name('bank-reconciliations.print')->middleware('permission:accounting.bank-reconciliations.print');

    // Payment Matchings (مطابقة وتخصيص المدفوعات)
    Route::middleware('permission:accounting.payment-matchings.view')->group(function () {
        Route::get('payment-matchings/data', [\Modules\Accounting\Http\Controllers\PaymentMatchingController::class, 'data'])->name('payment-matchings.data');
        Route::get('payment-matchings/accounts-by-entity', [\Modules\Accounting\Http\Controllers\PaymentMatchingController::class, 'getAccountsByEntity'])->name('payment-matchings.accounts-by-entity');
        Route::get('payment-matchings/unmatched-data', [\Modules\Accounting\Http\Controllers\PaymentMatchingController::class, 'unmatchedData'])->name('payment-matchings.unmatched-data');
        Route::get('payment-matchings', [\Modules\Accounting\Http\Controllers\PaymentMatchingController::class, 'index'])->name('payment-matchings.index');
    });
    Route::middleware('permission:accounting.payment-matchings.create')->group(function () {
        Route::get('payment-matchings/create', [\Modules\Accounting\Http\Controllers\PaymentMatchingController::class, 'create'])->name('payment-matchings.create');
        Route::post('payment-matchings', [\Modules\Accounting\Http\Controllers\PaymentMatchingController::class, 'store'])->name('payment-matchings.store');
    });
    Route::delete('payment-matchings/{id}', [\Modules\Accounting\Http\Controllers\PaymentMatchingController::class, 'destroy'])->name('payment-matchings.destroy')->middleware('permission:accounting.payment-matchings.delete');

    // Chart of Accounts (دليل / شجرة الحسابات)
    Route::middleware('permission:accounting.accounts.export')->group(function () {
        Route::get('accounts/template/download', [AccountController::class, 'downloadTemplate'])->name('accounts.template.download');
        Route::get('accounts/export', [AccountController::class, 'export'])->name('accounts.export');
    });
    Route::post('accounts/import', [AccountController::class, 'import'])->name('accounts.import')->middleware('permission:accounting.accounts.import');

    Route::middleware('permission:accounting.accounts.view')->group(function () {
        Route::get('accounts/data', [AccountController::class, 'data'])->name('accounts.data');
        Route::get('accounts/tree-data', [AccountController::class, 'treeData'])->name('accounts.tree-data');
        Route::get('accounts/suggest-code', [AccountController::class, 'suggestCode'])->name('accounts.suggest-code');
        Route::get('accounts/parents', [AccountController::class, 'parents'])->name('accounts.parents');
        Route::get('accounts', [AccountController::class, 'index'])->name('accounts.index');
        Route::get('accounts/{account}', [AccountController::class, 'show'])->name('accounts.show');
    });
    Route::middleware('permission:accounting.accounts.create')->group(function () {
        Route::get('accounts/create', [AccountController::class, 'create'])->name('accounts.create');
        Route::post('accounts', [AccountController::class, 'store'])->name('accounts.store');
    });
    Route::middleware('permission:accounting.accounts.edit')->group(function () {
        Route::get('accounts/{account}/edit', [AccountController::class, 'edit'])->name('accounts.edit');
        Route::match(['put', 'patch'], 'accounts/{account}', [AccountController::class, 'update'])->name('accounts.update');
    });
    Route::delete('accounts/{account}', [AccountController::class, 'destroy'])->name('accounts.destroy')->middleware('permission:accounting.accounts.delete');
    Route::post('accounts/{account}/toggle-status', [AccountController::class, 'toggleStatus'])->name('accounts.toggle-status')->middleware('permission:accounting.accounts.toggle-status');
    Route::post('accounts/{account}/toggle-deprecated', [AccountController::class, 'toggleDeprecated'])->name('accounts.toggle-deprecated')->middleware('permission:accounting.accounts.toggle-status');

    // Financial Reports (التقارير المالية)
    Route::prefix('reports')->as('reports.')->group(function () {
        Route::get('general-ledger', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'generalLedger'])->name('general-ledger')->middleware('permission:accounting.reports.general-ledger');
        Route::get('general-ledger/print', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'printGeneralLedger'])->name('general-ledger.print')->middleware('permission:accounting.reports.print');
        Route::get('general-ledger/export', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'exportGeneralLedger'])->name('general-ledger.export')->middleware('permission:accounting.reports.export');

        Route::get('trial-balance', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'trialBalance'])->name('trial-balance')->middleware('permission:accounting.reports.trial-balance');
        Route::get('trial-balance/print', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'printTrialBalance'])->name('trial-balance.print')->middleware('permission:accounting.reports.print');
        Route::get('trial-balance/export', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'exportTrialBalance'])->name('trial-balance.export')->middleware('permission:accounting.reports.export');

        Route::get('income-statement', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'incomeStatement'])->name('income-statement')->middleware('permission:accounting.reports.income-statement');
        Route::get('income-statement/print', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'printIncomeStatement'])->name('income-statement.print')->middleware('permission:accounting.reports.print');
        Route::get('income-statement/export', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'exportIncomeStatement'])->name('income-statement.export')->middleware('permission:accounting.reports.export');

        Route::get('balance-sheet', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'balanceSheet'])->name('balance-sheet')->middleware('permission:accounting.reports.balance-sheet');
        Route::get('balance-sheet/print', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'printBalanceSheet'])->name('balance-sheet.print')->middleware('permission:accounting.reports.print');
        Route::get('balance-sheet/export', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'exportBalanceSheet'])->name('balance-sheet.export')->middleware('permission:accounting.reports.export');

        Route::get('account-statement', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'accountStatement'])->name('account-statement')->middleware('permission:accounting.reports.account-statement');
        Route::get('account-statement/print', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'printAccountStatement'])->name('account-statement.print')->middleware('permission:accounting.reports.print');
        Route::get('account-statement/export', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'exportAccountStatement'])->name('account-statement.export')->middleware('permission:accounting.reports.export');

        Route::get('cash-flow', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'cashFlow'])->name('cash-flow')->middleware('permission:accounting.reports.cash-flow');
        Route::get('cash-flow/print', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'printCashFlow'])->name('cash-flow.print')->middleware('permission:accounting.reports.print');
        Route::get('cash-flow/export', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'exportCashFlow'])->name('cash-flow.export')->middleware('permission:accounting.reports.export');

        Route::get('cost-center-report', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'costCenterReport'])->name('cost-center-report')->middleware('permission:accounting.reports.cost-center');
        Route::get('cost-center-report/print', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'printCostCenterReport'])->name('cost-center-report.print')->middleware('permission:accounting.reports.print');
        Route::get('cost-center-report/export', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'exportCostCenterReport'])->name('cost-center-report.export')->middleware('permission:accounting.reports.export');
    });

    // Accounting Audit Trail & Activity Log (سجل النشاط والرقابة المالية)
    Route::middleware('permission:accounting.audit-logs.view')->group(function () {
        Route::get('audit-logs/data', [\Modules\Accounting\Http\Controllers\AccountingAuditLogController::class, 'data'])->name('audit-logs.data');
        Route::get('audit-logs/{id}', [\Modules\Accounting\Http\Controllers\AccountingAuditLogController::class, 'show'])->name('audit-logs.show');
        Route::get('audit-logs', [\Modules\Accounting\Http\Controllers\AccountingAuditLogController::class, 'index'])->name('audit-logs.index');
    });

    // AI Financial Assistant & Smart Guide (المساعد المالي والمرشد الذكي)
    Route::prefix('ai-assistant')->as('ai-assistant.')->group(function () {
        Route::get('/', [\Modules\Accounting\Http\Controllers\FinancialAssistantController::class, 'index'])->name('index')->middleware('permission:accounting.ai-assistant.view');
        Route::get('screen-context', [\Modules\Accounting\Http\Controllers\FinancialAssistantController::class, 'screenContext'])->name('screen-context')->middleware('permission:accounting.ai-assistant.view');
        Route::post('ask', [\Modules\Accounting\Http\Controllers\FinancialAssistantController::class, 'ask'])->name('ask')->middleware('permission:accounting.ai-assistant.ask');
    });
});
