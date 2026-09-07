<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $table = 'application';
    protected $primaryKey = 'application_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; // Disable automatic timestamps
    
    protected $fillable = [
        'application_id', 'ic_number', 'date_of_birth', 'place_of_birth',
        'applicant_image', 'gender', 'nationality', 'religion', 'race',
        'address', 'marital_status', 'serious_illness', 'disability',
        'total_children', 'sp_name', 'sp_job', 'sp_employer', 'sp_emp_phone',
        'sp_emp_address', 'interest', 'islamic_involvement', 'community_involvement',
        'quran_level', 'reason_apply', 'expected_salary', 'driving_license',
        'vehicle_type', 'application_status', 'offer_response', 'application_date',
        'declaration', 'applicant_id', 'school_id', 'hr_id'
    ];
    
    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id', 'applicant_id');
    }
}