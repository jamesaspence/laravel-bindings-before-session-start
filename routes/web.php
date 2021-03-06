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

    //Authed user works here when signed in
    Route::get('/', function () {
        dd(Auth::user());
    });

    Route::get('logout', function () {
        Auth::logout();
        return redirect('/');
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

/*
 * No issue here as well - this is the "web" middleware
 * without the substitution of route bindings.
 * Everything works as expected with the auth.
 */
Route::middleware('webWithoutBindings')->group(function () {
    Route::get('withoutBindings', function () {
        dd(Auth::user());
    });
});

/*
 * However, if we add binding to the route, before the session is initialized,
 * the session gets messed up and Auth::user() returns null.
 */
Route::middleware(['bindings', 'webWithoutBindings'])->group(function () {
    Route::get('withBindings', function () {
        dd(Auth::user());
    });
});