@extends('layouts/contentNavbarLayout')
@section('title', __('Edit Daily Report'))
@section('content')
<div class="mb-4"><nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('academic.daily-reports.index') }}">{{ __('Daily Reports') }}</a></li><li class="breadcrumb-item active">{{ __('Edit Daily Report') }}</li></ol></nav><h4 class="fw-bold">{{ __('Edit Daily Report') }}</h4></div>
@include('academic::daily-reports._form', ['report'=>$report])
@endsection
