<?php

namespace App\Http\Controllers;

use App\Data\TelegramBot\StoreTelegramBotData;
use App\Data\TelegramBot\UpdateTelegramBotData;
use App\Data\TelegramBot\UploadTelegramBotData;
use App\Services\TelegramBotService;
use Illuminate\Http\Request;

class TelegramBotController extends Controller
{
    public function __construct(protected TelegramBotService $telegramBotService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $response = $this->telegramBotService->getLatestTelegramBots($this->blogId());

        return view('telegram_bot.index', [
            'telegramBots' => $response->getData('telegramBots'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('telegram_bot.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $storeTelegramBotData = new StoreTelegramBotData(
            null,
            $this->blogId(),
            $request->telegram_token
        );

        $response = $this->telegramBotService->storeTelegramBot($storeTelegramBotData);

        return $response->successfulRoute(route('telegram_bots.index'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $response = $this->telegramBotService->getTelegramBot($this->blogId(), $id)->abortUnSuccessful();

        return view('telegram_bot.edit', [
            'telegramBot' => $response->getData('telegramBot'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $updateTelegramBotData = new UpdateTelegramBotData(
            $id,
            $this->blogId(),
            $request->telegram_token
        );

        $response = $this->telegramBotService->updateTelegramBot($updateTelegramBotData);

        return $response->successfulRoute(route('telegram_bots.index'));
    }

    public function destroy(Request $request, int $id)
    {
        return $this->telegramBotService->destroyTelegramBot($this->blogId(), $id);
    }

    public function upload(Request $request, int $id)
    {
        $uploadTelegramBotData = new UploadTelegramBotData(
            $id,
            $this->blogId(),
            $request->attribute_name
        );

        $response = $this->telegramBotService->uploadTelegramBot($uploadTelegramBotData);

        return $response->successfulRoute(route('telegram_bots.index'));
    }
}
