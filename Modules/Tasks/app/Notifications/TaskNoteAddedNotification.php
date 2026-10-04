<?php

namespace Modules\Tasks\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Tasks\Models\Task;
use Modules\Tasks\Models\TaskNote;

class TaskNoteAddedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Task $task,
        public TaskNote $note,
        public User $author
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
             $rendered = $templateService->render('task_note_added', [
                 '{employee_name}' => $notifiable->name,
                 '{task_title}'    => $this->task->title,
                 '{task_code}'     => 'TSK-' . $this->task->id,
                 '{note_author}'   => $this->author->name,
                 '{note_content}'  => $this->note->note,
                 '{task_url}'      => $taskUrl,
             ]);

             if ($rendered) {
                 return (new MailMessage)
                     ->subject($rendered['subject'])
                     ->html($rendered['html']);
             }
         }

         return (new MailMessage)
             ->subject(__('New Note on Task: :title', ['title' => $this->task->title]))
             ->greeting(__('Hello :name,', ['name' => $notifiable->name]))
             ->line(__(':author added a new note on task ":title":', [
                 'author' => $this->author->name,
                 'title'  => $this->task->title,
             ]))
             ->line('"' . $this->note->note . '"')
             ->action(__('View Task & Comments'), $taskUrl)
             ->line(__('Thank you for using our task management system!'));
     }

    /**
     * Get the array representation of the notification (for database).
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'task_id'     => $this->task->id,
            'note_id'     => $this->note->id,
            'author_name' => $this->author->name,
            'action_url'  => route('tasks.tasks.show', $this->task->id),
            'message'     => __(':author commented on task: :title', [
                'author' => $this->author->name,
                'title'  => $this->task->title,
            ]),
        ];
    }
}
