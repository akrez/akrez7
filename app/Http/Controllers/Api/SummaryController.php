<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BlogService;
use App\Services\SummaryService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    public function __construct(protected BlogService $blogService) {}

    public function index(Request $request, int $blog_id)
    {
        $blogResponse = $this->blogService->getApiResource($blog_id);
        if (! $blogResponse->isSuccessful()) {
            return ApiResponse::new($blogResponse->getStatus());
        }

        return SummaryService::new()->getApiResponse($blog_id, request());
    }
}
