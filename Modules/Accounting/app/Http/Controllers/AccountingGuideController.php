<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AccountingGuideController extends Controller
{
    /**
     * Display the comprehensive interactive Accounting System User Guide.
     */
    public function index(): View
    {
        return view('accounting::guide.index');
    }
}
