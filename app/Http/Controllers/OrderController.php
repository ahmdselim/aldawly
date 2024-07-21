<?php

namespace App\Http\Controllers;

use App\Models\cart;
use App\Models\cartItem;
use App\Models\government;
use App\Models\offer;
use App\Models\order;
use App\Models\orderdetails;
use App\Models\shippingaddress;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    use ResponseTrait;
    const VODAFONE_CASH = 'voda_cash';
    protected function hasActiveOffer(Request $request, $productId)
    {

        try {

            $user = auth('api')->user();

            if (!$user) {
                return response()->json(["error" => "Unauthenticated"], 401);
            }
            $validator = Validator::make($request->all(), [
                'promocode' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            $today = now(); // Current date and time

            $offer = Offer::where('product_id', $productId)
                ->where('promocode', $request->promocode)
                ->where('start_date', '<=', $today)
                ->where('exp_date', '>=', $today)
                ->first();

            if ($offer) {
                $cart = cart::where('user_id', $user->id)->first();
                $cartitem = $cart->cartItems()->where('product_id', $offer->product_id)->first();
                $discountPercentage = $offer->percentage;
                // dd($newprice=$cartitem->totalprice);
                $newprice = $cartitem->totalprice = $cartitem->totalprice * ($discountPercentage / 100);
                // dd($newprice);
                return $newprice;
            }
        } catch (\Throwable $th) {
            return response()->json(["error" => $th->getMessage()]);
        }
    }
    public function placeOrder(Request $request)
{
    try {

        $user = auth('api')->user();

        if (!$user) {
//                return response()->json(["error" => "Unauthenticated"], 401);
            return $this->returnData('false',[], 400,"Unauthenticated");
        }
        $validator = Validator::make($request->all(), [
            'City_id' => 'required|exists:governments,id',
            'distnation' => 'required',
            'street' => 'required',
            'number_of_billiding' => 'required',
            'payment_way'=> 'required'
        ]);

        if ($validator->fails()) {
//            return response()->json(['error' => $validator->errors()], 401);
            return $this->returnData('false',[], 400,$validator->errors());
        }
        $cart = Cart::where('user_id', $user->id)->first();
        $cartItems = $cart->cartItems()->get();

        $orderAmount = $cartItems->sum('totalprice');

        if ($request->promocode) {
            $today = now(); // Current date and time

            $offer = Offer::where('promocode', $request->promocode)
                ->where('start_date', '<=', $today)
                ->where('exp_date', '>=', $today)
                ->first();

            if ($offer) {
                // Apply discount based on the offer percentage
                $discountedAmount = $orderAmount * ($offer->percentage / 100);
                $orderAmount -= $discountedAmount;
            } else {
//                return response()->json(["message" => "Sorry, the promo code is invalid or expired"]);
                return $this->returnData('false',[], 400,"Sorry, the promo code is invalid or expired");
            }
        }

        // Create the order
        $order = Order::create([
            'user_id' => $user->id,
            'amount' => $orderAmount,
            'status' => $request->payment_way == self::VODAFONE_CASH ? 'waiting payment' : 'Pending',
            'payment_way'=> $request->payment_way
        ]);



        // Create order details
        $orderDetails = [];
        foreach ($cartItems as $cartItem) {
            $orderDetails[] = [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'price' => $cartItem->totalprice,
                'sizeid' => $cartItem->sizeid,
                'quantity' => $cartItem->quantity,
                'color'=>$cartItem->color
            ];
        }
        OrderDetails::insert($orderDetails);

        // Validate and create shipping address


        $government = Government::find($request->City_id);
        $shipping = ShippingAddress::create([
            'user_id' => $user->id,
            'special_mark' => $request->special_mark,
            'order_id' => $order->id,
            'government_id' => $government->id,
            'price' => $request->payment_way == self::VODAFONE_CASH ? $government->voda_price + $order->amount : $government->cash_price + $order->amount ,
            'distnation' => $request->distnation,
            'street' => $request->street,
            'number_of_billiding' => $request->number_of_billiding,
            'number_of_floor' => $request->number_of_floor,
            'number_of_flat' => $request->number_of_flat
        ]);

        if ($shipping) {

            if($request->payment_way == self::VODAFONE_CASH ){
                $order->update(['amount' => $government->voda_price + $order->amount]);
            }else{
                $order->update(['amount' => $government->cash_price + $order->amount]);
            }
            // Update order amount with government price


            // Optionally, clear the cart after placing the order
            $cart->cartItems()->delete();

//            return response()->json(["message" => "Order placed successfully"]);
            return $this->returnData('Order placed successfully',[],200);
        } else {
//            return response()->json(["message" => "Something went wrong unexpected"]);
            return $this->returnData('false',[], 400,"Something went wrong unexpected");
        }
    } catch (\Throwable $th) {
        return response()->json(["error" => $th->getMessage()]);
    }
}

    public function allorders(Request $request)
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
//                return response()->json(["error" => "Unauthenticated"], 401);
                return $this->returnData('false',[], 400,"Unauthenticated");
            }

            $order = order::where('user_id', $user->id)->with('orderdetails')->get();
            if($request->status){
                $order =$order->where('status',$request->status);
            }
            if ($order->count() > 0) {
//                return response()->json(["orders" => $order]);
                return $this->returnData('true',$order,200);
            } else {
//                return response()->json(["message" => "sorry there is not order yet"]);
                return $this->returnData('true',[], 200,"sorry there is no order yet");
            }
        } catch (\Throwable $th) {
            return response()->json(["error" => $th->getMessage()]);
        }
    }

    public function oneorder(Request $request)
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return $this->returnData('false', [], 400, "Unauthenticated");
            }

            // Fetch the order with details and associated product and size data
            $order = Order::where('user_id', $user->id)
                ->where('id', $request->id)
                ->with(['orderdetails.product', 'orderdetails.size'])
                ->first();

            if ($order) {
                // Apply status filter if provided


                // Structure the response data
                $orderData = [
                    'id' => $order->id,
                    'amount' => $order->amount,
                    'status' => $order->status,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                    'payment_way' => $order->payment_way,
                    'order_details' => $order->orderDetails->map(function ($detail)use ($order) {
                        return [
                            'product_id' => $detail->product_id,
                            'product_name' => $detail->product->productName,
                            'product_description' => $detail->product->productDescription,
                            'price' => $detail->price,
                            'size' => $detail->size->size,
                            'quantity' => $detail->quantity,
                            'color' => $detail->color,
                            'images' => $detail->product->images->map(function ($image) {
                                return $image->image;
                            }),
                        ];
                    })
                ];

                return $this->returnData('true', $orderData, 200);
            } else {
                return $this->returnData('true', [], 200, "Sorry, no order with that id");
            }
        } catch (\Throwable $th) {
            return response()->json(["error" => $th->getMessage()]);
        }
    }

    public function choosecity(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:governments,id',
            ]);

            if ($validator->fails()) {
//                return response()->json(['error' => $validator->errors()], 401);
                return $this->returnData('false',[], 400,$validator->errors());
            }
            $government =    government::where('id', $request->id)->first();
            return response()->json(["data" =>  $government]);
        } catch (\Throwable $th) {
            return response()->json(["error" => $th->getMessage()]);
        }
    }

    public function shipingprice(Request $request, $orderid)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:governments,id',
                'distnation' => 'required',
                'street' => 'required',
                'number_of_billiding' => 'required',

            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            $government =    government::where('id', $request->id)->first();
            $user = auth('api')->user();
            $order = order::where('order_id', $orderid)->where('user_id', $user->id)->first();
            $shipping = shippingaddress::creat([
                'user_id' => $user->id,
                'special_mark' => $request->special_mark,
                'order_id' => $orderid,
                'government_id' => $request->id,
                'price' => $government->price + $order->amount,
                'distnation' => $request->distnation,
                'street' => $request->street,
                'number_of_billiding' => $request->number_of_billiding,
                'number_of_floor' => $request->number_of_floor,
                'number_of_flat' => $request->number_of_flat
            ]);
            if ($shipping) {
                order::where('order_id', $orderid)->where('user_id', $user->id)->update('amount', $government->price + $order->amount);
            }

            return        $shipping;
        } catch (\Throwable $th) {
            return response()->json(["error" => $th->getMessage()]);
        }
    }

    public function allcities()
    {
        try {

            $government =    government::all();
            if ($government != null) {
//                return response()->json(["status" => true, "data" =>  $government]);
                return $this->returnData('true',$government,200);
            }
//            return response()->json(["status" => true, "data" => "no cities available now"]);
            return $this->returnData('false',[], 400,"no cities available now");
        } catch (\Throwable $th) {
            return response()->json(["error" => $th->getMessage()]);
        }
    }


    public function cancel_order(Request $request)
    {
        // Validate the request parameters
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        try {
            $order = order::findOrFail($request->order_id);

            $order->status = 'canceled';
            $order->save();

            return response()->json(['message' => 'Order canceled successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to cancel order'], 500);
        }
    }

    public function allCancelOrders(Request $request)
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
//                return response()->json(["error" => "Unauthenticated"], 401);
                return $this->returnData('false',[], 400,"Unauthenticated");
            }

            $order = order::where('status', 'canceled')->with('orderdetails')->get();

             if($request->status){
                 $order =$order->where('status',$request->status);
             }
            if ($order->count() > 0) {
//                return response()->json(["orders" => $order]);
                return $this->returnData('true',$order,200);
            } else {
//                return response()->json(["message" => "sorry there is not order yet"]);
                return $this->returnData('true',[], 200,"sorry there is no order yet");
            }

//            return view('Dashboard.cancelOrders', compact('orders'));
//            return response()->json(['orders' => $orders], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to cancel order'], 500);
        }
    }
}
