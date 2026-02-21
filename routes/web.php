<?php

use App\Http\Controllers\ProfileController;

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\SocialLoginController;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ManufacturerOrderController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\BuyerCartController;
use App\Http\Controllers\DashboardRoleController;
use App\Http\Controllers\BuyerOrderController;
use App\Http\Controllers\ManufacturerController;
use App\Http\Controllers\PremiumSellerPlanController;
use App\Http\Controllers\PremiumBuyerPlanController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\ShippingTemplateController;
use App\Http\Controllers\ProductPriceController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderReviewController;
use App\Http\Controllers\OrderDisputeController;
use App\Http\Controllers\SupplierReviewController;
use App\Http\Controllers\HelpController;

use App\Http\Controllers\Supplier\SupplierRfqController;

use App\Http\Controllers\Buyer\BuyerRfqController;
use App\Http\Controllers\Buyer\BuyerProjectController;
use App\Http\Controllers\Buyer\ProjectItemController;

use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminSellersController;
use App\Http\Controllers\Admin\AdminMessageController;
use App\Http\Controllers\Admin\AdminOrdersController;
use App\Http\Controllers\Admin\AdminBannersController;
use App\Http\Controllers\Admin\PremiumPlanController;
use App\Http\Controllers\Admin\AdminFAQController;
use App\Http\Controllers\Admin\AdminCurrencyController;
use App\Http\Controllers\Admin\AdminExchangeRateController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\AdminShippingCenterController;
use App\Http\Controllers\Admin\AdminShippingTemplateController;
use App\Http\Controllers\Admin\Settings\ConstantsController;
use App\Http\Controllers\Admin\Settings\SupplierTypeController;
use App\Http\Controllers\Admin\Settings\UnitsController;
use App\Http\Controllers\Admin\Settings\MaterialsController;
use App\Http\Controllers\Admin\Settings\LanguagesController;
use App\Http\Controllers\Admin\Settings\CountriesController;
use App\Http\Controllers\Admin\Settings\CategoryController;
use App\Http\Controllers\Admin\Settings\LocationController;
use App\Http\Controllers\Admin\Help\AdminHelpController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home page
Route::get('/', [HomeController::class, 'index'])->name('main');
// Local page
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');









Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');



Route::get('/product/{slug}', [ProductController::class, 'show'])
    ->name('product.show');

Route::get('/product/{product}/chat', [MessageController::class, 'productThread'])
    ->middleware(['auth', 'role:buyer'])
    ->name('product.chat');

Route::post('/dashboard/messages/{thread}/send', [MessageController::class, 'sendMessage'])
    ->middleware(['auth', 'role:buyer,manufacturer'])
    ->name('messages.send');

Route::get('/dashboard/messages/{thread}/poll', [MessageController::class, 'pollMessages'])
    ->middleware(['auth', 'role:buyer,manufacturer'])
    ->name('messages.poll');

// Route::post('/dashboard/messages/send', [MessageController::class, 'sendMessage'])
//     ->middleware(['auth', 'role:buyer'])
//     ->name('messages.send.new');

Route::get('/catalog/{category?}', [CatalogController::class, 'index'])->name('catalog.index');

Route::get('/catalog/set-country/{country}', [App\Http\Controllers\CatalogController::class, 'setCountry'])
    ->name('catalog.set_country');

Route::get('/set-country/{code}', [CountryController::class, 'set'])
    ->name('country.set');

Route::get('/set-currency/{currency}', [CurrencyController::class, 'setCurrency'])
    ->name('currency.set');

Route::get('/supplier/{supplier:slug}', [SupplierController::class, 'show'])
    ->name('supplier.show');

Route::get('/suppliers', [SupplierController::class, 'index'])
    ->name('suppliers.index');

Route::prefix('dashboard/manufacturer')->name('manufacturer.')->group(function () {

    Route::delete('/products/{product}', [ProductController::class, 'destroy'])
        ->name('products.destroy');

    Route::get('/products', [ProductController::class, 'index'])
        ->name('products.index');

    Route::get('/add-product', [ProductController::class, 'create'])
        ->name('products.create');

    Route::post('/products', [ProductController::class, 'store'])
        ->name('products.store');

    Route::get('/orders', [ManufacturerOrderController::class, 'index'])
        ->name('orders');

    Route::get('/orders/{id}', [ManufacturerOrderController::class, 'show'])
        ->name('orders.show');


    // Messages
    Route::get('/messages', [MessageController::class, 'index'])->name('messages');



    Route::get('/analytics', function () {
        return view('dashboard.manufacturer.analytics');
    })->name('analytics');

    Route::post('/dashboard/manufacturer/products/{id}/update-stock', [ProductController::class, 'updateStock'])
        ->middleware(['auth', 'role:manufacturer'])->name('products.update-stock');

    Route::get('/company-profile', [ManufacturerController::class, 'companyProfile'])
        ->name('company.profile');

    Route::post('/company-profile', [ManufacturerController::class, 'updateCompany'])
        ->name('company.update');

    Route::get('/premium-seller-plans', [PremiumSellerPlanController::class, 'index'])
        ->name('premium-plans');

    Route::get('/premium-seller-plans/compare', [PremiumSellerPlanController::class, 'compare'])
        ->name('premium-plans.compare');

    Route::post('/premium-seller-plans/subscribe', [PremiumSellerPlanController::class, 'subscribe'])
        ->name('premium-plans.subscribe');

    Route::post(
        '/products/{product}/update-stock',
        [ProductController::class, 'updateStock']
    )->name('products.updateStock');
});



Route::post('/dashboard/manufacturer/products/{product}/update-price-tiers', [ProductPriceController::class, 'updatePriceTiers'])
    ->name('products.update-price-tiers');

Route::middleware(['auth', 'role:manufacturer'])
    ->prefix('manufacturer')
    ->group(function () {
        Route::post(
            '/products/{product}/price-tiers',
            [ProductPriceController::class, 'store']
        );

        Route::get(
            '/products/{product}/edit',
            [ProductController::class, 'edit']
        )->name('products.edit');

        Route::put(
            '/products/{product}',
            [ProductController::class, 'update']
        )->name('products.update');
    });

Route::prefix('dashboard/manufacturer')
    ->name('manufacturer.')
    ->middleware(['auth', 'role:manufacturer'])
    ->group(function () {



    Route::put('orders/{order}/shipments/{orderItemShipment}', [ManufacturerOrderController::class, 'updateShipment'])
    ->name('orders.shipments.update');
    
    Route::delete(
            '/certificates/{certificate}',
            [ManufacturerController::class, 'deleteCertificate']
        )->name('certificates.delete');

    Route::resource('shipping-templates', ShippingTemplateController::class)
            ->except(['show']);
    });

Route::post('/certificates/upload', [ManufacturerController::class, 'uploadCertificate'])
    ->name('manufacturer.certificates.upload');




Route::prefix('dashboard/buyer')
    ->name('buyer.')->middleware(['auth', 'role:buyer'])
    ->group(function () {


        Route::get('/premium-buyer-plans', [PremiumBuyerPlanController::class, 'index'])
        ->name('premium-plans');

        Route::get('/premium-buyer-plans/compare', [PremiumBuyerPlanController::class, 'compare'])
        ->name('premium-plans.compare');

        Route::post('/premium-buyer-plans/subscribe', [PremiumBuyerPlanController::class, 'subscribe'])
        ->name('premium-plans.subscribe');
      
        Route::get('/orders', [BuyerOrderController::class, 'index'])
            ->name('orders');

        Route::get('/orders/{id}', [BuyerOrderController::class, 'show'])->name('orders.show');

        Route::put('orders/{order}/update-address', [OrderController::class, 'updateAddress'])->name('orders.update-address');

        Route::get('/messages/', [MessageController::class, 'threadMessages'])->name('messages');
    });



Route::post('/dashboard/switch-role', [DashboardRoleController::class, 'switch'])
    ->name('dashboard.switch-role');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard/manufacturer', function () {
        return view('dashboard.manufacturer.home');
    })->middleware('role:manufacturer')->name('manufacturer.home');

    Route::get('/dashboard/buyer', function () {
        return view('dashboard.buyer.home');
    })->middleware('role:buyer')->name('buyer.home');
});






//----------------------------



//main supplier
Route::prefix('manufacturer')->middleware(['auth', 'role:manufacturer'])->group(function () {

    Route::get('orders', [ManufacturerOrderController::class, 'index'])->name('manufacturer.orders');
    Route::get('orders/{id}', [ManufacturerOrderController::class, 'show'])->name('manufacturer.orders.show');
    Route::delete('certificate/{id}', [ManufacturerController::class, 'deleteCertificate'])->name('manufacturer.certificate.delete');
    // RFQ list
    Route::get('rfqs', [SupplierRfqController::class, 'index'])->name('manufacturer.rfqs.index');
    // RFQ details
    Route::get('rfqs/{rfq}', [SupplierRfqController::class, 'show'])->name('manufacturer.rfqs.show');
    // Send offer
    Route::post('rfqs/{rfq}/offer', [SupplierRfqController::class, 'storeOffer'])->name('manufacturer.rfqs.offer.store');
    Route::get('rfqs', [SupplierRfqController::class, 'index'])->name('supplier.rfqs.index');
    Route::get('rfqs/{rfq}', [SupplierRfqController::class, 'show'])->name('supplier.rfqs.show');

    Route::get('/locations/regions', [LocationController::class, 'regionsByCountry'])
        ->name('manufacturer.locations.regions');

    Route::get('/locations/locations', [LocationController::class, 'locationsByRegion'])
    ->name('manufacturer.locations.locations');

    Route::post('orders/origin/{item}', [ManufacturerOrderController::class, 'storeOrigin'])
    ->name('manufacturer.orders.origin.store');
});

Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('rfqs/{rfq}', [BuyerRfqController::class, 'show'])->name('admin.rfqs.show');
});

//main buyer
Route::prefix('buyer')->middleware(['auth', 'role:buyer'])->group(function () {

    Route::put('disputes/{dispute}/accept', [OrderDisputeController::class, 'accept'])->name('buyer.disputes.accept');
    // My RFQs
    Route::get('rfqs', [BuyerRfqController::class, 'index'])->name('buyer.rfqs.index');
    // Create RFQ
    Route::get('rfqs/create', [BuyerRfqController::class, 'create'])->name('buyer.rfqs.create');
    Route::post('rfqs', [BuyerRfqController::class, 'store'])->name('buyer.rfqs.store');
    // RFQ details + offers
    Route::get('rfqs/{rfq}', [BuyerRfqController::class, 'show'])->name('buyer.rfqs.show');
    // Choose offer / close RFQ
    Route::post('/rfqs/{rfq}/offers/accept', [BuyerRfqController::class, 'acceptOffer'])->name('buyer.rfqs.accept');
    Route::get('rfqs/{rfq}/edit', [BuyerRfqController::class, 'edit'])->name('buyer.rfqs.edit');
    Route::patch('rfqs/{rfq}', [BuyerRfqController::class, 'update'])->name('buyer.rfqs.update');
    //Projects
    Route::resource('projects', BuyerProjectController::class)->names([
        'index' => 'buyer.projects.index',
        'create' => 'buyer.projects.create',
        'store' => 'buyer.projects.store',
        'show' => 'buyer.projects.show',
        'edit' => 'buyer.projects.edit',
        'update' => 'buyer.projects.update',
        'destroy' => 'buyer.projects.destroy',
    ]);

    Route::post('project-items', [ProjectItemController::class, 'store'])->name('buyer.project-items.store');

    Route::post('custom-orders', [BuyerProjectController::class, 'storeCustomization'])->name('buyer.custom-orders.store');

});



//----------------------------







Route::post('/manufacturer/orders/{order}/update-tracking', [ManufacturerOrderController::class, 'updateTracking'])
    ->name('manufacturer.orders.update-tracking');



Route::post(
    '/manufacturer/orders/{order}/status',
    [ManufacturerOrderController::class, 'updateStatus']
)->name('manufacturer.orders.update-status');

Route::middleware(['auth', 'role:buyer'])->prefix('buyer/cart')->name('buyer.cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{product}', [CartController::class, 'add'])->name('add');
    Route::patch('/update/{cartItem}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{cartItem}', [CartController::class, 'remove'])->name('remove');
});

Route::middleware(['auth', 'role:buyer'])->prefix('buyer/orders')->name('buyer.orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index'); // Список заказов
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout'); // Страница оформления
    Route::post('/store', [OrderController::class, 'store'])->name('store'); // Создание заказа
    Route::get('/{order}', [OrderController::class, 'show'])->name('show'); // Просмотр заказа
    // 🔹 Редактирование заказа
    Route::get('/{id}/edit', [OrderController::class, 'edit'])->name('edit'); // Форма редактирования
    Route::put('/{id}', [OrderController::class, 'update'])->name('update'); // Сохранение изменений
});

Route::middleware(['auth', 'role:buyer'])->group(function () {
    Route::post('/buyer/orders/{order}/cancel', [OrderController::class, 'cancel'])
        ->name('buyer.orders.cancel');

    Route::get('/buyer/orders/{order}/edit-address', [OrderController::class, 'editAddress'])
        ->name('buyer.orders.edit-address');

    Route::get('/buyer/orders/{order}/invoice', [OrderController::class, 'invoice'])
        ->name('buyer.orders.invoice');

    Route::get('/buyer/orders/{order}/track', [OrderController::class, 'track'])
        ->name('buyer.orders.track');

    Route::post('buyer/orders/{order}/confirm-delivery-price', [OrderController::class, 'confirmDeliveryPrice'])
    ->name('buyer.orders.confirm-delivery-price');

    Route::get('/buyer/locations/regions', [LocationController::class, 'regionsByCountry'])
        ->name('buyer.locations.regions');

    Route::get('/buyer/locations/locations', [LocationController::class, 'locationsByRegion'])
    ->name('buyer.locations.locations');
});


// Маршруты для отзывов
Route::prefix('buyer/orders')->middleware(['auth', 'role:buyer'])->group(function () {
    Route::get('{order}/review', [OrderReviewController::class, 'create'])->name('buyer.orders.review');
    Route::post('{order}/review', [OrderReviewController::class, 'store'])->name('buyer.orders.review.store');

    // Маршруты для жалоб / возвратов / споров
    Route::get('{order}/dispute', [OrderDisputeController::class, 'create'])->name('buyer.orders.dispute');
    Route::post('{order}/dispute', [OrderDisputeController::class, 'store'])->name('buyer.orders.dispute.store');

    Route::get('{order}/supplier-review', [SupplierReviewController::class, 'create'])->name('buyer.orders.supplier.review');
    Route::post('{order}/supplier-review', [SupplierReviewController::class, 'store'])->name('buyer.orders.supplier.review.store');
});



Route::prefix('buyer/disputes')
    ->middleware(['auth', 'role:buyer'])
    ->group(function () {

        Route::put('{dispute}/cancel', [OrderDisputeController::class, 'cancel'])
            ->name('buyer.disputes.cancel');

        Route::put('{dispute}/appeal', [OrderDisputeController::class, 'appeal'])->name('buyer.disputes.appeal');

        // Новый маршрут для закрытия спора покупателем
        Route::put('{dispute}/close', [OrderDisputeController::class, 'close'])
            ->name('buyer.disputes.close');
    });

// Маршрут для принятия решения по спору покупателем
Route::prefix('buyer')->middleware(['auth', 'role:buyer'])->group(function () {
    Route::put('disputes/{dispute}/accept', [OrderDisputeController::class, 'accept'])
        ->name('buyer.disputes.accept');
});



// Покупатель отклоняет предложение продавца
Route::prefix('buyer')->middleware(['auth', 'role:buyer'])->group(function () {
    Route::put('disputes/{dispute}/reject', [OrderDisputeController::class, 'reject'])
        ->name('buyer.disputes.reject');
});

Route::prefix('buyer/support')
    ->middleware(['auth', 'role:buyer'])
    ->group(function () {

        Route::get('dispute/{dispute}', [OrderDisputeController::class, 'support'])
            ->name('buyer.support.chat');
    });

// Маршруты для споров продавца
Route::prefix('manufacturer/orders')
    ->middleware(['auth', 'role:manufacturer'])
    ->group(function () {

        Route::put('{order}/dispute/{dispute}', [OrderDisputeController::class, 'update'])
            ->name('manufacturer.orders.dispute.update');
    });



Route::get('/login/google', [SocialLoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/login/google/callback', [SocialLoginController::class, 'handleGoogleCallback']);

Route::get('/login/linkedin', [SocialLoginController::class, 'redirectToLinkedIn'])->name('login.linkedin');
Route::get('/login/linkedin/callback', [SocialLoginController::class, 'handleLinkedInCallback']);

Route::get('/faq', function () {
    return view('pages.faq');
})->name('faq');



// Admin routes

Route::prefix('dashboard/admin')->name('admin.')->middleware(['auth', 'is_admin'])->group(function () {

    Route::get('/', function () {
        return view('dashboard.admin.layout', [
            'title' => 'Admin Dashboard',
            'content' => 'Добро пожаловать в админку! Здесь можно управлять контентом и модерацией товаров.'
        ]);
    })->name('home');

    Route::get('orders/{order}/shipments', [AdminOrdersController::class, 'shipments'])->name('orders.shipments');
    Route::put('orders/{order}/shipments/{orderItemShipment}', [AdminOrdersController::class, 'updateShipment'])
    ->name('orders.shipments.update');
    Route::post('orders/{order}/upload-invoice-delivery', [AdminOrdersController::class, 'uploadInvoiceDelivery'])
        ->name('orders.upload-invoice-delivery');
    Route::post('orders/{order}/calculate-delivery', [AdminOrdersController::class, 'calculateDeliveryPrice'])
    ->name('orders.calculate-delivery');
    

    // Virify & Trusted
    Route::post('sellers/{seller}/verify-trust', [AdminSellersController::class, 'updateVerifyTrust']);
    
    // Products moderation
    Route::resource('products', AdminProductController::class);
    Route::get('/products/{product}', [AdminProductController::class, 'show'])->name('products.show');

    // Moderation actions
    Route::post('products/{product}/approve', [AdminProductController::class, 'approve'])->name('products.approve');
    Route::post('products/{product}/reject', [AdminProductController::class, 'reject'])->name('products.reject');

    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::patch('users/{user}/toggle-block', [AdminUserController::class, 'toggleBlock'])->name('users.toggleBlock');

    Route::get('sellers', [AdminSellersController::class, 'index'])->name('sellers.index');
    Route::get('sellers/{seller}/show', [AdminSellersController::class, 'show'])->name('sellers.show');
    Route::get('sellers/{id}/edit', [AdminSellersController::class, 'edit'])->name('sellers.edit');
    Route::put('sellers/{id}', [AdminSellersController::class, 'update'])->name('sellers.update');
    // Загрузка сертификата
    Route::post('sellers/{seller}/certificates', [AdminSellersController::class, 'uploadCertificate'])->name('sellers.certificates.upload');
    // Удаление сертификата
    Route::delete('sellers/certificates/{certificate}', [AdminSellersController::class, 'deleteCertificate'])->name('sellers.certificates.delete');
    Route::get('sellers/{seller}/certificates/list', [AdminSellersController::class, 'listCertificates']);

    Route::get('messages', [AdminMessageController::class, 'index'])->name('messages');
    Route::post('messages/send', [AdminMessageController::class, 'send'])->name('messages.send');

    Route::get('orders', [AdminOrdersController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrdersController::class, 'show'])->name('orders.show');
    Route::post('orders/disputes/{dispute}/admin-comment', [AdminOrdersController::class, 'addDisputeAdminComment'])->name('orders.disputes.adminComment');
    Route::patch('disputes/{dispute}', [AdminOrdersController::class, 'update'])->name('disputes.update');

    Route::get('banners', [AdminBannersController::class, 'index'])->name('banners.index');
    Route::post('banners', [AdminBannersController::class, 'store'])->name('banners.store');


    Route::get('premium-plans', [PremiumPlanController::class, 'index'])->name('premium-plans.index');
    Route::get('premium-plans/create', [PremiumPlanController::class, 'create'])->name('premium-plans.create');
    Route::post('premium-plans', [PremiumPlanController::class, 'store'])->name('premium-plans.store');
    Route::get('premium-plans/{id}/edit', [PremiumPlanController::class, 'edit'])->name('premium-plans.edit');
    Route::put('premium-plans/{id}', [PremiumPlanController::class, 'update'])->name('premium-plans.update');
    Route::delete('premium-plans/{id}', [PremiumPlanController::class, 'destroy'])->name('premium-plans.destroy');


    Route::resource('faq', AdminFAQController::class);

    //Shipping-center
    Route::resource('shipping-center', AdminShippingCenterController::class);
    Route::get('main-shipping-center', [AdminShippingCenterController::class, 'main'])->name('shipping-center.main');

    Route::resource('currencies', AdminCurrencyController::class)->except(['show']);
    Route::get('exchange-rates', [AdminExchangeRateController::class, 'index'])->name('exchange-rates.index');
    Route::put('exchange-rates/{currency}', [AdminExchangeRateController::class, 'update'])->name('exchange-rates.update');

    //Shipping-templates
    Route::get('shipping-templates', [AdminShippingTemplateController::class, 'index'])->name('shipping-templates.index');
    // Создание
    Route::get('shipping-templates/create', [AdminShippingTemplateController::class, 'create'])->name('shipping-templates.create');
    Route::post('shipping-templates', [AdminShippingTemplateController::class, 'store'])->name('shipping-templates.store');
    // Редактирование
    Route::get('shipping-templates/{shippingTemplate}/edit', [AdminShippingTemplateController::class, 'edit'])->name('shipping-templates.edit');
    Route::put('shipping-templates/{shippingTemplate}', [AdminShippingTemplateController::class, 'update'])->name('shipping-templates.update');
    // Удаление
    Route::delete('shipping-templates/{shippingTemplate}', [AdminShippingTemplateController::class, 'destroy'])->name('shipping-templates.destroy');

    Route::prefix('settings')->name('settings.')->group(function () {

        // Главная страница Settings
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::resource('categories', CategoryController::class);
        Route::resource('countries', CountriesController::class)->except(['show']);
        Route::get('constants', [ConstantsController::class, 'index'])->name('constants');

        Route::resource('languages', LanguagesController::class)->except(['show']);
        Route::get('languages/{language}', [LanguagesController::class, 'show'])->name('languages.show');

        // Supplier type
        Route::resource('supplier-types', SupplierTypeController::class);

        // Materials
        Route::get('materials', [MaterialsController::class, 'index'])->name('materials.index');
        Route::get('materials/create', [MaterialsController::class, 'create'])->name('materials.create');
        Route::post('materials', [MaterialsController::class, 'store'])->name('materials.store');
        Route::get('materials/{material}/edit', [MaterialsController::class, 'edit'])->name('materials.edit');
        Route::put('materials/{material}', [MaterialsController::class, 'update'])->name('materials.update');
        Route::delete('materials/{material}', [MaterialsController::class, 'destroy'])->name('materials.destroy');
        
        //Locations
        Route::get('locations/regions', [LocationController::class, 'regionsByCountry'])->name('locations.regions');
        Route::get('locations/locations', [LocationController::class, 'regionsWithChildren'])->name('locations.locations');
        Route::resource('locations', LocationController::class);
    
        });


    // === Help Center ===
    Route::prefix('help')->name('help.')->group(function () {

        Route::get('/', [AdminHelpController::class, 'index'])->name('index'); // Главная Help

        // Категории
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [AdminHelpController::class, 'categories'])->name('index');
            Route::get('/create', [AdminHelpController::class, 'create'])->name('create');
            Route::post('/', [AdminHelpController::class, 'store'])->name('store');
            Route::get('/{category}/edit', [AdminHelpController::class, 'edit'])->name('edit');
            Route::put('/{category}', [AdminHelpController::class, 'update'])->name('update');
            Route::delete('/{category}', [AdminHelpController::class, 'destroy'])->name('destroy');
        });

        // Статьи
        Route::prefix('articles')->name('articles.')->group(function () {
            Route::get('/', [AdminHelpController::class, 'articles'])->name('index');
            Route::get('/create', [AdminHelpController::class, 'createArticle'])->name('create');
            Route::post('/', [AdminHelpController::class, 'storeArticle'])->name('store');
            Route::get('/{article}/edit', [AdminHelpController::class, 'editArticle'])->name('edit');
            Route::put('/{article}', [AdminHelpController::class, 'updateArticle'])->name('update');
            Route::delete('/{article}', [AdminHelpController::class, 'destroyArticle'])->name('destroy');
        });
    });


    Route::get('messages', [AdminMessageController::class, 'index'])->name('messages');
    Route::post('messages/{thread}/send', [AdminMessageController::class, 'send'])->name('send');
    Route::get('messages/{thread}', [AdminMessageController::class, 'threadMessages'])->name('thread');

    Route::post('messages/{thread}/send', [AdminMessageController::class, 'sendMessage'])
    ->middleware(['auth', 'role:admin'])
    ->name('messages.send');

    Route::get('messages/{thread}/poll', [AdminMessageController::class, 'pollMessages'])
    ->middleware(['auth', 'role:admin'])
    ->name('messages.poll');

    Route::get('messages/{thread}', [AdminMessageController::class, 'threadMessages'])
    ->middleware(['auth', 'role:admin'])
    ->name('messages.thread');




});



// Help Center
Route::prefix('help')->name('help.')->group(function () {

    Route::get('/', [HelpController::class, 'index'])->name('index');
    Route::get('/category/{slug}', [HelpController::class, 'category'])->name('category');
});

require __DIR__ . '/auth.php';
