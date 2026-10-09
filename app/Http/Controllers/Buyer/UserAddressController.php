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
     * List addresses belonging to the active buyer.
     */
    public function index()
{
    $addresses = $this->addressQuery()
        ->with(['regionLocation', 'countryLocation'])
        ->orderByDesc('is_default')
        ->orderByDesc('updated_at')
        ->orderByDesc('id')
        ->get();

    return view('dashboard.buyer.addresses.index', compact('addresses'));
}

    /**
     * Show the address creation form.
     */
    public function create()
    {
        $address = new UserAddress();

        $countries = Country::query()
            ->withCurrentTranslation()
            ->orderBy('name')
            ->get();

        return view('dashboard.buyer.addresses.form', compact(
            'address',
            'countries'
        ));
    }

    /**
     * Store a new delivery address.
     */
    public function store(Request $request)
    {
        $buyer = $this->getBuyer();
        $data = $this->validateAddress($request);
        $city = $this->resolveCity($data);

        DB::transaction(function () use ($buyer, $data, $city, &$address) {
            $isFirstAddress = $this->addressQuery()->doesntExist();

            $makeDefault = $isFirstAddress || !empty($data['is_default']);

            if ($makeDefault) {
                $this->addressQuery()->update([
                    'is_default' => false,
                ]);
            }

            $address = UserAddress::create([
                'user_id' => $buyer->id,
                'user_type' => Buyer::class,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'] ?? null,
                'country' => $data['country'],
                'region' => $data['region'],
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
     * Show the address editing form.
     */
    public function edit(UserAddress $address)
    {
        $address = $this->findOwnedAddress($address->id);

        $countries = Country::query()
            ->withCurrentTranslation()
            ->orderBy('name')
            ->get();

        return view('dashboard.buyer.addresses.form', compact(
            'address',
            'countries'
        ));
    }

    /**
     * Update an existing delivery address.
     */
    public function update(Request $request, UserAddress $address)
    {
        $address = $this->findOwnedAddress($address->id);
        $data = $this->validateAddress($request);
        $city = $this->resolveCity($data);

        DB::transaction(function () use ($data, $city, $address) {
            $makeDefault = !empty($data['is_default'])
                || (bool) $address->is_default;

            if ($makeDefault) {
                $this->addressQuery()->update([
                    'is_default' => false,
                ]);
            }

            $address->update([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'] ?? null,
                'country' => $data['country'],
                'region' => $data['region'],
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
     * Set an address as the buyer's default.
     */
    public function setDefault(UserAddress $address)
    {
        $address = $this->findOwnedAddress($address->id);

        DB::transaction(function () use ($address) {
            $this->addressQuery()->update([
                'is_default' => false,
            ]);

            $address->update([
                'is_default' => true,
            ]);
        });

        return redirect()
            ->route('buyer.addresses.index')
            ->with('success', 'Default delivery address updated.');
    }

    /**
     * Delete an address.
     */
    public function destroy(UserAddress $address)
    {
        $address = $this->findOwnedAddress($address->id);

        DB::transaction(function () use ($address) {
            $wasDefault = (bool) $address->is_default;

            $address->delete();

            if ($wasDefault) {
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
     * Get the active buyer.
     */
    private function getBuyer(): Buyer
    {
        $buyer = $this->context->buyer();

        abort_unless($buyer instanceof Buyer && $buyer->exists, 403);

        return $buyer;
    }

    /**
     * Query restricted to the active buyer.
     */
    private function addressQuery()
    {
        $buyer = $this->getBuyer();

        return UserAddress::query()
            ->where('user_id', $buyer->id)
            ->where('user_type', Buyer::class);
    }

    /**
     * Resolve an address and ensure it belongs to the active buyer.
     */
    private function findOwnedAddress(int $id): UserAddress
    {
        return $this->addressQuery()->findOrFail($id);
    }

    /**
     * Validate address fields.
     */
    private function validateAddress(Request $request): array
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],

            'country' => ['required', 'integer', 'exists:countries,id'],
            'region' => ['required', 'integer', 'exists:locations,id'],

            'city' => ['nullable', 'integer', 'exists:locations,id'],
            'city_manual' => ['nullable', 'string', 'max:150'],

            'street' => ['required', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:30'],
            'phone' => ['required', 'string', 'max:50'],

            'is_default' => ['sometimes', 'boolean'],
        ]);

        if (
            empty(trim($data['city_manual'] ?? ''))
            && empty($data['city'])
        ) {
            throw ValidationException::withMessages([
                'city' => 'Please select a city or enter it manually.',
            ]);
        }

        return $data;
    }

    /**
     * Resolve the city using the same storage convention as checkout.
     *
     * user_addresses.city stores the city name, not its ID.
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
                ->where('name', $manualCity)
                ->where('parent_id', $regionId)
                ->where('country_id', $countryId)
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
}