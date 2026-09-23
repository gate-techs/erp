@extends('layouts/contentNavbarLayout')

@php
    $displayName = $employee->translate('ar')?->name ?? $employee->translate('en')?->name ?? '';
@endphp

@section('title', __('Edit Employee') . ' - ' . $displayName . ' - ' . config('variables.templateName'))

    @section('vendor-style')
        @vite([
            'resources/assets/vendor/libs/select2/select2.scss'
        ])
    @endsection

    @section('vendor-script')
        @vite([
            'resources/assets/vendor/libs/select2/select2.js'
        ])
    @endsection

    @section('content')
        <!-- Page Header with Breadcrumbs & Back Action -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 employee-page-header">
            <div class="d-flex flex-column justify-content-center">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item">
                            <a href="{{ route('hr.employees.index') }}" class="text-muted fw-medium small">{{ __('Employees') }}</a>
                        </li>
                        <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ $displayName }}</li>
                    </ol>
                </nav>

                <!-- Title & Back -->
                <div class="d-flex align-items-center gap-2 mt-1">
                    <a href="{{ route('hr.employees.index') }}" class="btn btn-icon btn-sm btn-outline-secondary rounded-circle employee-back-btn" title="{{ __('Back to Employees') }}">
                        <i class="bx bx-arrow-back font-20"></i>
                    </a>
                    <h4 class="mb-0 fw-bold text-heading">{{ __('Edit Employee') }}: <span class="text-primary">{{ $displayName }}</span></h4>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex flex-wrap align-items-center gap-2 mt-3 mt-md-0">
                <a href="{{ route('hr.employees.index') }}" class="btn btn-outline-secondary employee-page-btn px-4 rounded-3">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" form="employeeEditForm" class="btn btn-primary employee-page-btn d-flex align-items-center gap-2 px-4 shadow-none rounded-3">
                    <i class="bx bx-save font-18"></i>
                    <span>{{ __('Save Changes') }}</span>
                </button>
            </div>
        </div>

     

        <!-- Form Body -->
        <form action="{{ route('hr.employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data"
            id="employeeEditForm">
            @csrf
            @method('PUT')

            @include('hr::employees.partials.form', ['employee' => $employee])

            <!-- Sticky / Clean Bottom Action Bar -->
            <div class="card shadow-sm border-0 mt-4 mb-5">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <span class="text-muted small">
                        <i class="bx bx-info-circle me-1 text-primary"></i>
                        {{ __('Ensure all required fields marked with (*) are accurately filled.') }}
                    </span>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('hr.employees.index') }}" class="btn btn-outline-secondary px-3">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="icon-base bx bx-check me-1"></i>
                            {{ __('Save Changes') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @endsection

    @section('page-script')
        <script>
            window.addEventListener('load', function () {
                const $ = window.jQuery || window.$;
                if (!$) return;

                // 1. Initialize Select2 with universal Sneat Bootstrap-5 theme matching Settings exactly
                function setupSelect2(container = document) {
                    const isRtl = $('html').attr('dir') === 'rtl' || $('html').attr('lang') === 'ar';
                    const noResultsText = isRtl ? 'لا توجد نتائج مطابقة' : 'No results found';
                    const searchingText = isRtl ? 'جاري البحث...' : 'Searching...';

                    if (typeof window.initSelect2 === 'function') {
                        window.initSelect2(container);
                    } else if ($.fn.select2) {
                        $(container).find('.select2, [data-control="select2"]').each(function () {
                            if (!$(this).hasClass('select2-hidden-accessible')) {
                                $(this).select2({
                                    theme: 'bootstrap-5',
                                    placeholder: $(this).data('placeholder') || '',
                                    allowClear: $(this).data('allow-clear') !== false,
                                    dir: isRtl ? 'rtl' : 'ltr',
                                    width: '100%',
                                    dropdownParent: $(this).closest('.modal, .offcanvas').length ? $(this).closest('.modal, .offcanvas') : $(document.body),
                                    language: {
                                        noResults: () => noResultsText,
                                        searching: () => searchingText
                                    }
                                });
                            }
                        });
                    }
                }

                setupSelect2();

                // 2. Chained Dropdown: Administration -> Department
                const allDeptOptions = $('#department_select option').clone();
                $('#administration_select').on('change', function () {
                    const adminId = $(this).val();
                    const currentVal = $('#department_select').val();
                    $('#department_select').empty().append('<option value=""></option>');
                    allDeptOptions.each(function () {
                        const parentId = $(this).data('parent-id');
                        if (!adminId || parentId == adminId || !$(this).val()) {
                            $('#department_select').append($(this).clone());
                        }
                    });
                    if (currentVal && $('#department_select').find(`option[value="${currentVal}"]`).length) {
                        $('#department_select').val(currentVal);
                    }
                    $('#department_select').trigger('change');
                });

                // 3. Chained Dropdown: Governorate -> City
                const allCityOptions = $('#city_select option').clone();
                $('#governorate_select').on('change', function () {
                    const govId = $(this).val();
                    const currentCity = $('#city_select').val();
                    $('#city_select').empty().append('<option value=""></option>');
                    allCityOptions.each(function () {
                        const parentId = $(this).data('parent-id');
                        if (!govId || parentId == govId || !$(this).val()) {
                            $('#city_select').append($(this).clone());
                        }
                    });
                    if (currentCity && $('#city_select').find(`option[value="${currentCity}"]`).length) {
                        $('#city_select').val(currentCity);
                    }
                    $('#city_select').trigger('change');
                });

                // 4. Chained Dropdown: Bank -> Bank Branch
                const allBankBranchOptions = $('#bank_branch_select option').clone();
                $('#bank_select').on('change', function () {
                    const bankId = $(this).val();
                    const currentBranch = $('#bank_branch_select').val();
                    $('#bank_branch_select').empty().append('<option value=""></option>');
                    allBankBranchOptions.each(function () {
                        const parentId = $(this).data('parent-id');
                        if (!bankId || parentId == bankId || !$(this).val()) {
                            $('#bank_branch_select').append($(this).clone());
                        }
                    });
                    if (currentBranch && $('#bank_branch_select').find(`option[value="${currentBranch}"]`).length) {
                        $('#bank_branch_select').val(currentBranch);
                    }
                    $('#bank_branch_select').trigger('change');
                });

                // 5. Avatar Preview & Gender-based SVG Avatar Swap Handler
                const avatarInput = document.getElementById('avatarInput');
                const avatarPreview = document.getElementById('avatarPreview');
                const genderSelect = $('#genderSelect');
                let userHasUploadedAvatar = false;
                const hasCustomAvatar = avatarPreview ? avatarPreview.getAttribute('data-has-custom') === '1' : false;

                function updateSvgAvatarByGender() {
                    if (!avatarPreview || userHasUploadedAvatar || hasCustomAvatar) return;
                    const femaleSvg = avatarPreview.getAttribute('data-female-avatar');
                    const maleSvg = avatarPreview.getAttribute('data-male-avatar');
                    const selectedGender = String(genderSelect.val() || '').toLowerCase();

                    if (selectedGender === 'male' || selectedGender === 'ذكر') {
                        avatarPreview.src = maleSvg;
                    } else {
                        avatarPreview.src = femaleSvg;
                    }
                }

                if (avatarInput && avatarPreview) {
                    avatarInput.addEventListener('change', function (e) {
                        const file = e.target.files[0];
                        if (file) {
                            userHasUploadedAvatar = true;
                            const reader = new FileReader();
                            reader.onload = function (event) {
                                avatarPreview.src = event.target.result;
                            };
                            reader.readAsDataURL(file);
                            recalcLiveProfileCompletion();
                        }
                    });
                }

                if (genderSelect.length && avatarPreview) {
                    genderSelect.on('change', function () {
                        updateSvgAvatarByGender();
                        recalcLiveProfileCompletion();
                    });
                }

                // Dynamic Live Profile Completion Recalculator
                function recalcLiveProfileCompletion() {
                    const circle = document.getElementById('profileCompletionCircle');
                    const badge = document.getElementById('profileCompletionBadge');
                    if (!circle || !badge) return;

                    let points = 0;
                    // 1. Basic Info (25)
                    if ($('input[name="name_ar"]').val()?.trim() || $('input[name="name_en"]').val()?.trim()) points += 5;
                    if ($('input[name="civil_id"]').val()?.trim() || $('input[name="passport_number"]').val()?.trim()) points += 5;
                    if ($('input[name="date_of_birth"]').val()?.trim()) points += 5;
                    if ($('#genderSelect').val()) points += 5;
                    if ($('select[name="nationality"]').val()) points += 5;

                    // 2. Contact & Address (20)
                    if ($('input[name="phone"]').val()?.trim()) points += 10;
                    if ($('input[name="email"]').val()?.trim()) points += 5;
                    if ($('input[name="emergency_contact_phone"]').val()?.trim() || $('textarea[name="address_ar"]').val()?.trim() || $('#governorate_select').val()) points += 5;

                    // 3. Organization & Job (25)
                    if ($('#primary_branch_id').val()) points += 10;
                    if ($('#department_select').val()) points += 5;
                    if ($('select[name="job_title_id"]').val()) points += 5;
                    if ($('input[name="hire_date"]').val()?.trim()) points += 5;

                    // 4. Working & Payroll (20)
                    if (parseFloat($('input[name="basic_salary"]').val() || 0) > 0) points += 10;
                    if ($('select[name="work_schedule_id"]').val() || $('select[name="shift_id"]').val()) points += 5;
                    if ($('#bank_select').val() || $('input[name="iban"]').val()?.trim() || $('input[name="account_number"]').val()?.trim()) points += 5;

                    // 5. Documents & Avatar (10)
                    if (userHasUploadedAvatar || hasCustomAvatar) points += 5;
                    if ($('.document-item').length > 0) points += 5;

                    const percent = Math.min(100, Math.max(0, points));
                    const circumference = 389.56;
                    const offset = Math.round(circumference - (circumference * (percent / 100)));

                    circle.style.strokeDashoffset = offset;

                    let color = '#ff3e1d';
                    let bgClass = 'bg-danger';
                    if (percent >= 80) {
                        color = '#71dd37';
                        bgClass = 'bg-success';
                    } else if (percent >= 50) {
                        color = '#ffab00';
                        bgClass = 'bg-warning';
                    }

                    circle.setAttribute('stroke', color);
                    badge.className = `position-absolute badge ${bgClass} rounded-pill text-white fw-bold shadow-sm`;
                    badge.textContent = `${percent}%`;
                }

                $(document).on('input change', 'input, select, textarea', function () {
                    recalcLiveProfileCompletion();
                });

                // 6. Dynamic Name Preview on Avatar Card
                const nameInput = document.querySelector('input[name="name_ar"]');
                const cardEmployeeName = document.getElementById('cardEmployeeName');

                if (nameInput && cardEmployeeName) {
                    nameInput.addEventListener('input', function () {
                        cardEmployeeName.textContent = this.value.trim() || "{{ $displayName }}";
                    });
                }

                // 7. User Account Toggle
                const accountSwitch = document.getElementById('createUserAccountSwitch');
                const accountFields = document.getElementById('userAccountFields');

                if (accountSwitch && accountFields) {
                    accountSwitch.addEventListener('change', function () {
                        if (this.checked) {
                            accountFields.classList.remove('d-none');
                        } else {
                            accountFields.classList.add('d-none');
                        }
                    });
                }

                // 8. Dynamic Salary Allowances Repeater
                let allowanceIndex = $('#allowancesTableBody tr.allowance-row').length;
                $('#btnAddAllowance').on('click', function () {
                    const templateHtml = $('#allowanceRowTemplate').html();
                    if (!templateHtml) return;

                    const renderedHtml = templateHtml.replace(/__INDEX__/g, allowanceIndex);
                    allowanceIndex++;

                    const $newRow = $(renderedHtml);
                    $('#allowancesTableBody').append($newRow);
                    $('#noAllowancesAlert').addClass('d-none');

                    setupSelect2($newRow);
                });

                $(document).on('click', '.btn-remove-allowance', function () {
                    $(this).closest('tr.allowance-row').remove();
                    if ($('#allowancesTableBody tr.allowance-row').length === 0) {
                        $('#noAllowancesAlert').removeClass('d-none');
                    }
                });

                // Employee File Upload custom handler
                $(document).on('change', '.employee-file-input', function() {
                    let fileName = $(this).val().split('\\').pop() || "{{ __('No file chosen') }}";
                    $(this).closest('.employee-file-upload').find('.employee-file-name').text(fileName);
                });
// 9. Dynamic Documents Repeater
                let documentIndex = $('#documentsContainer .document-item').length;
                $('#btnAddDocument').on('click', function () {
                    const templateHtml = $('#documentItemTemplate').html();
                    if (!templateHtml) return;

                    const docNum = $('#documentsContainer .document-item').length + 1;
                    let renderedHtml = templateHtml.replace(/__INDEX__/g, documentIndex);
                    renderedHtml = renderedHtml.replace(/__DOC_NUM__/g, docNum);
                    documentIndex++;

                    const $newDoc = $(renderedHtml);
                    $('#documentsContainer').append($newDoc);
                    $('#noDocumentsAlert').addClass('d-none');

                    setupSelect2($newDoc);
                });

                $(document).on('click', '.btn-remove-document', function () {
                    $(this).closest('.document-item').remove();
                    $('#documentsContainer .document-item').each(function (idx) {
                        $(this).find('.doc-number-label').text(idx + 1);
                    });
                    if ($('#documentsContainer .document-item').length === 0) {
                        $('#noDocumentsAlert').removeClass('d-none');
                    }
                });
            });
        </script>
    @endsection