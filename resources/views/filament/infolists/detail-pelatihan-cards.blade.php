@php
    $record = $getRecord();
    $items = $record->rincianPelatihans ?? collect();
    $total = $items->sum('jumlah_peserta');
    $totalLulus = $items->sum('jumlah_lulus');
    $lulusRate = $total > 0 ? round(($totalLulus / $total) * 100, 1) : 0;
@endphp

<div style="display: flex; flex-direction: column; gap: 0;">

    {{-- Summary bar --}}
    @if($items->count() > 0)
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 10px; margin-bottom: 18px;">
        <div style="background: linear-gradient(135deg, #1e40af, #0284c7); border-radius: 12px; padding: 12px 16px; color: #fff;">
            <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; opacity: 0.85;">Total Pelatihan</div>
            <div style="font-size: 1.5rem; font-weight: 900; margin-top: 2px;">{{ $items->count() }}</div>
        </div>
        <div style="background: linear-gradient(135deg, #065f46, #059669); border-radius: 12px; padding: 12px 16px; color: #fff;">
            <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; opacity: 0.85;">Total Peserta</div>
            <div style="font-size: 1.5rem; font-weight: 900; margin-top: 2px;">{{ number_format($total, 0, ',', '.') }}</div>
        </div>
        <div style="background: linear-gradient(135deg, #7c3aed, #9b59b6); border-radius: 12px; padding: 12px 16px; color: #fff;">
            <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; opacity: 0.85;">Total Lulus</div>
            <div style="font-size: 1.5rem; font-weight: 900; margin-top: 2px;">{{ number_format($totalLulus, 0, ',', '.') }}</div>
        </div>
        <div style="background: linear-gradient(135deg, #b45309, #d97706); border-radius: 12px; padding: 12px 16px; color: #fff;">
            <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; opacity: 0.85;">Tingkat Kelulusan</div>
            <div style="font-size: 1.5rem; font-weight: 900; margin-top: 2px;">{{ $lulusRate }}%</div>
        </div>
    </div>
    @endif

    {{-- Cards daftar pelatihan --}}
    @forelse($items as $i => $item)
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px 20px; margin-bottom: 10px; transition: all 0.2s ease; position: relative; overflow: hidden;"
         class="dark:bg-gray-900 dark:border-slate-800">

        {{-- Accent bar --}}
        <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: linear-gradient(180deg, #1e40af, #0284c7); border-radius: 14px 0 0 14px;"></div>

        <div style="padding-left: 10px;">
            {{-- Header row --}}
            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 14px;">
                <div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-size: 11px; font-weight: 800; padding: 2px 10px; border-radius: 9999px;">#{{ $i + 1 }}</span>
                        <span style="font-size: 0.95rem; font-weight: 800; color: #0f172a;" class="dark:text-white">
                            {{ $item->nama_pelatihan ?? '-' }}
                        </span>
                    </div>
                    @if($item->sumber_dana)
                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 4px; display: flex; align-items: center; gap: 4px;">
                        <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Sumber Dana: <strong style="color: #334155;">{{ $item->sumber_dana }}</strong>
                    </div>
                    @endif
                </div>

                {{-- Lulus rate badge --}}
                @php $r = (int) $item->jumlah_peserta > 0 ? round(((int)$item->jumlah_lulus / (int)$item->jumlah_peserta) * 100) : 0; @endphp
                <span style="background: {{ $r >= 80 ? '#ecfdf5' : ($r >= 50 ? '#fffbeb' : '#fef2f2') }}; color: {{ $r >= 80 ? '#047857' : ($r >= 50 ? '#b45309' : '#be123c') }}; border: 1px solid {{ $r >= 80 ? '#a7f3d0' : ($r >= 50 ? '#fde68a' : '#fecaca') }}; font-size: 12px; font-weight: 800; padding: 4px 12px; border-radius: 9999px;">
                    {{ $r }}% Lulus
                </span>
            </div>

            {{-- Stats grid --}}
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px;">
                <div style="background: #f8fafc; border-radius: 10px; padding: 10px 12px; text-align: center;" class="dark:bg-slate-800">
                    <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b;">Peserta</div>
                    <div style="font-size: 1.1rem; font-weight: 900; color: #0284c7; margin-top: 2px;">{{ number_format((int)$item->jumlah_peserta, 0, ',', '.') }}</div>
                </div>
                <div style="background: #f8fafc; border-radius: 10px; padding: 10px 12px; text-align: center;" class="dark:bg-slate-800">
                    <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b;">Lulus</div>
                    <div style="font-size: 1.1rem; font-weight: 900; color: #059669; margin-top: 2px;">{{ number_format((int)$item->jumlah_lulus, 0, ',', '.') }}</div>
                </div>
                <div style="background: #f8fafc; border-radius: 10px; padding: 10px 12px; text-align: center;" class="dark:bg-slate-800">
                    <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b;">Laki-laki</div>
                    <div style="font-size: 1.1rem; font-weight: 900; color: #1e40af; margin-top: 2px;">{{ number_format((int)$item->jumlah_laki_laki, 0, ',', '.') }}</div>
                </div>
                <div style="background: #f8fafc; border-radius: 10px; padding: 10px 12px; text-align: center;" class="dark:bg-slate-800">
                    <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b;">Perempuan</div>
                    <div style="font-size: 1.1rem; font-weight: 900; color: #7c3aed; margin-top: 2px;">{{ number_format((int)$item->jumlah_perempuan, 0, ',', '.') }}</div>
                </div>
            </div>

            {{-- Lulus progress bar --}}
            @if((int)$item->jumlah_peserta > 0)
            <div style="margin-top: 12px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                    <span style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.06em;">Tingkat Kelulusan</span>
                    <span style="font-size: 10px; font-weight: 800; color: #059669;">{{ $r }}%</span>
                </div>
                <div style="height: 6px; background: #e2e8f0; border-radius: 999px; overflow: hidden;">
                    <div style="height: 100%; width: {{ $r }}%; background: linear-gradient(90deg, #059669, #10b981); border-radius: 999px; transition: width 0.6s ease;"></div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @empty
    <div style="text-align: center; padding: 32px; color: #94a3b8; border: 1.5px dashed #cbd5e1; border-radius: 14px;">
        <div style="font-size: 2rem;">📋</div>
        <div style="font-weight: 700; margin-top: 8px;">Belum ada data pelatihan</div>
    </div>
    @endforelse
</div>

