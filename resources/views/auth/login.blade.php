@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')

{{-- ===== Panel kiri: branding ===== --}}
<div class="brand-pane hidden lg:flex flex-col lg:w-1/2 relative overflow-hidden">
    <div class="relative z-10 flex-col justify-center gap-10 flex-1 p-12 xl:p-16 w-full text-white">
        {{-- Logo --}}
        <div class="flex items-center gap-3 fade-up">
            <div class="w-11 h-11 rounded-2xl bg-white/15 backdrop-blur-md border-white/25 flex items-center justify-center font-bold text-xl shadow-lg">
                W
            </div>
            <div>
                <p class="font-bold text-lg leading-tight tracking-tight">Warna Tanjung Jaya</p>
                <p class="text-xs text-blue-200/90 font-medium">Laporan Fiqri</p>
            </div>
        </div>

        {{-- Teks utama --}}
        <div class="fade-up fade-up-2">
            <div class="inline-flex items-center gap-2 rounded-full bg-white/10 border-white/20 px-3 py-1 text-xs font-medium text-blue-100 backdrop-blur-md mb-6">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                Sistem Laporan Oplosan
            </div>
            <h1 class="text-4xl xl:text-5xl font-bold leading-[1.1] tracking-tight">
                Kelola laporan<br>oplosan dengan <span class="text-blue-300">mudah</span>.
            </h1>
            <p class="mt-5 text-blue-100/80 text-base max-w-md leading-relaxed">
                Catat tinting per unit, pantau riwayat order, dan ekspor laporan PDF — semua dalam satu tempat.
            </p>

            {{-- Poin keunggulan --}}
            <div class="mt-9 space-y-3">
                @foreach ([
                    'Input laporan oplosan per unit kendaraan',
                    'Riwayat order tercatat rapi & mudah dicari',
                    'Preview & ekspor PDF sekali klik',
                ] as $point)
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 shrink-0 rounded-lg bg-white/15 border-white/20 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </span>
                        <span class="text-sm text-blue-50/90">{{ $point }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Footer panel --}}
        <div class="fade-up fade-up-3 mt-16 pt-6 border-t border-white/10 text-xs text-blue-200/70">
            © {{ date('Y') }} Warna Tanjung Jaya · Semua hak dilindungi
        </div>
    </div>

    {{-- Ornamen dekoratif --}}
    <div class="floaty absolute -top-16 -right-16 w-72 h-72 rounded-full bg-blue-400/20 blur-3xl"></div>
    <div class="floaty absolute bottom-0 -left-20 w-80 h-80 rounded-full bg-indigo-400/20 blur-3xl" style="animation-delay: 2s"></div>
</div>

{{-- ===== Panel kanan: form login ===== --}}
<div class="auth-pane w-full lg:w-1/2 flex items-center justify-center px-5 sm:px-8 py-10">
    <div class="w-full max-w-md">

        {{-- Logo versi mobile --}}
        <div class="lg:hidden text-center mb-8 fade-up">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/30 text-white font-bold text-2xl mb-3">
                W
            </div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Warna Tanjung Jaya</h1>
            <p class="text-sm text-slate-500 mt-0.5">Laporan Fiqri · Sistem Laporan Oplosan</p>
        </div>

        {{-- Kartu Login --}}
        <div class="fade-up fade-up-2 bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl shadow-slate-900/5 ring-1 ring-white/70 p-6 sm:p-8">
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Masuk ke Sistem</h2>
            <p class="text-sm text-slate-500 mt-1 mb-6">Pilih peranmu, lalu masukkan akun.</p>

            {{-- Dua tombol pilihan peran.
                 Dibungkus satu grup supaya jelas bahwa pilihan ini saling
                 menggantikan, bukan dua tombol yang bisa menyala bersama. --}}
            <div class="grid grid-cols-2 gap-3 mb-4" role="radiogroup" aria-label="Pilih peran">
                <button type="button" data-role="tinter" data-accent="blue" role="radio" aria-checked="true" aria-label="Peran tinter: bikin laporan"
                        class="role-btn is-active group relative rounded-2xl border-2 border-slate-200 bg-white p-4 text-left transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    <span class="role-check absolute top-3 right-3 w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center opacity-100 scale-100 transition-all duration-300">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-blue-500/30 mb-3 transition-transform duration-300 group-hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-slate-800">Bikin Laporan</p>
                    <p class="text-xs text-slate-500 mt-0.5">Isi & kelola data</p>
                </button>

                <button type="button" data-role="manajer" data-accent="emerald" role="radio" aria-checked="false" aria-label="Peran manajer: cek laporan"
                        class="role-btn group relative rounded-2xl border-2 border-slate-200 bg-white p-4 text-left transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    <span class="role-check absolute top-3 right-3 w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center opacity-0 scale-75 transition-all duration-300">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-md shadow-emerald-500/30 mb-3 transition-transform duration-300 group-hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-slate-800">Cek Laporan</p>
                    <p class="text-xs text-slate-500 mt-0.5">Manajer · lihat saja</p>
                </button>
            </div>

            {{-- Info mode terpilih --}}
            <div id="roleInfo" class="mb-5 flex items-center gap-2 text-xs font-medium text-slate-600 bg-slate-100/80 rounded-xl px-3 py-2.5">
                <span id="roleDot" class="h-2 w-2 shrink-0 rounded-full bg-blue-600"></span>
                <span id="roleInfoText">Mode: Bikin Laporan (bisa isi data)</span>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                @if ($errors->any())
                    <div class="flex items-start gap-2.5 rounded-xl bg-rose-500/10 ring-1 ring-rose-500/20 text-rose-700 px-4 py-3 text-sm fade-up">
                        <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <svg class="w-4.5 h-4.5" style="width:1.125rem;height:1.125rem" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                               placeholder="nama@warnatanjungjaya.com"
                               class="w-full rounded-xl border-slate-200 bg-white/90 pl-11 pr-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <svg style="width:1.125rem;height:1.125rem" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        <input type="password" name="password" id="password" required
                               placeholder="Masukkan password"
                               class="w-full rounded-xl border-slate-200 bg-white/90 pl-11 pr-11 py-3 text-sm text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                        <button type="button" onclick="togglePassword()" aria-label="Tampilkan password"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 transition-colors">
                            <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="eyeOffIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500/30">
                        Ingat saya
                    </label>
                </div>

                <button type="submit"
                        class="group w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-3 rounded-xl text-sm font-semibold shadow-lg shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/35 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300">
                    <span>Masuk</span>
                    <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
            </form>
        </div>

        <p class="lg:hidden text-center text-xs text-slate-400 mt-6">© {{ date('Y') }} Warna Tanjung Jaya</p>
    </div>
</div>

@push('scripts')
<script>
    const roleInfoText = document.getElementById('roleInfoText');
    const roleDot = document.getElementById('roleDot');
    const roleLabels = {
        tinter: 'Mode: Bikin Laporan (bisa isi data)',
        manajer: 'Mode: Cek Laporan (hanya melihat)',
    };

    function selectRole(btn) {
        // Reset semua tombol
        document.querySelectorAll('.role-btn').forEach(b => {
            b.classList.remove('is-active');
            b.setAttribute('aria-checked', 'false');
            b.querySelector('.role-check').classList.add('opacity-0', 'scale-75');
            b.querySelector('.role-check').classList.remove('opacity-100', 'scale-100');
        });

        // Tandai tombol terpilih
        btn.classList.add('is-active');
        btn.setAttribute('aria-checked', 'true');
        const check = btn.querySelector('.role-check');
        check.classList.remove('opacity-0', 'scale-75');
        check.classList.add('opacity-100', 'scale-100');

        // Warna badge centang mengikuti aksen tombol
        check.classList.toggle('bg-blue-600', btn.dataset.accent === 'blue');
        check.classList.toggle('bg-emerald-600', btn.dataset.accent === 'emerald');

        // Titik penanda mode ikut warna perannya, jadi tidak ada
        // satu pun elemen biru saat mode manajer sedang terpilih.
        if (roleDot) {
            roleDot.classList.toggle('bg-blue-600', btn.dataset.accent === 'blue');
            roleDot.classList.toggle('bg-emerald-600', btn.dataset.accent === 'emerald');
        }

        // Update info mode
        roleInfoText.textContent = roleLabels[btn.dataset.role];
    }

    document.querySelectorAll('.role-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            selectRole(btn);

            // Fokus diarahkan ke kolom email supaya bisa langsung
            // mengetik memakai keyboard, tanpa klik ulang.
            document.getElementById('email')?.focus();
        });
    });

    function togglePassword() {
        const input = document.getElementById('password');
        const eye = document.getElementById('eyeIcon');
        const eyeOff = document.getElementById('eyeOffIcon');
        const show = input.type === 'password';

        input.type = show ? 'text' : 'password';
        eye.classList.toggle('hidden', show);
        eyeOff.classList.toggle('hidden', !show);
    }

    // Tampilkan status awal tombol "Bikin Laporan"
    document.addEventListener('DOMContentLoaded', () => {
        selectRole(document.querySelector('.role-btn[data-role="tinter"]'));
    });
</script>
@endpush
@endsection
