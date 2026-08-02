<?php

namespace App\Domain\Company\Actions;

use App\Models\Buyer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UpdateBuyerOverviewAction
{
    public function __invoke(Request $request, Buyer $company): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'country_id' => ['nullable', 'integer'],
            'business_types_selected' => ['nullable', 'string'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Generate unique slug
        |--------------------------------------------------------------------------
        */

        $slug = Str::slug($data['name']);

        $originalSlug = $slug;

        $counter = 1;

        while (
            Buyer::query()
            ->where('slug', $slug)
            ->whereKeyNot($company->id)
            ->exists()
        ) {
            $slug = "{$originalSlug}-{$counter}";

            $counter++;
        }

        $data['slug'] = $slug;

        /*
        |--------------------------------------------------------------------------
        | Save
        |--------------------------------------------------------------------------
        */

        $company->update($data);

       
        /*
        |--------------------------------------------------------------------------
        | Sync business types
        |--------------------------------------------------------------------------
        */

        $businessTypeIds = collect(
            explode(',', $request->input('business_types_selected', ''))
        )
            ->filter()
            ->map(fn($id) => (int) $id);

        $company->businessTypes()->sync($businessTypeIds);



        return response()->json([
            'success' => true,
            'message' => 'Company overview updated successfully.',
            'company' => [
                'id' => $company->id,
                'name' => $company->name,
                'slug' => $company->slug,
                'short_description' => $company->short_description,
                'description' => $company->description,
                'country_id' => $company->country_id,
                'country_name' => $company->country?->name,
            ],
        ]);
    }
}
