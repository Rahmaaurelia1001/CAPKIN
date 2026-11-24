<x-filament-widgets::widget>
    @php
        $badgeBg = match($urgency) {
            'danger'  => '#fee2e2',
            'warning' => '#fef3c7',
            default   => '#e0f2fe',
        };
        $badgeText = match($urgency) {
            'danger'  => '#991b1b',
            'warning' => '#92400e',
            default   => '#0369a1',
        };
        $badgeBorder = match($urgency) {
            'danger'  => '#fca5a5',
            'warning' => '#fcd34d',
            default   => '#bae6fd',
        };
        $borderColor = match($urgency) {
            'danger'  => '#f87171',
            'warning' => '#fbbf24',
            default   => '#38bdf8',
        };
    @endphp

    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-left: 5px solid {{ $borderColor }}; border-radius: 14px; padding: 18px 24px; box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04); width: 100%; box-sizing: border-box; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;" class="dark:bg-gray-900 dark:border-slate-800">
        
        {{-- Sisi Kiri: Info Tugas & Deadline --}}
        <div style="display: flex; align-items: center; gap: 16px; flex: 1; min-width: 290px;">
            {{-- Ikon Kalender / Jam --}}
            <div style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #f0f9ff, #e0f2fe); border: 1px solid #bae6fd; color: #0284c7; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 8px rgba(2, 132, 199, 0.12);">
                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>

            <div>
                {{-- Baris Label & Badge Sisa Hari --}}
                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 4px;">
                    <span style="font-size: 0.74rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #0369a1; background: #e0f2fe; padding: 3px 10px; border-radius: 6px;">
                        Tugas Anda
                    </span>

                    <span style="background: {{ $badgeBg }}; color: {{ $badgeText }}; border: 1px solid {{ $badgeBorder }}; font-size: 0.76rem; font-weight: 800; padding: 3px 12px; border-radius: 9999px; display: inline-flex; align-items: center; gap: 5px;">
                        @if($urgency === 'danger')
                            🔴 {{ $sisaHari }} Hari Menuju Deadline
                        @elseif($urgency === 'warning')
                            🟡 {{ $sisaHari }} Hari Menuju Deadline
                        @else
                            ⏱️ {{ $sisaHari }} Hari Menuju Deadline
                        @endif
                    </span>
                </div>

                {{-- Headline Batas Waktu --}}
                <div style="font-size: 1.05rem; font-weight: 800; color: #0f172a; letter-spacing: -0.01em;" class="dark:text-white">
                    Deadline Periode {{ $bulanIni }}: <span style="color: #0284c7;">{{ $config['deadline'] }}</span>
                </div>

                {{-- Deskripsi Tugas --}}
                <div style="font-size: 0.83rem; color: #64748b; margin-top: 3px; font-weight: 500; line-height: 1.4;" class="dark:text-slate-400">
                    {{ $config['tugas_utama'] }}
                </div>
            </div>
        </div>

        {{-- Sisi Kanan: Action Button & Info Email --}}
        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 8px; flex-shrink: 0;">
            <a href="{{ $config['action_url'] }}" style="display: inline-flex; align-items: center; gap: 8px; font-size: 0.85rem; font-weight: 800; color: #ffffff; background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); padding: 10px 20px; border-radius: 10px; text-decoration: none; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.28); transition: transform 0.15s ease; white-space: nowrap;">
                <span>{{ $config['action_text'] }}</span>
                <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>

            <div style="display: inline-flex; align-items: center; gap: 5px; font-size: 0.73rem; color: #94a3b8;">
                <svg style="width: 12px; height: 12px; color: #0284c7;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                </svg>
                <span>Pengingat juga dikirim ke email Anda</span>
            </div>
        </div>

    </div>
</x-filament-widgets::widget>
