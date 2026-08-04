<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\Company\ActiveContextService;

use App\Models\Buyer;


class BuyerController extends Controller
{

    public function __construct(
        private ActiveContextService $context,


    ) {}
  

    public function show(Request $request, $slug)
    {



        $tabs = config('marketplace.buyer_tabs');
        $activeTab = $request->get('tab', 'profile');

        $buyer = Buyer::with([
            'country',
            'businessTypes.translation',
            'factoryPhotos',
            'certificatesMedia',
        ])
            ->where('slug', $slug)
            ->firstOrFail();



        /*
    |--------------------------------------------------------------------------
    | Reputation & Level Logic
    |--------------------------------------------------------------------------
    */

        $score = $buyer->reputation ?? 0;

        /*
    |--------------------------------------------------------------------------
    | Reputation Level (Accessor Driven)
    |--------------------------------------------------------------------------
    */

        $level = $buyer->level;

        /*
    |--------------------------------------------------------------------------
    | Progress toward next level (optional)
    |--------------------------------------------------------------------------
    */

        $nextLevelScore = match ($level) {
            'Basic' => 51,
            'Silver' => 121,
            'Gold' => 201,
            default => max($score, 1)
        };

        $progress = min(($score / $nextLevelScore) * 100, 100);

      

        /*
    |--------------------------------------------------------------------------
    | Buyer Types
    |--------------------------------------------------------------------------
    */

        $types = $buyer->businessTypes
            ->map(
                fn($type) =>
                $type->translation?->name ?? $type->slug
            )
            ->filter()
            ->values();

        /*
    |--------------------------------------------------------------------------
    | Years on Platform
    |--------------------------------------------------------------------------
    */

        $yearsOnPlatform = now()->diffInYears($buyer->created_at);


     

if ($buyer->buyerable_type == 'App\Models\User') {
            $is_personal = true;
        } else {
            $is_personal = false;
        }


       $activeCountries = collect($request->get('country', []))
            ->map(fn($id) => (int) $id)
            ->toArray();

        return view('buyer.show', compact(
            'buyer', 
            'level',
            'types',
            'yearsOnPlatform',
            'tabs',
            'activeTab',
            'activeCountries',
            'is_personal',
        ));
    }
}
