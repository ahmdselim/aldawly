<?php

namespace App\Http\Controllers;

use App\Models\category;
use App\Models\favouritProduct;
use App\Models\product;
use App\Models\rate;
use App\Models\size;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use ResponseTrait;
    public function viewAllProducts(Request $request)
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
//                return response()->json(["error" => "Unauthenticated"], 401);
                return $this->returnData('false',[], 400,"Unauthenticated");
            }

            $query = product::query()->with('size');

            // Search within product name and description
            if ($request->has('search')) {
                $searchTerm = $request->input('search');
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('productName', 'like', "%$searchTerm%")
                        ->orWhere('productDescription', 'like', "%$searchTerm%")
                        ->orWhere('description_ar', 'like', "%$searchTerm%")
                        ->orWhere('name_ar', 'like', "%$searchTerm%");
                });
            }

            // Filter by category
            if ($request->has('cat')) {
                $categoryId = $request->input('cat');
                $query->where('cat_id', $categoryId);
            }

            // Filter by color
            if ($request->has('color')) {
                $color = $request->input('color');
                $query->whereHas('size.colors', function ($q) use ($color) {
                    $q->where('color_name', $color);
                });
            }

            if ($request->has('size')) {
                $size = $request->input('size');
                $query->whereHas('size', function ($q) use ($size) {
                    $q->where('size', $size);
                });
            }

            // Filter by products with offers
            if ($request->has('has_offer')) {
                $hasOffer = $request->input('has_offer');
                $query->where('has_offer', $hasOffer);
            }

            $limit = $request->input('limit', 10);
            $page = $request->input('page', 1);
            $products = $query->paginate($limit, ['*'], 'page', $page);

            $productData = [];

            foreach ($products as $product) {
                $productDetails = [
                    'id'=>$product->id,
                    'name_en' => $product->productName,
                    'name_ar' => $product->name_ar,
                    'description_en' => $product->productDescription,
                    'description_ar' => $product->description_ar,
                    'images' => $product->images,
                    'category' => $product->category, // Assuming you have a 'category' relationship in your Product model
                    'subcat' => $product->subcat,
                    'has_offer' => $product->has_offer,
                    'sizes' => [],
                ];

                foreach ($product->size as $size) {
                    switch ($user->type) {
                        case 'player':
                            $price = $size->Player_price;
                            $old_price=$size->old_Player_price;
                            break;
                        case 'coach':
                            $price = $size->Coach_price;
                            $old_price=$size->old_Coach_price;
                            break;
                        case 'store':
                            $price = $size->Store_price;
                            $old_price=$size->old_Store_price;
                            break;
                        default:
                            $price = "you dont have the right permission for viewing the price";
                            break;
                    }

                    $productDetails['sizes'][] = [
                        'size_id' => $size->id,
                        'size_name' => $size->size,
                        'size_price' => $price,
                        'old_price'=>$old_price,
                        'colors' => $size->colors,
                    ];
                }

                $productData[] = $productDetails;
            }

//            return response()->json(["products" => $productData]);
            return $this->returnData('true',$productData,200);
        } catch (\Throwable $th) {
            return $this->returnData('false',[], 400,$th->getMessage());
        }
    }
    public function viewAllProductsGuest(Request $request)
    {
        try {
            $query = product::query()->with('size');

            // Search within product name and description
            if ($request->has('search')) {
                $searchTerm = $request->input('search');
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('productName', 'like', "%$searchTerm%")
                        ->orWhere('productDescription', 'like', "%$searchTerm%")
                        ->orWhere('description_ar', 'like', "%$searchTerm%")
                        ->orWhere('name_ar', 'like', "%$searchTerm%");
                });
            }

            // Filter by category
            if ($request->has('cat')) {
                $categoryId = $request->input('cat');
                $query->where('cat_id', $categoryId);
            }

            // Filter by color
            if ($request->has('color')) {
                $color = $request->input('color');
                $query->whereHas('size.colors', function ($q) use ($color) {
                    $q->where('color_name', $color);
                });
            }

            if ($request->has('size')) {
                $size = $request->input('size');
                $query->whereHas('size', function ($q) use ($size) {
                    $q->where('size', $size);
                });
            }

            // Filter by products with offers
            if ($request->has('has_offer')) {
                $hasOffer = $request->input('has_offer');
                $query->where('has_offer', $hasOffer);
            }

            $limit = $request->input('limit', 10);
            $page = $request->input('page', 1);
            $products = $query->paginate($limit, ['*'], 'page', $page);

            $productData = [];

            foreach ($products as $product) {
                $productDetails = [
                    'id' => $product->id,
                    'name_en' => $product->productName,
                    'name_ar' => $product->name_ar,
                    'description_en' => $product->productDescription,
                    'description_ar' => $product->description_ar,
                    'images' => $product->images,
                    'category' => $product->category,
                    'subcat' => $product->subcat,
                    'has_offer' => $product->has_offer,
                    'sizes' => [],
                ];

                foreach ($product->size as $size) {
                    $price = $size->Player_price;
                    $old_price = $size->old_Player_price;

                    $productDetails['sizes'][] = [
                        'size_id' => $size->id,
                        'size_name' => $size->size,
                        'size_price' => $price,
                        'old_price' => $old_price,
                        'colors' => $size->colors,
                    ];
                }

                $productData[] = $productDetails;
            }

            return $this->returnData('true',$productData,200);
        } catch (\Throwable $th) {
            return $this->returnData('false',[], 400,$th->getMessage());
        }
    }

    public function offer_product(Request $request)
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
//                return response()->json(["error" => "Unauthenticated"], 401);
                return $this->returnData('false',[], 400,"Unauthenticated");
            }
            $limit = $request->input('limit', 10);
            $page = $request->input('page', 1);
            $productData = [];
            $products = product::where('has_offer','1')->with('size');
            $products =$products-> paginate($limit, ['*'], 'page', $page);

            foreach ($products as $product) {
                $productDetails = [
                    'id'=>$product->id,
                    'name_en' => $product->productName,
                    'name_ar' => $product->name_ar,
                    'description_en' => $product->productDescription,
                    'description_ar' => $product->description_ar,
                    'images' => $product->images,
                    'category' => $product->category, // Assuming you have a 'category' relationship in your Product model
                    'subcat' => $product->subcat,
                    'has_offer' => $product->has_offer,
                    'sizes' => [],
                ];

                foreach ($product->size as $size) {
                    switch ($user->type) {
                        case 'player':
                            $price = $size->Player_price;
                            $old_price=$size->old_Player_price;
                            break;
                        case 'coach':
                            $price = $size->Coach_price;
                            $old_price=$size->old_Coach_price;
                            break;
                        case 'store':
                            $price = $size->Store_price;
                            $old_price=$size->old_Store_price;
                            break;
                        default:
                            $price = "you dont have the right permission for viewing the price";
                            break;
                    }

                    $productDetails['sizes'][] = [
                        'size_id' => $size->id,
                        'size_name' => $size->size,
                        'size_price' => $price,
                        'old_price'=>$old_price,
                        'colors' => $size->colors,
                    ];
                }

                $productData[] = $productDetails;
            }

//            return response()->json(["products" => $productData]);
            return $this->returnData('true',$productData,200);
        } catch (\Throwable $th) {
            return $this->returnData('false',[], 400,$th->getMessage());
        }
    }

    public function productDetails($id)
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
//                return response()->json(["error" => "Unauthenticated"], 401);
                return $this->returnData('false',[], 400,"Unauthenticated");
            }

            $productData = [];
            $product = Product::with('size')->where('id',$id)->first();

            if (!$product) {
//                return response()->json(["error" => "There is no product with the given ID"]);
                return $this->returnData('false',[], 404,"There is no product with the given ID");
            }

            foreach ($product->size as $size) {

                $category = category::findOrFail($product->cat_id)->first();
                $rate = Rate::where('product_id', $product->id)->avg('rate');

                // Determine the price based on user type
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
                        $price = null; // You might want to handle this case differently
                        break;
                }
                $sizearray[] = [
                'size_id' => $size->id,
                'size_name' => $size->size,
                'size_price' => $price,
                    'colors' => $size->colors,
                    ];

            }
            $productData[] = [
                'id'=>$product->id,
                'name_en' => $product->productName,
                'name_ar' => $product->name_ar,
                'description_en' => $product->productDescription,
                'description_ar' => $product->description_ar,
                'images' => $product->images,
                // 'price' => $price,
                'category' => $category,
                'subcat' => $product->subcat,
                'size' => $sizearray,
                'rate' => $rate,
                'has_offer'=>$product->has_offer,
            ];
            return $this->returnData('true',$productData,200);
        } catch (\Throwable $th) {
            return $this->returnData('false',[], 400,$th->getMessage());
        }
    }




    public function prodsearch(Request $request)
    {
        $data = $request->validate(["search" => "required"]);
        $search = $data['search'];

        $product = product::where('productName', 'like', "%$search%")->get();
        if ($product) {
            return response()->json(["product" => $product]);
        } elseif ($data['search'] == null) {

            $allproduct = product::all();
            return response()->json(["product" => $allproduct]);
        }
    }

    public function like(Request $request)
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
//                return response()->json(["error" => "Unauthenticated"], 401);
                return $this->returnData('false',[], 400,"Unauthenticated");
            }
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',

            ]);

            if ($validator->fails()) {
//                return response()->json(['error' => $validator->errors()], 401);
                return $this->returnData('false',[], 400,$validator->errors());
            }

            $favproduct = favouritProduct::FirstOrCreate([
                'product_id' => $request->product_id,
                'user_id' => $user->id
            ]);

            if ($favproduct->wasRecentlyCreated) {

//                return response()->json(['message' => 'you added product to your love products successfully']);
                return $this->returnData('you added product to your love products successfully',[],200);
            } else {
                // dd($favproduct->id);
                $dislikeProduct = favouritProduct::where('id', $favproduct->id)->delete();
//                return response()->json(['message' => 'you removed product from love products']);
                return $this->returnData('you removed product from love products',[],200);
            }
        } catch (\Throwable $th) {
            return $this->returnData('false',[], 400,$th->getMessage());
        }
    }
    public function loveproduct(Request $request)
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return $this->returnData('false',[], 400,"Unauthenticated");
            }
            $limit = $request->input('limit', 10);
            $page = $request->input('page', 1);
            $favproduct = favouritProduct::where('user_id', $user->id);
            $favproduct =$favproduct-> paginate($limit, ['*'], 'page', $page);
            if ($favproduct->count() > 0) {
                $products = [];

                foreach ($favproduct as $value) {
                    $productData = product::where('id', $value->product_id)->with('size')->first();

                    $product = [
                        'id'=>$productData->id,
                        'name_en' => $productData->productName,
                        'name_ar' => $productData->name_ar,
                        'description_en' => $productData->productDescription,
                        'description_ar' => $productData->description_ar,
                        'images' => $productData->images,
                        'category' => [
                            'id' => $productData->cat_id,
                            'name' => $productData->category->name,
                            'created_at' => $productData->category->created_at,
                            'updated_at' => $productData->category->updated_at,
                        ],
                        'subcat' => $productData->subcat,
                        'has_offer' => $productData->has_offer,
                        'sizes' => [],
                    ];

                    foreach ($productData->size as $size) {
                        switch ($user->type) {
                            case 'player':
                                $price = $size->Player_price;
                                $old_price = $size->old_Player_price;
                                break;
                            case 'coach':
                                $price = $size->Coach_price;
                                $old_price = $size->old_Coach_price;
                                break;
                            case 'store':
                                $price = $size->Store_price;
                                $old_price = $size->old_Store_price;
                                break;
                            default:
                                $price = "you dont have the right permission for viewing the price";
                                $old_price = null;
                                break;
                        }

                        $product['sizes'][] = [
                            'size_id' => $size->id,
                            'size_name' => $size->size,
                            'size_price' => $price,
                            'old_price' => $old_price,
                            'colors' => $size->colors,
                        ];
                    }

                    $products[] = $product;
                }

                return $this->returnData('true', $products, 200);
            } else {
                return $this->returnData('No favorite products found', [], 200);
            }
        } catch (\Throwable $th) {
            return $this->returnData('false', [], 400, $th->getMessage());
        }
    }


    public function offers(){
        try {
            $user = auth('api')->user();

            if (!$user) {
//                return response()->json(["error" => "Unauthenticated"], 401);
                return $this->returnData('false',[], 400,"Unauthenticated");
            }

            $productData = [];
            $products = Product::where('has_offer','1')->with('size')->get();

            foreach ($products as $product) {
                $productDetails = [
                    'name' => $product->productName,
                    'color' => $product->color,
                    'description' => $product->productDescription,
                    'images' => $product->images,
                    'category' => $product->category, // Assuming you have a 'category' relationship in your Product model
                    'subcat' => $product->subcat,
                    'has_offer' => $product->has_offer,
                    'sizes' => [],
                ];

                foreach ($product->size as $size) {
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
                            $price = "you dont have the right permission for viewing the price";
                            break;
                    }

                    $productDetails['sizes'][] = [
                        'size_id' => $size->id,
                        'size_name' => $size->size,
                        'size_price' => $price,
                        'colors' => $size->colors,
                    ];
                }

                $productData[] = $productDetails;
            }

            return $this->returnData('true',$productData,200);
        } catch (\Throwable $th) {
            return $this->returnData('false',[], 400,$th->getMessage());
        }
    }
}
