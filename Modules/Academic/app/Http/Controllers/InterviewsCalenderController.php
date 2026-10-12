<?php

namespace Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Academic\Http\Requests\StoreInterviewsCalenderRequest;
use Modules\Academic\Http\Requests\UpdateInterviewsCalenderRequest;
use Modules\Academic\Models\GuardianInterview;
use Modules\Academic\Services\InterviewsCalenderService;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;

class InterviewsCalenderController extends Controller
{
    //------------------------------
    public function __construct(
        protected InterviewsCalenderService $service
    ) {}

    //------------------------------
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $filters = $request->only(['status', 'institution_id', 'branch_id', 'search']);
            $items = $this->service->all(auth()->id(), $filters);

            return response()->json([
                'success' => true,
                'data'    => $items,
            ]);
        }

        $institutions = Nursery::with('translations')->where('status', 'active')->get();
        $statuses     = GuardianInterview::statuses();
        $statusColors = GuardianInterview::statusColors();
        $sidebarData  = $this->service->getSidebarData(auth()->id());

        return view('academic::interviews_calender.index', compact(
            'institutions',
            'statuses',
            'statusColors',
            'sidebarData'
        ));
    }

    //------------------------------
    public function sidebarData(): JsonResponse
    {
        $data = $this->service->getSidebarData(auth()->id());

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    //------------------------------
    public function calendarEvents(Request $request): JsonResponse
    {
        $filters = $request->only([
            'institution_id',
            'branch_id',
            'status',
            'start',
            'end',
        ]);

        $events = $this->service->getCalendarEvents(auth()->id(), $filters);

        return response()->json($events);
    }

    //------------------------------
    public function checkGuardian(Request $request): JsonResponse
    {
        $civilId = $request->query('civil_id', '');
        $result  = $this->service->checkGuardianByCivilId($civilId);

        return response()->json([
            'success' => true,
            'data'    => $result,
        ]);
    }

    //------------------------------
    public function branches(int $institutionId): JsonResponse
    {
        $locale = app()->getLocale();
        $branches = NurseryBranch::with('translations')
            ->where('nursery_id', $institutionId)
            ->where('status', 'active')
            ->get()
            ->map(function ($branch) use ($locale) {
                return [
                    'id'   => $branch->id,
                    'name' => $branch->translate($locale)?->name ?? $branch->name,
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $branches,
        ]);
    }

    //------------------------------
    public function store(StoreInterviewsCalenderRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['user_id'] = auth()->id();

            $interview = $this->service->create($data);

            return response()->json([
                'success' => true,
                'message' => __('Interview scheduled successfully.'),
                'data'    => $interview,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to schedule interview.') . ' ' . $e->getMessage(),
            ], 500);
        }
    }

    //------------------------------
    public function show(int $id): JsonResponse
    {
        $interview = $this->service->find($id, auth()->id());

        if (! $interview) {
            return response()->json([
                'success' => false,
                'message' => __('Interview not found.'),
            ], 404);
        }

        $locale = app()->getLocale();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                   => $interview->id,
                'guardian_name'        => $interview->guardian_name ?: ($interview->guardian?->translate($locale)?->name ?? $interview->guardian?->name),
                'guardian_civil_id'    => $interview->guardian_civil_id ?? '',
                'guardian_phone'       => $interview->guardian_phone ?? '',
                'guardian_email'       => $interview->guardian_email ?? '',
                'academic_guardian_id' => $interview->academic_guardian_id,
                'institution_id'       => $interview->institution_id,
                'institution_name'     => $interview->institution?->translate($locale)?->name ?? $interview->institution?->name ?? '',
                'branch_id'            => $interview->branch_id,
                'branch_name'          => $interview->branch?->translate($locale)?->name ?? $interview->branch?->name ?? '',
                'interview_date'       => $interview->interview_date ? $interview->interview_date->format('Y-m-d') : '',
                'start_time'           => $interview->start_time ? substr($interview->start_time, 0, 5) : '',
                'end_time'             => $interview->end_time ? substr($interview->end_time, 0, 5) : '',
                'notes'                => $interview->notes ?? '',
                'status'               => $interview->status,
                'status_label'         => GuardianInterview::statuses()[$interview->status] ?? $interview->status,
                'color'                => $interview->color ?? '#0b3252',
            ],
        ]);
    }

    //------------------------------
    public function edit(int $id): JsonResponse
    {
        return $this->show($id);
    }

    //------------------------------
    public function update(UpdateInterviewsCalenderRequest $request, int $id): JsonResponse
    {
        try {
            $data = $request->validated();
            $updated = $this->service->update($id, $data, auth()->id());

            if (! $updated) {
                return response()->json([
                    'success' => false,
                    'message' => __('Interview not found or unauthorized.'),
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => __('Interview updated successfully.'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to update interview.') . ' ' . $e->getMessage(),
            ], 500);
        }
    }

    //------------------------------
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->service->delete($id, auth()->id());

            if (! $deleted) {
                return response()->json([
                    'success' => false,
                    'message' => __('Interview not found or unauthorized.'),
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => __('Interview deleted successfully.'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to delete interview.') . ' ' . $e->getMessage(),
            ], 500);
        }
    }

    //------------------------------
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:planned,completed,cancelled,absent',
        ]);

        $updated = $this->service->updateStatus($id, $request->input('status'), auth()->id());

        if (! $updated) {
            return response()->json([
                'success' => false,
                'message' => __('Interview not found or unauthorized.'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => __('Status updated successfully.'),
        ]);
    }
}
