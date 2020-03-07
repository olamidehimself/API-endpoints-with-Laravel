<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    //
    protected $fillable = ['course_title', 'course_description', 'course_code'];

    public function all_users_courses()
    {
        return $this->hasMany(UserCourse::class);
    }
}
