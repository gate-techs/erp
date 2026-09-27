<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Accounting\Services\FinancialAssistantService;

class FinancialAssistantController extends Controller
{
    public function __construct(
        protected FinancialAssistantService $assistantService
    ) {}

    public function index(Request $request): View
    {
        $currentRoute = $request->query('screen') ?: ($request->route()?->getName() ?: 'accounting.dashboard');
        $currentUrl = $request->fullUrl();

        return view('accounting::assistant.index', [
            'currentRouteName' => $currentRoute,
            'currentUrl'       => $currentUrl,
            'screenContext'    => $this->assistantService->resolveScreenContext($currentRoute, $currentUrl),
            'screenCatalog'    => $this->assistantService->getScreenCatalog(),
            'financialFacts'   => $this->assistantService->getRealtimeFinancialFacts(),
        ]);
    }

    /**
     * Ask a question to the Financial Assistant.
     */
    public function ask(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'question'      => ['required', 'string', 'max:2000'],
            'current_route' => ['nullable', 'string', 'max:255'],
            'current_url'   => ['nullable', 'string', 'max:500'],
            'entity_id'     => ['nullable', 'integer'],
            'history'       => ['nullable', 'array'],
        ]);

        $response = $this->assistantService->ask(
            question: $validated['question'],
            currentRoute: $validated['current_route'] ?? null,
            currentUrl: $validated['current_url'] ?? null,
            entityId: $validated['entity_id'] ?? null,
            history: $validated['history'] ?? []
        );

        return response()->json($response);
    }

    /**
     * Get screen context and tailored suggestions for the current screen.
     */
    public function screenContext(Request $request): JsonResponse
    {
        $currentRoute = $request->query('current_route');
        $currentUrl = $request->query('current_url');

        $context = $this->assistantService->resolveScreenContext($currentRoute, $currentUrl);

        return response()->json([
            'success'        => true,
            'screen_context' => $context,
            'suggestions'    => $context['suggestions'] ?? [],
        ]);
    }
}
