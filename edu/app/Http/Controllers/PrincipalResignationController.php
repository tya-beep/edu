<?php

namespace App\Http\Controllers;

use App\Models\Principal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrincipalResignationController extends Controller
{
    /**
     * Show resignation form for principal
     */
    public function create()
    {
        $principalId = session('userID');
        $principal = Principal::where('principalID', $principalId)->first();
        
        if (!$principal) {
            return redirect()->route('principal.dashboard')->with('error', 'Principal not found');
        }
        
        // Check if already has pending request
        if ($principal->resignation_request_status == 'pending') {
            return redirect()->route('principal.resignations.index')
                ->with('error', 'You already have a pending resignation request.');
        }
        
        // Check if already resigned
        if ($principal->status == 'Berhenti') {
            return redirect()->route('principal.dashboard')
                ->with('error', 'You have already resigned.');
        }
        
        return view('principal.resignation.create', compact('principal'));
    }
    
    /**
     * Submit resignation request
     */
    public function store(Request $request)
    {
        $request->validate([
            'resignation_date' => 'required|date',
            'reason' => 'required|string|min:20|max:1000',
        ]);
        
        $principalId = session('userID');
        
        try {
            $principal = Principal::where('principalID', $principalId)->first();
            
            $principal->resignation_request_date = $request->resignation_date;
            $principal->resignation_request_reason = $request->reason;
            $principal->resignation_request_status = 'pending';
            $principal->save();
            
            return redirect()->route('principal.resignations.index')
                ->with('success', 'Resignation request submitted successfully. HR will review it.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to submit resignation: ' . $e->getMessage());
        }
    }
    
    /**
     * View principal's resignation requests
     */
    public function index()
    {
        $principalId = session('userID');
        $principal = Principal::where('principalID', $principalId)->first();
        
        return view('principal.resignation.index', compact('principal'));
    }
    
    /**
     * Cancel pending resignation request
     */
    public function cancel()
    {
        $principalId = session('userID');
        
        try {
            $principal = Principal::where('principalID', $principalId)->first();
            
            if ($principal->resignation_request_status != 'pending') {
                throw new \Exception('Only pending requests can be cancelled.');
            }
            
            $principal->resignation_request_date = null;
            $principal->resignation_request_reason = null;
            $principal->resignation_request_status = null;
            $principal->save();
            
            return redirect()->route('principal.resignations.index')
                ->with('success', 'Resignation request cancelled successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}