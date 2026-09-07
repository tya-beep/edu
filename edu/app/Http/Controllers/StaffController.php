<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class StaffController extends Controller
{
    /**
     * LIST STAFF - Group by department
     */
    public function index(Request $request): View
    {
        $query = DB::table('staff')
            ->where(function ($q) {
                $q->where('status', 'active')
                  ->orWhere('status', 'Aktif')
                  ->orWhereNull('status');
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('staffName', 'like', "%$search%")
                  ->orWhere('staffID', 'like', "%$search%")
                  ->orWhere('ICNumber', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        $staffs = $query->orderBy('staffName')
                        ->get()
                        ->groupBy('department');

        return view('staff.staff-list', compact('staffs'));
    }

    /**
     * STORE STAFF - Complete version with all fields
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'staffID'       => 'required|unique:staff,staffID',
            'staffName'     => 'required|string|max:255',
            'ICNumber'      => 'required|string|max:20',
            'phoneNumber'   => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:100|unique:staff,email',
            'maritalStatus' => 'nullable|in:Single,Married,Divorced,Widowed',
            'gender'        => 'nullable|in:Male,Female',
            'address'       => 'nullable|string',
            'race'          => 'nullable|string|max:50',
            'appointedDate' => 'nullable|date',
            'serviceDate'   => 'nullable|integer|min:0', // Changed to integer
            'pensionDate'   => 'nullable|date',
            'latestAge'     => 'nullable|integer|min:0|max:120',
            'department'    => 'required|string|max:100',
            'otherRace'     => 'nullable|string|max:50',
        ]);

        try {
            DB::beginTransaction();

            $race = $request->race;
            if ($request->race == 'Other' && $request->filled('otherRace')) {
                $race = $request->otherRace;
            }

            $defaultPassword = Hash::make('password');

            // Calculate service years if appointedDate is provided
            $serviceYears = null;
            if ($request->appointedDate) {
                $serviceYears = Carbon::parse($request->appointedDate)->diffInYears(now());
            }

            DB::table('staff')->insert([
                'staffID' => $request->staffID,
                'staffName' => $request->staffName,
                'ICNumber' => $request->ICNumber,
                'phoneNumber' => $request->phoneNumber,
                'email' => $request->email,
                'maritalStatus' => $request->maritalStatus,
                'gender' => $request->gender,
                'address' => $request->address,
                'race' => $race,
                'appointedDate' => $request->appointedDate,
                'serviceDate' => $serviceYears, // Store calculated years
                'pensionDate' => $request->pensionDate,
                'latestAge' => $request->latestAge,
                'department' => $request->department,
                'password' => $defaultPassword,
                'password_change_required' => 1,
                'role' => 'staff',
                'status' => 'active',
                'credit_hour' => $request->credit_hour ?? 0,
            ]);

            DB::commit();

            return redirect()->route('staff.list')
                           ->with('status', 'Staff registered successfully! Default password: password');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Staff registration failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to register staff: ' . $e->getMessage());
        }
    }

    /**
     * SHOW STAFF DETAILS
     */
    public function show($staffID): View|RedirectResponse
    {
        $staff = DB::table('staff')->where('staffID', $staffID)->first();

        if (!$staff) {
            return redirect()->route('staff.list')->with('error', 'Staff not found.');
        }

        return view('staff.show', compact('staff'));
    }

    /**
     * EDIT STAFF - FIXED with date formatting
     */
    public function edit($staffID): View|RedirectResponse
    {
        $staff = DB::table('staff')->where('staffID', $staffID)->first();

        if (!$staff) {
            return redirect()->route('staff.list')->with('error', 'Staff not found.');
        }

        // Format dates for the date input fields (Y-m-d format)
        if ($staff->appointedDate) {
            $staff->appointedDate = Carbon::parse($staff->appointedDate)->format('Y-m-d');
        }

        if ($staff->pensionDate) {
            $staff->pensionDate = Carbon::parse($staff->pensionDate)->format('Y-m-d');
        }

        // Calculate service years if not set
        if (!$staff->serviceDate && $staff->appointedDate) {
            $staff->serviceDate = Carbon::parse($staff->appointedDate)->diffInYears(now());
        }

        return view('staff.edit', compact('staff'));
    }

    /**
     * UPDATE STAFF - FIXED VERSION
     */
    public function update(Request $request, $staffID): RedirectResponse
    {
        Log::info('Updating staff: ' . $staffID, $request->all());

        $request->validate([
            'staffName'     => 'required|string|max:255',
            'ICNumber'      => 'required|string|max:20',
            'phoneNumber'   => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:100|unique:staff,email,' . $staffID . ',staffID',
            'maritalStatus' => 'nullable|in:Single,Married,Divorced,Widowed',
            'gender'        => 'nullable|in:Male,Female',
            'address'       => 'nullable|string',
            'race'          => 'nullable|string|max:50',
            'appointedDate' => 'nullable|date',
            'serviceDate'   => 'nullable|integer|min:0', // Integer validation
            'pensionDate'   => 'nullable|date',
            'latestAge'     => 'nullable|integer|min:0|max:120',
            'department'    => 'required|string|max:100',
            'status'        => 'nullable|string',
            'credit_hour'   => 'nullable|integer|min:0',
            'otherRace'     => 'nullable|string|max:50',
        ]);

        try {
            $race = $request->race;
            if ($request->race == 'Other' && $request->filled('otherRace')) {
                $race = $request->otherRace;
            }

            // Calculate service years from appointedDate if provided
            $serviceYears = $request->serviceDate;
            if ($request->appointedDate && !$request->serviceDate) {
                $serviceYears = Carbon::parse($request->appointedDate)->diffInYears(now());
            }

            $updateData = [
                'staffName' => $request->staffName,
                'ICNumber' => $request->ICNumber,
                'phoneNumber' => $request->phoneNumber,
                'email' => $request->email,
                'maritalStatus' => $request->maritalStatus,
                'gender' => $request->gender,
                'address' => $request->address,
                'race' => $race,
                'appointedDate' => $request->appointedDate,
                'serviceDate' => $serviceYears, // Store as integer
                'pensionDate' => $request->pensionDate,
                'latestAge' => $request->latestAge,
                'department' => $request->department,
                'credit_hour' => $request->credit_hour ?? 0,
            ];

            if ($request->has('status')) {
                $status = $request->status;
                if (in_array($status, ['active', 'Active', 'AKTIF', 'Aktif'])) {
                    $updateData['status'] = 'active';
                } elseif (in_array($status, ['Berhenti', 'berhenti', 'inactive', 'Inactive'])) {
                    $updateData['status'] = 'Berhenti';
                } else {
                    $updateData['status'] = $status;
                }
            }

            $affected = DB::table('staff')
                ->where('staffID', $staffID)
                ->update($updateData);

            Log::info('Staff update affected rows: ' . $affected);

            return redirect()->route('staff.show', $staffID)
                           ->with('status', 'Staff profile updated successfully!');

        } catch (\Exception $e) {
            Log::error('Staff update failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update staff: ' . $e->getMessage());
        }
    }

    /**
     * TERMINATE STAFF
     */
    public function terminate($staffID): RedirectResponse
    {
        try {
            DB::table('staff')
                ->where('staffID', $staffID)
                ->update(['status' => 'Berhenti']);

            return back()->with('status', 'Staff terminated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to terminate staff: ' . $e->getMessage());
        }
    }

    /**
     * DOWNLOAD TEMPLATE CSV
     */
    public function template(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=staff_template.csv',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, [
                'staffID',
                'staffName',
                'ICNumber',
                'phoneNumber',
                'email',
                'maritalStatus',
                'gender',
                'address',
                'race',
                'appointedDate',
                'serviceDate',
                'pensionDate',
                'latestAge',
                'department',
                'credit_hour'
            ]);

            fputcsv($file, [
                'STF001',
                'Ahmad Bin Abdullah',
                '750101101234',
                '0123456789',
                'ahmad@school.edu',
                'Married',
                'Male',
                'No 1, Jalan School, Kuala Lumpur',
                'Malay',
                '2020-01-15',
                '', // Leave empty - will be auto-calculated
                '2035-01-15',
                '45',
                'Administration',
                '40'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * IMPORT CSV - With better error handling
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csvfile' => 'required|file|mimes:csv,txt|max:5120'
        ]);

        $file = $request->file('csvfile');
        
        // Read and clean file content
        $content = file_get_contents($file->getRealPath());
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
        
        // Save to temp file
        $tempFile = tempnam(sys_get_temp_dir(), 'staff_clean_');
        file_put_contents($tempFile, $content);
        
        $handle = fopen($tempFile, 'r');
        
        // Detect delimiter
        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = strpos($firstLine, "\t") !== false ? "\t" : ",";
        
        // Read and skip header
        $header = fgetcsv($handle, 0, $delimiter);
        
        DB::beginTransaction();
        $successCount = 0;
        $failedCount = 0;
        $warnings = [];
        $errors = [];

        try {
            $rowNumber = 1;
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rowNumber++;
                
                // Skip empty rows
                if (empty(trim($row[0] ?? ''))) {
                    continue;
                }
                
                // Pad row to ensure we have at least 15 columns
                $row = array_pad($row, 15, '');
                
                // Clean data
                $staffID = trim($row[0]);
                $staffName = trim($row[1]);
                $ICNumber = trim($row[2]);
                $phoneNumber = !empty(trim($row[3])) ? trim($row[3]) : null;
                $email = !empty(trim($row[4])) ? trim($row[4]) : null;
                $maritalStatus = !empty(trim($row[5])) ? trim($row[5]) : null;
                $gender = !empty(trim($row[6])) ? trim($row[6]) : null;
                $address = !empty(trim($row[7])) ? trim($row[7]) : null;
                $race = !empty(trim($row[8])) ? trim($row[8]) : null;
                $department = trim($row[13]);
                $creditHour = !empty($row[14]) && is_numeric($row[14]) ? (int)$row[14] : 0;
                
                // Validate required fields
                if (empty($staffID)) {
                    $errors[] = "Row {$rowNumber}: Missing staffID";
                    $failedCount++;
                    continue;
                }
                
                if (empty($staffName)) {
                    $errors[] = "Row {$rowNumber}: Missing staffName for ID {$staffID}";
                    $failedCount++;
                    continue;
                }
                
                // Handle missing ICNumber
                if (empty($ICNumber)) {
                    $ICNumber = 'TEMP_' . preg_replace('/[^A-Za-z0-9]/', '', $staffID) . '_' . date('Ymd');
                    $warnings[] = "Row {$rowNumber}: Generated temporary IC for {$staffID}";
                }
                
                if (empty($department)) {
                    $errors[] = "Row {$rowNumber}: Missing department for ID {$staffID}";
                    $failedCount++;
                    continue;
                }
                
                // Check if staff already exists
                $existingStaff = DB::table('staff')->where('staffID', $staffID)->first();
                
                if ($existingStaff) {
                    $errors[] = "Row {$rowNumber}: Staff ID {$staffID} already exists";
                    $failedCount++;
                    continue;
                }
                
                // Parse dates
                $appointedDate = !empty($row[9]) && trim($row[9]) != '?' ? $this->parseDate($row[9]) : null;
                $pensionDate = !empty($row[11]) && trim($row[11]) != '?' ? $this->parseDate($row[11]) : null;
                $latestAge = !empty($row[12]) && is_numeric($row[12]) ? (int)$row[12] : null;
                
                // Calculate service years from appointed date
                $serviceYears = null;
                if ($appointedDate) {
                    $serviceYears = Carbon::parse($appointedDate)->diffInYears(now());
                }
                
                try {
                    DB::table('staff')->insert([
                        'staffID' => $staffID,
                        'staffName' => $staffName,
                        'ICNumber' => $ICNumber,
                        'phoneNumber' => $phoneNumber,
                        'email' => $email,
                        'maritalStatus' => $maritalStatus,
                        'gender' => $gender,
                        'address' => $address,
                        'race' => $race,
                        'appointedDate' => $appointedDate,
                        'serviceDate' => $serviceYears, // Auto-calculated
                        'pensionDate' => $pensionDate,
                        'latestAge' => $latestAge,
                        'department' => $department,
                        'credit_hour' => $creditHour,
                        'password' => Hash::make('password'),
                        'password_change_required' => 1,
                        'role' => 'staff',
                        'status' => 'active'
                    ]);
                    $successCount++;
                    
                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = "Row {$rowNumber}: " . $e->getMessage();
                    Log::error("Staff import error for ID {$staffID}: " . $e->getMessage());
                }
            }

            fclose($handle);
            unlink($tempFile);
            DB::commit();

            $message = "✨ Import completed! Success: {$successCount}, Failed: {$failedCount}";
            
            if (!empty($warnings)) {
                $message .= " Warnings: " . implode(", ", array_slice($warnings, 0, 3));
                if (count($warnings) > 3) {
                    $message .= " and " . (count($warnings) - 3) . " more warnings.";
                }
            }
            
            if (!empty($errors)) {
                $message .= " Errors: " . implode(", ", array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= " and " . (count($errors) - 5) . " more errors.";
                }
            }
            
            if ($successCount > 0) {
                return back()->with('status', $message);
            } else {
                return back()->with('error', $message);
            }

        } catch (\Exception $e) {
            DB::rollback();
            if (isset($handle)) fclose($handle);
            if (isset($tempFile)) unlink($tempFile);
            
            Log::error("Staff CSV Import Exception: " . $e->getMessage());
            
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Helper function to parse dates
     */
    private function parseDate(?string $dateString): ?string
    {
        if (empty($dateString)) return null;
        
        $dateString = trim($dateString);
        $dateString = str_replace('/', '-', $dateString);
        
        $timestamp = strtotime($dateString);
        if ($timestamp === false || $timestamp <= 0) {
            return null;
        }
        
        return date('Y-m-d', $timestamp);
    }

    /**
     * DISPLAY STOPPED STAFF
     */
    public function stopped(Request $request): View
    {
        $query = DB::table('staff')
            ->where('status', 'Berhenti');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('staffName', 'like', "%$search%")
                  ->orWhere('staffID', 'like', "%$search%")
                  ->orWhere('ICNumber', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('department', 'like', "%$search%");
            });
        }

        $resignedStaff = $query->orderBy('staffName')
                               ->get()
                               ->groupBy('department');

        $stats = [
            'total_resigned' => DB::table('staff')->where('status', 'Berhenti')->count(),
            'by_department' => DB::table('staff')
                ->select('department', DB::raw('count(*) as count'))
                ->where('status', 'Berhenti')
                ->groupBy('department')
                ->get(),
            'this_year_resigned' => DB::table('staff')
                ->where('status', 'Berhenti')
                ->whereYear('pensionDate', now()->year)
                ->count(),
        ];

        return view('staff.stopped', compact('resignedStaff', 'stats'));
    }

    /**
     * FIX SERVICE YEARS - One-time fix for all staff
     */
    public function fixServiceYears(): \Illuminate\Http\JsonResponse
    {
        try {
            $updated = DB::statement("
                UPDATE staff 
                SET serviceDate = TIMESTAMPDIFF(YEAR, appointedDate, CURDATE())
                WHERE appointedDate IS NOT NULL
            ");
            
            return response()->json([
                'message' => 'Service years updated successfully',
                'updated' => DB::table('staff')->whereNotNull('appointedDate')->count()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}