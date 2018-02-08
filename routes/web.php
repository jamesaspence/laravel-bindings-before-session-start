<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\User;

Route::middleware('web')->group(function () {
    Route::get('login', function () {
        $user = User::find(1);
        if ($user === null) {
            $user = new User();
            $user->name = 'Test User';
            $user->email = 'test@test.test';
            $user->password = Hash::make('password');
            $user->save();
        }

        Auth::login($user);
        return redirect('/');
    });

    //Authed user works here
    Route::get('/', function () {
        dd(Auth::user());
    });
});

/*
 * Authed user doesn't work here
 * Appears to be due to the bindings middleware
 * happening BEFORE session is started
 */
Route::middleware(['bindings', 'web'])->group(function () {
    Route::get('test', function () {
        dd(Auth::user());
    });
});