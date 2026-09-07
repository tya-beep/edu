<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HRStaffResignationController extends Controller
{
    /**
     * List all pending staff resignation requests
     */
    public function pending()
    {
        // Remove the school join since staff table doesn't have schoolID
        $staff = Staff::select('staff.*')
            ->where('staff.resignation_request_status', 'pending')
            ->orderBy('staff.resignation_request_date', 'asc')
            ->get();
        
        $stats = [
            'pending' => Staff::where('resignation_request_status', 'pending')->count(),
            'approved' => Staff::where('resignation_request_status', 'approved')->count(),
            'rejected' => Staff::where('resignation_request_status', 'rejected')->count(),
        ];
        
        return view('hr.staff-resignations.pending', compact('staff', 'stats'));
    }
    
    /**
     * List all staff resignation requests
     */
    public function all(Request $request)
    {
        $query = Staff::select('staff.*')
            ->whereNotNull('staff.resignation_request_status');
        
        if ($request->filled('status')) {
            $query->where('staff.resignation_request_status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('staff.staffName', 'like', "%{$search}%")
                  ->orWhere('staff.staffID', 'like', "%{$search}%");
            });
        }
        
        $staff = $query->orderBy('staff.resignation_request_date', 'desc')
            ->paginate(20);
        
        $stats = [
            'pending' => Staff::where('resignation_request_status', 'pending')->count(),
            'approved' => Staff::where('resignation_request_status', 'approved')->count(),
            'rejected' => Staff::where('resignation_request_status', 'rejected')->count(),
        ];
        
        return view('hr.staff-resignations.all', compact('staff', 'stats'));
    }
    
    /**
     * Show resignation details for review
     */
    public function show($id)
    {
        // Remove the school join since staff table doesn't have schoolID
        $staff = Staff::where('staff.staffID', $id)->firstOrFail();
        
        return view('hr.staff-resignations.review', compact('staff'));
    }
    
    /**
     * Approve resignation request
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'pension_date' => 'required|date|after:today',
        ]);
        
        try {
            DB::beginTransaction();
            
            $staff = Staff::where('staffID', $id)->first();
            
            if (!$staff || $staff->resignation_request_status != 'pending') {
                throw new \Exception('No pending resignation request found.');
            }
            
            $staff->resignation_request_status = 'approved';
            $staff->pensionDate = $request->pension_date;
            $staff->status = 'Berhenti';
            $staff->save();
            
            DB::commit();
            
            return redirect()->route('hr.staff-resignations.pending')
                ->with('success', 'Staff resignation approved. Will retire on ' . $request->pension_date);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to approve: ' . $e->getMessage());
        }
    }
    
    /**
     * Reject resignation request
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);
        
        try {
            $staff = Staff::where('staffID', $id)->first();
            
            if (!$staff || $staff->resignation_request_status != 'pending') {
                throw new \Exception('No pending resignation request found.');
            }
            
            $staff->resignation_request_status = 'rejected';
            $staff->save();
            
            return redirect()->route('hr.staff-resignations.pending')
                ->with('success', 'Staff resignation request rejected.');
                
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to reject: ' . $e->getMessage());
        }
    }
}