<?php

namespace App\Services;

use App\Support\ApiResponse;
use App\Support\Arr;
use Illuminate\Support\Facades\File;

class DomainService
{
    public static function new()
    {
        return app(self::class);
    }

    public function getDomainsArray(): ApiResponse
    {
        return ApiResponse::new()->data([
            'domains' => array_keys($this->getDomainsToBlogIdsArray()),
        ]);
    }

    public function blogIdToDomains($blogId): ApiResponse
    {
        if (empty($blogId)) {
            return ApiResponse::new(404);
        }

        $domainsToBlogIdsArray = $this->getDomainsToBlogIdsArray();

        $domains = array_keys($domainsToBlogIdsArray, $blogId);

        return ApiResponse::new($domains ? 200 : 404)->data([
            'domains' => $domains,
        ]);
    }

    public function domainToBlogId($domain): ApiResponse
    {
        if (empty($domain)) {
            return ApiResponse::new(404);
        }

        $domainsToBlogIdsArray = $this->getDomainsToBlogIdsArray();

        $blogId = Arr::get($domainsToBlogIdsArray, $domain);

        return ApiResponse::new($blogId ? 200 : 404)->data([
            'blog_id' => $blogId,
        ]);
    }

    protected function getDomainFilePath()
    {
        return storage_path('domains.json');
    }

    protected function getDomainsToBlogIdsArray()
    {
        $filePath = $this->getDomainFilePath();

        if (! File::exists($filePath)) {
            File::put($filePath, json_encode([]));
        }

        return (array) File::json($filePath);
    }
}
