<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Hash;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/**
 * Route Pengaturan Umum
 */
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TenagaKerjaController;
Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/my_profil', [TenagaKerjaController::class, 'index'])->name('profil');
    Route::get('/regional', [TenagaKerjaController::class, 'getRegional'])->name('api.regional');
});


/**
 * Route Pengaturan Umum
 */
use App\Http\Controllers\PengaturanUmumController;
Route::group(['middleware' => 'auth'], function () {
    Route::get('/setting', [PengaturanUmumController::class, 'index'])->name('setting');
    Route::post('/setting/save', [PengaturanUmumController::class, 'store'])->name('setting.save');
});

/**
 * Route Master Data Entitas
 */
use App\Http\Controllers\M_EntitasController;
Route::group(['middleware' => 'auth'], function () {
    Route::get('/m_entitas', [M_EntitasController::class, 'index'])->name('entitas');
    Route::get('/m_entitas/add', [M_EntitasController::class, 'create'])->name('entitas.create');
    Route::post('/m_entitas/save', [M_EntitasController::class, 'store'])->name('entitas.save');
    Route::get('/m_entitas/edit', [M_EntitasController::class, 'edit'])->name('entitas.edit');
    Route::put('/m_entitas/update/{id}', [M_EntitasController::class, 'update'])->name('entitas.update');
    Route::delete('/m_entitas/delete/{id}', [M_EntitasController::class, 'destroy'])->name('entitas.destroy');
});


/**
 * Route Master Data Project
 */
use App\Http\Controllers\M_ProjectController;
Route::group(['middleware' => 'auth'], function () {
    Route::get('/m_project', [M_ProjectController::class, 'index'])->name('project');
    Route::get('/m_project/download', [M_ProjectController::class, 'download'])->name('project.template');
    Route::get('/m_project/add', [M_ProjectController::class, 'create'])->name('project.create');
    Route::post('/m_project/import', [M_ProjectController::class, 'store'])->name('project.save');
});


/**
 * Route Master Data Regional
 */
use App\Http\Controllers\M_RegionalController;
Route::group(['middleware' => 'auth'], function () {
    Route::get('/m_regional', [M_RegionalController::class, 'index'])->name('regional');
    Route::get('/m_regional/download', [M_RegionalController::class, 'download'])->name('regional.template');
    Route::get('/m_regional/add', [M_RegionalController::class, 'create'])->name('regional.create');
    Route::post('/m_regional/import', [M_RegionalController::class, 'store'])->name('regional.save');
});

/**
 * Route Master Data Job
 */
use App\Http\Controllers\M_JobController;
Route::group(['middleware' => 'auth'], function () {
    Route::get('/m_job', [M_JobController::class, 'index'])->name('job');
    Route::get('/m_job/add', [M_JobController::class, 'create'])->name('job.create');
    Route::post('/m_job/import', [M_JobController::class, 'store'])->name('job.save');
});



/**
 * Route Master Data Unit
 */
use App\Http\Controllers\M_UnitController;
Route::group(['middleware' => 'auth'], function () {
    Route::get('/m_unit', [M_UnitController::class, 'index'])->name('unit');
    Route::get('/m_unit/download', [M_UnitController::class, 'download'])->name('unit.template');
    Route::get('/m_unit/add', [M_UnitController::class, 'create'])->name('unit.create');
    Route::post('/m_unit/import', [M_UnitController::class, 'store'])->name('unit.save');
});


/**
 * Route Master Data Formation
 */
use App\Http\Controllers\FormationController;
Route::group(['middleware' => 'auth'], function () {
    Route::get('/m_formation', [FormationController::class, 'index'])->name('formation');
    Route::get('/m_formation/download', [FormationController::class, 'download'])->name('formation.template');
    Route::get('/m_formation/add', [FormationController::class, 'create'])->name('formation.create');
    Route::post('/m_formation/import', [FormationController::class, 'store'])->name('formation.save');
});


/**
 * Route Master Data Employee
 */
use App\Http\Controllers\EmployeeController;
Route::group(['middleware' => 'auth'], function () {
    Route::get('/m_employee', [EmployeeController::class, 'index'])->name('employee');
    Route::get('/m_employee/download', [EmployeeController::class, 'download'])->name('employee.template');
    Route::get('/m_employee/add', [EmployeeController::class, 'create'])->name('employee.create');
    Route::post('/m_employee/import', [EmployeeController::class, 'store'])->name('employee.save');
});


/**
 * Route Master Data SOP
 */
use App\Http\Controllers\SopController;
Route::group(['middleware' => 'auth'], function () {
    Route::get('/sop', [SopController::class, 'index'])->name('sop');
    Route::get('/sop/update/{id}', [SopController::class, 'edit'])->name('sop.edit');
    Route::get('/sop/add', [SopController::class, 'create'])->name('sop.create');
    Route::post('/sop/save', [SopController::class, 'store'])->name('sop.save');
    Route::put('/sop/update', [SopController::class, 'update'])->name('sop.update');
    Route::delete('/sop/destroy', [SopController::class, 'destroy'])->name('sop.destroy');
});


/**
 * Route Master Data Video
 */
use App\Http\Controllers\VideoController;
Route::group(['middleware' => 'auth'], function () {
    Route::get('/video', [VideoController::class, 'index'])->name('video');
    Route::get('/video/update/{id}', [VideoController::class, 'edit'])->name('video.edit');
    Route::get('/video/add', [VideoController::class, 'create'])->name('video.create');
    Route::post('/video/save', [VideoController::class, 'store'])->name('video.save');
    Route::put('/video/update', [VideoController::class, 'update'])->name('video.update');
    Route::delete('/video/destroy', [VideoController::class, 'destroy'])->name('video.destroy');
});



/**
 * Route Master Data Users
 */
use App\Http\Controllers\UsersController;
Route::group(['middleware' => 'auth'], function () {
    Route::get('/users', [UsersController::class, 'index'])->name('users');
    // Route::get('/users/add', [UsersController::class, 'create'])->name('users.create');
    // Route::post('/users/import', [UsersController::class, 'store'])->name('users.save');
});



