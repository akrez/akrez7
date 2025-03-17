<?php

namespace App\Http\Controllers;

use App\Data\Blog\StoreBlogData;
use App\Data\Blog\UpdateBlogData;
use App\Services\BlogService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    public function __construct(protected BlogService $blogService) {}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $blogs = $this->blogService->getUserBlogs(Auth::id())->getData('blogs');

        return view('blog.index', [
            'blogs' => $blogs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('blog.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $response = $this->blogService->storeBlog(Auth::id(), new StoreBlogData(
            $request->name,
            $request->short_description,
            $request->description,
            $request->blog_status,
        ));

        return $response->successfulRoute(route('blogs.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $blog = $this->blogService->getUserBlog(Auth::id(), $id);
        $blog->abortUnSuccessful();

        return view('blog.edit', [
            'blog' => $blog->getData('blog'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $blog = $this->blogService->getUserBlog(Auth::id(), $id);
        $blog->abortUnSuccessful();

        $response = $this->blogService->updateBlog($blog->getData('blog'), new UpdateBlogData(
            $request->name,
            $request->short_description,
            $request->description,
            $request->blog_status,
        ));

        return $response->successfulRoute(route('blogs.index'));
    }

    public function active(int $id)
    {
        $user = Auth::user();

        $blog = $this->blogService->getUserBlog($user->id, $id);
        $blog->abortUnSuccessful();

        $response = UserService::new()->setActiveBlog($user, $blog->getData('blog.id'));
        $response->abortUnSuccessful();

        return $response->message(__(':name is selected successfully', [
            'name' => __('Blog'),
        ]))->successfulRoute(route('blogs.index'));
    }
}
