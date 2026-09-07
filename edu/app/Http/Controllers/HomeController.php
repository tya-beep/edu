<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Staff;
use App\Models\Principal;
use App\Models\School;

class HomeController extends Controller
{
    public function index()
    {
        // Get counts - using correct column names
        $totalTeachers = Teacher::count();
        $totalStaff = Staff::count();
        $totalPrincipals = Principal::count();
        $totalSchools = School::count();
        
        // Active counts - using appropriate status fields
        // For teachers, check if they have a resignation request status
        // If no resignation request or status is not 'approved', consider them active
        $activeTeachers = Teacher::where(function($query) {
            $query->whereNull('resignation_request_status')
                  ->orWhere('resignation_request_status', '!=', 'approved');
        })->count();
        
        // For staff - similar logic
        $activeStaff = Staff::where(function($query) {
            $query->whereNull('resignation_request_status')
                  ->orWhere('resignation_request_status', '!=', 'approved');
        })->count();
        
        // For principals - similar logic
        $activePrincipals = Principal::where(function($query) {
            $query->whereNull('resignation_request_status')
                  ->orWhere('resignation_request_status', '!=', 'approved');
        })->count();
        
        $activeEmployees = $activeTeachers + $activeStaff + $activePrincipals;
        
        // Total employees
        $totalEmployees = $totalTeachers + $totalStaff + $totalPrincipals;
        
        // Pending items - using resignation_request_status
        $pendingTeachers = Teacher::where('resignation_request_status', 'pending')->count();
        $pendingStaff = Staff::where('resignation_request_status', 'pending')->count();
        $pendingPrincipals = Principal::where('resignation_request_status', 'pending')->count();
        $pendingCount = $pendingTeachers + $pendingStaff + $pendingPrincipals;
        
        // Sample recent activities
        $recentActivities = collect([
            (object) [
                'icon' => 'fa-user-plus',
                'title' => 'New Teacher Registered',
                'description' => 'A new teacher was added to the system',
                'created_at' => now()->subHours(2)
            ],
            (object) [
                'icon' => 'fa-file-signature',
                'title' => 'Offer Letter Sent',
                'description' => 'An offer letter was sent to an applicant',
                'created_at' => now()->subHours(5)
            ],
            (object) [
                'icon' => 'fa-school',
                'title' => 'School Updated',
                'description' => 'School information was updated',
                'created_at' => now()->subDay()
            ],
        ]);
        
        // Sample upcoming events
        $upcomingEvents = collect([
            (object) ['title' => 'Staff Meeting', 'date' => now()->addDays(5)],
            (object) ['title' => 'Training Session', 'date' => now()->addDays(10)],
            (object) ['title' => 'Performance Review', 'date' => now()->addDays(15)],
        ]);
        
        return view('home', compact(
            'totalEmployees',
            'activeEmployees',
            'pendingCount',
            'totalTeachers',
            'totalStaff',
            'totalPrincipals',
            'totalSchools',
            'recentActivities',
            'upcomingEvents'
        ));
    }
}