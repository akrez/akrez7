<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class ResourceCollection extends \Illuminate\Http\Resources\Json\ResourceCollection
{
    public function toArr(?Request $request = null)
    {
        return (array) @json_decode(json_encode($this->toArray($request ?? request())), true);
    }
}
