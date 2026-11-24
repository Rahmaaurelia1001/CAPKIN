@php
    $record = $getRecord();
    $komponens = $record->angkaKinerjas()->with('ikkKomponen')->get();
    $kerjasamas = $record->rincianKerjasamas ?? collect();

    $totalCapaian = 0;
    foreach ($komponens as $it) {
        if ((float) $it->penyebut > 0 && $it->ikkKomponen) {
            $totalCapaian += ((float) $it->pembilang / (float) $it->penyebut) * 100 * ((float) $it->ikkKomponen->bobot / 100);
        }
    }
@endphp

<div style="display: flex; flex-direction: column; gap: 20px;">

    {{-- ── BAGIAN 1: Komponen Berbobot ── --}}
    <div>
        <div style="font-size: 0.8rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
            <div style="width: 3px; height: 16px; background: linear-gradient(180deg,#1e40af,#0284c7); border-radius: 999px;"></div>
            Komponen Berbobot ({{ $komponens->count() }} Komponen)
            @if($komponens->count() > 0)
            &nbsp;—&nbsp;
            <span style="color: #0284c7; font-weight: 900;">Capaian Gabungan: {{ number_format($totalCapaian, 2, ',', '.') }}%</span>
            @endif
        </div>

        @forelse($komponens as $i => $item)
        @php
            $capaian = (float) $item->penyebut > 0
                ? ((float) $item->pembilang / (float) $item->penyebut) * 100
                : 0;
            $bobot = (float) ($item->ikkKomponen?->bobot ?? 0);
            $kontribusi = $capaian * ($bobot / 100);
            $barWidth = min(100, $capaian);
        @endphp
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 16px 20px; margin-bottom: 10px; position: relative; overflow: hidden;"
             class="dark:bg-gray-900 dark:border-slate-800">
            <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: {{ $capaian >= 100 ? 'linear-gradient(180deg,#059669,#10b981)' : ($capaian >= 70 ? 'linear-gradient(180deg,#d97706,#f59e0b)' : 'linear-gradient(180deg,#be123c,#f43f5e)') }}; border-radius: 14px 0 0 14px;"></div>

            <div style="padding-left: 10px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 8px; margin-bottom: 10px;">
                    <div>
                        <div style="font-size: 0.9rem; font-weight: 800; color: #0f172a;" class="dark:text-white">
                            {{ $item->ikkKomponen?->nama_komponen ?? "Komponen {$item->id}" }}
                        </div>
                        <div style="font-size: 0.72rem; color: #64748b; margin-top: 3px;">
                            Bobot: <strong style="color:#1e40af">{{ $bobot }}%</strong>
                            &nbsp;•&nbsp; Kontribusi: <strong style="color:#0284c7">{{ number_format($kontribusi,2,',','.') }}%</strong>
                        </div>
                    </div>
                    <span style="background: {{ $capaian>=100 ? '#ecfdf5' : ($capaian>=70 ? '#fffbeb' : '#fef2f2') }}; color: {{ $capaian>=100 ? '#047857' : ($capaian>=70 ? '#b45309' : '#be123c') }}; border: 1px solid {{ $capaian>=100 ? '#a7f3d0' : ($capaian>=70 ? '#fde68a' : '#fecaca') }}; font-size: 13px; font-weight: 900; padding: 4px 12px; border-radius: 9999px;">
                        {{ number_format($capaian,2,',','.') }}%
                    </span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 10px;">
                    <div style="background:#f8fafc; border-radius:10px; padding:8px 12px;" class="dark:bg-slate-800">
                        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#64748b;">Realisasi</div>
                        <div style="font-size:1rem;font-weight:900;color:#0284c7;margin-top:2px;">{{ number_format((float)$item->pembilang,2,',','.') }}</div>
                    </div>
                    <div style="background:#f8fafc; border-radius:10px; padding:8px 12px;" class="dark:bg-slate-800">
                        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#64748b;">Target</div>
                        <div style="font-size:1rem;font-weight:900;color:#64748b;margin-top:2px;">{{ number_format((float)$item->penyebut,2,',','.') }}</div>
                    </div>
                </div>

                <div style="height:6px;background:#e2e8f0;border-radius:999px;overflow:hidden;">
                    <div style="height:100%;width:{{ $barWidth }}%;background:{{ $capaian>=100?'linear-gradient(90deg,#059669,#10b981)':($capaian>=70?'linear-gradient(90deg,#d97706,#f59e0b)':'linear-gradient(90deg,#be123c,#f43f5e)') }};border-radius:999px;"></div>
                </div>
            </div>
        </div>
        @empty
        <div style="text-align: center; padding: 24px; color: #94a3b8; border: 1.5px dashed #cbd5e1; border-radius: 14px; margin-bottom: 10px;">
            Belum ada data komponen
        </div>
        @endforelse
    </div>

    {{-- ── BAGIAN 2: Daftar Kerja Sama ── --}}
    <div>
        <div style="font-size: 0.8rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
            <div style="width: 3px; height: 16px; background: linear-gradient(180deg,#7c3aed,#9b59b6); border-radius: 999px;"></div>
            Daftar Judul Kerja Sama ({{ $kerjasamas->count() }} Kerja Sama)
        </div>

        @forelse($kerjasamas as $i => $ks)
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 18px; margin-bottom: 8px; display: flex; align-items: center; gap: 12px; position: relative; overflow: hidden;"
             class="dark:bg-gray-900 dark:border-slate-800">
            <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: linear-gradient(180deg,#7c3aed,#9b59b6); border-radius: 12px 0 0 12px;"></div>
            <div style="width: 28px; height: 28px; border-radius: 999px; background: #fdf4ff; border: 1.5px solid #e9d5ff; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800; color: #7c3aed; flex-shrink: 0; margin-left: 8px;">
                {{ $i + 1 }}
            </div>
            <div style="font-size: 0.88rem; font-weight: 700; color: #0f172a; line-height: 1.4;" class="dark:text-white">
                {{ $ks->judul_kerjasama ?? '-' }}
            </div>
        </div>
        @empty
        <div style="text-align: center; padding: 24px; color: #94a3b8; border: 1.5px dashed #cbd5e1; border-radius: 14px;">
            <div style="font-size: 2rem;">🤝</div>
            <div style="font-weight: 700; margin-top: 8px;">Belum ada data kerja sama</div>
        </div>
        @endforelse
    </div>
</div>

