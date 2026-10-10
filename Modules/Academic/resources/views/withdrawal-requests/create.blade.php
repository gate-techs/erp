@extends('layouts/contentNavbarLayout')
@section('title', __('Student Withdrawal Requests'))
@section('content')
<div class="mb-4"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('academic.withdrawal-requests.index') }}">{{ __('Student Withdrawal Requests') }}</a></li><li class="breadcrumb-item active">{{ __('Add Withdrawal Request') }}</li></ol><h4 class="fw-bold">{{ __('Student Withdrawal Request') }}</h4></div>
@if($errors->any())
<div class="alert alert-danger">{{ __('Please review the highlighted fields.') }}</div>
@endif
@include('academic::withdrawal-requests._form')
@endsection
