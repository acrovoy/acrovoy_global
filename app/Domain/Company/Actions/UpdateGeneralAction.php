<?php

namespace App\Domain\Company\Actions;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UpdateGeneralAction
{
    public function __invoke(
        Request $request,
        Supplier $company
    ): JsonResponse {

        $data = $request->validate([
            'about_us_description'    => ['nullable', 'string'],
            'founded_year'            => ['nullable', 'integer', 'min:1800', 'max:' . date('Y')],
            'annual_export_revenue'   => ['nullable', 'numeric', 'min:0'],
            'total_employees'         => ['nullable', 'integer', 'min:0'],
            'registration_capital'    => ['nullable', 'numeric', 'min:0'],
            'export_markets_selected' => ['nullable', 'string'],
        ]);

        $profileData = [
            'about_us_description'  => $data['about_us_description'] ?? null,
            'founded_year'          => $data['founded_year'] ?? null,
            'annual_export_revenue' => $data['annual_export_revenue'] ?? null,
            'total_employees'       => $data['total_employees'] ?? null,
            'registration_capital'  => $data['registration_capital'] ?? null,
        ];

        $company->profile()->updateOrCreate(
            ['supplier_id' => $company->id],
            $profileData
        );

        $marketIds = collect(
            explode(',', $data['export_markets_selected'] ?? '')
        )
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->values();

        $company->exportMarkets()->sync($marketIds);

        return response()->json([
            'success' => true,
            'message' => 'General information updated successfully.',
        ]);
    }
}