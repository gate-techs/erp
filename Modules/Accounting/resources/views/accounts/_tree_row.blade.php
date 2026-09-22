@php
    $locale = app()->getLocale();
    $name = $account->translate($locale)?->name 
         ?? ($locale === 'ar' ? $account->translate('en')?->name : $account->translate('ar')?->name) 
         ?? '-';
    $hasChildren = $account->children && $account->children->isNotEmpty();
    $typeColors = [
        'asset'       => 'primary',
        'liability'   => 'danger',
        'equity'      => 'warning',
        'income'      => 'success',
        'expense'     => 'info',
        'off_balance' => 'secondary',
    ];
    $color = $typeColors[$account->account_type->value] ?? 'secondary';
    $indent = ($account->level - 1) * 24;
@endphp

<tr class="coa-tree-row {{ $account->parent_id ? 'child-of-' . $account->parent_id : 'root-node' }} {{ $account->is_deprecated ? 'row-deprecated opacity-75' : '' }}"
    id="coa-row-{{ $account->id }}"
    data-id="{{ $account->id }}"
    data-parent-id="{{ $account->parent_id ?? '' }}"
    data-level="{{ $account->level }}"
    data-is-group="{{ $account->is_group ? '1' : '0' }}"
    data-type="{{ $account->account_type->value }}"
    data-nature="{{ $account->is_group ? 'group' : 'posting' }}"
    data-status="{{ $account->is_deprecated ? 'deprecated' : ($account->is_active ? 'active' : 'inactive') }}"
    data-code="{{ $account->code }}"
    data-name="{{ strtolower($name . ' ' . $account->code) }}">
    
    <!-- 1. Code & Localized Name with Indentation -->
    <td class="coa-node-cell text-nowrap">
        <div class="d-flex align-items-center" style="{{ app()->getLocale() === 'ar' ? 'padding-right: ' . $indent . 'px;' : 'padding-left: ' . $indent . 'px;' }}">
            
            @if($hasChildren)
                <button type="button" class="btn btn-icon btn-sm btn-text-secondary btn-tree-toggle me-1 p-0 rounded-circle" data-target="{{ $account->id }}" title="{{ __('Toggle Sub-accounts') }}">
                    <i class="icon-base bx bx-chevron-down font-18 text-primary transition-transform"></i>
                </button>
            @else
                <span class="tree-leaf-spacer me-1 d-inline-block" style="width: 24px;">
                    <i class="icon-base bx bx-minus text-muted opacity-25 font-14"></i>
                </span>
            @endif

            <div class="avatar avatar-xs me-2 flex-shrink-0 d-flex align-items-center justify-content-center rounded {{ $account->is_group ? 'bg-label-primary' : 'bg-label-secondary' }}">
                @if($account->is_group)
                    <i class="icon-base bx {{ $hasChildren ? 'bx-folder' : 'bx-folder-minus' }} font-16 node-icon"></i>
                @else
                    <i class="icon-base bx bx-receipt font-16 text-heading"></i>
                @endif
            </div>

            <span class="fw-bold font-monospace text-heading me-2">{{ $account->code }}</span>

            <span class="account-name-text {{ $account->is_group ? 'fw-bold text-heading' : 'text-body' }}">
                {{ $name }}
            </span>

            @if($hasChildren)
                <span class="badge rounded-pill bg-label-secondary ms-2 fs-tiny fw-normal">
                    {{ $account->children->count() }}
                </span>
            @endif
        </div>
    </td>

    <!-- 2. Account Type -->
    <td class="text-nowrap text-center align-middle">
        <span class="badge bg-label-{{ $color }}">
            {{ $account->account_type->label() }}
        </span>
    </td>

    <!-- 3. Normal Balance -->
    <td class="text-nowrap text-center align-middle">
        @if($account->normal_balance === 'debit')
            <span class="badge bg-label-success">{{ __('Debit') }}</span>
        @else
            <span class="badge bg-label-info">{{ __('Credit') }}</span>
        @endif
    </td>

    <!-- 4. Group / Posting Nature -->
    <td class="text-nowrap text-center align-middle">
        @if($account->is_group)
            <span class="badge bg-label-secondary d-inline-flex align-items-center"><i class="icon-base bx bx-folder me-1 font-12"></i>{{ __('Group') }}</span>
        @else
            <span class="badge bg-label-primary d-inline-flex align-items-center"><i class="icon-base bx bx-receipt me-1 font-12"></i>{{ __('Posting') }}</span>
        @endif
    </td>

    <!-- 5. Status -->
    <td class="text-nowrap text-center align-middle">
        @if($account->is_deprecated)
            <span class="badge bg-label-secondary d-inline-flex align-items-center"><i class="icon-base bx bx-archive me-1 font-12"></i>{{ __('Deprecated') }}</span>
        @elseif($account->is_active)
            <span class="badge bg-label-success d-inline-flex align-items-center"><i class="icon-base bx bx-check-circle me-1 font-12"></i>{{ __('Active') }}</span>
        @else
            <span class="badge bg-label-danger d-inline-flex align-items-center"><i class="icon-base bx bx-x-circle me-1 font-12"></i>{{ __('Inactive') }}</span>
        @endif
    </td>

    <!-- 6. Actions (Matching standard ERP icons) -->
    <td class="text-end text-nowrap">
        <div class="d-inline-flex gap-1">
            @if($account->is_group)
                <button type="button" 
                        class="btn btn-icon btn-sm btn-outline-success btn-quick-add-child" 
                        data-parent-id="{{ $account->id }}" 
                        data-parent-code="{{ $account->code }}" 
                        data-type="{{ $account->account_type->value }}" 
                        title="{{ __('Add Sub-Account') }}">
                    <i class="icon-base bx bx-plus"></i>
                </button>
            @endif

            <button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-account" data-id="{{ $account->id }}" title="{{ __('Edit') }}">
                <i class="icon-base bx bx-edit"></i>
            </button>

            <button type="button" 
                    class="btn btn-icon btn-sm btn-outline-warning btn-deprecate-account" 
                    data-id="{{ $account->id }}" 
                    data-deprecated="{{ $account->is_deprecated ? '1' : '0' }}" 
                    data-code="{{ $account->code }}"
                    data-name="{{ $name }}"
                    title="{{ $account->is_deprecated ? __('Reactivate Account') : __('Deprecate Account') }}">
                <i class="icon-base bx {{ $account->is_deprecated ? 'bx-undo' : 'bx-archive' }}"></i>
            </button>

            <button type="button" 
                    class="btn btn-icon btn-sm btn-outline-danger btn-delete-account" 
                    data-id="{{ $account->id }}" 
                    data-has-children="{{ $hasChildren ? '1' : '0' }}"
                    data-code="{{ $account->code }}"
                    title="{{ __('Delete') }}">
                <i class="icon-base bx bx-trash"></i>
            </button>
        </div>
    </td>
</tr>

@if($hasChildren)
    @foreach($account->children as $child)
        @include('accounting::accounts._tree_row', ['account' => $child])
    @endforeach
@endif
