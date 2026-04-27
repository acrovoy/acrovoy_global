<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\RFQ\Models\Rfq;
use App\Domain\RFQ\Enums\RfqVisibilityType;

class RfqVisibilityController extends Controller
{
    /**
     * UPDATE RFQ VISIBILITY
     */
    public function update(Request $request, Rfq $rfq)
    {

    
        /**
         * POLICY CHECK
         * только владелец RFQ может менять visibility
         */
        $this->authorize('update', $rfq);

        /**
         * VALIDATION
         */
        $validated = $request->validate([
            'visibility_type' => [
                'required',
                'string',
                'in:' . implode(',', array_column(
                    RfqVisibilityType::cases(),
                    'value'
                )),
            ],
        ]);

        /**
         * UPDATE
         */
        $rfq->update([
            'visibility_type' => $validated['visibility_type'],
        ]);

        /**
         * RESPONSE
         */

        
        return back()->with(
            'success',
            'RFQ visibility updated successfully ✅'
        );
    }


    public function updateCategory(Request $request, Rfq $rfq)
{
    $this->authorize('update', $rfq);

    $validated = $request->validate([
        'category_ids' => ['array'],
        'category_ids.*' => ['integer', 'exists:categories,id'],
    ]);

    $rfq->visibilityCategories()->sync($validated['category_ids'] ?? []);

    return back()->with('success', 'Visibility categories updated');
}


}