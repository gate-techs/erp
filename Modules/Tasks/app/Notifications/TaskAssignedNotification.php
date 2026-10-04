<?php

namespace Modules\Tasks\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Tasks\Models\Task;

class TaskAssignedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Task $task
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $taskUrl = route('tasks.tasks.show', $this->task->id);

        // Check if dynamic email template is configured
        if (class_exists(\Modules\Settings\Services\EmailTemplateService::class)) {
            $templateService = app(\Modules\Settings\Services\EmailTemplateService::class);
            $rendered = $templateService->render('task_assigned', [
                '{employee_name}' => $notifiable->name,
                '{task_title}'    => $this->task->title,
                '{task_code}'     => 'TSK-' . $this->task->id,
                '{priority}'      => $this->task->priority?->label() ?? '-',
                '{status}'        => $this->task->status?->label() ?? '-',
                '{due_date}'      => $this->task->completion_date?->format('Y-m-d') ?? __('Not specified'),
                '{assigned_by}'   => auth()->user()?->name ?? __('System Administrator'),
                '{task_url}'      => $taskUrl,
            ]);

            if ($rendered) {
                return (new MailMessage)
                    ->subject($rendered['subject'])
                    ->html($rendered['html']);
            }
        }

        return (new MailMessage)
            ->subject(__('New Task Assigned: :title', ['title' => $this->task->title]))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name]))
            ->line(__('A new task has been assigned to you in the system:'))
            ->line('**' . __('Task Title') . ':** ' . $this->task->title)
            ->line('**' . __('Priority') . ':** ' . ($this->task->priority?->label() ?? '-'))
            ->line('**' . __('Due Date') . ':** ' . ($this->task->completion_date?->format('Y-m-d') ?? __('Not specified')))
            ->action(__('View Task Details'), $taskUrl)
            ->line(__('Please review and update the task progress regularly.'));
    }

    /**
     * Get the array representation of the notification (for database).
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'task_id'    => $this->task->id,
            'title'      => $this->task->title,
            'priority'   => $this->task->priority?->value,
            'action_url' => route('tasks.tasks.show', $this->task->id),
            'message'    => __('A new task has been assigned to you: :title', ['title' => $this->task->title]),
        ];
    }
}
