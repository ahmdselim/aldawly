<?php

namespace App\Http\Controllers;

use App\Models\category;
use App\Models\product;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    use ResponseTrait;
    public function get_categories(Request $request)
    {
        if ($request->ajax()) {
            $data = category::all();

            return DataTables::of($data)
                ->addColumn('image', function ($row) {
                    // Assuming 'image' is the column name where the image filenames are stored
                    $imagePath = asset( $row->image); // Adjust the path as per your directory structure
                    return '<img src="'.asset('categories/'.$row->image) . '" alt="Category Image" style="width: 50px; height: 50px;">';
                })
                ->addColumn('edit', function ($row) {
                    return '<button type="button" class="btn btn-info edit-category" data-id="' . $row->id . '">Edit</button>';
                })

                ->addColumn('delete', function ($row) {
                    return '<button type="button" class="btn btn-danger delete-category" data-id="' . $row->id . '">Delete</button>';
                })
                ->rawColumns(['edit', 'delete','image'])
                ->make(true);
        }

        return view('Dashboard.categories');
    }
    public function deleteCategory(Request $request)
    {
        try {
            $categoryId = $request->input('category_id');
            category::where('id', $categoryId)->delete();

            return response()->json(['message' => 'Category deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting category']);
        }
    }

    public function getCategoryDetails(Request $request)
    {
        try {
            $categoryId = $request->input('category_id');
            $category = category::find($categoryId);

            return response()->json(['category' => $category]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error getting category details']);
        }
    }

    // Method to update a category
    public function updateCategory(Request $request)
    {
        try {
            $validator = $request->validate([
                'name' => 'required',
                'name_ar' => 'required',
            ]);

            $categoryId = $request->input('category_id');
            $category = category::find($categoryId);

            $category->name = $request->input('name');
            $category->name_ar = $request->input('name_ar');

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('categories'), $imageName);
                $category->image = $imageName;
            }

            $category->save();

            return response()->json(['message' => 'Category updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating category']);
        }
    }

    public function addCategory(Request $request)
    {

        try {
            $validator = $request->validate([
                'name' => 'required',
                'name_ar' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validation for image upload
            ]);

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('categories'), $imageName);
            }

            // Create the category with name, Arabic name, and image
            category::create([
                'name' => $request->input('name'),
                'name_ar' => $request->input('name_ar'),
                'image' => $imageName, // Save the image name in the database
            ]);

            return response()->json(['message' => 'Category added successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error adding category']);
        }
    }


    public function allCategories()
    {
        try {

            $category = category::all();
            if ($category != null) {
//                return response()->json(['message' => $category]);
                return $this->returnData('true',$category,200);
            }
//            return response()->json(['message' => 'error while getting the category']);
            return $this->returnData('true',[], 200,'no categories found');
        } catch (\Throwable $th) {
            return $this->returnData('false',[], 400,$th->getMessage());
        }
    }
    public function categoryproduct(Request $request)
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
//                return response()->json(["error" => "Unauthenticated"], 401);
                return $this->returnData('false',[], 400,"Unauthenticated");
            }

            $validator = Validator::make($request->all(), [
                'cat_id' => 'required|exists:categories,id',
            ]);

            if ($validator->fails()) {
//                return response()->json(['error' => $validator->errors()], 401);
                return $this->returnData('false',[], 400,$validator->errors());
            }

            $category = category::where('id', $request->cat_id)->first();

            if ($category != null) {
                $products = product::with('size')->where('cat_id', $category->id)->get();
                $productData = [];

                foreach ($products as $product) {
                    $sizeData = [];

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
                                $price = "you dont have the right permission for viewing the price"; // Provide a default price or handle this case as needed
                                break;
                        }

                        $sizeData[] = [
                            'size_id' => $size->id,
                            'size_name' => $size->size,
                            'has_offer' => $product->has_offer,
                        ];
                    }

                    $productData[] = [
                        'name' => $product->productName,
                        'color' => $product->color,
                        'description' => $product->productDescription,
                        'image' => $product->productImage,
                        'price' => $price,
                        'category' => $category,
                        'subcat' => $product->subcat,
                        'has_offer'=>$product->has_offer,
                        'size' => $sizeData,
                    ];
                }

//                return response()->json(["products" => $productData]);
                return $this->returnData('true',$productData,200);
            }

//            return response()->json(['message' => ]);
            return $this->returnData('false',[], 400,'error while getting the category');
        } catch (\Throwable $th) {
//            return response()->json(['error' => 'Error getting category products', 'message' => ]);
            return $this->returnData('false',[], 400,$th->getMessage());
        }
    }
}
