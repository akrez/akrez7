<?php

namespace App\Services;

class UserService
{
    public static function new()
    {
        return app(self::class);
    }

    public function getSuperAdminRoleName()
    {
        $superAdminRole = config('permission.super_admin_role_name');
        abort_unless($superAdminRole, 500, 'set SUPER_ADMIN_ROLE_NAME in env');

        return $superAdminRole;
    }
}
