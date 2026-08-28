<?php

namespace App\Http\Controllers;

use App\Data\BaleBot\StoreBaleBotData;
use App\Data\BaleBot\UpdateBaleBotData;
use App\Data\BaleBot\UploadBaleBotData;
use App\Services\BaleBotService;
use Illuminate\Http\Request;

class BaleBotController extends Controller
{
    public function __construct(protected BaleBotService $baleBotService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $response = $this->baleBotService->getLatestBaleBots($this->blogId());

        return view('bale_bot.index', [
            'baleBots' => $response->getData('baleBots'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('bale_bot.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $storeBaleBotData = new StoreBaleBotData(
            null,
            $this->blogId(),
            $request->bale_token,
            $request->bale_bot_status,
            $request->user_service,
            $request->notify_admin_on_invoice,
            $request->admin
        );

        $response = $this->baleBotService->storeBaleBot($storeBaleBotData);

        return $response->successfulRoute(route('bale_bots.index'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $response = $this->baleBotService->getBaleBot($this->blogId(), $id)->abortUnSuccessful();

        return view('bale_bot.edit', [
            'baleBot' => $response->getData('baleBot'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $updateBaleBotData = new UpdateBaleBotData(
            $id,
            $this->blogId(),
            $request->bale_token,
            $request->bale_bot_status,
            $request->user_service,
            $request->notify_admin_on_invoice,
            $request->admin
        );

        $response = $this->baleBotService->updateBaleBot($updateBaleBotData);

        return $response->successfulRoute(route('bale_bots.index'));
    }

    public function destroy(Request $request, int $id)
    {
        return $this->baleBotService->destroyBaleBot($this->blogId(), $id);
    }

    public function upload(Request $request, int $id)
    {
        $uploadBaleBotData = new UploadBaleBotData(
            $id,
            $this->blogId(),
            $request->attribute_name
        );

        $response = $this->baleBotService->uploadBaleBot($uploadBaleBotData);

        return $response->successfulRoute(route('bale_bots.index'));
    }
}
