
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

    Route::middleware(['ensure.institution'])->group(function () {
        Route::post('/processos/{process}/filial/gerar-convite-dirigente', [BranchLeaderInviteController::class, 'generate'])->name('processes.branch.leader_invite.generate');
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
        Route::get('/processos/{process}/bylaws-revision/upload-estatuto', [\App\Http\Controllers\BylawsRevisionController::class, 'uploadStatute'])->name('processes.bylaws_revision.upload_statute');
        Route::post('/processos/{process}/bylaws-revision/upload-estatuto', [\App\Http\Controllers\BylawsRevisionController::class, 'saveStatute'])->name('processes.bylaws_revision.save_statute');
        Route::delete('/processos/{process}/bylaws-revision/delete-estatuto', [\App\Http\Controllers\BylawsRevisionController::class, 'deleteStatute'])->name('processes.bylaws_revision.delete_statute');
        // Dashboard do processo de ata de eleição da diretoria
        Route::post('/processos/{process}/ata-eleicao/mandato', [\App\Http\Controllers\BoardElectionProcessController::class, 'storeMandate'])->name('processes.board_election.mandate.store');
        Route::post('/processos/{process}/ata-eleicao/membros', [App\Http\Controllers\MemberController::class, 'storeForBoardElection'])->name('processes.board_election.members.store');
        Route::post('/processos/{process}/ata-eleicao/documentos', [\App\Http\Controllers\BoardElectionProcessController::class, 'storeDocuments'])->name('processes.board_election.documents.store');
        Route::get('/processos/{process}/ata-eleicao/membros', [\App\Http\Controllers\BoardElectionProcessController::class, 'members'])->name('processes.board_election.members');
        Route::get('/processos/{process}/ata-eleicao/dashboard', [\App\Http\Controllers\BoardElectionProcessController::class, 'dashboard'])->name('processes.board_election.dashboard');
    });
});

// Rotas públicas para cadastro do dirigente via convite (fora do grupo de autenticação)
Route::get('/cadastro-dirigente/{invite:key}', [App\Http\Controllers\BranchLeaderInviteController::class, 'showPublicForm'])->name('branch_leader_invite.form');
Route::post('/cadastro-dirigente/{invite:key}', [App\Http\Controllers\BranchLeaderInviteController::class, 'storeLeader'])->name('branch_leader_invite.store');


// Rotas públicas para cadastro de membros (fora de qualquer middleware de autenticação)
Route::get('/cadastro/{invite:key}', [InviteController::class, 'showPublicForm'])->name('invite.form');
Route::post('/cadastro/{invite:key}', [InviteController::class, 'storeMember'])->name('invite.store');
Route::get('/cadastro/{invite:key}/confirmacao', [InviteController::class, 'showConfirmation'])->name('invite.confirmation');



