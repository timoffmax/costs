<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     * Require auth.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Show current user
     * user can view only onw data
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return auth('api')->user();
    }


    /**
     * Update current user
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        // Ignore ID parameter and use current user
        $user = auth('api')->user();
        $formData = $request->all();

        $this->validate($request, [
            'name' => 'required|string|max:50',
            'email' => "required|string|email|max:150|unique:users,email,{$user->id}",
            'password' => 'sometimes|required|string|min:8|max:30',
            'passwordConfirmation' => 'required_with:password|same:password|string|min:8|max:30',
        ]);

        $user->fill($formData);

        // Encrypt password
        if ($request->password) {
            $user->password = Hash::make($request['password']);
        }

        // Processing profile image
        if ($request->photo && !file_exists($request->photo)) {
            // Get extension and create unique name
            $extension = explode('/', explode(';', $request->photo)[0])[1];
            $filename = uniqid('profile_image_', true) . ".{$extension}";
            $fullPath = public_path('img/profile/') . $filename;

            // Create image from Base64 and update user info
            $result = Image::make($request->photo)->save($fullPath);

            if ($result) {
                $user->photo = public_path('img/profile/') . $filename;
            }
        }

        $user->save();
    }

    /**
     * Remove user
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $user = auth('api')->user();
        $user->delete();
    }
}
