<?php 



namespace App\View\Composers;

use App\Facades\ActiveContext;

use Illuminate\View\View;
use App\Models\Category;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Language;
use App\Models\OrderDispute;
use App\Models\Wishlist;
use App\Models\CartItem;

class NavigationComposer
{
    public function compose(View $view): void
    {
        

$catalogCategories = Category::with('children')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();
        // countries
        $countries = Country::withCurrentTranslation()
            ->where('is_active', 1)
            ->orderBy('translated_name')
            ->get();

        $currentCountry = strtolower(
            auth()->user()->purchase_country
                ?? request()->cookie('purchase_country')
                ?? session('purchase_country', Country::where('is_default', 1)->value('code') ?? 'us')
        );

        $currentCountryName = optional(
            $countries->firstWhere('code', $currentCountry)
        )->name ?? strtoupper($currentCountry);

        $mainCountries = $countries->where('is_priority', 1);
        $otherCountries = $countries->where('is_priority', 0);

        //currency
        $currencies = Currency::where('is_active', 1)
            ->orderBy('code')
            ->get();

        // Текущая валюта
        $currentCurrency = strtolower(
            auth()->user()->currency
                ?? session('currency', 'usd')
        );

        $currentCurrencyObj = $currencies->firstWhere('code', strtoupper($currentCurrency));
        $currentCurrencyCode = $currentCurrencyObj->code ?? strtoupper($currentCurrency);

        // Разделяем валюты по приоритету
        $mainCurrencies = $currencies->where('is_priority', 1);
        $otherCurrencies = $currencies->where('is_priority', 0);
        //languages
        $languages = Language::where('is_active', true)
            ->orderBy('sort_order')
            ->get(); // <- Получаем коллекцию объектов
        $currentLang = app()->getLocale();


        //dispute

        $disputeCount = 0;
        $disputeLink = null;

        if (auth()->check()) {

            // 🧑 Покупатель
            if (auth()->user()->role === 'buyer') {

                $buyerOpenDisputes = OrderDispute::whereHas('order', function ($q) {
                    $q->where('user_id', auth()->id());
                })
                    ->whereIn('status', ['pending', 'supplier_offer', 'rejected', 'admin_review'])
                    ->get();

                $disputeCount = $buyerOpenDisputes->count();

                if ($disputeCount > 0) {
                    $disputeLink = route(
                        'buyer.orders.show',
                        $buyerOpenDisputes->first()->order_id
                    );
                }
            }

            // 🏭 Продавец (manufacturer)
            if (
                auth()->user()->role === 'manufacturer' &&
                auth()->user()->supplier
            ) {

                $sellerOpenDisputes = OrderDispute::whereHas(
                    'order.items.product',
                    function ($q) {
                        $q->where(
                            'supplier_id',
                            auth()->user()->supplier->id
                        );
                    }
                )
                    ->whereIn('status', ['pending', 'supplier_offer', 'rejected', 'admin_review'])
                    ->get();

                $disputeCount = $sellerOpenDisputes->count();

                if ($disputeCount > 0) {
                    $disputeLink = route(
                        'manufacturer.orders.show',
                        $sellerOpenDisputes->first()->order_id
                    );
                }
            }
        }

        //Wishlist

        $wishlistCount = Wishlist::where('buyer_type', ActiveContext::type())
    ->where('buyer_id', ActiveContext::id())
    ->count();

                    //Cart
$cartCount = CartItem::where('user_id', auth()->id())->sum('quantity');

        $view->with([
            'catalogCategories' => $catalogCategories,
            'countries' => $countries,
            'currentCountry' => $currentCountry,
            'currentCountryName' => $currentCountryName,
            'mainCountries' => $mainCountries,
            'otherCountries' => $otherCountries,
            'currencies' => $currencies,
            'currentCurrencyCode' => $currentCurrencyCode,
            'currentCurrency' => $currentCurrency,
            'mainCurrencies' => $mainCurrencies,
            'otherCurrencies' => $otherCurrencies,
            'languages' => $languages,
            'currentLang' => $currentLang,
             //dispute
             'disputeCount' => $disputeCount,
             'disputeLink' => $disputeLink,
             //Wishlist
             'wishlistCount' => $wishlistCount,
    //Cart
    'cartCount' => $cartCount,








]);
         }
}