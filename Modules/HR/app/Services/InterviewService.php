<?php

namespace Modules\HR\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\HR\Enums\ApplicationStage;
use Modules\HR\Enums\EvaluationRecommendation;
use Modules\HR\Enums\InterviewStatus;
use Modules\HR\Models\Interview;
use Modules\HR\Models\InterviewEvaluation;
use Modules\HR\Models\JobApplication;

class InterviewService
{
    /**
     * Schedule a new interview session.
     */
    public function scheduleInterview(array $data): Interview
    {
        return DB::transaction(function () use ($data) {
            $application = JobApplication::findOrFail($data['job_application_id']);

            // Calculate interview round
            $previousCount = Interview::where('job_application_id', $application->id)->count();
            $round = $previousCount + 1;

            $interview = Interview::create([
                'job_application_id' => $application->id,
                'job_opening_id'     => $application->job_opening_id,
                'candidate_id'       => $application->candidate_id,
                'interview_round'    => $round,
                'title'              => $data['title'] ?? (__('Interview Round') . ' #' . $round),
                'type'               => $data['type'],
                'mode'               => $data['mode'],
                'status'             => InterviewStatus::SCHEDULED,
                'scheduled_at'       => Carbon::parse($data['scheduled_at']),
                'duration_minutes'   => $data['duration_minutes'] ?? 45,
                'location'           => $data['location'] ?? null,
                'meeting_link'       => $data['meeting_link'] ?? null,
                'notes'              => $data['notes'] ?? null,
                'created_by'         => auth()->id(),
            ]);

            // Attach interviewers
            if (!empty($data['interviewer_ids'])) {
                $syncData = [];
                foreach ($data['interviewer_ids'] as $idx => $empId) {
                    $syncData[$empId] = ['is_primary' => ($idx === 0)];
                }
                $interview->interviewers()->sync($syncData);
            }

            // Automatically transition application stage to INTERVIEW in pipeline if still in new or screening
            if (in_array($application->stage, [ApplicationStage::NEW, ApplicationStage::SCREENING])) {
                $application->update(['stage' => ApplicationStage::INTERVIEW]);
            }

            return $interview;
        });
    }

    /**
     * Update an interview session details, logistics, mode, type, status, and committee.
     */
    public function updateInterview(Interview $interview, array $data): Interview
    {
        return DB::transaction(function () use ($interview, $data) {
            $interview->update([
                'title'            => $data['title'] ?? $interview->title,
                'type'             => $data['type'] ?? $interview->type,
                'mode'             => $data['mode'] ?? $interview->mode,
                'status'           => $data['status'] ?? $interview->status,
                'scheduled_at'     => isset($data['scheduled_at']) ? Carbon::parse($data['scheduled_at']) : $interview->scheduled_at,
                'duration_minutes' => $data['duration_minutes'] ?? $interview->duration_minutes,
                'location'         => $data['location'] ?? null,
                'meeting_link'     => $data['meeting_link'] ?? null,
                'notes'            => $data['notes'] ?? $interview->notes,
            ]);

            if (isset($data['interviewer_ids'])) {
                $syncData = [];
                foreach ($data['interviewer_ids'] as $idx => $empId) {
                    $syncData[$empId] = ['is_primary' => ($idx === 0)];
                }
                $interview->interviewers()->sync($syncData);
            }

            return $interview;
        });
    }

    /**
     * Submit an evaluation scorecard for an interview.
     */
    public function submitEvaluation(Interview $interview, int $interviewerId, array $data): InterviewEvaluation
    {
        return DB::transaction(function () use ($interview, $interviewerId, $data) {
            $scores = array_filter([
                $data['technical_score'] ?? null,
                $data['communication_score'] ?? null,
                $data['problem_solving_score'] ?? null,
                $data['cultural_fit_score'] ?? null,
                $data['experience_score'] ?? null,
            ], fn ($val) => !is_null($val) && (int)$val > 0);

            $average = count($scores) > 0 ? round(array_sum($scores) / count($scores), 2) : 0.00;

            $evaluation = InterviewEvaluation::updateOrCreate(
                [
                    'interview_id'   => $interview->id,
                    'interviewer_id' => $interviewerId,
                ],
                [
                    'technical_score'       => $data['technical_score'] ?? null,
                    'communication_score'   => $data['communication_score'] ?? null,
                    'problem_solving_score' => $data['problem_solving_score'] ?? null,
                    'cultural_fit_score'    => $data['cultural_fit_score'] ?? null,
                    'experience_score'      => $data['experience_score'] ?? null,
                    'average_score'         => $average,
                    'recommendation'        => $data['recommendation'] ?? EvaluationRecommendation::NEUTRAL,
                    'strengths'             => $data['strengths'] ?? null,
                    'weaknesses'            => $data['weaknesses'] ?? null,
                    'notes'                 => $data['notes'] ?? null,
                    'submitted_at'          => now(),
                ]
            );

            // Recalculate overall score of the interview
            $allEvaluations = $interview->evaluations()->get();
            $overallScore = $allEvaluations->avg('average_score');
            $interview->update([
                'overall_score' => $overallScore ? round($overallScore, 2) : null,
                'status'        => InterviewStatus::COMPLETED,
            ]);

            // Sync rating to the JobApplication for Kanban display (1-5 stars)
            if ($overallScore && $interview->jobApplication) {
                $interview->jobApplication->update([
                    'rating' => (int) round($overallScore),
                ]);
            }

            return $evaluation;
        });
    }

    /**
     * Reschedule an interview.
     */
    public function rescheduleInterview(Interview $interview, string $newDateTime, ?string $reason = null): Interview
    {
        $interview->update([
            'scheduled_at' => Carbon::parse($newDateTime),
            'status'       => InterviewStatus::RESCHEDULED,
            'notes'        => $interview->notes . ($reason ? "\n" . __('Rescheduled: ') . $reason : ''),
        ]);

        return $interview;
    }

    /**
     * Cancel an interview.
     */
    public function cancelInterview(Interview $interview, ?string $reason = null): Interview
    {
        $interview->update([
            'status' => InterviewStatus::CANCELLED,
            'notes'  => $interview->notes . ($reason ? "\n" . __('Cancelled: ') . $reason : ''),
        ]);

        return $interview;
    }
}
