@php
    $record = $getRecord();
    $items = $record->angkaKinerjas()->with('ikkKomponen')->get();

    $totalBobot = $items->sum(fn($it) => (float) $it->ikkKomponen?->bobot ?? 0);
    $totalCapaian = 0;
    foreach ($items as $it) {
        if ((float) $it->penyebut > 0 && $it->ikkKomponen) {
            $totalCapaian += ((float) $it->pembilang / (float) $it->penyebut) * 100 * ((float) $it->ikkKomponen->bobot / 100);
        }
    }
@endphp

<div style="display: flex; flex-direction: column; gap: 0;">

    {{-- Summary total capaian --}}
    @if($items->count() > 0)
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 10px; margin-bottom: 18px;">
        <div style="background: linear-gradient(135deg, #1e40af, #0284c7); border-radius: 12px; padding: 12px 16px; color: #fff;">
            <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; opacity: 0.85;">Komponen</div>
            <div style="font-size: 1.5rem; font-weight: 900; margin-top: 2px;">{{ $items->count() }}</div>
        </div>
        <div style="background: linear-gradient(135deg, #065f46, #059669); border-radius: 12px; padding: 12px 16px; color: #fff;">
            <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; opacity: 0.85;">Capaian Gabungan</div>
            <div style="font-size: 1.5rem; font-weight: 900; margin-top: 2px;">{{ number_format($totalCapaian, 2, ',', '.') }}%</div>
        </div>
        <div style="background: linear-gradient(135deg, #7c3aed, #9b59b6); border-radius: 12px; padding: 12px 16px; color: #fff;">
            <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; opacity: 0.85;">Total Bobot</div>
            <div style="font-size: 1.5rem; font-weight: 900; margin-top: 2px;">{{ number_format($totalBobot, 0, ',', '.') }}%</div>
        </div>
    </div>
    @endif

    {{-- Cards komponen --}}
    @forelse($items as $i => $item)
    @php
        $capaian = (float) $item->penyebut > 0
            ? ((float) $item->pembilang / (float) $item->penyebut) * 100
            : 0;
        $capaianFormatted = number_format($capaian, 2, ',', '.');
        $bobot = (float) ($item->ikkKomponen?->bobot ?? 0);
        $kontribusi = $capaian * ($bobot / 100);
        $barWidth = min(100, $capaian);
        $barColor = $capaian >= 100 ? 'linear-gradient(90deg,#059669,#10b981)' : ($capaian >= 70 ? 'linear-gradient(90deg,#d97706,#f59e0b)' : 'linear-gradient(90deg,#be123c,#f43f5e)');
    @endphp
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px 20px; margin-bottom: 10px; position: relative; overflow: hidden;"
         class="dark:bg-gray-900 dark:border-slate-800">

        {{-- Accent bar --}}
        <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: {{ $capaian >= 100 ? 'linear-gradient(180deg,#059669,#10b981)' : ($capaian >= 70 ? 'linear-gradient(180deg,#d97706,#f59e0b)' : 'linear-gradient(180deg,#be123c,#f43f5e)') }}; border-radius: 14px 0 0 14px;"></div>

        <div style="padding-left: 10px;">
            {{-- Header --}}
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 10px; margin-bottom: 14px;">
                <div>
                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <span style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-size: 11px; font-weight: 800; padding: 2px 10px; border-radius: 9999px;">Komponen {{ $i + 1 }}</span>
                        <span style="font-size: 0.95rem; font-weight: 800; color: #0f172a;" class="dark:text-white">
                            {{ $item->ikkKomponen?->nama_komponen ?? "Komponen {$item->id}" }}
                        </span>
                    </div>
                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 4px;">
                        Bobot: <strong style="color: #1e40af;">{{ $bobot }}%</strong>
                        &nbsp;•&nbsp;
                        Kontribusi Capaian: <strong style="color: #0284c7;">{{ number_format($kontribusi, 2, ',', '.') }}%</strong>
                    </div>
                </div>

                <span style="background: {{ $capaian >= 100 ? '#ecfdf5' : ($capaian >= 70 ? '#fffbeb' : '#fef2f2') }}; color: {{ $capaian >= 100 ? '#047857' : ($capaian >= 70 ? '#b45309' : '#be123c') }}; border: 1px solid {{ $capaian >= 100 ? '#a7f3d0' : ($capaian >= 70 ? '#fde68a' : '#fecaca') }}; font-size: 14px; font-weight: 900; padding: 6px 14px; border-radius: 9999px;">
                    {{ $capaianFormatted }}%
                </span>
            </div>

            {{-- Realisasi vs Target --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 12px;">
                <div style="background: #f8fafc; border-radius: 10px; padding: 10px 14px;" class="dark:bg-slate-800">
                    <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b;">Realisasi (Pembilang)</div>
                    <div style="font-size: 1.1rem; font-weight: 900; color: #0284c7; margin-top: 2px;">{{ number_format((float)$item->pembilang, 2, ',', '.') }}</div>
                </div>
                <div style="background: #f8fafc; border-radius: 10px; padding: 10px 14px;" class="dark:bg-slate-800">
                    <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b;">Target (Penyebut)</div>
                    <div style="font-size: 1.1rem; font-weight: 900; color: #64748b; margin-top: 2px;">{{ number_format((float)$item->penyebut, 2, ',', '.') }}</div>
                </div>
            </div>

            {{-- Progress bar capaian --}}
            <div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                    <span style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.06em;">Capaian Komponen</span>
                    <span style="font-size: 10px; font-weight: 800; color: {{ $capaian >= 100 ? '#059669' : ($capaian >= 70 ? '#d97706' : '#be123c') }};">{{ $capaianFormatted }}% / 100%</span>
                </div>
                <div style="height: 8px; background: #e2e8f0; border-radius: 999px; overflow: hidden;">
                    <div style="height: 100%; width: {{ $barWidth }}%; background: {{ $barColor }}; border-radius: 999px; transition: width 0.6s ease;"></div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div style="text-align: center; padding: 32px; color: #94a3b8; border: 1.5px dashed #cbd5e1; border-radius: 14px;">
        <div style="font-size: 2rem;">📊</div>
        <div style="font-weight: 700; margin-top: 8px;">Belum ada data komponen</div>
    </div>
    @endforelse
</div>

