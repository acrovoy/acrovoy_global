<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Message;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        // THREAD 1 — обычный диалог
        Message::create([
            'thread_id' => 1,
            'user_id'   => 7, // buyer
            'role'      => 'buyer',
            'text'      => 'Hello! I am interested in your wooden chair. What is the MOQ?',
        ]);

        Message::create([
            'thread_id' => 1,
            'user_id'   => 2, // manufacturer
            'role'      => 'manufacturer',
            'text'      => 'Hello! MOQ is 50 units. We can also discuss customization.',
        ]);

        Message::create([
            'thread_id' => 1,
            'user_id'   => 7,
            'role'      => 'buyer',
            'text'      => 'Great! What is the production lead time?',
        ]);

        // THREAD 2 — системное сообщение
        Message::create([
            'thread_id' => 2,
            'user_id'   => 2,
            'role'      => 'manufacturer',
            'text'      => 'Your product has been approved and is now live.',
        ]);
    }
}
