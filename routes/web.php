<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ApiClientController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\PassportClientController;
use App\Http\Controllers\PmbController;
use App\Http\Controllers\CbtController;

use App\Http\Controllers\Kanban\KanbanController;

use App\Http\Controllers\Apps\{
    ActivityTypeController, 
    ActivityController, 
    ActivitySessionController, 
    ActivityReportController, 
    AttendanceController,  
    ShortLinkController,  
};  

use App\Http\Controllers\Admin\{
    RoleController,
    PermissionController,
};

use App\Http\Controllers\Auth\{
    ForgotPasswordController,
    LoginController,
    LogoutController,
    ResetPasswordController,
    SocialiteController,
    TwoFactorAuthController,
    VerificationController,
};
use Illuminate\Support\Facades\DB;


// PENCEGAT DARURAT: Mengintip data yang dilempar dari SIAKAD Lokal
// Route::get('/oauth/authorize', function(\Illuminate\Http\Request $request) {
//     dd([
//         'KETERANGAN' => 'Melihat data kiriman dari SIAKAD Lokal',
//         'CLIENT_ID_YANG_DIKIRIM' => $request->query('client_id'),
//         'REDIRECT_URI_TUTUAN'    => $request->query('redirect_uri'),
//         'RESPONSE_TYPE'          => $request->query('response_type'),
//         'STATE_OAUTH'            => $request->query('state'),
//         'SEMUA_PARAMETER_URL'    => $request->all()
//     ]);
// });

/*
|--------------------------------------------------------------------------
| Public Routes (Dapat Diakses Tanpa Login / Pihak Luar)
|--------------------------------------------------------------------------
*/
// Form Presensi via Scan QR Code (Peserta Internal & Tamu Eksternal)
Route::get('/presensi/{token}', [AttendanceController::class, 'publicForm'])->name('attendances.public_form');
Route::post('/presensi/{token}', [AttendanceController::class, 'publicSubmit'])->name('attendances.public_submit');

// Hal Verifikasi QR Tanda Tangan Digital Berita Acara
Route::get('/verify-report/{token}', [ActivityReportController::class, 'verifyDigitalSignature'])->name('activity-reports.verify');

Route::get('/approve-report/{token}', [ActivityReportController::class, 'publicApprovalView'])->name('activity_reports.public_approval');
Route::post('/approve-report/{token}', [ActivityReportController::class, 'publicApprovalSubmit'])->name('activity_reports.public_approval_submit');

Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => redirect('/auth/login'));
    Route::post('/login', [LoginController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Authenticated App Routes (Prefix: /apps)
|--------------------------------------------------------------------------
*/

Route::prefix('apps')->name('apps.')->group(function () {
    Route::prefix('sessions')->name('sessions.')->group(function () { 
            // Menampilkan Layar QR Code Sesi untuk di-scan Peserta
            Route::get('/{session}/qr-display', [ActivitySessionController::class, 'qrDisplay'])->name('qr_display');
    });
});
Route::middleware(['auth'])->prefix('apps')->name('apps.')->group(function () {

    // 1. MASTER DATA: Tipe Kegiatan
    Route::resource('activity-types', ActivityTypeController::class);

    // 2. KEGIATAN & SESI
    Route::resource('activities', ActivityController::class);
    
    // Nested/Additional Route untuk Sesi Pertemuan di dalam Kegiatan
    Route::prefix('activities/{activity}')->name('activities.')->group(function () {
        Route::post('/sessions', [ActivitySessionController::class, 'store'])->name('sessions.store');
    });
    
    Route::prefix('sessions')->name('sessions.')->group(function () {
        Route::get('/{session}', [ActivitySessionController::class, 'show'])->name('show');
        Route::put('/{session}', [ActivitySessionController::class, 'update'])->name('update');
        Route::delete('/{session}', [ActivitySessionController::class, 'destroy'])->name('destroy');
        
        
    });

    // 3. BERITA ACARA & NOTULENSI
    Route::resource('activity-reports', ActivityReportController::class);
    Route::prefix('activity-reports')->name('activity-reports.')->group(function () {
        // Helper route untuk fetch data laporan berdasarkan activity_id
        Route::get('/by-activity/{activityId}', [ActivityReportController::class, 'getByActivity'])->name('by_activity');
    
        // Cetak Dokumen Berita Acara PDF
        Route::get('/{activityReport}/print-pdf', [ActivityReportController::class, 'printPdf'])->name('print_pdf');
        // Fitur Pengesahan Digital (Aprove/Reject)
        Route::post('/{activityReport}/approve', [ActivityReportController::class, 'approve'])->name('approve');
    });

    // 4. DATA PRESENSI (REKAP & MONITORING)
    Route::resource('attendances', AttendanceController::class)->only(['index', 'destroy']);
    Route::prefix('attendances')->name('attendances.')->group(function () {
        // Export Rekap Kehadiran (Excel/PDF)
        Route::get('/export/excel', [AttendanceController::class, 'exportExcel'])->name('export_excel');
        Route::get('/export/pdf', [AttendanceController::class, 'exportPdf'])->name('export_pdf');
        // Manual Input Presensi oleh Operator
        Route::post('/manual-store', [AttendanceController::class, 'manualStore'])->name('manual_store');
    });

});

Route::prefix('auth')->group(function () {
    
    // Rute khusus GUEST (Hanya bisa diakses jika BELUM login)
    Route::middleware(['guest'])->group(function () {
        
        Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login']);

        // Socialite Google
        Route::get('google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
        Route::get('google/callback', [SocialiteController::class, 'handleGoogleCallback']);

        // Password Reset Request
        Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
        Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    });

    // Rute khusus AUTH (Hanya bisa diakses jika SUDAH login)
    Route::middleware(['auth'])->group(function () {
        Route::post('logout', [LogoutController::class, 'logout'])->name('logout');

        // Email Verification
        Route::get('email/verify', [VerificationController::class, 'show'])->name('verification.notice');
        Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
            ->name('verification.verify')->middleware(['signed']);
        Route::post('email/resend', [VerificationController::class, 'resend'])
            ->name('verification.resend')->middleware(['throttle:6,1']);
            
        Route::post('/2fa/toggle', [UserController::class, 'toggleSecured'])->name('2fa.toggle');
         
    });

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('auth/2fa/setup', [TwoFactorAuthController::class, 'setup'])->name('2fa.setup');
        Route::post('auth/2fa/activate', [TwoFactorAuthController::class, 'activate'])->name('2fa.activate');
        Route::get('auth/2fa/verify', [TwoFactorAuthController::class, 'index'])->name('2fa.index');
        Route::post('auth/2fa/verify', [TwoFactorAuthController::class, 'verify'])->name('2fa.verify');
    });
    
    
});

Route::middleware(['auth'])->group(function () {
    
    Route::prefix('kanban')->name('kanban.')->group(function () { 
        // 1. Rute Utama & Manajemen Board (Query String)
        Route::get('/', [KanbanController::class, 'index'])->name('index'); 
        Route::post('/board/store', [KanbanController::class, 'storeBoard'])->name('board.store');
        Route::delete('/board/delete/{id}', [KanbanController::class, 'deleteBoard'])->name('board.delete');
        
        // 2. TAMBAHAN BARIS BARU: Rute Fitur Kolaborasi (Share Board)
        // Mengambil daftar user/dosen untuk dipilih di modal share
        Route::get('/board/{id}/users', [KanbanController::class, 'getAvailableUsers'])->name('board.users');
        // Menyimpan kolaborator baru ke tabel pivot
        Route::post('/board/share', [KanbanController::class, 'shareBoard'])->name('board.share');
        
        // Fitur Kolaborasi Lanjutan
        Route::get('/board/{id}/members', [KanbanController::class, 'getBoardMembers'])->name('board.members');
        
        // PERBAIKAN DI SINI: Tambahkan ->name('member.update')
        Route::post('/board/member/update', [KanbanController::class, 'updateMemberRole'])->name('member.update');
        
        // PERBAIKAN DI SINI: Tambahkan ->name('member.remove')
        Route::delete('/board/member/remove/{board_id}/{user_id}', [KanbanController::class, 'removeMember'])->name('member.remove');

        // 3. Rute AJAX Manajemen Data & Kartu Tugas (Tetap Sama)
        Route::get('/board-data/{id}', [KanbanController::class, 'getBoardData'])->name('data');
        Route::post('/task/store', [KanbanController::class, 'storeTask'])->name('task.store');
        Route::post('/task/update-position', [KanbanController::class, 'updateTaskPosition'])->name('task.updatePosition');
        Route::put('/task/update/{id}', [KanbanController::class, 'updateTask'])->name('task.update');
        Route::delete('/task/delete/{id}', [KanbanController::class, 'deleteTask'])->name('task.delete');
    });

});

Route::middleware(['auth'])->prefix('pmb')->name('pmb.')->group(function () {
    Route::get('/', [PmbController::class, 'index'])->name('index');
    Route::get('/exams', [CbtController::class, 'index'])->name('exams.index');
    Route::post('/exams/{examId}/registrations/{registrationId}/start', [CbtController::class, 'start'])->name('exams.start');
    Route::get('/exams/attempts/{attemptId}', [CbtController::class, 'take'])->name('exams.take');
    Route::post('/exams/attempts/{attemptId}', [CbtController::class, 'submit'])->name('exams.submit');
    Route::get('/exams/attempts/{attemptId}/result', [CbtController::class, 'result'])->name('exams.result');
    Route::post('/registrations', [PmbController::class, 'store'])->name('registrations.store');
    Route::put('/profile', [PmbController::class, 'saveCandidateProfile'])->name('profile.update');
    Route::post('/registrations/{registrationId}/payments/{paymentId}/proof', [PmbController::class, 'uploadPaymentProof'])
        ->name('payments.proof');
    Route::get('/payments/{paymentId}/proof', [PmbController::class, 'downloadPaymentProof'])
        ->name('payments.download');
    Route::post('/registrations/{registrationId}/documents/{requirementId}', [PmbController::class, 'uploadCandidateDocument'])->name('documents.upload');
    Route::get('/documents/{documentId}', [PmbController::class, 'downloadCandidateDocument'])->name('documents.download');
    Route::get('/documents/{documentId}/preview', [PmbController::class, 'previewCandidateDocument'])->name('documents.preview');
});

Route::prefix('admin/cbt')->name('admin.cbt.')->middleware(['auth', 'verified', '2fa', 'can:pmb-cbt-manage'])->group(function () {
    Route::get('/', [CbtController::class, 'adminIndex'])->name('index');
    Route::post('/exams', [CbtController::class, 'storeExam'])->name('exams.store');
    Route::patch('/exams/{examId}/paths', [CbtController::class, 'updateExamPaths'])->name('exams.paths');
    Route::patch('/exams/{examId}', [CbtController::class, 'updateExam'])->name('exams.update');
    Route::delete('/exams/{examId}', [CbtController::class, 'destroyExam'])->name('exams.destroy');
    Route::get('/exams/{examId}/questions', [CbtController::class, 'questions'])->name('questions.index');
    Route::post('/exams/{examId}/questions', [CbtController::class, 'storeQuestion'])->name('questions.store');
    Route::delete('/questions/{questionId}', [CbtController::class, 'destroyQuestion'])->name('questions.destroy');
    Route::get('/exams/{examId}/attempts', [CbtController::class, 'attempts'])->name('attempts.index');
    Route::patch('/answers/{answerId}/grade', [CbtController::class, 'gradeAnswer'])->name('answers.grade');
});

Route::prefix('admin/pmb')->name('admin.pmb.')->middleware(['auth', 'verified', '2fa'])->group(function () {
    Route::get('/', [PmbController::class, 'adminIndex'])->name('index');
    Route::get('/section/{section}', [PmbController::class, 'adminSection'])->name('section');
    Route::get('/data/{section}', [PmbController::class, 'adminData'])->name('data');
    Route::patch('/documents/{documentId}/verification', [PmbController::class, 'verifyCandidateDocument'])->middleware('can:pmb-review')->name('documents.verify');
    Route::post('/requirements', [PmbController::class, 'storeDocumentRequirement'])->middleware('can:pmb-manage')->name('requirements.store');
    Route::put('/requirements/{requirementId}', [PmbController::class, 'updateDocumentRequirement'])->middleware('can:pmb-manage')->name('requirements.update');
    Route::delete('/requirements/{requirementId}', [PmbController::class, 'destroyDocumentRequirement'])->middleware('can:pmb-manage')->name('requirements.destroy');
    Route::post('/referrals', [PmbController::class, 'storeReferral'])->middleware('can:pmb-manage')->name('referrals.store');
    Route::patch('/referrals/{referralId}/status', [PmbController::class, 'updateReferralStatus'])->middleware('can:pmb-manage')->name('referrals.status');
    Route::delete('/referrals/{referralId}', [PmbController::class, 'destroyReferral'])->middleware('can:pmb-manage')->name('referrals.destroy');
    Route::post('/periods', [PmbController::class, 'storePeriod'])->middleware('can:pmb-manage')->name('periods.store');
    Route::put('/periods/{periodId}', [PmbController::class, 'updatePeriod'])->middleware('can:pmb-manage')->name('periods.update');
    Route::delete('/periods/{periodId}', [PmbController::class, 'destroyPeriod'])->middleware('can:pmb-manage')->name('periods.destroy');
    Route::patch('/periods/{periodId}/status', [PmbController::class, 'updatePeriodStatus'])
        ->middleware('can:pmb-manage')
        ->name('periods.status');
    Route::post('/paths', [PmbController::class, 'storePath'])->middleware('can:pmb-manage')->name('paths.store');
    Route::put('/paths/{pathId}', [PmbController::class, 'updatePath'])->middleware('can:pmb-manage')->name('paths.update');
    Route::delete('/paths/{pathId}', [PmbController::class, 'destroyPath'])->middleware('can:pmb-manage')->name('paths.destroy');
    Route::post('/programs', [PmbController::class, 'storeProgram'])->middleware('can:pmb-manage')->name('programs.store');
    Route::put('/programs/{programId}', [PmbController::class, 'updateProgram'])->middleware('can:pmb-manage')->name('programs.update');
    Route::delete('/programs/{programId}', [PmbController::class, 'destroyProgram'])->middleware('can:pmb-manage')->name('programs.destroy');
    Route::post('/fees', [PmbController::class, 'storeFee'])->middleware('can:pmb-manage')->name('fees.store');
    Route::post('/fees/bulk', [PmbController::class, 'storeFeesBulk'])->middleware('can:pmb-manage')->name('fees.bulk');
    Route::put('/fees/{feeId}', [PmbController::class, 'updateFee'])->middleware('can:pmb-manage')->name('fees.update');
    Route::delete('/fees/{feeId}', [PmbController::class, 'destroyFee'])->middleware('can:pmb-manage')->name('fees.destroy');
    Route::patch('/registrations/{registrationId}/status', [PmbController::class, 'updateRegistrationStatus'])
        ->middleware('can:pmb-review')
        ->name('registrations.status');
    Route::put('/registrations/{registrationId}', [PmbController::class, 'updateRegistrationDetails'])
        ->middleware('can:pmb-registration-manage')
        ->name('registrations.update');
    Route::delete('/registrations/{registrationId}', [PmbController::class, 'destroyRegistration'])
        ->middleware('can:pmb-registration-manage')
        ->name('registrations.destroy');
    Route::patch('/payments/{paymentId}', [PmbController::class, 'verifyPayment'])
        ->middleware('can:pmb-payment-review')
        ->name('payments.verify');
});

// Rute yang membutuhkan Login (Untuk Manajemen Pembuatan Link)
Route::middleware(['auth'])->group(function () {
    Route::resource('short-links', ShortLinkController::class); 
    Route::get('short-links/{id}/qr-data', [ShortLinkController::class, 'getQrData'])->name('short-links.qr');
    Route::post('/qr/generate-instant', [ShortLinkController::class, 'generateInstantQr'])->name('qr.generate-instant');
});

Route::prefix('apps')->name('apps.')->group(function () {
    // Resource route otomatis mencakup 7 method CRUD (index, create, store, show, edit, update, destroy)
    Route::resource('activity-types', ActivityTypeController::class);
});

// Rute Umum/Publik diletakkan di luar middleware auth (Taruh di baris paling bawah file web.php)
// Skema URL: iam.cwcu.ac.id/s/{kode}
Route::get('/s/{code}', [ShortLinkController::class, 'handleRedirect'])->name('shortlink.redirect');
 

Route::get('/', function () {
    $referral = \App\Models\Pmb\Referral::where('owner_id', auth()->id())->where('is_active', true)->first();
    return view('dashboard', compact('referral'));
})->middleware(['auth', 'verified', '2fa'])->name('dashboard');
    
// Halaman Utama setelah lolos semua gerbang keamanan
Route::get('/dashboard', function () {
    $referral = \App\Models\Pmb\Referral::where('owner_id', auth()->id())->where('is_active', true)->first();
    return view('dashboard', compact('referral'));
})->middleware(['auth', 'verified', '2fa'])->name('dashboard');

// Route untuk Tampilan (Halaman Kosong)
Route::view('/admin/users/data', 'admin.users.index')->name('admin.users.data');

Route::get('/check-new-notifications', function () {
    $user = Auth::user();
    $unreadCount = $user->unreadNotifications->count();

    // Render view partial menjadi string HTML
    // Kita asumsikan file view list notifikasi ada di resources/views/partials/notification-list.blade.php
    $html = view('partials.notification-list')->render();

    return response()->json([
        'unread_count' => $unreadCount,
        'notification_html' => $html
    ]);
})->middleware('auth');

// Masukkan ke dalam middleware admin agar aman
Route::prefix('admin')->middleware(['auth', 'verified', '2fa'])->group(function () {

    // Resource Utama User
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');

    // Modul Kredensial (Brankas Password)
    Route::get('/users/{hashid}/credential', [UserController::class, 'showCredential'])->name('admin.user.credential');
    Route::post('/users/{hashid}/reset-password', [UserController::class, 'resetPassword'])->name('admin.users.reset');

    // Modul Role (Spatie)
    Route::get('/users/{hashid}/roles', [UserController::class, 'getRoles'])->name('admin.users.get_roles');
    Route::post('/users/{hashid}/roles', [UserController::class, 'updateRoles'])->name('admin.users.update_roles');

    // (Opsional) Modul Permission Spesifik
    Route::get('/users/{hashid}/permissions', [UserController::class, 'getPermissions'])->name('admin.users.get_permissions');
    Route::post('/users/{hashid}/permissions', [UserController::class, 'updatePermissions'])->name('admin.users.update_permissions');

    // logs Role (Spatie)
    Route::get('/users/{hashid}/logs', [UserController::class, 'getUserLogs'])->name('admin.users.logs');

    // Route untuk CRUD API Client
    // Route::resource('api-clients', ApiClientController::class);
    // // Route tambahan untuk toggle status aktif/nonaktif secara cepat
    // Route::post('api-clients/{id}/toggle', [ApiClientController::class, 'toggleStatus'])->name('api-clients.toggle');
    Route::post('system/clear-cache', [PassportClientController::class, 'clearSystemCache'])->name('admin.clear-cache');
    Route::post('api-clients/{id}/regenerate-secret', [PassportClientController::class, 'regenerateSecret']);
    Route::resource('api-clients', PassportClientController::class)->except(['create', 'edit']);
});

// Rute Pengaturan Akun Bersifat Universal
Route::middleware(['auth'])->prefix('account')->group(function () {
    Route::put('/update-profile', [AccountController::class, 'updateProfile']);
    Route::put('/update-password', [AccountController::class, 'updatePassword']);
});

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::resource('roles', RoleController::class);
});

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::resource('permissions', PermissionController::class);
});

Route::get('/mark-notifications-read', function () {
    Auth::user()->unreadNotifications->markAsRead();
    return response()->json(['status' => 'success']);
});
