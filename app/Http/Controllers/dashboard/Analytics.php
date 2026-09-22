<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Analytics extends Controller
{
  //==================================
  public function index()
  {
    $user = auth()->user();
    if ($user && ($user->hasRole('employee') || $user->user_type === \App\Enums\UserType::EMPLOYEE) && ! $user->hasAnyRole(['super_admin', 'admin'])) {
        return redirect()->route('profile.show');
    }

    return view('content.dashboard.dashboards-analytics');
  }
}
