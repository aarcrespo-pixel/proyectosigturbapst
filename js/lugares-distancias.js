(function () {
    const root = document.querySelector('.distancias-lugares');
    if (!root) return;

    const dataElement = root.querySelector('[data-distancias-lugares]');
    const form = root.querySelector('[data-distancias-form]');
    const originSelect = root.querySelector('[data-distancias-origen]');
    const destinationSelect = root.querySelector('[data-distancias-destino]');
    const lineDistance = root.querySelector('[data-distancia-linea]');
    const walkingDistance = root.querySelector('[data-distancia-caminando]');
    const walkingTime = root.querySelector('[data-tiempo-caminando]');
    const status = root.querySelector('[data-distancias-aviso]');
    const source = root.querySelector('[data-distancias-fuente]');
    const mapElement = root.querySelector('[data-distancias-mapa]');

    if (!dataElement || !form || !originSelect || !destinationSelect || !lineDistance
        || !walkingDistance || !walkingTime || !status || !source || !mapElement) return;

    if (typeof window.L === 'undefined') {
        status.textContent = 'No se pudo cargar el mapa. Revisá la conexión e intentá actualizar la página.';
        return;
    }

    const places = JSON.parse(dataElement.textContent);
    const placesById = new Map(places.map((place) => [String(place.id), place]));
    const map = window.L.map(mapElement, { scrollWheelZoom: false }).setView([-31.38, -57.96], 12);
    window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap contributors</a>',
    }).addTo(map);

    let activeMarkers = [];
    let activeLine = null;
    let activeRequest = null;
    let requestNumber = 0;

    const formatDistance = (meters) => {
        if (meters < 1000) {
            return `${new Intl.NumberFormat('es-UY', { maximumFractionDigits: 0 }).format(meters)} m`;
        }
        return `${new Intl.NumberFormat('es-UY', { maximumFractionDigits: 2 }).format(meters / 1000)} km`;
    };

    const haversineDistance = (first, second) => {
        const earthRadiusMeters = 6371000;
        const radians = (degrees) => degrees * Math.PI / 180;
        const latitudeDifference = radians(second.latitude - first.latitude);
        const longitudeDifference = radians(second.longitude - first.longitude);
        const haversine = Math.min(1, Math.sin(latitudeDifference / 2) ** 2
            + Math.cos(radians(first.latitude)) * Math.cos(radians(second.latitude))
            * Math.sin(longitudeDifference / 2) ** 2);

        return 2 * earthRadiusMeters * Math.asin(Math.sqrt(haversine));
    };

    const clearMap = () => {
        activeMarkers.forEach((marker) => map.removeLayer(marker));
        activeMarkers = [];
        if (activeLine) {
            map.removeLayer(activeLine);
            activeLine = null;
        }
    };

    const showSources = (origin, destination) => {
        source.replaceChildren();
        [origin, destination].forEach((place, index) => {
            if (index > 0) source.append(document.createTextNode(' · '));
            const link = document.createElement('a');
            link.href = place.osmUrl;
            link.target = '_blank';
            link.rel = 'noopener noreferrer';
            link.textContent = `Ver ${place.name} en OpenStreetMap`;
            source.append(link);
        });
    };

    const addMarkers = (origin, destination) => {
        clearMap();
        const selected = [origin, destination];
        const points = selected.map((place) => [place.latitude, place.longitude]);

        selected.forEach((place) => {
            const marker = window.L.circleMarker([place.latitude, place.longitude], {
                radius: 9,
                color: '#713928',
                weight: 2,
                fillColor: '#f7b500',
                fillOpacity: 0.95,
            }).bindPopup(`<strong>${place.name.replace(/[&<>"']/g, (character) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
            })[character])}</strong>`);
            marker.addTo(map);
            activeMarkers.push(marker);
        });

        activeLine = window.L.polyline(points, {
            color: '#d36248',
            weight: 4,
            dashArray: '9 8',
            opacity: 0.9,
        }).addTo(map);
        map.fitBounds(window.L.latLngBounds(points).pad(0.3), { maxZoom: 15 });
        window.setTimeout(() => map.invalidateSize(), 100);
    };

    const showStraightLineOnly = (origin, destination, message, currentRequest) => {
        if (currentRequest !== requestNumber) return;
        walkingDistance.textContent = 'No disponible';
        walkingTime.textContent = message;
        status.textContent = 'Se muestra la distancia geográfica en línea recta; no se pudo confirmar una ruta peatonal.';
        addMarkers(origin, destination);
    };

    const loadWalkingRoute = async (origin, destination, currentRequest) => {
        if (activeRequest) activeRequest.abort();
        const controller = new AbortController();
        activeRequest = controller;
        const timeout = window.setTimeout(() => controller.abort(), 12000);
        const coordinates = `${origin.longitude},${origin.latitude};${destination.longitude},${destination.latitude}`;
        const routeUrl = `https://routing.openstreetmap.de/routed-foot/route/v1/driving/${coordinates}?overview=full&geometries=geojson&steps=false`;

        try {
            const response = await fetch(routeUrl, { signal: controller.signal });
            if (!response.ok) throw new Error('El servicio de rutas no respondió correctamente.');
            const result = await response.json();
            const route = result.code === 'Ok' && Array.isArray(result.routes) ? result.routes[0] : null;
            const routeCoordinates = route?.geometry?.coordinates;
            if (!route || !Number.isFinite(route.distance) || !Number.isFinite(route.duration)
                || !Array.isArray(routeCoordinates) || routeCoordinates.length < 2) {
                throw new Error('El servicio no encontró una ruta peatonal para estos lugares.');
            }
            if (currentRequest !== requestNumber) return;

            clearMap();
            [origin, destination].forEach((place) => {
                const marker = window.L.circleMarker([place.latitude, place.longitude], {
                    radius: 9,
                    color: '#713928',
                    weight: 2,
                    fillColor: '#f7b500',
                    fillOpacity: 0.95,
                }).bindPopup(`<strong>${place.name.replace(/[&<>"']/g, (character) => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;',
                })[character])}</strong>`);
                marker.addTo(map);
                activeMarkers.push(marker);
            });
            activeLine = window.L.geoJSON(route.geometry, {
                style: { color: '#d36248', weight: 5, opacity: 0.9 },
            }).addTo(map);
            map.fitBounds(activeLine.getBounds().pad(0.15), { maxZoom: 15 });
            window.setTimeout(() => map.invalidateSize(), 100);
            walkingDistance.textContent = formatDistance(route.distance);
            walkingTime.textContent = `Tiempo estimado por el servicio de rutas: ${new Intl.NumberFormat('es-UY', { maximumFractionDigits: 0 }).format(route.duration / 60)} min.`;
            status.textContent = 'La línea continua muestra la ruta peatonal disponible; la distancia recta se calcula con Haversine.';
        } catch (error) {
            if (currentRequest !== requestNumber) return;
            const errorMessage = error.name === 'AbortError'
                ? 'La consulta de ruta tardó demasiado o fue interrumpida.'
                : error.message;
            showStraightLineOnly(origin, destination, `${errorMessage} Solo se informa la distancia en línea recta.`, currentRequest);
        } finally {
            window.clearTimeout(timeout);
        }
    };

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const currentRequest = ++requestNumber;
        if (activeRequest) activeRequest.abort();
        const origin = placesById.get(originSelect.value);
        const destination = placesById.get(destinationSelect.value);

        if (!origin || !destination || origin.latitude === null || destination.latitude === null) {
            status.textContent = 'Elegí dos lugares que tengan una ubicación verificada en OpenStreetMap.';
            return;
        }
        if (origin.id === destination.id) {
            status.textContent = 'Elegí dos lugares diferentes para comparar sus distancias.';
            return;
        }

        const directDistance = haversineDistance(origin, destination);
        lineDistance.textContent = formatDistance(directDistance);
        walkingDistance.textContent = 'Consultando…';
        walkingTime.textContent = 'Esperando una respuesta del servicio de rutas peatonales.';
        status.textContent = 'Consultando la ruta caminando. Si no está disponible, se mostrará solo la distancia en línea recta.';
        showSources(origin, destination);
        addMarkers(origin, destination);
        loadWalkingRoute(origin, destination, currentRequest);
    });

    const availablePlaces = places.filter((place) => place.latitude !== null && place.longitude !== null);
    if (availablePlaces.length > 1) {
        originSelect.value = String(availablePlaces[0].id);
        destinationSelect.value = String(availablePlaces[1].id);
    }
}());
