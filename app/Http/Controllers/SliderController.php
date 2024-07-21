<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SliderController extends Controller
{
    use ResponseTrait;
    public function get_sliders(Request $request)
    {
        if ($request->ajax()) {
            $data = Slider::all();

            return DataTables::of($data)
                ->addColumn('image', function ($row) {
                    return '<img src="' . asset('sliders/'.$row->image) . '" alt="Slider Image" style="max-width: 100px; max-height: 100px;">';
                })
                ->addColumn('actions', function ($row) {
                    $editButton = '<button type="button" class="btn btn-info edit-slider" data-id="' . $row->id . '">Edit</button>';
                    $deleteButton = '<button type="button" class="btn btn-danger delete-slider" data-id="' . $row->id . '">Delete</button>';
                    return $editButton . ' ' . $deleteButton;
                })
                ->rawColumns(['actions','image'])
                ->make(true);
        }

        return view('Dashboard.sliders');
    }

    // Method to delete a slider
    public function deleteSlider(Request $request)
    {
        try {
            $sliderId = $request->input('slider_id');
            // Find the slider by ID and delete it
            Slider::where('id', $sliderId)->delete();

            return response()->json(['message' => 'Slider deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting slider']);
        }
    }

    // Method to get slider details
    public function getSliderDetails(Request $request)
    {
        try {
            $sliderId = $request->input('slider_id');
            $slider = Slider::find($sliderId);

            return response()->json(['slider' => $slider]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error getting slider details']);
        }
    }

    // Method to update a slider
    public function updateSlider(Request $request)
    {
        try {
            $sliderId = $request->input('slider_id');
            $slider = Slider::find($sliderId);

            // Update slider URL
            $slider->url = $request->input('url');

            // Update slider image if a new one is provided
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('sliders'), $imageName);
                $slider->image = $imageName;
            }

            $slider->save();

            return response()->json(['message' => 'Slider updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating slider']);
        }
    }

    // Method to add a new slider
    public function addSlider(Request $request)
    {
        try {

            $validator = $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,bmp,tiff,ico,webp,svg,heif,heic|max:2048',
//                'url' => 'required|url',
            ]);

            // Check if request has image file
            if ($request->hasFile('image')) {
                // Get the file from the request
                $image = $request->file('image');
                // Generate a unique filename for the image
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                // Move the image to the desired location
                $image->move(public_path('sliders'), $imageName);
            } else {
                // Return error response if image is not provided
                return response()->json(['error' => 'Slider image is required'], 400);
            }

            // Create a new slider record in the database
            Slider::create([
                'image' => $imageName,
                'url' => $request->input('url'),
            ]);

            return response()->json(['message' => 'Slider added successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error adding slider: ' . $e->getMessage()], 500);
        }
    }

    public function get_all_sliders_api()
    {
        try {
            $sliders = Slider::all();
            return $this->returnData('Sliders',$sliders,200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error getting sliders'], 500);
        }
    }
}
