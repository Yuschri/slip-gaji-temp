<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SysUser extends Authenticatable
{
    use Notifiable;

    protected $connection = 'sqlsrv_second';
    protected $table = 'SYS_USER';

    protected $primaryKey = 'USER_ID';

    protected $fillable = [
        'USER_NAME',
        'USER_PASSWORD',
    ];

    protected $hidden = [
        'USER_PASSWORD',
    ];

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->USER_PASSWORD;
    }

    public function userRoles()
    {
        return $this->hasMany(UserRole::class, 'user_id', 'USER_ID');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'm_user_role', 'user_id', 'role_id');
    }
}
