<?php

namespace App\Services;

use App\Models\User;
use App\Support\WebResponse;

class UserService
{
    public static function new()
    {
        return app(self::class);
    }

    public function setActiveBlog(User $user, int $blogId)
    {
        $user->active_blog = $blogId;

        return WebResponse::new($user->save() ? 200 : 500);
    }

    public function getSuperAdminRoleName()
    {
        $superAdminRole = config('permission.super_admin_role_name');
        abort_unless($superAdminRole, 500, 'set SUPER_ADMIN_ROLE_NAME in env');

        return $superAdminRole;
    }
}
