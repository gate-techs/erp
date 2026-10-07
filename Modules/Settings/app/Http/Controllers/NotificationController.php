<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Settings\Enums\NotificationAudience;
use Modules\Settings\Enums\NotificationType;
use Modules\Settings\Services\Contracts\NotificationServiceInterface;

class NotificationController extends Controller
{
    public function __construct(private readonly NotificationServiceInterface $service) {}

    public function index(): View
    {
        $notifications = $this->service->getUserNotifications((int) auth()->id());

        return view('settings::notifications.index', compact('notifications'));
    }

    public function broadcasts(): View
    {
        return view('settings::notifications.broadcasts');
    }

    public function createBroadcast(): View
    {
        return view('settings::notifications.create-broadcast', [
            'audiences' => NotificationAudience::cases(),
            'types' => NotificationType::cases(),
        ]);
    }

    public function broadcastsData(Request $request): JsonResponse
    {
        $result = $this->service->getBroadcastData([
            'search' => $request->input('search.value'),
            'start' => (int) $request->input('start', 0),
            'length' => (int) $request->input('length', 10),
        ]);

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $result['total'],
            'recordsFiltered' => $result['filtered'],
            'data' => $result['rows']->map(fn ($batch) => [
                'title' => e($batch->payload['title_ar'] ?? __('General Notification')),
                'audience' => e(NotificationAudience::tryFrom($batch->audience_type)?->label() ?? $batch->audience_type),
                'recipients' => $batch->recipients_count,
                'status' => e(__($batch->status)),
                'scheduled_at' => $batch->scheduled_at?->format('Y-m-d H:i') ?? '-',
                'created_at' => $batch->created_at?->format('Y-m-d H:i'),
            ])->values(),
        ]);
    }

    public function storeBroadcast(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'audience_type' => ['required', 'in:all,employees,teachers,guardians'],
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'message_ar' => ['required', 'string', 'max:5000'],
            'message_en' => ['nullable', 'string', 'max:5000'],
            'action_url' => ['nullable', 'string', 'max:500'],
            'is_scheduled' => ['nullable', 'boolean'],
            'scheduled_at' => ['required_if:is_scheduled,1', 'nullable', 'date', 'after:now'],
        ]);

        $result = $this->service->sendBroadcast($validated, (int) auth()->id());

        return redirect()->route('notifications.broadcasts')->with('success', $result['scheduled']
            ? __('General notification scheduled successfully.')
            : __('General notification sent successfully to :count users.', ['count' => $result['recipients']]));
    }
}
