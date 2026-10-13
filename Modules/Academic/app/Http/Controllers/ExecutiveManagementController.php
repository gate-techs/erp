<?php

namespace Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Academic\Services\ExecutiveManagementService;

class ExecutiveManagementController extends Controller
{
    protected ExecutiveManagementService $service;

    public function __construct(ExecutiveManagementService $service)
    {
        $this->service = $service;
    }

    /**
     * Display the Executive Management General Report
     */
    public function index(Request $request): View
    {
        $filters = [
            'branch_id'  => $request->input('branch_id'),
            'start_date' => $request->input('start_date'),
            'end_date'   => $request->input('end_date'),
        ];

        $data = $this->service->getDashboardData($filters);

        return view('academic::executive-management.index', $data);
    }

    /**
     * AJAX endpoint to fetch dashboard data dynamically when filtering by branch or dates
     */
    public function data(Request $request): JsonResponse
    {
        $filters = [
            'branch_id'  => $request->input('branch_id'),
            'start_date' => $request->input('start_date'),
            'end_date'   => $request->input('end_date'),
        ];

        $data = $this->service->getDashboardData($filters);

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }
}
