<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrganizationController extends Controller
{
    /**
     * Display a listing of organizations.
     */
    public function index(Request $request)
    {
        $query = DB::table('organizations');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('OrganizationID', 'LIKE', "%{$search}%")
                  ->orWhere('OrganizationName', 'LIKE', "%{$search}%")
                  ->orWhere('OrganizationAddress', 'LIKE', "%{$search}%")
                  ->orWhere('PhoneNumber', 'LIKE', "%{$search}%");
            });
        }

        $organizations = $query->orderBy('OrganizationName')->paginate(10);
        $totalOrganizations = DB::table('organizations')->count();

        return view('organizations.index', compact('organizations', 'totalOrganizations'));
    }

    /**
     * Show the form for creating a new organization.
     */
    public function create()
    {
        return view('organizations.create');
    }

    /**
     * Store a newly created organization.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'OrganizationID' => 'required|string|max:20|unique:organizations,OrganizationID',
                'OrganizationName' => 'required|string|max:255',
                'OrganizationAddress' => 'nullable|string',
                'RegisterDate' => 'nullable|date',
                'PhoneNumber' => 'nullable|string|max:20'
            ]);

            // Insert directly using DB facade
            DB::table('organizations')->insert([
                'OrganizationID' => $request->OrganizationID,
                'OrganizationName' => $request->OrganizationName,
                'OrganizationAddress' => $request->OrganizationAddress,
                'RegisterDate' => $request->RegisterDate,
                'PhoneNumber' => $request->PhoneNumber,
            ]);

            return redirect()->route('orgs.index')
                ->with('success', 'Organization created successfully!');

        } catch (\Exception $e) {
            Log::error('Error creating organization: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create organization: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified organization.
     */
    public function show($id)
    {
        $organization = DB::table('organizations')->where('OrganizationID', $id)->first();
        
        if (!$organization) {
            abort(404, 'Organization not found');
        }
        
        // Get schools for this organization
        $schools = DB::table('school')->where('OrganizationID', $id)->get();
        
        return view('organizations.show', compact('organization', 'schools'));
    }

    /**
     * Show the form for editing the specified organization.
     */
    public function edit($id)
    {
        $organization = DB::table('organizations')->where('OrganizationID', $id)->first();
        
        if (!$organization) {
            abort(404, 'Organization not found');
        }
        
        return view('organizations.edit', compact('organization'));
    }

    /**
     * Update the specified organization.
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'OrganizationName' => 'required|string|max:255',
                'OrganizationAddress' => 'nullable|string',
                'RegisterDate' => 'nullable|date',
                'PhoneNumber' => 'nullable|string|max:20'
            ]);

            DB::table('organizations')
                ->where('OrganizationID', $id)
                ->update([
                    'OrganizationName' => $request->OrganizationName,
                    'OrganizationAddress' => $request->OrganizationAddress,
                    'RegisterDate' => $request->RegisterDate,
                    'PhoneNumber' => $request->PhoneNumber,
                ]);

            return redirect()->route('orgs.index')
                ->with('success', 'Organization updated successfully!');

        } catch (\Exception $e) {
            Log::error('Error updating organization: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update organization: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified organization.
     */
    public function destroy($id)
    {
        try {
            // Check if organization has schools
            $schoolCount = DB::table('school')->where('OrganizationID', $id)->count();
            
            if ($schoolCount > 0) {
                return redirect()->route('orgs.index')
                    ->with('error', 'Cannot delete organization because it has schools assigned to it.');
            }

            DB::table('organizations')->where('OrganizationID', $id)->delete();

            return redirect()->route('orgs.index')
                ->with('success', 'Organization deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Error deleting organization: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete organization: ' . $e->getMessage());
        }
    }

    /**
     * Import organizations from CSV.
     */
    public function import(Request $request)
    {
        try {
            $request->validate([
                'csvfile' => 'required|file|mimes:csv,txt|max:2048'
            ]);

            $file = $request->file('csvfile');
            $handle = fopen($file->getPathname(), 'r');
            
            // Skip header row
            fgetcsv($handle);
            
            $imported = 0;
            $errors = [];
            $rowNumber = 1;

            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $rowNumber++;
                
                try {
                    // Trim and clean data
                    $data = array_map('trim', $row);
                    
                    // Check if required fields exist
                    if (empty($data[0]) || empty($data[1])) {
                        $errors[] = "Row {$rowNumber}: Missing required fields (OrganizationID or OrganizationName)";
                        continue;
                    }

                    // Check if OrganizationID already exists
                    $exists = DB::table('organizations')->where('OrganizationID', $data[0])->exists();
                    if ($exists) {
                        $errors[] = "Row {$rowNumber}: OrganizationID '{$data[0]}' already exists";
                        continue;
                    }

                    // Insert directly using DB facade
                    DB::table('organizations')->insert([
                        'OrganizationID' => $data[0],
                        'OrganizationName' => $data[1],
                        'OrganizationAddress' => $data[2] ?? null,
                        'RegisterDate' => !empty($data[3]) ? date('Y-m-d', strtotime($data[3])) : null,
                        'PhoneNumber' => $data[4] ?? null,
                    ]);
                    
                    $imported++;
                    
                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNumber}: " . $e->getMessage();
                    Log::error('CSV import row error: ' . $e->getMessage(), ['row' => $row]);
                }
            }

            fclose($handle);

            // Log the import results
            Log::info('CSV import completed', ['imported' => $imported, 'errors' => $errors]);

            if ($imported > 0) {
                $message = "Successfully imported {$imported} organizations!";
                if (!empty($errors)) {
                    $message .= " (" . count($errors) . " errors encountered)";
                }
                return redirect()->route('orgs.index')
                    ->with('success', $message)
                    ->with('import_errors', $errors);
            } else {
                $errorMessage = "No organizations were imported. ";
                if (!empty($errors)) {
                    $errorMessage .= "Errors: " . implode('; ', array_slice($errors, 0, 3));
                    if (count($errors) > 3) {
                        $errorMessage .= " ... and " . (count($errors) - 3) . " more";
                    }
                } else {
                    $errorMessage .= "Please check your file format.";
                }
                return redirect()->route('orgs.index')
                    ->with('error', $errorMessage);
            }

        } catch (\Exception $e) {
            Log::error('CSV import error: ' . $e->getMessage());
            return redirect()->route('orgs.index')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Download CSV template for organizations import.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="organization_template.csv"',
        ];

        $columns = [
            'OrganizationID',
            'OrganizationName',
            'OrganizationAddress',
            'RegisterDate',
            'PhoneNumber'
        ];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Write header row
            fputcsv($file, $columns);
            
            // Write sample data rows
            fputcsv($file, [
                'ORG001',
                'Ministry of Education',
                'Level 5, Block E, Complex Government, Putrajaya',
                '2024-01-01',
                '03-8000-0000'
            ]);
            
            fputcsv($file, [
                'ORG002',
                'Selangor Education Department',
                'Jalan SS 7/2, Petaling Jaya, Selangor',
                '2024-01-15',
                '03-7000-0000'
            ]);
            
            fputcsv($file, [
                'ORG003',
                'Kuala Lumpur Education Department',
                'Jalan Tuanku Abdul Rahman, Kuala Lumpur',
                '2024-02-01',
                '03-6000-0000'
            ]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}