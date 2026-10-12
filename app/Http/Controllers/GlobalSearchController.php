<?php

namespace App\Http\Controllers;

use App\Services\GlobalSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    protected GlobalSearchService $searchService;

    public function __construct(GlobalSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Handle incoming search query.
     */
    public function search(Request $request): JsonResponse
    {
        $term = (string) $request->input('q', '');
        $limit = (int) $request->input('limit', 5);

        $results = $this->searchService->search($term, $limit);

        return response()->json([
            'status' => true,
            'data' => $results,
        ]);
    }
}
