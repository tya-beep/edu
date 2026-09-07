<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\GuruNew;
use App\Models\Document;
use App\Models\Teacher;
use App\Models\School;
use App\Models\Applicant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PlacementController extends Controller
{
    /**
     * Display the teacher placement form (only pending assignments)
     * Only shows applicants with application_status = 'Accepted' AND confirmation_response = 'accepted'
     */
    public function placement()
    {
        try {
            // First get all pending guru_new records with their data
            $guruNewList = DB::table('guru_new')
                ->leftJoin('application', 'guru_new.application_id', '=', 'application.application_id')
                ->leftJoin('school', 'guru_new.schoolID', '=', 'school.schoolID')
                ->where('guru_new.assign_status', 'pending')
                ->whereNull('guru_new.teacherID')
                ->where('application.application_status', 'Accepted')
                ->where('application.confirmation_response', 'accepted')
                ->select(
                    'guru_new.*',
                    'application.applicant_id as app_applicant_id',
                    'school.schoolName',
                    'school.schoolAddress'
                )
                ->orderBy('guru_new.join_date', 'desc')
                ->get();

            // Now manually get the applicant data
            $guruNewListWithApplicants = collect();
            
            foreach ($guruNewList as $record) {
                $applicant = null;
                
                // Try different ID formats to find the applicant
                $applicantId = $record->app_applicant_id ?? $record->applicant_id;
                
                if ($applicantId) {
                    // Try direct match
                    $applicant = DB::table('applicant')
                        ->where('applicant_id', $applicantId)
                        ->first();
                    
                    // If not found, try replacing APP with A
                    if (!$applicant && strpos($applicantId, 'APP') === 0) {
                        $convertedId = 'A' . substr($applicantId, 3);
                        $applicant = DB::table('applicant')
                            ->where('applicant_id', $convertedId)
                            ->first();
                    }
                    
                    // If not found, try replacing APP with APL
                    if (!$applicant && strpos($applicantId, 'APP') === 0) {
                        $convertedId = 'APL' . substr($applicantId, 3);
                        $applicant = DB::table('applicant')
                            ->where('applicant_id', $convertedId)
                            ->first();
                    }
                    
                    // If still not found, try prefixing with A
                    if (!$applicant && strpos($applicantId, 'APP') !== 0) {
                        $convertedId = 'A' . $applicantId;
                        $applicant = DB::table('applicant')
                            ->where('applicant_id', $convertedId)
                            ->first();
                    }
                }
                
                // If still no applicant, try by email from guru_new or application
                if (!$applicant && !empty($record->email)) {
                    $applicant = DB::table('applicant')
                        ->where('email', $record->email)
                        ->first();
                }
                
                // Create enriched record
                $enrichedRecord = (object) array_merge(
                    (array) $record,
                    [
                        'full_name' => $applicant ? $applicant->full_name : 'N/A',
                        'applicant_email' => $applicant ? $applicant->email : 'No email found',
                        'phone_number' => $applicant ? $applicant->phone_number : 'N/A',
                        'applicant' => $applicant
                    ]
                );
                
                $guruNewListWithApplicants->push($enrichedRecord);
            }

            $pendingCount = DB::table('guru_new')
                ->where('assign_status', 'pending')
                ->count();

            $assignedCount = DB::table('guru_new')
                ->where('assign_status', 'accepted')
                ->count();

            // Calculate ONLY active teachers from ASSIGN table
            $totalTeachers = DB::table('assign')
                ->where('status', 'Aktif')
                ->distinct('teacherID')
                ->count('teacherID');

            // Get ONLY schools with vacancies (vacancy > 0)
            $schools = School::where('vacancy', '>', 0)
                ->orderBy('schoolName')
                ->get();

            // Calculate active teacher count for each school
            foreach ($schools as $school) {
                $school->activeTeacherCount = DB::table('assign')
                    ->where('schoolID', $school->schoolID)
                    ->where('status', 'Aktif')
                    ->count();
            }

            return view('placement.assign', compact(
                'guruNewListWithApplicants', 
                'schools', 
                'assignedCount', 
                'pendingCount',
                'totalTeachers'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading placement page: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to load placement data: ' . $e->getMessage());
        }
    }

    /**
     * Assign school to applicant based on vacancy (for offer letters)
     */
    public function assignSchool(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'application_id' => 'required',
            'schoolID' => 'required|exists:school,schoolID'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        try {
            DB::beginTransaction();
            
            $application = DB::table('application')
                           ->where('application_id', $request->application_id)
                           ->first();
            
            if (!$application) {
                throw new \Exception('Application not found!');
            }
            
            $school = DB::table('school')
                      ->where('schoolID', $request->schoolID)
                      ->first();
            
            if (!$school) {
                throw new \Exception('School not found!');
            }
            
            if ($school->vacancy <= 0) {
                throw new \Exception('This school has no available vacancies!');
            }
            
            DB::table('application')
              ->where('application_id', $request->application_id)
              ->update([
                  'schoolID' => $request->schoolID
              ]);
            
            DB::commit();
            
            return redirect()->back()->with('success', 'School assigned successfully! You can now send the offer letter.');
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error assigning school: ' . $e->getMessage());
            return back()->with('error', 'Failed to assign school: ' . $e->getMessage());
        }
    }
    
    public function assignToSchool(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gn_id' => 'required|exists:guru_new,gn_id',
            'schoolID' => 'required|exists:school,schoolID',
            'teacherID' => 'required|string|max:20|unique:teacher,teacherID',
            'assignDate' => 'required|date|after_or_equal:today'
        ], [
            'teacherID.unique' => 'This Teacher ID is already taken. Please use a different ID.',
            'assignDate.after_or_equal' => 'Assignment date must be today or a future date.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        DB::beginTransaction();
        
        try {
            // Get the guru_new record
            $guruNew = DB::table('guru_new')
                ->where('gn_id', $request->gn_id)
                ->first();
            
            if (!$guruNew) {
                throw new \Exception('Guru New record not found!');
            }
            
            // Get the application
            $application = DB::table('application')
                ->where('application_id', $guruNew->application_id)
                ->first();
            
            if (!$application) {
                throw new \Exception('Application not found!');
            }
            
            // Get the applicant - try different ID formats
            $applicant = null;
            $applicantId = $application->applicant_id ?? $guruNew->applicant_id;
            
            if ($applicantId) {
                // Try direct match
                $applicant = DB::table('applicant')
                    ->where('applicant_id', $applicantId)
                    ->first();
                
                // If not found, try replacing APP with A
                if (!$applicant && strpos($applicantId, 'APP') === 0) {
                    $convertedId = 'A' . substr($applicantId, 3);
                    $applicant = DB::table('applicant')
                        ->where('applicant_id', $convertedId)
                        ->first();
                }
                
                // If not found, try replacing APP with APL
                if (!$applicant && strpos($applicantId, 'APP') === 0) {
                    $convertedId = 'APL' . substr($applicantId, 3);
                    $applicant = DB::table('applicant')
                        ->where('applicant_id', $convertedId)
                        ->first();
                }
            }
            
            if (!$applicant) {
                throw new \Exception('Applicant data not found for this record!');
            }
            
            // Check application status
            if ($application->application_status !== 'Accepted') {
                throw new \Exception('Application status must be "Accepted" to convert! Current status: ' . $application->application_status);
            }
            
            if ($application->confirmation_response !== 'accepted') {
                throw new \Exception('Confirmation response must be "accepted" to convert! Current response: ' . $application->confirmation_response);
            }
            
            if (!empty($guruNew->teacherID)) {
                throw new \Exception('This Guru New has already been converted to a teacher!');
            }
            
            if ($guruNew->assign_status === 'accepted') {
                throw new \Exception('This assignment has already been accepted!');
            }
            
            $teacherID = $request->teacherID;
            
            // Check if teacher ID already exists
            $existingTeacher = DB::table('teacher')
                ->where('teacherID', $teacherID)
                ->first();
            
            if ($existingTeacher) {
                throw new \Exception('Teacher ID already exists! Please use a different ID.');
            }
            
            // Check if email already exists
            $existingEmail = DB::table('teacher')
                ->where('email', $applicant->email)
                ->first();
            
            if ($existingEmail) {
                throw new \Exception('A teacher with this email already exists! Teacher ID: ' . $existingEmail->teacherID);
            }
            
            // ==========================================
            // 1. INSERT INTO TEACHER TABLE
            // ==========================================
            DB::table('teacher')->insert([
                'teacherID' => $teacherID,
                'teacherName' => $applicant->full_name,
                'email' => $applicant->email,
                'password' => bcrypt('password'),
                'ICNumber' => $application->ic_number ?? null,
                'phoneNumber' => $applicant->phone_number ?? null,
                'gender' => $application->gender ?? null,
                'address' => $application->address ?? null,
                'race' => $application->race ?? null,
                'maritalStatus' => $application->marital_status ?? null,
                'appointedDate' => Carbon::today(),
                'serviceDate' => null,
                'role' => 'teacher',
                'password_change_required' => 1
            ]);
            
            Log::info('Teacher created', ['teacherID' => $teacherID]);
            
            // ==========================================
            // 2. INSERT INTO ASSIGN TABLE
            // ==========================================
            DB::table('assign')->insert([
                'schoolID' => $request->schoolID,
                'teacherID' => $teacherID,
                'assignDate' => $request->assignDate,
                'status' => 'Aktif'
            ]);
            
            Log::info('Assign record created', ['schoolID' => $request->schoolID, 'teacherID' => $teacherID]);
            
            // ==========================================
            // 3. UPDATE GURU_NEW TABLE
            // ==========================================
            DB::table('guru_new')
                ->where('gn_id', $request->gn_id)
                ->update([
                    'teacherID' => $teacherID,
                    'schoolID' => $request->schoolID,
                    'assign_status' => 'accepted',
                    'current_status' => 'Inactive'
                ]);
            
            Log::info('Guru New updated', ['gn_id' => $request->gn_id, 'teacherID' => $teacherID]);
            
            // ==========================================
            // 4. UPDATE APPLICATION TABLE
            // ==========================================
            if ($guruNew->application_id) {
                DB::table('application')
                    ->where('application_id', $guruNew->application_id)
                    ->update([
                        'schoolID' => $request->schoolID,
                        'offer_response' => 'accepted',
                        'confirmation_response' => 'accepted'
                    ]);
            }
            
            // ==========================================
            // 5. UPDATE SCHOOL TABLE
            // ==========================================
            DB::table('school')
                ->where('schoolID', $request->schoolID)
                ->decrement('vacancy');
            
            DB::table('school')
                ->where('schoolID', $request->schoolID)
                ->increment('totalTeacher');
            
            // ==========================================
            // 6. CREATE PLACEMENT DOCUMENT
            // ==========================================
            $documentID = 'PLC-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            
            DB::table('document')->insert([
                'documentID' => $documentID,
                'gn_id' => $request->gn_id,
                'applicant_id' => $applicant->applicant_id,
                'documentType' => 'Placement Letter',
                'documentDescription' => 'Teacher placement and conversion letter',
                'documentTitle' => 'Placement Letter for ' . $applicant->full_name,
                'dateIssued' => $request->assignDate,
                'signedBy' => session('user')->hrid ?? 'HR-001',
                'hrid' => session('user')->hrid ?? 'HR-001',
                'teacherID' => $teacherID,
                'pdfFile' => null,
                'filePath' => null,
                'status' => 'Completed'
            ]);
            
            DB::commit();
            
            Log::info('Teacher conversion completed successfully', [
                'teacherID' => $teacherID,
                'gn_id' => $request->gn_id
            ]);
            
            return redirect()->route('placement.records')
                ->with('success', "✓ Successfully Assigned! Teacher ID: {$teacherID} | School: {$request->schoolID}");
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Teacher conversion failed: ' . $e->getMessage(), [
                'gn_id' => $request->gn_id ?? null,
                'teacherID' => $request->teacherID ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to convert: ' . $e->getMessage());
        }
    }
    
    /**
     * Display placement records (only accepted assignments)
     */
    public function placementRecords()
    {
        try {
            // Get all accepted guru_new records with their data
            $placements = DB::table('guru_new')
                ->leftJoin('school', 'guru_new.schoolID', '=', 'school.schoolID')
                ->leftJoin('teacher', 'guru_new.teacherID', '=', 'teacher.teacherID')
                ->leftJoin('assign', function($join) {
                    $join->on('guru_new.teacherID', '=', 'assign.teacherID')
                        ->on('guru_new.schoolID', '=', 'assign.schoolID');
                })
                ->where('guru_new.assign_status', 'accepted')
                ->whereNotNull('guru_new.teacherID')
                ->select(
                    'guru_new.*',
                    'school.schoolName as school_name',
                    'school.schoolAddress as school_address',
                    'school.vacancy as school_vacancy',
                    'school.totalTeacher as school_total_teacher',
                    'school.phoneNumber as school_phone',
                    'teacher.teacherName as teacher_name',
                    'teacher.email as teacher_email',
                    'teacher.phoneNumber as teacher_phone',
                    'teacher.gender as teacher_gender',
                    'teacher.appointedDate as appointed_date',
                    'assign.assignDate as assign_date',
                    'assign.status as assign_status'
                )
                ->orderBy('guru_new.join_date', 'desc')
                ->paginate(20);

            // Get applicant names for each placement
            foreach ($placements as $placement) {
                $applicant = null;
                $applicantId = $placement->applicant_id;
                
                if ($applicantId) {
                    $applicant = DB::table('applicant')
                        ->where('applicant_id', $applicantId)
                        ->first();
                    
                    if (!$applicant && strpos($applicantId, 'APP') === 0) {
                        $convertedId = 'A' . substr($applicantId, 3);
                        $applicant = DB::table('applicant')
                            ->where('applicant_id', $convertedId)
                            ->first();
                    }
                }
                
                $placement->applicant_name = $applicant ? $applicant->full_name : 'N/A';
                $placement->applicant_email = $applicant ? $applicant->email : 'No email found';
                $placement->applicant_phone = $applicant ? $applicant->phone_number : 'N/A';
            }
            
            return view('placement.records', compact('placements'));
        } catch (\Exception $e) {
            Log::error('Error loading placement records: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to load placement records: ' . $e->getMessage());
        }
    }
    
    /**
     * Display pending assignments
     */
    public function pendingAssignments()
    {
        try {
            $pendingAssignments = DB::table('guru_new')
                ->leftJoin('school', 'guru_new.schoolID', '=', 'school.schoolID')
                ->where('guru_new.assign_status', 'pending')
                ->whereNull('guru_new.teacherID')
                ->select(
                    'guru_new.*',
                    'school.schoolName'
                )
                ->orderBy('guru_new.join_date', 'desc')
                ->paginate(20);

            // Get applicant names for each pending assignment
            foreach ($pendingAssignments as $pending) {
                $applicant = null;
                $applicantId = $pending->applicant_id;
                
                if ($applicantId) {
                    $applicant = DB::table('applicant')
                        ->where('applicant_id', $applicantId)
                        ->first();
                    
                    if (!$applicant && strpos($applicantId, 'APP') === 0) {
                        $convertedId = 'A' . substr($applicantId, 3);
                        $applicant = DB::table('applicant')
                            ->where('applicant_id', $convertedId)
                            ->first();
                    }
                }
                
                $pending->full_name = $applicant ? $applicant->full_name : 'N/A';
                $pending->applicant_email = $applicant ? $applicant->email : 'No email found';
            }
            
            $pendingCount = DB::table('guru_new')
                ->where('assign_status', 'pending')
                ->whereNull('teacherID')
                ->count();
            
            return view('placement.pending', compact('pendingAssignments', 'pendingCount'));
        } catch (\Exception $e) {
            Log::error('Error loading pending assignments: ' . $e->getMessage());
            return back()->with('error', 'Failed to load pending assignments.');
        }
    }

    /**
     * ==================== STEP 2: SEND OFFER LETTERS ====================
     * Show only applicants with offer_response = 'not_offer' OR NULL
     */
    public function offerLetters()
    {
        try {
            $applications = DB::table('application')
                ->leftJoin('school', 'application.schoolID', '=', 'school.schoolID')
                ->where('application.application_status', 'Accepted')
                ->where(function($query) {
                    $query->where('application.offer_response', 'not_offer')
                        ->orWhereNull('application.offer_response')
                        ->orWhere('application.offer_response', '');
                })
                ->select(
                    'application.*',
                    'school.schoolName as school_name',
                    'school.vacancy',
                    'school.schoolAddress'
                )
                ->orderBy('application.application_date', 'desc')
                ->get();

            // Get applicant data for each application
            foreach ($applications as $application) {
                $applicant = null;
                $applicantId = $application->applicant_id;
                
                if ($applicantId) {
                    $applicant = DB::table('applicant')
                        ->where('applicant_id', $applicantId)
                        ->first();
                    
                    if (!$applicant && strpos($applicantId, 'APP') === 0) {
                        $convertedId = 'A' . substr($applicantId, 3);
                        $applicant = DB::table('applicant')
                            ->where('applicant_id', $convertedId)
                            ->first();
                    }
                }
                
                $application->full_name = $applicant ? $applicant->full_name : 'N/A';
                $application->applicant_email = $applicant ? $applicant->email : 'No email found';
            }
            
            $schools = DB::table('school')
                ->where('vacancy', '>', 0)
                ->orderBy('schoolName')
                ->get();
            
            $pendingCount = DB::table('application')
                ->where('application_status', 'Accepted')
                ->where(function($query) {
                    $query->where('offer_response', 'not_offer')
                        ->orWhereNull('offer_response')
                        ->orWhere('offer_response', '');
                })
                ->count();
            
            return view('placement.offer-letters', compact('applications', 'pendingCount', 'schools'));
        } catch (\Exception $e) {
            Log::error('Error loading offer letters page: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to load offer letters: ' . $e->getMessage());
        }
    }
    
    /**
     * View sent offers - shows all sent offers regardless of status
     */
    public function viewSentOffers()
    {
        try {
            $documents = DB::table('document')
                ->where('document.documentType', 'Offer Letter')
                ->where('document.status', 'Sent')
                ->select('document.*')
                ->orderBy('document.dateIssued', 'desc')
                ->paginate(20);

            // Get applicant data for each document
            foreach ($documents as $document) {
                $applicant = null;
                $applicantId = $document->applicant_id;
                
                if ($applicantId) {
                    $applicant = DB::table('applicant')
                        ->where('applicant_id', $applicantId)
                        ->first();
                    
                    if (!$applicant && strpos($applicantId, 'APP') === 0) {
                        $convertedId = 'A' . substr($applicantId, 3);
                        $applicant = DB::table('applicant')
                            ->where('applicant_id', $convertedId)
                            ->first();
                    }
                }
                
                $document->full_name = $applicant ? $applicant->full_name : 'N/A';
                $document->applicant_email = $applicant ? $applicant->email : 'No email found';
            }
            
            return view('placement.sent-offers', compact('documents'));
        } catch (\Exception $e) {
            Log::error('Error loading sent offers: ' . $e->getMessage());
            return back()->with('error', 'Failed to load sent offers.');
        }
    }

    /**
     * Download PDF from database
     */
    public function downloadPDF(string $document_id)
    {
        try {
            $document = Document::find($document_id);
            
            if (!$document) {
                return response()->json(['error' => 'Document not found'], 404);
            }
            
            if (!$document->pdfFile) {
                return back()->with('error', 'PDF not found in database!');
            }
            
            $content = $document->pdfFile;
            $isHtml = strpos($content, '<html') !== false || strpos($content, '<!DOCTYPE') !== false;
            
            if ($isHtml) {
                return response($content)
                    ->header('Content-Type', 'text/html')
                    ->header('Content-Disposition', 'inline; filename="' . $document->documentID . '.html"');
            } else {
                return response($content)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'inline; filename="' . $document->documentID . '.pdf"');
            }
            
        } catch (\Exception $e) {
            Log::error('Error downloading PDF: ' . $e->getMessage());
            return back()->with('error', 'Failed to download PDF: ' . $e->getMessage());
        }
    }

    /**
     * View PDF in browser
     */
    public function viewPDF(string $document_id)
    {
        try {
            $document = Document::find($document_id);
            
            if (!$document || !$document->pdfFile) {
                return response()->json(['error' => 'PDF not found'], 404);
            }
            
            $content = $document->pdfFile;
            $isHtml = strpos($content, '<html') !== false || strpos($content, '<!DOCTYPE') !== false;
            
            if ($isHtml) {
                return response($content)->header('Content-Type', 'text/html');
            } else {
                return response($content)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'inline; filename="' . $document->documentID . '.pdf"');
            }
            
        } catch (\Exception $e) {
            Log::error('Error viewing PDF: ' . $e->getMessage());
            return back()->with('error', 'Failed to view PDF: ' . $e->getMessage());
        }
    }

    /**
     * Preview offer letter
     */
    public function previewOfferLetter(string $application_id)
    {
        try {
            $application = DB::table('application')
                ->leftJoin('school', 'application.schoolID', '=', 'school.schoolID')
                ->where('application.application_id', $application_id)
                ->select(
                    'application.*',
                    'school.schoolName as school_name',
                    'school.schoolAddress as school_address',
                    'school.vacancy'
                )
                ->first();
            
            if (!$application) {
                return redirect()->route('offer.letters')
                    ->with('error', 'Application not found!');
            }
            
            // Get applicant data
            $applicant = null;
            $applicantId = $application->applicant_id;
            
            if ($applicantId) {
                $applicant = DB::table('applicant')
                    ->where('applicant_id', $applicantId)
                    ->first();
                
                if (!$applicant && strpos($applicantId, 'APP') === 0) {
                    $convertedId = 'A' . substr($applicantId, 3);
                    $applicant = DB::table('applicant')
                        ->where('applicant_id', $convertedId)
                        ->first();
                }
            }
            
            // Check if school is assigned
            if (!$application->schoolID) {
                return redirect()->route('offer.letters')
                    ->with('error', 'Please assign a school to this applicant first!');
            }
            
            // Get school name from the joined table or fetch it directly
            $schoolName = $application->school_name ?? 'N/A';
            $schoolAddress = $application->school_address ?? 'N/A';
            
            // If school name is still empty, try to fetch it directly
            if (empty($schoolName) || $schoolName == 'N/A') {
                $school = DB::table('school')
                    ->where('schoolID', $application->schoolID)
                    ->first();
                if ($school) {
                    $schoolName = $school->schoolName ?? 'N/A';
                    $schoolAddress = $school->schoolAddress ?? 'N/A';
                }
            }
            
            $app = (object) [
                'application_id' => $application->application_id ?? null,
                'applicant' => (object) [
                    'full_name' => $applicant ? $applicant->full_name : 'N/A',
                    'email' => $applicant ? $applicant->email : 'N/A',
                ],
                'address' => $application->address ?? 'N/A',
                'ic_number' => $application->ic_number ?? 'N/A',
                'expected_salary' => $application->expected_salary ?? 2500,
                'application_date' => $application->application_date ?? now(),
                'offer_response' => $application->offer_response ?? 'not_offer',
                'school' => (object) [
                    'schoolID' => $application->schoolID,
                    'schoolName' => $schoolName,
                    'schoolAddress' => $schoolAddress,
                    'vacancy' => $application->vacancy ?? 0
                ]
            ];
            
            $offerContent = $this->generateOfferLetterContent($app);
            $pdfContent = $this->generatePDFContent($app);
            
            $documentID = 'OFF' . date('ymd') . rand(10, 99);
            
            $existingDocument = DB::table('document')
                ->where('applicant_id', $application->applicant_id)
                ->where('documentType', 'Offer Letter')
                ->where('status', 'Draft')
                ->first();
            
            if ($existingDocument) {
                DB::table('document')
                    ->where('documentID', $existingDocument->documentID)
                    ->update([
                        'pdfFile' => $pdfContent,
                        'documentTitle' => 'Offer Letter for ' . ($applicant ? $applicant->full_name : 'N/A'),
                        'dateIssued' => now()
                    ]);
                
                $document = Document::where('documentID', $existingDocument->documentID)->first();
            } else {
                $document = Document::create([
                    'documentID' => $documentID,
                    'gn_id' => null,
                    'applicant_id' => $application->applicant_id,
                    'documentType' => 'Offer Letter',
                    'documentDescription' => 'Job offer letter for teaching position',
                    'documentTitle' => 'Offer Letter for ' . ($applicant ? $applicant->full_name : 'N/A'),
                    'dateIssued' => now(),
                    'signedBy' => session('user')->hrid ?? 'HR01',
                    'hrid' => session('user')->hrid ?? 'HR01',
                    'teacherID' => null,
                    'pdfFile' => $pdfContent,
                    'filePath' => null,
                    'status' => 'Draft'
                ]);
            }
            
            return view('placement.offer-letter-preview', compact('app', 'document', 'offerContent'));
            
        } catch (\Exception $e) {
            Log::error('Error previewing offer letter: ' . $e->getMessage(), [
                'application_id' => $application_id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to preview offer letter: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF content as string
     */
    private function generatePDFContent(object $application)
    {
        try {
            $applicantName = isset($application->applicant->full_name) ? strtoupper($application->applicant->full_name) : 'N/A';
            $address = $application->address ?? 'N/A';
            $icNumber = $application->ic_number ?? 'N/A';
            $expectedSalary = $application->expected_salary ?? 2500;
            $applicationId = $application->application_id ?? 'N/A';
            
            // Get school name from the school object
            $schoolName = isset($application->school->schoolName) ? $application->school->schoolName : 'To be assigned';
            $schoolAddress = isset($application->school->schoolAddress) ? $application->school->schoolAddress : '';
            
            $date = date('d F Y');
            
            $html = "
            <html>
            <head>
                <meta charset='utf-8'>
                <title>Offer Letter</title>
                <style>
                    body {
                        font-family: 'Times New Roman', serif;
                        line-height: 1.8;
                        padding: 40px;
                        max-width: 800px;
                        margin: 0 auto;
                        color: #333;
                    }
                    h2 {
                        text-align: center;
                        font-size: 18px;
                        font-weight: bold;
                        margin-bottom: 5px;
                    }
                    .date {
                        text-align: right;
                        margin-bottom: 30px;
                    }
                    .subject {
                        font-weight: bold;
                    }
                    ul {
                        padding-left: 20px;
                    }
                    ul li {
                        margin-bottom: 5px;
                    }
                    .signature {
                        margin-top: 30px;
                    }
                    .footer {
                        margin-top: 40px;
                        text-align: center;
                        font-size: 12px;
                        color: #666;
                        border-top: 1px solid #ddd;
                        padding-top: 10px;
                    }
                </style>
            </head>
            <body>
                <h2>OFFER LETTER</h2>
                
                <div class='date'>Date: " . $date . "</div>
                
                <p>Dear <strong>" . e($applicantName) . "</strong>,</p>
                
                <p><strong>Subject:</strong> Offer of Employment as Teacher</p>
                
                <p>We are pleased to inform you that after careful consideration of your application, qualifications, and interview performance, you have been selected for employment at <strong>" . e($schoolName) . "</strong> as a Teacher.</p>
                
                <p>Please review this offer carefully. By accepting this offer, you agree to comply with all school policies, regulations, and employment requirements.</p>
                
                <p><strong>Offer Details:</strong></p>
                <ul>
                    <li><strong>Position:</strong> Teacher</li>
                    <li><strong>School:</strong> " . e($schoolName) . "</li>
                    <li><strong>Location:</strong> " . e($schoolAddress) . "</li>
                    <li><strong>Expected Salary:</strong> RM " . number_format($expectedSalary, 2) . "</li>
                    <li><strong>Start Date:</strong> To be confirmed upon acceptance</li>
                </ul>
                
                <p>We look forward to welcoming you to our organization and believe that your skills and dedication will make a valuable contribution to our school community.</p>
                
                <p>Kindly submit your response within <strong>7 days</strong> from the date of this offer. Failure to respond within the specified period may result in the withdrawal of this offer.</p>
                
                <br>
                <p>Yours sincerely,</p>
                <div class='signature'>
                    <br><br>
                    <p><strong>HR Administrator</strong><br>
                    Human Resource Department</p>
                </div>
                
                <div class='footer'>
                    This is a system-generated document. No signature is required.
                </div>
            </body>
            </html>";
            
            return $html;
            
        } catch (\Exception $e) {
            Log::error('Error generating PDF: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Preview confirmation letter
     */
    public function previewConfirmationLetter(string $application_id)
    {
        try {
            $application = DB::table('application')
                ->where('application.application_id', $application_id)
                ->select('application.*')
                ->first();
            
            if (!$application) {
                return redirect()->route('confirmation.letters')
                    ->with('error', 'Application not found!');
            }
            
            // Get applicant data
            $applicant = null;
            $applicantId = $application->applicant_id;
            
            if ($applicantId) {
                $applicant = DB::table('applicant')
                    ->where('applicant_id', $applicantId)
                    ->first();
                
                if (!$applicant && strpos($applicantId, 'APP') === 0) {
                    $convertedId = 'A' . substr($applicantId, 3);
                    $applicant = DB::table('applicant')
                        ->where('applicant_id', $convertedId)
                        ->first();
                }
            }
            
            $app = (object) [
                'application_id' => $application->application_id,
                'applicant' => (object) [
                    'full_name' => $applicant ? $applicant->full_name : 'N/A',
                    'email' => $applicant ? $applicant->email : 'N/A',
                ],
                'address' => $application->address ?? 'N/A',
                'ic_number' => $application->ic_number ?? 'N/A',
                'expected_salary' => $application->expected_salary ?? 2500,
            ];
            
            // Generate the confirmation letter content
            $confirmationContent = $this->generateConfirmationLetterContent($app);
            
            // Generate PDF content for database storage
            $pdfContent = $this->generateConfirmationPDFContent($app);
            
            // Create or update document with PDF content
            $documentID = 'CONF-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            
            $existingDocument = DB::table('document')
                ->where('applicant_id', $application->applicant_id)
                ->where('documentType', 'Confirmation Letter')
                ->where('status', 'Draft')
                ->first();
            
            if ($existingDocument) {
                // Update existing document with PDF content
                DB::table('document')
                    ->where('documentID', $existingDocument->documentID)
                    ->update([
                        'pdfFile' => $pdfContent,
                        'documentTitle' => 'Confirmation Letter for ' . ($applicant ? $applicant->full_name : 'Applicant'),
                        'dateIssued' => now(),
                        'documentDescription' => 'Employment confirmation letter'
                    ]);
                
                $document = DB::table('document')
                    ->where('documentID', $existingDocument->documentID)
                    ->first();
            } else {
                // Create new document with PDF content
                DB::table('document')->insert([
                    'documentID' => $documentID,
                    'gn_id' => null,
                    'applicant_id' => $application->applicant_id,
                    'documentType' => 'Confirmation Letter',
                    'documentDescription' => 'Employment confirmation letter',
                    'documentTitle' => 'Confirmation Letter for ' . ($applicant ? $applicant->full_name : 'Applicant'),
                    'dateIssued' => now(),
                    'signedBy' => session('user')->hrid ?? 'HR-001',
                    'hrid' => session('user')->hrid ?? 'HR-001',
                    'teacherID' => null,
                    'pdfFile' => $pdfContent,
                    'filePath' => null,
                    'status' => 'Draft'
                ]);
                
                $document = DB::table('document')
                    ->where('documentID', $documentID)
                    ->first();
            }
            
            return view('placement.confirmation-letter-preview', compact('app', 'application', 'document', 'confirmationContent'));
        } catch (\Exception $e) {
            Log::error('Error previewing confirmation letter: ' . $e->getMessage(), [
                'application_id' => $application_id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to preview confirmation letter: ' . $e->getMessage());
        }
    }

    /**
     * Generate confirmation letter PDF content for database storage
     */
    private function generateConfirmationPDFContent(object $application)
    {
        $applicantName = isset($application->applicant->full_name) ? $application->applicant->full_name : 'N/A';
        $address = $application->address ?? 'N/A';
        $icNumber = $application->ic_number ?? 'N/A';
        $expectedSalary = $application->expected_salary ?? 2500;
        $applicationId = $application->application_id ?? 'N/A';
        $date = date('d F Y');
        
        $html = "
        <html>
        <head>
            <meta charset='utf-8'>
            <title>Confirmation Letter</title>
            <style>
                body {
                    font-family: 'Times New Roman', serif;
                    line-height: 1.8;
                    padding: 40px;
                    max-width: 800px;
                    margin: 0 auto;
                    color: #333;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                }
                .header h2 {
                    font-size: 18px;
                    font-weight: bold;
                    margin-bottom: 5px;
                }
                .header h4 {
                    font-size: 14px;
                    margin-bottom: 5px;
                }
                .header p {
                    font-size: 12px;
                    margin-bottom: 5px;
                }
                .header hr {
                    border: 1px solid #333;
                    margin: 15px 0;
                }
                .reference {
                    text-align: right;
                    margin-bottom: 20px;
                }
                .subject {
                    font-weight: bold;
                    margin: 15px 0;
                }
                ul {
                    padding-left: 20px;
                }
                ul li {
                    margin-bottom: 5px;
                }
                .signature {
                    margin-top: 30px;
                }
                .footer {
                    margin-top: 40px;
                    text-align: center;
                    font-size: 12px;
                    color: #666;
                    border-top: 1px solid #ddd;
                    padding-top: 10px;
                }
                .section-title {
                    font-weight: bold;
                    margin-top: 20px;
                    margin-bottom: 10px;
                }
            </style>
        </head>
        <body>
            <div class='header'>
                <h2>MINISTRY OF EDUCATION MALAYSIA</h2>
                <h4>Human Resource Division</h4>
                <p>Level 4, Block E8, Complex E, Federal Government Administrative Centre<br>
                62604 Putrajaya, Malaysia</p>
                <hr>
                <h3>CONFIRMATION LETTER</h3>
            </div>

            <div class='reference'>
                <p><strong>Reference No:</strong> CONF/" . date('Y') . "/" . str_pad($applicationId, 4, '0', STR_PAD_LEFT) . "</p>
                <p><strong>Date:</strong> " . $date . "</p>
            </div>

            <div>
                <p>
                    <strong>" . e($applicantName) . "</strong><br>
                    " . e($address) . "<br>
                    IC No: " . e($icNumber) . "
                </p>
            </div>

            <div>
                <p>Dear <strong>" . e($applicantName) . "</strong>,</p>
                
                <p class='subject'>RE: CONFIRMATION OF APPOINTMENT AS A TEACHER</p>
                
                <p>We are pleased to confirm your appointment as a <strong>Teacher</strong> at the Ministry of Education Malaysia.</p>
                
                <p class='section-title'>1. Appointment Details</p>
                <p>Your appointment is confirmed effective from <strong>" . $date . "</strong>.</p>
                
                <p class='section-title'>2. Salary</p>
                <p>Your monthly salary is <strong>RM " . number_format($expectedSalary, 2) . "</strong>.</p>
                
                <p class='section-title'>3. Probation Period</p>
                <p>You have successfully completed your probation period.</p>
                
                <p class='section-title'>4. Next Steps</p>
                <ul>
                    <li>You will be assigned to a school within 7 working days</li>
                    <li>You will receive orientation details via email</li>
                    <li>Please report to your assigned school on the given date</li>
                </ul>
                
                <p>Congratulations and welcome to the team!</p>
                
                <p>Yours sincerely,</p>
                <div class='signature'>
                    <br><br>
                    <p><strong>_________________________</strong><br>
                    <strong>Director of Human Resources</strong><br>
                    Ministry of Education Malaysia</p>
                </div>
            </div>
            
            <div class='footer'>
                This is a system-generated document. No signature is required.
            </div>
        </body>
        </html>";
        
        return $html;
    }

    /**
     * Generate confirmation letter content for display (HTML without full page structure)
     */
    private function generateConfirmationLetterContent(object $application)
    {
        $applicantName = isset($application->applicant->full_name) ? $application->applicant->full_name : 'N/A';
        $address = $application->address ?? 'N/A';
        $icNumber = $application->ic_number ?? 'N/A';
        $expectedSalary = $application->expected_salary ?? 2500;
        
        return "
        <div class='confirmation-letter-content' style='font-family: \"Times New Roman\", serif; line-height: 1.6;'>
            <div class='text-center mb-5'>
                <h2>MINISTRY OF EDUCATION MALAYSIA</h2>
                <h4>Human Resource Division</h4>
                <p>Level 4, Block E8, Complex E, Federal Government Administrative Centre<br>
                62604 Putrajaya, Malaysia</p>
                <hr>
                <h3>CONFIRMATION LETTER</h3>
                <p><strong>Reference No:</strong> CONF/" . date('Y') . "/" . str_pad($application->application_id ?? 0, 4, '0', STR_PAD_LEFT) . "</p>
            </div>

            <div class='mb-4'>
                <p><strong>Date:</strong> " . date('d F Y') . "</p>
            </div>

            <div class='mb-4'>
                <p>
                    <strong>" . e($applicantName) . "</strong><br>
                    " . e($address) . "<br>
                    IC No: " . e($icNumber) . "
                </p>
            </div>

            <div class='mb-4'>
                <p>Dear <strong>" . e($applicantName) . "</strong>,</p>
                
                <p><strong>RE: CONFIRMATION OF APPOINTMENT AS A TEACHER</strong></p>
                
                <p>We are pleased to confirm your appointment as a <strong>Teacher</strong> at the Ministry of Education Malaysia.</p>
                
                <h5 class='mt-4'>1. Appointment Details</h5>
                <p>Your appointment is confirmed effective from <strong>" . date('d F Y') . "</strong>.</p>
                
                <h5>2. Salary</h5>
                <p>Your monthly salary is <strong>RM " . number_format($expectedSalary, 2) . "</strong>.</p>
                
                <h5>3. Probation Period</h5>
                <p>You have successfully completed your probation period.</p>
                
                <h5>4. Next Steps</h5>
                <ul>
                    <li>You will be assigned to a school within 7 working days</li>
                    <li>You will receive orientation details via email</li>
                    <li>Please report to your assigned school on the given date</li>
                </ul>
                
                <p>Congratulations and welcome to the team!</p>
                
                <p>Yours sincerely,</p>
                <div class='mt-5'>
                    <p><strong>_________________________</strong><br>
                    <strong>Director of Human Resources</strong><br>
                    Ministry of Education Malaysia</p>
                </div>
            </div>
        </div>";
    }

    /**
     * Send confirmation letter
     */
    public function sendConfirmationLetter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'application_id' => 'required|exists:application,application_id',
            'confirmation_date' => 'required|date|after_or_equal:today'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        try {
            DB::beginTransaction();
            
            $application = DB::table('application')
                           ->where('application_id', $request->application_id)
                           ->first();
            
            if (!$application) {
                throw new \Exception('Application not found!');
            }
            
            // Get applicant data for generating content
            $applicant = null;
            $applicantId = $application->applicant_id;
            
            if ($applicantId) {
                $applicant = DB::table('applicant')
                    ->where('applicant_id', $applicantId)
                    ->first();
                
                if (!$applicant && strpos($applicantId, 'APP') === 0) {
                    $convertedId = 'A' . substr($applicantId, 3);
                    $applicant = DB::table('applicant')
                        ->where('applicant_id', $convertedId)
                        ->first();
                }
            }
            
            // Prepare application object for PDF generation
            $app = (object) [
                'application_id' => $application->application_id,
                'applicant' => (object) [
                    'full_name' => $applicant ? $applicant->full_name : 'N/A',
                    'email' => $applicant ? $applicant->email : 'N/A',
                ],
                'address' => $application->address ?? 'N/A',
                'ic_number' => $application->ic_number ?? 'N/A',
                'expected_salary' => $application->expected_salary ?? 2500,
            ];
            
            // Generate PDF content
            $pdfContent = $this->generateConfirmationPDFContent($app);
            
            // Get the document - find by applicant_id and documentType
            $document = DB::table('document')
                        ->where('applicant_id', $application->applicant_id)
                        ->where('documentType', 'Confirmation Letter')
                        ->where('status', 'Draft')
                        ->first();
            
            if (!$document) {
                // Create document if not exists
                $documentID = 'CONF-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
                
                DB::table('document')->insert([
                    'documentID' => $documentID,
                    'gn_id' => null,
                    'applicant_id' => $application->applicant_id,
                    'documentType' => 'Confirmation Letter',
                    'documentDescription' => 'Employment confirmation letter',
                    'documentTitle' => 'Confirmation Letter for ' . ($applicant ? $applicant->full_name : 'Applicant'),
                    'dateIssued' => $request->confirmation_date,
                    'signedBy' => session('user')->hrid ?? 'HR-001',
                    'hrid' => session('user')->hrid ?? 'HR-001',
                    'teacherID' => null,
                    'pdfFile' => $pdfContent,
                    'filePath' => null,
                    'status' => 'Sent'
                ]);
            } else {
                // Update existing document with PDF content and status
                DB::table('document')
                  ->where('documentID', $document->documentID)
                  ->update([
                      'status' => 'Sent',
                      'dateIssued' => $request->confirmation_date,
                      'pdfFile' => $pdfContent
                  ]);
            }
            
            // Update application confirmation_response to 'pending'
            DB::table('application')
              ->where('application_id', $request->application_id)
              ->update([
                  'confirmation_response' => 'pending'
              ]);
            
            // Try to find guru_new record
            $guruNew = DB::table('guru_new')
                       ->where('application_id', $request->application_id)
                       ->first();
            
            if ($guruNew) {
                DB::table('guru_new')
                  ->where('gn_id', $guruNew->gn_id)
                  ->update([
                      'current_status' => 'Active'
                  ]);
            } else {
                DB::table('guru_new')->insert([
                    'current_status' => 'Active',
                    'join_date' => now(),
                    'class' => null,
                    'subject_teaching' => null,
                    'applicant_id' => $application->applicant_id,
                    'application_id' => $request->application_id,
                    'schoolID' => $application->schoolID ?? null,
                    'assign_status' => 'pending'
                ]);
            }
            
            DB::commit();
            
            // Redirect to confirmation tracking page with success message
            return redirect()->route('confirmation.tracking')
                ->with('success', 'Confirmation letter sent successfully!');
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error sending confirmation letter: ' . $e->getMessage(), [
                'application_id' => $request->application_id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to send confirmation letter: ' . $e->getMessage());
        }
    }
    
    /**
     * Confirmation tracking - shows all confirmation letters with their status
     * Status is based on confirmation_response from application table
     */
    public function confirmationTracking()
    {
        try {
            // Get all confirmation letters with their status
            $confirmations = DB::table('document')
                ->where('document.documentType', 'Confirmation Letter')
                ->select('document.*')
                ->orderBy('document.dateIssued', 'desc')
                ->paginate(20);

            // Get applicant data and application status for each confirmation
            foreach ($confirmations as $confirmation) {
                // Get applicant data
                $applicant = null;
                $applicantId = $confirmation->applicant_id;
                
                if ($applicantId) {
                    $applicant = DB::table('applicant')
                        ->where('applicant_id', $applicantId)
                        ->first();
                    
                    if (!$applicant && strpos($applicantId, 'APP') === 0) {
                        $convertedId = 'A' . substr($applicantId, 3);
                        $applicant = DB::table('applicant')
                            ->where('applicant_id', $convertedId)
                            ->first();
                    }
                    
                    if (!$applicant && strpos($applicantId, 'APP') === 0) {
                        $convertedId = 'APL' . substr($applicantId, 3);
                        $applicant = DB::table('applicant')
                            ->where('applicant_id', $convertedId)
                            ->first();
                    }
                }
                
                $confirmation->full_name = $applicant ? $applicant->full_name : 'N/A';
                $confirmation->applicant_email = $applicant ? $applicant->email : 'No email found';
                
                // Get the application to check confirmation_response status
                $application = DB::table('application')
                    ->where('applicant_id', $applicantId)
                    ->first();
                
                // PRIMARY SOURCE OF TRUTH: confirmation_response from application
                if ($application) {
                    $confirmation->confirmation_response = $application->confirmation_response ?? 'draft';
                    $confirmation->application_id = $application->application_id;
                } else {
                    $confirmation->confirmation_response = 'draft';
                    $confirmation->application_id = null;
                }
                
                // Determine display status based SOLELY on confirmation_response
                $response = $confirmation->confirmation_response;
                
                if ($response === 'accepted') {
                    $confirmation->display_status = 'Accepted';
                    $confirmation->status_badge_class = 'badge-accepted';
                } elseif ($response === 'rejected') {
                    $confirmation->display_status = 'Rejected';
                    $confirmation->status_badge_class = 'badge-rejected';
                } elseif ($response === 'pending') {
                    $confirmation->display_status = 'Pending';
                    $confirmation->status_badge_class = 'badge-pending';
                } else {
                    // draft or null
                    $confirmation->display_status = 'Draft';
                    $confirmation->status_badge_class = 'badge-draft';
                }
            }
            
            // Get statistics for the dashboard
            // Count based on confirmation_response from application table
            $acceptedCount = DB::table('application')
                ->where('confirmation_response', 'accepted')
                ->count();
            
            $rejectedCount = DB::table('application')
                ->where('confirmation_response', 'rejected')
                ->count();
            
            $pendingCount = DB::table('application')
                ->where('confirmation_response', 'pending')
                ->count();
            
            $draftCount = DB::table('application')
                ->where(function($query) {
                    $query->whereNull('confirmation_response')
                        ->orWhere('confirmation_response', 'draft')
                        ->orWhere('confirmation_response', '');
                })
                ->count();
            
            // Total sent = pending + accepted + rejected (any that have a response)
            $totalSent = $pendingCount + $acceptedCount + $rejectedCount;
            
            return view('placement.confirmation-tracking', compact(
                'confirmations',
                'totalSent',
                'draftCount',
                'acceptedCount',
                'rejectedCount',
                'pendingCount'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading confirmation tracking: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to load confirmation tracking: ' . $e->getMessage());
        }
    }
    
    /**
     * Accept confirmation letter (applicant accepts the confirmation)
     */
    public function acceptConfirmation(string $application_id)
    {
        try {
            DB::beginTransaction();
            
            $application = DB::table('application')
                ->where('application_id', $application_id)
                ->first();
            
            if (!$application) {
                throw new \Exception('Application not found!');
            }
            
            // Update confirmation response to 'accepted'
            DB::table('application')
                ->where('application_id', $application_id)
                ->update([
                    'confirmation_response' => 'accepted'
                ]);
            
            // Update the document status to 'Accepted'
            DB::table('document')
                ->where('applicant_id', $application->applicant_id)
                ->where('documentType', 'Confirmation Letter')
                ->update([
                    'status' => 'Accepted'
                ]);
            
            DB::commit();
            
            return redirect()->route('confirmation.tracking')
                ->with('success', 'Confirmation has been accepted successfully!');
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error accepting confirmation: ' . $e->getMessage(), [
                'application_id' => $application_id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to accept confirmation: ' . $e->getMessage());
        }
    }
    
    /**
     * Reject confirmation letter (applicant rejects the confirmation)
     */
    public function rejectConfirmation(string $application_id)
    {
        try {
            DB::beginTransaction();
            
            $application = DB::table('application')
                ->where('application_id', $application_id)
                ->first();
            
            if (!$application) {
                throw new \Exception('Application not found!');
            }
            
            // Update confirmation response to 'rejected'
            DB::table('application')
                ->where('application_id', $application_id)
                ->update([
                    'confirmation_response' => 'rejected'
                ]);
            
            // Update the document status to 'Rejected'
            DB::table('document')
                ->where('applicant_id', $application->applicant_id)
                ->where('documentType', 'Confirmation Letter')
                ->update([
                    'status' => 'Rejected'
                ]);
            
            DB::commit();
            
            return redirect()->route('confirmation.tracking')
                ->with('success', 'Confirmation has been rejected.');
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error rejecting confirmation: ' . $e->getMessage(), [
                'application_id' => $application_id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to reject confirmation: ' . $e->getMessage());
        }
    }
    
    /**
     * ==================== STEP 5: CONVERT TO TEACHER ====================
     */
    public function convertToTeacher(string $application_id)
    {
        DB::beginTransaction();
        
        try {
            $application = DB::table('application')
                           ->where('application_id', $application_id)
                           ->first();
            
            if (!$application) {
                throw new \Exception('Application not found!');
            }
            
            // Get applicant data
            $applicant = null;
            $applicantId = $application->applicant_id;
            
            if ($applicantId) {
                $applicant = DB::table('applicant')
                    ->where('applicant_id', $applicantId)
                    ->first();
                
                if (!$applicant && strpos($applicantId, 'APP') === 0) {
                    $convertedId = 'A' . substr($applicantId, 3);
                    $applicant = DB::table('applicant')
                        ->where('applicant_id', $convertedId)
                        ->first();
                }
            }
            
            if (!$applicant) {
                throw new \Exception('Applicant data not found!');
            }
            
            $existingTeacher = DB::table('teacher')
                              ->where('email', $applicant->email)
                              ->first();
            
            if ($existingTeacher) {
                throw new \Exception('This applicant is already a teacher! Teacher ID: ' . $existingTeacher->teacherID);
            }
            
            $year = date('Y');
            $lastTeacher = DB::table('teacher')->orderBy('teacherID', 'desc')->first();
            $lastNum = $lastTeacher ? intval(substr($lastTeacher->teacherID, -4)) : 0;
            $newNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
            $teacherID = 'TCH' . $year . $newNum;
            
            DB::table('teacher')->insert([
                'teacherID' => $teacherID,
                'teacherName' => $applicant->full_name,
                'email' => $applicant->email,
                'password' => bcrypt('password123'),
                'role' => 'teacher',
                'password_change_required' => 1
            ]);
            
            DB::table('application')
              ->where('application_id', $application_id)
              ->update([
                  'application_status' => 'Accepted',
                  'confirmation_response' => 'accepted'
              ]);
            
            DB::commit();
            
            return redirect()->route('confirmation.tracking')
                ->with('success', "Applicant converted to Teacher successfully! Teacher ID: {$teacherID}");
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Teacher conversion failed: ' . $e->getMessage(), [
                'application_id' => $application_id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to convert: ' . $e->getMessage());
        }
    }

    /**
     * Get offer response statistics
     */
    public function getOfferStatistics()
    {
        try {
            $stats = [
                'not_offer' => DB::table('application')
                              ->where('application_status', 'Accepted')
                              ->where(function($query) {
                                  $query->where('offer_response', 'not_offer')
                                        ->orWhereNull('offer_response')
                                        ->orWhere('offer_response', '');
                              })
                              ->count(),
                'pending' => DB::table('application')
                            ->where('application_status', 'Accepted')
                            ->where('offer_response', 'pending')
                            ->count(),
                'accepted' => DB::table('application')
                             ->where('application_status', 'Accepted')
                             ->where('offer_response', 'accepted')
                             ->count(),
                'rejected' => DB::table('application')
                             ->where('application_status', 'Accepted')
                             ->where('offer_response', 'rejected')
                             ->count(),
                'total' => DB::table('application')
                          ->where('application_status', 'Accepted')
                          ->count()
            ];
            
            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Error fetching offer statistics: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch statistics'], 500);
        }
    }

    /**
     * Get confirmation response statistics
     */
    public function getConfirmationStatistics()
    {
        try {
            $stats = [
                'pending' => DB::table('application')
                            ->where('confirmation_response', 'pending')
                            ->count(),
                'accepted' => DB::table('application')
                             ->where('confirmation_response', 'accepted')
                             ->count(),
                'rejected' => DB::table('application')
                             ->where('confirmation_response', 'rejected')
                             ->count(),
                'total' => DB::table('application')
                          ->whereNotNull('confirmation_response')
                          ->count()
            ];
            
            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Error fetching confirmation statistics: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch statistics'], 500);
        }
    }

    /**
     * Generate offer letter content
     */
    private function generateOfferLetterContent(object $application)
    {
        $applicantName = isset($application->applicant->full_name) ? strtoupper($application->applicant->full_name) : 'N/A';
        $address = $application->address ?? 'N/A';
        $icNumber = $application->ic_number ?? 'N/A';
        $expectedSalary = $application->expected_salary ?? 2500;
        
        // Get school name from the school object
        $schoolName = isset($application->school->schoolName) ? $application->school->schoolName : 'To be assigned';
        $schoolAddress = isset($application->school->schoolAddress) ? $application->school->schoolAddress : '';
        
        $applicationId = $application->application_id ?? 'N/A';
        $date = date('d F Y');
        
        return "
        <div class='offer-letter-content' style='font-family: \"Times New Roman\", serif; line-height: 1.8; max-width: 800px; margin: 0 auto; padding: 40px;'>
            
            <h2 style='text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 5px;'>OFFER LETTER</h2>
            
            <p style='text-align: right; margin-bottom: 30px;'>Date: " . $date . "</p>
            
            <p>Dear <strong>" . e($applicantName) . "</strong>,</p>
            
            <p><strong>Subject:</strong> Offer of Employment as Teacher</p>
            
            <p>We are pleased to inform you that after careful consideration of your application, qualifications, and interview performance, you have been selected for employment at <strong>" . e($schoolName) . "</strong> as a Teacher.</p>
            
            <p>Please review this offer carefully. By accepting this offer, you agree to comply with all school policies, regulations, and employment requirements.</p>
            
            <p><strong>Offer Details:</strong></p>
            <ul>
                <li><strong>Position:</strong> Teacher</li>
                <li><strong>School:</strong> " . e($schoolName) . "</li>
                <li><strong>Location:</strong> " . e($schoolAddress) . "</li>
                <li><strong>Expected Salary:</strong> RM " . number_format($expectedSalary, 2) . "</li>
                <li><strong>Start Date:</strong> To be confirmed upon acceptance</li>
            </ul>
            
            <p>We look forward to welcoming you to our organization and believe that your skills and dedication will make a valuable contribution to our school community.</p>
            
            <p>Kindly submit your response within <strong>7 days</strong> from the date of this offer. Failure to respond within the specified period may result in the withdrawal of this offer.</p>
            
            <br>
            <p>Yours sincerely,</p>
            <br><br>
            <p><strong>HR Administrator</strong><br>
            Human Resource Department</p>
            
            <hr style='margin-top: 40px; border: 1px solid #ddd;'>
            <p style='font-size: 12px; color: #666; text-align: center;'>This is a system-generated document. No signature is required.</p>
        </div>";
    }
    
    /**
     * Offer tracking
     */
    public function offerTracking()
    {
        try {
            $applications = DB::table('application')
                ->where('application.application_status', 'Accepted')
                ->whereIn('application.offer_response', ['pending', 'accepted', 'rejected'])
                ->select('application.*')
                ->orderBy('application.application_date', 'desc')
                ->paginate(20);

            // Get applicant data for each application
            foreach ($applications as $application) {
                $applicant = null;
                $applicantId = $application->applicant_id;
                
                if ($applicantId) {
                    $applicant = DB::table('applicant')
                        ->where('applicant_id', $applicantId)
                        ->first();
                    
                    if (!$applicant && strpos($applicantId, 'APP') === 0) {
                        $convertedId = 'A' . substr($applicantId, 3);
                        $applicant = DB::table('applicant')
                            ->where('applicant_id', $convertedId)
                            ->first();
                    }
                }
                
                $application->full_name = $applicant ? $applicant->full_name : 'N/A';
                $application->applicant_email = $applicant ? $applicant->email : 'No email found';
            }
            
            $pendingApplications = DB::table('application')
                ->where('application_status', 'Accepted')
                ->where('offer_response', 'pending')
                ->count();
            
            $acceptedApplications = DB::table('application')
                ->where('application_status', 'Accepted')
                ->where('offer_response', 'accepted')
                ->count();
            
            $rejectedApplications = DB::table('application')
                ->where('application_status', 'Accepted')
                ->where('offer_response', 'rejected')
                ->count();
            
            $notOfferedCount = DB::table('application')
                ->where('application_status', 'Accepted')
                ->where(function($query) {
                    $query->where('offer_response', 'not_offer')
                        ->orWhereNull('offer_response')
                        ->orWhere('offer_response', '');
                })
                ->count();
            
            return view('placement.offer-tracking', compact(
                'applications', 
                'pendingApplications',
                'acceptedApplications',
                'rejectedApplications',
                'notOfferedCount'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading offer tracking: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to load offer tracking: ' . $e->getMessage());
        }
    }
    
    /**
     * ==================== STEP 3: APPLICANT RESPONDS ====================
     */
    
    public function acceptOffer(string $application_id)
    {
        try {
            DB::beginTransaction();
            
            $application = DB::table('application')
                           ->where('application_id', $application_id)
                           ->first();
            
            if (!$application) {
                throw new \Exception('Application not found!');
            }
            
            if ($application->schoolID) {
                $school = DB::table('school')
                          ->where('schoolID', $application->schoolID)
                          ->first();
                
                if ($school && $school->vacancy > 0) {
                    DB::table('school')
                      ->where('schoolID', $application->schoolID)
                      ->decrement('vacancy');
                    
                    DB::table('school')
                      ->where('schoolID', $application->schoolID)
                      ->increment('totalTeacher');
                } else {
                    throw new \Exception('The assigned school no longer has vacancies!');
                }
            }
            
            DB::table('application')
              ->where('application_id', $application_id)
              ->update([
                  'offer_response' => 'accepted',
                  'confirmation_response' => 'accepted'
              ]);
            
            $guruNewExists = DB::table('guru_new')
                             ->where('applicant_id', $application->applicant_id)
                             ->where('application_id', $application_id)
                             ->exists();
            
            if (!$guruNewExists) {
                DB::table('guru_new')->insert([
                    'current_status' => 'Active',
                    'join_date' => now(),
                    'class' => null,
                    'subject_teaching' => null,
                    'applicant_id' => $application->applicant_id,
                    'application_id' => $application_id,
                    'schoolID' => $application->schoolID ?? null,
                    'assign_status' => 'pending'
                ]);
            }
            
            DB::commit();
            
            return view('placement.offer-accepted', compact('application'));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error accepting offer: ' . $e->getMessage());
            return redirect()->route('offer.tracking')
                ->with('error', 'Failed to accept offer: ' . $e->getMessage());
        }
    }
    
    public function rejectOffer(string $application_id)
    {
        try {
            DB::table('application')
              ->where('application_id', $application_id)
              ->update([
                  'offer_response' => 'rejected'
              ]);
            
            $application = DB::table('application')
                           ->where('application_id', $application_id)
                           ->first();
            
            if (!$application) {
                abort(404, 'Application not found');
            }
            
            return view('placement.offer-rejected', compact('application'));
        } catch (\Exception $e) {
            Log::error('Error rejecting offer: ' . $e->getMessage());
            return redirect()->route('offer.tracking')
                ->with('error', 'Failed to reject offer.');
        }
    }
    
/**
 * ==================== STEP 4: SEND CONFIRMATION LETTERS ====================
 * Show applicants who accepted the offer, with their confirmation response status
 */
public function confirmationLetters()
{
    try {
        // Get ALL applicants who accepted the offer, with their data
        $applications = DB::table('application')
            ->leftJoin('applicant', 'application.applicant_id', '=', 'applicant.applicant_id')
            ->where('application.offer_response', 'accepted')
            ->select(
                'application.*',
                'applicant.full_name',
                'applicant.email as applicant_email',
                'applicant.phone_number'
            )
            // FIXED: Use 'application_date' instead of 'updated_at'
            ->orderBy('application.application_date', 'desc')
            ->paginate(20);

        // Add confirmation_response status to each application
        foreach ($applications as $app) {
            // Get the confirmation response status
            $response = $app->confirmation_response ?? 'draft';
            
            // Determine display status
            if ($response === 'accepted') {
                $app->confirmation_status = 'Accepted';
                $app->status_badge_class = 'badge-accepted';
                $app->status_icon = '✅';
            } elseif ($response === 'rejected') {
                $app->confirmation_status = 'Rejected';
                $app->status_badge_class = 'badge-rejected';
                $app->status_icon = '❌';
            } elseif ($response === 'pending') {
                $app->confirmation_status = 'Pending';
                $app->status_badge_class = 'badge-pending';
                $app->status_icon = '⏳';
            } else {
                $app->confirmation_status = 'Not Sent';
                $app->status_badge_class = 'badge-draft';
                $app->status_icon = '📄';
            }
            
            // Store the response for action buttons
            $app->confirmation_response = $response;
        }
        
        // Get statistics
        $totalAccepted = DB::table('application')
            ->where('offer_response', 'accepted')
            ->count();
        
        $alreadyConfirmed = DB::table('application')
            ->where('offer_response', 'accepted')
            ->where('confirmation_response', 'accepted')
            ->count();
        
        $pendingConfirmation = DB::table('application')
            ->where('offer_response', 'accepted')
            ->where(function($query) {
                $query->whereNull('confirmation_response')
                    ->orWhere('confirmation_response', 'draft')
                    ->orWhere('confirmation_response', 'pending');
            })
            ->count();
        
        $rejectedConfirmation = DB::table('application')
            ->where('offer_response', 'accepted')
            ->where('confirmation_response', 'rejected')
            ->count();
        
        return view('placement.confirmation-letters', compact(
            'applications',
            'totalAccepted',
            'alreadyConfirmed',
            'pendingConfirmation',
            'rejectedConfirmation'
        ));
        
    } catch (\Exception $e) {
        Log::error('Error loading confirmation letters: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        return back()->with('error', 'Failed to load confirmation letters: ' . $e->getMessage());
    }
}

    /**
     * Send offer letter
     */
    public function sendOfferLetter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'application_id' => 'required|exists:application,application_id',
            'document_id' => 'required',
            'offer_date' => 'required|date|after_or_equal:today'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        try {
            DB::beginTransaction();
            
            $application = DB::table('application')
                           ->where('application_id', $request->application_id)
                           ->first();
            
            if (!$application) {
                throw new \Exception('Application not found!');
            }
            
            DB::table('document')
              ->where('documentID', $request->document_id)
              ->update([
                  'status' => 'Sent',
                  'dateIssued' => $request->offer_date
              ]);
            
            DB::table('application')
              ->where('application_id', $request->application_id)
              ->update([
                  'offer_response' => 'pending'
              ]);
            
            DB::commit();
            
            return redirect()->route('offer.tracking')
                ->with('success', 'Offer letter sent successfully!');
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error sending offer letter: ' . $e->getMessage());
            return back()->with('error', 'Failed to send offer letter: ' . $e->getMessage());
        }
    }
}