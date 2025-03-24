<?php

namespace App\Http\Controllers;

use App\Data\Color\StoreColorData;
use App\Data\Color\UpdateColorData;
use App\Services\ColorService;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function __construct(protected ColorService $colorService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $response = $this->colorService->getLatestColors(app('ActiveBlog')->id());

        return view('colors.index', [
            'colors' => $response->getData('colors'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('colors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $storeColorData = new StoreColorData(
            null,
            app('ActiveBlog')->id(),
            $request->code,
            $request->name
        );

        $response = $this->colorService->storeColor($storeColorData);

        return $response->successfulRoute(route('colors.index'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $response = $this->colorService->getColor(app('ActiveBlog')->id(), $id);
        $response->abortUnSuccessful();

        return view('colors.edit', [
            'color' => $response->getData('color'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $updateColorData = new UpdateColorData(
            $id,
            app('ActiveBlog')->id(),
            $request->code,
            $request->name,
        );

        $response = $this->colorService->updateColor($updateColorData);

        return $response->successfulRoute(route('colors.index'));
    }
}
