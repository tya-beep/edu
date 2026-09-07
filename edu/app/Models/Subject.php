<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'SUBJECT';
    protected $primaryKey = 'subjectID';

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'subjectID',
        'subjectName',
        'subjectDescription'
    ];
}