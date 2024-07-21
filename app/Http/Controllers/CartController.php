<?php

namespace App\Http\Controllers;

use App\Models\cart;
use App\Models\cartItem;
use App\Models\category;
use App\Models\product;
use App\Models\size;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    use ResponseTrait;
    //

    // public function addToCart(Request $request, product $product)
    // {
    //     try {
            // $user = auth('api')->user();

            // if (!$user) {
            //     return response()->json(['error' => 'Unauthenticated'], 401);
            // }
            // switch ($user->type) {
            //     case 'player':
            //         $price = $product->Player_price;
            //         break;
            //     case 'coach':
            //         $price = $product->Coach_price;
            //         break;
            //     case 'store':
            //         $price = $product->Store_price;
            //         break;
            //     default:
            //         $price = "you dont have the right permission for viewing the price"; // Provide a default price or handle this case as needed
            //         break;
            // }

    //         $existingCartItem = cartItem::where('user_id', $user->id)
    //             ->where('product_id', $product->id)
    //             ->first();

    //         if ($existingCartItem) {
    //             $newquantity = $existingCartItem->quantity + 1;
    //             $existingCartItem->update([
    //                 'quantity' => $newquantity,
    //                 'totalprice' => $price * $newquantity

    //             ]);
    //         } else {
    //             $cart =  cart::create([
    //                 'user_id' => $user->id,
    //                 'product_id' => $product->id,
    //                 'quantity' => 1,
    //                 'totalprice' => $price
    //             ]);
    //             return response()->json([
    //                 'success' => 'Product added to cart successfully',
    //                 'cart_info' => $cart
    //             ]);
    //         }

    //         return response()->json([
    //             'success' => 'Product added to cart successfully',
    //             'cart_info' => $existingCartItem
    //         ]);
    //     } catch (\Throwable $th) {
    //         return response()->json(['error' => $th->getMessage()]);
    //     }
    // }

    public function addToCart(Request $request, product $product)
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
//                return response()->json(["error" => "Unauthenticated"], 401);
                return $this->returnData('false',[], 400,"Unauthenticated");
            }

            $validator = Validator::make($request->all(), [
                'sizeid' => 'required|exists:sizes,id,product_id,' . $product->id,
                'color'=>'required',
                'quantity'=>'required'
            ]);

            if ($validator->fails()) {
//                return response()->json(['error' => ], 401);
                return $this->returnData('false',[], 400,$validator->errors());
            }

            $cart = Cart::firstOrCreate(['user_id' => $user->id]);

            $existingCartItem = $cart->cartItems()
                ->where('product_id', $product->id)
                ->where('sizeid', $request->sizeid)
                ->where('color',$request->color)
                ->first();

            $price = 0; // Default price if not found
            foreach ($product->size as $size) {
                if ($size->id == $request->sizeid) {
                    switch ($user->type) {
                        case 'player':
                            $price = $size->Player_price;
                            break;
                        case 'coach':
                            $price = $size->Coach_price;
                            break;
                        case 'store':
                            $price = $size->Store_price;
                            break;
                        default:
                            // Handle the case where user type is not recognized
                            break;
                    }
                    break; // Exit the loop since we found the matching size
                }
            }

            if ($existingCartItem) {
                // Update the quantity and total price if the product with the same size exists
                $oldPrice = $existingCartItem->totalprice;
                $newQuantity = $existingCartItem->quantity + 1;
                $newPrice = $oldPrice + $price;

                $existingCartItem->update([
                    'quantity' => $newQuantity,
                    'totalprice' => $newPrice
                ]);
            } else {
                // If the product with the same size doesn't exist, create a new cart item
                $cartItem = new CartItem([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'quantity' => $request->quantity,
                    'totalprice' => $price*$request->quantity,
                    'sizeid' => $request->sizeid,
                    'color'=>$request->color
                ]);

                // Associate the cart item with the cart
                $cart->cartItems()->save($cartItem);
            }

            $finalCart = Cart::with('cartItems')->where('user_id', $user->id)->first();

//            return response()->json([
//                'success' => 'Product added to the cart',
//                'data' => $finalCart
//            ]);
            return $this->returnData('true',$finalCart,200);
        } catch (\Throwable $th) {
            return $this->returnData('false',[], 400,$th->getMessage());
        }
    }

    public function CartProduct(Request $request, $cartItemid)
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
//                return response()->json(["error" => "Unauthenticated"], 401);
                return $this->returnData('false',[], 400,"Unauthenticated");
            }

            $cart = Cart::firstOrCreate(['user_id' => $user->id]);

            if ($cart) {
                $existingCartItem = $cart->cartItems()->where('id', $cartItemid)->first();

                if ($existingCartItem) {
                    $product = Product::with('size')->findOrFail($existingCartItem->product_id);

                    $price = 0; // Default price if not found
                    $selectedSize = null;

                    foreach ($product->size as $size) {
                        if ($size->id == $existingCartItem->sizeid) {
                            $selectedSize = $size;

                            switch ($user->type) {
                                case 'player':
                                    $price = $size->Player_price;
                                    break;
                                case 'coach':
                                    $price = $size->Coach_price;
                                    break;
                                case 'store':
                                    $price = $size->Store_price;
                                    break;
                                default:
                                    // Handle the case where user type is not recognized
                                    break;
                            }

                            break; // Exit the loop since we found the matching size
                        }
                    }

                    if ($selectedSize) {
                        $category = Category::findOrFail($product->cat_id);

                        $responseData = [
                            'name' => $product->productName,
                            'color' => $product->color,
                            'description' => $product->productDescription,
                            'images' => $product->images,
                            'category' => $category,
                            'subcat' => $product->subcat,
                            'price' => $existingCartItem->totalprice,
                            //cart item info
                            'cartitemid'=> $existingCartItem->id,
                            'quantity' => $existingCartItem->quantity,
                            'size_id' => $selectedSize->id,
                            'size_name' => $selectedSize->size,
                            'size_price' => $price,
                        ];

//                        return response()->json(["products" => $responseData]);
                        return $this->returnData('true',$responseData,200);
                    }
                }
            }

//            return response()->json(['error' => "Error while getting this product"]);
            return $this->returnData('false',[], 400,"Error while getting this product");
        } catch (\Throwable $th) {
            return $this->returnData('false',[], 400,$th->getMessage());
        }
    }



    public function allCart()
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return $this->returnData('false', [], 400, "Unauthenticated");
            }

            $cart = cart::where('user_id', $user->id)->first();

            if ($cart) {
                $cartItems = $cart->cartItems()->get();
                $orderItems = [];

                foreach ($cartItems as $item) {
                    $product = product::where('id',$item->product_id)->with('images')->first();
//                        dd($product);
                    if ($product) {
                        $productData = $product->toArray();
                        $productData['color'] = $item->color;
                        unset($item['color']);
                        unset($productData['productImage']);

                        // Merge product data with cart item data
                        $mergedData = array_merge($item->toArray(), ['product' => $productData]);
                        $orderItems[] = $mergedData;
                    }
                }

                return $this->returnData('true', $orderItems, 200);
            } else {
                return $this->returnData('Sorry, your cart is empty', [], 200);
            }
        } catch (\Throwable $th) {
            return $this->returnData('false', [], 400, $th->getMessage());
        }
    }

    public function deleteProduct($product)
    {
        $user = auth('api')->user();

        if (!$user) {
//                return response()->json(["error" => "Unauthenticated"], 401);
            return $this->returnData('false',[], 400,"Unauthenticated");
        }

        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart) {
//            return response()->json(['error' => 'Cart not found'], 404);
            return $this->returnData('false',[], 404,'Cart not found');
        }

        // Assuming the relationship between CartItem and Product is named "cartItem"
        $cartItems = $cart->cartItems()->get();

        foreach ($cartItems as $item) {

            if ($item->id == $product) {

                $orderItems = [];
            if( $item->delete())
            {

                $newcartItems = $cart->cartItems()->get();
                $orderItems = [];

                foreach ($newcartItems as  $item) {


                $productst = product::where('id', $item['product_id'])->get();


                $orderItems[] =
                    [
                        'cartinfo' => $item,
                        'products' => $productst
                    ];
                }

//                return response()->json([
//                    'message'=>'product removed from cart',
//                    'data' => $orderItems
//                ]);
                return $this->returnData('true',$orderItems,200);

               }
            }

        }
    }

    public function updateCartItem(Request $request)
    {
        $user = auth('api')->user();

        if (!$user) {
//                return response()->json(["error" => "Unauthenticated"], 401);
            return $this->returnData('false',[], 400,"Unauthenticated");
        }

        $validator = Validator::make($request->all(), [
            'cart_item_id' => 'required|exists:cart_items,id,user_id,' . $user->id,
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
//            return response()->json(['error' => $validator->errors()], 400);
            return $this->returnData('false',[], 400,$validator->errors());
        }

        // Retrieve the cart and the specified cart item
        $cart = Cart::where('user_id', $user->id)->first();
        $cartItem = $cart->cartItems()->where('id', $request->cart_item_id)->first();

        if (!$cart || !$cartItem) {
//            return response()->json(['error' => 'Cart or cart item not found'], 404);
            return $this->returnData('false',[], 404, 'Cart or cart item not found');
        }

        // Fetch the associated product for the cart item
        $product = Product::with('size')->findOrFail($cartItem->product_id);

        // Calculate the new total price based on the user's type and quantity
        $quantity = $request->input('quantity');
        $price = 0;

        foreach ($product->size as $size) {
            if ($size->id == $cartItem->sizeid) {
                switch ($user->type) {
                    case 'player':
                        $price = $size->Player_price;
                        break;
                    case 'coach':
                        $price = $size->Coach_price;
                        break;
                    case 'store':
                        $price = $size->Store_price;
                        break;
                    default:
                        // Handle the case where user type is not recognized
                        break;
                }
                break;
            }
        }

        $newTotalPrice = $price * $quantity;

        // Update the cart item
        $cartItem->update([
            'quantity' => $quantity,
            'totalprice' => $newTotalPrice,
        ]);

//        return response()->json(['message' => 'Cart item updated successfully']);
        return $this->returnData('Cart item updated successfully',[],200);
    }

    public function clearAll()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return $this->returnData('false', [], 401, "Unauthenticated");
            }

            cart::where('user_id', $user->id)->delete();

            return $this->returnData('true', [], 200, "Cart cleared successfully");
        } catch (\Throwable $th) {
            return $this->returnData('false', [], 500, $th->getMessage());
        }
    }


}
