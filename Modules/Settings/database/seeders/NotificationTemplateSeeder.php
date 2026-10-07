<?php

namespace Modules\Settings\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Settings\Models\NotificationTemplate;

class NotificationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'code' => 'leave_submitted',
                'type' => 'leave_submitted',
                'module' => 'hr',
                'name_ar' => 'تقديم طلب إجازة',
                'name_en' => 'Leave Request Submitted',
                'description' => 'إشعار عند تقديم طلب إجازة جديد.',
                'icon' => 'bx-calendar-plus',
                'color' => 'info',
                'variables' => ['employee_name', 'leave_type', 'start_date', 'end_date', 'total_days', 'action_url'],
                'translations' => [
                    'ar' => ['title' => 'تم تقديم طلب الإجازة', 'message' => 'تم تقديم طلب إجازة جديد من {employee_name} من {start_date} إلى {end_date}.', 'action_url' => '/portal/requests'],
                    'en' => ['title' => 'Leave request submitted', 'message' => 'A new leave request was submitted by {employee_name} from {start_date} to {end_date}.', 'action_url' => '/portal/requests'],
                ],
            ],
            [
                'code' => 'leave_approved',
                'type' => 'leave_approved',
                'module' => 'hr',
                'name_ar' => 'قبول طلب إجازة',
                'name_en' => 'Leave Request Approved',
                'description' => 'إشعار عند اعتماد طلب الإجازة.',
                'icon' => 'bx-calendar-check',
                'color' => 'success',
                'variables' => ['employee_name', 'leave_type', 'start_date', 'end_date', 'total_days', 'action_url'],
                'translations' => [
                    'ar' => ['title' => 'تم قبول طلب الإجازة', 'message' => 'تمت الموافقة على طلب إجازتك من {start_date} إلى {end_date}.', 'action_url' => '/portal/requests'],
                    'en' => ['title' => 'Leave request approved', 'message' => 'Your leave request from {start_date} to {end_date} has been approved.', 'action_url' => '/portal/requests'],
                ],
            ],
            [
                'code' => 'leave_rejected',
                'type' => 'leave_rejected',
                'module' => 'hr',
                'name_ar' => 'رفض طلب إجازة',
                'name_en' => 'Leave Request Rejected',
                'description' => 'إشعار عند رفض طلب الإجازة.',
                'icon' => 'bx-calendar-x',
                'color' => 'danger',
                'variables' => ['employee_name', 'leave_type', 'rejection_reason', 'action_url'],
                'translations' => [
                    'ar' => ['title' => 'تم رفض طلب الإجازة', 'message' => 'تم رفض طلب الإجازة الخاص بك. السبب: {rejection_reason}', 'action_url' => '/portal/requests'],
                    'en' => ['title' => 'Leave request rejected', 'message' => 'Your leave request was rejected. Reason: {rejection_reason}', 'action_url' => '/portal/requests'],
                ],
            ],
            [
                'code' => 'task_assigned',
                'type' => 'task_assigned',
                'module' => 'tasks',
                'name_ar' => 'إسناد مهمة',
                'name_en' => 'Task Assigned',
                'description' => 'إشعار عند إسناد مهمة إلى موظف.',
                'icon' => 'bx-task',
                'color' => 'primary',
                'variables' => ['employee_name', 'task_title', 'priority', 'due_date', 'action_url'],
                'translations' => [
                    'ar' => ['title' => 'تم إسناد مهمة جديدة إليك', 'message' => 'تم إسناد المهمة {task_title} إليك. موعد التسليم: {due_date}.', 'action_url' => '/tasks'],
                    'en' => ['title' => 'A new task was assigned to you', 'message' => 'The task {task_title} was assigned to you. Due date: {due_date}.', 'action_url' => '/tasks'],
                ],
            ],
            [
                'code' => 'task_note_added',
                'type' => 'task_note_added',
                'module' => 'tasks',
                'name_ar' => 'إضافة تعليق على مهمة',
                'name_en' => 'Task Note Added',
                'description' => 'إشعار عند إضافة تعليق جديد على مهمة.',
                'icon' => 'bx-message-rounded-detail',
                'color' => 'warning',
                'variables' => ['employee_name', 'task_title', 'author_name', 'note_content', 'action_url'],
                'translations' => [
                    'ar' => ['title' => 'تمت إضافة تعليق على المهمة', 'message' => 'أضاف {author_name} تعليقًا جديدًا على المهمة {task_title}.', 'action_url' => '/tasks'],
                    'en' => ['title' => 'A new note was added to a task', 'message' => '{author_name} added a new note to {task_title}.', 'action_url' => '/tasks'],
                ],
            ],
        ];

        foreach ($templates as $data) {
            $translations = $data['translations'];
            unset($data['translations']);

            $template = NotificationTemplate::updateOrCreate(['code' => $data['code']], array_merge($data, [
                'is_active' => true,
                'is_system' => true,
            ]));

            foreach ($translations as $locale => $translation) {
                $template->translations()->updateOrCreate(['locale' => $locale], $translation);
            }
        }
    }
}
