<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Gate::before(function (\App\Models\User $user, string $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });
        Gate::define('pmb-cbt-manage', function (\App\Models\User $user) {
            $permissions = $user->getAllPermissions()->pluck('name');
            return $permissions->contains('pmb-manage') || $permissions->contains('pmb-cbt-manage');
        });
        Gate::define('pmb-registration-manage', function (\App\Models\User $user) {
            $permissions = $user->getAllPermissions()->pluck('name');
            return $permissions->contains('pmb-manage') || $permissions->contains('pmb-registration-manage');
        });

        \Carbon\Carbon::setLocale('id');
        \App\Models\User::observe(\App\Observers\UserObserver::class);

        if ($this->app->runningInConsole()) {
            // Daftarkan folder migrasi tambahan (Kanban)
            $this->loadMigrationsFrom(database_path('migrations/Pmb'));
        }

        $this->loadViewsFrom(base_path('vendor/laravel/passport/resources/views'), 'passport');

        // 1. DAFTARKAN MODEL KUSTOM DI SINI (Cukup Satu Kali Saja)
        \Laravel\Passport\Passport::useClientModel(\App\Models\PassportClient::class);

        // 2. IKAT RESPONSE INTERFACE PASSPORT
        $this->app->bind(
            \Laravel\Passport\Contracts\AuthorizationViewResponse::class,
            \App\Http\Responses\SSOAuthorizeResponse::class
        );

        View::composer(['layouts.partials.*', 'layouts.parsial.*', 'layouts.partials.aside', 'layouts.partials.header'], function ($view) {
            $rawMenus = [
                [
                    'title' => 'Dashboard',
                    'icon' => 'solar:home-line-duotone',
                    'url' => url('/dashboard'),
                    'category' => 'Home',
                    'apps_menu' => true
                ],
                [
                    'title' => 'Pendaftaran PMB',
                    'icon' => 'solar:document-add-line-duotone',
                    'url' => route('pmb.index'),
                    'category' => 'Penerimaan Mahasiswa Baru',
                    'apps_menu' => true,
                    'active' => request()->is('pmb*'),
                ],
                [
                    'title' => 'Admin PMB',
                    'icon' => 'solar:clipboard-check-line-duotone',
                    'url' => route('admin.pmb.index'),
                    'category' => 'Penerimaan Mahasiswa Baru',
                    'apps_menu' => true,
                    'active' => request()->is('admin/pmb'),
                    'permission' => 'pmb-access',
                ],
                [
                    'title' => 'Pendaftar PMB',
                    'icon' => 'solar:users-group-rounded-line-duotone',
                    'url' => route('admin.pmb.section', ['section' => 'registrations']),
                    'category' => 'Penerimaan Mahasiswa Baru',
                    'apps_menu' => true,
                    'active' => request()->is('admin/pmb/section/registrations'),
                    'permission' => 'pmb-access',
                ],
                [
                    'title' => 'Periode PMB',
                    'icon' => 'solar:calendar-date-line-duotone',
                    'url' => route('admin.pmb.section', ['section' => 'periods']),
                    'category' => 'Penerimaan Mahasiswa Baru',
                    'apps_menu' => true,
                    'active' => request()->is('admin/pmb/section/periods'),
                    'permission' => 'pmb-manage',
                ],
                [
                    'title' => 'Jalur Pendaftaran',
                    'icon' => 'solar:route-line-duotone',
                    'url' => route('admin.pmb.section', ['section' => 'paths']),
                    'category' => 'Penerimaan Mahasiswa Baru',
                    'apps_menu' => true,
                    'active' => request()->is('admin/pmb/section/paths'),
                    'permission' => 'pmb-manage',
                ],
                [
                    'title' => 'Program Studi PMB',
                    'icon' => 'solar:square-academic-cap-line-duotone',
                    'url' => route('admin.pmb.section', ['section' => 'programs']),
                    'category' => 'Penerimaan Mahasiswa Baru',
                    'apps_menu' => true,
                    'active' => request()->is('admin/pmb/section/programs'),
                    'permission' => 'pmb-manage',
                ],
                [
                    'title' => 'Persyaratan Berkas',
                    'icon' => 'solar:document-text-line-duotone',
                    'url' => route('admin.pmb.section', ['section' => 'requirements']),
                    'category' => 'Penerimaan Mahasiswa Baru',
                    'apps_menu' => true,
                    'active' => request()->is('admin/pmb/section/requirements'),
                    'permission' => 'pmb-manage',
                ],
                [
                    'title' => 'Biaya PMB',
                    'icon' => 'solar:wallet-money-line-duotone',
                    'url' => route('admin.pmb.section', ['section' => 'fees']),
                    'category' => 'Penerimaan Mahasiswa Baru',
                    'apps_menu' => true,
                    'active' => request()->is('admin/pmb/section/fees'),
                    'permission' => 'pmb-manage',
                ],
                [
                    'title' => 'Marketing & Referral',
                    'icon' => 'solar:share-circle-line-duotone',
                    'url' => route('admin.pmb.section', ['section' => 'referrals']),
                    'category' => 'Penerimaan Mahasiswa Baru',
                    'apps_menu' => true,
                    'active' => request()->is('admin/pmb/section/referrals'),
                    'permission' => 'pmb-manage',
                ],
                [
                    'title' => 'Input Pendaftar PMB',
                    'icon' => 'solar:user-plus-line-duotone',
                    'url' => route('admin.pmb.section', ['section' => 'marketing']),
                    'category' => 'Penerimaan Mahasiswa Baru',
                    'apps_menu' => true,
                    'active' => request()->is('admin/pmb/section/marketing'),
                    'permission' => 'pmb-marketing',
                ],
                [
                    'title' => 'Ujian CBT',
                    'icon' => 'solar:clipboard-list-line-duotone',
                    'url' => route('pmb.exams.index'),
                    'category' => 'Penerimaan Mahasiswa Baru',
                    'apps_menu' => true,
                    'active' => request()->is('pmb/exams*'),
                ],
                [
                    'title' => 'Manajemen CBT',
                    'icon' => 'solar:document-add-line-duotone',
                    'url' => route('admin.cbt.index'),
                    'category' => 'Penerimaan Mahasiswa Baru',
                    'apps_menu' => true,
                    'active' => request()->is('admin/cbt*'),
                    'permission' => 'pmb-cbt-manage',
                ],


                // --- CATEGORY: ADMINISTRATOR ---
                [
                    'title' => 'Manajemen User',
                    'icon' => 'solar:users-group-two-rounded-line-duotone',
                    'url' => route('admin.users.data'),
                    'category' => 'Administrator',
                    'apps_menu' => true,
                    'active' => request()->is('admin/users/data'),
                    'permission' => 'user-list' // <-- Nama permission Spatie / Gate Anda
                ],
                [
                    'title' => 'User Roles',
                    'icon' => 'solar:user-speak-line-duotone',
                    'url' => url('/admin/roles'),
                    'category' => 'Administrator',
                    'apps_menu' => false,
                    'active' => request()->is('admin/roles*'),
                    'permission' => 'role-list' // <-- Hanya user dengan permission ini yang bisa melihat
                ],
                [
                    'title' => 'Permissions',
                    'icon' => 'solar:shield-keyhole-line-duotone',
                    'url' => url('/admin/permissions'),
                    'category' => 'Administrator',
                    'apps_menu' => false,
                    'active' => request()->is('admin/permissions*'),
                    'permission' => 'role-list'
                ],
                [
                    'title' => 'API Client',
                    'icon' => 'solar:code-square-line-duotone',
                    'url' => url('/admin/api-clients'),
                    'category' => 'Administrator',
                    'apps_menu' => false,
                    'active' => request()->is('admin/api-clients*'),
                    'permission' => 'api-list'
                ],
                [
                    'title' => 'Clear Cache',
                    'icon' => 'solar:refresh-square-line-duotone',
                    'url' => 'javascript:void(0)', // Di-set kosong agar tidak pindah halaman
                    'category' => 'Administrator',
                    'apps_menu' => false,
                    'active' => false,
                    'permission' => 'api-list', // Hanya bisa diakses oleh level Admin/Super Admin
                    'class' => 'btn-trigger-clear-cache' // Kita beri penanda class khusus untuk jQuery
                ],
            ];

            // FILTER MENU BERDASARKAN CAN / PERMISSION USER LOGGED IN
            $menus = array_filter($rawMenus, function ($menu) {
                // Jika menu tidak mendefinisikan key 'permission', tampilkan untuk semua user terautentikasi
                if (!isset($menu['permission'])) {
                    return true;
                }

                // Jika user memiliki permission yang sesuai, loloskan menu ini
                return auth()->check() && auth()->user()->can($menu['permission']);
            });

            // Kirim data menu yang sudah difilter ke view
            $view->with('menus', $menus);
        });


        config(['google2fa.view' => 'auth.2fa.index']);
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }
}
