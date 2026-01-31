<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\MessageThread;
use App\Models\Message;
use App\Models\MessageThreadRead;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /**
     * Отображение Message Center для текущей роли
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $supplier = Supplier::where('user_id', $user->id)->first();


        // Получаем все треды для пользователя как manufacturer
        $threads = MessageThread::where(function ($q) use ($supplier) {
            $q->where('manufacturer_id', $supplier->id);
        })->orderBy('updated_at', 'desc')->get();

        // Активный тред — первый или null, если тредов нет
        $activeThread = $request->filled('thread_id')
            ? $threads->firstWhere('id', $request->thread_id)
            : $threads->first();

        // Сообщения для активного треда
        $messages = $activeThread
            ? Message::where('thread_id', $activeThread->id)
            ->orderBy('created_at')
            ->get()
            : collect(); // пустая коллекция

        if ($activeThread)
            $activeThread->userReads()->syncWithoutDetaching($user);

        return view('dashboard.manufacturer.messages', [
            'threads' => $threads,
            'activeThread' => $activeThread,
            'messages' => $messages,
        ]);
    }


    /**
     * Получение сообщений для конкретного треда (AJAX)
     */
    public function threadMessages(Request $request)
    {
        $user = Auth::user();
        $supplier = Supplier::where('user_id', $user->id)->first();

        // Получаем все треды пользователя (buyer или manufacturer)
        $threads = MessageThread::with([
            'products.category',
            'products.supplier'
        ])
            ->where(function ($q) use ($user, $supplier) {
                if ($user->role === 'buyer') {
                    $q->where('buyer_id', $user->id);
                } else {
                    $q->where('manufacturer_id', $supplier->id);
                }
            })
            ->latest()
            ->get();

        // Определяем активный тред (по query ?thread_id= или первый)
        $activeThread = $request->filled('thread_id')
            ? $threads->firstWhere('id', $request->thread_id)
            : $threads->first();

        // Если тредов нет — пустая коллекция сообщений
        $messages = collect();

        if ($activeThread) {
            $messages = Message::where('thread_id', $activeThread->id)
                ->orderBy('created_at')
                ->get();
            $activeThread->userReads()->syncWithoutDetaching($user);
        }



        return view(
            $user->role === 'buyer'
                ? 'dashboard.buyer.messages'
                : 'dashboard.manufacturer.messages',
            [
                'threads' => $threads,
                'messages' => $messages,
                'activeThread' => $activeThread,
            ]
        );
    }


    /**
     * Отправка нового сообщения
     */
    public function sendMessage(Request $request, MessageThread $thread)
    {
        $user = Auth::user();
        $supplier = Supplier::where('user_id', $user->id)->first();
        $role = session('dashboard_role', $user->role);

        // Проверка доступа
        if (($role === 'buyer' && $thread->buyer_id !== $user->id) ||
            ($role === 'manufacturer' && $thread->manufacturer_id !== $supplier->id)
        ) {
            abort(403);
        }

        $request->validate([
            'text' => 'required|string|max:1000',
        ]);

        // Определяем user_id, который будет записан
        $senderUserId = $role === 'buyer' ? $user->id : ($supplier?->user_id ?? null);

        $message = Message::create([
            'thread_id' => $thread->id,
            'user_id' => $senderUserId,
            'role' => $role,
            'text' => $request->input('text'),
        ]);

        MessageThreadRead::where('message_thread_id', $thread->id)->delete();
        $thread?->userReads()->syncWithoutDetaching($user);

        // Обновляем поле updated_at треда и флаг unread
        $thread->touch();
        $thread->save();

        $message->load('user');

        return response()->json($message);
    }

    public function pollMessages($thread, Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make(
            $request->all(),
            ['lastMessage' => 'required|integer']
        );

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid lastMessage parameter'], 400);
        }

        $thread = MessageThread::find($thread);

        $messages = $thread->messages()->with('user')->where('id', '>', $request->lastMessage)->get();

        return response()->json(['messages' => $messages]);
    }

    public function productThread($productId)
    {
        $user = auth()->user();

        // Находим продукт вместе с supplier
        $product = Product::with('supplier')->findOrFail($productId);

        // Проверяем роль пользователя
        if ($user->role !== 'buyer') {
            abort(403, 'Only buyers can start a chat.');
        }

        // Проверяем, что у продукта есть supplier
        if (!$product->supplier || !$product->supplier->id) {
            abort(404, 'Supplier not found for this product.');
        }

        $thread = MessageThread::where('buyer_id', $user->id)
            ->where('manufacturer_id', $product->supplier->id)
            ->whereHas('products', function ($q) use ($product) {
                $q->where('products.id', $product->id);
            })
            ->first();

        if (!$thread) {
            $thread = MessageThread::create(
                [
                    'buyer_id' => $user->id,
                    'manufacturer_id' => $product->supplier->id,
                    'title' => 'Inquiry about ' . $product->name,
                    'role_view' => 'buyer',
                ]
            );
            $thread->products()->attach($product->id);
        }

        // Загружаем сообщения
        $messages = $thread->messages()->get();

        return response()->json(['messages' => $messages, 'thread' => $thread]);
    }
}
