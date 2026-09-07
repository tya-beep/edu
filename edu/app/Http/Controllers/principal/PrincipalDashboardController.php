<?php

namespace App\Http\Controllers\principal;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Teacher;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PrincipalDashboardController extends Controller
{
    public function index(): View
    {
        // Check if user is logged in
        if (!Session::has('user')) {
            abort(403, 'Unauthorized access. Please login first.');
        }
        
        $principal = Session::get('user');
        
        // Verify user is a principal
        if (!isset($principal->role) || strtolower($principal->role) !== 'principal') {
            abort(403, 'Unauthorized access.');
        }
        
        // Get school information
        $school = School::find($principal->schoolID);
        
        // Get teacher statistics from ASSIGN table
        $totalTeachers = Teacher::join('ASSIGN', 'Teacher.teacherID', '=', 'ASSIGN.teacherID')
                                ->where('ASSIGN.schoolID', $principal->schoolID)
                                ->count();
        
        $activeTeachers = Teacher::join('ASSIGN', 'Teacher.teacherID', '=', 'ASSIGN.teacherID')
                                 ->where('ASSIGN.schoolID', $principal->schoolID)
                                 ->where(function($q) {
                                     $q->where('ASSIGN.status', 'Aktif')
                                       ->orWhereNull('ASSIGN.status');
                                 })
                                 ->count();
        
        $resignedTeachers = Teacher::join('ASSIGN', 'Teacher.teacherID', '=', 'ASSIGN.teacherID')
                                   ->where('ASSIGN.schoolID', $principal->schoolID)
                                   ->where('ASSIGN.status', 'Berhenti')
                                   ->count();
        
        // School Capacity Information - Use vacancy from school table directly
        $schoolCapacity = $school->capacity ?? 0;
        $vacancy = $school->vacancy ?? 0;
        $totalTeacherFromSchool = $school->totalTeacher ?? 0;
        
        // Calculate capacity percentage
        $capacityPercentage = $schoolCapacity > 0 ? round(($totalTeacherFromSchool / $schoolCapacity) * 100) : 0;
        
        // Gender statistics - ONLY for ACTIVE teachers
        $maleTeachers = Teacher::join('ASSIGN', 'Teacher.teacherID', '=', 'ASSIGN.teacherID')
                               ->where('ASSIGN.schoolID', $principal->schoolID)
                               ->where(function($q) {
                                   $q->where('ASSIGN.status', 'Aktif')
                                     ->orWhereNull('ASSIGN.status');
                               })
                               ->where(function($q) {
                                   $q->where('Teacher.gender', 'Male')
                                     ->orWhere('Teacher.gender', 'Lelaki')
                                     ->orWhere('Teacher.gender', 'male')
                                     ->orWhere('Teacher.gender', 'lelaki');
                               })
                               ->count();
        
        $femaleTeachers = Teacher::join('ASSIGN', 'Teacher.teacherID', '=', 'ASSIGN.teacherID')
                                 ->where('ASSIGN.schoolID', $principal->schoolID)
                                 ->where(function($q) {
                                     $q->where('ASSIGN.status', 'Aktif')
                                       ->orWhereNull('ASSIGN.status');
                                 })
                                 ->where(function($q) {
                                     $q->where('Teacher.gender', 'Female')
                                       ->orWhere('Teacher.gender', 'Perempuan')
                                       ->orWhere('Teacher.gender', 'female')
                                       ->orWhere('Teacher.gender', 'perempuan');
                                 })
                                 ->count();
        
        // If no gender data found, try to estimate from IC numbers for active teachers
        if ($maleTeachers == 0 && $femaleTeachers == 0 && $activeTeachers > 0) {
            $teachers = Teacher::join('ASSIGN', 'Teacher.teacherID', '=', 'ASSIGN.teacherID')
                              ->where('ASSIGN.schoolID', $principal->schoolID)
                              ->where(function($q) {
                                  $q->where('ASSIGN.status', 'Aktif')
                                    ->orWhereNull('ASSIGN.status');
                              })
                              ->select('Teacher.ICNumber')
                              ->get();
            
            foreach ($teachers as $teacher) {
                if (!empty($teacher->ICNumber)) {
                    $icNumber = preg_replace('/[^0-9]/', '', $teacher->ICNumber);
                    if (strlen($icNumber) >= 1) {
                        $lastDigit = substr($icNumber, -1);
                        if (is_numeric($lastDigit)) {
                            if ($lastDigit % 2 == 1) {
                                $maleTeachers++;
                            } else {
                                $femaleTeachers++;
                            }
                        }
                    }
                }
            }
        }
        
        // ==========================================
        // FIX: Recent resigned teachers ordered by pension date (latest first)
        // ==========================================
        $recentResigned = Teacher::join('ASSIGN', 'Teacher.teacherID', '=', 'ASSIGN.teacherID')
                                 ->where('ASSIGN.schoolID', $principal->schoolID)
                                 ->where('ASSIGN.status', 'Berhenti')
                                 ->whereNotNull('Teacher.pensionDate')
                                 ->orderBy('Teacher.pensionDate', 'desc')
                                 ->limit(5)
                                 ->select(
                                     'Teacher.teacherID', 
                                     'Teacher.teacherName', 
                                     'Teacher.pensionDate as resigned_date'
                                 )
                                 ->get();
        
        // Recent active teachers (last 5)
        $recentTeachers = Teacher::join('ASSIGN', 'Teacher.teacherID', '=', 'ASSIGN.teacherID')
                                 ->where('ASSIGN.schoolID', $principal->schoolID)
                                 ->where(function($q) {
                                     $q->where('ASSIGN.status', 'Aktif')
                                       ->orWhereNull('ASSIGN.status');
                                 })
                                 ->orderBy('ASSIGN.assignDate', 'desc')
                                 ->limit(5)
                                 ->select('Teacher.teacherID', 'Teacher.teacherName', 'ASSIGN.assignDate')
                                 ->get();
        
        // ==========================================
        // FIX: Monthly resignation data based on PENSION DATE
        // ==========================================
        $monthlyResignations = [];
        
        // Get the last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthName = $month->format('M Y');
            $year = $month->year;
            $monthNum = $month->month;
            
            // Count teachers with pension date in this month
            $count = Teacher::join('ASSIGN', 'Teacher.teacherID', '=', 'ASSIGN.teacherID')
                           ->where('ASSIGN.schoolID', $principal->schoolID)
                           ->where('ASSIGN.status', 'Berhenti')
                           ->whereNotNull('Teacher.pensionDate')
                           ->whereYear('Teacher.pensionDate', $year)
                           ->whereMonth('Teacher.pensionDate', $monthNum)
                           ->count();
            
            $monthlyResignations[] = [
                'month' => $monthName,
                'count' => $count,
                'year' => $year,
                'month_num' => $monthNum
            ];
        }
        
        // Calculate percentages
        $activePercentage = $totalTeachers > 0 ? round(($activeTeachers / $totalTeachers) * 100) : 0;
        $resignedPercentage = $totalTeachers > 0 ? round(($resignedTeachers / $totalTeachers) * 100) : 0;
        
        return view('principal.dashboard', compact(
            'principal',
            'school',
            'totalTeachers',
            'activeTeachers',
            'resignedTeachers',
            'maleTeachers',
            'femaleTeachers',
            'activePercentage',
            'resignedPercentage',
            'recentTeachers',
            'recentResigned',
            'monthlyResignations',
            'schoolCapacity',
            'vacancy',
            'capacityPercentage',
            'totalTeacherFromSchool'
        ));
    }
}