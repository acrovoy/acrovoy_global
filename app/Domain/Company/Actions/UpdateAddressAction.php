<?php

namespace App\Domain\Company\Actions;

use App\Models\CompanyAddress;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\GeocoderService;

class UpdateAddressAction
{
    public function __construct(
        protected GeocoderService $geocoder
    ) {
    }


    public function __invoke(
        Request $request,
        Supplier $company
    ): JsonResponse {


        $data = $request->validate([

            'country_id' => [
                'nullable',
                'exists:countries,id'
            ],

            'state' => [
                'nullable',
                'string',
                'max:255'
            ],

            'city' => [
                'nullable',
                'string',
                'max:255'
            ],

            'postal_code' => [
                'nullable',
                'string',
                'max:100'
            ],

            'address_line_1' => [
                'nullable',
                'string',
                'max:255'
            ],

            'address_line_2' => [
                'nullable',
                'string',
                'max:255'
            ],

            'latitude' => [
                'nullable',
                'numeric'
            ],

            'longitude' => [
                'nullable',
                'numeric'
            ],

        ]);



        $address = $company->primaryAddress;



        if (!$address) {

            $address = new CompanyAddress();

            $address->is_primary = true;

            $company
                ->primaryAddress()
                ->save($address);
        }



        /*
        |--------------------------------------------------------------------------
        | Save address fields
        |--------------------------------------------------------------------------
        */

        $address->fill(
            collect($data)
                ->except([
                    'latitude',
                    'longitude'
                ])
                ->toArray()
        );


        $address->save();



        /*
        |--------------------------------------------------------------------------
        | Coordinates
        |--------------------------------------------------------------------------
        |
        | 1. Manual coordinates entered now
        | 2. Automatic geocoding
        | 3. Clear if nothing found
        |
        */


        $manualLat = $request->input('latitude');
        $manualLng = $request->input('longitude');



        if (
            filled($manualLat) &&
            filled($manualLng)
        ) {

            // пользователь ввел вручную

            $address->latitude = $manualLat;
            $address->longitude = $manualLng;


        } else {


            // сначала очищаем старые координаты

            $address->latitude = null;
            $address->longitude = null;



            $locationParts = collect([

                $address->address_line_1,

                $address->city,

                $address->state,

                $address->country?->name,

            ])
            ->filter();



            if ($locationParts->count() > 1) {


                $coordinates = $this->geocoder
                    ->coordinates(
                        $locationParts->implode(', ')
                    );



                if ($coordinates) {

                    $address->latitude =
                        $coordinates['lat'];

                    $address->longitude =
                        $coordinates['lon'];

                }

            }

        }



        $address->save();



        return response()->json([

            'success' => true,

            'message' => 'Address updated successfully.',

        ]);

    }
}