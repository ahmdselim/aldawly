<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\SliderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//route::group([ 'middleware'=>'guest:api'],function($router)
//{

    Route::post('usersignup',[ AuthController::class,'signup']);
    Route::post('login',[ AuthController::class,'login']);
    Route::get('/sliders', [SliderController::class,'get_all_sliders_api']);
    Route::get('/allCategories', [CategoryController::class,'allCategories']);
    Route::get('/view_products_guest', [ProductController::class,'viewAllProductsGuest']);
    Route::post('send-reset-code', [AuthController::class, 'send_code_reset_password']);
    Route::post('reset-password', [AuthController::class, 'Reset_Password']);
//});


Route::group(['middleware' => ['auth:api', 'active']], function () {

    // user
Route::post('/logout', [AuthController::class,'logout']);
Route::delete('/delete_my_account', [AuthController::class,'delete']);
Route::get('/profile', [AuthController::class,'profile']);
Route::put('/update', [AuthController::class,'update']);
Route::post('/uploadImage', [AuthController::class,'uploadImage']);

// category


Route::get('/categoryproduct', [CategoryController::class,'categoryproduct']);




// product
Route::get('/viewAllProducts', [ProductController::class,'viewAllProducts']); //done
Route::get('/offer_products', [ProductController::class,'offer_product']);
Route::get('/offersproducts', [ProductController::class,'offers']); //done
Route::get('/productDetails/{id}', [productcontroller::class,'productDetails']);//done
Route::post('/likeProduct', [productcontroller::class,'like']);
Route::get('/loveproduct', [productcontroller::class,'loveproduct']);




// cart
Route::post('/addToCart/{product}', [CartController::class,'addToCart']);   //done
Route::get('/Cart/{product}', [CartController::class,'CartProduct']);  //done
Route::get('/allCart', [CartController::class,'allCart']);   //done
Route::delete('/removeCartProduct/{productid}', [CartController::class,'deleteProduct']); //done  #remove product from cart
Route::put('/updateCartItem', [CartController::class,'updateCartItem']);  //done

// remove all cart



// order
Route::post('/placeOrder', [OrderController::class,'placeOrder']);
Route::get('/orders', [OrderController::class,'allorders']);
//Route::get('/all-cancel-orders', [OrderController::class,'allCancelOrders']);
Route::get('/order-details', [OrderController::class, 'oneorder']);
    Route::get('/all-cancel-orders', [\App\Http\Controllers\AdminOrdersController::class, 'getAllCancelOrders'])->name('get.all-cancel-orders');
// shipping address
Route::post('/choosecity', [OrderController::class,'choosecity']);
Route::get('/allcities', [OrderController::class,'allcities']);

Route::post('send-notification', [NotificationController::class, 'sendPushNotification']);
Route::post('setToken', [NotificationController::class, 'setToken'])->name('firebase.token');


// rate
Route::post('/rate/{productid}', [RateController::class,'rate']);
Route::delete('/clear_all_cart', [CartController::class, 'clearAll']);
Route::post('/cancel-order', [OrderController::class, 'cancel_order']);
});


