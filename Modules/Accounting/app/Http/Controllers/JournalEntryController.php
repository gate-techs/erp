<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Accounting\Enums\JournalEntryStatus;
use Modules\Accounting\Enums\JournalEntryType;
use Modules\Accounting\Http\Requests\StoreJournalEntryRequest;
use Modules\Accounting\Http\Requests\UpdateJournalEntryRequest;
use Modules\Accounting\Models\Account;
use Modules\Accounting\Models\CostCenter;
use Modules\Accounting\Models\Entity;
use Modules\Accounting\Models\Journal;
use Modules\Accounting\Models\JournalEntry;
use Modules\Accounting\Services\JournalEntryService;
use Yajra\DataTables\Facades\DataTables;

class JournalEntryController extends Controller
{
    public function __construct(
        protected JournalEntryService $service
    ) {}

    public function index(Request $request): View
    {
        $entities = Entity::with('translations')->orderBy('id')->get();
        $selectedEntityId = (int) ($request->get('entity_id') ?? $entities->first()?->id ?? 0);

        return view('accounting::journal-entries.index', [
            'entities' => $entities,
            'selectedEntityId' => $selectedEntityId,
            'journals' => Journal::with('translations')->where('is_active', true)->orderBy('code')->get(),
            'statuses' => JournalEntryStatus::cases(),
            'types' => JournalEntryType::cases(),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = $this->service->getDataTableQuery($request->all())
            ->with(['entity.translations', 'journal.translations', 'creator']);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->filterColumn('entry_number_formatted', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('journal_entries.entry_number', 'like', "%{$keyword}%")
                        ->orWhere('journal_entries.reference', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('narration_formatted', function ($query, $keyword) {
                $query->where('journal_entries.narration', 'like', "%{$keyword}%");
            })
            ->orderColumn('entry_number_formatted', fn ($query, $order) => $query->orderBy('journal_entries.entry_number', $order))
            ->orderColumn('narration_formatted', fn ($query, $order) => $query->orderBy('journal_entries.narration', $order))
            ->addColumn('entry_number_formatted', function (JournalEntry $entry) {
                $viewUrl = route('accounting.journal-entries.show', $entry->id);
                $referenceHtml = $entry->reference ? '<div class="text-muted font-11 mt-1"><i class="bx bx-hash"></i> ' . e($entry->reference) . '</div>' : '';
                $reversalHtml = $entry->is_reversal ? '<span class="badge bg-label-danger font-11 ms-1">' . __('Reversal') . '</span>' : '';

                return '<div class="py-1">' .
                    '<a href="' . $viewUrl . '" class="text-primary fw-bold font-13 text-hover-underline font-monospace">' . e($entry->entry_number) . '</a>' .
                    $reversalHtml .
                    $referenceHtml .
                    '</div>';
            })
            ->addColumn('narration_formatted', function (JournalEntry $entry) {
                return '<span class="text-heading font-13 text-truncate d-inline-block" style="max-width: 320px;" title="' . e($entry->narration) . '">' . e($entry->narration) . '</span>';
            })
            ->addColumn('entry_info', function (JournalEntry $entry) {
                $viewUrl = route('accounting.journal-entries.show', $entry->id);
                return '<a href="' . $viewUrl . '" class="text-primary fw-bold">' . e($entry->entry_number) . '</a>';
            })
            ->addColumn('entity_info', function (JournalEntry $entry) {
                $name = $entry->entity?->translate(app()->getLocale())?->name ?? $entry->entity?->code ?? '-';
                return '<span class="badge bg-label-secondary fw-semibold">' . e($name) . '</span>';
            })
            ->addColumn('journal_info', function (JournalEntry $entry) {
                $name = $entry->journal?->translate(app()->getLocale())?->name ?? $entry->journal?->code ?? '-';
                return '<div class="d-flex flex-column"><span class="fw-semibold text-heading">' . e($name) . '</span><small class="text-muted">' . e($entry->journal?->code) . '</small></div>';
            })
            ->addColumn('entry_date_formatted', fn (JournalEntry $entry) => '<span class="fw-semibold text-heading">' . $entry->entry_date->format('Y-m-d') . '</span>')
            ->addColumn('total_debit_formatted', fn (JournalEntry $entry) => '<span class="fw-bold text-success font-13" style="font-variant-numeric: tabular-nums;">' . number_format((float) $entry->total_debit, 2) . '</span> <small class="text-muted font-11">' . e($entry->currency) . '</small>')
            ->addColumn('total_credit_formatted', fn (JournalEntry $entry) => '<span class="fw-bold text-danger font-13" style="font-variant-numeric: tabular-nums;">' . number_format((float) $entry->total_credit, 2) . '</span> <small class="text-muted font-11">' . e($entry->currency) . '</small>')
            ->addColumn('status_badge', function (JournalEntry $entry) {
                return '<span class="badge ' . $entry->status->badgeClass() . ' fw-semibold">' . e($entry->status->label()) . '</span>';
            })
            ->addColumn('actions', function (JournalEntry $entry) {
                $user = auth()->user();
                $showUrl = route('accounting.journal-entries.show', $entry->id);
                $editUrl = route('accounting.journal-entries.edit', $entry->id);

                $actions = '<div class="d-inline-flex gap-1">';
                $actions .= '<a href="' . $showUrl . '" class="btn btn-icon btn-sm btn-outline-secondary" title="' . __('View Voucher') . '"><i class="icon-base bx bx-show"></i></a>';

                if ($entry->canBeEdited()) {
                    if ($user->can('accounting.journal-entries.edit')) {
                        $actions .= '<a href="' . $editUrl . '" class="btn btn-icon btn-sm btn-outline-primary" title="' . __('Edit Draft') . '"><i class="icon-base bx bx-edit"></i></a>';
                    }
                    if ($user->can('accounting.journal-entries.post')) {
                        $actions .= '<button type="button" class="btn btn-icon btn-sm btn-outline-success btn-post-entry" data-id="' . $entry->id . '" title="' . __('Post Entry') . '"><i class="icon-base bx bx-check-double"></i></button>';
                    }
                    if ($user->can('accounting.journal-entries.delete')) {
                        $actions .= '<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-entry" data-id="' . $entry->id . '" data-number="' . e($entry->entry_number) . '" title="' . __('Delete') . '"><i class="icon-base bx bx-trash"></i></button>';
                    }
                } elseif ($entry->canBeReversed()) {
                    if ($user->can('accounting.journal-entries.reverse')) {
                        $actions .= '<button type="button" class="btn btn-icon btn-sm btn-outline-warning btn-reverse-entry" data-id="' . $entry->id . '" data-number="' . e($entry->entry_number) . '" title="' . __('Reverse Entry') . '"><i class="icon-base bx bx-undo"></i></button>';
                    }
                }

                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['entry_number_formatted', 'narration_formatted', 'entry_info', 'entity_info', 'journal_info', 'entry_date_formatted', 'total_debit_formatted', 'total_credit_formatted', 'status_badge', 'actions'])
            ->make(true);
    }

    public function create(Request $request): View
    {
        $entities = Entity::with('translations')->orderBy('id')->get();
        $selectedEntityId = (int) ($request->get('entity_id') ?? $entities->first()?->id ?? 0);

        return view('accounting::journal-entries.create', [
            'entities' => $entities,
            'selectedEntityId' => $selectedEntityId,
            'journals' => Journal::with('translations')->where('is_active', true)->orderBy('code')->get(),
            'accounts' => Account::with('translations')->where('is_group', false)->where('is_active', true)->orderBy('entity_id')->orderBy('code')->get(),
            'costCenters' => CostCenter::with('translations')->active()->orderBy('entity_id')->orderBy('code')->get(),
            'types' => JournalEntryType::cases(),
        ]);
    }

    public function store(StoreJournalEntryRequest $request): JsonResponse|RedirectResponse
    {
        $data = $request->validated();
        $lines = $data['lines'] ?? [];
        unset($data['lines']);

        $entry = $this->service->create($data, $lines, auth()->id());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Journal entry created successfully.'),
                'data' => $entry,
                'redirect_url' => route('accounting.journal-entries.show', $entry->id),
            ]);
        }

        return redirect()->route('accounting.journal-entries.show', $entry->id)->with('success', __('Journal entry created successfully.'));
    }

    public function show(int $id): View
    {
        $entry = $this->service->getById($id);

        return view('accounting::journal-entries.show', [
            'entry' => $entry,
        ]);
    }

    public function edit(int $id): View|RedirectResponse
    {
        $entry = $this->service->getById($id);

        if (!$entry->canBeEdited()) {
            return redirect()->route('accounting.journal-entries.show', $entry->id)
                ->with('error', __('Only draft journal entries can be modified.'));
        }

        return view('accounting::journal-entries.edit', [
            'entry' => $entry,
            'entities' => Entity::with('translations')->orderBy('id')->get(),
            'journals' => Journal::with('translations')->where('is_active', true)->orderBy('code')->get(),
            'accounts' => Account::with('translations')->where('is_group', false)->where('is_active', true)->orderBy('entity_id')->orderBy('code')->get(),
            'costCenters' => CostCenter::with('translations')->active()->orderBy('entity_id')->orderBy('code')->get(),
            'types' => JournalEntryType::cases(),
        ]);
    }

    public function update(UpdateJournalEntryRequest $request, int $id): JsonResponse|RedirectResponse
    {
        $data = $request->validated();
        $lines = $data['lines'] ?? [];
        unset($data['lines']);

        $entry = $this->service->update($id, $data, $lines, auth()->id());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Journal entry updated successfully.'),
                'data' => $entry,
                'redirect_url' => route('accounting.journal-entries.show', $entry->id),
            ]);
        }

        return redirect()->route('accounting.journal-entries.show', $entry->id)->with('success', __('Journal entry updated successfully.'));
    }

    public function destroy(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $this->service->delete($id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Journal entry deleted successfully.'),
            ]);
        }

        return redirect()->route('accounting.journal-entries.index')->with('success', __('Journal entry deleted successfully.'));
    }

    public function post(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $entry = $this->service->post($id, auth()->id());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Journal entry posted successfully.'),
                'data' => $entry,
            ]);
        }

        return back()->with('success', __('Journal entry posted successfully.'));
    }

    public function cancel(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $entry = $this->service->cancel($id, auth()->id());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Journal entry cancelled successfully.'),
                'data' => $entry,
            ]);
        }

        return back()->with('success', __('Journal entry cancelled successfully.'));
    }

    public function reverse(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $request->validate([
            'entry_date' => ['required', 'date'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $reversal = $this->service->reverse($id, $request->all(), auth()->id());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Journal entry reversed successfully. Reversal entry created: ') . $reversal->entry_number,
                'data' => $reversal,
                'redirect_url' => route('accounting.journal-entries.show', $reversal->id),
            ]);
        }

        return redirect()->route('accounting.journal-entries.show', $reversal->id)->with('success', __('Journal entry reversed successfully.'));
    }
}
