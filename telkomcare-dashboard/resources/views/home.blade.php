@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-bold text-white mb-4">Perbandingan Rata-Rata Achievement Datin per Regional</h2>
            <div style="position: relative; height:40vh; width:100%;">
                <canvas id="datinChart"></canvas>
            </div>
        </div>

        <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-bold text-white mb-4">Perbandingan Compliance TTR Wifi per Regional</h2>
            <div style="position: relative; height:40vh; width:100%;">
                <canvas id="wifiChart"></canvas>
            </div>
        </div>
        
        <div class="bg-gray-800 p-6 rounded-lg shadow-lg col-span-1 lg:col-span-2 mt-8">
            <h2 class="text-xl font-bold text-white mb-4">Perbandingan Realisasi TTR HSI per Regional</h2>
            <div style="position: relative; height:50vh; width:100%;">
                <canvas id="hsiChart"></canvas>
            </div>
        </div>

    </div>
</div>
@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Fungsi pembantu untuk pembulatan agar rapi
    function round(value, decimals) {
        return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
    }

    // Opsi umum untuk semua grafik agar tidak duplikasi
    const commonChartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: { color: 'white', callback: value => value + '%' },
                grid: { color: 'rgba(255, 255, 255, 0.2)' }
            },
            x: {
                ticks: { color: 'white' },
                grid: { display: false }
            }
        },
        plugins: {
            legend: { labels: { color: 'white' } }
        }
    };

    // --- 1. Grafik Datin (Logika tetap sama, berdasarkan Ach) ---
        const datinData = @json($datinData);
    const datinLabels = datinData.map(item => item.name);
    
    // Siapkan data dan warna untuk setiap K
    const k1_values = datinData.map(item => round(item.summary.k1_ttr_comply, 2));
    const k2_values = datinData.map(item => round(item.summary.k2_ttr_comply, 2));
    const k3_values = datinData.map(item => round(item.summary.k3_ttr_comply, 2));

    const k1_colors = datinData.map(item => item.summary.k1_ttr_comply >= item.summary.k1_target ? 'rgba(75, 192, 192, 0.6)' : 'rgba(255, 99, 132, 0.6)');
    const k2_colors = datinData.map(item => item.summary.k2_ttr_comply >= item.summary.k2_target ? 'rgba(54, 162, 235, 0.6)' : 'rgba(255, 206, 86, 0.6)');
    const k3_colors = datinData.map(item => item.summary.k3_ttr_comply >= item.summary.k3_target ? 'rgba(153, 102, 255, 0.6)' : 'rgba(255, 159, 64, 0.6)');
    
    new Chart(document.getElementById('datinChart'), {
        type: 'bar',
        data: {
            labels: datinLabels,
            datasets: [
                { label: 'TTR Comply K1 (%)', data: k1_values, backgroundColor: k1_colors },
                { label: 'TTR Comply K2 (%)', data: k2_values, backgroundColor: k2_colors },
                { label: 'TTR Comply K3 (%)', data: k3_values, backgroundColor: k3_colors },
            ]
        },
        options: commonChartOptions
    });
    // --- 2. Grafik WiFi (Logika pewarnaan diubah) ---
    const wifiData = @json($wifiData);
    const wifiLabels = wifiData.map(item => item.name);
    const wifiValues = wifiData.map(item => round(item.summary.compliance_percentage, 2));
    // LOGIKA PEWARNAAN BARU: Bandingkan compliance_percentage dengan target
    const wifiColors = wifiData.map(item => item.summary.compliance_percentage >= item.summary.target ? 'rgba(54, 162, 235, 0.6)' : 'rgba(255, 206, 86, 0.6)');

    new Chart(document.getElementById('wifiChart'), {
        type: 'bar',
        data: {
            labels: wifiLabels,
            datasets: [{ label: 'Compliance TTR Wifi (%)', data: wifiValues, backgroundColor: wifiColors }]
        },
        options: commonChartOptions
    });

    // --- 3. Grafik HSI (Logika pewarnaan diubah) ---
    const hsiData = @json($hsiData);
    const hsiLabels = hsiData.map(item => item.name);
    const hsi4HValues = hsiData.map(item => round(item.summary.h4_real, 2));
    const hsi24HValues = hsiData.map(item => round(item.summary.h24_real, 2));
    
    // LOGIKA PEWARNAAN BARU: Bandingkan h4_real dengan h4_target
    const hsi4HColors = hsiData.map(item => item.summary.h4_real >= item.summary.h4_target ? 'rgba(255, 99, 132, 0.6)' : 'rgba(255, 159, 64, 0.6)');
    // LOGIKA PEWARNAAN BARU: Bandingkan h24_real dengan h24_target
    const hsi24HColors = hsiData.map(item => item.summary.h24_real >= item.summary.h24_target ? 'rgba(75, 192, 192, 0.6)' : 'rgba(153, 102, 255, 0.6)');

    new Chart(document.getElementById('hsiChart'), {
        type: 'bar',
        data: {
            labels: hsiLabels,
            datasets: [
                { label: 'Real 4H (%)', data: hsi4HValues, backgroundColor: hsi4HColors },
                { label: 'Real 24H (%)', data: hsi24HValues, backgroundColor: hsi24HColors }
            ]
        },
        options: commonChartOptions
    });
});
</script>
@endpush