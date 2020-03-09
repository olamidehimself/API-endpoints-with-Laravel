<?php

namespace App\Http\Controllers\api;

use App\Course;
use Carbon\Carbon;
use Faker\Factory;
use App\UserCourse;
use Illuminate\Http\Request;
use App\Exports\CoursesExport;
use App\Jobs\CreateCoursesJob;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class CoursesController extends Controller
{
    /**
     * Gets the list of all courses created. 
     * Also eager courses registered by users
     */
    public function index()
    {
        $courses = Course::with('users_courses.course')->get();

        return response()->json($courses, 200);
    }

    /**
     * This function creates all the 50 courses by running a disoatch job in a queue
     * To get the queue running, type 'php artisan queue:work' in your terminal
     */
    public function create()
    {
        $courses = (new CreateCoursesJob())->delay(Carbon::now()->addSeconds(1));
        dispatch($courses);
        
        return response()->json(['msg' => 'Courses created'], 200);
    }

    /**
     * This function handles course registration by a user
     */
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

        // dd($user_picked_course->toArray());
        
        $user_course = UserCourse::create([
            'course_id' => $user_picked_course->id,
            'user_id' => auth()->user()->id,
            'registered_at' => date("Y-m-d")
        ]);

        return response()->json(['msg' => 'Course succefully registered'], 200);
    }

    /**
     * This function handles the downloading of the courses table in excel format
     */
    public function downloadCourses()
    {
        return Excel::download(new CoursesExport, 'courses.xlsx');
    }
}
