<?php

namespace App\Http\Controllers;

use App\Models\TeacherAudit;
use App\Models\Teacher;
use App\Models\School;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SchoolController extends Controller
{
    public function index()
    {
        return redirect()->route('school.list');
    }

    // SCHOOL LIST + SEARCH
    public function list(Request $request)
    {
        $query = School::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('schoolName', 'like', "%$search%")
                  ->orWhere('schoolID', 'like', "%$search%")
                  ->orWhere('schoolAddress', 'like', "%$search%");
            });
        }

        $schools = $query->orderBy('schoolName')->get();

        return view('school.school-list', compact('schools'));
    }

    public function records(Request $request)
    {
        $query = School::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('schoolName', 'like', "%$search%")
                  ->orWhere('schoolID', 'like', "%$search%")
                  ->orWhere('schoolAddress', 'like', "%$search%");
            });
        }

        $schools = $query->orderBy('schoolName')->get();

        return view('school.school-records', compact('schools'));
    }

  public function organizations()
{
    return redirect()->route('orgs.index');
}
    
    public function uploadForm()
    {
        return view('school.upload');
    }
    
    public function importCSV(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120'
        ]);

        $file = $request->file('file');
        
        try {
            $handle = fopen($file->getRealPath(), 'r');
            
            if (!$handle) {
                throw new \Exception('Cannot open the file');
            }

            $firstLine = fgets($handle);
            rewind($handle);
            $delimiter = strpos($firstLine, "\t") !== false ? "\t" : ",";
            $header = fgetcsv($handle, 0, $delimiter);
            
            if (!$header) {
                throw new \Exception('File is empty or invalid format');
            }
            
            $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
            
            $importedCount = 0;
            $skippedCount = 0;
            $updatedCount = 0;
            $errors = [];

            DB::beginTransaction();

            $rowNumber = 1;
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rowNumber++;
                
                if (empty(trim($row[0] ?? '')) && empty(trim($row[1] ?? ''))) {
                    $skippedCount++;
                    continue;
                }

                $schoolID      = !empty(trim($row[0] ?? '')) ? trim($row[0]) : null;
                $schoolName    = trim($row[1] ?? '');
                $schoolAddress = trim($row[2] ?? '');
                $registerDate  = !empty($row[3]) ? date('Y-m-d', strtotime($row[3])) : null;
                $phoneNumber   = !empty(trim($row[4] ?? '')) ? trim($row[4]) : null;
                $totalTeacher  = !empty($row[5]) && is_numeric($row[5]) ? (int)$row[5] : 0;
                $vacancy       = !empty($row[6]) && is_numeric($row[6]) ? (int)$row[6] : 0;
                $capacity      = !empty($row[7]) && is_numeric($row[7]) ? (int)$row[7] : 15;

                if (empty($schoolID)) {
                    $schoolID = 'SCH' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $schoolName), 0, 8)) . rand(100, 999);
                }

                if (empty($schoolName)) {
                    $errors[] = "Row {$rowNumber}: Missing school name";
                    $skippedCount++;
                    continue;
                }
                
                if (empty($schoolAddress)) {
                    $errors[] = "Row {$rowNumber}: Missing address for {$schoolName}";
                    $skippedCount++;
                    continue;
                }

                try {
                    $existingSchool = School::where('schoolID', $schoolID)->first();
                    
                    // Recalculate vacancy if needed
                    $calculatedVacancy = $capacity - $totalTeacher;
                    $finalVacancy = $calculatedVacancy >= 0 ? $calculatedVacancy : 0;
                    
                    if ($existingSchool) {
                        $existingSchool->update([
                            'schoolName' => $schoolName,
                            'schoolAddress' => $schoolAddress,
                            'registerDate' => $registerDate,
                            'phoneNumber' => $phoneNumber,
                            'totalTeacher' => $totalTeacher,
                            'vacancy' => $finalVacancy,
                            'capacity' => $capacity,
                        ]);
                        $updatedCount++;
                    } else {
                        School::create([
                            'schoolID' => $schoolID,
                            'schoolName' => $schoolName,
                            'schoolAddress' => $schoolAddress,
                            'registerDate' => $registerDate,
                            'phoneNumber' => $phoneNumber,
                            'totalTeacher' => $totalTeacher,
                            'vacancy' => $finalVacancy,
                            'capacity' => $capacity,
                        ]);
                        $importedCount++;
                    }
                    
                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNumber} (ID: {$schoolID}): " . $e->getMessage();
                    Log::error("CSV Import Error at row {$rowNumber}: " . $e->getMessage(), ['schoolID' => $schoolID]);
                }
            }

            DB::commit();
            fclose($handle);

            $message = "✅ Import completed! ";
            $message .= "New: {$importedCount}, Updated: {$updatedCount}";
            
            if ($skippedCount > 0) {
                $message .= ", Skipped: {$skippedCount}";
            }
            
            if (!empty($errors)) {
                $message .= " ⚠️ " . count($errors) . " errors encountered.";
                Log::warning("CSV Import Warnings: " . implode("; ", array_slice($errors, 0, 10)));
            }

            return redirect()
                ->route('school.list')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            
            if (isset($handle) && is_resource($handle)) {
                fclose($handle);
            }
            
            Log::error("CSV Import Exception: " . $e->getMessage());
            
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to import CSV: ' . $e->getMessage()]);
        }
    }
    
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_sekolah.csv"',
        ];

        $columns = [
            'schoolID',
            'schoolName',
            'schoolAddress',
            'registerDate',
            'phoneNumber',
            'totalTeacher',
            'vacancy',
            'capacity'
        ];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['SCH001', 'Sekolah Kebangsaan Damai', 'Jalan Damai, Kuala Lumpur', '2024-01-01', '03-42567890', '10', '5', '15']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function store(Request $request)
    {
        $request->validate([
            'schoolID' => 'required|unique:school,schoolID',
            'schoolName' => 'required',
            'schoolAddress' => 'required',
            'registerDate' => 'nullable|date',
            'phoneNumber' => 'nullable|string|max:20',
            'totalTeacher' => 'required|integer|min:0',
            'capacity' => 'required|integer|min:0',
        ]);

        $capacity = $request->capacity;
        $totalTeacher = $request->totalTeacher;
        $vacancy = $capacity - $totalTeacher;

        School::create([
            'schoolID' => $request->schoolID,
            'schoolName' => $request->schoolName,
            'schoolAddress' => $request->schoolAddress,
            'registerDate' => $request->registerDate,
            'phoneNumber' => $request->phoneNumber ?: null,
            'totalTeacher' => $totalTeacher,
            'vacancy' => max(0, $vacancy),
            'capacity' => $capacity,
        ]);

        return redirect()
            ->route('school.list')
            ->with('success', 'School registered successfully!');
    }

    public function edit($id)
    {
        $school = School::where('schoolID', $id)->firstOrFail();
        return view('school.edit', compact('school'));
    }

    public function update(Request $request, $id)
    {
        $id = urldecode($id);
        
        $school = School::where('schoolID', $id)->first();
        
        if (!$school) {
            return redirect()
                ->route('school.list')
                ->with('error', 'School not found');
        }

        $request->validate([
            'schoolName' => 'required|string|max:255',
            'schoolAddress' => 'required|string',
            'registerDate' => 'nullable|date',
            'phoneNumber' => 'nullable|string|max:20',
            'totalTeacher' => 'required|integer|min:0',
            'capacity' => 'required|integer|min:0',
        ]);

        try {
            $capacity = $request->capacity;
            $totalTeacher = $request->totalTeacher;
            $vacancy = $capacity - $totalTeacher;
            
            if ($vacancy < 0) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Total teachers (' . $totalTeacher . ') exceeds school capacity (' . $capacity . ')! Please adjust capacity or teacher count.');
            }

            $data = [
                'schoolName' => $request->schoolName,
                'schoolAddress' => $request->schoolAddress,
                'registerDate' => $request->registerDate,
                'totalTeacher' => $totalTeacher,
                'vacancy' => $vacancy,
                'capacity' => $capacity,
            ];
            
            if ($request->filled('phoneNumber')) {
                $data['phoneNumber'] = $request->phoneNumber;
            } else {
                $data['phoneNumber'] = null;
            }
            
            $school->update($data);

            return redirect()
                ->route('school.show', $school->schoolID)
                ->with('success', 'School updated successfully! Capacity: ' . $capacity . ', Total Teachers: ' . $totalTeacher . ', Vacancy: ' . $vacancy);
                
        } catch (\Exception $e) {
            Log::error('School update error: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update school: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $school = School::where('schoolID', $id)->firstOrFail();
        $school->delete();

        return redirect()
            ->route('school.records')
            ->with('success', 'Deleted successfully');
    }

 public function show($id)
{
    $id = urldecode($id);
    
    $school = School::where('schoolID', $id)->first();
    
    if (!$school) {
        abort(404, 'School not found with ID: ' . $id);
    }
    
    // Get active teachers assigned to this school
    $teachers = DB::table('teacher')
        ->join('assign', 'teacher.teacherID', '=', 'assign.teacherID')
        ->select('teacher.*', 'assign.status')
        ->where('assign.schoolID', $id)
        ->where('assign.status', '=', 'Aktif')
        ->orderBy('teacher.teacherName')
        ->get();
    
    // Get ALL teachers (including inactive) for statistics
    $allTeachers = DB::table('teacher')
        ->join('assign', 'teacher.teacherID', '=', 'assign.teacherID')
        ->select('teacher.*', 'assign.status')
        ->where('assign.schoolID', $id)
        ->orderBy('teacher.teacherName')
        ->get();
    
    // Get statistics
    $totalTeachers = $allTeachers->count();
    $activeTeachers = $allTeachers->where('status', 'Aktif')->count();
    $inactiveTeachers = $allTeachers->where('status', 'Berhenti')->count();
    
    // Update school totalTeacher count based on active teachers
    $this->updateSchoolTeacherCount($id);
    
    // Refresh school data
    $school = School::where('schoolID', $id)->first();
    
    return view('school.show', compact('school', 'teachers', 'totalTeachers', 'activeTeachers', 'inactiveTeachers'));
}

    /**
     * Update the school's totalTeacher count based on active teachers
     */
    private function updateSchoolTeacherCount($schoolId)
    {
        try {
            // Count active teachers for this school
            $activeCount = DB::table('teacher')
                ->join('assign', 'teacher.teacherID', '=', 'assign.teacherID')
                ->where('assign.schoolID', $schoolId)
                ->where('assign.status', 'Aktif')
                ->count();
            
            // Get school capacity
            $school = School::where('schoolID', $schoolId)->first();
            $capacity = $school->capacity ?? 15;
            
            // Calculate vacancy
            $vacancy = $capacity - $activeCount;
            
            // Update the school table
            School::where('schoolID', $schoolId)
                ->update([
                    'totalTeacher' => $activeCount,
                    'vacancy' => max(0, $vacancy)
                ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to update school teacher count for {$schoolId}: " . $e->getMessage());
            return false;
        }
    }

}