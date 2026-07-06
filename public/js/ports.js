let map;
let markerClusterGroup;
let allPorts = [];
let activeMarkers = {};

document.addEventListener("DOMContentLoaded", function () {
    const data = window.portsData || {};

    // Initialize Leaflet map
    map = L.map('ports-map', {
        minZoom: 2,
        maxBounds: [
            [-90, -180],
            [90, 180]
        ],
        maxBoundsViscosity: 1.0
    }).setView([10.0, 110.0], 2);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        noWrap: true,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Initialize Marker Cluster Group
    markerClusterGroup = L.markerClusterGroup();
    map.addLayer(markerClusterGroup);

    // Load ports data from global variable
    allPorts = data.ports || [];

    // Render initially
    filterPorts();
});

function renderPortsOnMapAndTable(ports) {
    // Update active count badge
    const badge = document.getElementById('active-count');
    if (badge) {
        badge.innerText = `${ports.length} Pelabuhan`;
    }

    // Clear existing markers from map
    if (markerClusterGroup) {
        markerClusterGroup.clearLayers();
    }
    activeMarkers = {};

    const bounds = [];

    // Repopulate map markers
    ports.forEach(port => {
        if (port.latitude && port.longitude) {
            const lat = parseFloat(port.latitude);
            const lon = parseFloat(port.longitude);

            const marker = L.marker([lat, lon]);
            const popupContent = `
                <div style="font-family: 'Outfit', sans-serif; min-width: 160px; line-height: 1.4;">
                    <h6 style="margin: 0 0 5px; font-weight: bold; color: #1e293b; font-size: 1.05rem;">
                        <i class="bi bi-anchor text-primary me-1"></i>${port.name}
                    </h6>
                    <hr style="margin: 4px 0; border-color: #cbd5e1;">
                    <span style="display: block; font-size: 0.85em; color: #475569;"><b>Kode:</b> ${port.code ?? 'N/A'}</span>
                    <span style="display: block; font-size: 0.85em; color: #475569;"><b>Negara:</b> ${port.country ? port.country.name : 'N/A'}</span>
                    <span style="display: block; font-size: 0.85em; color: #475569;"><b>Koordinat:</b> ${lat}, ${lon}</span>
                </div>
            `;
            marker.bindPopup(popupContent);
            markerClusterGroup.addLayer(marker);

            // Store reference
            activeMarkers[port.id] = marker;
            bounds.push([lat, lon]);
        }
    });

    // Fit map bounds to show all active markers nicely
    if (bounds.length > 0 && map) {
        map.fitBounds(bounds, { padding: [40, 40] });
    }

    // Repopulate sidebar table
    const tbody = document.querySelector('table tbody');
    if (tbody) {
        tbody.innerHTML = '';

        if (ports.length === 0) {
            tbody.innerHTML = `<tr><td colspan="3" class="text-center text-muted py-4">Belum ada pelabuhan yang terdaftar atau cocok.</td></tr>`;
            return;
        }

        ports.forEach(port => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <span class="fw-bold text-slate-855 d-block">${port.name}</span>
                    <span class="text-muted small">Kode: ${port.code ?? 'N/A'}</span>
                </td>
                <td>
                    <span class="badge bg-secondary">${port.country ? port.country.name : 'N/A'}</span>
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-info fw-bold" 
                            onclick="focusPort(${port.id}, ${port.latitude}, ${port.longitude}, '${port.name}')"
                            title="Fokus Lokasi">
                        <i class="bi bi-geo-alt"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }
}

function filterPorts() {
    const searchText = document.getElementById('search-input').value.toLowerCase();
    const countryId = document.getElementById('country-filter').value;

    const filteredPorts = allPorts.filter(port => {
        const matchesSearch = port.name.toLowerCase().includes(searchText) || 
                              (port.code && port.code.toLowerCase().includes(searchText));
        const matchesCountry = !countryId || (port.country_id && port.country_id.toString() === countryId);

        return matchesSearch && matchesCountry;
    });

    renderPortsOnMapAndTable(filteredPorts);
}

// Global functions exposed to window
window.focusPort = function (portId, lat, lon, name) {
    if (map && activeMarkers[portId]) {
        const marker = activeMarkers[portId];
        
        // Zoom and show marker (handling cluster zoom-to)
        if (markerClusterGroup) {
            markerClusterGroup.zoomToShowLayer(marker, function() {
                marker.openPopup();
            });
        } else {
            map.setView([lat, lon], 14);
            marker.openPopup();
        }
    }
};

window.filterPorts = filterPorts;
