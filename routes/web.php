<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminOrdersController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GovernmentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SliderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/welcome', function () {
    return view('welcome');
});

    Route::get('/', function () {
        return view('Auth.marketLogin');
    })->name('login.page');
    Route::post('login', [AuthController::class, 'admin_login'])->name('market.login');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('Dashboard.index');
    });
    Route::get('/users', function () {
        return view('Dashboard.users');
    });
    Route::get('/categores', function () {
        return view('Dashboard.categories');
    });
    Route::get('/slidrs', function () {
        return view('Dashboard.sliders');
    });
    Route::get('/orders', function () {
        return view('Dashboard.orders');
    });
    Route::get('/all-cancel-orders', function () {
        return view('Dashboard.cancelOrders');
    });
    Route::get('/all-delivered-orders', function () {
        return view('Dashboard.deliveredOrders');
    });
    Route::get('prodcts',function (){
       return view('Dashboard.products') ;
    });
    Route::get('shipping',function (){
       return view('Dashboard.shipping') ;
    });
    Route::get('all-users', [AdminController::class, 'get_users'])->name('get.users');
    Route::post('/update-user-status', [AdminController::class,'updateUserStatus'])->name('update.user.status');
    Route::post('/delete-user', [AdminController::class,'deleteUser'])->name('delete.user');
    Route::post('/get-user-type', [AdminController::class,'getUserType'])->name('get.user.type');
    Route::get('/get-categories',[CategoryController::class,'get_categories'] )->name('get.categories');
    Route::post('/delete-category', [CategoryController::class,'deleteCategory'])->name('delete.category');
    Route::post('/get-category-details', [CategoryController::class,'getCategoryDetails'])->name('get.category.details');
    Route::post('/update-category', [CategoryController::class,'updateCategory'])->name('update.category');
    Route::post('/add-category', [CategoryController::class,'addCategory'])->name('add.category');
    Route::get('logout', [AuthController::class, 'admin_logout'])->name('market.logout');
    Route::get('/get-orders',[AdminOrdersController::class,'get_orders'] )->name('get.orders');
    Route::get('/get-cancel_orders',[AdminOrdersController::class,'get_cancel_orders'] )->name('get.cancel_orders');
    Route::get('/get-delivered_orders',[AdminOrdersController::class,'get_delivered_orders'] )->name('get.delivered_orders');
    Route::post('/update-order-status', [AdminOrdersController::class, 'updateOrderStatus'])->name('update.order.status');
    Route::get('/order/details/{orderId}', [AdminOrdersController::class, 'getOrderDetails']);
    Route::get('/get-products', '\App\Http\Controllers\AdminProductController@get_products')->name('get.products');
    Route::post('/add-product', [AdminProductController::class, 'add_product'])->name('add.product');
    Route::post('/delete-product', '\App\Http\Controllers\AdminProductController@delete_product')->name('delete.product');
    Route::post('/add/government', [GovernmentController::class, 'add']);
    Route::post('/update/government', [GovernmentController::class, 'update']);
    Route::delete('/government/delete/{id}', [GovernmentController::class, 'delete']);
    Route::get('/government/show', [GovernmentController::class, 'show'])->name('government.show');
    Route::get('/government/{id}', [GovernmentController::class, 'getGovernmentById'])->name('government.getById');
    Route::get('/get-sizes-prices/{productId}', '\App\Http\Controllers\AdminProductController@getSizesAndPrices')->name('get.sizes.prices');
    Route::post('/update-product', '\App\Http\Controllers\AdminProductController@update_product')->name('update.product');
    Route::get('/get_product_details/{id}', '\App\Http\Controllers\AdminProductController@getProductDetails'); // Route to fetch product details by ID
    Route::get('/get_sizes_with_prices/{id}', '\App\Http\Controllers\AdminProductController@getSizesWithPrices'); // Route to fetch sizes with prices for a product
    Route::post('/update_prices/{productId}', '\App\Http\Controllers\AdminProductController@updatePrices');
    Route::post('/update-offer-status/{id}', '\App\Http\Controllers\AdminProductController@updateOfferStatus')->name('update.offer.status');
    Route::get('/get_sliders', [SliderController::class, 'get_sliders'])->name('sliders.get');
    Route::post('/delete-slider', [SliderController::class, 'deleteSlider'])->name('sliders.delete');
    Route::post('/get-slider-details', [SliderController::class, 'getSliderDetails'])->name('sliders.details');
    Route::post('/update-slider', [SliderController::class, 'updateSlider'])->name('sliders.update');
    Route::post('/add-slider', [SliderController::class, 'addSlider'])->name('sliders.add');
    Route::put('/update_color_out_of_stock/{colorId}', '\App\Http\Controllers\AdminProductController@updateOutOfStock')->name('colors.updateOutOfStock');

    Route::patch('/fcm-token', [HomeController::class, 'updateToken'])->name('fcmToken');
    Route::post('/send-notification',[HomeController::class,'notification'])->name('notification');
});

