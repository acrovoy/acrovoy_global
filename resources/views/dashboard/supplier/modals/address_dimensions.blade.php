{{-- Модалка для ввода места погрузки --}}
<div
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 overflow-hidden">
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition.opacity
        class="absolute inset-0 bg-black/50 p-6"
        @click="open = false">
    </div>

    {{-- Drawer --}}
    <div
        x-show="open"
        x-transition:enter="transform transition ease-in-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in-out duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="absolute right-0 top-0 h-full w-full max-w-xl bg-white shadow-xl overflow-y-auto p-6">
        <div class="flex items-center justify-between mb-6 border-b pb-4">
            <h3 class="text-lg font-semibold">
                Enter Pickup Address
            </h3>

            <button
                type="button"
                @click="open = false"
                class="text-gray-500 hover:text-gray-700 text-xl">
                ×
            </button>
        </div>

        <form method="POST" :action="`/supplier/orders/origin/${itemId}`">
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
                            <option :value="r.id" x-text="r.name" @if ($lastAddress->origin_region_id)
                                :selected="r.id == {{$lastAddress->origin_region_id}}"
                                @endif></option>
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
                            <option :value="c.id" x-text="c.name" @if ($lastAddress->origin_city_id)
                                :selected="c.id == {{$lastAddress->origin_city_id}}"
                                @endif></option>
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