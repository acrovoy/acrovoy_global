<?php

namespace App\Domain\Company\Actions;

use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UpdateOverviewAction
{
    public function __invoke(Request $request, Supplier $company): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'country_id' => ['nullable', 'integer'],
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
            Supplier::query()
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