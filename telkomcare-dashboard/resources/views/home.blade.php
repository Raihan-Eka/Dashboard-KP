@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-bold text-white mb-4">Perbandingan TTR Comply Datin per Regional</h2>
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
    
    function round(value, decimals) { return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals); }

    const greenColor = 'rgba(75, 192, 192, 0.6)';
    const redColor = 'rgba(255, 99, 132, 0.6)';

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

    // --- 1. Grafik Datin ---
    const datinData = @json($datinData);
    const datinLabels = datinData.map(item => item.name);
    
    const k1_values = datinData.map(item => round(item.summary.k1_ttr_comply, 2));
    const k2_values = datinData.map(item => round(item.summary.k2_ttr_comply, 2));
    const k3_values = datinData.map(item => round(item.summary.k3_ttr_comply, 2));

    const k1_colors = datinData.map(item => item.summary.k1_ttr_comply >= item.summary.k1_target ? greenColor : redColor);
    const k2_colors = datinData.map(item => item.summary.k2_ttr_comply >= item.summary.k2_target ? greenColor : redColor);
    const k3_colors = datinData.map(item => item.summary.k3_ttr_comply >= item.summary.k3_target ? greenColor : redColor);
    
    new Chart(document.getElementById('datinChart'), {
        type: 'bar',
        data: {
            labels: datinLabels,
            datasets: [
                { label: 'TTR Comply K1 (%)', data: k1_values, backgroundColor: k1_colors, borderColor: greenColor, borderWidth: 1 },
                { label: 'TTR Comply K2 (%)', data: k2_values, backgroundColor: k2_colors, borderColor: greenColor, borderWidth: 1 },
                { label: 'TTR Comply K3 (%)', data: k3_values, backgroundColor: k3_colors, borderColor: greenColor, borderWidth: 1 },
            ]
        },
        options: commonChartOptions
    });

    // --- 2. Grafik WiFi ---
    const wifiData = @json($wifiData);
    const wifiLabels = wifiData.map(item => item.name);
    const wifiValues = wifiData.map(item => round(item.summary.compliance_percentage, 2));
    const wifiColors = wifiData.map(item => item.summary.compliance_percentage >= item.summary.target ? greenColor : redColor);

    new Chart(document.getElementById('wifiChart'), {
        type: 'bar',
        data: {
            labels: wifiLabels,
            datasets: [{ 
                label: 'Compliance TTR Wifi (%)', 
                data: wifiValues, 
                backgroundColor: wifiColors,
                borderColor: greenColor, // Warna legenda diatur ke hijau
                borderWidth: 1
            }]
        },
        options: commonChartOptions
    });

    // --- 3. Grafik HSI ---
    const hsiData = @json($hsiData);
    const hsiLabels = hsiData.map(item => item.name);
    const hsi4HValues = hsiData.map(item => round(item.summary.h4_real, 2));
    const hsi24HValues = hsiData.map(item => round(item.summary.h24_real, 2));
    
    const hsi4HColors = hsiData.map(item => item.summary.h4_real >= item.summary.h4_target ? greenColor : redColor);
    const hsi24HColors = hsiData.map(item => item.summary.h24_real >= item.summary.h24_target ? greenColor : redColor);

    new Chart(document.getElementById('hsiChart'), {
        type: 'bar',
        data: {
            labels: hsiLabels,
            datasets: [
                { 
                    label: 'Real 4H (%)', 
                    data: hsi4HValues, 
                    backgroundColor: hsi4HColors,
                    borderColor: greenColor, // Warna legenda diatur ke hijau
                    borderWidth: 1
                },
                { 
                    label: 'Real 24H (%)', 
                    data: hsi24HValues, 
                    backgroundColor: hsi24HColors,
                    borderColor: greenColor, // Warna legenda diatur ke hijau
                    borderWidth: 1
                }
            ]
        },
        options: commonChartOptions
    });
});
</script>
@endpush