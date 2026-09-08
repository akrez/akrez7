<?php

namespace App\Http\Controllers;

use App\Data\CategoryProperty\StoreCategoryPropertyData;
use App\Data\CategoryProperty\UpdateCategoryPropertyData;
use App\Services\CategoryPropertyService;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryPropertyController extends Controller
{
    public function __construct(protected CategoryPropertyService $categoryPropertyService) {}

    public function index()
    {
        $response = $this->categoryPropertyService->getLatestCategoryProperties($this->blogId());
        $categories = CategoryService::new()->getLatestCategories($this->blogId())->getData('categories');

        return view('category_property.index', [
            'category_properties' => $response->getData('category_properties'),
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        $categories = CategoryService::new()->getLatestCategories($this->blogId())->getData('categories');

        return view('category_property.create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $storeCategoryPropertyData = new StoreCategoryPropertyData(
            null,
            $this->blogId(),
            $request->category_id,
            $request->name,
            $request->filter_type,
            $request->unit
        );

        $response = $this->categoryPropertyService->storeCategoryProperty($storeCategoryPropertyData);

        return $response->successfulRoute(route('category_properties.index'));
    }

    public function edit(int $id)
    {
        $response = $this->categoryPropertyService->getCategoryProperty($this->blogId(), $id)->abortUnSuccessful();
        $categories = CategoryService::new()->getLatestCategories($this->blogId())->getData('categories');

        return view('category_property.edit', [
            'category_property' => $response->getData('category_property'),
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $updateCategoryPropertyData = new UpdateCategoryPropertyData(
            $id,
            $this->blogId(),
            $request->category_id,
            $request->name,
            $request->filter_type,
            $request->unit
        );

        $response = $this->categoryPropertyService->updateCategoryProperty($updateCategoryPropertyData);

        return $response->successfulRoute(route('category_properties.index'));
    }
}
