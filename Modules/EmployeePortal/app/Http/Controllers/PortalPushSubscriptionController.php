<?php

namespace Modules\EmployeePortal\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\EmployeePortal\Services\Contracts\PortalPushSubscriptionServiceInterface;

class PortalPushSubscriptionController extends Controller
{
    public function __construct(
        private readonly PortalPushSubscriptionServiceInterface $service
    ) {}

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'url', 'max:2000'],
            'keys.p256dh' => ['required', 'string', 'max:500'],
            'keys.auth' => ['required', 'string', 'max:500'],
            'content_encoding' => ['nullable', 'string', 'max:30'],
        ]);

        $this->service->subscribe($request->user(), [
            'endpoint' => $validated['endpoint'],
            'public_key' => $validated['keys']['p256dh'],
            'auth_token' => $validated['keys']['auth'],
            'content_encoding' => $validated['content_encoding'] ?? 'aes128gcm',
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Notifications enabled successfully.'),
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'url', 'max:2000'],
        ]);

        $this->service->unsubscribe($request->user(), $validated['endpoint']);

        return response()->json(['success' => true]);
    }
}
