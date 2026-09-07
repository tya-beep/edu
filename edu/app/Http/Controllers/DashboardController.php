<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Teacher;
use App\Models\Staff;
use App\Models\Principal;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ============ BASIC STATS - ACTIVE ONLY ============
        
        // Count ACTIVE teachers (those with 'Aktif' status in assign table)
        $totalTeachers = DB::table('assign')
            ->where('status', 'Aktif')
            ->distinct('teacherID')
            ->count('teacherID');
        
        // Count ACTIVE staff (status field in staff table)
        $totalStaff = Staff::where('status', 'active')->count();
        
        // Count ACTIVE principals (status field in principal table)
        $totalPrincipals = Principal::where('status', 'Aktif')->count();
        
        // Total schools (all schools)
        $totalSchools = School::count();
        
        // Vacancies (school vacancy count)
        $vacancies = School::sum('vacancy');
        
        // ============ TEACHER ASSIGNMENT STATS ============
        
        // Teachers currently assigned and active
        $assignedTeachers = DB::table('assign')
            ->where('status', 'Aktif')
            ->count();
        
        // Teachers who have stopped (Berhenti)
        $totalStopTeachers = DB::table('assign')
            ->where('status', 'Berhenti')
            ->count();
        
        // ============ RESIGNATION STATISTICS ============
        
        // Get teacher IDs that are active in assign table
        $activeTeacherIds = DB::table('assign')
            ->where('status', 'Aktif')
            ->pluck('teacherID')
            ->toArray();
        
        // Teacher Resignations (only from active teachers)
        $teacherResignations = [
            'pending' => Teacher::whereIn('teacherID', $activeTeacherIds)
                ->where('resignation_request_status', 'pending')
                ->count(),
            'approved' => Teacher::whereIn('teacherID', $activeTeacherIds)
                ->where('resignation_request_status', 'approved')
                ->count(),
            'rejected' => Teacher::whereIn('teacherID', $activeTeacherIds)
                ->where('resignation_request_status', 'rejected')
                ->count(),
            'total' => Teacher::whereIn('teacherID', $activeTeacherIds)
                ->whereNotNull('resignation_request_status')
                ->count(),
        ];
        
        // Staff Resignations (only from active staff)
        $staffResignations = [
            'pending' => Staff::where('status', 'Aktif')
                ->where('resignation_request_status', 'pending')
                ->count(),
            'approved' => Staff::where('status', 'Aktif')
                ->where('resignation_request_status', 'approved')
                ->count(),
            'rejected' => Staff::where('status', 'Aktif')
                ->where('resignation_request_status', 'rejected')
                ->count(),
            'total' => Staff::where('status', 'Aktif')
                ->whereNotNull('resignation_request_status')
                ->count(),
        ];
        
        // Principal Resignations (only from active principals)
        $principalResignations = [
            'pending' => Principal::where('status', 'Aktif')
                ->where('resignation_request_status', 'pending')
                ->count(),
            'approved' => Principal::where('status', 'Aktif')
                ->where('resignation_request_status', 'approved')
                ->count(),
            'rejected' => Principal::where('status', 'Aktif')
                ->where('resignation_request_status', 'rejected')
                ->count(),
            'total' => Principal::where('status', 'Aktif')
                ->whereNotNull('resignation_request_status')
                ->count(),
        ];
        
        // Total pending resignations across all types (active only)
        $totalPendingResignations = $teacherResignations['pending'] + 
                                   $staffResignations['pending'] + 
                                   $principalResignations['pending'];
        
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
        
        // ============ RECENT RESIGNATIONS ============
        
        // Recent teacher resignations (active only)
        $recentTeachers = Teacher::select(
                'teacherID as id', 
                'teacherName as name', 
                DB::raw("'teacher' as type"), 
                'resignation_request_date as request_date', 
                'resignation_request_status as status'
            )
            ->whereIn('teacherID', $activeTeacherIds)
            ->whereNotNull('resignation_request_date')
            ->orderBy('resignation_request_date', 'desc')
            ->limit(5)
            ->get();
        
        // Recent staff resignations (active only)
        $recentStaff = Staff::select(
                'staffID as id', 
                'staffName as name', 
                DB::raw("'staff' as type"), 
                'resignation_request_date as request_date', 
                'resignation_request_status as status'
            )
            ->where('status', 'Aktif')
            ->whereNotNull('resignation_request_date')
            ->orderBy('resignation_request_date', 'desc')
            ->limit(5)
            ->get();
        
        // Recent principal resignations (active only)
        $recentPrincipals = Principal::select(
                'principalID as id', 
                'principalName as name', 
                DB::raw("'principal' as type"), 
                'resignation_request_date as request_date', 
                'resignation_request_status as status'
            )
            ->where('status', 'Aktif')
            ->whereNotNull('resignation_request_date')
            ->orderBy('resignation_request_date', 'desc')
            ->limit(5)
            ->get();
        
        $recentResignations = $recentTeachers->concat($recentStaff)->concat($recentPrincipals)
            ->sortByDesc('request_date')
            ->take(5);
        
        // ============ CALCULATED METRICS ============
        
        $totalPositions = $assignedTeachers + $vacancies;
        $occupancyRate = $totalPositions > 0 ? round(($assignedTeachers / $totalPositions) * 100) : 0;
        
        // ============ SCHOOLS DATA ============
        
        $schools = School::select('schoolID', 'schoolName', 'totalTeacher', 'vacancy')
            ->orderBy('schoolName')
            ->get();
        
        // ============ LATEST ASSIGNMENT ============
        
        $latestAssign = DB::table('assign')
            ->join('Teacher', 'assign.teacherID', '=', 'Teacher.teacherID')
            ->join('school', 'assign.schoolID', '=', 'school.schoolID')
            ->select('Teacher.teacherName', 'school.schoolName', 'assign.assignDate')
            ->latest('assign.assignDate')
            ->first();
        
        // ============ RETIRING SOON (Active Teachers Only) ============
        // NEW: Combined retirement watch for all types
        
        // Teachers retiring soon (active only)
        $retiringTeachers = Teacher::whereIn('teacherID', $activeTeacherIds)
            ->whereNotNull('pensionDate')
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
        
        // Principals retiring soon (active only)
        $retiringPrincipals = Principal::where('status', 'Aktif')
            ->whereNotNull('pensionDate')
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
        
        // Staff retiring soon (active only)
        $retiringStaff = Staff::where('status', 'Aktif')
            ->whereNotNull('pensionDate')
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
        
        // Combine all retiring soon
        $retiringSoon = $retiringTeachers->concat($retiringPrincipals)->concat($retiringStaff)
            ->sortBy('pensionDate');
        
        // ============ ADDITIONAL METRICS ============
        
        // Teachers about to retire this month (active only)
        $retiringThisMonth = Teacher::whereIn('teacherID', $activeTeacherIds)
            ->whereNotNull('pensionDate')
            ->whereMonth('pensionDate', now()->month)
            ->whereYear('pensionDate', now()->year)
            ->count();
        
        // Principals about to retire this month
        $retiringPrincipalsThisMonth = Principal::where('status', 'Aktif')
            ->whereNotNull('pensionDate')
            ->whereMonth('pensionDate', now()->month)
            ->whereYear('pensionDate', now()->year)
            ->count();
        
        // Staff about to retire this month
        $retiringStaffThisMonth = Staff::where('status', 'Aktif')
            ->whereNotNull('pensionDate')
            ->whereMonth('pensionDate', now()->month)
            ->whereYear('pensionDate', now()->year)
            ->count();
        
        // Total retiring this month
        $totalRetiringThisMonth = $retiringThisMonth + $retiringPrincipalsThisMonth + $retiringStaffThisMonth;
        
        // Teachers retired (those with 'Berhenti' status in assign table)
        $retiredTeacherIds = DB::table('assign')
            ->where('status', 'Berhenti')
            ->pluck('teacherID')
            ->toArray();
        $totalRetiredTeachers = count($retiredTeacherIds);
        
        // Principals retired
        $totalRetiredPrincipals = Principal::where('status', 'Berhenti')->count();
        
        // Staff retired
        $totalRetiredStaff = Staff::where('status', 'Berhenti')->count();
        
        // ============ RETURN VIEW ============
        
        return view('hr.dashboard', compact(
            'totalSchools',
            'totalTeachers',
            'totalStaff',
            'totalPrincipals',
            'vacancies',
            'assignedTeachers',
            'totalStopTeachers',
            'occupancyRate',
            'schools',
            'latestAssign',
            'retiringSoon',
            'teacherResignations',
            'staffResignations',
            'principalResignations',
            'totalPendingResignations',
            'recentResignations',
            'retiringThisMonth',
            'totalRetiredTeachers',
            'totalRetiredPrincipals',
            'totalRetiredStaff',
            'schoolsWithoutPrincipal',
            'totalSchoolsWithoutPrincipal',
            'retiringPrincipalsThisMonth',
            'retiringStaffThisMonth',
            'totalRetiringThisMonth'
        ));
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