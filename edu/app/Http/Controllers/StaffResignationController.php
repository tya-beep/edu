<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StaffResignationController extends Controller
{
    /**
     * View staff's resignation requests
     */
    public function index()
    {
        // Check if user is logged in
        if (!session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        // Check if user is staff
        if (session('role') != 'staff') {
            abort(403, 'Access denied. Staff only.');
        }

        $staffId = session('userID');
        
        if (!$staffId) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        $staff = Staff::where('staffID', $staffId)->first();

        if (!$staff) {
            return redirect()->route('login')->with('error', 'Staff record not found.');
        }

        return view('staff.resignation.index', compact('staff'));
    }
    
    /**
     * Show resignation form for staff
     */
    public function create()
    {
        // Check if user is logged in
        if (!session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        // Check if user is staff
        if (session('role') != 'staff') {
            abort(403, 'Access denied. Staff only.');
        }

        $staffId = session('userID');
        $staff = Staff::where('staffID', $staffId)->first();
        
        if (!$staff) {
            return redirect()->route('staff.dashboard')->with('error', 'Staff not found');
        }
        
        // Check if already has pending request
        if ($staff->resignation_request_status == 'pending') {
            return redirect()->route('staff.resignations.index')
                ->with('error', 'You already have a pending resignation request.');
        }
        
        // Check if already resigned
        if ($staff->status == 'Berhenti') {
            return redirect()->route('staff.dashboard')
                ->with('error', 'You have already resigned.');
        }
        
        return view('staff.resignation.create', compact('staff'));
    }
    
    /**
     * Submit resignation request
     */
    public function store(Request $request)
    {
        $request->validate([
            'resignation_date' => 'required|date|after_or_equal:today',
            'reason' => 'required|string|min:20|max:1000',
        ]);
        
        $staffId = session('userID');
        
        try {
            $staff = Staff::where('staffID', $staffId)->first();
            
            if (!$staff) {
                throw new \Exception('Staff not found');
            }
            
            // Check if already has pending request
            if ($staff->resignation_request_status == 'pending') {
                throw new \Exception('You already have a pending resignation request.');
            }
            
            // Check if already resigned
            if ($staff->status == 'Berhenti') {
                throw new \Exception('You have already resigned.');
            }
            
            $staff->resignation_request_date = $request->resignation_date;
            $staff->resignation_request_reason = $request->reason;
            $staff->resignation_request_status = 'pending';
            $staff->save();
            
            return redirect()->route('staff.resignations.index')
                ->with('success', 'Resignation request submitted successfully. HR will review it.');
                
        } catch (\Exception $e) {
            Log::error('Staff resignation submission failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to submit resignation: ' . $e->getMessage());
        }
    }
    
    /**
     * Cancel pending resignation request
     */
    public function cancel(Request $request)
    {
        $staffId = session('userID');
        
        try {
            $staff = Staff::where('staffID', $staffId)->first();
            
            if (!$staff) {
                throw new \Exception('Staff not found');
            }
            
            if ($staff->resignation_request_status != 'pending') {
                throw new \Exception('Only pending requests can be cancelled.');
            }
            
            $staff->resignation_request_date = null;
            $staff->resignation_request_reason = null;
            $staff->resignation_request_status = null;
            $staff->save();
            
            return redirect()->route('staff.resignations.index')
                ->with('success', 'Resignation request cancelled successfully.');
                
        } catch (\Exception $e) {
            Log::error('Staff resignation cancellation failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}