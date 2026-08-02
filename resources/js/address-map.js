export function initCompanyAddressMap() {

    const container = document.getElementById('company-address-map');

    if (!container) {
        return;
    }

    const lat = parseFloat(container.dataset.lat);
    const lng = parseFloat(container.dataset.lng);

    console.log('MAP DATA:', lat, lng);

    if (!lat || !lng) {
        console.log('No coordinates');
        return;
    }

    if (typeof L === 'undefined') {
        console.error('Leaflet is not loaded');
        return;
    }


    const map = L.map(container).setView(
        [lat, lng],
        15
    );


    L.tileLayer(
        'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            attribution: '&copy; OpenStreetMap contributors'
        }
    ).addTo(map);


    L.marker([lat, lng])
        .addTo(map);


    setTimeout(() => {
        map.invalidateSize();
    }, 300);

}