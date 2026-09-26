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
    Route::get('/', [AccountingDashboardController::class, 'index'])->name('index');
    Route::get('dashboard', [AccountingDashboardController::class, 'index'])->name('dashboard');
    Route::get('guide', [\Modules\Accounting\Http\Controllers\AccountingGuideController::class, 'index'])->name('guide');

    // Entities (الكيانات المحاسبية)
    Route::get('entities/data', [EntityController::class, 'data'])->name('entities.data');
    Route::resource('entities', EntityController::class);

    // Fiscal Years
    Route::get('fiscal-years/data', [FiscalYearController::class, 'data'])->name('fiscal-years.data');
    Route::post('fiscal-years/{id}/toggle-status', [FiscalYearController::class, 'toggleStatus'])->name('fiscal-years.toggle-status');
    Route::resource('fiscal-years', FiscalYearController::class);

    // Lock Dates and Exceptions
    Route::put('lock-dates/{entityId}', [LockDateController::class, 'update'])->name('lock-dates.update');
    Route::post('lock-dates/exceptions', [LockDateController::class, 'storeException'])->name('lock-dates.exceptions.store');
    Route::post('lock-dates/exceptions/{id}/revoke', [LockDateController::class, 'revokeException'])->name('lock-dates.exceptions.revoke');
    Route::get('lock-dates', [LockDateController::class, 'index'])->name('lock-dates.index');

    // Cost Centers (مراكز التكلفة)
    Route::get('cost-centers/data', [\Modules\Accounting\Http\Controllers\CostCenterController::class, 'data'])->name('cost-centers.data');
    Route::get('cost-centers/parents', [\Modules\Accounting\Http\Controllers\CostCenterController::class, 'getParentsByEntity'])->name('cost-centers.parents');
    Route::get('cost-centers/branches', [\Modules\Accounting\Http\Controllers\CostCenterController::class, 'getBranchesByEntity'])->name('cost-centers.branches');
    Route::post('cost-centers/{id}/toggle-status', [\Modules\Accounting\Http\Controllers\CostCenterController::class, 'toggleStatus'])->name('cost-centers.toggle-status');
    Route::resource('cost-centers', \Modules\Accounting\Http\Controllers\CostCenterController::class);

    // Journals (دفاتر اليومية)
    Route::get('journals/data', [JournalController::class, 'data'])->name('journals.data');
    Route::post('journals/{id}/toggle-status', [JournalController::class, 'toggleStatus'])->name('journals.toggle-status');
    Route::resource('journals', JournalController::class);

    // Cash & Bank Accounts (الخزائن والبنوك)
    Route::get('cash-accounts/data', [\Modules\Accounting\Http\Controllers\CashAccountController::class, 'data'])->name('cash-accounts.data');
    Route::post('cash-accounts/{id}/toggle-status', [\Modules\Accounting\Http\Controllers\CashAccountController::class, 'toggleStatus'])->name('cash-accounts.toggle-status');
    Route::resource('cash-accounts', \Modules\Accounting\Http\Controllers\CashAccountController::class);

    // Journal Entries (القيود اليومية)
    Route::get('journal-entries/data', [JournalEntryController::class, 'data'])->name('journal-entries.data');
    Route::post('journal-entries/{id}/post', [JournalEntryController::class, 'post'])->name('journal-entries.post');
    Route::post('journal-entries/{id}/cancel', [JournalEntryController::class, 'cancel'])->name('journal-entries.cancel');
    Route::post('journal-entries/{id}/reverse', [JournalEntryController::class, 'reverse'])->name('journal-entries.reverse');
    Route::resource('journal-entries', JournalEntryController::class);

    // Opening Balances (الأرصدة الافتتاحية)
    Route::prefix('opening-balances')->as('opening-balances.')->group(function () {
        Route::get('/', [\Modules\Accounting\Http\Controllers\OpeningBalanceController::class, 'index'])->name('index');
        Route::get('accounts', [\Modules\Accounting\Http\Controllers\OpeningBalanceController::class, 'getAccounts'])->name('accounts');
        Route::post('save', [\Modules\Accounting\Http\Controllers\OpeningBalanceController::class, 'save'])->name('save');
        Route::post('reopen', [\Modules\Accounting\Http\Controllers\OpeningBalanceController::class, 'reopen'])->name('reopen');
        Route::get('print', [\Modules\Accounting\Http\Controllers\OpeningBalanceController::class, 'print'])->name('print');
    });

    // Bank Reconciliations (التسويات البنكية)
    Route::get('bank-reconciliations/data', [\Modules\Accounting\Http\Controllers\BankReconciliationController::class, 'data'])->name('bank-reconciliations.data');
    Route::get('bank-reconciliations/banks-by-entity', [\Modules\Accounting\Http\Controllers\BankReconciliationController::class, 'getBanksByEntity'])->name('bank-reconciliations.banks-by-entity');
    Route::get('bank-reconciliations/workspace-data', [\Modules\Accounting\Http\Controllers\BankReconciliationController::class, 'workspaceData'])->name('bank-reconciliations.workspace-data');
    Route::post('bank-reconciliations/{id}/reopen', [\Modules\Accounting\Http\Controllers\BankReconciliationController::class, 'reopen'])->name('bank-reconciliations.reopen');
    Route::get('bank-reconciliations/{id}/print', [\Modules\Accounting\Http\Controllers\BankReconciliationController::class, 'print'])->name('bank-reconciliations.print');
    Route::resource('bank-reconciliations', \Modules\Accounting\Http\Controllers\BankReconciliationController::class);

    // Payment Matchings (مطابقة وتخصيص المدفوعات)
    Route::get('payment-matchings/data', [\Modules\Accounting\Http\Controllers\PaymentMatchingController::class, 'data'])->name('payment-matchings.data');
    Route::get('payment-matchings/accounts-by-entity', [\Modules\Accounting\Http\Controllers\PaymentMatchingController::class, 'getAccountsByEntity'])->name('payment-matchings.accounts-by-entity');
    Route::get('payment-matchings/unmatched-data', [\Modules\Accounting\Http\Controllers\PaymentMatchingController::class, 'unmatchedData'])->name('payment-matchings.unmatched-data');
    Route::get('payment-matchings/create', [\Modules\Accounting\Http\Controllers\PaymentMatchingController::class, 'create'])->name('payment-matchings.create');
    Route::post('payment-matchings', [\Modules\Accounting\Http\Controllers\PaymentMatchingController::class, 'store'])->name('payment-matchings.store');
    Route::delete('payment-matchings/{id}', [\Modules\Accounting\Http\Controllers\PaymentMatchingController::class, 'destroy'])->name('payment-matchings.destroy');
    Route::get('payment-matchings', [\Modules\Accounting\Http\Controllers\PaymentMatchingController::class, 'index'])->name('payment-matchings.index');



    // Chart of Accounts (دليل / شجرة الحسابات)
    Route::get('accounts/template/download', [AccountController::class, 'downloadTemplate'])->name('accounts.template.download');
    Route::get('accounts/export', [AccountController::class, 'export'])->name('accounts.export');
    Route::post('accounts/import', [AccountController::class, 'import'])->name('accounts.import');
    Route::get('accounts/data', [AccountController::class, 'data'])->name('accounts.data');
    Route::get('accounts/tree-data', [AccountController::class, 'treeData'])->name('accounts.tree-data');
    Route::get('accounts/suggest-code', [AccountController::class, 'suggestCode'])->name('accounts.suggest-code');
    Route::get('accounts/parents', [AccountController::class, 'parents'])->name('accounts.parents');
    Route::post('accounts/{account}/toggle-status', [AccountController::class, 'toggleStatus'])->name('accounts.toggle-status');
    Route::post('accounts/{account}/toggle-deprecated', [AccountController::class, 'toggleDeprecated'])->name('accounts.toggle-deprecated');
    Route::resource('accounts', AccountController::class);

    // Financial Reports (التقارير المالية)
    Route::prefix('reports')->as('reports.')->group(function () {
        Route::get('general-ledger', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'generalLedger'])->name('general-ledger');
        Route::get('general-ledger/print', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'printGeneralLedger'])->name('general-ledger.print');
        Route::get('general-ledger/export', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'exportGeneralLedger'])->name('general-ledger.export');

        Route::get('trial-balance', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'trialBalance'])->name('trial-balance');
        Route::get('trial-balance/print', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'printTrialBalance'])->name('trial-balance.print');
        Route::get('trial-balance/export', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'exportTrialBalance'])->name('trial-balance.export');

        Route::get('income-statement', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'incomeStatement'])->name('income-statement');
        Route::get('income-statement/print', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'printIncomeStatement'])->name('income-statement.print');
        Route::get('income-statement/export', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'exportIncomeStatement'])->name('income-statement.export');

        Route::get('balance-sheet', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'balanceSheet'])->name('balance-sheet');
        Route::get('balance-sheet/print', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'printBalanceSheet'])->name('balance-sheet.print');
        Route::get('balance-sheet/export', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'exportBalanceSheet'])->name('balance-sheet.export');

        Route::get('account-statement', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'accountStatement'])->name('account-statement');
        Route::get('account-statement/print', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'printAccountStatement'])->name('account-statement.print');
        Route::get('account-statement/export', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'exportAccountStatement'])->name('account-statement.export');

        Route::get('cash-flow', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'cashFlow'])->name('cash-flow');
        Route::get('cash-flow/print', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'printCashFlow'])->name('cash-flow.print');
        Route::get('cash-flow/export', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'exportCashFlow'])->name('cash-flow.export');

        Route::get('cost-center-report', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'costCenterReport'])->name('cost-center-report');
        Route::get('cost-center-report/print', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'printCostCenterReport'])->name('cost-center-report.print');
        Route::get('cost-center-report/export', [\Modules\Accounting\Http\Controllers\FinancialReportController::class, 'exportCostCenterReport'])->name('cost-center-report.export');
    });

    // Accounting Audit Trail & Activity Log (سجل النشاط والرقابة المالية)
    Route::get('audit-logs/data', [\Modules\Accounting\Http\Controllers\AccountingAuditLogController::class, 'data'])->name('audit-logs.data');
    Route::get('audit-logs/{id}', [\Modules\Accounting\Http\Controllers\AccountingAuditLogController::class, 'show'])->name('audit-logs.show');
    Route::get('audit-logs', [\Modules\Accounting\Http\Controllers\AccountingAuditLogController::class, 'index'])->name('audit-logs.index');

    // AI Financial Assistant & Smart Guide (المساعد المالي والمرشد الذكي)
    Route::prefix('ai-assistant')->as('ai-assistant.')->group(function () {
        Route::post('ask', [\Modules\Accounting\Http\Controllers\FinancialAssistantController::class, 'ask'])->name('ask');
        Route::get('screen-context', [\Modules\Accounting\Http\Controllers\FinancialAssistantController::class, 'screenContext'])->name('screen-context');
    });
});


