<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MSOauthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleConfigController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('unsubscribe/{email}', function ($email) {
    User::where('email', $email)->firstOrFail()->scheduleConfig()->firstOrFail()->updateOrFail(['is_subscribed' => false]);
    echo "You have been successfully unsubscribe";
})->name('unsubscribe')->middleware('signed');

Route::middleware('auth')->group(function () {
    Route::get('about', [HomeController::class, 'about'])->name('about');
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::group(['prefix' => 'schedule', 'as' => 'schedule.'], function () {
        Route::post('getGrouping', [ScheduleConfigController::class, 'getGrouping'])->name('getGrouping');
        Route::get('syncNow', [ScheduleConfigController::class, 'syncNow'])->name('syncNow');
    });

    Route::get('msOAuth', [MSOauthController::class, 'signin'])->name('msOAuth.signin');
    Route::get('msOAuth/callback', [MSOauthController::class, 'callback'])->name('msOAuth.callback');
    Route::resource('scheduleConfig', ScheduleConfigController::class);

    Route::group(['prefix' => 'payment', 'as' => 'payments.'], function () {
        Route::post('confirm', [PaymentController::class, 'confirm'])->name('confirm');
        Route::get('checkout', [PaymentController::class, 'checkout'])->name('checkout');
        Route::post('pay', [PaymentController::class, 'pay'])->name('pay');
    });

});

Route::stripeWebhooks('/stripe/webhook');
