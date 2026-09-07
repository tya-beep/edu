<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\Staff;
use App\Models\Principal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PensionController extends Controller
{
    /**
     * Display pension dashboard for HR with filtering
     */
    public function dashboard(Request $request)
    {
        // Get filter parameters
        $search = $request->input('search');
        $typeFilter = $request->input('type', 'all');
        $daysFilter = $request->input('days', 'all');
        $statusFilter = $request->input('status', 'all');

        // Get teachers with pension dates - using a different approach to get school name
        // First, get all teachers with pension dates
        $teachersWithPension = Teacher::whereNotNull('pensionDate')
            ->where('pensionDate', '>', now())
            ->get();
        
        // Now get the school name for each teacher from the assign table
        $teachers = collect();
        foreach ($teachersWithPension as $teacher) {
            // Get the assignment for this teacher
            $assignment = DB::table('assign')
                ->where('teacherID', $teacher->teacherID)
                ->first();
            
            $schoolName = null;
            if ($assignment) {
                $school = DB::table('school')
                    ->where('schoolID', $assignment->schoolID)
                    ->first();
                $schoolName = $school->schoolName ?? null;
            }
            
            // Create a new object with the teacher data and school name
            $teacherData = new \stdClass();
            $teacherData->id = $teacher->teacherID;
            $teacherData->name = $teacher->teacherName;
            $teacherData->type = 'teacher';
            $teacherData->schoolName = $schoolName;
            $teacherData->status = $assignment->status ?? null;
            $teacherData->pensionDate = $teacher->pensionDate;
            $teacherData->department = null;
            
            $teachers->push($teacherData);
        }

        // Get staff with pension dates
        $staffQuery = Staff::select(
            'staff.staffID as id',
            'staff.staffName as name',
            DB::raw("'staff' as type"),
            DB::raw("NULL as schoolName"),
            'staff.department',
            'staff.status',
            'staff.pensionDate'
        )
        ->whereNotNull('staff.pensionDate')
        ->where('staff.pensionDate', '>', now());

        // Get principals with pension dates
        $principalsQuery = Principal::leftJoin('school', 'principal.schoolID', '=', 'school.schoolID')
            ->select(
                'principal.principalID as id',
                'principal.principalName as name',
                DB::raw("'principal' as type"),
                'school.schoolName',
                'principal.status',
                'principal.pensionDate',
                DB::raw("NULL as department")
            )
            ->whereNotNull('principal.pensionDate')
            ->where('principal.pensionDate', '>', now());

        // Apply filters to staff and principals
        // Apply type filter
        if ($typeFilter !== 'all') {
            switch ($typeFilter) {
                case 'teacher':
                    $staffQuery->whereRaw('1 = 0');
                    $principalsQuery->whereRaw('1 = 0');
                    break;
                case 'staff':
                    $teachers = collect();
                    $principalsQuery->whereRaw('1 = 0');
                    break;
                case 'principal':
                    $teachers = collect();
                    $staffQuery->whereRaw('1 = 0');
                    break;
            }
        }

        // Apply search filter
        if ($search) {
            $searchTerm = $search;
            $teachers = $teachers->filter(function($teacher) use ($searchTerm) {
                return stripos($teacher->id, $searchTerm) !== false ||
                       stripos($teacher->name, $searchTerm) !== false ||
                       ($teacher->schoolName && stripos($teacher->schoolName, $searchTerm) !== false);
            });
            
            $staffQuery->where(function($q) use ($searchTerm) {
                $q->where('staff.staffID', 'like', "%{$searchTerm}%")
                  ->orWhere('staff.staffName', 'like', "%{$searchTerm}%")
                  ->orWhere('staff.department', 'like', "%{$searchTerm}%");
            });
            
            $principalsQuery->where(function($q) use ($searchTerm) {
                $q->where('principal.principalID', 'like', "%{$searchTerm}%")
                  ->orWhere('principal.principalName', 'like', "%{$searchTerm}%")
                  ->orWhere('school.schoolName', 'like', "%{$searchTerm}%");
            });
        }

        // Apply status filter for teachers
        if ($statusFilter !== 'all') {
            if ($statusFilter === 'active') {
                $teachers = $teachers->filter(function($teacher) {
                    return $teacher->status === 'Aktif';
                });
                $staffQuery->where(function($q) {
                    $q->where('staff.status', 'Aktif')
                      ->orWhereNull('staff.status')
                      ->orWhere('staff.status', '');
                });
                $principalsQuery->where('principal.status', 'Aktif');
            } else {
                // Resigned
                $teachers = $teachers->filter(function($teacher) {
                    return $teacher->status === 'Berhenti';
                });
                $staffQuery->where('staff.status', 'Berhenti');
                $principalsQuery->where('principal.status', 'Berhenti');
            }
        }

        // Apply days filter
        if ($daysFilter !== 'all') {
            $days = (int) $daysFilter;
            $dateThreshold = now()->addDays($days);
            
            $teachers = $teachers->filter(function($teacher) use ($dateThreshold) {
                return $teacher->pensionDate <= $dateThreshold;
            });
            
            $staffQuery->where('staff.pensionDate', '<=', $dateThreshold);
            $principalsQuery->where('principal.pensionDate', '<=', $dateThreshold);
        }

        // Get staff and principals
        $staff = $staffQuery->get();
        $principals = $principalsQuery->get();

        // Merge all employees
        $employees = $teachers->concat($staff)->concat($principals)
            ->sortBy('pensionDate');

        // Get recently retired (last 30 days)
        $recentlyRetired = $this->getRecentlyRetired();

        // Statistics
        $stats = [
            'teachers_pending' => Teacher::whereNotNull('pensionDate')
                ->where('pensionDate', '>', now())
                ->count(),
            
            'staff_pending' => Staff::whereNotNull('pensionDate')
                ->where('pensionDate', '>', now())
                ->count(),
            
            'principals_pending' => Principal::whereNotNull('pensionDate')
                ->where('pensionDate', '>', now())
                ->count(),
            
            'near_retirement' => $this->getNearRetirementCount(),
            
            'total_retired' => DB::table('assign')->where('status', 'Berhenti')->count() 
                + Staff::where('status', 'Berhenti')->count() 
                + Principal::where('status', 'Berhenti')->count(),
        ];

        // Paginate results
        $perPage = 25;
        $currentPage = request()->input('page', 1);
        $employees = $this->paginateCollection($employees, $perPage, $currentPage);

        return view('pension.dashboard', compact(
            'employees', 
            'recentlyRetired', 
            'stats'
        ));
    }

    /**
     * Paginate a collection
     */
    private function paginateCollection($collection, $perPage, $currentPage)
    {
        $items = $collection instanceof \Illuminate\Support\Collection ? $collection : collect($collection);
        $total = $items->count();
        
        $offset = ($currentPage - 1) * $perPage;
        $sliced = $items->slice($offset, $perPage)->values();
        
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $sliced,
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    /**
     * Get count of employees near retirement
     */
    private function getNearRetirementCount()
    {
        $days = 90;
        $dateThreshold = now()->addDays($days);

        $teacherCount = Teacher::whereNotNull('pensionDate')
            ->where('pensionDate', '>', now())
            ->where('pensionDate', '<=', $dateThreshold)
            ->count();

        $staffCount = Staff::whereNotNull('pensionDate')
            ->where('pensionDate', '>', now())
            ->where('pensionDate', '<=', $dateThreshold)
            ->count();

        $principalCount = Principal::whereNotNull('pensionDate')
            ->where('pensionDate', '>', now())
            ->where('pensionDate', '<=', $dateThreshold)
            ->count();

        return $teacherCount + $staffCount + $principalCount;
    }

    /**
     * Get recently retired staff
     */
    private function getRecentlyRetired()
    {
        $thirtyDaysAgo = now()->subDays(30);
        
        // Get retired teachers with school names
        $retiredTeachers = collect();
        $teachersWithPension = Teacher::whereNotNull('pensionDate')
            ->where('pensionDate', '>=', $thirtyDaysAgo)
            ->limit(20)
            ->get();
        
        foreach ($teachersWithPension as $teacher) {
            $assignment = DB::table('assign')
                ->where('teacherID', $teacher->teacherID)
                ->where('status', 'Berhenti')
                ->first();
            
            if ($assignment) {
                $school = DB::table('school')
                    ->where('schoolID', $assignment->schoolID)
                    ->first();
                
                $teacherData = new \stdClass();
                $teacherData->id = $teacher->teacherID;
                $teacherData->name = $teacher->teacherName;
                $teacherData->type = 'teacher';
                $teacherData->schoolName = $school->schoolName ?? null;
                $teacherData->department = null;
                $teacherData->pensionDate = $teacher->pensionDate;
                
                $retiredTeachers->push($teacherData);
            }
        }

        $retiredStaff = Staff::select(
            'staff.staffID as id',
            'staff.staffName as name',
            DB::raw("'staff' as type"),
            DB::raw("NULL as schoolName"),
            'staff.department',
            'staff.pensionDate'
        )
        ->where('staff.status', 'Berhenti')
        ->whereNotNull('staff.pensionDate')
        ->where('staff.pensionDate', '>=', $thirtyDaysAgo)
        ->limit(20)
        ->get();

        $retiredPrincipals = Principal::leftJoin('school', 'principal.schoolID', '=', 'school.schoolID')
            ->select(
                'principal.principalID as id',
                'principal.principalName as name',
                DB::raw("'principal' as type"),
                'school.schoolName',
                DB::raw("NULL as department"),
                'principal.pensionDate'
            )
            ->where('principal.status', 'Berhenti')
            ->whereNotNull('principal.pensionDate')
            ->where('principal.pensionDate', '>=', $thirtyDaysAgo)
            ->limit(20)
            ->get();

        return $retiredTeachers->concat($retiredStaff)->concat($retiredPrincipals)
            ->sortByDesc('pensionDate');
    }

    /**
     * Set pension date for teacher
     */
    public function setTeacherPension(Request $request, string $id)
    {
        $validated = $request->validate([
            'pensionDate' => 'required|date|after:today',
        ]);
        
        try {
            DB::beginTransaction();
            
            $teacher = Teacher::where('teacherID', $id)->first();
            if (!$teacher) {
                throw new \Exception('Teacher not found');
            }
            
            $oldPensionDate = $teacher->pensionDate;
            $teacher->pensionDate = $validated['pensionDate'];
            $teacher->save();
            
            DB::table('teacher_audit')->insert([
                'auditID' => 'PEN_' . time() . '_' . $id,
                'teacherID' => $id,
                'status' => 'Pension Set',
                'action' => 'Set Pension Date',
                'oldData' => json_encode(['pensionDate' => $oldPensionDate]),
                'newData' => json_encode(['pensionDate' => $validated['pensionDate']]),
                'actionDate' => now(),
            ]);
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Pension date set successfully for teacher');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to set pension date: ' . $e->getMessage());
        }
    }
    
    /**
     * Set pension date for staff
     */
    public function setStaffPension(Request $request, string $id)
    {
        $validated = $request->validate([
            'pensionDate' => 'required|date|after:today',
        ]);
        
        try {
            DB::beginTransaction();
            
            $staff = Staff::where('staffID', $id)->first();
            if (!$staff) {
                throw new \Exception('Staff not found');
            }
            
            $staff->pensionDate = $validated['pensionDate'];
            $staff->save();
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Pension date set successfully for staff');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to set pension date: ' . $e->getMessage());
        }
    }
    
    /**
     * Set pension date for principal
     */
    public function setPrincipalPension(Request $request, string $id)
    {
        $validated = $request->validate([
            'pensionDate' => 'required|date|after:today',
        ]);
        
        try {
            DB::beginTransaction();
            
            $principal = Principal::where('principalID', $id)->first();
            if (!$principal) {
                throw new \Exception('Principal not found');
            }
            
            $principal->pensionDate = $validated['pensionDate'];
            $principal->save();
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Pension date set successfully for principal');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to set pension date: ' . $e->getMessage());
        }
    }
    
    /**
     * Remove/cancel pension date
     */
    public function removePension(string $type, string $id)
    {
        try {
            DB::beginTransaction();
            
            switch ($type) {
                case 'teacher':
                    $teacher = Teacher::where('teacherID', $id)->first();
                    if ($teacher) {
                        $oldDate = $teacher->pensionDate;
                        $teacher->pensionDate = null;
                        $teacher->save();
                        
                        DB::table('teacher_audit')->insert([
                            'auditID' => 'REM_' . time() . '_' . $id,
                            'teacherID' => $id,
                            'status' => 'Pension Cancelled',
                            'action' => 'Cancel Pension',
                            'oldData' => json_encode(['pensionDate' => $oldDate]),
                            'newData' => json_encode(['pensionDate' => null]),
                            'actionDate' => now(),
                        ]);
                    }
                    break;
                case 'staff':
                    $staff = Staff::where('staffID', $id)->first();
                    if ($staff) {
                        $staff->pensionDate = null;
                        $staff->save();
                    }
                    break;
                case 'principal':
                    $principal = Principal::where('principalID', $id)->first();
                    if ($principal) {
                        $principal->pensionDate = null;
                        $principal->save();
                    }
                    break;
                default:
                    throw new \Exception('Invalid type');
            }
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Pension date cancelled successfully');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to cancel pension date: ' . $e->getMessage());
        }
    }
    
    /**
     * Display resigned teachers (HR view)
     */
    public function resignedTeachers(Request $request)
    {
        $search = $request->input('search');
        
        $query = Teacher::leftJoin('assign', 'teacher.teacherID', '=', 'assign.teacherID')
            ->leftJoin('school', 'assign.schoolID', '=', 'school.schoolID')
            ->select('teacher.*', 'assign.status as assign_status', 'school.schoolName')
            ->where('assign.status', 'Berhenti');
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('teacher.teacherID', 'like', "%{$search}%")
                  ->orWhere('teacher.teacherName', 'like', "%{$search}%")
                  ->orWhere('teacher.ICNumber', 'like', "%{$search}%")
                  ->orWhere('school.schoolName', 'like', "%{$search}%");
            });
        }
        
        $teachers = $query->orderBy('school.schoolName')
            ->orderBy('teacher.teacherName')
            ->get()
            ->groupBy('schoolName');
        
        $stats = [
            'total' => Teacher::leftJoin('assign', 'teacher.teacherID', '=', 'assign.teacherID')
                ->where('assign.status', 'Berhenti')->count(),
        ];
        
        return view('pension.resigned-teachers', compact('teachers', 'stats'));
    }
    
    /**
     * Display resigned staff (HR view)
     */
    public function resignedStaff(Request $request)
    {
        $search = $request->input('search');
        
        $query = Staff::select('staff.*')
            ->where('staff.status', 'Berhenti');
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('staff.staffID', 'like', "%{$search}%")
                  ->orWhere('staff.staffName', 'like', "%{$search}%")
                  ->orWhere('staff.ICNumber', 'like', "%{$search}%")
                  ->orWhere('staff.department', 'like', "%{$search}%");
            });
        }
        
        $staff = $query->orderBy('staff.department')
            ->orderBy('staff.staffName')
            ->get()
            ->groupBy('department');
        
        $stats = [
            'total' => Staff::where('status', 'Berhenti')->count(),
        ];
        
        return view('pension.resigned-staff', compact('staff', 'stats'));
    }
    
    /**
     * Display resigned principals (HR view)
     */
    public function resignedPrincipals(Request $request)
    {
        $search = $request->input('search');
        
        $query = Principal::leftJoin('school', 'principal.schoolID', '=', 'school.schoolID')
            ->select('principal.*', 'school.schoolName')
            ->where('principal.status', 'Berhenti');
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('principal.principalID', 'like', "%{$search}%")
                  ->orWhere('principal.principalName', 'like', "%{$search}%")
                  ->orWhere('principal.ICNumber', 'like', "%{$search}%")
                  ->orWhere('school.schoolName', 'like', "%{$search}%");
            });
        }
        
        $principals = $query->orderBy('school.schoolName')
            ->orderBy('principal.principalName')
            ->get()
            ->groupBy('schoolName');
        
        $stats = [
            'total' => Principal::where('status', 'Berhenti')->count(),
        ];
        
        return view('pension.resigned-principals', compact('principals', 'stats'));
    }
    
    /**
     * Get pension report
     */
    public function report(Request $request)
    {
        $type = $request->input('type', 'all');
        $days = $request->input('days', 'all');
        
        $teachers = collect();
        $staff = collect();
        $principals = collect();
        
        $queryTeachers = Teacher::leftJoin('assign', 'teacher.teacherID', '=', 'assign.teacherID')
            ->leftJoin('school', 'assign.schoolID', '=', 'school.schoolID')
            ->select('teacher.*', 'school.schoolName')
            ->whereNotNull('teacher.pensionDate')
            ->where('assign.status', 'Aktif');
            
        $queryStaff = Staff::select('staff.*')
            ->whereNotNull('staff.pensionDate')
            ->where('staff.pensionDate', '>', now());
            
        $queryPrincipals = Principal::leftJoin('school', 'principal.schoolID', '=', 'school.schoolID')
            ->select('principal.*', 'school.schoolName')
            ->whereNotNull('principal.pensionDate')
            ->where('principal.status', 'Aktif');
        
        // Apply days filter for report
        if ($days !== 'all') {
            $dateThreshold = now()->addDays((int) $days);
            $queryTeachers->where('teacher.pensionDate', '<=', $dateThreshold);
            $queryStaff->where('staff.pensionDate', '<=', $dateThreshold);
            $queryPrincipals->where('principal.pensionDate', '<=', $dateThreshold);
        }
        
        if ($type === 'all' || $type === 'teacher') {
            $teachers = $queryTeachers->orderBy('teacher.pensionDate', 'asc')->get();
        }
        
        if ($type === 'all' || $type === 'staff') {
            $staff = $queryStaff->orderBy('staff.pensionDate', 'asc')->get();
        }
        
        if ($type === 'all' || $type === 'principal') {
            $principals = $queryPrincipals->orderBy('principal.pensionDate', 'asc')->get();
        }
        
        return view('pension.report', compact('teachers', 'staff', 'principals', 'type', 'days'));
    }
    
    /**
     * Export pension report to CSV
     */
    public function export(Request $request)
    {
        $type = $request->input('type', 'all');
        $days = $request->input('days', 'all');
        $data = [];
        
        // Build queries with filters
        $queryTeachers = Teacher::leftJoin('assign', 'teacher.teacherID', '=', 'assign.teacherID')
            ->leftJoin('school', 'assign.schoolID', '=', 'school.schoolID')
            ->select('teacher.*', 'school.schoolName')
            ->whereNotNull('teacher.pensionDate')
            ->where('assign.status', 'Aktif');
            
        $queryStaff = Staff::select('staff.*')
            ->whereNotNull('staff.pensionDate')
            ->where('staff.pensionDate', '>', now());
            
        $queryPrincipals = Principal::leftJoin('school', 'principal.schoolID', '=', 'school.schoolID')
            ->select('principal.*', 'school.schoolName')
            ->whereNotNull('principal.pensionDate')
            ->where('principal.status', 'Aktif');
        
        // Apply days filter
        if ($days !== 'all') {
            $dateThreshold = now()->addDays((int) $days);
            $queryTeachers->where('teacher.pensionDate', '<=', $dateThreshold);
            $queryStaff->where('staff.pensionDate', '<=', $dateThreshold);
            $queryPrincipals->where('principal.pensionDate', '<=', $dateThreshold);
        }
        
        if ($type === 'all' || $type === 'teacher') {
            $teachers = $queryTeachers->orderBy('teacher.pensionDate', 'asc')->get();
            foreach ($teachers as $teacher) {
                $assignStatus = DB::table('assign')->where('teacherID', $teacher->teacherID)->value('status');
                $daysLeft = \Carbon\Carbon::now()->diffInDays($teacher->pensionDate, false);
                $data[] = [
                    'ID' => $teacher->teacherID,
                    'Name' => $teacher->teacherName,
                    'Type' => 'Teacher',
                    'IC Number' => $teacher->ICNumber,
                    'School/Department' => $teacher->schoolName ?? '-',
                    'Pension Date' => $teacher->pensionDate,
                    'Days Left' => max(0, $daysLeft),
                    'Status' => $assignStatus ?? 'Aktif',
                ];
            }
        }
        
        if ($type === 'all' || $type === 'staff') {
            $staffMembers = $queryStaff->orderBy('staff.pensionDate', 'asc')->get();
            foreach ($staffMembers as $staff) {
                $daysLeft = \Carbon\Carbon::now()->diffInDays($staff->pensionDate, false);
                $data[] = [
                    'ID' => $staff->staffID,
                    'Name' => $staff->staffName,
                    'Type' => 'Staff',
                    'IC Number' => $staff->ICNumber,
                    'School/Department' => $staff->department ?? '-',
                    'Pension Date' => $staff->pensionDate,
                    'Days Left' => max(0, $daysLeft),
                    'Status' => $staff->status ?? 'Aktif',
                ];
            }
        }
        
        if ($type === 'all' || $type === 'principal') {
            $principalsList = $queryPrincipals->orderBy('principal.pensionDate', 'asc')->get();
            foreach ($principalsList as $principal) {
                $daysLeft = \Carbon\Carbon::now()->diffInDays($principal->pensionDate, false);
                $data[] = [
                    'ID' => $principal->principalID,
                    'Name' => $principal->principalName,
                    'Type' => 'Principal',
                    'IC Number' => $principal->ICNumber,
                    'School/Department' => $principal->schoolName ?? '-',
                    'Pension Date' => $principal->pensionDate,
                    'Days Left' => max(0, $daysLeft),
                    'Status' => $principal->status ?? 'Aktif',
                ];
            }
        }
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="pension_report_' . date('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            
            if (!empty($data)) {
                $headers = array_keys($data[0]);
                fputcsv($file, $headers);
                
                foreach ($data as $row) {
                    fputcsv($file, $row);
                }
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}