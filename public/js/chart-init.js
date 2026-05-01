const canvas = document.getElementById('chartPeminjaman');
const labels = JSON.parse(canvas.dataset.labels);
const values = JSON.parse(canvas.dataset.values);

new Chart(canvas, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Peminjaman',
            data: values,
            borderColor: '#f97316',
            backgroundColor: 'rgba(249,115,22,0.15)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        plugins: {
            legend: { labels: { color: '#374151' } }
        },
        scales: {
            x: { ticks: { color: '#6b7280' }, grid: { color: '#e5e7eb' } },
            y: { ticks: { color: '#6b7280' }, grid: { color: '#e5e7eb' } }
        }
    }
});