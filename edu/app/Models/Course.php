<?php
namespace App\Models;
use App\Models\CourseSession;
use Illuminate\Database\Eloquent\Model;

class Course extends Model {
    protected $table = 'COURSE';
    protected $primaryKey = 'course_id';
    public $timestamps = false; // Jika table tiada created_at/updated_at

    public function sessions() {
        return $this->hasMany(CourseSession::class, 'course_id');
    }
}