<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Services\SupplierOrderAccessService;

use App\Models\Order;
use App\Models\OrderDispute;
use App\Models\Supplier;

class OrderDisputeController extends Controller
{
    public function create(Order $order)
    {
        $this->authorize('view', $order);

        if ($order->status !== 'completed') {
            return redirect()->back()->with('error', 'Спор можно открыть только после завершения заказа.');
        }

        return view('buyer.orders.dispute', compact('order'));
    }

    public function store(Request $request, Order $order)
    {
        $this->authorize('view', $order);

        if ($order->status !== 'completed') {
            return redirect()->back()->with('error', 'Невозможно открыть спор для незавершенного заказа.');
        }

        $request->validate([
            'reason' => 'required|string|max:1000',
            'action' => 'required|in:return,compensation,exchange',
            'attachment' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
        ]);

        $dispute = new OrderDispute();
        $dispute->order_id = $order->id;
        $dispute->user_id = Auth::id();
        $dispute->reason = $request->reason;
        $dispute->action = $request->action;

        if ($request->hasFile('attachment')) {
            $dispute->attachment = $request->file('attachment')->store('disputes', 'public');
        }

        $dispute->save();

        // Можно пометить заказ как "спор" для отображения в репутации продавца
        // $order->update(['in_dispute' => true]);

        return redirect()->route('buyer.orders.show', $order->id)
                         ->with('success', 'Спор успешно отправлен!');
    }



    /**
     * Продавец — обновление спора
     */
    public function update(Request $request, Order $order, OrderDispute $dispute, SupplierOrderAccessService $access)
    {

        \Log::info('Dispute update called', [
    'order_id' => $order->id,
    'dispute_id' => $dispute->id,
    'user_id' => auth()->id(),
]);


        // Проверяем что заказ принадлежит продавцу
        $supplier = Supplier::where('user_id', auth()->id())->first();

       

abort_if(
    !$access->canAccess($order, $supplier),
    403
);

abort_if($dispute->order_id !== $order->id, 404);


      

      

        $request->validate([
            'status'        => 'required|in:pending,approved,rejected,return,resolved,supplier_offer',
            'supplier_comment' => 'nullable|string|max:1000',
        ]);

        $dispute->update([
            'status'        => $request->status,
            'supplier_comment' => $request->supplier_comment,
            
        ]);

        return redirect()->back()->with('success', 'Спор обновлён');
    }



    public function cancel(OrderDispute $dispute)
{
    // Проверка владельца спора
    if ($dispute->user_id !== auth()->id()) {
        return redirect()->back()
            ->with('error', 'У вас нет доступа к этому спору.');
    }

    // Проверка статуса
    if ($dispute->status !== 'pending') {
        return redirect()->back()
            ->with('info', 'Этот спор уже обработан и не может быть отменён.');
    }

    $dispute->update([
        'status' => 'cancelled',
    ]);

    return redirect()->back()
        ->with('success', 'Спор успешно отменён.');
}



    public function support(OrderDispute $dispute)
{
    if ($dispute->user_id !== auth()->id()) {
        return redirect()->route('buyer.orders')
            ->with('error', 'Доступ запрещён.');
    }

    return view('buyer.support.dispute', compact('dispute'));
}


public function accept(OrderDispute $dispute)
{
    // Проверяем, что спор принадлежит текущему покупателю
    if ($dispute->user_id !== auth()->id()) {
        abort(403, 'Unauthorized');
    }

    // Спор должен быть в статусе supplier_offer
    if ($dispute->status !== 'supplier_offer') {
        return back()->with('error', 'Невозможно принять решение.');
    }

    $dispute->update([
        'status' => 'resolved', // или любой статус, который у тебя считается принятым
        'supplier_comment' => $dispute->admin_comment, // оставляем коммент продавца
    ]);

    return back()->with('success', 'Вы приняли предложение продавца.');
}

public function reject(Request $request, OrderDispute $dispute)
{
    // Проверяем, что спор принадлежит текущему покупателю
    if ($dispute->user_id !== auth()->id()) {
        abort(403, 'Unauthorized');
    }

    // Спор должен быть в статусе supplier_offer
    if ($dispute->status !== 'supplier_offer') {
        return back()->with('error', 'Невозможно отклонить это предложение.');
    }

    $data = $request->validate([
        'buyer_comment' => 'nullable|string|max:500',
    ]);

    $dispute->update([
        'status' => 'pending', // возвращаем статус в ожидание
        
        'buyer_comment' => $data['buyer_comment'] ?? null, // комментарий покупателя
    ]);

    // Тут можно вызвать уведомление продавцу
    // Notification::send($dispute->order->supplier->user, new DisputeRejected($dispute));

    return back()->with('success', 'Вы отклонили предложение продавца. Спор снова открыт для обсуждения.');
}

public function appeal(Request $request, OrderDispute $dispute)
{
    // Проверяем, что спор принадлежит текущему покупателю
    if ($dispute->user_id !== auth()->id()) {
        abort(403, 'Unauthorized');
    }

    // Спор должен быть в статусе отклонён продавцом
    if ($dispute->status !== 'rejected') {
        return back()->with('error', 'Невозможно подать апелляцию на этот спор.');
    }

    // Валидация комментария покупателя
    $data = $request->validate([
        'buyer_comment' => 'nullable|string|max:500',
    ]);

    // Меняем статус на admin_review
    $dispute->update([
        'status' => 'admin_review',             // статус апелляции к админу
        'buyer_comment' => $data['buyer_comment'] ?? null,  // комментарий покупателя
                    // очищаем предыдущий комментарий продавца
    ]);

    // Здесь можно уведомить администратора
    // Notification::send(User::admin(), new DisputeAppealed($dispute));

    return back()->with('success', 'Апелляция отправлена администратору. Ожидается рассмотрение спора.');
}

public function close(OrderDispute $dispute)
{
    // Проверяем, что спор принадлежит текущему покупателю
    if ($dispute->user_id !== auth()->id()) {
        abort(403, 'Unauthorized');
    }

    // Спор может быть закрыт только если был отклонён продавцом
    if ($dispute->status !== 'rejected') {
        return back()->with('error', 'Этот спор нельзя закрыть.');
    }

    // Меняем статус на закрытый
    $dispute->update([
        'status' => 'resolved',
        'buyer_comment' => $dispute->buyer_comment ?? null, // сохраняем комментарий, если есть
    ]);

    return back()->with('success', 'Вы закрыли спор.');
}

}
