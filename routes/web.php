<?php

use App\Http\Controllers\ProfileController;

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\SocialLoginController;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\ManufacturerOrderController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\BuyerCartController;
use App\Http\Controllers\DashboardRoleController;
use App\Http\Controllers\BuyerOrderController;
use App\Http\Controllers\SupplierCompanyController;
use App\Http\Controllers\BuyerCompanyController;
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
use App\Http\Controllers\CategorySelectorController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CompanySwitchController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\PublicCollectionController;


use App\Http\Controllers\Supplier\SupplierRfqController;

use App\Http\Controllers\Buyer\BuyerRfqController;
use App\Http\Controllers\Buyer\RfqRequirementController;
use App\Http\Controllers\Buyer\RfqParticipantController;
use App\Http\Controllers\Buyer\RfqAuditController;
use App\Http\Controllers\Buyer\RfqVisibilityController;

use App\Http\Controllers\Project\Buyer\BuyerProjectController;
use App\Http\Controllers\Project\Supplier\SupplierProjectController;

use App\Http\Controllers\Rfq\RfqController;
use App\Http\Controllers\Rfq\RfqOfferController;




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
use App\Http\Controllers\Admin\Settings\BusinessTypeController;
use App\Http\Controllers\Admin\Settings\UnitsController;
use App\Http\Controllers\Admin\Settings\MaterialsController;
use App\Http\Controllers\Admin\Settings\LanguagesController;
use App\Http\Controllers\Admin\Settings\CountriesController;
use App\Http\Controllers\Admin\Settings\CategoryController;
use App\Http\Controllers\Admin\Settings\LocationController;
use App\Http\Controllers\Admin\Settings\AttributeOptionController;
use App\Http\Controllers\Admin\Settings\AttributeController;
use App\Http\Controllers\Admin\Settings\ManufacturingCapabilityController;
use App\Http\Controllers\Admin\Help\AdminHelpController;
use App\Http\Controllers\Admin\AdminMessengerController;
use App\Http\Controllers\Admin\ProductCollectionController;
use App\Http\Controllers\Admin\Content\PageController;

use App\Http\Controllers\SupportRequestController;

use App\Http\Controllers\ConversationController;

use App\Http\Controllers\Api\UserTimezoneController;

use App\Http\Controllers\SupplierMessengerController;
use App\Http\Controllers\BuyerMessengerController;

use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactDrawerController;

use App\Models\Supplier;

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
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');
Route::get('/dashboard', DashboardController::class)->middleware(['auth'])->name('dashboard.home');

Route::middleware('auth')->group(function () {
    Route::get('/company/switcher', [CompanySwitchController::class, 'index'])->name('company.switcher');
    Route::post('/company/switch', [CompanySwitchController::class, 'switch'])->name('company.switch');
});

Route::get('/rfqs/{rfq}', [RfqController::class, 'show'])->name('rfqs.workspace');

Route::get('/collections', [PublicCollectionController::class, 'index'])->name('collections.index');
Route::get('/collections/{collection:slug}', [PublicCollectionController::class, 'show'])->name('collections.show');



//CONTACTS
Route::prefix('contacts')->name('contacts.')->group(function () {
        Route::post('/', [ContactController::class, 'store'])->name('store');
        Route::put('/{contact}', [ContactController::class, 'update'])->name('update');
        Route::delete('/{contact}', [ContactController::class, 'destroy'])->name('destroy');
        Route::get('/drawer', [ContactDrawerController::class, 'index'])->name('drawer');

    });

Route::post('/conversations/open', [ConversationController::class, 'open'])->name('conversations.open');
Route::post('/conversations/message', [ConversationController::class, 'message'])->name('conversations.message');
Route::get('/conversations/{conversation}/messages', [ConversationController::class, 'messages'])->name('conversations.messages');

Route::prefix('dashboard/supplier')->name('supplier.')->group(function () {

    Route::get('/messenger', [SupplierMessengerController::class, 'index'])->name('messenger.index');
    Route::get('/messenger/conversations', [SupplierMessengerController::class, 'conversations'])->name('messenger.conversations');
    Route::get('/messenger/conversations/{conversation}', [SupplierMessengerController::class, 'show'])->name('messenger.show');
    Route::post('/messenger/conversations/{conversation}/read', [SupplierMessengerController::class, 'markAsRead'])->name('messenger.read');
    Route::post('/messenger/conversations/{conversation}/support', [SupplierMessengerController::class, 'requestSupport'])->name('messenger.support');
    Route::get('/messenger/conversations/{conversation}/messages/new', [SupplierMessengerController::class, 'newMessages']);
});

//main supplier
Route::middleware('auth')->prefix('supplier')->group(function () {

    Route::get('orders', [ManufacturerOrderController::class, 'index'])->name('supplier.orders');
    Route::get('orders/{id}', [ManufacturerOrderController::class, 'show'])->name('supplier.orders.show');
    

    Route::get('/locations/regions', [LocationController::class, 'regionsByCountry'])->name('supplier.locations.regions');
    Route::get('/locations/locations', [LocationController::class, 'locationsByRegion'])->name('supplier.locations.locations');
    Route::post('orders/origin/{item}', [ManufacturerOrderController::class, 'storeOrigin'])->name('manufacturer.orders.origin.store');
    Route::post('orders/{order}/status', [ManufacturerOrderController::class, 'updateStatus'])->name('supplier.orders.update-status');
    
    Route::post('/products/{product}/price-tiers', [ProductPriceController::class, 'store']);

    // =========================
    // RFQ FEED
    // =========================

    Route::get('/rfq', [SupplierRfqController::class, 'index'])
        ->name('supplier.rfqs.index');

    // =========================
    // WORKSPACE (READ ONLY)
    // =========================

    Route::get('/rfq/{rfq}', [SupplierRfqController::class, 'show'])
        ->name('supplier.rfq.show');


    Route::put('/rfq/{rfq}/shipping-dimensions', [SupplierRfqController::class, 'updateShippingDimensions'])->name('rfq.shipping-dimensions.update');




    // =========================
    // OFFERS
    // =========================

    Route::prefix('/rfq/{rfq}/offers')->group(function () {


        Route::delete('/versions/{version}', [RfqOfferController::class, 'deleteDraftVersion'])->name('supplier.rfq.offers.versions.delete');




        /*
        |--------------------------------------------------------------------------
        | SUBMIT OFFER VERSION
        |--------------------------------------------------------------------------
        */

        Route::post('/versions/{version}/submit', [RfqOfferController::class, 'submitOfferVersion'])->name('supplier.rfq.offers.versions.submit');


        Route::post('/', [RfqOfferController::class, 'store'])
            ->name('supplier.rfq.offers.store');

        Route::get('/{offer}', [RfqOfferController::class, 'show'])
            ->name('supplier.rfq.offers.show');
    });

    Route::post('/offers/{offer}/reject', [RfqOfferController::class, 'reject'])
        ->name('supplier.offers.reject');

    
    

});

//SUPPLIER WAREHOUSE PROJECTS
Route::middleware('auth')->prefix('dashboard/supplier')->name('supplier.')->group(function () {

    Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
    Route::get('/warehouses/create', [WarehouseController::class, 'create'])->name('warehouses.create');
    Route::post('/warehouses', [WarehouseController::class, 'store'])->name('warehouses.store');
    Route::get('/warehouses/{warehouse}/edit', [WarehouseController::class, 'edit'])->name('warehouses.edit');
    Route::put('/warehouses/{warehouse}', [WarehouseController::class, 'update'])->name('warehouses.update');
    Route::delete('/warehouses/{warehouse}', [WarehouseController::class, 'destroy'])->name('warehouses.destroy');

    /*
    |--------------------------------------------------------------------------
    | PROJECTS
    |--------------------------------------------------------------------------
    */
    Route::get('/projects', [SupplierProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [SupplierProjectController::class, 'show'])->name('projects.show');
    Route::get('/projects/rfq/{rfq}', [SupplierProjectController::class, 'requirements'])->name('projects.rfq.requirements');
    

});

Route::get('/locations/search', [LocationController::class, 'search']);
Route::post('/warehouses/attach-location', [WarehouseController::class, 'attachLocation'])->name('supplier.warehouses.attach-location');

Route::post('/products/{product}/attach-attributes', [ProductController::class, 'attachAttributes'])->name('products.attach-attributes');
Route::delete(
    'products/{product}/attributes/{attribute}',
    [ProductController::class, 'deleteAttribute']
)->name('products.delete-attribute');

Route::post('/rfq/{rfq}/custom-attribute', [RfqRequirementController::class, 'storeCustomAttribute'])->name('rfqs.custom-attributes.store');
Route::post('/rfqs/{rfq}/custom-attributes/attach', [RfqRequirementController::class, 'attach'])->name('rfqs.custom-attributes.attach');
Route::delete('/rfqs/{rfq}/custom-attributes/{attribute}', [RfqRequirementController::class, 'dettach'])->name('rfqs.custom-attributes.dettach');
Route::post('/rfqs/{rfq}/custom-attributes/bulk-archive', [RfqRequirementController::class, 'bulkArchive'])->name('rfqs.custom-attributes.bulk-archive');
Route::post('/custom-attributes', [ProductController::class, 'storeCustomAttribute'])->name('custom-attributes.store');
Route::post('/dashboard/supplier/rfqs/{rfq}/offer/create-revision', [RfqOfferController::class, 'createRevision'])->name('supplier.rfq.offer.create-revision');

Route::prefix('dashboard/buyer')->name('buyer.')->group(function () {

        // =========================
        // RFQ CORE
        // =========================
        Route::get('/rfqs', [BuyerRfqController::class, 'index'])->name('rfqs.index');
        Route::get('/rfqs/create', [BuyerRfqController::class, 'create'])->name('rfqs.create');
        Route::post('/rfqs', [BuyerRfqController::class, 'store'])->name('rfqs.store');
        Route::post('/rfqs/customization', [BuyerRfqController::class, 'storeCustomization'])->name('rfqs.customization.store');
        Route::get('/rfqs/{rfq}/edit', [BuyerRfqController::class, 'edit'])->name('rfqs.edit');
        Route::put('/rfqs/{rfq}', [BuyerRfqController::class, 'update'])->name('rfqs.update');

        Route::post('/rfqs/{rfq}/publish', [RfqController::class, 'publish'])->name('rfqs.publish');
        Route::post('/rfqs/{rfq}/close', [RfqController::class, 'close'])->name('rfqs.close');


        // =========================
        // WORKSPACE / DRAWER UPDATE
        // =========================
        Route::patch('/rfqs/{rfq}/field', [BuyerRfqController::class, 'updateField'])
            ->name('rfqs.update.field');

        // =========================
        // WORKSPACE ENTRY (SHELL)
        // =========================

        Route::post('/rfqs/{rfq}/address', [RfqController::class, 'attachAddress'])->name('rfqs.attach.address');


        // =========================
        // REQUIREMENTS (WORKSPACE TAB)
        // =========================
        Route::prefix('/rfqs/{rfq}/requirements')->group(function () {
            Route::get('/', [RfqRequirementController::class, 'show'])->name('rfqs.requirements.show');
            Route::get('/edit', [RfqRequirementController::class, 'edit'])->name('rfqs.requirements.edit');
            Route::post('/', [RfqRequirementController::class, 'store'])->name('rfqs.requirements.store');
            Route::post('/restore-all', [RfqRequirementController::class, 'restoreAll'])->name('rfqs.requirements.restoreAll');
            
        });

        // =========================
        // PARTICIPANTS
        // =========================
        Route::patch('/rfq/{rfq}/visibility/category', [RfqVisibilityController::class, 'updateCategory'])->name('rfq.visibility.category.update');
        Route::patch('/rfq/{rfq}/visibility', [RfqVisibilityController::class, 'update'])->name('rfq.visibility.update');
        Route::get('/rfqs/{rfq}/participants', [RfqParticipantController::class, 'index'])->name('rfqs.participants.index');
        Route::post('/rfqs/{rfq}/participants/invite', [RfqParticipantController::class, 'invite'])->name('rfqs.participants.invite');
        Route::post('/rfq/{rfq}/participants', [RfqParticipantController::class, 'store'])->name('rfq.participants.store');
        Route::patch('/rfq/{rfq}/participants/{participant}/remove', [RfqParticipantController::class, 'remove'])->name('rfq.participants.remove');

        // =========================
        // OFFERS
        // =========================
        Route::prefix('/rfqs/{rfq}/offers')->group(function () {
            Route::get('/comparison', [RfqOfferController::class, 'comparison'])->name('rfqs.offer-comparison');
            Route::post('/{version}/submit-counter', [RfqOfferController::class, 'submitCounterOfferVersion'])->name('rfqs.counter.submit');
            Route::post('/{offer}/versions/{version}/autosave', [RfqOfferController::class, 'buyerCounterAutosave'])->name('rfqs.counter-offer.autosave');
            Route::get('/{offer}/counter-offer/create', [RfqOfferController::class, 'createCounterOffer'])->name('rfqs.counter-offer.create');
            Route::get('/', [RfqOfferController::class, 'index'])->name('rfqs.offers.index');
            Route::get('/{offer}', [RfqOfferController::class, 'show'])->name('rfqs.offers.show');
            Route::post('/{offer}/versions/{version}/accept', [RfqOfferController::class, 'accept'])->name('rfqs.offers.versions.accept');
            Route::post('/{offer}/reject', [RfqOfferController::class, 'reject'])->name('rfqs.offers.reject');

            Route::delete('/{offer}/counter-offer/{version}', [RfqOfferController::class, 'deleteDraftCounterOfferVersion'])->name('rfqs.counter.delete');
        });

        // =========================
        // AUDIT
        // =========================
        Route::get('/rfqs/{rfq}/audit', [RfqAuditController::class, 'index'])->name('rfqs.audit.index');


        // =========================
        // PROJECT BUYER
        // =========================
        // Список проектов
        Route::get('/projects', [BuyerProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/create', [BuyerProjectController::class, 'create'])->name('projects.create');
        Route::post('/projects', [BuyerProjectController::class, 'store'])->name('projects.store');
        Route::get('/projects/{project}/edit', [BuyerProjectController::class, 'edit'])->name('projects.edit');
        Route::put('/projects/{project}', [BuyerProjectController::class, 'update'])->name('projects.update');

        Route::get('/projects/{project}', [BuyerProjectController::class, 'show'])->name('projects.show');
        Route::get('/projects/{project}/rfqs/{rfq}', [BuyerProjectController::class, 'requirements'])->name('projects.requirements');
        Route::get('/projects/{project}/rfqs/{rfq}/offers', [BuyerProjectController::class, 'offers'])->name('projects.offers');
        Route::get('/projects/{project}/participants', [BuyerProjectController::class, 'participants'])->name('projects.participants');
        Route::delete('/projects/{project}', [BuyerProjectController::class, 'destroy'])->name('projects.destroy');
        Route::patch('/projects/{project}/field', [BuyerProjectController::class, 'updateField'])->name('projects.update.field');
        Route::patch('/projects/{project}/visibility', [BuyerProjectController::class, 'updateVisibility'])->name('projects.visibility.update');
        Route::post('/projects/{project}/participants', [BuyerProjectController::class, 'storeParticipant'])->name('projects.participants.store');
        Route::patch('/projects/{project}/participants/{participant}/remove', [BuyerProjectController::class, 'removeParticipant'])->name('projects.participants.remove');
        Route::patch('/projects/{project}/visibility/categories', [BuyerProjectController::class, 'updateVisibilityCategories'])->name('projects.visibility.category.update');

        // =========================
        // MESSENGER
        // =========================

        Route::prefix('messenger')->name('messenger.')->group(function () {
                Route::get('/', [BuyerMessengerController::class, 'index'])->name('index');
                Route::get('/conversations', [BuyerMessengerController::class, 'conversations'])->name('conversations');
                Route::get('/conversations/{conversation}', [BuyerMessengerController::class, 'show'])->name('show');
                Route::post('/conversations/{conversation}/read', [BuyerMessengerController::class, 'markAsRead'])->name('read');
                Route::post('/conversations/{conversation}/support', [BuyerMessengerController::class, 'requestSupport'])->name('support');
                Route::get('/conversations/{conversation}/messages/new', [BuyerMessengerController::class, 'newMessages']);

            });

    Route::delete('certificate/{id}', [BuyerCompanyController::class, 'deleteCertificate'])->name('certificate.delete');
    Route::get('/profile/show', [BuyerCompanyController::class, 'showCompanyProfile'])->name('profile.show');
    Route::get('/company-profile', [BuyerCompanyController::class, 'companyProfile'])->name('company.profile');

    Route::post('/company-profile', [BuyerCompanyController::class, 'updateCompany'])->name('company.update.legacy');

    Route::post('/company-profile/logo', [BuyerCompanyController::class, 'updateLogo'])->name('company.logo');
    Route::get('/company/drawer/{section}', [BuyerCompanyController::class, 'drawer'])->name('company.drawer');
    Route::post('/company/update/{section}', [BuyerCompanyController::class, 'update'])->name('company.update');

    });


Route::prefix('dashboard/supplier')->name('supplier.')->group(function () {
    Route::prefix('company/team')->name('team.')->group(function () {
        Route::get('/', [TeamController::class, 'index'])->name('index');
        Route::get('/members', [TeamController::class, 'members'])->name('members');
        Route::get('/invite', [TeamController::class, 'invite'])->name('invite');
        Route::post('/invite', [TeamController::class, 'sendInvite'])->name('invite.send');
        Route::get('/roles', [TeamController::class, 'roles'])->name('roles');
    });

    Route::resource('shipping-templates', ShippingTemplateController::class)->except(['show']);
    Route::post('shipping-templates/{template}/attach-warehouse', [ShippingTemplateController::class, 'attachWarehouse'])->name('shipping-templates.attach-warehouse');
    Route::post('shipping-templates/{template}/toggle-active',  [ShippingTemplateController::class, 'toggleActive'])->name('shipping-templates.toggle-active');

    Route::delete('certificate/{id}', [SupplierCompanyController::class, 'deleteCertificate'])->name('certificate.delete');
    Route::get('/profile/show', [SupplierCompanyController::class, 'showCompanyProfile'])->name('profile.show');
    Route::get('/company-profile', [SupplierCompanyController::class, 'companyProfile'])->name('company.profile');

    Route::post('/company-profile', [SupplierCompanyController::class, 'updateCompany'])->name('company.update.legacy');

    Route::post('/company-profile/logo', [SupplierCompanyController::class, 'updateLogo'])->name('company.logo');
    Route::get('/company/drawer/{section}', [SupplierCompanyController::class, 'drawer'])->name('company.drawer');
    Route::post('/company/update/{section}', [SupplierCompanyController::class, 'update'])->name('company.update');

    Route::post('/rfqs/{rfq}/offer/autosave', [RfqOfferController::class, 'autosave'])->name('offer.autosave');

    //            /*
    //     |--------------------------------------------------------------------------
    //     | RFQ SUPPLIER WORKSPACE
    //     |--------------------------------------------------------------------------
    //     */

    //     Route::prefix('rfqs')->name('rfqs.')->group(function () {



    //        Route::post('/{rfq}/offer/autosave', [RfqOfferController::class, 'autosave'])
    //     ->name('offer.autosave');

    //     Route::post(
    //     '/{rfq}/custom-autosave',
    //     [RfqOfferController::class, 'customAutosave']
    // );


    //     });


});

Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');

Route::get('/catalog/{category?}', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/set-country/{country}', [App\Http\Controllers\CatalogController::class, 'setCountry'])->name('catalog.set_country');
Route::get('/set-country/{code}', [CountryController::class, 'set'])->name('country.set');
Route::get('/set-currency/{currency}', [CurrencyController::class, 'setCurrency'])->name('currency.set');
Route::get('/supplier/{supplier:slug}', [SupplierController::class, 'show'])->name('supplier.show');
Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
Route::post('/company/switch', [CompanySwitchController::class, 'switch'])->name('company.switch');

Route::get('/buyer/{buyer:slug}', [BuyerController::class, 'show'])->name('buyer.show');

Route::prefix('dashboard/category-selector')->group(function () {
    Route::get('/root', [CategorySelectorController::class, 'root']);
    Route::get('/children/{parent}', [CategorySelectorController::class, 'children']);
    Route::get('/path/{id}', [CategorySelectorController::class, 'getPath']);
    Route::get('/attributes/{categoryId}', [CategorySelectorController::class, 'attributes']);
});

Route::middleware('auth')->prefix('dashboard/supplier')->name('supplier.')->group(function () {
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    Route::get('/add-product', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products-step1', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit-step/{step}', [ProductController::class, 'edit'])->name('products.edit-step');
    Route::put('/products/{product}/update-step1/{step}', [ProductController::class, 'update'])->name('products.update');
    
    Route::get('/orders', [ManufacturerOrderController::class, 'index'])->name('orders');
    Route::get('/orders/{id}', [ManufacturerOrderController::class, 'show'])->name('orders.show');
    Route::get('/analytics', function () {
        return view('dashboard.manufacturer.analytics');})->name('analytics');

    Route::post('/dashboard/manufacturer/products/{id}/update-stock', [ProductController::class, 'updateStock'])->middleware(['auth', 'role:manufacturer'])->name('products.update-stock');

    // SHOW PAGE
    Route::get('/premium-seller-plans', [PremiumSellerPlanController::class, 'index'])->name('premium-plans');
    Route::get('/premium-seller-plans/compare', [PremiumSellerPlanController::class, 'compare'])->name('premium-plans.compare');
    Route::post('/premium-seller-plans/subscribe', [PremiumSellerPlanController::class, 'subscribe'])->name('premium-plans.subscribe');
    Route::post('/products/{product}/update-stock', [ProductController::class, 'updateStock'])->name('products.updateStock');
});

Route::post('/dashboard/supplier/products/{product}/update-price-tiers', [ProductPriceController::class, 'updatePriceTiers'])->name('products.update-price-tiers');

Route::prefix('dashboard/manufacturer')->name('manufacturer.')->group(function () {

        Route::post('catalog-image', [SupplierCompanyController::class, 'uploadCatalogImage'])->name('catalog.upload');
        Route::put('orders/{order}/shipments/{orderItemShipment}', [ManufacturerOrderController::class, 'updateShipment'])->name('orders.shipments.update');
        Route::delete('/certificates/{certificate}', [SupplierCompanyController::class, 'deleteCertificate'])->name('certificates.delete');
});

Route::post('/certificates/upload', [SupplierCompanyController::class, 'uploadCertificate'])->name('manufacturer.certificates.upload');
Route::post('/factory/photos/upload', [SupplierCompanyController::class, 'uploadFactoryPhotos'])->name('manufacturer.factory.photos.upload');
Route::delete('/factory/photos/{id}', [SupplierCompanyController::class, 'deleteFactoryPhoto'])->name('manufacturer.factory.photos.delete');




Route::prefix('dashboard/buyer')->name('buyer.')->group(function () {

        Route::post('/certificates/upload', [BuyerCompanyController::class, 'uploadCertificate'])->name('certificates.upload');
        Route::post('/factory/photos/upload', [BuyerCompanyController::class, 'uploadFactoryPhotos'])->name('factory.photos.upload');
        Route::delete('/factory/photos/{id}', [BuyerCompanyController::class, 'deleteFactoryPhoto'])->name('factory.photos.delete');
        Route::delete('certificates/{certificate}', [BuyerCompanyController::class, 'deleteCertificate'])->name('certificates.delete');


        Route::get('/premium-buyer-plans', [PremiumBuyerPlanController::class, 'index'])->name('premium-plans');
        Route::get('/premium-buyer-plans/compare', [PremiumBuyerPlanController::class, 'compare'])->name('premium-plans.compare');
        Route::post('/premium-buyer-plans/subscribe', [PremiumBuyerPlanController::class, 'subscribe'])->name('premium-plans.subscribe');
        Route::get('/orders', [BuyerOrderController::class, 'index'])->name('orders');
        Route::get('/orders/{id}', [BuyerOrderController::class, 'show'])->name('orders.show');
        Route::put('orders/{order}/update-address', [OrderController::class, 'updateAddress'])->name('orders.update-address');
        
    });


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


Route::middleware('auth')->group(function () {
    Route::get('/company/switcher', [CompanySwitchController::class, 'index'])->name('company.switcher');
    Route::post('/company/switch', [CompanySwitchController::class, 'switch'])->name('company.switch');
});



//----------------------------








//main buyer
Route::prefix('buyer')->middleware(['auth', 'role:buyer'])->group(function () {
    Route::put('disputes/{dispute}/accept', [OrderDisputeController::class, 'accept'])->name('buyer.disputes.accept');
    
});

Route::post('/manufacturer/orders/{order}/update-tracking', [ManufacturerOrderController::class, 'updateTracking'])->name('manufacturer.orders.update-tracking');

Route::post('/manufacturer/orders/{order}/status', [ManufacturerOrderController::class, 'updateStatus'])->name('manufacturer.orders.update-status');

Route::middleware(['auth', 'role:buyer'])->prefix('buyer/cart')->name('buyer.cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{product}', [CartController::class, 'add'])->name('add');
    Route::post('/add-and-redirect/{product}', [CartController::class, 'addAndRedirect'])->name('add.redirect');
    Route::patch('/update/{cartItem}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{cartItem}', [CartController::class, 'remove'])->name('remove');
});

Route::middleware(['auth', 'role:buyer'])->prefix('buyer/wishlist')->name('buyer.wishlist.')->group(function () {
    Route::get('/', [WishlistController::class, 'index'])->name('index');
    Route::post('/toggle/{product}', [WishlistController::class, 'toggle'])->name('toggle');
    Route::get('/count', [WishlistController::class, 'count'])->name('count');
});



Route::middleware(['auth', 'role:buyer'])->prefix('buyer/orders')->name('buyer.orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index'); // Список заказов
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout'); // Страница оформления Product
    Route::get('/rfq-checkout/{offerVersion}', [OrderController::class, 'rfqCheckout'])->name('rfq-checkout');
    Route::post('/store', [OrderController::class, 'store'])->name('store'); // Создание заказа
    Route::post('/rfq-store', [OrderController::class, 'storeRfqOrder'])->name('rfq-store'); // Создание RFQ заказа
    Route::get('/{order}', [OrderController::class, 'show'])->name('show'); // Просмотр заказа
    // 🔹 Редактирование заказа
    Route::get('/{id}/edit', [OrderController::class, 'edit'])->name('edit'); // Форма редактирования
    Route::put('/{id}', [OrderController::class, 'update'])->name('update'); // Сохранение изменений
});

//Buyer orders
Route::middleware(['auth', 'role:buyer'])->group(function () {
    Route::post('/buyer/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('buyer.orders.cancel');
    Route::get('/buyer/orders/{order}/edit-address', [OrderController::class, 'editAddress'])->name('buyer.orders.edit-address');
    Route::get('/buyer/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('buyer.orders.invoice');
    Route::get('/buyer/orders/{order}/track', [OrderController::class, 'track'])->name('buyer.orders.track');
    Route::post('buyer/orders/{order}/confirm-delivery-price', [OrderController::class, 'confirmDeliveryPrice'])->name('buyer.orders.confirm-delivery-price');
    Route::get('/buyer/locations/regions', [LocationController::class, 'regionsByCountry'])->name('buyer.locations.regions');
    Route::get('/buyer/locations/locations', [LocationController::class, 'locationsByRegion'])->name('buyer.locations.locations');
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



Route::prefix('buyer/disputes')->middleware(['auth', 'role:buyer'])->group(function () {

        Route::put('{dispute}/cancel', [OrderDisputeController::class, 'cancel'])
            ->name('buyer.disputes.cancel');

        Route::put('{dispute}/appeal', [OrderDisputeController::class, 'appeal'])->name('buyer.disputes.appeal');

        // Новый маршрут для закрытия спора покупателем
        Route::put('{dispute}/close', [OrderDisputeController::class, 'close'])->name('buyer.disputes.close');
    });

// Маршрут для принятия решения по спору покупателем
Route::prefix('buyer')->middleware(['auth', 'role:buyer'])->group(function () {
    Route::put('disputes/{dispute}/accept', [OrderDisputeController::class, 'accept'])->name('buyer.disputes.accept');
});



// Покупатель отклоняет предложение продавца
Route::prefix('buyer')->middleware(['auth', 'role:buyer'])->group(function () {
    Route::put('disputes/{dispute}/reject', [OrderDisputeController::class, 'reject'])->name('buyer.disputes.reject');
});

Route::prefix('buyer/support')->middleware(['auth', 'role:buyer'])->group(function () {
        Route::get('dispute/{dispute}', [OrderDisputeController::class, 'support'])
            ->name('buyer.support.chat');
    });

// Маршруты для споров продавца
Route::prefix('manufacturer/orders')->middleware(['auth', 'role:manufacturer'])->group(function () {
        Route::put('{order}/dispute/{dispute}', [OrderDisputeController::class, 'update'])->name('manufacturer.orders.dispute.update');
    });


//LOGIN GOOGLE
Route::get('/login/google', [SocialLoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/login/google/callback', [SocialLoginController::class, 'handleGoogleCallback']);

Route::get('/login/linkedin', [SocialLoginController::class, 'redirectToLinkedIn'])->name('login.linkedin');
Route::get('/login/linkedin/callback', [SocialLoginController::class, 'handleLinkedInCallback']);

Route::get('/faq', function () { return view('pages.faq'); })->name('faq');



// ADMIN ROUTES /////////////////////////////////////////////////////////////
Route::prefix('dashboard/admin')->name('admin.')->group(function () {


   Route::prefix('pages')->name('pages.')->group(function () {
        Route::resource('', PageController::class)->parameters(['' => 'page']);
        Route::post('{page}/publish', [PageController::class, 'publish'])->name('publish');
        Route::post('{page}/unpublish', [PageController::class, 'unpublish'])->name('unpublish');
        Route::post('/upload', [PageController::class, 'upload'])->name('upload');
    });



    Route::prefix('collections')->name('collections.')->group(function () {

        Route::get('/{collection}/search-products', [ProductCollectionController::class, 'searchProducts'])->name('products.search');

        Route::get('/', [ProductCollectionController::class, 'index'])->name('index');
        Route::get('/create', [ProductCollectionController::class, 'create'])->name('create');
        Route::post('/', [ProductCollectionController::class, 'store'])->name('store');

        Route::get('/{collection}', [ProductCollectionController::class, 'show'])->name('show');
        Route::get('/{collection}/edit', [ProductCollectionController::class, 'edit'])->name('edit');
        Route::put('/{collection}', [ProductCollectionController::class, 'update'])->name('update');
        Route::delete('/{collection}', [ProductCollectionController::class, 'destroy'])->name('destroy');

        Route::post('/{collection}/publish', [ProductCollectionController::class, 'publish'])->name('publish');
        Route::post('/{collection}/unpublish', [ProductCollectionController::class, 'unpublish'])->name('unpublish');

        Route::post('/{collection}/products', [ProductCollectionController::class, 'attachProducts'])->name('products.attach');
        Route::delete('/{collection}/products/{product}', [ProductCollectionController::class, 'detachProduct'])->name('products.detach');

        Route::post('/{collection}/reorder', [ProductCollectionController::class, 'reorder'])->name('reorder');

        Route::get('/{collection}/products', [ProductCollectionController::class, 'products'])->name('products');

        

    });

    Route::prefix('messenger')->name('messenger.')->group(function () {

            Route::get('/', [AdminMessengerController::class, 'index'])->name('index');

            Route::get('/all-messages', [AdminMessengerController::class, 'allMessages'])->name('all-messages');
            Route::get('/all-messages/conversations', [AdminMessengerController::class, 'allConversations'])->name('all.conversations');
            Route::get('/all-messages/conversations/{conversation}', [AdminMessengerController::class, 'showAll'])->name('all.show');
            Route::post('/all-messages/conversations/{conversation}/read', [AdminMessengerController::class, 'markAsRead'])->name('read');
            Route::post('/all-messages/conversations/{conversation}/close', [AdminMessengerController::class, 'close'])->name('conversations.close');
            Route::post('/all-messages/conversations/{conversation}/reopen', [AdminMessengerController::class, 'reopen'])->name('conversations.reopen');
            Route::get('/all-messages/conversations/{conversation}/messages/new', [AdminMessengerController::class, 'newMessages']);
            Route::delete('/all-messages/conversations/{conversation}', [AdminMessengerController::class, 'destroyConversation'])->name('conversations.destroy');

            Route::get('/notice-messages', [AdminMessengerController::class, 'noticeMessages'])->name('notice');
            Route::get('/notice-conversations', [AdminMessengerController::class, 'noticeConversations'])->name('notice.conversations');
            Route::get('/notice-conversations/{conversation}', [AdminMessengerController::class, 'showNotice'])->name('notice.show');
            Route::post('/notice-conversations/{conversation}/read', [AdminMessengerController::class, 'markAsRead'])->name('read');
            Route::get('/notice-conversations/{conversation}/messages/new', [AdminMessengerController::class, 'newMessages']);
            Route::delete('/notice-conversations/{conversation}', [AdminMessengerController::class, 'destroyConversation'])->name('conversations.destroy');
            Route::post('/notices', [AdminMessengerController::class, 'createNotice'])->name('notices.store');

            Route::get('/conversations', [AdminMessengerController::class, 'conversations'])->name('conversations');
            Route::get('/conversations/{conversation}', [AdminMessengerController::class, 'show'])->name('show');
            Route::get('/conversations/{conversation}/messages/new', [AdminMessengerController::class, 'newMessages']);
            Route::post('/conversations/{conversation}/read', [AdminMessengerController::class, 'markAsRead'])->name('read');
            Route::delete('/conversations/empty', [AdminMessengerController::class, 'deleteEmptyConversations'])->name('delete-empty');
            Route::get('/statistics', [AdminMessengerController::class, 'statistics'])->name('statistics');
            Route::delete('/messages/{message}', [AdminMessengerController::class, 'destroyMessage'])->name('messages.destroy');
            Route::post('/conversations/{conversation}/close', [AdminMessengerController::class, 'close'])->name('conversations.close');
            Route::post('/conversations/{conversation}/reopen', [AdminMessengerController::class, 'reopen'])->name('conversations.reopen');
            Route::delete('/conversations/{conversation}', [AdminMessengerController::class, 'destroyConversation'])->name('conversations.destroy');

        });

    Route::get('/', function () { return view('dashboard.admin.home'); })->name('home');

    // Virify & Trusted
    Route::post('sellers/{seller}/verify-trust', [AdminSellersController::class, 'updateVerifyTrust']);

    // Products moderation
    Route::resource('products', AdminProductController::class);
    Route::prefix('products/{product}')->name('products.')->group(function () {
            Route::post('approve', [AdminProductController::class, 'approve'])->name('approve');
            Route::post('reject', [AdminProductController::class, 'reject'])->name('reject');
        });

    // Users
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminUserController::class, 'index'])->name('index');
        Route::prefix('{user}')->group(function () {

                Route::get('/', [AdminUserController::class, 'show'])->name('show');
                Route::get('edit', [AdminUserController::class, 'edit'])->name('edit');
                Route::put('/', [AdminUserController::class, 'update'])->name('update');
                Route::delete('/', [AdminUserController::class, 'destroy'])->name('destroy');
                Route::patch('toggle-block', [AdminUserController::class, 'toggleBlock'])->name('toggleBlock');
            });
    });

    // Sellers
    Route::prefix('sellers')->name('sellers.')->group(function () {
        Route::get('/', [AdminSellersController::class, 'index'])->name('index');

        Route::prefix('{seller}')->group(function () {
                Route::get('show', [AdminSellersController::class, 'show'])->name('show');
                Route::post('certificates', [AdminSellersController::class, 'uploadCertificate'])->name('certificates.upload');
                Route::get('certificates/list', [AdminSellersController::class, 'listCertificates']);
            });

        Route::get('{id}/edit', [AdminSellersController::class, 'edit'])->name('edit');
        Route::put('{id}', [AdminSellersController::class, 'update'])->name('update');
        Route::delete('certificates/{certificate}', [AdminSellersController::class, 'deleteCertificate'])->name('certificates.delete');
    });

    
    // Orders & Disputes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [AdminOrdersController::class, 'index'])->name('index');

        Route::prefix('{order}')->group(function () {
                Route::get('/', [AdminOrdersController::class, 'show'])->name('show');
                Route::get('shipments', [AdminOrdersController::class, 'shipments'])->name('shipments');
                Route::put('shipments/{orderItemShipment}', [AdminOrdersController::class, 'updateShipment'])->name('shipments.update');
                Route::post('upload-invoice-delivery', [AdminOrdersController::class, 'uploadInvoiceDelivery'])->name('upload-invoice-delivery');
                Route::post('calculate-delivery', [AdminOrdersController::class, 'calculateDeliveryPrice'])->name('calculate-delivery');
            });

        Route::post('disputes/{dispute}/admin-comment', [AdminOrdersController::class, 'addDisputeAdminComment'])->name('disputes.adminComment');
    });
    Route::patch('disputes/{dispute}', [AdminOrdersController::class, 'update'])->name('disputes.update');

    // Banners
    Route::prefix('banners')->name('banners.')->group(function () {
        Route::get('/', [AdminBannersController::class, 'index'])->name('index');
        Route::post('/', [AdminBannersController::class, 'store'])->name('store');
    });

    // Premium Plans
    Route::prefix('premium-plans')->name('premium-plans.')->group(function () {
        Route::get('/', [PremiumPlanController::class, 'index'])->name('index');
        Route::get('create', [PremiumPlanController::class, 'create'])->name('create');
        Route::post('/', [PremiumPlanController::class, 'store'])->name('store');
        Route::get('{id}/edit', [PremiumPlanController::class, 'edit'])->name('edit');
        Route::put('{id}', [PremiumPlanController::class, 'update'])->name('update');
        Route::delete('{id}', [PremiumPlanController::class, 'destroy'])->name('destroy');
    });

    Route::resource('faq', AdminFAQController::class);

    //Shipping-center
    Route::resource('shipping-center', AdminShippingCenterController::class);
    Route::get('main-shipping-center', [AdminShippingCenterController::class, 'main'])->name('shipping-center.main');

    Route::resource('currencies', AdminCurrencyController::class)->except(['show']);
    Route::get('exchange-rates', [AdminExchangeRateController::class, 'index'])->name('exchange-rates.index');
    Route::put('exchange-rates/{currency}', [AdminExchangeRateController::class, 'update'])->name('exchange-rates.update');

    //Shipping-templates
    Route::prefix('shipping-templates')->name('shipping-templates.')->group(function () {
        Route::get('/', [AdminShippingTemplateController::class, 'index'])->name('index');
        Route::get('create', [AdminShippingTemplateController::class, 'create'])->name('create');
        Route::post('/', [AdminShippingTemplateController::class, 'store'])->name('store');
        Route::get('{shippingTemplate}/edit', [AdminShippingTemplateController::class, 'edit'])->name('edit');
        Route::put('{shippingTemplate}', [AdminShippingTemplateController::class, 'update'])->name('update');
        Route::delete('{shippingTemplate}', [AdminShippingTemplateController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('settings')->name('settings.')->group(function () {

        // Главная страница Settings
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::resource('categories', CategoryController::class);
        Route::resource('countries', CountriesController::class)->except(['show']);
        Route::get('constants', [ConstantsController::class, 'index'])->name('constants');

        Route::resource('languages', LanguagesController::class)->except(['show']);
        Route::get('languages/{language}', [LanguagesController::class, 'show'])->name('languages.show');

        // Supplier type
        Route::resource('business-types', BusinessTypeController::class);

        // Materials
        Route::get('materials', [MaterialsController::class, 'index'])->name('materials.index');
        Route::get('materials/create', [MaterialsController::class, 'create'])->name('materials.create');
        Route::post('materials', [MaterialsController::class, 'store'])->name('materials.store');
        Route::get('materials/{material}/edit', [MaterialsController::class, 'edit'])->name('materials.edit');
        Route::put('materials/{material}', [MaterialsController::class, 'update'])->name('materials.update');
        Route::delete('materials/{material}', [MaterialsController::class, 'destroy'])->name('materials.destroy');

        // Manufacturing capabilities
        Route::get('manufacturing-capabilities', [ManufacturingCapabilityController::class, 'index'])->name('manufacturing-capabilities.index');
        Route::get('manufacturing-capabilities/create', [ManufacturingCapabilityController::class, 'create'])->name('manufacturing-capabilities.create');
        Route::post('manufacturing-capabilities', [ManufacturingCapabilityController::class, 'store'])->name('manufacturing-capabilities.store');
        Route::get('manufacturing-capabilities/{capability}/edit', [ManufacturingCapabilityController::class, 'edit'])->name('manufacturing-capabilities.edit');
        Route::put('manufacturing-capabilities/{capability}', [ManufacturingCapabilityController::class, 'update'])->name('manufacturing-capabilities.update');
        Route::delete('manufacturing-capabilities/{capability}', [ManufacturingCapabilityController::class, 'destroy'])->name('manufacturing-capabilities.destroy');

        //Locations
        Route::get('locations/regions', [LocationController::class, 'regionsByCountry'])->name('locations.regions');
        Route::get('locations/locations', [LocationController::class, 'regionsWithChildren'])->name('locations.locations');
        Route::resource('locations', LocationController::class);

        //ATTRIBUTES
        Route::resource('attributes', AttributeController::class);

        //ATTRIBUTES OPTIONS (select / multiselect)
        Route::get('attributes/{attribute}/options', [AttributeOptionController::class, 'index'])->name('attributes.options.index');
        Route::post('attributes/{attribute}/options', [AttributeOptionController::class, 'store'])->name('attributes.options.store');
        Route::put('attributes/{attribute}/options/{option}', [AttributeOptionController::class, 'update'])->name('attributes.options.update');
        Route::delete('attributes/{attribute}/options/{option}', [AttributeOptionController::class, 'destroy'])->name('attributes.options.destroy');
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


        Route::post('/upload-image', [AdminHelpController::class, 'upload'])->name('upload');


    });
    
});






Route::prefix('dashboard')->name('dashboard.')->group(function () {

    Route::get('/companies', [CompanyController::class, 'index'])
        ->name('companies.index');

    Route::get('/companies/create', [CompanyController::class, 'create'])
        ->name('companies.create');

    Route::post('/companies', [CompanyController::class, 'store'])
        ->name('companies.store');

    Route::get('/companies/{id}/edit', [CompanyController::class, 'edit'])
        ->name('companies.edit');

    Route::put('/companies/{id}', [CompanyController::class, 'update'])
        ->name('companies.update');

    Route::delete('/companies/{id}', [CompanyController::class, 'destroy'])
        ->name('companies.destroy');
});

Route::get('/dashboard/users/find-by-email', [UserController::class, 'findByEmail'])->name('dashboard.users.findByEmail');

Route::post('/dashboard/companies/{company}/transfer-owner', [CompanyController::class, 'transferOwner'])->name('dashboard.companies.transfer-owner');

// Help Center
Route::prefix('help')->name('help.')->group(function () {

    Route::get('/', [HelpController::class, 'index'])->name('index');
    Route::get('/category/{slug}', [HelpController::class, 'category'])->name('category');
});

Route::post('/api/user/timezone', [UserTimezoneController::class, 'update'])->middleware('auth');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');


Route::post('/dashboard/support/request',[SupportRequestController::class, 'store'])->name('support.request');





require __DIR__ . '/auth.php';

Route::get('/{page:slug}', [PageController::class, 'show'])->name('pages.show');
