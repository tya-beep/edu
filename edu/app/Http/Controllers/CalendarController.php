<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSession;
use Illuminate\Http\Request;


class CalendarController extends Controller
{
    public function index()
    {
        $started = CourseSession::count();

        $progress = CourseSession::whereMonth(
            'session_date',
            now()->month
        )->count();

        $complete = CourseSession::whereDate(
            'session_date',
            '<',
            now()
        )->count();

        $upcomingSessions = CourseSession::with('course')
            ->whereDate('session_date', '>=', now())
            ->orderBy('session_date')
            ->take(5)
            ->get();

        return view('calendar.index', compact(
            'started',
            'progress',
            'complete',
            'upcomingSessions'
        ));
    }

    public function getEvents()
{
    $sessions = CourseSession::with('course')->get();

    return response()->json(
        $sessions->map(function ($session) {

            $course = $session->course;

            if (!$course) return null;

            $color = $this->getCategoryColor($course->course_type);

            return [
                'id' => $session->session_id,
                'title' => $course->course_name,

                'start' => $session->session_date . 'T' . $session->start_time,
                'end' => $session->session_date . 'T' . $session->end_time,

                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#ffffff',

                'extendedProps' => [
                    'location' => $session->location,
                    'category' => $course->course_type,
                    'organiser' => $course->organiser_name,
                    'mode' => $course->course_mode,
                    'price' => $course->price,
                    'capacity' => $course->capacity,
                    'description' => $course->description
                ]
            ];
        })->filter()
    );
}
    private function getCategoryColor($type)
    {
        return match ($type) {

            'Inspiring Parents' => '#14b8a6',

            'Kursus Guru' => '#3b82f6',

            'Kursus Guru Baharu' => '#8b5cf6',

            default => '#6b7280',
        };
    }
}