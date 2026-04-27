<?php

namespace App\Domain\RFQ\Services;

use App\Domain\RFQ\Models\Rfq;
use App\Services\Company\ActiveContextService;
use App\Domain\RFQ\Enums\RfqStatus;
use App\Domain\RFQ\Enums\RfqVisibilityType;
use App\Models\Supplier;

class RfqVisibilityService
{
    /**
     * SINGLE RFQ VISIBILITY CHECK
     */
    public function canViewInList(Rfq $rfq, ActiveContextService $context): bool
    {
        /*
        OWNER (BUYER)
        */

        if (
            $rfq->buyer_id === $context->id()
            && $rfq->buyer_type === $context->type()
        ) {
            return true;
        }

        /*
        ONLY SUPPLIERS дальше
        */

        if (!$context->isCompany()) {
            return false;
        }

        if ($context->type() !== Supplier::class) {
            return false;
        }

        /*
        PARTICIPANT ACCESS
        */

        $isParticipant = $rfq->participants()
            ->where('supplier_id', $context->id())
            ->exists();

        if ($isParticipant) {
            return true;
        }

        /*
        ONLY PUBLISHED дальше
        */

        if ($rfq->status !== RfqStatus::PUBLISHED->value) {
            return false;
        }

        /*
        CATEGORY DISCOVERY
        */

        if ($rfq->visibility_type === RfqVisibilityType::CATEGORY->value) {
            return $this->supplierMatchesCategory($rfq, $context);
        }

        /*
        PLATFORM DISCOVERY
        */

        if ($rfq->visibility_type === RfqVisibilityType::PLATFORM->value) {
            return true;
        }

        /*
        OPEN DISCOVERY
        */

        if ($rfq->visibility_type === RfqVisibilityType::OPEN->value) {
            return true;
        }

        return false;
    }

    /**
     * BULK QUERY FILTER
     */
    public function filterVisible($query, ActiveContextService $context)
    {
        return $query->where(function ($q) use ($context) {

            /*
            OWNER RFQs
            */

            if ($context->isCompany()) {

                $q->orWhere(function ($owner) use ($context) {

                    $owner
                        ->where('buyer_id', $context->id())
                        ->where('buyer_type', $context->type());
                });
            }

            /*
            SUPPLIER DISCOVERY
            */

            if (
                $context->isCompany()
                && $context->type() === Supplier::class
            ) {

                $supplierId = $context->id();

                /*
                PARTICIPANT
                */

                $q->orWhereHas('participants', function ($p) use ($supplierId) {

                    $p->where('supplier_id', $supplierId);
                });

                /*
                CATEGORY
                */

                $q->orWhere(function ($category) {

                    $category
                        ->where('visibility_type', 'category')
                        ->where('status', 'published');
                });

                /*
                PLATFORM
                */

                $q->orWhere(function ($platform) {

                    $platform
                        ->where('visibility_type', 'platform')
                        ->where('status', 'published');
                });

                /*
                OPEN
                */

                $q->orWhere(function ($open) {

                    $open
                        ->where('visibility_type', 'open')
                        ->where('status', 'published');
                });
            }
        });
    }

    /**
     * CATEGORY MATCH ENGINE
     */
    private function supplierMatchesCategory(
        Rfq $rfq,
        ActiveContextService $context
    ): bool {

        /*
        здесь позже будет:

        supplier_categories pivot
        */

        return true;
    }
}