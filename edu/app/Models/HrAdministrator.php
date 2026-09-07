<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class HrAdministrator extends Authenticatable
{
    protected $table = 'hr_administrator';
    protected $primaryKey = 'hrid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'hrid',
        'username',
        'email',
        'password',
        'role',
        'password_change_required',
        'phone_number'
    ];

    protected $hidden = ['password'];
}