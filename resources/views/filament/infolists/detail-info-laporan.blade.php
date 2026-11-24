@php
    use Illuminate\Support\Facades\Storage;

    $record = $getRecord();
    $buktiUrl  = $record->bukti_dukung && Storage::disk('public')->exists($record->bukti_dukung)
        ? Storage::disk('public')->url($record->bukti_dukung) : null;
    $buktiName = $record->bukti_dukung ? basename($record->bukti_dukung) : null;

    $hasContent = $record->catatan || $record->narasi_capaian || $record->kendala || $record->tindak_lanjut || $buktiUrl;
@endphp

<div style="display: flex; flex-direction: column; gap: 14px;">
    @if($hasContent)
        {{-- Narasi Capaian --}}
        @if($record->narasi_capaian)
        <div style="padding: 14px 18px; background: #f0f9ff; border-left: 4px solid #0284c7; border-radius: 0 10px 10px 0;" class="dark:bg-blue-950/30">
            <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: #0284c7; margin-bottom: 5px;">
                📝 Narasi Capaian Kinerja:
            </div>
            <div style="font-size: 0.88rem; color: #1e3a8a; line-height: 1.6; font-weight: 500;" class="dark:text-blue-200">
                {{ $record->narasi_capaian }}
            </div>
        </div>
        @endif

        {{-- Catatan PIC --}}
        @if($record->catatan)
        <div style="padding: 14px 18px; background: #f8fafc; border-left: 4px solid #64748b; border-radius: 0 10px 10px 0;" class="dark:bg-slate-800/60">
            <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b; margin-bottom: 5px;">
                💬 Catatan Pelaksanaan PIC:
            </div>
            <div style="font-size: 0.88rem; color: #334155; line-height: 1.6;" class="dark:text-slate-300">
                {{ $record->catatan }}
            </div>
        </div>
        @endif

        {{-- Kendala & Tindak Lanjut (2 Kolom jika ada keduanya) --}}
        @if($record->kendala || $record->tindak_lanjut)
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 12px;">
            @if($record->kendala)
            <div style="padding: 14px 18px; background: #fef2f2; border-left: 4px solid #ef4444; border-radius: 0 10px 10px 0;" class="dark:bg-red-950/30">
                <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: #be123c; margin-bottom: 5px;">
                    ⚠️ Kendala yang Dihadapi:
                </div>
                <div style="font-size: 0.86rem; color: #991b1b; line-height: 1.6;" class="dark:text-red-200">
                    {{ $record->kendala }}
                </div>
            </div>
            @endif

            @if($record->tindak_lanjut)
            <div style="padding: 14px 18px; background: #f0fdf4; border-left: 4px solid #22c55e; border-radius: 0 10px 10px 0;" class="dark:bg-green-950/30">
                <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: #166534; margin-bottom: 5px;">
                    ✅ Solusi & Tindak Lanjut:
                </div>
                <div style="font-size: 0.86rem; color: #166534; line-height: 1.6;" class="dark:text-green-200">
                    {{ $record->tindak_lanjut }}
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- Bukti Pendukung --}}
        @if($buktiUrl)
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; padding: 14px 18px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px;" class="dark:bg-gray-900 dark:border-slate-800">
            <div>
                <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b; display: block;">
                    Dokumen Bukti Pendukung
                </span>
                <span style="font-size: 0.86rem; font-weight: 700; color: #0f172a;" class="dark:text-white">
                    {{ $buktiName }}
                </span>
            </div>
            <a href="{{ $buktiUrl }}" target="_blank"
               style="display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #1e40af, #0284c7); color: #ffffff; text-decoration: none; font-size: 0.84rem; font-weight: 800; padding: 8px 18px; border-radius: 8px; box-shadow: 0 2px 8px rgba(2, 132, 199, 0.3);">
                <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Unduh / Lihat File
                <svg style="width: 12px; height: 12px; opacity: 0.75;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
            </a>
        </div>
        @endif
    @else
        <div style="text-align: center; padding: 24px; color: #94a3b8; border: 1.5px dashed #cbd5e1; border-radius: 12px;">
            <div style="font-size: 1.5rem;">📄</div>
            <div style="font-weight: 700; font-size: 0.88rem; margin-top: 6px;">Tidak ada catatan narasi atau kendala khusus untuk laporan periode ini.</div>
        </div>
    @endif
</div>
