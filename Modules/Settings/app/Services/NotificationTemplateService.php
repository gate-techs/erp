<?php

namespace Modules\Settings\Services;

use Illuminate\Support\Facades\DB;
use Modules\Settings\Models\NotificationTemplate;

class NotificationTemplateService
{
    public function all(?string $module = null)
    {
        return NotificationTemplate::with('translations')
            ->when($module && $module !== 'all', fn ($query) => $query->where('module', $module))
            ->orderBy('module')->orderBy('name_ar')->get();
    }

    public function find(int $id): ?NotificationTemplate
    {
        return NotificationTemplate::with('translations')->find($id);
    }

    public function update(NotificationTemplate $template, array $data, array $translations): NotificationTemplate
    {
        return DB::transaction(function () use ($template, $data, $translations) {
            $template->update($data);

            foreach ($translations as $locale => $translation) {
                $template->translations()->updateOrCreate(
                    ['locale' => $locale],
                    $translation
                );
            }

            return $template->fresh('translations');
        });
    }

    public function toggle(NotificationTemplate $template): bool
    {
        $template->update(['is_active' => ! $template->is_active]);

        return (bool) $template->is_active;
    }
}
