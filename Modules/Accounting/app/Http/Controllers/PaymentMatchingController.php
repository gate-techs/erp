<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Accounting\Models\Account;
use Modules\Accounting\Models\Entity;
use Modules\Accounting\Models\PaymentMatching;
use Modules\Accounting\Services\PaymentMatchingService;
use Yajra\DataTables\Facades\DataTables;

class PaymentMatchingController extends Controller
{
    //--------------------------------------------------
    public function __construct(
        protected PaymentMatchingService $service
    ) {}

    //--------------------------------------------------
    public function index(Request $request): View
    {
        $entities = Entity::with('translations')->orderBy('id')->get();
        $selectedEntityId = (int) ($request->integer('entity_id') ?: $entities->first()?->id);

        $accounts = Account::with('translations')
            ->where('is_group', false)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('accounting::payment-matchings.index', [
            'entities'         => $entities,
            'selectedEntityId' => $selectedEntityId,
            'accounts'         => $accounts,
        ]);
    }

    //--------------------------------------------------
    public function create(Request $request): View
    {
        $entities = Entity::with('translations')->orderBy('id')->get();
        $selectedEntityId = (int) ($request->integer('entity_id') ?: $entities->first()?->id);

        $accounts = Account::with('translations')
            ->where('entity_id', $selectedEntityId)
            ->where('is_group', false)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('accounting::payment-matchings.create', [
            'entities'         => $entities,
            'selectedEntityId' => $selectedEntityId,
            'accounts'         => $accounts,
        ]);
    }

    //--------------------------------------------------
    public function getAccountsByEntity(Request $request): JsonResponse
    {
        $entityId = $request->integer('entity_id');
        $accounts = Account::with('translations')
            ->where('entity_id', $entityId)
            ->where('is_group', false)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $locale = app()->getLocale();

        return response()->json([
            'success'  => true,
            'accounts' => $accounts->map(function ($acc) use ($locale) {
                return [
                    'id'   => $acc->id,
                    'code' => $acc->code,
                    'name' => $acc->translate($locale)?->name ?? $acc->translate('ar')?->name ?? $acc->code,
                ];
            }),
        ]);
    }

    //--------------------------------------------------
    public function data(Request $request): JsonResponse
    {
        $query = $this->service->getDataTableQuery($request->all());

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('matching_info', function (PaymentMatching $item) {
                return '<div class="d-flex flex-column">
                    <span class="fw-bold text-heading">#' . $item->id . '</span>
                    <span class="text-muted small">' . e($item->matched_at->format('Y-m-d H:i')) . '</span>
                </div>';
            })
            ->addColumn('debit_info', function (PaymentMatching $item) {
                $entryNum = $item->debitLine?->entry?->entry_number ?? '-';
                $accName = $item->debitLine?->account?->translate(app()->getLocale())?->name ?? $item->debitLine?->account?->code ?? '-';

                return '<div class="d-flex flex-column">
                    <span class="fw-semibold text-primary">' . e($entryNum) . '</span>
                    <span class="text-muted small">' . e($accName) . ' &bull; ' . number_format($item->debitLine?->debit ?? 0, 2) . '</span>
                </div>';
            })
            ->addColumn('credit_info', function (PaymentMatching $item) {
                $entryNum = $item->creditLine?->entry?->entry_number ?? '-';
                $accName = $item->creditLine?->account?->translate(app()->getLocale())?->name ?? $item->creditLine?->account?->code ?? '-';

                return '<div class="d-flex flex-column">
                    <span class="fw-semibold text-success">' . e($entryNum) . '</span>
                    <span class="text-muted small">' . e($accName) . ' &bull; ' . number_format($item->creditLine?->credit ?? 0, 2) . '</span>
                </div>';
            })
            ->addColumn('amount_fmt', function (PaymentMatching $item) {
                return '<span class="badge bg-label-success fs-6">' . number_format($item->matched_amount, 2) . '</span>';
            })
            ->addColumn('user_info', function (PaymentMatching $item) {
                return '<span class="small text-muted">' . e($item->matchedBy?->name ?? '-') . '</span>';
            })
            ->addColumn('actions', function (PaymentMatching $item) {
                return '<div class="d-flex align-items-center gap-1 justify-content-end">
                    <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-unmatch" data-id="' . $item->id . '" title="' . __('Unmatch / Delete') . '">
                        <i class="icon-base bx bx-unlink font-16"></i>
                    </button>
                </div>';
            })
            ->rawColumns(['matching_info', 'debit_info', 'credit_info', 'amount_fmt', 'user_info', 'actions'])
            ->make(true);
    }

    //--------------------------------------------------
    public function unmatchedData(Request $request): JsonResponse
    {
        $entityId = $request->integer('entity_id');
        $accountId = $request->integer('account_id') ?: null;
        $partnerId = $request->integer('partner_id') ?: null;

        if (!$entityId) {
            return response()->json(['success' => false, 'message' => __('Entity is required.')], 422);
        }

        $data = $this->service->getUnmatchedLines($entityId, $accountId, $partnerId);

        $locale = app()->getLocale();

        $formatLine = function ($line) use ($locale) {
            return [
                'id'               => $line->id,
                'entry_id'         => $line->entry?->id,
                'entry_number'     => $line->entry?->entry_number,
                'entry_date'       => $line->entry?->entry_date?->format('Y-m-d'),
                'account_name'     => $line->account?->translate($locale)?->name ?? $line->account?->code,
                'journal_name'     => $line->entry?->journal?->translate($locale)?->name ?? '',
                'description'      => $line->description ?: $line->entry?->narration,
                'debit'            => (float) $line->debit,
                'credit'           => (float) $line->credit,
                'matched_amount'   => (float) $line->matched_amount,
                'remaining_amount' => (float) $line->remaining_amount,
            ];
        };

        return response()->json([
            'success'     => true,
            'debitLines'  => $data['debitLines']->map($formatLine),
            'creditLines' => $data['creditLines']->map($formatLine),
        ]);
    }

    //--------------------------------------------------
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'entity_id'      => ['required', 'integer', 'exists:entities,id'],
            'debit_line_id'  => ['required', 'integer', 'exists:journal_entry_lines,id'],
            'credit_line_id' => ['required', 'integer', 'exists:journal_entry_lines,id'],
            'amount'         => ['required', 'numeric', 'min:0.0001'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        $matching = $this->service->match(
            $request->integer('entity_id'),
            $request->integer('debit_line_id'),
            $request->integer('credit_line_id'),
            (float) $request->input('amount'),
            $request->input('notes')
        );

        return response()->json([
            'success'     => true,
            'message'     => __('Items matched successfully.'),
            'matching_id' => $matching->id,
        ]);
    }

    //--------------------------------------------------
    public function destroy(int $id): JsonResponse
    {
        $this->service->unmatch($id);

        return response()->json([
            'success' => true,
            'message' => __('Matching removed successfully.'),
        ]);
    }
}
