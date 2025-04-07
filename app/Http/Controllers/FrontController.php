<?php

namespace App\Http\Controllers;

use App\Services\BlogService;
use App\Services\DomainService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontController extends Controller
{
    public function __construct(
        protected BlogService $blogService
    ) {}

    public function domain(Request $request, $front)
    {
        $id = DomainService::new()->domainToBlogId($front);
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
        return view('front.show', [
            'data' => $this->blogService->getApiResponse($id)->getData(),
        ]);
    }
}
