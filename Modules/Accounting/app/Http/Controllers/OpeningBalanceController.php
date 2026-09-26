<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Accounting\Models\CostCenter;
use Modules\Accounting\Models\Entity;
use Modules\Accounting\Models\FiscalYear;
use Modules\Accounting\Services\OpeningBalanceService;

class OpeningBalanceController extends Controller
{
    //--------------------------------------------------
    public function __construct(
        protected OpeningBalanceService $service
    ) {}

    //--------------------------------------------------
    public function index(Request $request): View
    {
        $entities = Entity::with('translations')->orderBy('id')->get();
        $selectedEntityId = (int) ($request->integer('entity_id') ?: $entities->first()?->id);

        $fiscalYears = FiscalYear::where('entity_id', $selectedEntityId)
            ->with('translations')
            ->orderByDesc('start_date')
            ->get();

        $selectedFiscalYearId = (int) ($request->integer('fiscal_year_id') ?: $fiscalYears->first()?->id);

        $costCenters = CostCenter::where('entity_id', $selectedEntityId)
            ->with('translations')
            ->where('status', \Modules\Accounting\Enums\CostCenterStatus::ACTIVE)
            ->get();

        return view('accounting::opening-balances.index', [
            'entities' => $entities,
            'selectedEntityId' => $selectedEntityId,
            'fiscalYears' => $fiscalYears,
            'selectedFiscalYearId' => $selectedFiscalYearId,
            'costCenters' => $costCenters,
        ]);
    }

    //--------------------------------------------------
    public function getAccounts(Request $request): JsonResponse
    {
        $entityId = $request->integer('entity_id');
        $fiscalYearId = $request->integer('fiscal_year_id');

        if (!$entityId || !$fiscalYearId) {
            return response()->json([
                'success' => false,
                'message' => __('Entity and Fiscal Year are required.'),
            ], 422);
        }

        $data = $this->service->getOpeningBalanceData($entityId, $fiscalYearId);

        // Group accounts by account type for easy tabbed / accordion rendering
        $locale = app()->getLocale();
        $formattedAccounts = $data['accounts']->map(function ($account) use ($locale, $data) {
            $existing = $data['linesByAccount'][$account->id] ?? null;

            return [
                'id' => $account->id,
                'code' => $account->code,
                'name' => $account->translate($locale)?->name ?? $account->translate('ar')?->name ?? $account->translate('en')?->name ?? $account->code,
                'type' => $account->account_type?->value ?? 'asset',
                'type_label' => $account->account_type?->label() ?? '',
                'normal_balance' => $account->normal_balance,
                'debit' => $existing['debit'] ?? 0,
                'credit' => $existing['credit'] ?? 0,
                'notes' => $existing['notes'] ?? '',
                'cost_center_id' => $existing['cost_center_id'] ?? null,
            ];
        });

        return response()->json([
            'success' => true,
            'accounts' => $formattedAccounts,
            'totals' => $data['totals'],
            'opening_entry' => $data['openingEntry'] ? [
                'id' => $data['openingEntry']->id,
                'entry_number' => $data['openingEntry']->entry_number,
                'entry_date' => $data['openingEntry']->entry_date->format('Y-m-d'),
                'status' => $data['openingEntry']->status->value,
                'status_label' => $data['openingEntry']->status->label(),
                'is_posted' => $data['totals']['is_posted'],
                'posted_at' => $data['openingEntry']->posted_at?->format('Y-m-d H:i'),
                'posted_by_name' => $data['openingEntry']->postedBy?->name,
                'currency' => $data['openingEntry']->currency,
            ] : null,
        ]);
    }

    //--------------------------------------------------
    public function save(Request $request): JsonResponse
    {
        $request->validate([
            'entity_id' => ['required', 'integer', 'exists:entities,id'],
            'fiscal_year_id' => ['required', 'integer', 'exists:fiscal_years,id'],
            'entry_date' => ['required', 'date'],
            'action' => ['required', 'string', 'in:save_draft,post'],
            'lines' => ['required', 'array'],
            'lines.*.account_id' => ['required', 'integer', 'exists:accounts,id'],
            'lines.*.debit' => ['nullable', 'numeric', 'min:0'],
            'lines.*.credit' => ['nullable', 'numeric', 'min:0'],
            'lines.*.notes' => ['nullable', 'string', 'max:500'],
            'lines.*.cost_center_id' => ['nullable', 'integer', 'exists:accounting_cost_centers,id'],
        ]);

        $post = ($request->input('action') === 'post');

        $headerData = [
            'entity_id' => $request->integer('entity_id'),
            'fiscal_year_id' => $request->integer('fiscal_year_id'),
            'entry_date' => $request->input('entry_date'),
            'narration' => __('Opening balances for fiscal year'),
            'reference' => 'OPENING-BALANCE',
        ];

        $entry = $this->service->save($headerData, $request->input('lines', []), $post);

        $message = $post
            ? __('Opening balances posted and approved successfully.')
            : __('Opening balances draft saved successfully.');

        return response()->json([
            'success' => true,
            'message' => $message,
            'entry_id' => $entry->id,
            'entry_number' => $entry->entry_number,
            'is_posted' => $entry->isPosted(),
        ]);
    }

    //--------------------------------------------------
    public function reopen(Request $request): JsonResponse
    {
        $request->validate([
            'entity_id' => ['required', 'integer', 'exists:entities,id'],
            'fiscal_year_id' => ['required', 'integer', 'exists:fiscal_years,id'],
        ]);

        $entry = $this->service->reopen($request->integer('entity_id'), $request->integer('fiscal_year_id'));

        return response()->json([
            'success' => true,
            'message' => __('Opening entry reopened to draft status successfully.'),
            'is_posted' => false,
        ]);
    }

    //--------------------------------------------------
    public function print(Request $request): View
    {
        $entityId = $request->integer('entity_id');
        $fiscalYearId = $request->integer('fiscal_year_id');

        $entity = Entity::with('translations')->findOrFail($entityId);
        $fiscalYear = FiscalYear::with('translations')->findOrFail($fiscalYearId);

        $data = $this->service->getOpeningBalanceData($entityId, $fiscalYearId);

        return view('accounting::opening-balances.print', [
            'entity' => $entity,
            'fiscalYear' => $fiscalYear,
            'accounts' => $data['accounts'],
            'linesByAccount' => $data['linesByAccount'],
            'totals' => $data['totals'],
            'openingEntry' => $data['openingEntry'],
        ]);
    }
}
