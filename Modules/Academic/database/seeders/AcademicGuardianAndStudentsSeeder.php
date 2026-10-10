<?php

namespace Modules\Academic\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Academic\Models\AcademicClass;
use Modules\Academic\Models\AcademicFee;
use Modules\Academic\Models\AcademicFeeInstallment;
use Modules\Academic\Models\AcademicGuardian;
use Modules\Academic\Models\AcademicStage;
use Modules\Academic\Models\AcademicStudent;
use Modules\Academic\Models\AcademicStudentAttendance;
use Modules\Academic\Models\AcademicStudentFee;
use Modules\Academic\Models\AcademicStudentParent;
use Modules\Academic\Models\AcademicYear;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;

class AcademicGuardianAndStudentsSeeder extends Seeder
{
    //------------------------------------------
    /**
     * Run the database seeds.
     * سيم إنشاء ولي أمر ومعه طفلين مرتبطين به
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Get or create basic dependencies
            $nursery = Nursery::first();
            $branch  = NurseryBranch::where('nursery_id', $nursery?->id)->first() ?? NurseryBranch::first();
            $year    = AcademicYear::where('status', 'active')->first() ?? AcademicYear::first();
            $stage   = AcademicStage::where('institution_id', $nursery?->id)->first() ?? AcademicStage::first();
            $class   = AcademicClass::where('institution_id', $nursery?->id)->first() ?? AcademicClass::first();

            $institutionId = $nursery?->id ?? 1;
            $branchId      = $branch?->id ?? 1;
            $yearId        = $year?->id ?? 1;
            $stageId       = $stage?->id ?? 1;
            $classId       = $class?->id ?? 1;

            // 2. Create Guardian (ولي الأمر)
            $guardian = AcademicGuardian::updateOrCreate(
                ['civil_id' => '285041201948'],
                [
                    'guardian_code'  => 'GRD-1001',
                    'institution_id' => $institutionId,
                    'branch_id'      => $branchId,
                    'phone_code'     => '+965',
                    'phone'          => '98765432',
                    'email'          => 'abdullah.salem@example.com',
                    'status'         => AcademicGuardian::STATUS_ACTIVE,
                ]
            );

            // Update translations for Guardian
            $guardian->translateOrNew('ar')->name = 'عبدالله محمد السالم';
            $guardian->translateOrNew('en')->name = 'Abdullah Mohamed Al-Salem';
            $guardian->save();

            // 3. Create Child 1 (الطفل الأول: محمد)
            $student1 = AcademicStudent::updateOrCreate(
                ['civil_id' => '320041501234'],
                [
                    'student_code'                 => 'STU-1001',
                    'academic_guardian_id'         => $guardian->id,
                    'institution_id'               => $institutionId,
                    'branch_id'                    => $branchId,
                    'academic_year_id'             => $yearId,
                    'academic_stage_id'            => $stageId,
                    'academic_class_id'            => $classId,
                    'birth_date'                   => '2021-04-15',
                    'gender'                       => AcademicStudent::GENDER_MALE,
                    'status'                       => AcademicStudent::STATUS_ACTIVE,
                    'terms_accepted'               => true,
                    'financial_policy_accepted'    => true,
                    'pickup_policy_accepted'       => true,
                    'medical_declaration_accepted' => true,
                ]
            );

            $student1->translateOrNew('ar')->name = 'محمد عبدالله السالم';
            $student1->translateOrNew('en')->name = 'Mohamed Abdullah Al-Salem';
            $student1->save();

            // Parents info for Child 1
            $this->seedParentsForStudent($student1, $guardian);

            // Attendance records for Child 1
            $this->seedAttendanceForStudent($student1, $classId, $institutionId, $branchId, true);

            // Fee assignment for Child 1
            $this->seedFeeForStudent($student1);

            // 4. Create Child 2 (الطفل الثاني: سارة)
            $student2 = AcademicStudent::updateOrCreate(
                ['civil_id' => '322092004567'],
                [
                    'student_code'                 => 'STU-1002',
                    'academic_guardian_id'         => $guardian->id,
                    'institution_id'               => $institutionId,
                    'branch_id'                    => $branchId,
                    'academic_year_id'             => $yearId,
                    'academic_stage_id'            => $stageId,
                    'academic_class_id'            => $classId,
                    'birth_date'                   => '2023-09-20',
                    'gender'                       => AcademicStudent::GENDER_FEMALE,
                    'status'                       => AcademicStudent::STATUS_ACTIVE,
                    'terms_accepted'               => true,
                    'financial_policy_accepted'    => true,
                    'pickup_policy_accepted'       => true,
                    'medical_declaration_accepted' => true,
                ]
            );

            $student2->translateOrNew('ar')->name = 'سارة عبدالله السالم';
            $student2->translateOrNew('en')->name = 'Sara Abdullah Al-Salem';
            $student2->save();

            // Parents info for Child 2
            $this->seedParentsForStudent($student2, $guardian);

            // Attendance records for Child 2
            $this->seedAttendanceForStudent($student2, $classId, $institutionId, $branchId, false);

            // Fee assignment for Child 2
            $this->seedFeeForStudent($student2);
        });
    }

    //------------------------------------------
    /**
     * Seed parents info (Father & Mother) for a student.
     */
    protected function seedParentsForStudent(AcademicStudent $student, AcademicGuardian $guardian): void
    {
        // Father
        AcademicStudentParent::updateOrCreate(
            [
                'academic_student_id' => $student->id,
                'parent_type'         => AcademicStudentParent::TYPE_FATHER,
            ],
            [
                'academic_guardian_id' => $guardian->id,
                'name'                 => 'عبدالله محمد السالم',
                'civil_id'             => $guardian->civil_id,
                'nationality'          => 'كويتي',
                'occupation'           => 'مهندس',
                'workplace'            => 'وزارة الأشغال العامة',
                'phone'                => $guardian->phone,
                'email'                => $guardian->email,
                'marital_status'       => AcademicStudentParent::MARITAL_MARRIED,
            ]
        );

        // Mother
        AcademicStudentParent::updateOrCreate(
            [
                'academic_student_id' => $student->id,
                'parent_type'         => AcademicStudentParent::TYPE_MOTHER,
            ],
            [
                'academic_guardian_id' => $guardian->id,
                'name'                 => 'فاطمة أحمد الكندري',
                'civil_id'             => '289071501122',
                'nationality'          => 'كويتية',
                'occupation'           => 'معلمة',
                'workplace'            => 'وزارة التربية',
                'phone'                => '98765433',
                'email'                => 'fatima.kandari@example.com',
                'marital_status'       => AcademicStudentParent::MARITAL_MARRIED,
            ]
        );
    }

    //------------------------------------------
    /**
     * Seed attendance records for student.
     */
    protected function seedAttendanceForStudent(AcademicStudent $student, int $classId, int $institutionId, int $branchId, bool $alwaysPresent): void
    {
        $dates = [
            Carbon::now()->subDays(2)->toDateString() => AcademicStudentAttendance::STATUS_ATTEND,
            Carbon::now()->subDay()->toDateString()    => $alwaysPresent ? AcademicStudentAttendance::STATUS_ATTEND : AcademicStudentAttendance::STATUS_ABSENT,
            Carbon::now()->toDateString()             => AcademicStudentAttendance::STATUS_ATTEND,
        ];

        foreach ($dates as $date => $status) {
            AcademicStudentAttendance::updateOrCreate(
                [
                    'academic_student_id' => $student->id,
                    'date'                => $date,
                ],
                [
                    'academic_class_id'   => $classId,
                    'institution_id'      => $institutionId,
                    'branch_id'           => $branchId,
                    'status'              => $status,
                    'approved'            => true,
                    'approved_at'         => now(),
                    'notes'               => $status === AcademicStudentAttendance::STATUS_ATTEND ? 'حضور منتظم' : 'غياب بعذر',
                ]
            );
        }
    }

    //------------------------------------------
    /**
     * Seed fee assignment for student if an academic fee exists.
     */
    protected function seedFeeForStudent(AcademicStudent $student): void
    {
        $fee = AcademicFee::where('status', 'active')->first();
        if (! $fee) {
            return;
        }

        $studentFee = AcademicStudentFee::updateOrCreate(
            [
                'academic_student_id' => $student->id,
                'academic_fee_id'     => $fee->id,
            ],
            [
                'total_amount'        => $fee->amount,
                'installments_count'  => 2,
                'installment_amount'  => $fee->amount / 2,
                'start_date'          => Carbon::now()->startOfMonth()->toDateString(),
                'status'              => AcademicStudentFee::STATUS_ACTIVE,
            ]
        );

        // Create 2 installments
        for ($i = 1; $i <= 2; $i++) {
            AcademicFeeInstallment::updateOrCreate(
                [
                    'academic_student_fee_id' => $studentFee->id,
                    'installment_number'      => $i,
                ],
                [
                    'amount'                  => $fee->amount / 2,
                    'paid_amount'             => $i === 1 ? $fee->amount / 2 : 0,
                    'due_date'                => Carbon::now()->addMonths($i - 1)->toDateString(),
                    'status'                  => $i === 1 ? 'paid' : 'pending',
                ]
            );
        }
    }
}
