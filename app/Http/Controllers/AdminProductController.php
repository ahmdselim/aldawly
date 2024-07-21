<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Colors;
use App\Models\product;
use App\Models\ProductImage;
use App\Models\size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AdminProductController extends Controller
{
    public function get_products(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('products')
                ->select(
                    'products.id',
                    'products.has_offer',
                    'products.productName as Name',
                    'products.name_ar as Name_ar',
                    'products.productDescription as Description',
                    'products.description_ar as Description_ar',
                    'products.color',
                    'products.productImage',
                    'products.subcat',
                    'categories.name as CategoryName'
                )
                ->join('categories', 'products.cat_id', '=', 'categories.id')
                ->distinct()
                ->get();
            foreach ($data as $product) {
                $sizes = size::where('product_id', $product->id)->pluck('size')->toArray();
                $product->sizes = implode(', ', $sizes);
            }
            return DataTables::of($data)
                ->addColumn('productImage', function ($row) {
                    return '<img src="' . asset('products/'.$row->productImage ) .'" alt="' . $row->Name . '" width="50" height="50">';
                })
              ->addColumn('edit', function ($row) {
                    return '<button type="button" class="btn btn-info edit-product" data-id="' . $row->id . '">Edit</button>';
                })

                ->addColumn('delete', function ($row) {
                    return '<button type="button" class="btn btn-danger delete-product" data-id="' . $row->id . '">Delete</button>';
                })
                ->addColumn('sizes', function ($row) {
                    return '<button type="button" class="btn btn-info sizes-prices" data-id="' . $row->id . '">Sizes&prices</button>';
                })
                ->addColumn('add_offer', function ($row) {
                    return '<button type="button" class="btn btn-success add-offer" data-id="' . $row->id . '">Add Offer</button>';
                })
                ->addColumn('is_offer', function ($row) {
                    return '<label class="switch">
                          <input type="checkbox" class="toggle-active changeIsOffer" data-id="' . $row->id . '" ' . ($row->has_offer ? 'checked' : '') . '>
                          <span class="slider"></span>
                        </label>';
                })
                ->rawColumns(['edit', 'delete','productImage','sizes','add_offer','is_offer'])
                ->make(true);
        }

        return view('Dashboard.products');
    }

    public function add_product(Request $request)
    {
//        dd($request);
        $validator = Validator::make($request->all(), [
            'productDescription' => 'required',
            'productDescription_ar' => 'required',
            'productName' => 'required',
            'productName_ar' => 'required',
            'images' => 'required',
            'subcat' => 'required',
            'cat_id' => 'required',
            'sizes' => 'required', // Ensure sizes is an array
            'sizes.*' => 'required',
            'sizes.*.colors' => 'required|array|min:1', // Each size must have a colors array with at least one color
            'sizes.*.colors.*' => 'required|string',// Each size in the sizes array is required
            'coach_prices' => 'required|array', // Ensure coach_prices is an array
            'coach_prices.*' => 'required|numeric', // Each coach_price in the array is required and must be numeric
            'store_prices' => 'required|array', // Ensure store_prices is an array
            'store_prices.*' => 'required|numeric', // Each store_price in the array is required and must be numeric
            'player_prices' => 'required|array', // Ensure player_prices is an array
            'player_prices.*' => 'required|numeric', // Each player_price in the array is required and must be numeric
            'colors' => 'required|array', // Ensure colors is an array
            'colors.*' => 'required|string', // Each color within the colors array within each size array is required and must be a string
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
//        dd($request->sizes);
        DB::beginTransaction();

        try {
            $product = product::create([
                'productDescription' => $request->productDescription,
                'description_ar' => $request->productDescription_ar,
                'productName' => $request->productName,
                'name_ar' => $request->productName_ar,
                'subcat' => $request->subcat,
                'cat_id' => $request->cat_id
            ]);

            // Upload image
            if ($request->hasFile('images')) {
                $images = $request->file('images');

                foreach ($images as $image) {
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('products'), $imageName);
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => $imageName,
                    ]);
                }
            }

            $sizes = json_decode($request->sizes, true);

            foreach ($sizes as $index => $size) {
                $sizeModel = size::create([
                    'product_id' => $product->id,
                    'size' => $size['size'],
                    'Coach_price' => $request->coach_prices[$index],
                    'Store_price' => $request->store_prices[$index],
                    'Player_price' => $request->player_prices[$index],
                ]);

                // Loop through colors for the current size
                foreach ($size['colors'] as $color) {
                    Colors::create([
                        'size_id' => $sizeModel->id,
                        'color_name' => $color,
                    ]);
                }
            }




            DB::commit();
            return response()->json(['success' => 'Product added successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function getSizesAndPrices($productId)
    {
        $product = product::find($productId);
        if (!$product) {
            return response()->json(['success' => false, 'error' => 'Product not found']);
        }

        $sizesAndPrices = size::where('product_id', $productId)
            ->with('colors')
            ->get();
        return response()->json(['success' => true, 'data' => $sizesAndPrices]);
    }
    public function update_product(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'productDescriptionu' => 'required',
            'productDescription_aru' => 'required',
            'productName_aru' => 'required',
            'productNameu' => 'required',
            'sizes' => 'required',
            'subcatu' => 'required',
            'cat_idu' => 'required',
            'sizesu' => 'required|array', // Ensure sizesu is an array
            'sizesu.*' => 'required', // Each size in the sizesu array is required
            'coach_pricesu' => 'required|array', // Ensure coach_pricesu is an array
            'coach_pricesu.*' => 'required|numeric', // Each coach_price in the array is required and must be numeric
            'store_pricesu' => 'required|array', // Ensure store_pricesu is an array
            'store_pricesu.*' => 'required|numeric', // Each store_price in the array is required and must be numeric
            'player_pricesu' => 'required|array', // Ensure player_pricesu is an array
            'player_pricesu.*' => 'required|numeric', // Each player_price in the array is required and must be numeric
        ]);

        // Check for validation failure
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        DB::beginTransaction();
        try {
            // Fetch the product by ID
            $product = product::findOrFail($request->product_idu);

            // Update the product fields
            $product->update([
                'productDescription' => $request->productDescriptionu,
                'description_ar' => $request->productDescription_aru,
                'productName' => $request->productNameu,
                'name_ar' => $request->productName_aru,
                'color' => $request->coloru,
                'subcat' => $request->subcatu,
                'cat_id' => $request->cat_idu,
            ]);

            if ($request->hasFile('imageu')) {

                $oldImages = $product->images()->pluck('image')->toArray();
                foreach ($oldImages as $oldImage) {
                    $filePath = public_path('products/' . $oldImage);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
                $product->images()->delete();

                foreach ($request->file('imageu') as $imageu) {
                    $store_imageName = time() . '_' . $imageu->getClientOriginalName();
                    $imageu->move(public_path('products'), $store_imageName);
//                    dd($store_imageName);
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => $store_imageName,
                    ]);
                }
            }

            $sizes = json_decode($request->sizes, true);
            foreach ($sizes as $index => $sizeData) {
                // Check if size_id exists and is not null
                if (isset($sizeData['size_id']) && !is_null($sizeData['size_id'])) {
                    $size = Size::find($sizeData['size_id']);
                    if ($size) {
                        // Update the existing size
                        $size->update([
                            'size' => $sizeData['size'],
                            'Coach_price' => $sizeData['coach_price'],
                            'Store_price' => $sizeData['store_price'],
                            'Player_price' => $sizeData['player_price'],
                        ]);

                        $existingColors = $size->colors()->pluck('color_name')->toArray();
                        $newColors = $sizeData['colors'];

                        $colorsToAdd = array_diff($newColors, $existingColors);
                        $colorsToRemove = array_diff($existingColors, $newColors);

                        // Add new colors
                        foreach ($colorsToAdd as $colorName) {
                            Colors::create([
                                'size_id' => $size->id,
                                'color_name' => $colorName,
                            ]);
                        }

                        // Remove colors that are no longer associated with the size
                        Colors::where('size_id', $size->id)
                            ->whereIn('color_name', $colorsToRemove)
                            ->delete();
                    }
                } else {
                    // Create a new size
                    $size = Size::create([
                        'product_id' => $product->id,
                        'size' => $sizeData['size'],
                        'Coach_price' => $sizeData['coach_price'],
                        'Store_price' => $sizeData['store_price'],
                        'Player_price' => $sizeData['player_price'],
                    ]);

                    // Add new colors for the new size
                    foreach ($sizeData['colors'] as $colorName) {
                        Colors::create([
                            'size_id' => $size->id,
                            'color_name' => $colorName,
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => 'Product updated successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function getProductDetails($id)
    {
        $product = product::findOrFail($id);
        return response()->json($product);
    }

    public function getSizesWithPrices($id)
    {
        // Logic to fetch sizes with prices for the specified product ID
        $sizesWithPrices = size::with('colors')->where('product_id', $id)->get(); // Assuming Size model and relationship
        // Return the sizes with prices as JSON

        return response()->json($sizesWithPrices);
    }

    public function updatePrices(Request $request, $productId)
    {
        foreach ($request->sizes_prices as $key => $size) {
            // Find the size by ID (assuming you have an ID for each size)
            $sizeModel = size::where('product_id', $request->product_id)->findOrFail($request->size_ids[$key]);


            // Update the prices and save the old prices
            if ($sizeModel) {

                $sizeModel->update(['old_Coach_price' => $sizeModel->Coach_price]);


                $sizeModel->update(['old_Store_price' => $sizeModel->Store_price]);


                $sizeModel->update(['old_Player_price' => $sizeModel->Player_price]);


                $sizeModel->update([
                    'Coach_price' => $request->sizes_prices[$key]['Coach_price'],
                    'Store_price' => $request->sizes_prices[$key]['Store_price'],
                    'Player_price' => $request->sizes_prices[$key]['Player_price'],
                ]);
            } else {
                return response()->json(['error' => 'error'], 500);
            }
        }
        return response()->json(['success' => 'Prices updated successfully']);
    }

    public function updateOfferStatus(Request $request, $id)
    {
        $isActive = $request->input('is_active');
        try {
            $product = product::find($id);
            if ($isActive === 'false') {
                // Iterate through each size of the product
                foreach ($product->size as $size) {
                    // Check if the product has an offer
                    if ($product->has_offer === '1') {
                        // Store current prices in variables
                        $oldCoachPrice = $size->old_Coach_price;
                        $oldStorePrice = $size->old_Store_price;
                        $oldPlayerPrice = $size->old_Player_price;

                        // Update current prices with old prices
                        $size->update([
                            'Coach_price' => $oldCoachPrice,
                            'Store_price' => $oldStorePrice,
                            'Player_price' => $oldPlayerPrice,

                            'old_Coach_price' => null,
                            'old_Store_price' => null,
                            'old_Player_price' => null,
                        ]);
                    }
                }
                $product->update(['has_offer' => '0']);
            } else {
                $product->update(['has_offer' => '1']);
            }

            return response()->json(['success' => 'Status updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function delete_product(Request $request)
    {
        // Retrieve the product ID from the request
        $productId = $request->input('product_id');

        // Perform the deletion
        $deleted = DB::table('products')->where('id', $productId)->delete();

        if ($deleted) {
            // If deletion is successful, return success response
            return response()->json(['success' => 'Product deleted successfully']);
        } else {
            // If deletion fails, return error response
            return response()->json(['error' => 'Failed to delete product']);
        }
    }
    public function updateOutOfStock(Request $request, $colorId)
    {
        try {
            $color = Colors::findOrFail($colorId);

            $color->update(['out_of_stock' => $request->out_of_stock]);

            return response()->json(['message' => 'Color out_of_stock status updated successfully'], 200);
        } catch (\Exception $e) {
            // Handle any errors that occur during the update process
            return response()->json(['error' => 'Failed to update color out_of_stock status'], 500);
        }
    }
}
