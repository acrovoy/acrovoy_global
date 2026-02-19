    <div class="space-y-1">
        {{-- Левая колонка: данные погрузки --}}
                                    <div>
                                        <strong>Status:</strong>
                                        <span class="{{ $shipment->status ? '' : 'text-red-500' }}">
                                            {{ $shipment->status ?? '-' }}
                                        </span>
                                    </div>
                                    <div>
                                        <strong>Contact Name:</strong>
                                        <span class="{{ $shipment->origin_contact_name ? '' : 'text-red-500' }}">
                                            {{ $shipment->origin_contact_name ?? '-' }}
                                        </span>
                                    </div>
                                    <div>
                                        <strong>Phone:</strong>
                                        <span class="{{ $shipment->origin_contact_phone ? '' : 'text-red-500' }}">
                                            {{ $shipment->origin_contact_phone ?? '-' }}
                                        </span>
                                    </div>
                                    <div>
                                        <strong>Address:</strong>
                                        <span class="{{ $shipment->origin_address ? '' : 'text-red-500' }}">
                                            {{ $shipment->origin_address ?? '-' }}
                                        </span>
                                    </div>
                                    <div>
                                        <strong>City:</strong>
                                        <span class="{{ $shipment->originCity?->name ? '' : 'text-red-500' }}">
                                            {{ $shipment->originCity?->name ?? '-' }}
                                        </span>
                                    </div>
                                    <div>
                                        <strong>Region:</strong>
                                        <span class="{{ $shipment->originRegion?->name ? '' : 'text-red-500' }}">
                                            {{ $shipment->originRegion?->name ?? '-' }}
                                        </span>
                                    </div>
                                    <div>
                                        <strong>Country:</strong>
                                        <span class="{{ $shipment->originCountry?->name ? '' : 'text-red-500' }}">
                                            {{ $shipment->originCountry?->name ?? '-' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Правая колонка: данные упаковки --}}
                                <div class="space-y-1">
                                    <div>
                                        <strong>Weight:</strong>
                                        <span class="{{ $shipment->weight ? '' : 'text-red-500' }}">
                                            {{ $shipment->weight ?? '-' }} kg
                                        </span>
                                    </div>
                                    <div>
                                        <strong>Dimensions:</strong>
                                        <span class="{{ $shipment->length && $shipment->width && $shipment->height ? '' : 'text-red-500' }}">
                                            {{ $shipment->length ?? '-' }} × {{ $shipment->width ?? '-' }} × {{ $shipment->height ?? '-' }}
                                        </span>
                                    </div>
                                    <div>
                                        <strong>Price:</strong>
                                        <span class="{{ $shipment->shipping_price ? '' : 'text-red-500' }}">
                                            {{ $shipment->shipping_price ?? '-' }}
                                        </span>
                                    </div>
                                    <div>
                                        <strong>Delivery Time:</strong>
                                        <span class="{{ $shipment->delivery_time ? '' : 'text-red-500' }}">
                                            {{ $shipment->delivery_time ?? '-' }} days
                                        </span>
                                    </div>
                                    <div>
                                        <strong>Tracking:</strong>
                                        <span class="{{ $shipment->tracking_number ? '' : 'text-red-500' }}">
                                            {{ $shipment->tracking_number ?? '-' }}
                                        </span>
                                    </div>
                                </div>