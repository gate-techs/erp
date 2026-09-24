<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Accounting\Http\Requests\StoreLockExceptionRequest;
use Modules\Accounting\Http\Requests\UpdateLockDatesRequest;
use Modules\Accounting\Models\Entity;
use Modules\Accounting\Services\LockDateService;

class LockDateController extends Controller
{
    //===============================
    public function __construct(
        protected LockDateService $service
    ) {}

    //===============================
    public function index(Request $request): View
    {
        $entities = Entity::with('translations')->orderBy('id')->get();
        $entityId = (int) ($request->integer('entity_id') ?: $entities->first()?->id);
        $selectedEntity = $entities->firstWhere('id', $entityId);

        return view('accounting::lock-dates.index', [
            'entities' => $entities,
            'users' => User::query()->select('id', 'name')->orderBy('name')->get(),
            'selectedEntity' => $selectedEntity,
            'lockDates' => $selectedEntity ? $this->service->getLockDates($entityId) : null,
            'exceptions' => $selectedEntity ? $this->service->getExceptions($entityId) : collect(),
            'lockTypes' => [
                'lock_all' => __('Journal Entries (Non-Advisers)'),
                'tax' => __('Tax Return Lock'),
                'fiscal_year' => __('Fiscal Year Lock'),
            ],
        ]);
    }

    //===============================
    public function update(UpdateLockDatesRequest $request, int $entityId): RedirectResponse|JsonResponse
    {
        $this->ensureEntityExists($entityId);
        $lockDates = $this->service->saveLockDates($entityId, $request->validated(), auth()->id());

        return $this->respond($request, __('Lock dates updated successfully.'), $lockDates);
    }

    //===============================
    public function storeException(StoreLockExceptionRequest $request): RedirectResponse|JsonResponse
    {
        $exception = $this->service->createException($request->validated(), auth()->id());

        return $this->respond($request, __('Lock exception created successfully.'), $exception);
    }

    //===============================
    public function revokeException(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $exception = $this->service->revokeException($id, auth()->id());

        return $this->respond($request, __('Lock exception revoked successfully.'), $exception);
    }

    //===============================
    protected function ensureEntityExists(int $entityId): void
    {
        abort_unless(Entity::whereKey($entityId)->exists(), 404);
    }

    //===============================
    protected function respond(Request $request, string $message, mixed $data = null): RedirectResponse|JsonResponse
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $message, 'data' => $data]);
        }

        $entityId = $request->input('entity_id', $request->route('entityId'));

        return redirect()
            ->route('accounting.lock-dates.index', $entityId ? ['entity_id' => $entityId] : [])
            ->with('success', $message);
    }
}
