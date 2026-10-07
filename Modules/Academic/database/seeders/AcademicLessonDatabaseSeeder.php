<?php

namespace Modules\Academic\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Academic\Models\AcademicLesson;
use Modules\Academic\Models\AcademicStage;
use Modules\Academic\Models\AcademicSubject;
use Modules\Nursery\Models\Nursery;

class AcademicLessonDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds for Academic Lessons.
     */
    public function run(): void
    {
        $adminUser = User::first();
        $adminId = $adminUser?->id ?? 1;

        $nurseries = Nursery::all();

        if ($nurseries->isEmpty()) {
            return;
        }

        foreach ($nurseries as $nursery) {
            $stages = AcademicStage::where('institution_id', $nursery->id)->get();
            $subjects = AcademicSubject::where('institution_id', $nursery->id)->with('translations')->get();

            if ($stages->isEmpty() || $subjects->isEmpty()) {
                continue;
            }

            $firstStage = $stages->first();

            foreach ($subjects as $index => $subject) {
                $subjectNameAr = $subject->translate('ar')?->name ?? 'المادة';
                $subjectNameEn = $subject->translate('en')?->name ?? 'Subject';

                $lessonData = [
                    'title_ar'   => "الدرس الأول: مدخل ومفاهيم أساسية في {$subjectNameAr}",
                    'title_en'   => "Lesson 1: Introduction and Core Concepts in {$subjectNameEn}",
                    'content_ar' => "شرح مفصل للدرس الأول وأهم الأهداف التعليمية والأنشطة التفاعلية مع الطلاب لمادة {$subjectNameAr}.",
                    'content_en' => "Comprehensive overview and interactive learning activities for Lesson 1 in {$subjectNameEn}.",
                    'start_date' => now()->toDateString(),
                    'end_date'   => now()->addDays(14)->toDateString(),
                    'order'      => $index + 1,
                    'status'     => AcademicLesson::STATUS_ACTIVE,
                ];

                $lesson = AcademicLesson::where('institution_id', $nursery->id)
                    ->where('academic_stage_id', $firstStage->id)
                    ->where('academic_subject_id', $subject->id)
                    ->whereHas('translations', function ($q) use ($lessonData) {
                        $q->where('title', $lessonData['title_ar']);
                    })
                    ->first();

                if (!$lesson) {
                    $lesson = new AcademicLesson();
                    $lesson->institution_id      = $nursery->id;
                    $lesson->academic_stage_id   = $firstStage->id;
                    $lesson->academic_subject_id = $subject->id;
                    $lesson->start_date          = $lessonData['start_date'];
                    $lesson->end_date            = $lessonData['end_date'];
                    $lesson->order               = $lessonData['order'];
                    $lesson->status              = $lessonData['status'];
                    $lesson->created_by          = $adminId;

                    $lesson->translateOrNew('ar')->title   = $lessonData['title_ar'];
                    $lesson->translateOrNew('ar')->content = $lessonData['content_ar'];

                    $lesson->translateOrNew('en')->title   = $lessonData['title_en'];
                    $lesson->translateOrNew('en')->content = $lessonData['content_en'];

                    $lesson->save();
                }
            }
        }
    }
}
