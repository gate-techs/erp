@extends('layouts/contentNavbarLayout')
@section('title', __('Edit Package') . ' - ' . config('variables.templateName'))
@section('content')
<div class="mb-4"><nav aria-label="breadcrumb"><ol class="breadcrumb mb-1"><li class="breadcrumb-item"><a href="{{ route('packages.index') }}">{{ __('Packages') }}</a></li><li class="breadcrumb-item active">{{ __('Edit Package') }}</li></ol></nav><h4 class="fw-bold mb-0">{{ __('Edit Package') }}</h4></div>
@if($errors->any())<div class="alert alert-danger">{{ __('Please review the highlighted fields.') }}</div>@endif
@include('nursery::packages._form', ['package' => $package])
@endsection
