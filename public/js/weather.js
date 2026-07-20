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
    }).setView([15.0, 20.0], 2.5); // Centered nicely for a global overview

    // Use standard OpenStreetMap tiles for natural look (like GMaps)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        noWrap: true,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Initialize Marker Cluster Group for clean grouping of the 250 countries
    const markers = L.markerClusterGroup({
        showCoverageOnHover: false,
        spiderfyOnMaxZoom: true,
        maxClusterRadius: 40,
        disableClusteringAtZoom: 16
    });

    // Helper to get custom colored marker based on storm risk
    function getMarkerIcon(stormRisk, countryName) {
        let color = '#10b981'; // Green (Aman)
        let pulse = '';
        if (stormRisk >= 15) {
            color = '#ef4444'; // Red (High Risk)
            pulse = 'pulse-red';
        } else if (stormRisk >= 8) {
            color = '#f59e0b'; // Orange (Medium Risk)
            pulse = 'pulse-orange';
        }

        return L.divIcon({
            className: 'custom-weather-marker',
            html: `<div class="marker-pin ${pulse}" style="background-color: ${color};" title="${countryName}: Storm Risk ${stormRisk}%"></div>`,
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });
    }

    // Populate markers
    countries.forEach(c => {
        if (c.latitude && c.longitude && c.weather) {
            const marker = L.marker([c.latitude, c.longitude], {
                icon: getMarkerIcon(c.weather.storm_risk, c.name)
            });

            const popupContent = `
                <div style="font-family: 'Outfit', sans-serif; min-width: 160px; padding: 4px;">
                    <div style="display: flex; align-items: center; margin-bottom: 8px;">
                        ${c.flag ? `<img src="${c.flag}" style="width: 18px; height: 11px; object-fit: cover; border-radius: 1px; margin-right: 6px; border: 1px solid #cbd5e1;" />` : ''}
                        <h6 style="margin: 0; font-weight: 700; color: #1e293b; font-size: 0.95em;">${c.name}</h6>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 4px; border-top: 1px solid #f1f5f9; padding-top: 6px;">
                        <span style="font-size: 0.85em; color: #475569;"><i class="bi bi-thermometer-half me-1"></i><b>Suhu:</b> ${c.weather.temperature}°C</span>
                        <span style="font-size: 0.85em; color: #475569;"><i class="bi bi-cloud-drizzle me-1"></i><b>Hujan:</b> ${c.weather.rain} mm</span>
                        <span style="font-size: 0.85em; color: #475569;"><i class="bi bi-wind me-1"></i><b>Angin:</b> ${c.weather.wind_speed} km/h</span>
                        <span style="font-size: 0.85em; color: ${c.weather.storm_risk >= 8 ? '#ef4444' : '#10b981'}; font-weight: 600;">
                            <i class="bi bi-exclamation-triangle me-1"></i><b>Risiko:</b> ${c.weather.storm_risk}%
                        </span>
                    </div>
                </div>
            `;

            marker.bindPopup(popupContent);
            markers.addLayer(marker);
        }
    });

    // Add cluster group to map
    map.addLayer(markers);
});
