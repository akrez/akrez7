<?php

namespace App\Http\Controllers;

use App\Services\BlogService;
use App\Services\SummaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SummaryController extends Controller
{
    public function __construct(
        protected BlogService $blogService
    ) {}

    public function blog(Request $request, int $id)
    {
        $blog = $this->blogService->getUserBlog(Auth::id(), $id)->abortUnSuccessful();

        return $this->render($id, true);
    }

    public function show(Request $request)
    {
        $blog_id = $request->blog_id;
        $blog = $this->blogService->getApiResource($blog_id)->abortUnSuccessful();

        return $this->render($blog_id);
    }

    protected function render(int $id, bool $forgetCache = false)
    {
        return view('summary.show', [
            'data' => SummaryService::new()->getCachedApiResponse($id, request(), $forgetCache)->getData(),
        ]);
    }
}
