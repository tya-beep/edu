<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSession extends Model {
    protected $table = 'COURSE_SESSION';
    protected $primaryKey = 'session_id';
    public $timestamps = false;

    public function course() {
        return $this->belongsTo(Course::class, 'course_id');
    }
}