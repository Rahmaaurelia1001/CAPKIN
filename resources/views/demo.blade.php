@extends('filament::page')

@section('content')
<div style="padding: 24px; max-width: 800px; margin: auto;">
    <h1 style="font-size: 2rem; font-weight: 700; text-align: center; margin-bottom: 24px;">
        Demo Dashboard
    </h1>
    <p style="text-align: center; color: #4b5563; margin-bottom: 16px;">
        Menampilkan data Anggaran (Pagu vs Realisasi) untuk tahun {{ $tahun }}
    </p>
    <canvas id="demoChart" width="400" height="400" style="display: block; margin: 0 auto;"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('demoChart').getContext('2d');
        const data = {
            labels: ['Realisasi', 'Sisa Anggaran'],
            datasets: [{
                data: [{{ $totalRealisasi }}, {{ $sisa }}],
                backgroundColor: ['#9b59b6', '#3498db'],
                hoverBackgroundColor: ['#8e44ad', '#2563eb'],
                borderWidth: 3,
                borderColor: '#ffffff',
            }]
        };
        new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '35%',
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: { enabled: true }
                }
            }
        });
    });
</script>
@endsection
