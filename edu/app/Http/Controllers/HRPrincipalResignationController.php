<?php

namespace App\Http\Controllers;

use App\Models\Principal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HRPrincipalResignationController extends Controller
{
    /**
     * List all pending principal resignation requests
     */
    public function pending()
    {
        $principals = Principal::leftJoin('school', 'principal.schoolID', '=', 'school.schoolID')
            ->select('principal.*', 'school.schoolName')
            ->where('principal.resignation_request_status', 'pending')
            ->orderBy('principal.resignation_request_date', 'asc')
            ->get();
        
        $stats = [
            'pending' => Principal::where('resignation_request_status', 'pending')->count(),
            'approved' => Principal::where('resignation_request_status', 'approved')->count(),
            'rejected' => Principal::where('resignation_request_status', 'rejected')->count(),
        ];
        
        return view('hr.principal-resignations.pending', compact('principals', 'stats'));
    }
    
    /**
     * List all principal resignation requests
     */
    public function all(Request $request)
    {
        $query = Principal::leftJoin('school', 'principal.schoolID', '=', 'school.schoolID')
            ->select('principal.*', 'school.schoolName')
            ->whereNotNull('principal.resignation_request_status');
        
        if ($request->filled('status')) {
            $query->where('principal.resignation_request_status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('principal.principalName', 'like', "%{$search}%")
                  ->orWhere('principal.principalID', 'like', "%{$search}%");
            });
        }
        
        $principals = $query->orderBy('principal.resignation_request_date', 'desc')
            ->paginate(20);
        
        $stats = [
            'pending' => Principal::where('resignation_request_status', 'pending')->count(),
            'approved' => Principal::where('resignation_request_status', 'approved')->count(),
            'rejected' => Principal::where('resignation_request_status', 'rejected')->count(),
        ];
        
        return view('hr.principal-resignations.all', compact('principals', 'stats'));
    }
    
    /**
     * Show resignation details for review
     */
    public function show($id)
    {
        $principal = Principal::leftJoin('school', 'principal.schoolID', '=', 'school.schoolID')
            ->select('principal.*', 'school.schoolName')
            ->where('principal.principalID', $id)
            ->firstOrFail();
        
        return view('hr.principal-resignations.review', compact('principal'));
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
            
            $principal = Principal::where('principalID', $id)->first();
            
            if (!$principal || $principal->resignation_request_status != 'pending') {
                throw new \Exception('No pending resignation request found.');
            }
            
            // Update resignation status to approved
            $principal->resignation_request_status = 'approved';
            $principal->pensionDate = $request->pension_date;
            
            // IMPORTANT: DO NOT change status to 'Berhenti' here
            // The auto-retirement command will handle this on the pension date
            // $principal->status = 'Berhenti'; // <-- REMOVE THIS LINE
            
            $principal->save();
            
            // ❌ AUDIT LOGGING REMOVED - Commented out the insert
            // DB::table('principal_audit')->insert([
            //     'auditID' => 'APP_' . time() . '_' . $id,
            //     'principalID' => $id,
            //     'action' => 'Resignation Approved',
            //     'oldData' => json_encode(['resignation_request_status' => 'pending']),
            //     'newData' => json_encode([
            //         'resignation_request_status' => 'approved',
            //         'pensionDate' => $request->pension_date
            //     ]),
            //     'actionDate' => now(),
            // ]);
            
            DB::commit();
            
            return redirect()->route('hr.principal-resignations.pending')
                ->with('success', "✅ Principal resignation approved. Will retire on {$request->pension_date}");
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to approve: ' . $e->getMessage());
        }
    }
    
    /**
     * Reject resignation request - AUDIT REMOVED
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);
        
        try {
            DB::beginTransaction();
            
            $principal = Principal::where('principalID', $id)->first();
            
            if (!$principal || $principal->resignation_request_status != 'pending') {
                throw new \Exception('No pending resignation request found.');
            }
            
            $principal->resignation_request_status = 'rejected';
            $principal->resignation_rejection_reason = $request->reason;
            $principal->resignation_rejected_date = now();
            $principal->save();
            
            // ❌ AUDIT LOGGING REMOVED - Commented out the insert
            // DB::table('principal_audit')->insert([
            //     'auditID' => 'REJ_' . time() . '_' . $id,
            //     'principalID' => $id,
            //     'action' => 'Resignation Rejected',
            //     'oldData' => json_encode(['resignation_request_status' => 'pending']),
            //     'newData' => json_encode([
            //         'resignation_request_status' => 'rejected',
            //         'rejection_reason' => $request->reason
            //     ]),
            //     'actionDate' => now(),
            // ]);
            
            DB::commit();
            
            return redirect()->route('hr.principal-resignations.pending')
                ->with('success', '❌ Principal resignation request rejected.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to reject: ' . $e->getMessage());
        }
    }
    
    /**
     * Manually process retirements (for testing)
     */
    public function processRetirementsManually()
    {
        try {
            $today = date('Y-m-d');
            
            $principals = Principal::where('resignation_request_status', 'approved')
                ->where('pensionDate', '<=', $today)
                ->where('status', '!=', 'Berhenti')
                ->get();
            
            $count = 0;
            foreach ($principals as $principal) {
                $principal->status = 'Berhenti';
                $principal->save();
                $count++;
            }
            
            return response()->json([
                'success' => true,
                'message' => "Processed {$count} retirements",
                'principals' => $principals->pluck('principalName')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}