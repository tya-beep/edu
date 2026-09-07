<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Teacher;
use App\Models\Staff;
use App\Models\Principal;
use App\Models\HrAdministrator;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        // First, check if the email exists in any table
        $user = Teacher::where('email', $request->email)->first()
            ?? Staff::where('email', $request->email)->first()
            ?? Principal::where('email', $request->email)->first()
            ?? HrAdministrator::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Email not found');
        }

        // ==========================================
        // CHECK IF USER IS RESIGNED/TERMINATED
        // ==========================================
        if (isset($user->status) && strtolower($user->status) === 'berhenti') {
            return back()->with('error', 'Your account has been terminated/resigned. Please contact the administrator for assistance.');
        }

        if (isset($user->assign_status) && strtolower($user->assign_status) === 'berhenti') {
            return back()->with('error', 'Your account has been terminated/resigned. Please contact the administrator for assistance.');
        }

        if (isset($user->is_active) && $user->is_active == 0) {
            return back()->with('error', 'Your account has been deactivated. Please contact the administrator for assistance.');
        }

        // Check password
        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Wrong password');
        }

        // If user is a teacher, check their ASSIGN status as well
        if (isset($user->role) && strtolower($user->role) === 'teacher') {
            $teacherStatus = Teacher::join('ASSIGN', 'Teacher.teacherID', '=', 'ASSIGN.teacherID')
                                   ->where('Teacher.teacherID', $this->getUserId($user))
                                   ->where('ASSIGN.status', 'Berhenti')
                                   ->first();
            
            if ($teacherStatus) {
                return back()->with('error', 'Your teacher account has been resigned. Please contact the administrator for assistance.');
            }
        }

        // ==========================================
        // LOGIN SUCCESSFUL - Create Session
        // ==========================================
        Session::put('user', $user);
        Session::put('userID', $this->getUserId($user));
        Session::put('userName', $this->getUserName($user));
        Session::put('role', strtolower($user->role));
        Session::put('email', $user->email); // Add email to session
        
        if (isset($user->schoolID)) {
            Session::put('schoolID', $user->schoolID);
        }

        // Check if password change is required
        if (isset($user->password_change_required) && $user->password_change_required == 1) {
            return redirect()->route('change.password');
        }

        return $this->redirectByRole($user->role);
    }

    private function getUserId($user)
    {
        $role = strtolower($user->role);
        return match($role) {
            'teacher' => $user->teacherID ?? null,
            'staff' => $user->staffID ?? null,
            'principal' => $user->principalID ?? null,
            'hr' => $user->hrid ?? null,
            'hr_administrator' => $user->hrid ?? null,
            default => null,
        };
    }

    private function getUserName($user)
    {
        $role = strtolower($user->role);
        return match($role) {
            'teacher' => $user->teacherName ?? null,
            'staff' => $user->staffName ?? null,
            'principal' => $user->principalName ?? null,
            'hr' => $user->username ?? null,
            'hr_administrator' => $user->username ?? null,
            default => null,
        };
    }

    public function showChangePassword()
    {
        if (!Session::has('user')) {
            return redirect()->route('login');
        }
        return view('auth.change-password');
    }

public function changePassword(Request $request)
{
    $request->validate([
        'password' => 'required|min:8|confirmed'
    ]);

    $user = Session::get('user');
    $role = strtolower(Session::get('role'));
    $email = Session::get('email');

    $record = null;
    
    switch($role) {
        case 'teacher':
            $record = Teacher::where('email', $email)->first();
            break;
        case 'staff':
            $record = Staff::where('email', $email)->first();
            break;
        case 'principal':
            $record = Principal::where('email', $email)->first();
            break;
        case 'hr':
        case 'hr_administrator':
            $record = HrAdministrator::where('email', $email)->first();
            break;
        default:
            $record = null;
    }

    if (!$record) {
        return back()->with('error', 'User record could not be found.');
    }

    // Hash the password manually (since we removed the mutator)
    $record->password = Hash::make($request->password);
    $record->password_change_required = 0;
    $record->save();

    // Refresh and update session with fresh data
    $record->refresh();
    Session::put('user', $record);
    Session::put('userID', $this->getUserId($record));
    Session::put('userName', $this->getUserName($record));
    Session::put('role', strtolower($record->role));
    Session::put('email', $record->email);
    
    if (isset($record->schoolID)) {
        Session::put('schoolID', $record->schoolID);
    }

    return $this->redirectByRole($record->role)->with('success', 'Password updated successfully');
}

    private function redirectByRole($role)
    {
        $role = strtolower($role);
        return match($role) {
            'teacher' => redirect()->route('teacher.dashboard'),
            'principal' => redirect()->route('principal.dashboard'),
            'staff' => redirect()->route('staff.dashboard'),
            'hr' => redirect()->route('hr.home'),
            'hr_administrator' => redirect()->route('hr.home'),
            default => redirect()->route('login')->with('error', 'Invalid role: ' . $role),
        };
    }

    public function logout()
    {
        Session::flush();
        return redirect()->route('login');
    }
}