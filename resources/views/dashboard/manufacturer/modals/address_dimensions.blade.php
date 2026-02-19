{{-- Модалка для ввода места погрузки --}}
    <div x-show="open" x-transition x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

        <div class="bg-white rounded-lg p-6 w-full max-w-lg" @click.away="open = false">
            <h3 class="text-lg font-semibold mb-4">Enter Pickup Address</h3>

            <form method="POST" :action="`/manufacturer/orders/origin/${itemId}`">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Country --}}
                    <div>
                        <label class="text-sm text-gray-600">Country</label>
                        <select name="origin_country_id" x-model="origin_country_id"
                                @change="fetchRegions()"
                                class="w-full border rounded p-2">
                            <option value="">Select country</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Region --}}
                    <div>
                        <label class="text-sm text-gray-600">Region</label>
                        <select name="origin_region_id" x-model="origin_region_id"
                                @change="fetchCities()"
                                :disabled="!regions.length"
                                class="w-full border rounded p-2">
                            <option value="">Select region</option>
                            <template x-for="r in regions" :key="r.id">
                                <option :value="r.id" x-text="r.name"></option>
                            </template>
                        </select>
                    </div>

                    {{-- City --}}
                    <div>
                        <label class="text-sm text-gray-600">City</label>
                        <select name="origin_city_id" x-model="origin_city_id"
                                :disabled="!cities.length"
                                class="w-full border rounded p-2">
                            <option value="">Select city</option>
                            <template x-for="c in cities" :key="c.id">
                                <option :value="c.id" x-text="c.name"></option>
                            </template>
                        </select>
                        <small class="text-gray-500 block mt-1">
                            If your city is not listed, enter manually below
                        </small>
                        <input type="text" name="origin_city_manual" x-model="origin_city_manual"
                               @input="if(origin_city_manual) origin_city_id = null"
                               placeholder="Enter city"
                               class="w-full border rounded p-2 mt-1">
                    </div>

                    {{-- Address --}}
                    <div class="sm:col-span-2">
                        <label class="text-sm text-gray-600">Street, house, apartment</label>
                        <input type="text" name="origin_address" x-model="origin_address"
                               class="w-full border rounded p-2">
                    </div>

                    {{-- Contact --}}
                    <div>
                        <label class="text-sm text-gray-600">Contact Name</label>
                        <input type="text" name="origin_contact_name" x-model="origin_contact_name"
                               class="w-full border rounded p-2">
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Contact Phone</label>
                        <input type="text" name="origin_contact_phone" x-model="origin_contact_phone"
                               class="w-full border rounded p-2">
                    </div>

                    {{-- Weight & Dimensions --}}
                    <div>
                        <label class="text-sm text-gray-600">Weight (kg)</label>
                        <input type="number" name="weight" x-model="weight" step="0.01" min="0"
                               class="w-full border rounded p-2">
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Length (cm)</label>
                        <input type="number" name="length" x-model="length" step="0.01" min="0"
                               class="w-full border rounded p-2">
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Width (cm)</label>
                        <input type="number" name="width" x-model="width" step="0.01" min="0"
                               class="w-full border rounded p-2">
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Height (cm)</label>
                        <input type="number" name="height" x-model="height" step="0.01" min="0"
                               class="w-full border rounded p-2">
                    </div>

                </div>

                <div class="text-right mt-4">
                    <button type="submit"
                            class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>