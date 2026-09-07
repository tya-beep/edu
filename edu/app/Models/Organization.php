<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $table = 'organizations';
    protected $primaryKey = 'OrganizationID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'OrganizationID',
        'OrganizationName',
        'OrganizationAddress',
        'RegisterDate',
        'PhoneNumber'
    ];

    // Relationship to schools
    public function schools()
    {
        return $this->hasMany(School::class, 'organizationID', 'OrganizationID');
    }

    // Accessor for school count
    public function getSchoolCountAttribute()
    {
        return $this->schools()->count();
    }
}