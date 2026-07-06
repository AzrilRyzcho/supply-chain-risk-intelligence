document.addEventListener("DOMContentLoaded", function () {
    const data = window.weatherData || {};
    const countries = data.countries || [];

    // Initialize Leaflet map
    const map = L.map('weather-map', {
        minZoom: 2,
        maxBounds: [
            [-90, -180],
            [90, 180]
        ],
        maxBoundsViscosity: 1.0
    }).setView([10.0, 110.0], 3);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        noWrap: true,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    countries.forEach(c => {
        if (c.latitude && c.longitude && c.weather) {
            const marker = L.marker([c.latitude, c.longitude]).addTo(map);
            const popupContent = `
                <div style="font-family: 'Outfit', sans-serif; min-width: 150px;">
                    <h6 style="margin: 0 0 5px; font-weight: bold; color: #1e293b;">${c.name} (${c.code})</h6>
                    <hr style="margin: 5px 0;">
                    <span style="display: block; font-size: 0.85em; color: #475569;"><b>Suhu:</b> ${c.weather.temperature}°C</span>
                    <span style="display: block; font-size: 0.85em; color: #475569;"><b>Hujan:</b> ${c.weather.rain} mm</span>
                    <span style="display: block; font-size: 0.85em; color: #475569;"><b>Angin:</b> ${c.weather.wind_speed} km/h</span>
                    <span style="display: block; font-size: 0.85em; color: #ef4444;"><b>Kerawanan Badai:</b> ${c.weather.storm_risk}%</span>
                </div>
            `;
            marker.bindPopup(popupContent);
        }
    });
});
