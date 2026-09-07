<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HRResignationController extends Controller
{
    /**
     * Display list of pending teacher resignations
     */
    public function pending()
    {
        $teachers = DB::table('teacher')
            ->leftJoin('assign', function($join) {
                $join->on('teacher.teacherID', '=', 'assign.teacherID')
                    ->whereRaw('assign.assignDate = (SELECT MAX(assignDate) FROM assign WHERE assign.teacherID = teacher.teacherID)');
            })
            ->leftJoin('school', 'assign.schoolID', '=', 'school.schoolID')
            ->where('teacher.resignation_request_status', 'pending')
            ->select(
                'teacher.*',
                'school.schoolName',
                'school.schoolID as assignedSchoolID'
            )
            ->orderBy('teacher.resignation_request_date', 'asc')
            ->paginate(10);
        
        $stats = [
            'pending' => Teacher::where('resignation_request_status', 'pending')->count(),
            'approved' => Teacher::where('resignation_request_status', 'approved')->count(),
            'rejected' => Teacher::where('resignation_request_status', 'rejected')->count(),
        ];
        
        return view('hr.teacher-resignations.pending', compact('teachers', 'stats'));
    }

    /**
     * Display all resignation requests with filters
     */
    public function all(Request $request)
    {
        $query = DB::table('teacher')
            ->leftJoin('assign', function($join) {
                $join->on('teacher.teacherID', '=', 'assign.teacherID')
                    ->whereRaw('assign.assignDate = (SELECT MAX(assignDate) FROM assign WHERE assign.teacherID = teacher.teacherID)');
            })
            ->leftJoin('school', 'assign.schoolID', '=', 'school.schoolID')
            ->whereNotNull('teacher.resignation_request_status')
            ->select(
                'teacher.*',
                'school.schoolName',
                'school.schoolID as assignedSchoolID'
            );
        
        // Search filter
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('teacher.teacherID', 'LIKE', "%{$search}%")
                  ->orWhere('teacher.teacherName', 'LIKE', "%{$search}%")
                  ->orWhere('teacher.ICNumber', 'LIKE', "%{$search}%");
            });
        }
        
        // Status filter
        if ($request->status && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('teacher.resignation_request_status', $request->status);
        }
        
        $teachers = $query->orderBy('teacher.resignation_request_date', 'desc')
            ->paginate(10);
        
        $stats = [
            'pending' => Teacher::where('resignation_request_status', 'pending')->count(),
            'approved' => Teacher::where('resignation_request_status', 'approved')->count(),
            'rejected' => Teacher::where('resignation_request_status', 'rejected')->count(),
        ];
        
        return view('hr.teacher-resignations.all', compact('teachers', 'stats'));
    }

    /**
     * Show specific resignation request details
     */
    public function show($id)
    {
        $teacher = DB::table('teacher')
            ->leftJoin('assign', function($join) {
                $join->on('teacher.teacherID', '=', 'assign.teacherID')
                    ->whereRaw('assign.assignDate = (SELECT MAX(assignDate) FROM assign WHERE assign.teacherID = teacher.teacherID)');
            })
            ->leftJoin('school', 'assign.schoolID', '=', 'school.schoolID')
            ->where('teacher.teacherID', $id)
            ->whereNotNull('teacher.resignation_request_status')
            ->select(
                'teacher.*',
                'school.schoolName',
                'school.schoolID as assignedSchoolID',
                'school.schoolAddress',
                'school.phoneNumber as schoolPhone'
            )
            ->first();
        
        if (!$teacher) {
            return redirect()->route('hr.resignations.pending')
                ->with('error', 'Teacher not found');
        }
        
        $stats = [
            'pending' => Teacher::where('resignation_request_status', 'pending')->count(),
            'approved' => Teacher::where('resignation_request_status', 'approved')->count(),
            'rejected' => Teacher::where('resignation_request_status', 'rejected')->count(),
        ];
        
        return view('hr.teacher-resignations.review', compact('teacher', 'stats'));
    }

    /**
     * Approve a resignation request
     */
    public function approve(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'remarks' => 'nullable|string|max:500',
            'effective_date' => 'nullable|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $teacher = Teacher::where('teacherID', $id)
                ->where('resignation_request_status', 'pending')
                ->firstOrFail();

            $teacher->resignation_request_status = 'approved';
            $teacher->resignation_approved_date = $request->effective_date ?? now();
            $teacher->resignation_remarks = $request->remarks ?? 'Approved by HR';
            $teacher->save();

            DB::commit();

            return redirect()->route('hr.resignations.pending')
                ->with('success', "Resignation request for {$teacher->teacherName} has been approved successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to approve resignation: ' . $e->getMessage());
        }
    }

    /**
     * Reject a resignation request
     */
    public function reject(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|min:10|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $teacher = Teacher::where('teacherID', $id)
                ->where('resignation_request_status', 'pending')
                ->firstOrFail();

            $teacher->resignation_request_status = 'rejected';
            $teacher->resignation_remarks = $request->rejection_reason;
            $teacher->save();

            DB::commit();

            return redirect()->route('hr.resignations.pending')
                ->with('success', "Resignation request for {$teacher->teacherName} has been rejected.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to reject resignation: ' . $e->getMessage());
        }
    }
}