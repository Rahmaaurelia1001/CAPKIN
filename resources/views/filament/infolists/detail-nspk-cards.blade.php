@php
    $record = $getRecord();
    $items = $record->rincianNSPKs ?? collect();

    $jenisColors = [
        'Peraturan'    => ['bg' => '#eff6ff', 'text' => '#1e40af', 'border' => '#bfdbfe'],
        'Pedoman'      => ['bg' => '#f0fdf4', 'text' => '#166534', 'border' => '#bbf7d0'],
        'Standar'      => ['bg' => '#fffbeb', 'text' => '#92400e', 'border' => '#fde68a'],
        'Juklak'       => ['bg' => '#fdf4ff', 'text' => '#6b21a8', 'border' => '#e9d5ff'],
        'Juknis'       => ['bg' => '#fff1f2', 'text' => '#be123c', 'border' => '#fecdd3'],
        'SOP'          => ['bg' => '#f0f9ff', 'text' => '#0369a1', 'border' => '#bae6fd'],
        'default'      => ['bg' => '#f8fafc', 'text' => '#475569', 'border' => '#e2e8f0'],
    ];
@endphp

<div style="display: flex; flex-direction: column; gap: 0;">

    {{-- Summary --}}
    @if($items->count() > 0)
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 10px; margin-bottom: 18px;">
        <div style="background: linear-gradient(135deg, #1e40af, #0284c7); border-radius: 12px; padding: 12px 16px; color: #fff;">
            <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; opacity: 0.85;">Total Dokumen NSPK</div>
            <div style="font-size: 1.5rem; font-weight: 900; margin-top: 2px;">{{ $items->count() }}</div>
        </div>
        @php $byJenis = $items->groupBy('jenis_nspk'); @endphp
        @foreach($byJenis as $jenis => $group)
        @php $c = $jenisColors[$jenis] ?? $jenisColors['default']; @endphp
        <div style="background: {{ $c['bg'] }}; border: 1px solid {{ $c['border'] }}; border-radius: 12px; padding: 12px 16px;">
            <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: {{ $c['text'] }}; opacity: 0.85;">{{ $jenis }}</div>
            <div style="font-size: 1.5rem; font-weight: 900; margin-top: 2px; color: {{ $c['text'] }};">{{ $group->count() }}</div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Cards daftar NSPK --}}
    @forelse($items as $i => $item)
    @php $c = $jenisColors[$item->jenis_nspk] ?? $jenisColors['default']; @endphp
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 16px 20px; margin-bottom: 10px; position: relative; overflow: hidden;"
         class="dark:bg-gray-900 dark:border-slate-800">

        {{-- Accent bar warna sesuai jenis --}}
        <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: {{ $c['text'] }}; border-radius: 14px 0 0 14px; opacity: 0.7;"></div>

        <div style="padding-left: 10px; display: flex; align-items: flex-start; gap: 14px; flex-wrap: wrap;">

            {{-- Nomor urut --}}
            <div style="width: 32px; height: 32px; border-radius: 999px; background: {{ $c['bg'] }}; border: 1.5px solid {{ $c['border'] }}; display: flex; align-items: center; justify-content: center; font-size: 0.78rem; font-weight: 800; color: {{ $c['text'] }}; flex-shrink: 0;">
                {{ $i + 1 }}
            </div>

            <div style="flex: 1; min-width: 0;">
                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 6px;">
                    {{-- Badge Jenis --}}
                    <span style="background: {{ $c['bg'] }}; color: {{ $c['text'] }}; border: 1px solid {{ $c['border'] }}; font-size: 11px; font-weight: 800; padding: 2px 10px; border-radius: 9999px; white-space: nowrap;">
                        {{ $item->jenis_nspk ?? 'Lainnya' }}
                    </span>
                    {{-- Nama NSPK --}}
                    <span style="font-size: 0.9rem; font-weight: 800; color: #0f172a; line-height: 1.4;" class="dark:text-white">
                        {{ $item->nama_nspk ?? '-' }}
                    </span>
                </div>

                @if($item->keterangan)
                <div style="font-size: 0.8rem; color: #64748b; line-height: 1.5; background: #f8fafc; border-radius: 8px; padding: 8px 12px; border-left: 3px solid {{ $c['text'] }}; opacity: 0.8;" class="dark:bg-slate-800 dark:text-slate-300">
                    <span style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: {{ $c['text'] }}; display: block; margin-bottom: 2px; opacity: 1;">Keterangan</span>
                    {{ $item->keterangan }}
                </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div style="text-align: center; padding: 32px; color: #94a3b8; border: 1.5px dashed #cbd5e1; border-radius: 14px;">
        <div style="font-size: 2rem;">📄</div>
        <div style="font-weight: 700; margin-top: 8px;">Belum ada data NSPK</div>
    </div>
    @endforelse
</div>

