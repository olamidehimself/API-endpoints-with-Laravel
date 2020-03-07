<?php

namespace App\Jobs;

use App\Course;
use Faker\Factory;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CourseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Course::truncate();

        $faker = Factory::create();

        // And now, let's create a few courses in our database:
        for ($i = 0; $i < 50; $i++) {
            Course::create([
                'course_title' => ucfirst($faker->word),
                'course_description' => $faker->unique()->realText,
                'course_code' => $faker->unique()->ean8
            ]);
        }

    }
}
