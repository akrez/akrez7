<?php

namespace App\Data\TelegramBot;

class UpdateTelegramBotData extends TelegramBotData
{
    public function rules($context)
    {
        return [
            'id' => ['required', 'integer'],
        ] + parent::rules($context);
    }
}
