<?php

namespace App\Http\Controllers;

use App\Mail\SendCodeResetPassword;
use App\Models\Admin;
use App\Models\ResetCodePassword;
use App\Models\User;
use App\Traits\ResponseTrait;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    use ResponseTrait;

    public function admin_login(Request $request)
    {
        $email = $request->email;
        $user = Admin::where("email", $email)->get();
        // dd($user);
        if (count($user) > 0) {
            if (Hash::check($request->password, $user[0]["password"])) {
                if (Auth::guard('web')->attempt(['email' => $email, 'password' => $request->password])) {
                    return redirect(url('/dashboard'));
                } else {
                    return back()->withInput($request->only('email'))->with('err', 'wrong email or password');
                }

            } else {
                return back()->withInput($request->only('email'))->with('err', 'wrong email or password');

                // return response(["messege"=>"password","id"=>0]);
            }
        }
        return back()->withInput($request->only('email'))->with('err', 'wrong email or password');
    }

    public function admin_logout()
    {
        Auth::guard('web')->logout();
        return redirect()->route('login.page');
    }

    public function signup(Request $request)
    {
        try {
            // Validation for 'type'
            $validator = Validator::make($request->all(), [
                'type' => 'required|string|in:player,coach,store',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            // Common validation rules for all user types
            $commonRules = [
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'address' => 'string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
                'phone_number' => 'required|numeric|digits:11',
            ];

            // Additional validation rules based on 'type'
            $typeSpecificRules = [];

            if ($request->type === 'coach') {
                $typeSpecificRules['id_image'] = 'required|image|mimes:jpeg,png,jpg,gif';
            } elseif ($request->type === 'store') {
                $typeSpecificRules['tax_card_image'] = 'required|image|mimes:jpeg,png,jpg,gif';
                $typeSpecificRules['store_image'] = 'required|image|mimes:jpeg,png,jpg,gif';
            } elseif ($request->type === 'player') {
                $typeSpecificRules['profile_image'] = 'image|mimes:jpeg,png,jpg,gif';
            }

            // Validate based on common and type-specific rules
            $validator = Validator::make($request->all() + $request->only(array_keys($typeSpecificRules)), $commonRules + $typeSpecificRules);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            // Hash the password
            $request['password'] = Hash::make($request->password);

            // Create user with common data and 'active' flag based on 'type'
            $userType = $request->input('type');
            $isActive = ($userType === 'player') ? 1 : 0;
            $userData = $request->except('active');
            $user = User::create(array_merge($userData, ['active' => $isActive]));

            // Handle image uploads based on 'type'
            if ($user) {
                if ($userType === 'coach' && $request->file('id_image')) {
                    $this->handleImageUpload($user, $request->file('id_image'), 'id_image');
                } elseif ($userType === 'store' && $request->file('store_image') && $request->file('tax_card_image')) {
                    $this->handleImageUpload($user, $request->file('store_image'), 'store_image');
                    $this->handleImageUpload($user, $request->file('tax_card_image'), 'tax_card_image');
                } elseif ($userType === 'player' && $request->file('profile_image')) {
                    $this->handleImageUpload($user, $request->file('profile_image'), 'profile_image');
                }

                return response()->json(['success' => 'true', 'code' => '200', 'message' => 'user registered successfully'], 200);

            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 401);
        }
    }

    private function handleImageUpload($user, $file, $field)
    {
        $imageName = time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('user_images'), $imageName);
        $user->update([$field => $imageName]);
    }

    public function login(Request $request)
    {
        // Attempt to authenticate the user
        $credentials = ['email' => $request['email'], 'password' => $request['password']];
        $user = User::where('email', $request['email'])->first();
        if ($user != null) {
            if ($user->active == 0) {
                return response()->json(['error' => 'Account not verified yet.'], 401);
            } else {
                $token = auth('api')->attempt($credentials);

                if (!$token) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }

                return $this->respondWithToken($token);
            }
        }
        return response()->json(['error' => 'Wrong email or password'], 401);

    }

    public function update(Request $request)
    {
        try {
            $user = auth('api')->user(); // Retrieve the authenticated user

            // Validate the request based on user type
            $commonRules = [
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'address' => 'string',
                'phone_number' => 'required|numeric|digits:10',
            ];

            if ($user->type === 'coach') {
                $validator = Validator::make($request->all(), $commonRules + [
                        'id_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                    ]);
            } elseif ($user->type === 'store') {
                $validator = Validator::make($request->all(), $commonRules + [
                        'tax_card_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                        'store_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                    ]);
            } else {
                // For normal user type = player
                $validator = Validator::make($request->all(), $commonRules + [
                        'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                    ]);
            }

            if ($validator->fails()) {
                return $this->returnData('false', [], 400, $validator->errors());
            }

            // Update user's information based on user typee
            $user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'address' => $request->address,
                'phone_number' => $request->phone_number,
            ]);

            if ($user->type === 'coach') {
                $idImage = $request->file('id_image')->store('images');
                $user->update(['id_image' => $idImage]);
            } elseif ($user->type === 'store') {
                $taxCardImage = $request->file('tax_card_image')->store('images');
                $storeImage = $request->file('store_image')->store('images');
                $user->update(['tax_card_image' => $taxCardImage, 'store_image' => $storeImage]);
            } else {
                // For normal user type = player
                $profileImage = $request->file('profile_image')->store('images');
                $user->update(['profile_image' => $profileImage]);
            }

//        return response()->json(['success' => 'User information updated successfully']);
            return $this->returnData('true', [], 200);
        } catch (\Throwable $th) {
//        return response()->json(['error' => $th->getMessage()], 500);
            return $this->returnData('false', [], 400, $th->getMessage());
        }
    }

    public function logout()
    {
        auth('api')->logout();

//        return response()->json(['message' => 'Successfully logged out']);
        return $this->returnData('Successfully logged out', [], 200);
    }

    public function profile()
    {
        $user = auth('api')->user();
        if ($user) {
//           return response()->json(['user' => auth('api')->user()]);
            return $this->returnData('true', $user, 200);
        } else {
//           return response()->json(['error' => 'not authorized']);
            return $this->returnData('false', [], 400, 'not authorized');
        }
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken(string $token)
    {

        return response()->json(
            [
                'state' => 'true',
                'token' => $token,
                'token_type' => 'bearer',
//                'expires_in' => auth('api')->factory()->getTTL(),
                'user' => auth('api')->user()
            ]
        );
    }

    public function uploadImage(Request $request,)
    {
        try {
            $user = auth('api')->user(); // Retrieve the authenticated user

            // Validate the request based on the image type
            // $validator = Validator::make($request->all(), [
            //     'id_image' => 'required|max:2048',
            // ]);

            // if ($validator->fails()) {
            //     return response()->json(['error' => $validator->errors()], 400);
            // }

            // Move the uploaded image to the 'public/images' directory
            $uploadedImage = $request->file('id_image');
            $imageName = time() . '.' . $uploadedImage->getClientOriginalExtension();
            $uploadedImage->move(public_path('a', $imageName), $imageName);
            $user->update(['id_image' => 'a/' . $imageName]);

//        return response()->json([
//            'success' => 'Image uploaded successfully',
//            // 'link'=> 'a/' . $imageName
//    ]);
            return $this->returnData('Image uploaded successfully', [], 200);
        } catch (\Throwable $th) {
//        return response()->json(['error' => $th->getMessage()], 500);
            return $this->returnData('false', [], 400, $th->getMessage());
        }
    }

    public function delete()
    {
        $user = Auth::user();
        if ($user) {
            $user->delete();
            return $this->returnData('your acc is deleted successfully', [], 200);
        } else {
            return $this->returnData('false', [], 400, 'Something went wrong');
        }
    }

    public function send_code_reset_password(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        // Delete all old code that user send before.
        ResetCodePassword::where('email', $request->email)->delete();

        // Generate random code
        $data['code'] = mt_rand(100000, 999999);

        // Create a new code
        $codeData = ResetCodePassword::create($data);

        Mail::to($request->email)->send(new SendCodeResetPassword($codeData->code));

        return response(['message' => trans('passwords.sent')], 200);
    }

    public function Reset_password(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:reset_code_passwords',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // find the code
        $passwordReset = ResetCodePassword::firstWhere('code', $request->code);

        // check if it does not expired: the time is one hour
        if ($passwordReset->created_at > now()->addHour()) {
            $passwordReset->delete();
            return response(['message' => trans('passwords.code_is_expire')], 422);
        }

        // find user's email
        $user = User::firstWhere('email', $passwordReset->email);

        // update user password
        $user->update(
            [
                'password'=> bcrypt($request->password)
            ]);

        // delete current code
        $passwordReset->delete();
        return $this->returnData('password has been successfully reset',[],200);
    }



}
