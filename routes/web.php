<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InitialSetupController;
use App\Http\Controllers\InstitutionAdministrationController;
use App\Http\Controllers\InstitutionAddressController;
use App\Http\Controllers\InstitutionPropertyController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::view('/erro', 'auth.error')->name('auth.error');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/auth/request-link', [AuthController::class, 'requestLink'])
    ->middleware('throttle:magic-link')
    ->name('auth.request-link');
Route::get('/auth/magic', [AuthController::class, 'magic'])->name('auth.magic');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/cadastro-inicial', [InitialSetupController::class, 'create'])->name('setup.create');
    Route::post('/cadastro-inicial', [InitialSetupController::class, 'storeInstitutionAndOwner'])->name('setup.store');
});

Route::middleware(['auth', 'ensure.institution'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('members', MemberController::class)->only(['index', 'edit', 'update', 'destroy']);
    Route::get('/administracao', [InstitutionAdministrationController::class, 'edit'])->name('administration.edit');
    Route::post('/administracao', [InstitutionAdministrationController::class, 'store'])->name('administration.store');
    Route::get('/instituicao/endereco', [InstitutionAddressController::class, 'edit'])->name('institution.address.edit');
    Route::put('/instituicao/endereco', [InstitutionAddressController::class, 'update'])->name('institution.address.update');
    Route::get('/instituicao/imovel', [InstitutionPropertyController::class, 'edit'])->name('institution.property.edit');
    Route::put('/instituicao/imovel', [InstitutionPropertyController::class, 'update'])->name('institution.property.update');
});

Route::get('/cadastro/{invite:key}', [InviteController::class, 'showPublicForm'])->name('invite.form');
Route::post('/cadastro/{invite:key}', [InviteController::class, 'storeMember'])->name('invite.store');
Route::get('/cadastro/{invite:key}/confirmacao', [InviteController::class, 'showConfirmation'])->name('invite.confirmation');







