<?php

namespace App\Http\Controllers;

use App\Models\category;
use App\Models\government;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class GovernmentController extends Controller
{
    public function show(Request $request)
    {
        if ($request->ajax()) {
            $data = government::all();

            return DataTables::of($data)
                ->addColumn('edit', function ($row) {
                    return '<button type="button" class="btn btn-info edit-government" data-id="' . $row->id . '">Edit</button>';
                })
                ->addColumn('delete', function ($row) {
                    return '<button type="button" class="btn btn-danger delete-government" data-id="' . $row->id . '">Delete</button>';
                })
                ->rawColumns(['edit', 'delete'])
                ->make(true);
        }

        return view('Dashboard.shipping');
    }


    public function add(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'cash_price' => 'required|numeric',
            'voda_price' => 'required|numeric'
        ]);

        $newGovernment = government::create([
            'name' => $validatedData['name'],
            'cash_price' => $validatedData['cash_price'],
            'voda_price' => $validatedData['voda_price']
        ]);

        if ($newGovernment) {
            return response()->json(['message' => 'Government added successfully'], 200);
        } else {
            return response()->json(['message' => 'Failed to add government'], 500);
        }
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:governments',
            'name' => 'required',
            'cash_price' => 'required|numeric',
            'voda_price' => 'required|numeric'
        ]);

        $government = government::find($validatedData['id']);

        if ($government) {
            $government->name = $validatedData['name'];
            $government->cash_price = $validatedData['cash_price'];
            $government->voda_price = $validatedData['voda_price'];

            $government->save();

            return response()->json(['message' => 'Government updated successfully'], 200);
        } else {
            return response()->json(['message' => 'Government not found'], 404);
        }
    }

    public function getGovernmentById($id)
    {
        $government = government::find($id);

        if ($government) {
            return response()->json(['government' => $government], 200);
        } else {
            return response()->json(['message' => 'Government not found'], 404);
        }
    }

    public function delete($id)
    {
        $government = government::find($id);

        if ($government) {
            $government->delete();
            return response()->json(['message' => 'Government deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Government not found'], 404);
        }
    }
}
