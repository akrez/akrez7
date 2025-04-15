<?php

namespace App\Services;

use App\Support\ApiResponse;

abstract class Service
{
    abstract public function getApiResource(int $blogId, int $id): ApiResponse;

    abstract public function getApiCollection(int $blogId): ApiResponse;

    abstract protected function getLatestBaseQuery($blogId): \Illuminate\Database\Eloquent\Builder;

    protected function getLatestApiQuery($blogId)
    {
        return $this->getLatestBaseQuery($blogId);
    }

    protected function getLatestBlogQuery($blogId)
    {
        return $this->getLatestBaseQuery($blogId);
    }
}
