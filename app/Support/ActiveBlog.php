<?php

namespace App\Support;

use App\Models\Blog;
use App\Models\User;
use App\Services\BlogService;

class ActiveBlog
{
    protected ?Blog $blog;

    public function __construct(?User $user = null)
    {
        $this->set($user);
    }

    public function set(?User $user): ?Blog
    {
        return $this->blog = $this->find($user);
    }

    public function find(?User $user): ?Blog
    {
        if (! $user) {
            return null;
        }

        if (! $user->active_blog) {
            return null;
        }

        return BlogService::new()->getUserBlog($user->id, $user->active_blog)->getData('blog');
    }

    public function get(): ?Blog
    {
        return $this->blog;
    }

    public function has(): bool
    {
        return $this->get() !== null;
    }

    public function attr(string $attribute): mixed
    {
        return $this->get() ? $this->get()->$attribute : null;
    }

    public function name(): ?string
    {
        return $this->attr('name');
    }
}
