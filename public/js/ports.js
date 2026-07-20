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

    // Repopulate list group
    const listGroup = document.getElementById('ports-list-group');
    if (listGroup) {
        listGroup.innerHTML = '';

        if (ports.length === 0) {
            listGroup.innerHTML = `<div class="text-center text-muted py-4 small">Belum ada pelabuhan yang terdaftar atau cocok.</div>`;
            return;
        }

        ports.forEach(port => {
            const item = document.createElement('div');
            item.className = 'list-group-item list-group-item-custom p-3 d-flex align-items-center justify-content-between';
            
            item.innerHTML = `
                <div class="pe-2 text-truncate">
                    <span class="fw-bold text-slate-800 d-block text-truncate" style="font-size: 0.95rem;">
                        <i class="bi bi-anchor text-primary me-1"></i>${port.name}
                    </span>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle text-truncate" style="font-size: 0.72rem; font-weight: 500;">
                            ${port.country ? port.country.name : 'N/A'}
                        </span>
                        <span class="text-muted small" style="font-size: 0.75rem;">Kode: ${port.code ?? 'N/A'}</span>
                    </div>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-primary rounded-circle p-0 d-flex align-items-center justify-content-center" 
                            style="width: 32px; height: 32px; transition: all 0.2s;"
                            onclick="focusPort(${port.id}, ${port.latitude}, ${port.longitude}, '${port.name}')"
                            title="Fokus Lokasi">
                        <i class="bi bi-geo-alt"></i>
                    </button>
                </div>
            `;
            listGroup.appendChild(item);
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
