<x-filament-panels::page.simple>
    <div class="simonika-split-wrapper">
        <!-- Top Rainbow Brand Bar (5 Palette Colors) -->
        <div class="simonika-rainbow-bar"></div>

        <!-- ── KOLOM KIRI: BRANDING PUSAT (LOGO BESAR & RINGKASAN RESMI) ── -->
        <div class="simonika-split-left">
            <!-- Decorative Ambient Glowing Light Spots -->
            <div class="simonika-glow glow-cyan"></div>
            <div class="simonika-glow glow-amber"></div>
            <div class="simonika-glow glow-violet"></div>

            <div class="simonika-brand-center-box">
                <!-- Logo SIMONIKA Besar di Tengah -->
                <div class="simonika-big-logo-wrap">
                    <img 
                        src="{{ asset('images/simonika-icon.png') }}" 
                        alt="Logo SIMONIKA" 
                        class="simonika-big-logo"
                    />
                </div>

                <!-- Nama Brand SIMONIKA -->
                <h1 class="simonika-brand-main-title">
                    SIMONIKA
                </h1>

                <!-- PPSDMAP di Bawah Nama SIMONIKA -->
                <div class="simonika-ppsdmap-wrap">
                    <span class="simonika-ppsdmap-badge">PPSDMAP</span>
                    <p class="simonika-ppsdmap-full">
                        Pusat Pengembangan Sumber Daya Manusia Aparatur Perhubungan
                    </p>
                </div>

                <!-- Tagline Resmi -->
                <div class="simonika-tagline-box">
                    <p class="simonika-tagline-quote">
                        “Terintegrasi dalam Data, Unggul dalam Kinerja”
                    </p>
                </div>

                <!-- Ringkasan Sistem (Ringkas, Padat, dan Estetik) -->
                <div class="simonika-quick-summary">
                    <div class="summary-item">
                        <span class="summary-icon icon-blue">📊</span>
                        <div class="summary-text">
                            <strong>7 IKK Utama</strong>
                            <small>Pemantauan Kinerja</small>
                        </div>
                    </div>
                    <div class="summary-item">
                        <span class="summary-icon icon-emerald">💰</span>
                        <div class="summary-text">
                            <strong>Pagu Anggaran</strong>
                            <small>Tracking Realisasi</small>
                        </div>
                    </div>
                    <div class="summary-item">
                        <span class="summary-icon icon-amber">⚡</span>
                        <div class="summary-text">
                            <strong>Pelaporan Final</strong>
                            <small>Persetujuan Bulanan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── KOLOM KANAN: FORM LOGIN ELEGAN & VIBRAN ── -->
        <div class="simonika-split-right">
            <div class="simonika-form-header">
                <div class="simonika-portal-tag">
                    <span class="tag-icon">🔐</span>
                    <span>Portal Masuk Terverifikasi</span>
                </div>
                <h2 class="simonika-form-title">
                    Selamat Datang di SIMONIKA
                </h2>
                <p class="simonika-form-subtitle">
                    Silakan masukkan email dan kata sandi akun Anda untuk melanjutkan
                </p>
            </div>

            <!-- Form Login Livewire Natively dari Filament -->
            <div class="simonika-form-body">
                {{ $this->content }}
            </div>

            <!-- Footer Hak Cipta -->
            <div class="simonika-login-footer">
                <p class="simonika-footer-copy">© {{ date('Y') }} PPSDMAP - Kementerian Perhubungan RI</p>
                <p class="simonika-footer-sub">All rights reserved</p>
            </div>
        </div>
    </div>
</x-filament-panels::page.simple>
