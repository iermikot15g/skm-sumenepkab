<?php

use App\Http\Controllers\SurveyController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController; // ---> tambahan

// Halaman publik
Route::get('/', [HomeController::class, 'index'])->name('home');
// Route::get('/', function () {
//     return view('home');
// })->name('home');

// Route survei
Route::prefix('survey')->name('survey.')->group(function () {
    // Pilih unit
    Route::get('/unit', [SurveyController::class, 'selectUnit'])->name('unit');
    Route::post('/unit', [SurveyController::class, 'postUnit'])->name('post.unit');
    
    // Pilih layanan
    Route::get('/layanan', [SurveyController::class, 'selectService'])->name('service');
    Route::post('/layanan', [SurveyController::class, 'postService'])->name('post.service');
    
    // Data diri
    Route::get('/data-diri', [SurveyController::class, 'dataDiri'])->name('data-diri');
    Route::post('/data-diri', [SurveyController::class, 'postDataDiri'])->name('post.data-diri');
    Route::get('/data-diri/edit', [SurveyController::class, 'editDataDiri'])->name('edit.data-diri');
    
    // 9 Pertanyaan
    Route::get('/pertanyaan/{step}', [SurveyController::class, 'showQuestion'])->name('question');
    Route::post('/pertanyaan/{step}', [SurveyController::class, 'postQuestion'])->name('post.question');
    
    // Konfirmasi & Submit
    Route::get('/confirm', [SurveyController::class, 'confirm'])->name('confirm');
    Route::post('/submit', [SurveyController::class, 'submit'])->name('submit');
    
    // Terima kasih
    Route::get('/thankyou', [SurveyController::class, 'thankyou'])->name('thankyou');
});

// Redirect setelah login berdasarkan role
Route::get('/dashboard', function () {
    $user = auth()->user();
    
    if ($user->isSuperAdmin()) {
        return redirect()->route('super-admin.dashboard');
    } elseif ($user->isAdminOpd()) {
        return redirect()->route('admin-opd.dashboard');
    } elseif ($user->isPimpinanOpd()) {
        return redirect()->route('pimpinan.dashboard');
    }
    
    return redirect('/');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

// ==================== SUPER ADMIN ROUTES ====================
Route::prefix('super-admin')->middleware(['auth', 'role:super_admin'])->name('super-admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('opds', App\Http\Controllers\Admin\SuperAdmin\OpdController::class);
    Route::resource('units', App\Http\Controllers\Admin\SuperAdmin\UnitController::class);
    Route::resource('periods', App\Http\Controllers\Admin\SuperAdmin\PeriodController::class);
    Route::resource('users', App\Http\Controllers\Admin\SuperAdmin\UserController::class);
    
    // Tambahkan route reset password (karena tidak termasuk dalam resource)
    Route::post('/users/{user}/reset-password', [App\Http\Controllers\Admin\SuperAdmin\UserController::class, 'resetPassword'])->name('users.reset-password');
    
    // Tambahkan route toggle active
    Route::post('/users/{user}/toggle-active', [App\Http\Controllers\Admin\SuperAdmin\UserController::class, 'toggleActive'])->name('users.toggle-active');
    
    Route::get('/reports', [App\Http\Controllers\Admin\SuperAdmin\ReportController::class, 'index'])->name('reports.index');

    Route::get('/reports/export-excel', [App\Http\Controllers\Admin\SuperAdmin\ReportController::class, 'exportExcel'])->name('reports.export-excel');
    Route::get('/reports/export-pdf', [App\Http\Controllers\Admin\SuperAdmin\ReportController::class, 'exportPdf'])->name('reports.export-pdf');
    
    // API for dynamic unit loading
    Route::get('/units/by-opd/{opd}', function($opdId) {
        return App\Models\Unit::where('opd_id', $opdId)->get(['id', 'name']);
    })->name('units.by-opd');
    
});

// ==================== ADMIN OPD ROUTES ====================
Route::prefix('admin-opd')->middleware(['auth', 'role:admin_opd'])->name('admin-opd.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminOpd\DashboardController::class, 'index'])->name('dashboard');
    
    // Manajemen Jenis Layanan
    Route::resource('services', App\Http\Controllers\Admin\AdminOpd\ServiceController::class);
    Route::post('/services/{id}/toggle-active', [App\Http\Controllers\Admin\AdminOpd\ServiceController::class, 'toggleActive'])->name('services.toggle-active');
    
    // Monitoring hasil survei
    Route::get('/surveys', [App\Http\Controllers\Admin\AdminOpd\SurveyController::class, 'index'])->name('surveys.index');
    Route::get('/surveys/{respondent}', [App\Http\Controllers\Admin\AdminOpd\SurveyController::class, 'show'])->name('surveys.show');
    Route::get('/surveys/{respondent}/pdf', [App\Http\Controllers\Admin\AdminOpd\SurveyController::class, 'exportPdf'])->name('surveys.export-pdf');
    
    // Laporan
    Route::get('/reports', [App\Http\Controllers\Admin\AdminOpd\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-excel', [App\Http\Controllers\Admin\AdminOpd\ReportController::class, 'exportExcel'])->name('reports.export-excel');
    Route::get('/reports/export-pdf', [App\Http\Controllers\Admin\AdminOpd\ReportController::class, 'exportPdf'])->name('reports.export-pdf');
});

// ==================== PIMPINAN OPD ROUTES ====================
Route::prefix('pimpinan')->middleware(['auth', 'role:pimpinan_opd'])->name('pimpinan.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Pimpinan\DashboardController::class, 'index'])->name('dashboard');
    
    // Analisis
    Route::get('/analysis', [App\Http\Controllers\Pimpinan\AnalysisController::class, 'index'])->name('analysis');
    Route::get('/analysis/lowest-aspects', [App\Http\Controllers\Pimpinan\AnalysisController::class, 'lowestAspects'])->name('analysis.lowest-aspects');
    Route::get('/analysis/complaints', [App\Http\Controllers\Pimpinan\AnalysisController::class, 'complaints'])->name('analysis.complaints');
    
    // Laporan (Read Only)
    Route::get('/reports', [App\Http\Controllers\Pimpinan\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-excel', [App\Http\Controllers\Pimpinan\ReportController::class, 'exportExcel'])->name('reports.export-excel');
    Route::get('/reports/export-pdf', [App\Http\Controllers\Pimpinan\ReportController::class, 'exportPdf'])->name('reports.export-pdf');
});

// Profile routes (simple version)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});