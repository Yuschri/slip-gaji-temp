<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $connection = 'sqlsrv_second';
    protected $table = 'm_user_role';

    public function user()
    {
        return $this->belongsTo(SysUser::class, 'user_id', 'USER_ID');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
}
