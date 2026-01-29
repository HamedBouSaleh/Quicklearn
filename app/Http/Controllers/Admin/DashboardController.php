<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Calculate stats
        $totalUsers = User::count();
        $totalCourses = Course::count();
        $totalInstructors = User::where('role', 'instructor')->count();
        $totalStudents = User::where('role', 'student')->count();

        // Get recent users
        $users = User::latest()
            ->take(10)
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => ucfirst($user->role),
                    'created_at' => $user->created_at->format('M d, Y')
                ];
            });

        // Recent activity
        $recentActivity = Enrollment::with(['user', 'course'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function($enrollment) {
                return [
                    'id' => $enrollment->id,
                    'description' => "{$enrollment->user->name} enrolled in {$enrollment->course->title}",
                    'date' => $enrollment->created_at ? $enrollment->created_at->diffForHumans() : 'Unknown'
                ];
            })->toArray();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_users' => $totalUsers,
                'total_courses' => $totalCourses,
                'total_instructors' => $totalInstructors,
                'total_students' => $totalStudents
            ],
            'users' => $users,
            'recentActivity' => $recentActivity
        ]);
    }
}
