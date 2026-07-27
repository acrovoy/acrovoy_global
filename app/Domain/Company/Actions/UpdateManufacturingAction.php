<?php

namespace App\Domain\Company\Actions;

use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateManufacturingAction
{
    public function __invoke(
        Request $request,
        Supplier $company
    ): JsonResponse {

        $data = $request->validate([
            'manufacturing_description' => ['nullable', 'string'],

            'factory_area' => ['nullable', 'integer', 'min:0'],
            'production_lines' => ['nullable', 'integer', 'min:0'],
            'monthly_capacity' => ['nullable', 'integer', 'min:0'],
            'moq' => ['nullable', 'integer', 'min:0'],
            'lead_time_days' => ['nullable', 'integer', 'min:0'],

            'manufacturing_capabilities_selected' => ['nullable', 'string'],
        ]);

        // сохраняем профиль
        $profile = $company->profile()->updateOrCreate(
            [
                'supplier_id' => $company->id,
            ],
            collect($data)
                ->except('manufacturing_capabilities_selected')
                ->toArray()
        );

        // сохраняем Manufacturing Capabilities
        if ($request->filled('manufacturing_capabilities_selected')) {

            $ids = collect(
                explode(',', $request->manufacturing_capabilities_selected)
            )
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->values();

            $profile->manufacturingCapabilities()->sync($ids);

        } else {

            $profile->manufacturingCapabilities()->detach();

        }

        return response()->json([
            'success' => true,
            'message' => 'Manufacturing profile updated successfully.',
        ]);
    }
}