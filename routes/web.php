<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\RankingsController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PatchNoteController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDbController;


Route::view('/legal/copyright-notice', 'legal.copyright')->name('legal.copyright');
Route::view('/legal/terms-of-use', 'legal.terms')->name('legal.terms');
Route::view('/legal/privacy-policy', 'legal.privacy')->name('legal.privacy');

Route::controller(PatchNoteController::class)->group(function () {
    Route::get('/patch-notes', 'index')->name('patch-notes.index');
    Route::get('/patch-notes/{patchNote}', 'show')->name('patch-notes.show'); // slug 바인딩
});

Route::resource('news', NewsController::class);

Route::resource('players', PlayerController::class);
Route::resource('teams', TeamController::class);
Route::resource('matches', MatchController::class);

Route::get('/rankings', [RankingsController::class, 'index'])->name('rankings.index');
Route::get('/', fn() => view('home'))->name('home');


Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/account', [AccountController::class, 'show'])->name('account.show');
    Route::get('/account/edit', [AccountController::class, 'edit'])->name('account.edit');
    Route::put('/account', [AccountController::class, 'update'])->name('account.update');
});
// 관리자 전용 영역
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // 관리자 대시보드
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // DB 브라우저
        Route::get('/db', [AdminDbController::class, 'index'])->name('db.index');
        Route::get('/db/{table}', [AdminDbController::class, 'show'])->name('db.show');
    });
