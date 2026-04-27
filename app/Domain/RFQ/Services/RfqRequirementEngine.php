<?php

namespace App\Domain\RFQ\Services;

use App\Models\Category;
use App\Models\Attribute;
use App\Domain\Negotiation\Models\RfqOffer;

class RfqRequirementEngine
{
    /**
     * STEP 1: Get selectable RFQ categories
     */
    public function getCategories()
    {
        return Category::query()
            ->whereHas('types', function ($q) {
                $q->where('type', 'rfq');
            })
            ->where('is_selectable', 1)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * STEP 2: Get attributes for category
     */
    public function getAttributesForCategory(int $categoryId)
    {
        return Attribute::query()
            ->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            })
            ->with(['options.translations'])
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * STEP 3: Build form schema (frontend-friendly)
     */
    public function buildSchema(int $categoryId): array
    {
        $attributes = $this->getAttributesForCategory($categoryId);

        return $attributes->map(function ($attr) {

            return [
                'id' => $attr->id,
                'code' => $attr->code,
                'type' => $attr->type,
                'name' => $attr->name,
                'is_required' => $attr->is_required,
                'options' => $attr->options->map(fn ($opt) => [
                    'id' => $opt->id,
                    'value' => $opt->translatedValue(),
                ]),
            ];
        })->toArray();
    }

    /**
     * STEP 4: Normalize input (SAVE ENGINE)
     */
    public function normalize(array $attributes): array
    {
        $result = [];

        foreach ($attributes as $attributeId => $value) {

            if ($value === null || $value === '' || $value === []) {
                continue;
            }

            $result[] = [
                'attribute_id' => $attributeId,
                'value' => $value,
            ];
        }

        return $result;
    }
}