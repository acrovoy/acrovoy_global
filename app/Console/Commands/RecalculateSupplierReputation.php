<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Supplier;
use App\Services\ReputationService;

class RecalculateSupplierReputation extends Command
{
    protected $signature = 'reputation:recalculate {supplierId?}';
    protected $description = 'Пересчитать репутацию всех поставщиков или одного поставщика';

    public function handle()
    {
        $reputationService = new ReputationService();

        $supplierId = $this->argument('supplierId');

        if ($supplierId) {
            $supplier = Supplier::find($supplierId);
            if (!$supplier) {
                $this->error("Поставщик с ID $supplierId не найден");
                return;
            }
            $score = $reputationService->recalculate($supplier);
            $this->info("Репутация поставщика ID $supplierId пересчитана: $score");
        } else {
            $suppliers = Supplier::all();
            foreach ($suppliers as $supplier) {
                $score = $reputationService->recalculate($supplier);
                $this->info("ID {$supplier->id} — репутация: $score");
            }
            $this->info("Репутация всех поставщиков пересчитана.");
        }
    }
}
