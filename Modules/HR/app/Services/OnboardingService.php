<?php

namespace Modules\HR\Services;

use Illuminate\Support\Facades\DB;
use Modules\HR\Enums\ApplicationStage;
use Modules\HR\Enums\ApplicationStatus;
use Modules\HR\Enums\EmployeeStatus;
use Modules\HR\Enums\EmploymentType;
use Modules\HR\Enums\Gender;
use Modules\HR\Enums\OnboardingStatus;
use Modules\HR\Models\Employee;
use Modules\HR\Models\JobApplication;
use Modules\HR\Models\Onboarding;
use Modules\HR\Models\OnboardingTask;

class OnboardingService
{
    public function startOnboarding(array $data, int $userId): Onboarding
    {
        return DB::transaction(function () use ($data, $userId) {
            $year = date('Y');
            $latestId = (Onboarding::max('id') ?? 0) + 1;
            $data['onboarding_code'] = 'ONB-' . $year . '-' . str_pad((string)$latestId, 4, '0', STR_PAD_LEFT);
            $data['created_by'] = $userId;
            $data['status'] = OnboardingStatus::PENDING->value;
            $data['progress_percentage'] = 0;

            $onboarding = Onboarding::create($data);

            // Default task checklist
            $defaultTasks = [
                ['title' => __('Collect required official onboarding documents'), 'category' => 'documentation'],
                ['title' => __('Sign official employment contract'), 'category' => 'documentation'],
                ['title' => __('Create corporate email & system account'), 'category' => 'it_access'],
                ['title' => __('Assign laptop & access peripherals'), 'category' => 'it_access'],
                ['title' => __('Office orientation & department team introduction'), 'category' => 'orientation'],
                ['title' => __('Review company work policy & code of conduct'), 'category' => 'orientation'],
                ['title' => __('Setup office desk & workstation'), 'category' => 'facilities'],
            ];

            foreach ($defaultTasks as $task) {
                $onboarding->tasks()->create($task);
            }

            return $onboarding->fresh(['tasks']);
        });
    }

    public function toggleTask(int $taskId): array
    {
        return DB::transaction(function () use ($taskId) {
            $task = OnboardingTask::findOrFail($taskId);
            $newCompleted = !$task->is_completed;

            $task->update([
                'is_completed' => $newCompleted,
                'completed_at' => $newCompleted ? now() : null,
            ]);

            $onboarding = $task->onboarding;
            $pct = $onboarding->recalculateProgress();

            return [
                'task_id'      => $task->id,
                'is_completed' => $task->is_completed,
                'completed_at' => $task->completed_at ? $task->completed_at->format('Y-m-d H:i') : null,
                'progress'     => $pct,
                'status'       => $onboarding->status->value,
                'status_label' => $onboarding->status->label(),
                'status_badge' => $onboarding->status->badgeClass(),
            ];
        });
    }

    public function addTask(int $onboardingId, array $data): OnboardingTask
    {
        $onboarding = Onboarding::findOrFail($onboardingId);
        $task = $onboarding->tasks()->create([
            'title'       => $data['title'],
            'category'    => $data['category'] ?? 'documentation',
            'assigned_to' => $data['assigned_to'] ?? null,
            'notes'       => $data['notes'] ?? null,
        ]);

        $onboarding->recalculateProgress();
        return $task;
    }

    public function convertToEmployee(int $onboardingId, array $data): Employee
    {
        return DB::transaction(function () use ($onboardingId, $data) {
            $onboarding = Onboarding::with(['candidate', 'jobOffer', 'jobApplication'])->findOrFail($onboardingId);
            $candidate  = $onboarding->candidate;
            $offer      = $onboarding->jobOffer;

            // Generate Employee Code if not specified
            $empCode = $data['employee_code'] ?? null;
            if (!$empCode) {
                $maxId = (Employee::max('id') ?? 0) + 1;
                $empCode = 'EMP-' . str_pad((string)$maxId, 4, '0', STR_PAD_LEFT);
            }

            $nameAr = $data['name_ar'] ?? ($candidate ? $candidate->full_name : 'موظف جديد');
            $nameEn = $data['name_en'] ?? ($candidate ? $candidate->full_name : 'New Employee');

            $employeeData = [
                'employee_code'   => $empCode,
                'department_id'   => $data['department_id'] ?? $onboarding->department_id ?? $offer?->department_id,
                'job_title_id'    => $data['job_title_id'] ?? $onboarding->job_title_id ?? $offer?->job_title_id,
                'basic_salary'    => $data['basic_salary'] ?? $offer?->basic_salary ?? 0,
                'hire_date'       => $data['hire_date'] ?? $onboarding->start_date ?? now()->toDateString(),
                'email'           => $data['email'] ?? $candidate?->email,
                'phone'           => $data['phone'] ?? $candidate?->phone,
                'nationality'     => $data['nationality'] ?? $candidate?->nationality,
                'gender'          => $data['gender'] ?? Gender::MALE->value,
                'employment_type' => $data['employment_type'] ?? $offer?->employment_type ?? EmploymentType::FULL_TIME->value,
                'status'          => EmployeeStatus::ACTIVE->value,
                'ar'              => ['name' => $nameAr],
                'en'              => ['name' => $nameEn],
            ];

            if (!empty($data['national_id'])) {
                $employeeData['national_id'] = $data['national_id'];
            }

            $employee = Employee::create($employeeData);

            // Link Employee to Onboarding
            $onboarding->update([
                'employee_id' => $employee->id,
                'status'      => OnboardingStatus::IN_PROGRESS->value,
            ]);

            // Update Job Application to HIRED
            if ($onboarding->job_application_id) {
                JobApplication::where('id', $onboarding->job_application_id)->update([
                    'stage'    => ApplicationStage::HIRED->value,
                    'status'   => ApplicationStatus::HIRED->value,
                    'hired_at' => now(),
                ]);
            }

            return $employee;
        });
    }
}
