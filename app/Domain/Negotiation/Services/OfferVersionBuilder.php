<?php

namespace App\Domain\Negotiation\Services;

use App\Domain\Negotiation\Models\RfqOffer;
use App\Domain\Negotiation\Models\RfqOfferVersion;
use App\Domain\Negotiation\Models\RfqOfferVersionItem;

class OfferVersionBuilder
{
    public function build(
        RfqOffer $offer,
        array $data
    ): RfqOfferVersion {

        $versionNumber = $offer
            ->versions()
            ->max('version_number') + 1;

        $version = RfqOfferVersion::create([
            'rfq_offer_id' => $offer->id,
            'version_number' => $versionNumber,
            'total_price' => $data['total_price'] ?? null,
            'comment' => $data['comment'] ?? null,
            'is_counter' => $data['is_counter'] ?? false,
            'created_by' => auth()->id(),
        ]);

        $this->copyItems($offer, $version);

        return $version;
    }

    /**
     * Copy snapshot of items into version
     */
    protected function copyItems(RfqOffer $offer, RfqOfferVersion $version): void
    {
        foreach ($offer->items as $item) {

            RfqOfferVersionItem::create([
                'offer_version_id' => $version->id,
                'offer_item_id' => $item->id,
                'requirement_id' => $item->attribute_id,

                'unit_price' => $item->unit_price,
                'quantity' => $item->quantity,
                'currency' => $item->currency,

                'lead_time_days' => $item->lead_time_days,
                'moq' => $item->moq,
                'notes' => $item->notes,
            ]);
        }
    }

    /**
     * AUTO SAVE — SINGLE FIELD UPDATE (IMPORTANT)
     */
    public function updateItem(
    RfqOfferVersion $version,
    int $attributeId,
    array $payload
): RfqOfferVersionItem {

logger()->info('DEBUG VERSION', [
    'version_id' => $version->id,
    'rfq_offer_id' => $version->rfq_offer_id,
    'status' => $version->status,
    'is_counter' => $version->is_counter,
    'created_by' => $version->created_by,
    'auth_id' => auth()->id(),
    'payload' => $payload,
]);

    $item = RfqOfferVersionItem::firstOrCreate([
        'offer_version_id' => $version->id,
        'attribute_id' => $attributeId,
    ]);

    /**
     * =========================
     * PRICE
     * =========================
     */
    if (array_key_exists('unit_price', $payload)) {
        $item->unit_price = $payload['unit_price'];
    }

    /**
     * =========================
     * NOTES
     * =========================
     */
    if (array_key_exists('notes', $payload)) {
        $item->notes = $payload['notes'];
    }

    /**
     * =========================
     * OTHER FIELDS (если будут)
     * =========================
     */
    if (array_key_exists('quantity', $payload)) {
        $item->quantity = $payload['quantity'];
    }

    if (array_key_exists('currency', $payload)) {
        $item->currency = $payload['currency'];
    }

    if (array_key_exists('lead_time_days', $payload)) {
        $item->lead_time_days = $payload['lead_time_days'];
    }

    if (array_key_exists('moq', $payload)) {
        $item->moq = $payload['moq'];
    }

    $item->save();

    /**
     * =========================
     * SELECT
     * =========================
     */
    if (array_key_exists('option_id', $payload)) {
        $item->options()->sync([$payload['option_id']]);
    }

    /**
     * =========================
     * MULTISELECT
     * =========================
     */
    if (array_key_exists('option_ids', $payload)) {
        $item->options()->sync($payload['option_ids']);
    }

    return $item;
}

public function updateCustomRequirement(
    RfqOfferVersion $version,
    int $requirementId,
    string $key,
    mixed $value
): RfqOfferVersionItem {

    $item = RfqOfferVersionItem::firstOrCreate([
        'offer_version_id' => $version->id,
        'requirement_id' => $requirementId,
    ]);

    if ($key === 'price') {
        $item->unit_price = $value;
    }

    if ($key === 'notes') {
        $item->notes = $value;
    }

    $item->save();

    return $item;
}


    public function getDraftVersion(int $rfqId, int $supplierId): RfqOfferVersion
{
    $offer = RfqOffer::query()
        ->firstOrCreate([
            'rfq_id' => $rfqId,
            'participant_type' => \App\Models\Supplier::class,
            'participant_id' => $supplierId,
        ]);

    // ищем последнюю draft версию
    $draft = $offer->versions()
        ->where('status', 'draft')
        ->latest('version_number')
        ->first();

    if ($draft) {
        return $draft;
    }

    // если нет — создаём одну draft версию
    return RfqOfferVersion::create([
        'rfq_offer_id' => $offer->id,
        'version_number' => $offer->versions()->max('version_number') + 1,
        'status' => 'draft',
        'created_by' => auth()->id(),
    ]);
}
}