<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BlogService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function __construct(protected BlogService $blogService) {}

    public function index(Request $request, int $id)
    {
        $blogResponse = $this->blogService->getApiResource($id);
        if (! $blogResponse->isSuccessful()) {
            return ApiResponse::new($blogResponse->getStatus());
        }

        return BlogService::new()->getApiResponse($id);
    }
}
