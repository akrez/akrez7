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
        $response = $this->colorService->getLatestColors($this->blogId());

        return view('color.index', [
            'colors' => $response->getData('colors'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('color.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $storeColorData = new StoreColorData(
            null,
            $this->blogId(),
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
        $response = $this->colorService->getColor($this->blogId(), $id)->abortUnSuccessful();

        return view('color.edit', [
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
            $this->blogId(),
            $request->code,
            $request->name,
        );

        $response = $this->colorService->updateColor($updateColorData);

        return $response->successfulRoute(route('colors.index'));
    }
}
