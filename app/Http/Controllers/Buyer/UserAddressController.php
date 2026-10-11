<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\Country;
use App\Models\Location;
use App\Models\UserAddress;
use App\Services\Company\ActiveContextService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserAddressController extends Controller
{
    public function __construct(
        private ActiveContextService $context,
    ) {}

    /**
     * Display delivery addresses for the active buyer.
     */
    public function index()
    {
        $addresses = $this->addressQuery()
            ->with(['regionLocation', 'countryLocation'])
            ->orderByDesc('is_default')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();

        return view(
            'dashboard.buyer.addresses.index',
            compact('addresses')
        );
    }

    /**
     * Show the create address form.
     */
    public function create()
    {
        return $this->formView(new UserAddress());
    }

    /**
     * Store a delivery address.
     */
    public function store(Request $request)
    {
        $buyer = $this->getBuyer();
        $data = $this->validateAddress($request);

        DB::transaction(function () use ($buyer, $data) {
            $this->lockBuyer($buyer);

            $hasAddresses = $this->addressQuery()->exists();

            $hasDefaultAddress = $this->addressQuery()
                ->where('is_default', true)
                ->exists();

            $makeDefault = !$hasAddresses
                || !$hasDefaultAddress
                || !empty($data['is_default']);

            if ($makeDefault) {
                $this->addressQuery()->update([
                    'is_default' => false,
                ]);
            }

            $city = $this->resolveCity($data);

            UserAddress::create([
                'user_id' => $buyer->id,
                'user_type' => Buyer::class,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'] ?? null,
                'country' => (int) $data['country'],
                'region' => (int) $data['region'],
                'city' => $city->name,
                'street' => $data['street'],
                'postal_code' => $data['postal_code'] ?? null,
                'phone' => $data['phone'],
                'is_default' => $makeDefault,
            ]);
        });

        return redirect()
            ->route('buyer.addresses.index')
            ->with('success', 'Delivery address created successfully.');
    }

    /**
     * Show the edit address form.
     */
    public function edit(UserAddress $address)
    {
        $address = $this->findOwnedAddress($address->id);

        return $this->formView($address);
    }

    /**
     * Update a delivery address.
     */
    public function update(Request $request, UserAddress $address)
    {
        $address = $this->findOwnedAddress($address->id);
        $data = $this->validateAddress($request);

        DB::transaction(function () use ($address, $data) {
            $buyer = $this->getBuyer();

            $this->lockBuyer($buyer);

            $currentAddress = $this->findOwnedAddress($address->id);

            $wasDefault = (bool) $currentAddress->is_default;
            $requestedDefault = !empty($data['is_default']);

            $hasOtherDefault = $this->addressQuery()
                ->where('is_default', true)
                ->where('id', '!=', $currentAddress->id)
                ->exists();

            $city = $this->resolveCity($data);

            if ($requestedDefault) {
                // The current address becomes the only default.
                $this->addressQuery()->update([
                    'is_default' => false,
                ]);

                $makeDefault = true;
            } elseif ($wasDefault) {
                // Transfer the default status if another address exists.
                $nextAddress = $this->addressQuery()
                    ->where('id', '!=', $currentAddress->id)
                    ->orderByDesc('updated_at')
                    ->orderByDesc('id')
                    ->first();

                if ($nextAddress) {
                    $this->addressQuery()->update([
                        'is_default' => false,
                    ]);

                    $nextAddress->update([
                        'is_default' => true,
                    ]);

                    $makeDefault = false;
                } else {
                    // Keep the only saved address as default.
                    $makeDefault = true;
                }
            } elseif (!$hasOtherDefault) {
                // Restore a default if none currently exists.
                $makeDefault = true;
            } else {
                $makeDefault = false;
            }

            $currentAddress->update([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'] ?? null,
                'country' => (int) $data['country'],
                'region' => (int) $data['region'],
                'city' => $city->name,
                'street' => $data['street'],
                'postal_code' => $data['postal_code'] ?? null,
                'phone' => $data['phone'],
                'is_default' => $makeDefault,
            ]);
        });

        return redirect()
            ->route('buyer.addresses.index')
            ->with('success', 'Delivery address updated successfully.');
    }

    /**
     * Set an address as the default delivery address.
     */
    public function setDefault(UserAddress $address)
    {
        $address = $this->findOwnedAddress($address->id);

        DB::transaction(function () use ($address) {
            $this->lockBuyer($this->getBuyer());

            $currentAddress = $this->findOwnedAddress($address->id);

            $this->addressQuery()->update([
                'is_default' => false,
            ]);

            $currentAddress->update([
                'is_default' => true,
            ]);
        });

        return redirect()
            ->route('buyer.addresses.index')
            ->with('success', 'Default delivery address updated.');
    }

    /**
     * Delete an address and promote another if necessary.
     */
    public function destroy(UserAddress $address)
    {
        $address = $this->findOwnedAddress($address->id);

        DB::transaction(function () use ($address) {
            $this->lockBuyer($this->getBuyer());

            $currentAddress = $this->findOwnedAddress($address->id);

            $currentAddress->delete();

            $hasDefaultAddress = $this->addressQuery()
                ->where('is_default', true)
                ->exists();

            if (!$hasDefaultAddress) {
                $nextAddress = $this->addressQuery()
                    ->orderByDesc('updated_at')
                    ->orderByDesc('id')
                    ->first();

                if ($nextAddress) {
                    $nextAddress->update([
                        'is_default' => true,
                    ]);
                }
            }
        });

        return redirect()
            ->route('buyer.addresses.index')
            ->with('success', 'Delivery address deleted successfully.');
    }

    /**
     * Render the shared create/edit form.
     *
     * Names such as savedAddresses, lastAddress, countries and regions
     * follow the conventions used by the checkout controller.
     */
    private function formView(UserAddress $address)
    {
        $savedAddresses = $this->addressQuery()
            ->with(['regionLocation', 'countryLocation'])
            ->orderByDesc('is_default')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();

        $lastAddress = $address->exists
            ? $address
            : $savedAddresses->first();

        $countries = Country::query()
            ->withCurrentTranslation()
            ->orderBy('name')
            ->get();

        $selectedCountry = old(
    'country',
    $address->exists
        ? ($address->getAttribute('country') ?? '')
        : ''
);

$selectedRegion = old(
    'region',
    $address->exists
        ? ($address->getAttribute('region') ?? '')
        : ''
);

        $regions = collect();

        if ($selectedCountry !== '' && $selectedCountry !== null) {
            $regions = Location::query()
                ->whereNull('parent_id')
                ->where('country_id', (int) $selectedCountry)
                ->orderBy('name')
                ->get();
        }

        $cities = collect();

        if (
            $selectedCountry !== ''
            && $selectedCountry !== null
            && $selectedRegion !== ''
            && $selectedRegion !== null
        ) {
            $cities = Location::query()
                ->where('parent_id', (int) $selectedRegion)
                ->where('country_id', (int) $selectedCountry)
                ->orderBy('name')
                ->get();
        }

        // user_addresses.city stores the city name, not its ID.
        // Resolve that name back to a Location ID for the form select.
        $selectedCity = '';

        if ($address->exists && $address->city) {
            $selectedCity = $cities
                ->firstWhere('name', $address->city)
                ?->id ?? '';
        }

        $selectedCity = old('city', $selectedCity);

        // Keep $addresses available for views using the existing name.
        $addresses = $savedAddresses;

        return view(
            'dashboard.buyer.addresses.form',
            compact(
                'address',
                'addresses',
                'savedAddresses',
                'lastAddress',
                'countries',
                'regions',
                'cities',
                'selectedCountry',
                'selectedRegion',
                'selectedCity'
            )
        );
    }

    /**
     * Return the active buyer profile.
     */
    private function getBuyer(): Buyer
    {
        $buyer = $this->context->buyer();

        abort_unless(
            $buyer instanceof Buyer && $buyer->exists,
            403
        );

        return $buyer;
    }

    /**
     * Lock the buyer row during address changes.
     */
    private function lockBuyer(Buyer $buyer): void
    {
        Buyer::query()
            ->whereKey($buyer->getKey())
            ->lockForUpdate()
            ->firstOrFail();
    }

    /**
     * Build a query scoped to the active buyer.
     */
    private function addressQuery()
    {
        $buyer = $this->getBuyer();

        return UserAddress::query()
            ->where('user_id', $buyer->id)
            ->where('user_type', Buyer::class);
    }

    /**
     * Find an address owned by the active buyer.
     */
    private function findOwnedAddress(int $id): UserAddress
    {
        return $this->addressQuery()->findOrFail($id);
    }

    /**
     * Validate submitted delivery address fields.
     */
    private function validateAddress(Request $request): array
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],

            'country' => [
                'required',
                'integer',
                'exists:countries,id',
            ],

            'region' => [
                'required',
                'integer',
                'exists:locations,id',
            ],

            'city' => [
                'nullable',
                'integer',
                'exists:locations,id',
                'required_without:city_manual',
            ],

            'city_manual' => [
                'nullable',
                'string',
                'max:150',
                'required_without:city',
            ],

            'street' => ['required', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:30'],
            'phone' => ['required', 'string', 'max:50'],
            'is_default' => ['sometimes', 'boolean'],
        ]);

        $data['city_manual'] = trim($data['city_manual'] ?? '');

        if ($data['city_manual'] === '' && empty($data['city'])) {
            throw ValidationException::withMessages([
                'city' => 'Please select a city or enter it manually.',
            ]);
        }

        return $data;
    }

    /**
     * Resolve the city and verify that it belongs to the selected region
     * and country. user_addresses.city stores the city name.
     */
    private function resolveCity(array $data): Location
    {
        $countryId = (int) $data['country'];
        $regionId = (int) $data['region'];

        $region = Location::query()
            ->whereKey($regionId)
            ->whereNull('parent_id')
            ->where('country_id', $countryId)
            ->first();

        if (!$region) {
            throw ValidationException::withMessages([
                'region' => 'The selected region does not belong to this country.',
            ]);
        }

        $manualCity = trim($data['city_manual'] ?? '');

        if ($manualCity !== '') {
            $city = Location::query()
                ->where('parent_id', $regionId)
                ->where('country_id', $countryId)
                ->whereRaw('LOWER(TRIM(name)) = ?', [
                    mb_strtolower($manualCity),
                ])
                ->first();

            if (!$city) {
                $city = Location::create([
                    'name' => $manualCity,
                    'parent_id' => $regionId,
                    'country_id' => $countryId,
                    'updated_by' => auth()->id(),
                ]);
            }

            return $city;
        }

        $city = Location::query()
            ->whereKey((int) $data['city'])
            ->where('parent_id', $regionId)
            ->where('country_id', $countryId)
            ->first();

        if (!$city) {
            throw ValidationException::withMessages([
                'city' => 'The selected city does not belong to this region.',
            ]);
        }

        return $city;
    }

    public function getRegions(int $country)
{
    $locale = app()->getLocale();

    $regions = \App\Models\Location::query()
        ->whereNull('parent_id')
        ->where('country_id', $country)
        ->with(['translations' => function ($query) use ($locale) {
            $query->whereIn('locale', array_unique([$locale, 'en']));
        }])
        ->orderBy('name')
        ->get();

    return response()->json(
        $regions->map(function ($region) use ($locale) {
            $translation = $region->translations->firstWhere('locale', $locale)
                ?? $region->translations->firstWhere('locale', 'en');

            return [
                'id' => $region->id,
                'name' => $translation?->name ?: $region->name,
            ];
        })->values()
    );
}

public function getCities(int $country, int $region)
{
    $locale = app()->getLocale();

    $cities = \App\Models\Location::query()
        ->where('parent_id', $region)
        ->where('country_id', $country)
        ->with(['translations' => function ($query) use ($locale) {
            $query->whereIn('locale', array_unique([$locale, 'en']));
        }])
        ->orderBy('name')
        ->get();

    return response()->json(
        $cities->map(function ($city) use ($locale) {
            $translation = $city->translations->firstWhere('locale', $locale)
                ?? $city->translations->firstWhere('locale', 'en');

            return [
                'id' => $city->id,
                'name' => $translation?->name ?: $city->name,
            ];
        })->values()
    );
}

}