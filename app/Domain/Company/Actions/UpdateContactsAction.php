<?php

namespace App\Domain\Company\Actions;

use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateContactsAction
{
    public function __invoke(
        Request $request,
        Supplier $company
    ): JsonResponse {

        $data = $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'address' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        $company->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Contact information updated successfully.',
        ]);
    }
}