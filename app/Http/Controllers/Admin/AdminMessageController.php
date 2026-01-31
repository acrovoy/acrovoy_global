<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Message;
use App\Models\MessageThread;
use App\Models\MessageThreadRead;
use App\Models\User;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Facades\Validator;

class AdminMessageController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $supplier = Supplier::where('user_id', $user->id)->first();


        // Получаем все треды для пользователя 
        $threads = MessageThread::all();





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


        return view('dashboard.admin.messages', [
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
        }


        if ($activeThread)
            $activeThread->userReads()->syncWithoutDetaching($user);


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

        // Определяем user_id, который будет записан
        $senderUserId = $user->id;

        $message = Message::create([
            'thread_id' => $thread->id,
            'user_id' => $senderUserId,
            'role' => $role,
            'text' => $request->input('message'),
        ]);

        MessageThreadRead::where('message_thread_id', $thread->id)->delete();
        $thread?->userReads()->syncWithoutDetaching($user);


        // Обновляем поле updated_at треда и флаг unread
        $thread->touch();
        $thread->save();

        return response()->json(data: $message);
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

        $messages = $thread->messages()->where('id', '>', $request->lastMessage)->get();

        return response()->json(['messages' => $messages]);
    }
}
