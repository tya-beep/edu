<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organization;

class OrganizationController extends Controller
{
    /**
     * Display a listing of organizations.
     */
    public function index(Request $request)
    {
        $query = Organization::query();

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
        $totalOrganizations = Organization::count();

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
        $validated = $request->validate([
            'OrganizationID' => 'required|string|max:20|unique:organizations,OrganizationID',
            'OrganizationName' => 'required|string|max:255',
            'OrganizationAddress' => 'nullable|string',
            'RegisterDate' => 'nullable|date',
            'PhoneNumber' => 'nullable|string|max:20'
        ]);

        Organization::create($validated);

        return redirect()->route('orgs.index')
            ->with('success', 'Organization created successfully!');
    }

    /**
     * Display the specified organization.
     */
    public function show($id)
    {
        $organization = Organization::with('schools')->findOrFail($id);
        return view('organizations.show', compact('organization'));
    }

    /**
     * Show the form for editing the specified organization.
     */
    public function edit($id)
    {
        $organization = Organization::findOrFail($id);
        return view('organizations.edit', compact('organization'));
    }

    /**
     * Update the specified organization.
     */
    public function update(Request $request, $id)
    {
        $organization = Organization::findOrFail($id);

        $validated = $request->validate([
            'OrganizationName' => 'required|string|max:255',
            'OrganizationAddress' => 'nullable|string',
            'RegisterDate' => 'nullable|date',
            'PhoneNumber' => 'nullable|string|max:20'
        ]);

        $organization->update($validated);

        return redirect()->route('orgs.index')
            ->with('success', 'Organization updated successfully!');
    }

    /**
     * Remove the specified organization.
     */
    public function destroy($id)
    {
        $organization = Organization::findOrFail($id);
        
        // Check if organization has schools
        if ($organization->schools()->count() > 0) {
            return redirect()->route('orgs.index')
                ->with('error', 'Cannot delete organization because it has schools assigned to it.');
        }

        $organization->delete();

        return redirect()->route('orgs.index')
            ->with('success', 'Organization deleted successfully!');
    }

    /**
     * Import organizations from CSV.
     */
    public function import(Request $request)
    {
        $request->validate([
            'csvfile' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('csvfile');
        $handle = fopen($file->getPathname(), 'r');
        
        // Skip header row
        fgetcsv($handle);
        
        $imported = 0;
        $errors = [];

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            try {
                Organization::create([
                    'OrganizationID' => $row[0] ?? null,
                    'OrganizationName' => $row[1] ?? null,
                    'OrganizationAddress' => $row[2] ?? null,
                    'RegisterDate' => $row[3] ?? null,
                    'PhoneNumber' => $row[4] ?? null
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($imported + 1) . ": " . $e->getMessage();
            }
        }

        fclose($handle);

        if ($imported > 0) {
            return redirect()->route('orgs.index')
                ->with('success', "Successfully imported {$imported} organizations!");
        } else {
            return redirect()->route('orgs.index')
                ->with('error', "No organizations were imported. Please check your file format.");
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