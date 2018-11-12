<?php

namespace App\Http\Controllers\API;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class UserController extends Controller
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
     * Display a list of users.
     *
     * @return array
     */
    public function index()
    {
        $this->authorize('viewAll', User::class);

        return User::latest()->paginate(10);
    }

    /**
     * Store a newly created user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        // Validate data
        $this->validate($request, [
            'name' => 'required|string|max:50',
            'role_id' => 'required|integer',
            'email' => 'required|string|email|max:150|unique:users',
            'password' => 'required|string|min:8|max:30',
            'passwordConfirmation' => 'required|required_with:password|same:password|string|min:8|max:30',
        ]);

        return User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'role_id' => $request['role_id'],
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $userModel = User::findOrFail($id);

        $this->authorize('view', $userModel);

        return $userModel;
    }

    /**
     * Update a user
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        // Check permissions
        $userModel = User::findOrFail($id);

        $this->authorize('update', $userModel);

        // Get form and validate
        $formData = $request->all();

        $this->validate($request, [
            'name' => 'required|string|max:50',
            'role_id' => 'sometimes|required|integer',
            'email' => "required|string|email|max:150|unique:users,email,{$userModel->id}",
            'password' => 'sometimes|required|string|min:8|max:30',
            'passwordConfirmation' => 'required_with:password|same:password|string|min:8|max:30',
        ]);

        $originalData = clone $userModel;
        $userModel->fill($formData);

        // Encrypt password
        if (!empty($request->password)) {
            $userModel->password = Hash::make($request['password']);
        }

        // Processing profile image
        $oldPhoto = $originalData->photo;
        $photoWasChanged = false;

        if (!empty($request->photo) && $request->photo !== $originalData->photo) {
            // Get extension and create unique name
            $extension = explode('/', explode(';', $request->photo)[0])[1];
            $filename = uniqid('profile_image_', true) . ".{$extension}";
            $fullPath = public_path("img/profile/{$filename}");

            // Create image from Base64 and update user info
            $result = Image::make($request->photo)
                ->resize(128, 128)
                ->save($fullPath);

            if ($result) {
                $userModel->photo = "img/profile/{$filename}";
                $photoWasChanged = true;
                $oldPhoto = public_path($oldPhoto);
            }
        }

        if ($userModel->save()) {
            // Remove old profile photo
            if ($photoWasChanged && !empty($oldPhoto) && file_exists($oldPhoto)) {
                unlink($oldPhoto);
            }
        }
    }

    /**
     * Remove user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        // Check permissions
        $userModel = User::findOrFail($id);

        $this->authorize('delete', $userModel);

        $userModel->delete();
    }
}
