<div class="bg-white border rounded-lg">

    <div class="border-b px-5 py-3 font-semibold">
        User Addresses
    </div>


    <div class="p-5">

        @if($user->addresses->count())

            <div class="space-y-4">

            @foreach($user->addresses as $address)

                <div class="border rounded-lg p-4
                    {{ $address->is_default ? 'border-green-300 bg-green-50' : 'border-gray-200' }}">

                    <div class="flex justify-between items-start mb-3">

                        <div class="font-semibold">
                            {{ $address->first_name }}
                            {{ $address->last_name }}
                        </div>


                        @if($address->is_default)

                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                Default
                            </span>

                        @endif

                    </div>


                    <div class="grid md:grid-cols-2 gap-3 text-sm">


                                                <div>

                            <div class="text-gray-500">
                                Country
                            </div>

                            <div>
                                {{ $address->countryLocation?->name ?? '-' }}
                            </div>

                        </div>



                        <div>

                            <div class="text-gray-500">
                                Region
                            </div>

                            <div>
                                {{ $address->regionLocation?->name ?? '-' }}
                            </div>

                        </div>



                        <div>

                            <div class="text-gray-500">
                                City
                            </div>

                            <div>
                                {{ $address->city }}
                            </div>

                        </div>



                        <div>

                            <div class="text-gray-500">
                                Postal Code
                            </div>

                            <div>
                                {{ $address->postal_code }}
                            </div>

                        </div>



                        <div class="md:col-span-2">

                            <div class="text-gray-500">
                                Street
                            </div>

                            <div>
                                {{ $address->street }}
                            </div>

                        </div>



                        <div>

                            <div class="text-gray-500">
                                Phone
                            </div>

                            <div>
                                {{ $address->phone }}
                            </div>

                        </div>


                    </div>


                </div>

            @endforeach

            </div>


        @else

            <div class="text-gray-500">
                No addresses found.
            </div>

        @endif

    </div>

</div>