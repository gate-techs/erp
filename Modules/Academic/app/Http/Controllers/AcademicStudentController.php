<?php

namespace Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Academic\Enums\DiseaseType;
use Modules\Academic\Enums\InformationForm;
use Modules\Academic\Enums\MedicalQuestionnaire1;
use Modules\Academic\Http\Requests\StoreAcademicStudentRequest;
use Modules\Academic\Http\Requests\UpdateAcademicStudentRequest;
use Modules\Academic\Models\AcademicGuardian;
use Modules\Academic\Models\AcademicStudent;
use Modules\Academic\Services\AcademicStudentService;
use Yajra\DataTables\Facades\DataTables;

class AcademicStudentController extends Controller
{
    public function __construct(
        protected AcademicStudentService $service
    ) {}

    //------------------------------------------
    public function index(Request $request)
    {
        $institutions = $this->service->getInstitutions();
        $branches     = $this->service->getBranches();
        $years        = $this->service->getAcademicYears();
        $stages       = $this->service->getStages();
        $classes      = $this->service->getClasses();
        $statuses     = AcademicStudent::statuses();
        $guardianId   = $request->integer('guardian_id') ?: null;
        $guardian     = $guardianId ? AcademicGuardian::find($guardianId) : null;

        return view('academic::students.index', compact(
            'institutions',
            'branches',
            'years',
            'stages',
            'classes',
            'statuses',
            'guardianId',
            'guardian'
        ));
    }

    //------------------------------------------
    public function data(Request $request): JsonResponse
    {
        $filters = $request->only([
            'institution_id',
            'branch_id',
            'academic_year_id',
            'academic_stage_id',
            'academic_class_id',
            'gender',
            'status',
            'guardian_id',
            'search',
        ]);

        $query = $this->service->getFilteredQuery($filters);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('student', function (AcademicStudent $student) {
                $locale = app()->getLocale();
                $name = $student->translate($locale)?->name
                    ?? $student->translate('ar')?->name
                    ?? $student->name
                    ?? '-';
                $photo = e($student->photo_url);

                return '
                    <div class="d-flex align-items-center gap-2">
                        <img src="' . $photo . '" alt="' . e($name) . '" class="rounded-circle shadow-sm" style="width: 36px; height: 36px; object-fit: cover;">
                        <a href="' . route('academic.students.show', $student->id) . '" class="fw-semibold text-heading student-name-text">' . e($name) . '</a>
                    </div>';
            })
            ->addColumn('civil_id', function (AcademicStudent $student) {
                return $student->civil_id
                    ? '<span class="font-monospace fw-medium text-body">' . e($student->civil_id) . '</span>'
                    : '<span class="text-muted">-</span>';
            })
            ->addColumn('class_name', function (AcademicStudent $student) {
                $locale = app()->getLocale();
                $class = $student->class?->translate($locale)?->name ?? $student->class?->name ?? '-';
                return e($class);
            })
            ->addColumn('academic_year_name', function (AcademicStudent $student) {
                $locale = app()->getLocale();
                return e($student->academicYear?->translate($locale)?->name ?? $student->academicYear?->name ?? '-');
            })
            ->addColumn('branch_name', function (AcademicStudent $student) {
                $locale = app()->getLocale();
                return e($student->branch?->translate($locale)?->name ?? $student->branch?->name ?? '-');
            })
            ->addColumn('institution_name', function (AcademicStudent $student) {
                $locale = app()->getLocale();
                return e($student->institution?->translate($locale)?->name ?? $student->institution?->name ?? '-');
            })
            ->addColumn('created_date', function (AcademicStudent $student) {
                return $student->created_at?->format('Y-m-d') ?? '-';
            })
            ->addColumn('created_by', function (AcademicStudent $student) {
                return e($student->creator?->name ?? '-');
            })
            ->addColumn('parents_info', function (AcademicStudent $student) {
                $father = $student->father;
                $mother = $student->mother;

                $html = '';
                if ($father) {
                    $html .= '<div class="font-12 mb-1"><strong class="text-heading">' . __('Father') . ':</strong> ' . e($father->name);
                    if ($father->phone) {
                        $html .= ' <a href="tel:' . e($father->phone) . '" class="text-primary font-monospace" dir="ltr"><i class="icon-base bx bx-phone font-12 ms-1"></i>' . e($father->phone) . '</a>';
                    }
                    $html .= '</div>';
                }
                if ($mother) {
                    $html .= '<div class="font-12"><strong class="text-heading">' . __('Mother') . ':</strong> ' . e($mother->name);
                    if ($mother->phone) {
                        $html .= ' <a href="tel:' . e($mother->phone) . '" class="text-primary font-monospace" dir="ltr"><i class="icon-base bx bx-phone font-12 ms-1"></i>' . e($mother->phone) . '</a>';
                    }
                    $html .= '</div>';
                }

                return $html ?: '<span class="text-muted">-</span>';
            })
            ->addColumn('status', function (AcademicStudent $student) {
                $isActive = $student->status === AcademicStudent::STATUS_ACTIVE;
                $label = $isActive ? __('Active') : ($student->status === AcademicStudent::STATUS_PENDING ? __('Pending') : __('Inactive'));
                $class = match ($student->status) {
                    AcademicStudent::STATUS_ACTIVE    => 'bg-label-success',
                    AcademicStudent::STATUS_PENDING   => 'bg-label-warning',
                    AcademicStudent::STATUS_GRADUATED => 'bg-label-primary',
                    AcademicStudent::STATUS_WITHDRAWN => 'bg-label-danger',
                    default                           => 'bg-label-secondary',
                };

                return '<span class="badge ' . $class . ' cursor-pointer toggle-status-btn" data-id="' . $student->id . '">' . e($label) . '</span>';
            })
            ->addColumn('actions', function (AcademicStudent $student) {
                $id = $student->id;
                $showUrl = route('academic.students.show', $id);
                $editUrl = route('academic.students.edit', $id);
                $name = e($student->translate(app()->getLocale())?->name ?? $student->translate('ar')?->name ?? $student->name ?? '');

                return '
                    <div class="d-inline-flex align-items-center gap-1">
                        <a href="' . $showUrl . '" class="btn btn-icon btn-sm btn-outline-info" title="' . e(__('View Profile')) . '">
                            <i class="icon-base bx bx-show"></i>
                        </a>
                        <a href="' . $editUrl . '" class="btn btn-icon btn-sm btn-outline-primary edit-record-btn" title="' . e(__('Edit')) . '">
                            <i class="icon-base bx bx-edit"></i>
                        </a>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-danger delete-record-btn btn-delete"
                            data-id="' . $id . '"
                            data-name="' . $name . '"
                            title="' . e(__('Delete')) . '">
                            <i class="icon-base bx bx-trash"></i>
                        </button>
                    </div>';
            })
            ->rawColumns(['student', 'civil_id', 'class_name', 'academic_year_name', 'branch_name', 'institution_name', 'created_date', 'created_by', 'parents_info', 'status', 'actions'])
            ->make(true);
    }

    //------------------------------------------
    public function create(Request $request)
    {
        $institutions  = $this->service->getInstitutions();
        $branches      = $this->service->getBranches();
        $years         = $this->service->getAcademicYears();
        $stages        = $this->service->getStages();
        $classes       = $this->service->getClasses();
        $nationalities = $this->service->getNationalities();
        $guardians     = $this->service->getGuardians();
        $guardianId    = $request->integer('guardian_id') ?: null;

        // Enums formatted for UI
        $diseaseOptions       = DiseaseType::options();
        $medicalQuestions     = MedicalQuestionnaire1::options();
        $form1Questions       = InformationForm::form1Options();
        $form2Questions       = InformationForm::form2Options();

        return view('academic::students.create', compact(
            'institutions',
            'branches',
            'years',
            'stages',
            'classes',
            'nationalities',
            'guardians',
            'guardianId',
            'diseaseOptions',
            'medicalQuestions',
            'form1Questions',
            'form2Questions'
        ));
    }

    //------------------------------------------
    public function store(StoreAcademicStudentRequest $request)
    {
        try {
            $data = $request->validated();
            $photo = $request->file('photo');

            // When adding from a guardian's filtered students page, keep the
            // guardian context authoritative instead of relying on the select value.
            $contextGuardianId = $request->integer('guardian_context_id') ?: null;
            if ($contextGuardianId) {
                $data['academic_guardian_id'] = $contextGuardianId;
                $data['primary_guardian_type'] = 'existing';
            }

            $student = $this->service->create($data, $photo);

            if ($request->ajax()) {
                return response()->json([
                    'success'  => true,
                    'message'  => __('Student registered successfully.'),
                    'redirect' => route('academic.students.show', $student->id),
                    'student'  => $student,
                ]);
            }

            return redirect()->route('academic.students.show', $student->id)
                ->with('success', __('Student registered successfully.'));
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error saving student: ') . $e->getMessage(),
                ], 500);
            }

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    //------------------------------------------
    public function show(int $id)
    {
        $student = $this->service->findWithAllRelations($id);
        if (! $student) {
            abort(404, __('Student not found.'));
        }

        return view('academic::students.show', compact('student'));
    }

    //------------------------------------------
    public function edit(int $id)
    {
        $student = $this->service->findWithAllRelations($id);
        if (! $student) {
            abort(404, __('Student not found.'));
        }

        $institutions  = $this->service->getInstitutions();
        $branches      = $this->service->getBranches($student->institution_id);
        $years         = $this->service->getAcademicYears($student->institution_id);
        $stages        = $this->service->getStages($student->institution_id);
        $classes       = $this->service->getClasses($student->academic_stage_id);
        $nationalities = $this->service->getNationalities();
        $guardians     = $this->service->getGuardians($student->institution_id);

        $diseaseOptions   = DiseaseType::options();
        $medicalQuestions = MedicalQuestionnaire1::options();
        $form1Questions   = InformationForm::form1Options();
        $form2Questions   = InformationForm::form2Options();

        // Index existing relation items for easy blade mapping
        $savedDiseases  = $student->diseases->keyBy(fn ($d) => $d->disease_code instanceof DiseaseType ? $d->disease_code->value : (int) $d->disease_code);
        $savedMedical   = $student->medicalResponses->keyBy(fn ($m) => $m->question_code instanceof MedicalQuestionnaire1 ? $m->question_code->value : (int) $m->question_code);
        $savedInfo      = $student->informationResponses->keyBy(fn ($i) => $i->question_code instanceof InformationForm ? $i->question_code->value : (int) $i->question_code);

        return view('academic::students.edit', compact(
            'student',
            'institutions',
            'branches',
            'years',
            'stages',
            'classes',
            'nationalities',
            'guardians',
            'diseaseOptions',
            'medicalQuestions',
            'form1Questions',
            'form2Questions',
            'savedDiseases',
            'savedMedical',
            'savedInfo'
        ));
    }

    //------------------------------------------
    public function update(UpdateAcademicStudentRequest $request, int $id)
    {
        try {
            $data = $request->validated();
            $photo = $request->file('photo');

            $student = $this->service->update($id, $data, $photo);

            if ($request->ajax()) {
                return response()->json([
                    'success'  => true,
                    'message'  => __('Student updated successfully.'),
                    'redirect' => route('academic.students.show', $student->id),
                    'student'  => $student,
                ]);
            }

            return redirect()->route('academic.students.show', $student->id)
                ->with('success', __('Student updated successfully.'));
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error updating student: ') . $e->getMessage(),
                ], 500);
            }

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    //------------------------------------------
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->service->delete($id);

            return response()->json([
                'success' => $deleted,
                'message' => $deleted ? __('Student deleted successfully.') : __('Student not found.'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error deleting student: ') . $e->getMessage(),
            ], 500);
        }
    }

    //------------------------------------------
    public function toggleStatus(int $id): JsonResponse
    {
        try {
            $student = $this->service->toggleStatus($id);

            return response()->json([
                'success' => (bool) $student,
                'status'  => $student?->status,
                'message' => __('Status updated successfully.'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error toggling status: ') . $e->getMessage(),
            ], 500);
        }
    }

    //------------------------------------------
    // AJAX Dynamic Dependents
    //------------------------------------------

    public function getBranches(int $institutionId): JsonResponse
    {
        $branches = $this->service->getBranches($institutionId);

        return response()->json($branches);
    }

    public function getYears(int $institutionId): JsonResponse
    {
        $years = $this->service->getAcademicYears($institutionId);

        return response()->json($years);
    }

    public function getStages(int $institutionId): JsonResponse
    {
        $stages = $this->service->getStages($institutionId);

        return response()->json($stages);
    }

    public function getClasses(int $stageId): JsonResponse
    {
        $classes = $this->service->getClasses($stageId);

        return response()->json($classes);
    }

    public function getGuardians(int $institutionId): JsonResponse
    {
        $guardians = $this->service->getGuardians($institutionId);

        return response()->json($guardians);
    }

    public function getGuardianFamily(int $guardianId): JsonResponse
    {
        $data = $this->service->getGuardianFamilyData($guardianId);

        return response()->json($data);
    }
}
