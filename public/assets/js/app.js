const searchForm = document.querySelector('[data-search-form]');

if (searchForm) {
    searchForm.addEventListener('submit', (event) => {
        const departure = searchForm.querySelector('[name="depart"]');
        const arrival = searchForm.querySelector('[name="arrivee"]');
        const date = searchForm.querySelector('[name="date"]');

        if (!departure.value.trim() || !arrival.value.trim() || !date.value) {
            event.preventDefault();
            window.alert('Veuillez renseigner une ville de depart, une ville d arrivee et une date.');
        }
    });
}

document.querySelectorAll('canvas[data-chart]').forEach((canvas) => {
    const data = JSON.parse(canvas.dataset.chart || '{}');
    const labels = Object.keys(data);
    const values = Object.values(data).map(Number);
    const ctx = canvas.getContext('2d');
    const width = canvas.width = canvas.clientWidth || 420;
    const height = canvas.height = 220;
    const max = Math.max(...values, 1);
    const barWidth = labels.length ? (width - 48) / labels.length : width - 48;

    ctx.clearRect(0, 0, width, height);
    ctx.fillStyle = '#f4f7f5';
    ctx.fillRect(0, 0, width, height);
    ctx.fillStyle = '#1f2933';
    ctx.font = '12px Arial';

    values.forEach((value, index) => {
        const barHeight = Math.round((height - 58) * (value / max));
        const x = 24 + index * barWidth;
        const y = height - 34 - barHeight;

        ctx.fillStyle = '#1f7a4d';
        ctx.fillRect(x, y, Math.max(18, barWidth - 12), barHeight);
        ctx.fillStyle = '#1f2933';
        ctx.fillText(String(value), x, y - 6);
        ctx.fillText(labels[index].slice(5), x, height - 12);
    });
});
