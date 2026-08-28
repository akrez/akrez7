<?php

namespace App\Http\Controllers\Api;

use App\Data\BaleMessage\StoreBaleMessageData;
use App\Http\Controllers\Controller;
use App\Services\BaleMessageService;

class BaleMessageController extends Controller
{
    public function webhook(int $blog_id, string $bale_token)
    {
        $storeBaleMessageData = new StoreBaleMessageData(
            $blog_id,
            $bale_token,
            request()->getContent()
        );

        return BaleMessageService::new()->webhook($storeBaleMessageData);
    }
}
