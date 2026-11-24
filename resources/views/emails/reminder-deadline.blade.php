<x-mail::message>
# 📋 Pengingat Deadline SIMONIKA

Halo, **{{ $namaUser }}**!

Ini adalah pengingat otomatis dari **SIMONIKA** (Sistem Informasi Monitoring Kinerja dan Anggaran PPSDMAP).

---

## ⏰ Deadline Mendekati — {{ $bulanTahun }}

| Keterangan | Detail |
|---|---|
| **Role Anda** | {{ $role }} |
| **Tugas** | {{ $aktivitas }} |
| **Batas Waktu** | {{ $deadline }} |
| **Sisa Waktu** | ⚠️ **{{ $hariTersisa }} hari lagi** |

---

@if($hariTersisa <= 1)
> 🔴 **MENDESAK!** Deadline tinggal {{ $hariTersisa }} hari. Segera selesaikan tugas Anda.
@elseif($hariTersisa <= 3)
> 🟡 **Segera** selesaikan tugas Anda sebelum deadline.
@else
> 🟢 Pastikan tugas Anda selesai sebelum deadline.
@endif

<x-mail::button :url="$urlSistem" color="primary">
Buka SIMONIKA Sekarang
</x-mail::button>

---

*Email ini dikirim otomatis oleh sistem SIMONIKA. Jangan balas email ini.*

Salam,  
**Tim Umum dan Perencanaan PPSDMAP**  
SIMONIKA
</x-mail::message>
