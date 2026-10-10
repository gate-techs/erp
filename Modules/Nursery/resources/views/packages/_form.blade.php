@php($editing = isset($package))
<form method="POST" action="{{ $editing ? route('packages.update', $package) : route('packages.store') }}" enctype="multipart/form-data">
    @csrf
    @if($editing) @method('PUT') @endif
    <div class="card shadow-sm border-0"><div class="card-body"><div class="row g-3">
        <div class="col-md-6"><label class="form-label fw-semibold">{{ __('Nursery') }} *</label><select name="nursery_id" class="form-select select2 @error('nursery_id') is-invalid @enderror" required><option value="">{{ __('Select Nursery') }}</option>@foreach($nurseries as $nursery)<option value="{{ $nursery->id }}" @selected(old('nursery_id', $package->nursery_id ?? '') == $nursery->id)>{{ $nursery->name }}</option>@endforeach</select>@error('nursery_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-3"><label class="form-label fw-semibold">{{ __('Order') }}</label><input type="number" min="0" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $package->sort_order ?? 0) }}">@error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-3"><label class="form-label fw-semibold">{{ __('Status') }} *</label><select name="status" class="form-select select2"><option value="active" @selected(old('status', $package->status ?? 'active') === 'active')>{{ __('Active') }}</option><option value="inactive" @selected(old('status', $package->status ?? '') === 'inactive')>{{ __('Inactive') }}</option></select></div>
        <div class="col-md-6"><label class="form-label fw-semibold">{{ __('Package Name (Arabic)') }} *</label><input name="name_ar" dir="rtl" class="form-control @error('name_ar') is-invalid @enderror" value="{{ old('name_ar', $editing ? $package->translateOrNew('ar')->name : '') }}" required>@error('name_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label class="form-label fw-semibold">{{ __('Package Name (English)') }} *</label><input name="name_en" dir="ltr" class="form-control @error('name_en') is-invalid @enderror" value="{{ old('name_en', $editing ? $package->translateOrNew('en')->name : '') }}" required>@error('name_en')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label class="form-label fw-semibold">{{ __('Description (Arabic)') }}</label><textarea name="description_ar" dir="rtl" rows="3" class="form-control">{{ old('description_ar', $editing ? $package->translateOrNew('ar')->description : '') }}</textarea></div>
        <div class="col-md-6"><label class="form-label fw-semibold">{{ __('Description (English)') }}</label><textarea name="description_en" dir="ltr" rows="3" class="form-control">{{ old('description_en', $editing ? $package->translateOrNew('en')->description : '') }}</textarea></div>
        <div class="col-md-4"><label class="form-label fw-semibold">{{ __('Price') }}</label><input type="number" min="0" step="0.001" name="price" class="form-control" value="{{ old('price', $package->price ?? '') }}"></div>
        <div class="col-md-4"><label class="form-label fw-semibold">{{ __('Discount Type') }}</label><select name="discount_type" class="form-select select2"><option value="fixed" @selected(old('discount_type', $package->discount_type ?? 'fixed') === 'fixed')>{{ __('Fixed') }}</option><option value="percentage" @selected(old('discount_type', $package->discount_type ?? '') === 'percentage')>{{ __('Percentage') }}</option></select></div>
        <div class="col-md-4"><label class="form-label fw-semibold">{{ __('Discount') }}</label><input type="number" min="0" step="0.001" name="discount_value" class="form-control" value="{{ old('discount_value', $package->discount_value ?? 0) }}"></div>
        <div class="col-md-6"><label class="form-label fw-semibold">{{ __('Cover Image') }}</label><input type="file" name="cover_image" accept="image/*" class="form-control">@if($editing && $package->cover_image_url)<a class="small d-inline-block mt-1" href="{{ $package->cover_image_url }}" target="_blank">{{ __('View current cover') }}</a>@endif</div>
        <div class="col-md-6"><label class="form-label fw-semibold">{{ __('Gallery Images') }}</label><input type="file" name="gallery[]" accept="image/*" multiple class="form-control"><small class="text-muted">{{ __('Uploading new images replaces the current gallery.') }}</small></div>
    </div></div></div>
    <div class="card shadow-sm border-0 mt-4"><div class="card-header d-flex justify-content-between align-items-center"><h5 class="mb-0">{{ __('Features') }}</h5><button type="button" class="btn btn-sm btn-label-primary" id="addFeature"><i class="bx bx-plus me-1"></i>{{ __('Add Feature') }}</button></div><div class="card-body"><div id="featuresContainer"></div></div></div>
    <div class="d-flex justify-content-end gap-2 mt-4"><a href="{{ route('packages.index') }}" class="btn btn-label-secondary">{{ __('Cancel') }}</a><button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i>{{ $editing ? __('Save Changes') : __('Save') }}</button></div>
</form>

<template id="featureTemplate"><div class="feature-row row g-2 mb-2"><div class="col-md-5"><input class="form-control" data-name="name_ar" placeholder="{{ __('Arabic feature') }}" dir="rtl"></div><div class="col-md-5"><input class="form-control" data-name="name_en" placeholder="{{ __('English feature') }}"></div><div class="col-md-2"><button type="button" class="btn btn-label-danger w-100 remove-feature"><i class="bx bx-trash"></i></button></div></div></template>

@push('scripts')
<script>
(() => {
    const container = document.getElementById('featuresContainer'); const template = document.getElementById('featureTemplate'); let index = 0;
    function addFeature(feature = {}) { const fragment = template.content.cloneNode(true); const row = fragment.querySelector('.feature-row'); row.querySelectorAll('[data-name]').forEach(input => { const field=input.dataset.name; input.name=`features[${index}][${field}]`; input.value=feature[field] || ''; }); index++; container.appendChild(fragment); }
    document.getElementById('addFeature').addEventListener('click', () => addFeature());
    container.addEventListener('click', event => { if(event.target.closest('.remove-feature')) event.target.closest('.feature-row').remove(); });
    const existing = @json(old('features', $editing ? $package->features->map(fn ($feature) => ['name_ar' => $feature->translateOrNew('ar')->name, 'name_en' => $feature->translateOrNew('en')->name])->values() : []));
    if(existing.length) existing.forEach(addFeature); else addFeature();
    function initSelect2() {
        if (!window.jQuery || !window.jQuery.fn.select2) { setTimeout(initSelect2, 50); return; }
        window.jQuery('.select2').select2({
            theme: 'bootstrap-5',
            allowClear: true,
            width: '100%',
            dir: document.documentElement.dir === 'rtl' ? 'rtl' : 'ltr'
        });
    }
    initSelect2();
})();
</script>
@endpush
