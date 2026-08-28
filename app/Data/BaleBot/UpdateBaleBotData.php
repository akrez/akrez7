<?php

namespace App\Data\BaleBot;

class UpdateBaleBotData extends BaleBotData
{
    public function rules($context)
    {
        return [
            'id' => ['required', 'integer'],
        ] + parent::rules($context);
    }
}
