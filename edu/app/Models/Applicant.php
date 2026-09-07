<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    protected $table = 'applicant';
    protected $primaryKey = 'applicant_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; // Disable automatic timestamps
    
    protected $fillable = [
        'applicant_id', 'full_name', 'email', 'password', 'phone_number'
    ];
    
    public function applications()
    {
        return $this->hasMany(Application::class, 'applicant_id', 'applicant_id');
    }
}