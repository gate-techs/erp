@extends('employeeportal::layouts.master')

@section('title', __('Official Documents'))

@section('content')
<div class="portal-docs-container">
    <!-- 1. Top Navigation Header -->
    <div class="portal-docs-nav-header">
        <a href="{{ route('portal.requests') }}" class="portal-docs-back-btn" title="{{ __('Back') }}">
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-right' : 'bx-chevron-left' }}"></i>
        </a>
        <h1 class="portal-docs-nav-title">{{ __('Official Documents') }}</h1>
        <div class="portal-docs-nav-placeholder"></div>
    </div>

    <!-- 2. Action Header Row (ملف الوثائق الرسمية + زر رفع مستند جديد) -->
    <div class="portal-docs-header-row">
        <div class="portal-docs-title-group">
            <h2 class="portal-docs-main-title">{{ __('Official Documents File') }}</h2>
            <p class="portal-docs-subtitle">{{ __('Register of professional documents and official accreditations') }}</p>
        </div>

        <a href="{{ route('portal.documents.create') }}" class="portal-docs-upload-trigger-btn">
            <i class="bx bx-cloud-upload"></i>
            <span>{{ __('Upload New Document') }}</span>
        </a>
    </div>

    <!-- 3. Dark Navy Warning Card (يظهر ديناميكياً إذا وُجد مستند منتهي أو يقترب من الانتهاء) -->
    @if($hasWarning && $warningBanner)
    <div class="portal-docs-warning-card">
        <div class="portal-docs-warning-header">
            <h3 class="portal-docs-warning-title">{{ $warningBanner['title'] }}</h3>
            <span class="portal-docs-warning-pill">{{ $warningBanner['days_badge'] }}</span>
        </div>
        <p class="portal-docs-warning-desc">{{ $warningBanner['description'] }}</p>
    </div>
    @endif

    <!-- 4. Stats Metrics Row (3 cards: مستندات معتمدة، يتطلب الإجراء، الامتثال المهني) -->
    <div class="portal-docs-stats-row">
        <!-- مستندات معتمدة -->
        <div class="portal-stat-card">
            <div class="portal-stat-card-num">{{ $approvedCount }}</div>
            <div class="portal-stat-card-label">{{ __('Approved Documents') }}</div>
        </div>

        <!-- يتطلب الإجراء -->
        <div class="portal-stat-card">
            <div class="portal-stat-card-num">{{ $actionRequiredCount }}</div>
            <div class="portal-stat-card-label">{{ __('Action Required') }}</div>
        </div>

        <!-- الامتثال المهني -->
        <div class="portal-stat-card">
            <div class="portal-stat-card-num">{{ $compliancePercentage }}%</div>
            <div class="portal-stat-card-label">{{ __('Professional Compliance') }}</div>
        </div>
    </div>

    <!-- 5. Section Heading: المستندات المحفوظة والمعتمدة -->
    <div class="portal-docs-section-title">
        <span>{{ __('Saved & Approved Documents') }}</span>
    </div>

    <!-- 6. List of Document Cards -->
    <div class="portal-docs-list">
        @forelse($documents as $doc)
        <div class="portal-doc-item-card">
            <!-- Top Line: Document Title & Expiry Badge -->
            <div class="portal-doc-item-top">
                <h3 class="portal-doc-item-title">{{ $doc->title }}</h3>
                <span class="portal-doc-status-badge {{ $doc->status_class }}">
                    <span class="portal-doc-dot {{ $doc->status_dot }}"></span>
                    <span>{{ $doc->status_label }}</span>
                </span>
            </div>

            <!-- Subtitle: Document Number or Notes -->
            <div class="portal-doc-item-meta">
                {{ $doc->subtitle }}
            </div>

            <!-- Action Buttons: Download PDF & Preview & Edit -->
            <div class="portal-doc-actions-row">
                <!-- تحميل PDF -->
                @if($doc->has_file)
                    <a href="{{ $doc->file_url }}" download class="portal-btn-download-pdf">
                        <i class="bx bx-download"></i>
                        <span>{{ __('Download PDF') }}</span>
                    </a>
                @else
                    <button type="button" class="portal-btn-download-pdf" onclick="showPortalToast('error', '{{ __('Official Documents') }}', '{{ __('No file attached') }}')">
                        <i class="bx bx-download"></i>
                        <span>{{ __('Download PDF') }}</span>
                    </button>
                @endif

                <!-- معاينة -->
                @if($doc->has_file)
                    <a href="{{ $doc->file_url }}" target="_blank" rel="noopener noreferrer" class="portal-btn-preview-doc">
                        <i class="bx bx-show"></i>
                        <span>{{ __('Preview') }}</span>
                    </a>
                @else
                    <a href="{{ route('portal.documents.create', ['id' => $doc->id]) }}" class="portal-btn-preview-doc">
                        <i class="bx bx-cloud-upload"></i>
                        <span>{{ __('Upload New Document') }}</span>
                    </a>
                @endif

                <!-- تعديل / تحديث المستند -->
                <a href="{{ route('portal.documents.create', ['id' => $doc->id]) }}" class="portal-btn-edit-doc" title="{{ __('Update Document') }}">
                    <i class="bx bx-edit-alt"></i>
                </a>
            </div>
        </div>
        @empty
        <div class="portal-docs-empty-state">
            <div class="portal-docs-empty-icon">
                <i class="bx bx-folder-open"></i>
            </div>
            <div class="portal-docs-empty-title">{{ __('No official documents uploaded yet.') }}</div>
            <p class="portal-docs-empty-desc">{{ __('You can add and record your official documents by clicking the button above.') }}</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
