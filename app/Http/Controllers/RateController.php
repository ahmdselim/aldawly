<?php

namespace App\Http\Controllers;

use App\Models\product;
use App\Models\rate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RateController extends Controller
{
    public function rate(Request $request, $product_id)
    {
        try {

            $user = auth('api')->user();

            if (!$user) {
                return response()->json(["error" => "Unauthenticated"], 401);
            }

            $validator = Validator::make($request->all(), [
                'rate' => 'required|integer',

            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            $product = product::where('id', $product_id)->first();
            if ($product != null) {
                $rate = rate::create([
                    'user_id' =>  $user->id,
                    'product_id' => $product->id,
                    'rate' => $request->rate,
                ]);
                if (!$rate) {
                    return response()->json(['error' => 'Cart not found'], 404);
                }
                return response()->json(['message' => $rate]);
            }
            return response()->json(['error' => 'product  not found'], 404);
        } catch (\Throwable $th) {
            return response()->json(["error" => $th->getMessage()]);
        }
    }
}
