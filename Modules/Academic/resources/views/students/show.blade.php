@extends('layouts/contentNavbarLayout')

@section('title', ($student->name ?? __('Student Profile')) . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div class="d-flex flex-column justify-content-center">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('academic.students.index') }}" class="text-muted fw-medium small">{{ __('Students') }}</a></li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ $student->name }}</li>
            </ol>
        </nav>
        <!-- Title -->
        <div class="d-flex align-items-center gap-2 mt-1">
            <i class="icon-base bx bx-user text-primary font-24"></i>
            <h4 class="mb-0 fw-bold text-heading">{{ __('Student Profile') }}: {{ $student->name }}</h4>
        </div>
    </div>

    <div class="mt-3 mt-md-0 d-flex align-items-center gap-2">
        <a href="{{ route('academic.students.edit', $student->id) }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1 shadow-sm">
            <i class="icon-base bx bx-edit font-18"></i>
            <span>{{ __('Edit Data') }}</span>
        </a>
        <a href="{{ route('academic.students.index') }}" class="btn btn-label-secondary btn-sm d-flex align-items-center gap-1">
            <i class="icon-base bx bx-arrow-back font-18"></i>
            <span>{{ __('Back to List') }}</span>
        </a>
    </div>
</div>

<!-- Student Header Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">
        <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start gap-4">
            <img src="{{ $student->photo_url }}" alt="{{ $student->name }}"
                class="rounded-circle shadow-sm border" style="width: 100px; height: 100px; object-fit: cover;">
            <div class="flex-grow-1 text-center text-md-start">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center align-items-md-start gap-2 mb-2">
                    <div>
                        <h4 class="fw-bold mb-1 text-heading">{{ $student->name }}</h4>
                        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-2">
                            <span class="badge bg-label-secondary font-monospace">{{ $student->student_code ?? '-' }}</span>
                            @if ($student->gender === \Modules\Academic\Models\AcademicStudent::GENDER_FEMALE)
                                <span class="badge bg-label-danger"><i class="icon-base bx bx-female me-1"></i>{{ __('Female') }}</span>
                            @else
                                <span class="badge bg-label-info"><i class="icon-base bx bx-male me-1"></i>{{ __('Male') }}</span>
                            @endif
                            <span class="badge bg-label-success">{{ __(ucfirst($student->status)) }}</span>
                            @if ($student->age)
                                <span class="badge bg-label-primary"><i class="icon-base bx bx-calendar me-1"></i>{{ $student->age }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row g-2 mt-2 pt-2 border-top text-muted small">
                    <div class="col-12 col-sm-6 col-md-3">
                        <strong class="text-heading">{{ __('Nursery / Institution') }}:</strong> {{ $student->institution?->name ?? '-' }}
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <strong class="text-heading">{{ __('Branch') }}:</strong> {{ $student->branch?->name ?? '-' }}
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <strong class="text-heading">{{ __('Academic Stage') }}:</strong> {{ $student->stage?->name ?? '-' }}
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <strong class="text-heading">{{ __('Class') }}:</strong> {{ $student->class?->name ?? '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs Profile Dossier -->
<div class="nav-align-top mb-4">
    <ul class="nav nav-pills mb-3" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab_view_family" type="button" role="tab">
                <i class="icon-base bx bx-group me-1"></i> {{ __('Family & Siblings Information') }}
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab_view_health" type="button" role="tab">
                <i class="icon-base bx bx-plus-medical me-1"></i> {{ __('Health Record & Medical Declaration') }}
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab_view_contacts" type="button" role="tab">
                <i class="icon-base bx bx-phone-call me-1"></i> {{ __('Emergency & Pick-up') }}
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab_view_forms" type="button" role="tab">
                <i class="icon-base bx bx-list-check me-1"></i> {{ __('Information Forms 1 & 2') }}
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab_view_notes" type="button" role="tab">
                <i class="icon-base bx bx-notepad me-1"></i> {{ __('Notes & Declarations') }}
            </button>
        </li>
    </ul>

    <div class="tab-content bg-transparent p-0 shadow-none">
        <!-- 1. Family & Siblings -->
        <div class="tab-pane fade show active" id="tab_view_family" role="tabpanel">
            <div class="row g-4">
                <!-- Primary Guardian Card -->
                <div class="col-12">
                    <div class="card shadow-sm border-0 border-start border-4 border-primary">
                        <div class="card-body p-4">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-label-primary px-3 py-2 fs-6">
                                            <i class="icon-base bx bx-shield-quarter me-1"></i> {{ __('Primary Guardian (Linked Account)') }}
                                        </span>
                                        @if ($student->guardian)
                                            <span class="badge bg-label-success">{{ __(ucfirst($student->guardian->status ?? 'active')) }}</span>
                                        @endif
                                    </div>
                                    @if ($student->guardian)
                                        <h4 class="fw-bold mb-1 mt-2 text-heading">{{ $student->guardian->name }}</h4>
                                        <div class="text-muted small d-flex flex-wrap align-items-center gap-3">
                                            <span><strong>{{ __('Guardian Code') }}:</strong> <code class="text-primary">{{ $student->guardian->guardian_code }}</code></span>
                                            <span><strong>{{ __('Civil ID') }}:</strong> <span class="font-monospace">{{ $student->guardian->civil_id ?? '-' }}</span></span>
                                            <span><strong>{{ __('Phone') }}:</strong> <a href="tel:{{ $student->guardian->phone }}" class="font-monospace text-primary" dir="ltr">{{ $student->guardian->phone ?? '-' }}</a></span>
                                            @if ($student->guardian->email)
                                                <span><strong>{{ __('Email') }}:</strong> {{ $student->guardian->email }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-muted mb-0 mt-2">{{ __('No registered guardian account linked yet.') }}</p>
                                    @endif
                                </div>
                                @if ($student->guardian)
                                    <div>
                                        <a href="{{ route('academic.guardians.show', $student->guardian->id) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="icon-base bx bx-user me-1"></i> {{ __('Guardian Details') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Father Card -->
                <div class="col-12 col-md-6">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header border-bottom py-3 d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0 fw-bold text-primary">
                                <i class="icon-base bx bx-male font-20 me-1"></i> {{ __('Father Information') }}
                            </h5>
                            @if ($student->guardian && $student->father?->academic_guardian_id === $student->guardian->id)
                                <span class="badge bg-label-primary"><i class="icon-base bx bx-check-circle me-1"></i>{{ __('Primary Guardian') }}</span>
                            @endif
                        </div>
                        <div class="card-body p-4">
                            @if ($student->father)
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <th class="text-muted w-40">{{ __('Name') }}:</th>
                                        <td class="fw-semibold text-heading">{{ $student->father->name ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">{{ __('Civil ID') }}:</th>
                                        <td class="font-monospace">{{ $student->father->civil_id ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">{{ __('Nationality') }}:</th>
                                        <td>{{ $student->father->nationality ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">{{ __('Profession') }}:</th>
                                        <td>{{ $student->father->occupation ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">{{ __('Workplace') }}:</th>
                                        <td>{{ $student->father->workplace ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">{{ __('Company / Branch') }}:</th>
                                        <td>{{ $student->father->company_branch ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">{{ __('Phone') }}:</th>
                                        <td><a href="tel:{{ $student->father->phone }}" class="text-primary font-monospace" dir="ltr">{{ $student->father->phone ?: '-' }}</a></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">{{ __('Email') }}:</th>
                                        <td>{{ $student->father->email ?: '-' }}</td>
                                    </tr>
                                </table>
                            @else
                                <p class="text-muted mb-0">{{ __('Father information has not been recorded.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Mother Card -->
                <div class="col-12 col-md-6">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header border-bottom py-3 d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0 fw-bold text-danger">
                                <i class="icon-base bx bx-female font-20 me-1"></i> {{ __('Mother Information') }}
                            </h5>
                            @if ($student->guardian && $student->mother?->academic_guardian_id === $student->guardian->id)
                                <span class="badge bg-label-danger"><i class="icon-base bx bx-check-circle me-1"></i>{{ __('Primary Guardian') }}</span>
                            @endif
                        </div>
                        <div class="card-body p-4">
                            @if ($student->mother)
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <th class="text-muted w-40">{{ __('Name') }}:</th>
                                        <td class="fw-semibold text-heading">{{ $student->mother->name ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">{{ __('Civil ID') }}:</th>
                                        <td class="font-monospace">{{ $student->mother->civil_id ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">{{ __('Nationality') }}:</th>
                                        <td>{{ $student->mother->nationality ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">{{ __('Profession') }}:</th>
                                        <td>{{ $student->mother->occupation ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">{{ __('Workplace') }}:</th>
                                        <td>{{ $student->mother->workplace ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">{{ __('Company / Branch') }}:</th>
                                        <td>{{ $student->mother->company_branch ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">{{ __('Phone') }}:</th>
                                        <td><a href="tel:{{ $student->mother->phone }}" class="text-primary font-monospace" dir="ltr">{{ $student->mother->phone ?: '-' }}</a></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">{{ __('Email') }}:</th>
                                        <td>{{ $student->mother->email ?: '-' }}</td>
                                    </tr>
                                </table>
                            @else
                                <p class="text-muted mb-0">{{ __('Mother information has not been recorded.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Siblings Card -->
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header border-bottom py-3">
                            <h5 class="card-title mb-0 fw-bold text-heading">
                                <i class="icon-base bx bx-group font-20 me-1"></i> {{ __('Siblings in Nursery') }}
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            @if ($student->siblings->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('Sibling Name') }}</th>
                                                <th>{{ __('Classroom') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($student->siblings as $sib)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td class="fw-semibold text-heading">{{ $sib->sibling_name }}</td>
                                                    <td>{{ $sib->academicClass?->name ?? $sib->class_name ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted mb-0">{{ __('No siblings registered in nursery.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Health & Medical -->
        <div class="tab-pane fade" id="tab_view_health" role="tabpanel">
            <div class="row g-4">
                <!-- Chronic Diseases -->
                <div class="col-12 col-md-6">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header border-bottom py-3">
                            <h5 class="card-title mb-0 fw-bold text-primary">
                                <i class="icon-base bx bx-heart font-20 me-1"></i> {{ __('Chronic Diseases') }}
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            @php $diseasesMap = $student->diseases->keyBy(fn ($d) => $d->disease_code->value); @endphp
                            @foreach (\Modules\Academic\Enums\DiseaseType::cases() as $case)
                                @php $rec = $diseasesMap->get($case->value); @endphp
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <div>
                                        <span class="fw-semibold text-heading">{{ $loop->iteration }}. {{ $case->label() }}</span>
                                        @if ($rec && $rec->medication_name)
                                            <div class="small text-muted mt-1"><i class="icon-base bx bx-capsule me-1 text-warning"></i>{{ $rec->medication_name }}</div>
                                        @endif
                                    </div>
                                    <div>
                                        @if ($rec && $rec->has_disease)
                                            <span class="badge bg-label-danger">{{ __('Suffers') }}</span>
                                        @else
                                            <span class="badge bg-label-success">{{ __('Healthy') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Medical Declaration -->
                <div class="col-12 col-md-6">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header border-bottom py-3">
                            <h5 class="card-title mb-0 fw-bold text-primary">
                                <i class="icon-base bx bx-shield-plus font-20 me-1"></i> {{ __('Medical Questionnaire') }}
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            @php $medMap = $student->medicalResponses->keyBy(fn ($m) => $m->question_code->value); @endphp
                            @foreach (\Modules\Academic\Enums\MedicalQuestionnaire1::cases() as $case)
                                @php $resp = $medMap->get($case->value); @endphp
                                <div class="py-2 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <span class="small fw-semibold text-heading">{{ $loop->iteration }}. {{ $case->question() }}</span>
                                        <div>
                                            @if ($resp && $resp->answer)
                                                <span class="badge bg-label-warning">{{ __('Yes') }}</span>
                                            @else
                                                <span class="badge bg-label-secondary">{{ __('No') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($resp && $resp->details)
                                        <div class="small text-muted bg-light p-2 rounded mt-1">{{ $resp->details }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical Declaration Status -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h5 class="card-title mb-0 fw-bold text-primary">
                                <i class="icon-base bx bx-file font-20 me-1"></i> {{ __('Medical Declaration') }}
                            </h5>
                            @if ($student->medical_declaration_accepted)
                                <span class="badge bg-label-success">
                                    <i class="icon-base bx bx-check me-1"></i> {{ __('Medical Declaration Accepted') }}
                                    @if ($student->medical_declaration_accepted_at)
                                        ({{ $student->medical_declaration_accepted_at->format('Y-m-d H:i') }})
                                    @endif
                                </span>
                            @else
                                <span class="badge bg-label-secondary">{{ __('Not Confirmed') }}</span>
                            @endif
                        </div>
                        <div class="card-body p-4">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2 text-muted"><i class="icon-base bx bx-check-circle text-primary me-2"></i>{{ __('I permit the school nurse to administer medicine and treatment to my child if needed, perform first aid, and transport them to hospital in emergencies.') }}</li>
                                <li class="mb-2 text-muted"><i class="icon-base bx bx-check-circle text-primary me-2"></i>{{ __('I declare that the health information provided is accurate and that I have not withheld any medical conditions concerning my child from the school.') }}</li>
                                <li class="text-muted"><i class="icon-base bx bx-check-circle text-primary me-2"></i>{{ __('I absolve the school of all responsibility regarding any health condition of my child related to a chronic illness or condition not declared by me.') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Contacts & Pickups -->
        <div class="tab-pane fade" id="tab_view_contacts" role="tabpanel">
            <div class="row g-4">
                <!-- Emergency Contacts -->
                <div class="col-12 col-md-6">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header border-bottom py-3">
                            <h5 class="card-title mb-0 fw-bold text-primary">
                                <i class="icon-base bx bx-phone-call font-20 me-1"></i> {{ __('Emergency Contacts') }}
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            @if ($student->emergencyContacts->count() > 0)
                                @foreach ($student->emergencyContacts as $contact)
                                    <div class="p-3 border rounded mb-2 bg-light">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-heading">#{{ $contact->priority }} {{ $contact->contact_name }}</span>
                                            <span class="badge bg-label-info">{{ $contact->relationship ?: '-' }}</span>
                                        </div>
                                        <div class="small text-muted">
                                            <div><strong>{{ __('Phone 1') }}:</strong> <a href="tel:{{ $contact->phone_1 }}" dir="ltr">{{ $contact->phone_1 }}</a></div>
                                            @if ($contact->phone_2)
                                                <div><strong>{{ __('Phone 2') }}:</strong> <a href="tel:{{ $contact->phone_2 }}" dir="ltr">{{ $contact->phone_2 }}</a></div>
                                            @endif
                                            @if ($contact->notes)
                                                <div class="mt-1"><strong>{{ __('Notes') }}:</strong> {{ $contact->notes }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted mb-0">{{ __('No emergency contacts recorded.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Authorized Pickups -->
                <div class="col-12 col-md-6">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header border-bottom py-3">
                            <h5 class="card-title mb-0 fw-bold text-primary">
                                <i class="icon-base bx bx-car font-20 me-1"></i> {{ __('Authorized Pick-up Persons') }}
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            @if ($student->authorizedPickups->count() > 0)
                                @foreach ($student->authorizedPickups as $p)
                                    <div class="d-flex align-items-center gap-3 p-3 border rounded mb-2 bg-light">
                                        @if ($p->photo_url)
                                            <img src="{{ $p->photo_url }}" class="rounded shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <div class="avatar bg-label-secondary rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="icon-base bx bx-user font-24"></i>
                                            </div>
                                        @endif
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold text-heading mb-1">{{ $p->name }}</h6>
                                            <div class="small text-muted">
                                                <span class="badge bg-label-primary font-11 me-1">{{ $p->relationship }}</span>
                                                <a href="tel:{{ $p->phone }}" dir="ltr" class="text-body font-monospace">{{ $p->phone }}</a>
                                                @if ($p->civil_id)
                                                    <span class="ms-2 font-monospace">({{ $p->civil_id }})</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted mb-0">{{ __('No authorized pick-up persons added.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Information Forms 1 & 2 -->
        <div class="tab-pane fade" id="tab_view_forms" role="tabpanel">
            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold text-primary">
                        <i class="icon-base bx bx-list-check font-20 me-1"></i> {{ __('Behavioral & Developmental Forms Responses (1 & 2)') }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    @php $infoMap = $student->informationResponses->keyBy(fn ($i) => $i->question_code->value); @endphp
                    @foreach (\Modules\Academic\Enums\InformationForm::cases() as $case)
                        @php $resp = $infoMap->get($case->value); @endphp
                        <div class="py-2 border-bottom">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div>
                                    <span class="badge bg-label-primary font-11 me-2">{{ $case->value <= 12 ? __('Form 1') : __('Form 2') }}</span>
                                    <span class="fw-semibold text-heading">{{ $case->value }}. {{ $case->question() }}</span>
                                </div>
                                <div>
                                    @if ($resp && $resp->answer)
                                        <span class="badge bg-label-danger">{{ __('Yes') }}</span>
                                    @else
                                        <span class="badge bg-label-secondary">{{ __('No') }}</span>
                                    @endif
                                </div>
                            </div>
                            @if ($resp && $resp->details)
                                <div class="small text-muted bg-light p-2 rounded mt-1">{{ $resp->details }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- 5. Notes & Consents -->
        <div class="tab-pane fade" id="tab_view_notes" role="tabpanel">
            <div class="row g-4">
                <div class="col-12 col-md-6">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header border-bottom py-3">
                            <h5 class="card-title mb-0 fw-bold text-primary">
                                <i class="icon-base bx bx-camera font-20 me-1"></i> {{ __('Child Photo Usage Consent') }}
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <strong class="text-heading">{{ __('Consent Status') }}:</strong>
                                @if ($student->photo_consent)
                                    <span class="badge bg-label-success ms-2">{{ __('Consented to Photography & Publishing') }}</span>
                                @else
                                    <span class="badge bg-label-danger ms-2">{{ __('Did Not Consent to Publishing Photos') }}</span>
                                @endif
                            </div>
                            @if ($student->photo_consent_notes)
                                <div class="bg-light p-3 rounded text-muted small">
                                    <strong>{{ __('Photo Consent Notes') }}:</strong>
                                    <p class="mb-0 mt-1">{{ $student->photo_consent_notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header border-bottom py-3">
                            <h5 class="card-title mb-0 fw-bold text-primary">
                                <i class="icon-base bx bx-notepad font-20 me-1"></i> {{ __('General Notes') }}
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <h6 class="fw-bold text-heading mb-1">{{ __('Guardian Notes:') }}</h6>
                                <p class="text-muted small bg-light p-3 rounded mb-0">{{ $student->guardian_notes ?: __('No notes available.') }}</p>
                            </div>
                            <div>
                                <h6 class="fw-bold text-heading mb-1">{{ __('Administration Notes:') }}</h6>
                                <p class="text-muted small bg-light p-3 rounded mb-0">{{ $student->admin_notes ?: __('No notes available.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
