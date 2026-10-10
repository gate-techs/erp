@extends('layouts/contentNavbarLayout')
@section('title', __('Add Daily Report'))
@section('content')
<div class="mb-4"><nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('academic.daily-reports.index') }}">{{ __('Daily Reports') }}</a></li><li class="breadcrumb-item active">{{ __('Add Daily Report') }}</li></ol></nav><h4 class="fw-bold">{{ __('Daily Report') }}</h4></div>
@if($errors->any())<div class="alert alert-danger">{{ __('Please review the highlighted fields.') }}</div>@endif
@include('academic::daily-reports._form')
@endsection
