<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;

use App\Models\Rfq;
use App\Models\RfqOffer;
use App\Models\User;


class RfqPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

     /**
     * Buyer: просмотр своего RFQ
     */
    public function view(User $user, Rfq $rfq): bool
{
    // buyer видит только свои RFQ
    if ($user->role === 'buyer') {
        return $rfq->buyer_id === $user->id;
    }

    // supplier (manufacturer)
    if ($user->role === 'manufacturer') {
        // видит активные RFQ
        if ($rfq->status === 'active') {
            return true;
        }

        // видит закрытые только если есть accepted оффер
        if ($rfq->status === 'closed') {

            $supplierId = $user->supplier->id ?? null;

            return $rfq->offers
                ->where('supplier_id', $supplierId)
                ->where('status', 'accepted')
                ->isNotEmpty();
        }

        // остальные статусы — запрещено
        return false;
    }

    return false;
}

    /**
     * Buyer: создание RFQ
     */
    public function create(User $user): bool
    {
        return $user->hasRole('buyer');
    }

    /**
     * Supplier: отправка оффера
     */
    /** 
 * Supplier: отправка оффера
 */
public function sendOffer(User $user, Rfq $rfq): bool
{
    // Только производитель может отправлять оффер
    if ($user->role !== 'manufacturer') {
        return false;
    }

    // RFQ должен быть активен
    if ($rfq->status !== 'active') {
        return false;
    }

    // Можно добавить проверку, что производитель не сделал оффер дважды
    if ($rfq->offers()->where('supplier_id', $user->id)->exists()) {
        return false;
    }

    return true;
}


    // ✅ Новый метод: покупатель может принять предложение
    public function acceptOffer(User $user, RfqOffer $offer): bool
    {

    dd([
        'user_id' => $user->id,
        'buyer_id' => $offer->rfq->buyer_id ?? null,
        'rfq_status' => $offer->rfq->status ?? null,
        'offer_status' => $offer->status,
    ]);


        // Только покупатель, который создал RFQ
        if ($user->id !== $offer->rfq->buyer_id) return false;

        // RFQ должна быть активной
        if ($offer->rfq->status !== 'active') return false;

        // Оффер ещё не принят
        if ($offer->status !== 'pending') return false;

        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Rfq $rfq): bool
{
    // Только покупатель может редактировать свой RFQ
    if ($user->role === 'buyer' && $rfq->buyer_id === $user->id) {
        // Разрешаем только если нет офферов
        return $rfq->offers()->count() === 0;
    }

    // Остальные не могут
    return false;
}



    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Rfq $rfq): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Rfq $rfq): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Rfq $rfq): bool
    {
        //
    }


    /**
     * Buyer: закрытие RFQ (выбор победителя)
     */
    

}
