<?php

namespace Modules\Nursery\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;
use Modules\Settings\Enums\GeneralSettingType;
use Modules\Settings\Models\GeneralSetting;

class NurseryDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds for Nursery and its branches.
     */
    public function run(): void
    {
        $this->call(NurseryPermissionSeeder::class);

        $adminUser = User::first();
        $adminId = $adminUser?->id ?? 1;

        // 1. Create or Find Main Nursery: "استرو" (Astro)
        $nursery = Nursery::whereTranslation('name', 'حضانة استرو')
            ->orWhereTranslation('name', 'استرو')
            ->first();

        if (!$nursery) {
            $nursery = new Nursery();
            $nursery->fill([
                'status'     => 'active',
                'whatsapp'   => '+96590000000',
                'email'      => 'info@astro-nursery.com',
                'created_by' => $adminId,
            ]);

            $nursery->translateOrNew('ar')->name = 'حضانة استرو';
            $nursery->translateOrNew('ar')->description = 'حضانة استرو النموذجية لتنمية مهارات ورعاية الأطفال المبكرة';

            $nursery->translateOrNew('en')->name = 'Astro Nursery';
            $nursery->translateOrNew('en')->description = 'Astro Model Nursery for Early Childhood Care and Development';

            $nursery->save();
        }

        // Fetch Mubarak Al-Kabeer & Hawalli governorates
        $mubarakGov = GeneralSetting::ofType(GeneralSettingType::GOVERNMENT)
            ->whereTranslationLike('name', '%مبارك الكبير%')
            ->first();

        $hawalliGov = GeneralSetting::ofType(GeneralSettingType::GOVERNMENT)
            ->whereTranslationLike('name', '%حولي%')
            ->first();

        // 2. Branch 1: فرع أبو فطيرة (Abu Fatira Branch)
        $branchAbuFatira = NurseryBranch::where('nursery_id', $nursery->id)
            ->where(function ($q) {
                $q->whereTranslationLike('name', '%أبو فطيرة%')
                  ->orWhereTranslationLike('name', '%ابو فطيرة%');
            })
            ->first();

        if (!$branchAbuFatira) {
            $branchAbuFatira = new NurseryBranch();
            $branchAbuFatira->fill([
                'nursery_id'    => $nursery->id,
                'government_id' => $mubarakGov?->id ?? 54,
                'status'        => 'active',
                'whatsapp'      => '+96590000001',
                'email'         => 'abufatira@astro-nursery.com',
                'created_by'    => $adminId,
            ]);

            $branchAbuFatira->translateOrNew('ar')->name = 'فرع أبو فطيرة';
            $branchAbuFatira->translateOrNew('ar')->description = 'فرع حضانة استرو بمنطقة أبو فطيرة - مجهز بأحدث وسائل التعليم والترفيه';
            $branchAbuFatira->translateOrNew('ar')->address = 'دولة الكويت - محافظة مبارك الكبير - منطقة أبو فطيرة - ق 2';

            $branchAbuFatira->translateOrNew('en')->name = 'Abu Fatira Branch';
            $branchAbuFatira->translateOrNew('en')->description = 'Astro Nursery Abu Fatira Branch - fully equipped with modern learning and play facilities';
            $branchAbuFatira->translateOrNew('en')->address = 'State of Kuwait - Mubarak Al-Kabeer - Abu Fatira - Block 2';

            $branchAbuFatira->save();
        }

        // 3. Branch 2: فرع الزهراء (Al Zahraa Branch)
        $branchZahraa = NurseryBranch::where('nursery_id', $nursery->id)
            ->whereTranslationLike('name', '%الزهراء%')
            ->first();

        if (!$branchZahraa) {
            $branchZahraa = new NurseryBranch();
            $branchZahraa->fill([
                'nursery_id'    => $nursery->id,
                'government_id' => $hawalliGov?->id ?? 16,
                'status'        => 'active',
                'whatsapp'      => '+96590000002',
                'email'         => 'alzahraa@astro-nursery.com',
                'created_by'    => $adminId,
            ]);

            $branchZahraa->translateOrNew('ar')->name = 'فرع الزهراء';
            $branchZahraa->translateOrNew('ar')->description = 'فرع حضانة استرو بمنطقة الزهراء - بيئة آمنة ومحفزة لنمو وتطور الطفل';
            $branchZahraa->translateOrNew('ar')->address = 'دولة الكويت - محافظة حولي - منطقة الزهراء - ق 4';

            $branchZahraa->translateOrNew('en')->name = 'Al Zahraa Branch';
            $branchZahraa->translateOrNew('en')->description = 'Astro Nursery Al Zahraa Branch - safe and stimulating environment for child growth';
            $branchZahraa->translateOrNew('en')->address = 'State of Kuwait - Hawalli - Al Zahraa - Block 4';

            $branchZahraa->save();
        }
    }
}
