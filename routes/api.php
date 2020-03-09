<?php





/**
 *  The endpoint handling the request for exporting courses in excelt format is in web.php
 */


Route::get('user', 'api\UserController@user');
Route::post('login', 'api\UserController@login');
Route::get('logout', 'api\UserController@logout');
Route::post('register', 'api\UserController@register');
Route::post('create/course', 'api\CoursesController@create');
Route::post('register/course', 'api\CoursesController@userRegisterCourse');
Route::get('courses', 'api\CoursesController@index');

/**
 *  The endpoint handling the request for exporting courses in excelt format is in web.php
 */

