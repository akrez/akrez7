<?php

namespace App\Http\Controllers;

use App\Services\BlogService;
use App\Services\SummaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

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
        $blogId = $request->blog_id;

        $blog = $this->blogService->getApiResource($blogId)->abortUnSuccessful();

        return $this->render($blogId);
    }

    protected function render(int $id, bool $forgetCache = false)
    {
        return view('summary.show', [
            'data' => SummaryService::new()->getCachedApiResponse($id, request(), $forgetCache)->getData(),
        ]);
    }

    public function sitemap(Request $request)
    {
        $blogId = $request->blog_id;

        $blog = $this->blogService->getApiResource($blogId)->abortUnSuccessful();

        $response = SummaryService::new()->getSitemapResponse($blogId, request())->abortUnSuccessful();

        return Response::make($response->getData('sitemap'), $response->getStatus())
            ->header('Content-Type', 'application/xml');
    }
}
