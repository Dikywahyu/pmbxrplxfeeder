<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\PassportClientController;
use App\Http\Controllers\PmbController;
use App\Http\Controllers\PmbClaimController;
use App\Http\Controllers\Admin\PmbMarketingController;
use App\Http\Controllers\CbtController;


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


/*
|--------------------------------------------------------------------------
| Public Routes (Dapat Diakses Tanpa Login / Pihak Luar)
|--------------------------------------------------------------------------
*/
// Form Presensi via Scan QR Code (Peserta Internal & Tamu Eksternal)

Route::get('/pmb/claim/{token}', [PmbClaimController::class, 'show'])
    ->where('token', '[A-Za-z0-9]{64}')
    ->name('pmb.claim.show');
Route::post('/pmb/claim/{token}', [PmbClaimController::class, 'claim'])
    ->where('token', '[A-Za-z0-9]{64}')
    ->middleware('throttle:10,1')
    ->name('pmb.claim.store');

Route::middleware('guest')->group(function () {
    Route::get('/login', fn() => redirect('/auth/login'));
    Route::post('/login', [LoginController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Authenticated App Routes (Prefix: /apps)
|--------------------------------------------------------------------------
*/


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
    Route::post('/marketing/registrations', [PmbMarketingController::class, 'store'])->middleware('can:pmb-marketing')->name('marketing.registrations.store');
    Route::get('/marketing/registrations/{registrationId}', [PmbMarketingController::class, 'registrationDetail'])->middleware('can:pmb-marketing')->name('marketing.registrations.show');
    Route::post('/marketing/registrations/{registrationId}/claim-link', [PmbMarketingController::class, 'renewClaimLink'])->middleware('can:pmb-marketing')->name('marketing.registrations.claim-link');
    Route::post('/marketing/registrations/{registrationId}/documents/{requirementId}', [PmbMarketingController::class, 'uploadRegistrationDocument'])->middleware('can:pmb-marketing')->name('marketing.registrations.documents.store');
    Route::get('/marketing/registrations/{registrationId}/documents/{documentId}/preview', [PmbMarketingController::class, 'previewRegistrationDocument'])->middleware('can:pmb-marketing')->name('marketing.registrations.documents.preview');
    Route::get('/marketing/registrations/{registrationId}/payments/{paymentId}/proof', [PmbMarketingController::class, 'previewRegistrationPaymentProof'])->middleware('can:pmb-marketing')->name('marketing.registrations.payments.proof');
    Route::post('/marketing/registrations/{registrationId}/payments/{paymentId}/proof', [PmbMarketingController::class, 'uploadRegistrationPaymentProof'])->middleware('can:pmb-marketing')->name('marketing.registrations.payments.proof.store');
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
    Route::post('/period-path-schedules', [PmbController::class, 'savePeriodPathSchedule'])->middleware('can:pmb-manage')->name('schedules.save');
    Route::put('/period-path-schedules', [PmbController::class, 'savePeriodPathSchedule'])->middleware('can:pmb-manage')->name('schedules.update');
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

Route::get('/', function () {
    $referral = \App\Models\Pmb\Referral::where('owner_id', Auth::id())->where('is_active', true)->first();
    return view('dashboard', compact('referral'));
})->middleware(['auth', 'verified', '2fa'])->name('dashboard');

// Halaman Utama setelah lolos semua gerbang keamanan
Route::get('/dashboard', function () {
    $referral = \App\Models\Pmb\Referral::where('owner_id', Auth::id())->where('is_active', true)->first();
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
