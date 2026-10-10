<?php

namespace Modules\Academic\Services;

use App\Enums\UserType;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Academic\Enums\DiseaseType;
use Modules\Academic\Enums\InformationForm;
use Modules\Academic\Enums\MedicalQuestionnaire1;
use Modules\Academic\Models\AcademicClass;
use Modules\Academic\Models\AcademicGuardian;
use Modules\Academic\Models\AcademicStage;
use Modules\Academic\Models\AcademicStudent;
use Modules\Academic\Models\AcademicStudentAuthorizedPickup;
use Modules\Academic\Models\AcademicStudentDisease;
use Modules\Academic\Models\AcademicStudentEmergencyContact;
use Modules\Academic\Models\AcademicStudentInformationResponse;
use Modules\Academic\Models\AcademicStudentMedicalResponse;
use Modules\Academic\Models\AcademicStudentParent;
use Modules\Academic\Models\AcademicStudentSibling;
use Modules\Academic\Models\AcademicYear;
use Modules\Academic\Repositories\Contracts\AcademicStudentRepositoryInterface;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;
use Modules\Settings\Enums\GeneralSettingType;
use Modules\Settings\Models\GeneralSetting;

class AcademicStudentService
{
    public function __construct(
        protected AcademicStudentRepositoryInterface $repository
    ) {}

    //------------------------------------------
    public function getFilteredQuery(array $filters = []): Builder
    {
        return $this->repository->getFilteredQuery($filters);
    }

    //------------------------------------------
    public function all(): Collection
    {
        return $this->repository->all();
    }

    //------------------------------------------
    public function find(int $id): ?AcademicStudent
    {
        return $this->repository->find($id);
    }

    //------------------------------------------
    public function findWithAllRelations(int $id): ?AcademicStudent
    {
        return $this->repository->findWithAllRelations($id);
    }

    //------------------------------------------
    /**
     * Create a student with all multi-step form data.
     *
     * @throws Exception
     */
    public function create(array $data, ?UploadedFile $photo = null): AcademicStudent
    {
        return DB::transaction(function () use ($data, $photo) {
            // 1. Process Photo
            if ($photo) {
                $data['photo'] = upload_image($photo, 'academic/students');
            }

            // 2. Student Code
            if (empty($data['student_code'])) {
                $data['student_code'] = $this->repository->generateStudentCode();
            }

            // 3. Status
            $data['status'] = $data['status'] ?? AcademicStudent::STATUS_ACTIVE;

            // 4. Timestamps for accepted terms
            if (! empty($data['terms_accepted'])) {
                $data['terms_accepted_at'] = now();
            }
            if (! empty($data['financial_policy_accepted'])) {
                $data['financial_policy_accepted_at'] = now();
            }
            if (! empty($data['pickup_policy_accepted'])) {
                $data['pickup_policy_accepted_at'] = now();
            }
            if (! empty($data['medical_declaration_accepted'])) {
                $data['medical_declaration_accepted_at'] = now();
            }

            // 5. Creator
            $data['created_by'] = auth()->id();

            // 6. Translations
            if (! empty($data['name_ar']) || ! empty($data['name_en'])) {
                $data['ar'] = [
                    'name' => $data['name_ar'] ?? $data['name_en'] ?? '',
                ];
                $data['en'] = [
                    'name' => $data['name_en'] ?? $data['name_ar'] ?? '',
                ];
            }

            // 7. Create Student
            $student = $this->repository->create($data);

            // 7.1 Resolve and Link Guardian
            $guardian = $this->resolveAndLinkGuardian($student, $data);

            // 7.2 Save Parents (Father & Mother)
            $this->saveParents($student, $data, $guardian);

            // 8. Save Siblings
            $this->saveSiblings($student, $data);

            // 9. Save Chronic Diseases
            $this->saveDiseases($student, $data);

            // 10. Save Medical Questionnaire 1
            $this->saveMedicalResponses($student, $data);

            // 11. Save Emergency Contacts
            $this->saveEmergencyContacts($student, $data);

            // 12. Save Authorized Pickups
            $this->saveAuthorizedPickups($student, $data);

            // 13. Save Information Form Responses (1 & 2)
            $this->saveInformationResponses($student, $data);

            return $student->fresh([
                'translations',
                'parents',
                'siblings',
                'diseases',
                'medicalResponses',
                'emergencyContacts',
                'authorizedPickups',
                'informationResponses',
            ]);
        });
    }

    //------------------------------------------
    /**
     * Update student with all multi-step form data.
     *
     * @throws Exception
     */
    public function update(int $id, array $data, ?UploadedFile $photo = null): ?AcademicStudent
    {
        return DB::transaction(function () use ($id, $data, $photo) {
            $student = $this->repository->find($id);
            if (! $student) {
                return null;
            }

            if ($photo) {
                $data['photo'] = upload_image($photo, 'academic/students');
            }

            if (! empty($data['terms_accepted']) && ! $student->terms_accepted_at) {
                $data['terms_accepted_at'] = now();
            }
            if (! empty($data['financial_policy_accepted']) && ! $student->financial_policy_accepted_at) {
                $data['financial_policy_accepted_at'] = now();
            }
            if (! empty($data['pickup_policy_accepted']) && ! $student->pickup_policy_accepted_at) {
                $data['pickup_policy_accepted_at'] = now();
            }
            if (! empty($data['medical_declaration_accepted']) && ! $student->medical_declaration_accepted_at) {
                $data['medical_declaration_accepted_at'] = now();
            }

            if (! empty($data['name_ar']) || ! empty($data['name_en'])) {
                $data['ar'] = [
                    'name' => $data['name_ar'] ?? $student->translate('ar')?->name ?? '',
                ];
                $data['en'] = [
                    'name' => $data['name_en'] ?? $data['name_ar'] ?? $student->translate('en')?->name ?? '',
                ];
            }

            $this->repository->update($id, $data);

            // Update Relations
            $guardian = $this->resolveAndLinkGuardian($student, $data);
            $this->saveParents($student, $data, $guardian);
            $this->saveSiblings($student, $data);
            $this->saveDiseases($student, $data);
            $this->saveMedicalResponses($student, $data);
            $this->saveEmergencyContacts($student, $data);
            $this->saveAuthorizedPickups($student, $data);
            $this->saveInformationResponses($student, $data);

            return $student->fresh([
                'translations',
                'parents',
                'siblings',
                'diseases',
                'medicalResponses',
                'emergencyContacts',
                'authorizedPickups',
                'informationResponses',
            ]);
        });
    }

    //------------------------------------------
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    //------------------------------------------
    public function toggleStatus(int $id): ?AcademicStudent
    {
        return $this->repository->toggleStatus($id);
    }

    //------------------------------------------
    /**
     * Resolve and link or automatically create AcademicGuardian for student.
     */
    protected function resolveAndLinkGuardian(AcademicStudent $student, array $data): ?AcademicGuardian
    {
        $guardianId   = ! empty($data['academic_guardian_id']) ? (int) $data['academic_guardian_id'] : null;
        $guardianType = $data['primary_guardian_type'] ?? 'father'; // 'father', 'mother', or 'existing'

        // 1. Explicit existing guardian chosen
        if (! empty($guardianId)) {
            $guardian = AcademicGuardian::find($guardianId);
            if ($guardian) {
                if ($student->academic_guardian_id !== $guardian->id) {
                    $student->update(['academic_guardian_id' => $guardian->id]);
                }
                return $guardian;
            }
        }

        // 2. Derive from selected parent (father or mother)
        $parentData = $guardianType === 'mother' ? ($data['mother'] ?? []) : ($data['father'] ?? []);
        $name       = trim($parentData['name'] ?? '');
        $phone      = trim($parentData['phone'] ?? '');
        $civilId    = trim($parentData['civil_id'] ?? '');
        $email      = trim($parentData['email'] ?? '');

        // Fallback phone from emergency contacts if not set on parent
        if (empty($phone) && ! empty($data['emergency_contacts'][0]['phone_1'])) {
            $phone = trim($data['emergency_contacts'][0]['phone_1']);
        }

        if (empty($name) && empty($phone) && empty($civilId)) {
            return null;
        }

        // Search for existing guardian by Civil ID or Phone in the same institution
        $guardianQuery = AcademicGuardian::where('institution_id', $student->institution_id);
        if (! empty($civilId)) {
            $guardianQuery->where(function ($q) use ($civilId, $phone) {
                $q->where('civil_id', $civilId);
                if (! empty($phone)) {
                    $q->orWhere('phone', $phone);
                }
            });
        } elseif (! empty($phone)) {
            $guardianQuery->where('phone', $phone);
        } else {
            $guardianQuery->whereHas('translations', function ($q) use ($name) {
                $q->where('name', $name);
            });
        }

        $guardian = $guardianQuery->first();

        // 3. Auto-create Guardian and User account if none exists
        if (! $guardian) {
            $guardianCode = 'G-' . date('Y') . '-' . str_pad((string) (AcademicGuardian::max('id') + 1), 4, '0', STR_PAD_LEFT);
            $user = null;

            if (! empty($phone)) {
                $user = User::where('phone', $phone)->first();
            }
            if (! $user && ! empty($email)) {
                $user = User::where('email', $email)->first();
            }

            if (! $user && (! empty($phone) || ! empty($email))) {
                $user = User::create([
                    'name'      => $name ?: ($guardianType === 'mother' ? __('Mother') : __('Father')),
                    'phone'     => $phone ?: null,
                    'email'     => $email ?: null,
                    'password'  => Hash::make('12345678'),
                    'user_type' => UserType::GUARDIAN,
                    'is_active' => true,
                ]);

                if (method_exists($user, 'assignRole')) {
                    $user->assignRole('guardian');
                }
            }

            $guardian = AcademicGuardian::create([
                'guardian_code'  => $guardianCode,
                'user_id'        => $user?->id,
                'civil_id'       => $civilId ?: null,
                'institution_id' => $student->institution_id,
                'branch_id'      => $student->branch_id,
                'phone'          => $phone ?: '00000000',
                'email'          => $email ?: null,
                'status'         => AcademicGuardian::STATUS_ACTIVE,
                'created_by'     => auth()->id(),
                'ar'             => ['name' => $name ?: ($guardianType === 'mother' ? 'الأم' : 'الأب')],
                'en'             => ['name' => $name ?: ($guardianType === 'mother' ? 'Mother' : 'Father')],
            ]);
        }

        if ($guardian) {
            $student->update(['academic_guardian_id' => $guardian->id]);
        }

        return $guardian;
    }

    //------------------------------------------
    /**
     * Save father and mother details.
     */
    protected function saveParents(AcademicStudent $student, array $data, ?AcademicGuardian $guardian = null): void
    {
        $guardianType = $data['primary_guardian_type'] ?? 'father';
        $fatherGuardianId = ($guardianType === 'father' && $guardian) ? $guardian->id : ($data['father']['academic_guardian_id'] ?? null);
        $motherGuardianId = ($guardianType === 'mother' && $guardian) ? $guardian->id : ($data['mother']['academic_guardian_id'] ?? null);

        // Father
        if (! empty($data['father']['name']) || ! empty($data['father']['phone']) || ! empty($data['father']['civil_id'])) {
            AcademicStudentParent::updateOrCreate(
                [
                    'academic_student_id' => $student->id,
                    'parent_type'          => AcademicStudentParent::TYPE_FATHER,
                ],
                [
                    'academic_guardian_id' => $fatherGuardianId,
                    'name'                 => $data['father']['name'] ?? '',
                    'civil_id'             => $data['father']['civil_id'] ?? null,
                    'nationality'          => $data['father']['nationality'] ?? null,
                    'occupation'           => $data['father']['occupation'] ?? null,
                    'workplace'            => $data['father']['workplace'] ?? null,
                    'company_branch'       => $data['father']['company_branch'] ?? null,
                    'marital_status'       => $data['father']['marital_status'] ?? null,
                    'phone'                => $data['father']['phone'] ?? null,
                    'email'                => $data['father']['email'] ?? null,
                ]
            );
        }

        // Mother
        if (! empty($data['mother']['name']) || ! empty($data['mother']['phone']) || ! empty($data['mother']['civil_id'])) {
            AcademicStudentParent::updateOrCreate(
                [
                    'academic_student_id' => $student->id,
                    'parent_type'          => AcademicStudentParent::TYPE_MOTHER,
                ],
                [
                    'academic_guardian_id' => $motherGuardianId,
                    'name'                 => $data['mother']['name'] ?? '',
                    'civil_id'             => $data['mother']['civil_id'] ?? null,
                    'nationality'          => $data['mother']['nationality'] ?? null,
                    'occupation'           => $data['mother']['occupation'] ?? null,
                    'workplace'            => $data['mother']['workplace'] ?? null,
                    'company_branch'       => $data['mother']['company_branch'] ?? null,
                    'marital_status'       => $data['mother']['marital_status'] ?? null,
                    'phone'                => $data['mother']['phone'] ?? null,
                    'email'                => $data['mother']['email'] ?? null,
                ]
            );
        }
    }

    //------------------------------------------
    /**
     * Save siblings in nursery.
     */
    protected function saveSiblings(AcademicStudent $student, array $data): void
    {
        if (isset($data['siblings']) && is_array($data['siblings'])) {
            $student->siblings()->delete();
            foreach ($data['siblings'] as $sibling) {
                if (! empty($sibling['sibling_name'])) {
                    $student->siblings()->create([
                        'sibling_name'       => $sibling['sibling_name'],
                        'academic_class_id'  => $sibling['academic_class_id'] ?? null,
                        'class_name'         => $sibling['class_name'] ?? null,
                        'sibling_student_id' => $sibling['sibling_student_id'] ?? null,
                    ]);
                }
            }
        }
    }

    //------------------------------------------
    /**
     * Save chronic diseases questionnaire (DiseaseType 1..8).
     */
    protected function saveDiseases(AcademicStudent $student, array $data): void
    {
        if (isset($data['diseases']) && is_array($data['diseases'])) {
            foreach ($data['diseases'] as $diseaseCode => $diseaseData) {
                $code = (int) $diseaseCode;
                if (DiseaseType::tryFrom($code)) {
                    AcademicStudentDisease::updateOrCreate(
                        [
                            'academic_student_id' => $student->id,
                            'disease_code'        => $code,
                        ],
                        [
                            'has_disease'     => ! empty($diseaseData['has_disease']),
                            'medication_name' => $diseaseData['medication_name'] ?? null,
                            'notes'           => $diseaseData['notes'] ?? null,
                        ]
                    );
                }
            }
        }
    }

    //------------------------------------------
    /**
     * Save medical declaration responses (MedicalQuestionnaire1 1..10).
     */
    protected function saveMedicalResponses(AcademicStudent $student, array $data): void
    {
        if (isset($data['medical_responses']) && is_array($data['medical_responses'])) {
            foreach ($data['medical_responses'] as $questionCode => $resp) {
                $code = (int) $questionCode;
                if (MedicalQuestionnaire1::tryFrom($code)) {
                    AcademicStudentMedicalResponse::updateOrCreate(
                        [
                            'academic_student_id' => $student->id,
                            'question_code'       => $code,
                        ],
                        [
                            'answer'  => ! empty($resp['answer']),
                            'details' => $resp['details'] ?? null,
                        ]
                    );
                }
            }
        }
    }

    //------------------------------------------
    /**
     * Save emergency contacts (up to 4).
     */
    protected function saveEmergencyContacts(AcademicStudent $student, array $data): void
    {
        if (isset($data['emergency_contacts']) && is_array($data['emergency_contacts'])) {
            $student->emergencyContacts()->delete();
            foreach ($data['emergency_contacts'] as $index => $contact) {
                if (! empty($contact['phone_1']) || ! empty($contact['relationship']) || ! empty($contact['contact_name'])) {
                    $student->emergencyContacts()->create([
                        'priority'     => (int) ($contact['priority'] ?? ($index + 1)),
                        'contact_name' => ! empty($contact['contact_name']) ? $contact['contact_name'] : ($contact['relationship'] ?? ('جهة طوارئ ' . ($index + 1))),
                        'relationship' => $contact['relationship'] ?? null,
                        'phone_1'      => $contact['phone_1'] ?? null,
                        'phone_2'      => $contact['phone_2'] ?? null,
                        'notes'        => $contact['notes'] ?? null,
                    ]);
                }
            }
        }
    }

    //------------------------------------------
    /**
     * Save authorized pickup persons.
     */
    protected function saveAuthorizedPickups(AcademicStudent $student, array $data): void
    {
        if (isset($data['authorized_pickups']) && is_array($data['authorized_pickups'])) {
            $student->authorizedPickups()->delete();
            foreach ($data['authorized_pickups'] as $index => $pickup) {
                if (! empty($pickup['phone']) || ! empty($pickup['relationship']) || ! empty($pickup['name'])) {
                    $photoPath = null;
                    if (isset($pickup['photo']) && $pickup['photo'] instanceof UploadedFile) {
                        $photoPath = upload_image($pickup['photo'], 'academic/pickups');
                    } elseif (! empty($pickup['existing_photo'])) {
                        $photoPath = $pickup['existing_photo'];
                    }

                    $student->authorizedPickups()->create([
                        'name'         => ! empty($pickup['name']) ? $pickup['name'] : (! empty($pickup['relationship']) ? $pickup['relationship'] : ('مصرح له ' . ($index + 1))),
                        'civil_id'     => $pickup['civil_id'] ?? null,
                        'relationship' => $pickup['relationship'] ?? null,
                        'phone'        => $pickup['phone'] ?? ($pickup['phone_1'] ?? ''),
                        'photo'        => $photoPath,
                        'notes'        => $pickup['notes'] ?? null,
                    ]);
                }
            }
        }
    }

    //------------------------------------------
    /**
     * Save behavioral / information form responses (InformationForm 1..22).
     */
    protected function saveInformationResponses(AcademicStudent $student, array $data): void
    {
        if (isset($data['information_responses']) && is_array($data['information_responses'])) {
            foreach ($data['information_responses'] as $qCode => $resp) {
                $code = (int) $qCode;
                if (InformationForm::tryFrom($code)) {
                    AcademicStudentInformationResponse::updateOrCreate(
                        [
                            'academic_student_id' => $student->id,
                            'question_code'       => $code,
                        ],
                        [
                            'answer'  => ! empty($resp['answer']),
                            'details' => $resp['details'] ?? null,
                        ]
                    );
                }
            }
        }
    }

    //------------------------------------------
    // Dropdown helpers for UI views
    //------------------------------------------

    public function getInstitutions(): Collection
    {
        return Nursery::with('translations')->where('status', 'active')->get();
    }

    public function getBranches(?int $institutionId = null): Collection
    {
        $query = NurseryBranch::with('translations')->where('status', 'active');
        if ($institutionId) {
            $query->where('institution_id', $institutionId);
        }

        return $query->get();
    }

    public function getAcademicYears(?int $institutionId = null): Collection
    {
        $query = AcademicYear::with('translations')->where('status', AcademicYear::STATUS_ACTIVE);
        if ($institutionId) {
            $query->where('institution_id', $institutionId);
        }

        return $query->get();
    }

    public function getStages(?int $institutionId = null): Collection
    {
        $query = AcademicStage::with('translations')->where('status', 'active');
        if ($institutionId) {
            $query->where('institution_id', $institutionId);
        }

        return $query->get();
    }

    public function getClasses(?int $stageId = null): Collection
    {
        $query = AcademicClass::with('translations')->where('status', 'active');
        if ($stageId) {
            $query->where('academic_stage_id', $stageId);
        }

        return $query->get();
    }

    public function getNationalities(): Collection
    {
        return GeneralSetting::ofType(GeneralSettingType::NATIONALITY)
            ->with('translations')
            ->get();
    }

    public function getGuardians(?int $institutionId = null): Collection
    {
        $query = AcademicGuardian::with('translations')->where('status', AcademicGuardian::STATUS_ACTIVE);
        if ($institutionId) {
            $query->where('institution_id', $institutionId);
        }

        return $query->get();
    }

    //------------------------------------------
    /**
     * Get guardian family data (Father, Mother, Siblings) for autofill.
     */
    public function getGuardianFamilyData(int $guardianId): array
    {
        $guardian = AcademicGuardian::with(['translations', 'students.parents', 'students.class.translations'])->find($guardianId);

        if (! $guardian) {
            return ['success' => false, 'message' => __('Guardian not found.')];
        }

        $latestStudent = $guardian->students()->latest('id')->first();
        $father = null;
        $mother = null;
        $siblings = [];

        if ($latestStudent) {
            $father = $latestStudent->father;
            $mother = $latestStudent->mother;

            foreach ($guardian->students as $st) {
                $siblings[] = [
                    'sibling_student_id' => $st->id,
                    'sibling_name'       => $st->name,
                    'academic_class_id'  => $st->academic_class_id,
                    'class_name'         => $st->class?->name ?? '',
                ];
            }
        }

        if (! $father) {
            $father = $guardian->parents()->where('parent_type', AcademicStudentParent::TYPE_FATHER)->latest('id')->first();
        }
        if (! $mother) {
            $mother = $guardian->parents()->where('parent_type', AcademicStudentParent::TYPE_MOTHER)->latest('id')->first();
        }

        return [
            'success'  => true,
            'guardian' => [
                'id'       => $guardian->id,
                'name'     => $guardian->name,
                'civil_id' => $guardian->civil_id,
                'phone'    => $guardian->phone,
                'email'    => $guardian->email,
            ],
            'father'   => $father ? [
                'name'           => $father->name,
                'civil_id'       => $father->civil_id,
                'nationality'    => $father->nationality,
                'occupation'     => $father->occupation,
                'workplace'      => $father->workplace,
                'company_branch' => $father->company_branch,
                'marital_status' => $father->marital_status,
                'phone'          => $father->phone,
                'email'          => $father->email,
            ] : [
                'name'     => $guardian->name,
                'civil_id' => $guardian->civil_id,
                'phone'    => $guardian->phone,
                'email'    => $guardian->email,
            ],
            'mother'   => $mother ? [
                'name'           => $mother->name,
                'civil_id'       => $mother->civil_id,
                'nationality'    => $mother->nationality,
                'occupation'     => $mother->occupation,
                'workplace'      => $mother->workplace,
                'company_branch' => $mother->company_branch,
                'marital_status' => $mother->marital_status,
                'phone'          => $mother->phone,
                'email'          => $mother->email,
            ] : null,
            'siblings' => $siblings,
        ];
    }
}
