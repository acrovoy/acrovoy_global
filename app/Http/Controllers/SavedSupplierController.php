<?php

namespace App\Http\Controllers;

use App\Models\SavedSupplier;
use App\Models\Supplier;
use App\Services\Company\ActiveContextService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SavedSupplierController extends Controller
{
    public function __construct(
        private readonly ActiveContextService $context
    ) {
    }

    public function index(): View
    {
        $buyer = $this->context->buyerProfile();

        abort_unless($buyer, 403, 'Buyer context is required.');

        $items = SavedSupplier::query()
            ->where('buyer_type', $buyer::class)
            ->where('buyer_id', $buyer->getKey())
            ->whereHas('supplier')
            ->with([
                'supplier.country',
                'supplier.profile',
            ])
            ->latest()
            ->paginate(8)
            ->withQueryString();

        return view(
            'dashboard.buyer.saved-suppliers.index',
            compact('items')
        );
    }

    public function store(Supplier $supplier): JsonResponse
    {
        $buyer = $this->context->buyerProfile();

        abort_unless($buyer, 403, 'Buyer context is required.');

        try {
            DB::transaction(function () use ($buyer, $supplier) {
                SavedSupplier::query()->firstOrCreate(
                    [
                        'buyer_type' => $buyer::class,
                        'buyer_id' => $buyer->getKey(),
                        'supplier_id' => $supplier->getKey(),
                    ],
                    [
                        'created_by' => auth()->id(),
                    ]
                );
            });
        } catch (QueryException $exception) {
            if (! $this->isDuplicateKeyException($exception)) {
                Log::error('Unable to save supplier.', [
                    'buyer_type' => $buyer::class,
                    'buyer_id' => $buyer->getKey(),
                    'supplier_id' => $supplier->getKey(),
                    'exception' => $exception->getMessage(),
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Unable to save this supplier right now.',
                ], 500);
            }

            $exists = SavedSupplier::query()
                ->where('buyer_type', $buyer::class)
                ->where('buyer_id', $buyer->getKey())
                ->where('supplier_id', $supplier->getKey())
                ->exists();

            if (! $exists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unable to save this supplier right now.',
                ], 500);
            }
        }

        return response()->json([
            'status' => 'saved',
            'count' => $this->countFor($buyer),
            'message' => 'Supplier added to your saved list.',
        ]);
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        $buyer = $this->context->buyerProfile();

        abort_unless($buyer, 403, 'Buyer context is required.');

        SavedSupplier::query()
            ->where('buyer_type', $buyer::class)
            ->where('buyer_id', $buyer->getKey())
            ->where('supplier_id', $supplier->getKey())
            ->delete();

        return response()->json([
            'status' => 'removed',
            'count' => $this->countFor($buyer),
            'message' => 'Supplier removed from your saved list.',
        ]);
    }

    public function count(): JsonResponse
    {
        $buyer = $this->context->buyerProfile();

        abort_unless($buyer, 403, 'Buyer context is required.');

        return response()->json([
            'count' => $this->countFor($buyer),
        ]);
    }

    private function countFor(object $buyer): int
    {
        return SavedSupplier::query()
            ->where('buyer_type', $buyer::class)
            ->where('buyer_id', $buyer->getKey())
            ->count();
    }

    private function isDuplicateKeyException(
        QueryException $exception
    ): bool {
        $sqlState = $exception->errorInfo[0] ?? null;
        $driverCode = (int) ($exception->errorInfo[1] ?? 0);

        if ($sqlState === '23000' && $driverCode === 1062) {
            return true;
        }

        if ($sqlState === '23505') {
            return true;
        }

        return $sqlState === '23000' && $driverCode === 19;
    }
}
