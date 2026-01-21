<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IntegracoesController;
use App\Http\Controllers\FakeLuggiaController;
use App\Http\Controllers\ColaboradoresController;
use App\Http\Controllers\OportunidadesController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GruposController;
// use App\Http\Controllers\PermissoesController;


// Authentication
Route::get('/signin', [AuthController::class, 'showLogin'])->name('auth.signin');
Route::post('/signin', [AuthController::class, 'login'])->name('auth.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
// Alias for default Laravel redirect
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

// calender pages
Route::get('/calendar', function () {
    return view('pages.calender', ['title' => 'Calendar']);
})->name('calendar');

// profile pages
Route::get('/profile', function () {
    return view('pages.profile', ['title' => 'Profile']);
})->name('profile');

// form pages
Route::get('/form-elements', function () {
    return view('pages.form.form-elements', ['title' => 'Form Elements']);
})->name('form-elements');

// tables pages
Route::get('/basic-tables', function () {
    return view('pages.tables.basic-tables', ['title' => 'Basic Tables']);
})->name('basic-tables');

// pages

Route::get('/blank', function () {
    return view('pages.blank', ['title' => 'Blank']);
})->name('blank');

// error pages
Route::get('/error-404', function () {
    return view('pages.errors.error-404', ['title' => 'Error 404']);
})->name('error-404');

// chart pages
Route::get('/line-chart', function () {
    return view('pages.chart.line-chart', ['title' => 'Line Chart']);
})->name('line-chart');

Route::get('/bar-chart', function () {
    return view('pages.chart.bar-chart', ['title' => 'Bar Chart']);
})->name('bar-chart');

// Protected application routes
Route::middleware('auth')->group(function () {
    // ERP Calls Dashboard (using new layout)
    Route::get('/', [DashboardController::class, 'calls'])->name('dashboard.calls');
    Route::get('/dashboard/calls', [DashboardController::class, 'calls'])->name('dashboard.calls.page');
    Route::get('/dashboard/calls/fragment', [DashboardController::class, 'callsFragment'])->name('dashboard.calls.fragment');
    Route::post('/dashboard/calls/filter', [DashboardController::class, 'filter'])->name('dashboard.calls.filter');

    // Integrations
    Route::prefix('integracoes')->group(function () {
        Route::get('/', [IntegracoesController::class, 'index'])->name('integracoes.index');
        Route::post('/{api}', [IntegracoesController::class, 'search'])->name('integracoes.search');
        Route::get('/progresso/{runId}', [IntegracoesController::class, 'progress'])->name('integracoes.progress');
        Route::get('/monitor', [IntegracoesController::class, 'monitor'])->name('integracoes.monitor');
        Route::post('/permissoes', [IntegracoesController::class, 'updatePermissions'])->name('integracoes.permissions.update');
    });

    // Colaboradores CRUD
    Route::resource('colaboradores', ColaboradoresController::class)->parameters([
        'colaboradores' => 'colaborador'
    ]);

    // Oportunidades CRUD
    Route::resource('oportunidades', OportunidadesController::class)->parameters([
        'oportunidades' => 'oportunidade'
    ]);

    // Usuários CRUD
    Route::resource('usuarios', UsuariosController::class)->parameters([
        'usuarios' => 'usuario'
    ]);

    // Grupos CRUD
    Route::resource('grupos', GruposController::class)->parameters([
        'grupos' => 'grupo'
    ]);

    // Permissões management moved into Grupo (no standalone page)
});

// Fake Luggia API for testing (public for jobs/testing)
Route::prefix('api/fake/luggia')->group(function () {
    Route::get('/calls', [FakeLuggiaController::class, 'calls']);
});

// Colaboradores CRUD
Route::resource('colaboradores', ColaboradoresController::class)->parameters([
    'colaboradores' => 'colaborador'
]);

// Oportunidades CRUD
Route::resource('oportunidades', OportunidadesController::class)->parameters([
    'oportunidades' => 'oportunidade'
]);

// Usuários CRUD
Route::resource('usuarios', UsuariosController::class)->parameters([
    'usuarios' => 'usuario'
]);


// optional signup page (static demo view)
Route::get('/signup', function () {
    return view('pages.auth.signup', ['title' => 'Sign Up']);
})->name('signup');

// ui elements pages
Route::get('/alerts', function () {
    return view('pages.ui-elements.alerts', ['title' => 'Alerts']);
})->name('alerts');

Route::get('/avatars', function () {
    return view('pages.ui-elements.avatars', ['title' => 'Avatars']);
})->name('avatars');

Route::get('/badge', function () {
    return view('pages.ui-elements.badges', ['title' => 'Badges']);
})->name('badges');

Route::get('/buttons', function () {
    return view('pages.ui-elements.buttons', ['title' => 'Buttons']);
})->name('buttons');

Route::get('/image', function () {
    return view('pages.ui-elements.images', ['title' => 'Images']);
})->name('images');

Route::get('/videos', function () {
    return view('pages.ui-elements.videos', ['title' => 'Videos']);
})->name('videos');






















