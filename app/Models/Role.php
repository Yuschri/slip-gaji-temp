<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $connection = 'sqlsrv_second';
    protected $table = 'm_role';

    public function users()
    {
        return $this->belongsToMany(SysUser::class, 'm_user_role', 'role_id', 'user_id');
    }
}
