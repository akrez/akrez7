<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BlogService;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function __construct(protected BlogService $blogService) {}

    public function index(Request $request, int $id)
    {
        return BlogService::new()->getApiResponse($id);
    }
}
