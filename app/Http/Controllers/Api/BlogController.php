<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BlogService;
use App\Services\PresentService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function __construct(protected BlogService $blogService) {}

    public function show(Request $request, int $blog_id)
    {
        $blogResponse = $this->blogService->getApiResource($blog_id);
        if (! $blogResponse->isSuccessful()) {
            return ApiResponse::new($blogResponse->getStatus());
        }

        return PresentService::new()->getCachedApiResponse($blog_id, request());
    }
}
