<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchLeaderController;
use App\Http\Controllers\BranchLocationController;
use App\Http\Controllers\BranchProcessController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InitialSetupController;
use App\Http\Controllers\InstitutionAdministrationController;
use App\Http\Controllers\InstitutionAddressController;
use App\Http\Controllers\InstitutionPropertyController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\OpeningProcessController;
use App\Http\Controllers\ProcessController;
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
    Route::post('/processos', [ProcessController::class, 'store'])->name('processes.store');
    Route::get('/processos/{process}', [ProcessController::class, 'show'])->name('processes.show');
    Route::get('/processos/{process}/abertura', [OpeningProcessController::class, 'show'])->name('processes.opening.show');
    Route::get('/processos/{process}/filial', [BranchProcessController::class, 'show'])->name('processes.branch.show');
    Route::get('/processos/{process}/filial/localizacao', [BranchLocationController::class, 'edit'])->name('processes.branch.location.edit');
    Route::put('/processos/{process}/filial/localizacao', [BranchLocationController::class, 'update'])->name('processes.branch.location.update');
    Route::get('/processos/{process}/filial/dirigente', [BranchLeaderController::class, 'edit'])->name('processes.branch.leader.edit');
    Route::put('/processos/{process}/filial/dirigente', [BranchLeaderController::class, 'update'])->name('processes.branch.leader.update');
    Route::resource('members', MemberController::class)->only(['index', 'edit', 'update', 'destroy']);
    Route::get('/administracao', [InstitutionAdministrationController::class, 'edit'])->name('administration.edit');
    Route::post('/administracao', [InstitutionAdministrationController::class, 'store'])->name('administration.store');
    Route::get('/instituicao/endereco', [InstitutionAddressController::class, 'edit'])->name('institution.address.edit');
    Route::put('/instituicao/endereco', [InstitutionAddressController::class, 'update'])->name('institution.address.update');
    Route::get('/instituicao/imovel', [InstitutionPropertyController::class, 'edit'])->name('institution.property.edit');
    Route::put('/instituicao/imovel', [InstitutionPropertyController::class, 'update'])->name('institution.property.update');
    Route::get('/processos/{process}/bylaws-revision', [\App\Http\Controllers\BylawsRevisionController::class, 'show'])->name('processes.bylaws_revision.show');
    Route::post('/processos/{process}/bylaws-revision', [\App\Http\Controllers\BylawsRevisionController::class, 'save'])->name('processes.bylaws_revision.save');
    Route::get('/processos/{process}/bylaws-revision/dashboard', [\App\Http\Controllers\BylawsRevisionController::class, 'dashboard'])->name('processes.bylaws_revision.dashboard');
    Route::get('/processos/{process}/bylaws-revision/motivo/{motivo}/edit', [\App\Http\Controllers\BylawsRevisionController::class, 'editMotivo'])->name('processes.bylaws_revision.edit_motivo');
    Route::put('/processos/{process}/bylaws-revision/motivo/{motivo}', [\App\Http\Controllers\BylawsRevisionController::class, 'updateMotivo'])->name('processes.bylaws_revision.update_motivo');
});

Route::get('/cadastro/{invite:key}', [InviteController::class, 'showPublicForm'])->name('invite.form');
Route::post('/cadastro/{invite:key}', [InviteController::class, 'storeMember'])->name('invite.store');
Route::get('/cadastro/{invite:key}/confirmacao', [InviteController::class, 'showConfirmation'])->name('invite.confirmation');
