<?php

namespace Modules\Settings\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Settings\Enums\EmailTemplateKey;
use Modules\Settings\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds and synchronizes all system email templates based on EmailTemplateKey Enum.
     */
    public function run(): void
    {
        foreach (EmailTemplateKey::cases() as $case) {
            $default = $case->defaultContent();

            EmailTemplate::updateOrCreate(
                ['code' => $case->value],
                [
                    'module'      => $case->module(),
                    'name'        => $case->label(),
                    'name_ar'     => $case->label(),
                    'subject_ar'  => $default['subject_ar'],
                    'subject_en'  => $default['subject_en'],
                    'body_ar'     => $default['body_ar'],
                    'body_en'     => $default['body_en'],
                    'is_active'   => true,
                    'is_system'   => true,
                ]
            );
        }
    }
}
