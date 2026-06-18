<div class="flex flex-col gap-3 relative">

    {{-- =========================
        1. SEARCH EXISTING
    ========================== --}}
    <input type="text"
           id="lp_search"
           placeholder="Search existing location..."
           class="w-full border rounded-lg px-3 py-2">

    <div id="lp_search_results"
         class="absolute top-12 left-0 right-0 bg-white border rounded-lg shadow hidden max-h-48 overflow-y-auto z-50"></div>

    <input type="hidden"
           name="location_id"
           id="lp_location_id">


    {{-- =========================
        DIVIDER
    ========================== --}}
    <div class="text-xs text-gray-400 mt-2">
        Or create new location:
    </div>


    {{-- =========================
        2. CREATE NEW LOCATION
    ========================== --}}

    {{-- COUNTRY --}}
    <select id="lp_country" name="country"
            class="w-full border rounded-lg px-3 py-2">
        <option value="">Select country</option>
        @foreach($countries as $country)
            <option value="{{ $country->id }}">
                {{ $country->name }}
            </option>
        @endforeach
    </select>

    {{-- REGION --}}
    <select id="lp_region" name="region"
            class="w-full border rounded-lg px-3 py-2"
            disabled>
        <option value="">Select region</option>
    </select>

    {{-- CITY (manual input only) --}}
    <input type="text"
           id="lp_city_manual"
           name="city_manual"
           placeholder="Type city name"
           class="w-full border rounded-lg px-3 py-2">

</div>

<script>
const searchInput = document.getElementById('lp_search');
const searchBox   = document.getElementById('lp_search_results');
const locationId  = document.getElementById('lp_location_id');

const country = document.getElementById('lp_country');
const region  = document.getElementById('lp_region');
const manual  = document.getElementById('lp_city_manual');
const regionsUrl = @json(route('buyer.locations.regions'));

let t = null;

/**
 * =========================
 * 1. SEARCH EXISTING
 * =========================
 */
searchInput?.addEventListener('input', function () {

    clearTimeout(t);

    if (this.value.length < 3) {
        searchBox.classList.add('hidden');
        return;
    }

    t = setTimeout(() => {

        fetch(`/locations/search?q=${this.value}`)
            .then(r => r.json())
            .then(data => {

                searchBox.innerHTML = '';

                data.forEach(loc => {

                    const div = document.createElement('div');
                    div.className = "p-2 text-sm hover:bg-gray-100 cursor-pointer";

                    div.innerHTML =
                        `${loc.name} / ${loc.region_name ?? ''} / ${loc.country_name ?? ''}`;

                    div.onclick = () => {
                        locationId.value = loc.id;
                        searchInput.value = loc.name;

                        // clear create fields
                        country.value = '';
                        region.innerHTML = `<option value="">Select region</option>`;
                        region.disabled = true;
                        manual.value = '';

                        searchBox.classList.add('hidden');
                    };

                    searchBox.appendChild(div);
                });

                searchBox.classList.remove('hidden');
            });

    }, 300);
});


/**
 * =========================
 * 2. CREATE FLOW
 * =========================
 */

// country → load regions
country?.addEventListener('change', function () {

    locationId.value = '';
    region.innerHTML = `<option value="">Select region</option>`;
    manual.value = '';

    if (!this.value) {
        region.disabled = true;
        return;
    }

    fetch(`${regionsUrl}?country_id=${this.value}`)
        .then(r => r.json())
        .then(data => {

            data.forEach(r => {
                const opt = document.createElement('option');
                opt.value = r.id;
                opt.textContent = r.name;
                region.appendChild(opt);
            });

            region.disabled = false;
        });
});

region?.addEventListener('change', function () {
    locationId.value = '';
});

/**
 * manual typing = create mode
 */
manual?.addEventListener('input', function () {

    if (this.value.trim() !== '') {
        locationId.value = '';
        searchInput.value = '';
    }
});
</script>