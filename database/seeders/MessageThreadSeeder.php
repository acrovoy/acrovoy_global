<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MessageThread;

class MessageThreadSeeder extends Seeder
{
    public function run(): void
    {
        MessageThread::create([
            'title'            => 'Inquiry about Wooden Chair',
            'product_id'       => 1, // существующий product_id
            'buyer_id'         => 7, // Mike (buyer)
            'manufacturer_id'  => 2, // Tommy (manufacturer)
            'role_view'        => 'buyer',
            'unread'           => 1,
        ]);

        MessageThread::create([
            'title'            => 'System: Product Approved',
            'product_id'       => null,
            'buyer_id'         => 7,
            'manufacturer_id'  => 2,
            'role_view'        => 'manufacturer',
            'unread'           => 0,
        ]);
    }
}
