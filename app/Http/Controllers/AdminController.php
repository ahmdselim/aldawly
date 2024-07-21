<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class AdminController extends Controller
{
    public function get_users(Request $request)
    {
        if ($request->ajax()) {
            $data = User::all();

            return DataTables::of($data)
                ->addColumn('active', function ($row) {
                    return '<label class="switch">
                          <input type="checkbox" class="toggle-active" data-id="' . $row->id . '" ' . ($row->active ? 'checked' : '') . '>
                          <span class="slider"></span>
                        </label>';
                })
                ->addColumn('cer_images', function ($row) {
                    return '<button type="button" class="btn btn-primary view-cer-images" data-id="' . $row->id . '">View Cer Images</button>';
                })
                ->addColumn('delete', function ($row) {
                    return '<button type="button" class="btn btn-danger delete-user" data-id="' . $row->id . '">Delete</button>';
                })
                ->rawColumns([ 'cer_images','active','delete'])
                ->make(true);
        }

        return view('Dashboard.users');
    }


    public function updateUserStatus(Request $request)
    {
        try {
            $userId = $request->input('user_id');
            $isActive = $request->input('is_active');
            if ($isActive === 'false'){
                User::where('id', $userId)->update(['active' => '0']);
        }
            else
            {
                User::where('id', $userId)->update(['active' => '1']);
            }

            return response()->json(['message' => 'User status updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating user status']);
        }
    }

    public function deleteUser(Request $request)
    {
        try {
            $userId = $request->input('user_id');

            // Delete the user
            User::where('id', $userId)->delete();

            return response()->json(['message' => 'User deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting user']);
        }
    }

    public function getUserType(Request $request)
    {
        try {
            $userId = $request->input('user_id');
            $user = User::find($userId);

            return response()->json(['user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error getting user type']);
        }
    }
}
