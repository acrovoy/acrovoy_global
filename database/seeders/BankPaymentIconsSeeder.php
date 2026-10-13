<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class BankPaymentIconsSeeder extends Seeder
{
    public function run(): void
    {
        $icons = [
    'bank_of_america_us' => 'boa',
    'wells_fargo_us' => 'wells',
    'deutsche_bank_de' => 'deutsche',
    'bnp_paribas_fr' => 'bnp',
    'bank_of_china_cn' => 'boc',
    'kb_kb_korea' => 'kookmin',
    'shinhan_korea' => 'shinhan',
    'hana_korea' => 'hana',
    'first_abu_dhabi_bank_ae' => 'fab',
    'qatar_national_bank_qa' => 'qnb',
    'commbank_au' => 'commonwealth',
];

        foreach ($icons as $code => $slug) {
            $method = DB::table('payment_methods')
                ->where('code', $code)
                ->whereNull('icon_svg')
                ->first();

            if (!$method) {
                continue;
            }

            $url = "https://raw.githubusercontent.com/auraveni/global-bank-logos/main/assets/bank/international-bank/{$slug}.svg";

            try {
                $response = Http::withOptions([
    'verify' => storage_path('app/cacert.pem'),
])->timeout(20)->get($url);

                if (!$response->successful()) {
                    $this->command?->warn("SVG not found: {$code}");
                    continue;
                }

                $svg = trim($response->body());

                if (!str_contains($svg, '<svg')) {
                    $this->command?->warn("Invalid SVG: {$code}");
                    continue;
                }

                DB::table('payment_methods')
                    ->where('id', $method->id)
                    ->update([
                        'icon_svg' => $svg,
                        'updated_at' => now(),
                    ]);

                $this->command?->info("Saved SVG: {$code}");
            } catch (\Throwable $e) {
                $this->command?->warn("Failed {$code}: {$e->getMessage()}");
            }
        }
    }
}