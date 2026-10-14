@extends('layouts/contentNavbarLayout')

@section('title', __('Audit Log & External Database Settings') . ' - ' . config('variables.templateName'))

@section('page-style')
<style>
    /* Phones: action buttons stacked full width (labels stay on one line) */
    @media (max-width: 575.98px) {
        .audit-settings-actions > div,
        .audit-settings-actions .btn {
            width: 100%;
        }
        .audit-settings-actions .btn {
            justify-content: center;
            white-space: nowrap;
        }
    }
</style>
@endsection

@section('content')
<!-- Header & Navigation -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div style="flex: 1 1 260px; min-width: 0;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item">{{ __('System') }}</li>
                    <li class="breadcrumb-item"><a href="{{ route('audit-logs.index') }}">{{ __('Activity Log') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('External Connection Settings') }}</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="icon-base bx bx-data text-primary font-24 flex-shrink-0"></i>
                <span>{{ __('External Connection Settings') }}</span>
            </h4>
            <p class="text-muted small mb-0">{{ __('Configure and route audit logs to a dedicated external server or database (Enterprise Isolation).') }}</p>
        </div>

        <a href="{{ route('audit-logs.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1 text-nowrap flex-shrink-0">
            <i class="bx bx-arrow-back"></i> {{ __('Back to Logs') }}
        </a>
    </div>



    <div class="row g-4">
        <!-- Settings Form Column -->
        <div class="col-xl-8 col-lg-7">
            <form action="{{ route('audit-logs.settings.update') }}" method="POST" id="auditSettingsForm">
                @csrf

                <!-- Card: External DB Connection -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="bx bx-server text-primary flex-shrink-0"></i> {{ __('External Database Connection Settings') }}
                        </h6>

                        <!-- Toggle Enable/Disable -->
                        <div class="form-check form-switch form-switch-lg mb-0">
                            <input class="form-check-input" type="checkbox" id="is_external_db_enabled" name="is_external_db_enabled" value="1" {{ old('is_external_db_enabled', $settings->is_external_db_enabled) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-primary text-nowrap" for="is_external_db_enabled">
                                {{ __('Enable External Storage') }}
                            </label>
                        </div>
                    </div>

                    <div class="card-body pt-4">
                        <div class="row g-3">
                            <!-- Driver -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-heading">{{ __('Database Driver') }}</label>
                                <select name="driver" id="db_driver" class="form-select">
                                    <option value="mysql" {{ old('driver', $settings->driver) === 'mysql' ? 'selected' : '' }}>MySQL</option>
                                    <option value="mariadb" {{ old('driver', $settings->driver) === 'mariadb' ? 'selected' : '' }}>MariaDB</option>
                                    <option value="pgsql" {{ old('driver', $settings->driver) === 'pgsql' ? 'selected' : '' }}>PostgreSQL</option>
                                    <option value="sqlsrv" {{ old('driver', $settings->driver) === 'sqlsrv' ? 'selected' : '' }}>SQL Server</option>
                                </select>
                            </div>

                            <!-- Host -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-heading">{{ __('Host / IP Address') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-globe"></i></span>
                                    <input type="text" name="host" id="db_host" class="form-control" placeholder="{{ __('127.0.0.1 or db.cloud-server.com') }}" value="{{ old('host', $settings->host) }}" dir="ltr">
                                </div>
                            </div>

                            <!-- Port -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-heading">{{ __('Port') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-hash"></i></span>
                                    <input type="text" name="port" id="db_port" class="form-control" placeholder="3306" value="{{ old('port', $settings->port ?: '3306') }}" dir="ltr">
                                </div>
                            </div>

                            <!-- Database Name -->
                            <div class="col-md-8">
                                <label class="form-label fw-semibold text-heading">{{ __('Database Name') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-cylinder"></i></span>
                                    <input type="text" name="database" id="db_database" class="form-control" placeholder="erp_audit_db" value="{{ old('database', $settings->database) }}" dir="ltr">
                                </div>
                            </div>

                            <!-- Username -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-heading">{{ __('Username') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-user"></i></span>
                                    <input type="text" name="username" id="db_username" class="form-control" placeholder="root / db_user" value="{{ old('username', $settings->username) }}" dir="ltr">
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-heading d-flex justify-content-between align-items-center">
                                    <span>{{ __('Password') }}</span>
                                    @if(!empty($settings->password))
                                        <small class="text-success"><i class="bx bx-lock-alt"></i> {{ __('Saved & Encrypted') }}</small>
                                    @endif
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-key"></i></span>
                                    <input type="password" name="password" id="db_password" class="form-control" placeholder="{{ !empty($settings->password) ? '••••••••••••' : __('Enter password...') }}" dir="ltr">
                                    <button class="btn btn-outline-secondary toggle-pass" type="button" data-target="#db_password">
                                        <i class="bx bx-hide"></i>
                                    </button>
                                </div>
                                <small class="text-muted">{{ __('Leave empty if you don\'t want to change the saved password.') }}</small>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Advanced Resilience Options -->
                        <h6 class="fw-bold text-dark mb-3">{{ __('Resilience & Fail-Safe Options') }}</h6>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="fallback_to_local" name="fallback_to_local" value="1" {{ old('fallback_to_local', $settings->fallback_to_local) ? 'checked' : '' }}>
                            <label class="form-check-label text-dark" for="fallback_to_local">
                                <strong>{{ __('Fail-Safe Fallback: Log to local DB if external server is down') }}</strong>
                                <small class="text-muted d-block">{{ __('Ensures system continuity and prevents failure during external network slowdowns or outages.') }}</small>
                            </label>
                        </div>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="card-footer audit-settings-actions bg-transparent border-top py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" id="btnTestConnection" class="btn btn-outline-primary d-flex align-items-center gap-1">
                                <i class="bx bx-broadcast"></i>
                                <span>{{ __('Test Connection') }}</span>
                            </button>

                            <button type="button" id="btnSyncSchema" class="btn btn-outline-info d-flex align-items-center gap-1">
                                <i class="bx bx-refresh"></i>
                                <span>{{ __('Sync / Create Logs Table') }}</span>
                            </button>
                        </div>

                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
                            <i class="bx bx-save"></i>
                            <span>{{ __('Save and Apply Settings') }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Info & Status Sidebar Column -->
        <div class="col-xl-4 col-lg-5">
            <!-- Connection Status Box -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="bx bx-pulse text-info"></i> {{ __('Connection & Readiness Status') }}
                    </h6>
                </div>
                <div class="card-body pt-3" id="testResultBox">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 p-3 rounded {{ $settings->is_external_db_enabled ? 'bg-label-success' : 'bg-label-secondary' }}">
                        <div class="fw-semibold">
                            {{ __('Current Working Mode:') }}
                        </div>
                        <span class="badge {{ $settings->is_external_db_enabled ? 'bg-success' : 'bg-secondary' }}">
                            {{ $settings->is_external_db_enabled ? __('External Database Enabled') : __('Local System Database') }}
                        </span>
                    </div>

                    @if($settings->last_tested_at)
                        <div class="border rounded p-3 mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted">{{ __('Last Connection Test:') }}</small>
                                <span class="badge {{ $settings->last_test_status === 'success' ? 'bg-label-success' : 'bg-label-danger' }}">
                                    {{ $settings->last_test_status === 'success' ? __('Successful') : __('Failed') }}
                                </span>
                            </div>
                            <small class="text-muted d-block mb-2 font-monospace">{{ $settings->last_tested_at->format('Y-m-d H:i:s') }}</small>
                            <p class="small mb-0 text-dark">{{ $settings->last_test_message }}</p>
                        </div>
                    @else
                        <div class="text-center py-3 text-muted small">
                            {{ __('Connection not tested yet. Click \'Test Connection\' to verify.') }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Architecture Guidelines Box -->
            <div class="card border-0 shadow-sm bg-lighter">
                <div class="card-body p-3">
                    <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-1">
                        <i class="bx bx-bulb text-warning"></i> {{ __('Architectural Isolation Guidelines') }}
                    </h6>
                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.7;">
                        <li>{{ __('External storage prevents primary database bloat over time.') }}</li>
                        <li>{{ __('Password is automatically encrypted in the DB with AES-256.') }}</li>
                        <li>{{ __('Relations between logs, users, and employees work seamlessly in memory without direct foreign keys.') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;

    // 1. Toggle Password Visibility
    $('.toggle-pass').on('click', function () {
        const target = $($(this).data('target'));
        const icon = $(this).find('i');
        if (target.attr('type') === 'password') {
            target.attr('type', 'text');
            icon.removeClass('bx-hide').addClass('bx-show');
        } else {
            target.attr('type', 'password');
            icon.removeClass('bx-show').addClass('bx-hide');
        }
    });

    // 2. AJAX Test Connection
    $('#btnTestConnection').on('click', function () {
        const btn = $(this);
        const originalHtml = btn.html();

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> {{ __("Testing connection...") }}');

        $.ajax({
            url: "{{ route('audit-logs.settings.test-connection') }}",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                driver: $('#db_driver').val(),
                host: $('#db_host').val(),
                port: $('#db_port').val(),
                database: $('#db_database').val(),
                username: $('#db_username').val(),
                password: $('#db_password').val(),
            },
            success: function (res) {
                let alertClass = res.success ? 'alert-success' : 'alert-danger';
                let icon = res.success ? 'bx-check-circle' : 'bx-x-circle';

                let html = `
                    <div class="alert ${alertClass} alert-dismissible mb-0" role="alert">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bx ${icon} fs-4"></i>
                            <div>${res.message}</div>
                        </div>
                    </div>
                `;

                $('#testResultBox').html(html);
            },
            error: function (xhr) {
                let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : '{{ __("Connection test failed") }}';
                $('#testResultBox').html(`
                    <div class="alert alert-danger mb-0">
                        <i class="bx bx-x-circle me-1"></i> ${msg}
                    </div>
                `);
            },
            complete: function () {
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    // 3. AJAX Sync Schema
    $('#btnSyncSchema').on('click', function () {
        const btn = $(this);
        const originalHtml = btn.html();

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> {{ __("Creating table...") }}');

        $.ajax({
            url: "{{ route('audit-logs.settings.sync-schema') }}",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                driver: $('#db_driver').val(),
                host: $('#db_host').val(),
                port: $('#db_port').val(),
                database: $('#db_database').val(),
                username: $('#db_username').val(),
                password: $('#db_password').val(),
            },
            success: function (res) {
                let alertClass = res.success ? 'alert-info' : 'alert-danger';
                let icon = res.success ? 'bx-check-circle' : 'bx-x-circle';

                $('#testResultBox').html(`
                    <div class="alert ${alertClass} mb-0">
                        <i class="bx ${icon} me-1"></i> ${res.message}
                    </div>
                `);
            },
            error: function (xhr) {
                let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : '{{ __("Table sync failed") }}';
                $('#testResultBox').html(`
                    <div class="alert alert-danger mb-0">
                        <i class="bx bx-x-circle me-1"></i> ${msg}
                    </div>
                `);
            },
            complete: function () {
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });
});
</script>
@endsection
