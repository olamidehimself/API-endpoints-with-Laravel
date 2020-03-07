<?php

namespace App\Http\Controllers\api;

use App\Course;
use Carbon\Carbon;
use Faker\Factory;
use App\UserCourse;
use Illuminate\Http\Request;
use App\Exports\CoursesExport;
use App\Jobs\CourseJob;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class CoursesController extends Controller
{
    public function index()
    {
        $courses = Course::with('all_users_courses')->get();

        return response()->json($courses, 200);
    }

    public function create()
    {
        $courses = (new CourseJob())->delay(Carbon::now()->addSeconds(1));
        dispatch($courses);
        
        return response()->json(['msg' => 'Courses created'], 200);
    }

    public function userRegisterCourse(Request $request)
    {
        $validator = $request->validate([
            'course_code'     => 'required',
        ]);

        if (!$validator) {
            return response()->json([
                'error'  => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 400);
        }

        $user_picked_course = Course::where('course_code', $request->course_code)->first();
        
        $user_course = UserCourse::create([
            'course_id' => $user_picked_course->id,
            'user_id' => auth()->user()->id
        ]);

        return response()->json(['msg' => 'Course registered'], 200);
    }

    public function downloadCourses()
    {
        return Excel::download(new CoursesExport, 'all_courses.xlsx');
    }
}
