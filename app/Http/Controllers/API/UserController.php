<?php

namespace App\Http\Controllers\API;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

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
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user, int $id)
    {
        $userModel = User::findOrFail($id);

        $this->authorize('view', $userModel);
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
            'role_id' => 'required|integer',
            'email' => "required|string|email|max:150|unique:users,email,{$userModel->id}",
            'password' => 'sometimes|required|string|min:8|max:30',
            'passwordConfirmation' => 'required_with:password|same:password|string|min:8|max:30',
        ]);

        $userModel->fill($formData);

        // Encrypt password
        if (isset($request['password'])) {
            $userModel->password = Hash::make($request['password']);
        }

        $userModel->save();
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
