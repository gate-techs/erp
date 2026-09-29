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
    $isAdmin = $user && (
        $user->hasAnyRole(['super_admin', 'admin', 'Super Admin', 'Admin']) ||
        $user->user_type === \App\Enums\UserType::ADMIN
    );

    if (! $isAdmin) {
        return redirect()->route('profile.show');
    }

    return view('content.dashboard.dashboards-analytics');
  }
}
