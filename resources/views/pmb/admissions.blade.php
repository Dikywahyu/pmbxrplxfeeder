@extends('layouts.pmb-public')

@section('title', 'Jalur & Jadwal Pendaftaran PMB | Universitas Cipta Wacana')
@section('meta_description', 'Lihat jalur Penerimaan Mahasiswa Baru Universitas Cipta Wacana yang sedang dibuka dan jadwal yang akan datang.')
@section('canonical_url', route('pmb.admissions'))

@push('styles')
<style>
    :root { --cwcu-red:#d71920; --cwcu-deep:#8d1118; --cwcu-ink:#203d48; --cwcu-muted:#6d7b80; }
    body { color:var(--cwcu-ink); background:#fffaf9; }
    .admissions-nav { border-bottom:1px solid #eee; background:#fff; }
    .admissions-nav .container { min-height:72px; }
    .admissions-logo { width:48px; height:48px; object-fit:contain; }
    .admissions-title { color:#263d43; font-weight:800; line-height:1.1; }
    .admissions-title small { display:block; margin-top:.25rem; color:var(--cwcu-muted); font-size:.65rem; letter-spacing:.08em; }
    .admissions-hero { position:relative; overflow:hidden; padding:clamp(2.5rem,6vw,4.5rem) 0; background:linear-gradient(110deg,#95121a,#d71920 68%,#eb4238); color:#fff; }
    .admissions-hero::after { position:absolute; width:390px; height:390px; right:-90px; top:-245px; border:1px solid rgba(255,255,255,.3); border-radius:50%; box-shadow:0 0 0 45px rgba(255,255,255,.05),0 0 0 90px rgba(255,255,255,.05); content:''; }
    .admissions-hero .container { position:relative; z-index:1; }
    .admissions-hero h1 { max-width:750px; margin:.8rem 0; color:#fff; font-size:clamp(2rem,4vw,3.35rem); font-weight:850; letter-spacing:-.04em; }
    .admissions-hero p { max-width:650px; margin:0; color:rgba(255,255,255,.84); line-height:1.7; }
    .admissions-main { padding:2.5rem 0 4rem; }
    .admissions-toolbar { display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:1.4rem; }
    .admissions-filters { display:flex; flex-wrap:wrap; gap:.5rem; }
    .admissions-filter { padding:.55rem 1rem; border:1px solid #eadbd9; border-radius:999px; background:#fff; color:#5d6769; font-size:.84rem; font-weight:700; transition:all .18s ease; }
    .admissions-filter:hover,.admissions-filter.active { border-color:var(--cwcu-red); background:var(--cwcu-red); color:#fff; }
    .admissions-search { width:min(100%,280px); padding:.65rem .9rem; border:1px solid #eadbd9; border-radius:.7rem; outline:none; }
    .admissions-search:focus { border-color:#d71920; box-shadow:0 0 0 3px rgba(215,25,32,.1); }
    .admissions-card { position:relative; display:flex; height:100%; flex-direction:column; padding:1.25rem; border:1px solid #f0e3e1; border-radius:1rem; background:#fff; box-shadow:0 9px 25px rgba(70,32,30,.055); transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease; }
    .admissions-card:hover { transform:translateY(-4px); border-color:#e6b7b4; box-shadow:0 16px 32px rgba(115,33,31,.12); }
    .admissions-card-top { display:flex; align-items:flex-start; justify-content:space-between; gap:.75rem; }
    .admissions-card-icon { display:grid; width:46px; height:46px; flex:0 0 46px; place-items:center; border-radius:14px; background:#fff0ee; color:var(--cwcu-red); font-size:1.35rem; }
    .admissions-card h2 { margin:1rem 0 .25rem; color:var(--cwcu-ink); font-size:1.15rem; font-weight:800; }
    .admissions-period { color:#566a70; font-size:.9rem; font-weight:650; }
    .admissions-description { min-height:2.8em; margin:.65rem 0 1rem; color:var(--cwcu-muted); font-size:.85rem; line-height:1.65; }
    .admissions-dates { display:flex; align-items:center; gap:.5rem; padding:.7rem .75rem; border-radius:.7rem; background:#fff7f5; color:#614f4f; font-size:.8rem; }
    .admissions-dates i { color:var(--cwcu-red); }
    .admissions-card .btn { margin-top:auto; }
    .admissions-empty { padding:3rem 1rem; border:1px dashed #e5cbc8; border-radius:1rem; background:#fff; color:var(--cwcu-muted); text-align:center; }
    .admissions-empty i { display:block; margin-bottom:.5rem; color:#c14b4b; font-size:2rem; }
    .admissions-footer { padding:1.5rem 0; border-top:1px solid #eee; background:#fff; color:var(--cwcu-muted); font-size:.82rem; }
    @media(max-width:575.98px) { .admissions-nav .container { min-height:64px; } .admissions-logo { width:42px;height:42px; } .admissions-title { font-size:.87rem; } .admissions-title small { font-size:.55rem; } .admissions-main { padding-top:1.6rem; } .admissions-search { width:100%; } }
</style>
@endpush

@section('content')
@php($startUrl = auth()->check() ? route('pmb.index') : route('login'))
<header class="admissions-nav">
    <div class="container d-flex align-items-center justify-content-between gap-3">
        <a class="d-flex align-items-center gap-2 text-decoration-none" href="{{ route('home') }}">
            <img class="admissions-logo" src="{{ asset('logo.png') }}" alt="Logo Universitas Cipta Wacana">
            <span class="admissions-title">Universitas Cipta Wacana<small>INFORMASI PENERIMAAN MAHASISWA BARU</small></span>
        </a>
        <a class="btn btn-danger rounded-pill px-3" href="{{ $startUrl }}">{{ auth()->check() ? 'Pendaftaran saya' : 'Masuk / Daftar' }}</a>
    </div>
</header>
<section class="admissions-hero">
    <div class="container">
        <a href="{{ route('home') }}" class="small text-white text-decoration-none"><i class="ti ti-arrow-left me-1"></i>Beranda</a>
        <h1>Informasi jalur pendaftaran</h1>
        <p>Temukan jalur yang sedang dibuka dan jadwal berikutnya. Halaman ini dapat dilihat tanpa masuk ke akun.</p>
    </div>
</section>
<main class="admissions-main">
    <div class="container">
        @php($allAdmissionSchedules = $openAdmissionSchedules->concat($upcomingAdmissionSchedules)->sortBy('tanggal_mulai')->values())
        <div class="admissions-toolbar">
            <div class="admissions-filters" role="group" aria-label="Filter status jalur">
                <button class="admissions-filter active" type="button" data-admission-filter="all">Semua <span class="ms-1">{{ $allAdmissionSchedules->count() }}</span></button>
                <button class="admissions-filter" type="button" data-admission-filter="open">Sedang dibuka <span class="ms-1">{{ $openAdmissionSchedules->count() }}</span></button>
                <button class="admissions-filter" type="button" data-admission-filter="upcoming">Akan dibuka <span class="ms-1">{{ $upcomingAdmissionSchedules->count() }}</span></button>
            </div>
            <input class="admissions-search" type="search" id="admissionSearch" placeholder="Cari jalur atau periode..." aria-label="Cari jalur atau periode">
        </div>
        <div class="row g-3" id="admissionCards">
            @forelse ($allAdmissionSchedules as $schedule)
                @php($isOpen = $schedule->tanggal_mulai->lte(today()))
                <div class="col-12 col-md-6 col-xl-4 admission-card-wrap" data-status="{{ $isOpen ? 'open' : 'upcoming' }}" data-search="{{ strtolower($schedule->path->nama_jalur.' '.$schedule->period->nama_periode) }}">
                    <article class="admissions-card">
                        <div class="admissions-card-top">
                            <span class="admissions-card-icon"><i class="ti {{ $isOpen ? 'ti-route' : 'ti-calendar-event' }}"></i></span>
                            <span class="badge rounded-pill {{ $isOpen ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning-emphasis' }}">{{ $isOpen ? 'Sedang dibuka' : 'Akan dibuka' }}</span>
                        </div>
                        <h2>{{ $schedule->path->nama_jalur }}</h2>
                        <div class="admissions-period">{{ $schedule->period->nama_periode }}</div>
                        <p class="admissions-description">{{ $schedule->path->deskripsi ?: 'Jalur penerimaan mahasiswa baru Universitas Cipta Wacana.' }}</p>
                        <div class="admissions-dates mb-3"><i class="ti ti-calendar"></i><span>{{ $schedule->tanggal_mulai->format('d M Y') }} <span class="mx-1">–</span> {{ $schedule->tanggal_selesai->format('d M Y') }}</span></div>
                        @if ($isOpen)
                            <a class="btn btn-danger rounded-pill w-100" href="{{ route('pmb.index', ['path_id' => $schedule->path_id, 'period_id' => $schedule->period_id]) }}">Pilih jalur ini <i class="ti ti-arrow-up-right ms-1"></i></a>
                        @else
                            <button class="btn btn-outline-secondary rounded-pill w-100" type="button" disabled>Belum dibuka</button>
                        @endif
                    </article>
                </div>
            @empty
                <div class="col-12"><div class="admissions-empty"><i class="ti ti-calendar-off"></i>Belum ada jalur pendaftaran aktif yang diumumkan.</div></div>
            @endforelse
        </div>
        <div class="admissions-empty mt-3 d-none" id="admissionNoMatch"><i class="ti ti-search-off"></i>Tidak ada jalur yang cocok dengan pencarian.</div>
    </div>
</main>
<footer class="admissions-footer"><div class="container d-flex flex-wrap justify-content-between gap-2"><span>Portal Penerimaan Mahasiswa Baru · Universitas Cipta Wacana</span><a class="text-decoration-none" href="{{ route('home') }}">Kembali ke beranda</a></div></footer>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const filters = document.querySelectorAll('[data-admission-filter]');
        const cards = document.querySelectorAll('.admission-card-wrap');
        const search = document.getElementById('admissionSearch');
        const noMatch = document.getElementById('admissionNoMatch');
        let activeFilter = 'all';
        const updateCards = () => {
            const term = (search?.value || '').trim().toLocaleLowerCase('id');
            let visibleCount = 0;
            cards.forEach((card) => {
                const matchesFilter = activeFilter === 'all' || card.dataset.status === activeFilter;
                const matchesSearch = !term || card.dataset.search.includes(term);
                const visible = matchesFilter && matchesSearch;
                card.classList.toggle('d-none', !visible);
                if (visible) visibleCount++;
            });
            noMatch?.classList.toggle('d-none', visibleCount > 0 || cards.length === 0);
        };
        filters.forEach((button) => button.addEventListener('click', () => {
            activeFilter = button.dataset.admissionFilter;
            filters.forEach((item) => item.classList.toggle('active', item === button));
            updateCards();
        }));
        search?.addEventListener('input', updateCards);
    });
</script>
@endsection
