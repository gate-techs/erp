@extends('layouts/contentNavbarLayout')
@section('title', __('Edit Withdrawal Request'))
@section('content')
<div class="mb-4"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('academic.withdrawal-requests.index') }}">{{ __('Student Withdrawal Requests') }}</a></li><li class="breadcrumb-item active">{{ __('Edit Withdrawal Request') }}</li></ol><h4 class="fw-bold">{{ __('Edit Withdrawal Request') }}</h4></div>
@if($errors->any())
<div class="alert alert-danger">{{ __('Please review the highlighted fields.') }}</div>
@endif
@include('academic::withdrawal-requests._form', ['request' => $request])
@endsection
