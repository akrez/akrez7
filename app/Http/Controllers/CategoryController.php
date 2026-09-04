<?php

namespace App\Http\Controllers;

use App\Data\Category\StoreCategoryData;
use App\Data\Category\UpdateCategoryData;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $categoryService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $response = $this->categoryService->getLatestCategories($this->blogId());

        return view('category.index', [
            'categories' => $response->getData('categories'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $storeCategoryData = new StoreCategoryData(
            null,
            $this->blogId(),
            $request->name,
            $request->category_status,
            $request->category_order
        );

        $response = $this->categoryService->storeCategory($storeCategoryData);

        return $response->successfulRoute(route('categories.index'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $response = $this->categoryService->getCategory($this->blogId(), $id)->abortUnSuccessful();

        return view('category.edit', [
            'category' => $response->getData('category'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $updateCategoryData = new UpdateCategoryData(
            $id,
            $this->blogId(),
            $request->name,
            $request->category_status,
            $request->category_order
        );

        $response = $this->categoryService->updateCategory($updateCategoryData);

        return $response->successfulRoute(route('categories.index'));
    }
}
