@php
    $isSimplePage = request()->routeIs('*login*') || request()->routeIs('*auth*') || request()->routeIs('*password*');
@endphp

@if($isSimplePage)
    {{-- Tampilan Logo Penuh Elegan Khusus Halaman Login / Auth --}}
    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 4px 0 10px 0; text-align: center;">
        <img 
            src="{{ asset('images/simonika-full-trans.png') }}" 
            alt="SIMONIKA PPSDMAP" 
            style="height: 150px; width: auto; max-width: 290px; object-fit: contain; filter: drop-shadow(0 8px 20px rgba(15, 44, 89, 0.15));"
        />
    </div>
@else
    {{-- Tampilan Logo Navigasi / Sidebar & Topbar (Kompak, Responsif & Elegan) --}}
    <div style="display: inline-flex; align-items: center; gap: 11px; height: 38px; max-height: 38px; overflow: hidden; vertical-align: middle;">
        <!-- SIMONIKA Modern App Icon Baru -->
        <img 
            src="{{ asset('images/simonika-icon.png') }}" 
            alt="SIMONIKA" 
            style="width: 35px; height: 35px; min-width: 35px; max-width: 35px; object-fit: contain; border-radius: 8px; flex-shrink: 0; box-shadow: 0 2px 8px rgba(15, 44, 89, 0.22);" 
        />

        <!-- Teks Branding SIMONIKA PPSDMAP -->
        <div style="display: flex; flex-direction: column; text-align: left; line-height: 1.15; justify-content: center;">
            <div style="display: flex; align-items: center; gap: 6px;">
                <span style="font-size: 15.5px; font-weight: 900; letter-spacing: 0.02em; color: #0b2545; white-space: nowrap;" class="dark:text-white">
                    SIMONIKA
                </span>
                <span style="font-size: 8px; font-weight: 800; background: #e0f2fe; color: #0284c7; padding: 1.5px 5.5px; border-radius: 4px; border: 1px solid #bae6fd; letter-spacing: 0.04em; text-transform: uppercase;">
                    PPSDMAP
                </span>
            </div>
            <span style="font-size: 8.5px; font-weight: 600; color: #64748b; letter-spacing: 0.01em; white-space: nowrap; margin-top: 2px;" class="dark:text-slate-400">
                Sistem Informasi Monitoring Kinerja dan Anggaran
            </span>
        </div>
    </div>
@endif
