<?php

namespace App\Services;

use App\Support\Arr;
use Illuminate\Support\Facades\File;

class DomainService
{
    public static function new()
    {
        return app(self::class);
    }

    public function getFilePath()
    {
        return storage_path('domains.json');
    }

    public function getDomainsToBlogIdsArray()
    {
        $filePath = $this->getFilePath();

        if (! File::exists($filePath)) {
            return [];
        }

        return (array) File::json($filePath);
    }

    public function getDomains()
    {
        return array_keys($this->getDomainsToBlogIdsArray());
    }

    public function domainToBlogId($domain)
    {
        $domainsToBlogIdsArray = $this->getDomainsToBlogIdsArray();

        $blogId = Arr::get($domainsToBlogIdsArray, $domain);
        if ($blogId) {
            return $blogId;
        }

        return null;
    }
}
