<?php

namespace App\Repositories;

use App\Models\SysUser;

class UserRepository
{
    public function findByUsername(string $username)
    {
        return SysUser::where('USER_NAME', $username)
            ->with('roles')
            ->first();
    }
}
