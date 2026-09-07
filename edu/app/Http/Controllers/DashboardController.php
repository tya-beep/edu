<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Teacher;
use App\Models\Staff;
use App\Models\Principal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // <-- ADD THIS LINE

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // ============ BASIC STATS ============
            
            // Total schools
            $totalSchools = School::count();
            
            // Total teachers (from teacher table)
            $totalTeachers = Teacher::count();
            
            // Total staff
            $totalStaff = Staff::count();
            
            // Total principals
            $totalPrincipals = Principal::count();
            
            // Vacancies (school vacancy count)
            $vacancies = School::sum('vacancy');
            
            // ============ SCHOOLS WITHOUT PRINCIPAL ============
            // Get schools that don't have an active principal assigned
            $schoolsWithoutPrincipal = School::leftJoin('principal', 'school.schoolID', '=', 'principal.schoolID')
                ->where(function($query) {
                    $query->whereNull('principal.principalID')
                        ->orWhere('principal.status', '!=', 'Aktif');
                })
                ->select(
                    'school.schoolID', 
                    'school.schoolName', 
                    'school.totalTeacher', 
                    'school.vacancy'
                )
                ->orderBy('school.schoolName')
                ->get();
            
            $totalSchoolsWithoutPrincipal = $schoolsWithoutPrincipal->count();
            
            // ============ SCHOOLS DATA ============
            $schools = School::select('schoolID', 'schoolName', 'totalTeacher', 'vacancy')
                ->orderBy('schoolName')
                ->get();
            
            // ============ LATEST ASSIGNMENT ============
            // Check if assign table exists first
            $latestAssign = null;
            try {
                $latestAssign = DB::table('assign')
                    ->join('teacher', 'assign.teacherID', '=', 'teacher.teacherID')
                    ->join('school', 'assign.schoolID', '=', 'school.schoolID')
                    ->select('teacher.teacherName', 'school.schoolName', 'assign.assignDate')
                    ->latest('assign.assignDate')
                    ->first();
            } catch (\Exception $e) {
                // Assign table might not exist or have different structure
                $latestAssign = null;
            }
            
            // ============ RESIGNATION STATISTICS ============
            // Check if resignation columns exist
            $teacherResignations = [
                'pending' => 0,
                'approved' => 0,
                'rejected' => 0,
                'total' => 0,
            ];
            
            $staffResignations = [
                'pending' => 0,
                'approved' => 0,
                'rejected' => 0,
                'total' => 0,
            ];
            
            $principalResignations = [
                'pending' => 0,
                'approved' => 0,
                'rejected' => 0,
                'total' => 0,
            ];
            
            try {
                // Teacher Resignations
                $teacherResignations = [
                    'pending' => Teacher::where('resignation_request_status', 'pending')->count(),
                    'approved' => Teacher::where('resignation_request_status', 'approved')->count(),
                    'rejected' => Teacher::where('resignation_request_status', 'rejected')->count(),
                    'total' => Teacher::whereNotNull('resignation_request_status')->count(),
                ];
            } catch (\Exception $e) {
                // Column might not exist
            }
            
            try {
                // Staff Resignations
                $staffResignations = [
                    'pending' => Staff::where('resignation_request_status', 'pending')->count(),
                    'approved' => Staff::where('resignation_request_status', 'approved')->count(),
                    'rejected' => Staff::where('resignation_request_status', 'rejected')->count(),
                    'total' => Staff::whereNotNull('resignation_request_status')->count(),
                ];
            } catch (\Exception $e) {
                // Column might not exist
            }
            
            try {
                // Principal Resignations
                $principalResignations = [
                    'pending' => Principal::where('resignation_request_status', 'pending')->count(),
                    'approved' => Principal::where('resignation_request_status', 'approved')->count(),
                    'rejected' => Principal::where('resignation_request_status', 'rejected')->count(),
                    'total' => Principal::whereNotNull('resignation_request_status')->count(),
                ];
            } catch (\Exception $e) {
                // Column might not exist
            }
            
            $totalPendingResignations = $teacherResignations['pending'] + 
                                       $staffResignations['pending'] + 
                                       $principalResignations['pending'];
            
            // ============ RECENT RESIGNATIONS ============
            $recentResignations = collect();
            
            // Try to get recent resignations if columns exist
            try {
                $recentTeachers = Teacher::select(
                        'teacherID as id', 
                        'teacherName as name', 
                        DB::raw("'teacher' as type"), 
                        'resignation_request_date as request_date', 
                        'resignation_request_status as status'
                    )
                    ->whereNotNull('resignation_request_date')
                    ->orderBy('resignation_request_date', 'desc')
                    ->limit(5)
                    ->get();
                
                $recentResignations = $recentResignations->concat($recentTeachers);
            } catch (\Exception $e) {
                // Column might not exist
            }
            
            try {
                $recentStaff = Staff::select(
                        'staffID as id', 
                        'staffName as name', 
                        DB::raw("'staff' as type"), 
                        'resignation_request_date as request_date', 
                        'resignation_request_status as status'
                    )
                    ->whereNotNull('resignation_request_date')
                    ->orderBy('resignation_request_date', 'desc')
                    ->limit(5)
                    ->get();
                
                $recentResignations = $recentResignations->concat($recentStaff);
            } catch (\Exception $e) {
                // Column might not exist
            }
            
            try {
                $recentPrincipals = Principal::select(
                        'principalID as id', 
                        'principalName as name', 
                        DB::raw("'principal' as type"), 
                        'resignation_request_date as request_date', 
                        'resignation_request_status as status'
                    )
                    ->whereNotNull('resignation_request_date')
                    ->orderBy('resignation_request_date', 'desc')
                    ->limit(5)
                    ->get();
                
                $recentResignations = $recentResignations->concat($recentPrincipals);
            } catch (\Exception $e) {
                // Column might not exist
            }
            
            $recentResignations = $recentResignations
                ->sortByDesc('request_date')
                ->take(5);
            
            // ============ RETIRING SOON ============
            $retiringSoon = collect();
            
            try {
                // Teachers retiring soon
                $retiringTeachers = Teacher::whereNotNull('pensionDate')
                    ->where('pensionDate', '>=', now())
                    ->where('pensionDate', '<=', now()->addMonths(6))
                    ->select(
                        'teacherID as id',
                        'teacherName as name',
                        DB::raw("'teacher' as type"),
                        'pensionDate'
                    )
                    ->orderBy('pensionDate', 'asc')
                    ->get();
                
                $retiringSoon = $retiringSoon->concat($retiringTeachers);
            } catch (\Exception $e) {
                // Column might not exist
            }
            
            try {
                // Principals retiring soon
                $retiringPrincipals = Principal::whereNotNull('pensionDate')
                    ->where('pensionDate', '>=', now())
                    ->where('pensionDate', '<=', now()->addMonths(6))
                    ->select(
                        'principalID as id',
                        'principalName as name',
                        DB::raw("'principal' as type"),
                        'pensionDate'
                    )
                    ->orderBy('pensionDate', 'asc')
                    ->get();
                
                $retiringSoon = $retiringSoon->concat($retiringPrincipals);
            } catch (\Exception $e) {
                // Column might not exist
            }
            
            try {
                // Staff retiring soon
                $retiringStaff = Staff::whereNotNull('pensionDate')
                    ->where('pensionDate', '>=', now())
                    ->where('pensionDate', '<=', now()->addMonths(6))
                    ->select(
                        'staffID as id',
                        'staffName as name',
                        DB::raw("'staff' as type"),
                        'pensionDate'
                    )
                    ->orderBy('pensionDate', 'asc')
                    ->get();
                
                $retiringSoon = $retiringSoon->concat($retiringStaff);
            } catch (\Exception $e) {
                // Column might not exist
            }
            
            $retiringSoon = $retiringSoon->sortBy('pensionDate');
            
            // ============ RETURN VIEW ============
            
            return view('hr.dashboard', compact(
                'totalSchools',
                'totalTeachers',
                'totalStaff',
                'totalPrincipals',
                'vacancies',
                'schools',
                'latestAssign',
                'retiringSoon',
                'teacherResignations',
                'staffResignations',
                'principalResignations',
                'totalPendingResignations',
                'recentResignations',
                'schoolsWithoutPrincipal',
                'totalSchoolsWithoutPrincipal'
            ));
            
        } catch (\Exception $e) {
            // Log the error
            Log::error('Dashboard error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return with default values
            return view('hr.dashboard', [
                'totalSchools' => 0,
                'totalTeachers' => 0,
                'totalStaff' => 0,
                'totalPrincipals' => 0,
                'vacancies' => 0,
                'schools' => collect(),
                'latestAssign' => null,
                'retiringSoon' => collect(),
                'teacherResignations' => ['pending' => 0, 'approved' => 0, 'rejected' => 0, 'total' => 0],
                'staffResignations' => ['pending' => 0, 'approved' => 0, 'rejected' => 0, 'total' => 0],
                'principalResignations' => ['pending' => 0, 'approved' => 0, 'rejected' => 0, 'total' => 0],
                'totalPendingResignations' => 0,
                'recentResignations' => collect(),
                'schoolsWithoutPrincipal' => collect(),
                'totalSchoolsWithoutPrincipal' => 0,
                'error' => $e->getMessage()
            ]);
        }
    }

    // Navigation Methods
    public function school() 
    { 
        return view('school.school'); 
    }
    
    public function teacher() 
    { 
        return view('teacher.teacher'); 
    }
    
    public function placement() 
    { 
        return view('placement.placement'); 
    }
    
    public function placementRecords() 
    { 
        return view('placement.records'); 
    }
    
    public function offer() 
    { 
        return view('offer.offer'); 
    }
    
    public function letter() 
    { 
        return view('letter.letter'); 
    }
}