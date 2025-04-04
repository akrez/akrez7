<?php

namespace App\Services;

use App\Support\ApiResponse;

abstract class Service
{
    abstract public function getApiResource(int $blogId): ApiResponse;

    abstract public function getApiCollection(int $blogId): ApiResponse;

    abstract protected function getLatestBaseQuery($blogId);

    protected function getLatestApiQuery($blogId)
    {
        return $this->getLatestBaseQuery($blogId);
    }

    protected function getLatestBlogQuery($blogId)
    {
        return $this->getLatestBaseQuery($blogId);
    }
}
