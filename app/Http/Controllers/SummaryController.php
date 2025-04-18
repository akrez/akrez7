<?php

namespace App\Http\Controllers;

use App\Services\BlogService;
use App\Services\DomainService;
use App\Services\SummaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SummaryController extends Controller
{
    public function __construct(
        protected BlogService $blogService
    ) {}

    public function domain(Request $request, $summary)
    {
        $id = DomainService::new()->domainToBlogId($summary);
        abort_unless($id, 404);

        $blog = $this->blogService->getApiResource($id);
        abort_unless($id, 404);

        return $this->render($id);
    }

    public function blog(Request $request, int $id)
    {
        $blog = $this->blogService->getUserBlog(Auth::id(), $id)->abortUnSuccessful();

        return $this->render($id);
    }

    public function show(Request $request, int $id)
    {
        $blog = $this->blogService->getApiResource($id);
        abort_unless($id, 404);

        return $this->render($id);
    }

    protected function render(int $id)
    {
        return view('summary.show', [
            'data' => SummaryService::new()->getApiResponse($id, request())->getData(),
        ]);
    }
}
