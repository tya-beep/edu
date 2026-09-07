<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\Assign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TeacherResignationController extends Controller
{
    /**
     * Show resignation form
     */
    public function create()
    {
        $teacherId = session('userID');
        $teacher = Teacher::where('teacherID', $teacherId)->first();
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')->with('error', 'Teacher not found');
        }
        
        // Check if already has pending request
        if ($teacher->resignation_request_status == 'pending') {
            return redirect()->route('teacher.resignations.index')
                ->with('error', 'You already have a pending resignation request.');
        }
        
        // Check if already resigned
        $isResigned = DB::table('assign')
            ->where('teacherID', $teacherId)
            ->where('status', 'Berhenti')
            ->exists();
        
        if ($isResigned) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'You have already resigned.');
        }
        
        return view('teacher.resignation.create', compact('teacher'));
    }
    
    /**
     * Submit resignation request
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'resignation_date' => 'required|date|after_or_equal:today',
            'reason' => 'required|string|min:20|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $teacherId = session('userID');
        
        try {
            DB::beginTransaction();
            
            $teacher = Teacher::where('teacherID', $teacherId)->first();
            
            if (!$teacher) {
                throw new \Exception('Teacher not found');
            }
            
            $teacher->resignation_request_date = $request->resignation_date;
            $teacher->resignation_request_reason = $request->reason;
            $teacher->resignation_request_status = 'pending';
            $teacher->save();
            
            DB::commit();
            
            return redirect()->route('teacher.resignations.index')
                ->with('success', 'Resignation request submitted successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to submit resignation: ' . $e->getMessage());
        }
    }
    
    /**
     * View teacher's resignation requests
     */
    public function index()
    {
        $teacherId = session('userID');
        $teacher = Teacher::where('teacherID', $teacherId)->first();
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')->with('error', 'Teacher not found');
        }
        
        return view('teacher.resignation.index', compact('teacher'));
    }
    
    /**
     * Cancel pending resignation request
     */
    public function cancel()
    {
        $teacherId = session('userID');
        
        try {
            DB::beginTransaction();
            
            $teacher = Teacher::where('teacherID', $teacherId)->first();
            
            if (!$teacher) {
                throw new \Exception('Teacher not found');
            }
            
            if ($teacher->resignation_request_status != 'pending') {
                throw new \Exception('Only pending requests can be cancelled.');
            }
            
            $teacher->resignation_request_date = null;
            $teacher->resignation_request_reason = null;
            $teacher->resignation_request_status = null;
            $teacher->save();
            
            DB::commit();
            
            return redirect()->route('teacher.resignations.index')
                ->with('success', 'Resignation request cancelled successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}