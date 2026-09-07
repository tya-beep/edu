<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'document';
    protected $primaryKey = 'documentID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = [
        'documentID', 'gn_id', 'applicant_id', 'documentType', 
        'documentDescription', 'documentTitle', 'dateIssued', 
        'signedBy', 'hrid', 'teacherID', 'pdfFile', 'filePath', 'status'
    ];
    
    // Relationship with Applicant
    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id', 'applicant_id');
    }
    
    // Relationship with GuruNew
    public function guruNew()
    {
        return $this->belongsTo(GuruNew::class, 'gn_id', 'gn_id');
    }
    
    // Relationship with Teacher
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacherID', 'teacherID');
    }
    
    // Accessor to get full PDF URL
    public function getPdfUrlAttribute()
    {
        if ($this->filePath) {
            return asset('storage/' . $this->filePath);
        }
        return null;
    }
    
    // Check if PDF exists
    public function hasPdf()
    {
        return $this->pdfFile !== null || $this->filePath !== null;
    }
}