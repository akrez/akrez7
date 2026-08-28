<?php

namespace App\Data\BaleMessage;

use App\Data\Data;
use App\Rules\BaleTokenRule;

class StoreBaleMessageData extends Data
{
    public function __construct(
        public $blog_id,
        public $bale_token,
        public $content_json
    ) {}

    public function rules($context)
    {
        return [
            'blog_id' => ['required', 'integer'],
            'bale_token' => ['required', 'max:64', new BaleTokenRule],
            'content_json' => ['required'],
        ];
    }
}
