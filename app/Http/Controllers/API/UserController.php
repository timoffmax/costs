<?php

namespace App\Http\Controllers\API;

use App\User;
use App\UserRole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a list of users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
    public function show($id)
    {
        //
    }

    /**
     * Update a user
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Get data
        $user = User::findOrFail($id);
        $formData = $request->all();

        // Validate data
        if (isset($formData['password'])) {
            $this->validate($request, [
                'name' => 'required|string|max:50',
                'role_id' => 'required|integer',
                'email' => 'required|string|email|max:150|unique:users',
                'password' => 'required|string|min:8|max:30',
                'passwordConfirmation' => 'required|required_with:password|same:password|string|min:8|max:30',
            ]);
        } else {
            $this->validate($request, [
                'name' => 'required|string|max:50',
                'role_id' => 'required|integer',
                'email' => 'required|string|email|max:150|unique:users',
            ]);
        }

        // Save
        $user->fill($formData);
        $user->save();
    }

    /**
     * Remove user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
    }
}
