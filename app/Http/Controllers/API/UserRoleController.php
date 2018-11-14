<?php

namespace App\Http\Controllers\API;

use App\UserRole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserRoleController extends Controller
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
     * Display a listing of the roles.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAll', UserRole::class);

        $roles = UserRole::all();
        $arrayOfRoles = [];

        foreach ($roles as $role) {
            $arrayOfRoles[$role->id] = $role->name;
        }

        return $arrayOfRoles;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', UserRole::class);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $userRoleModel = UserRole::findOrFail($id);

        $this->authorize('view', $userRoleModel);

        return $userRoleModel;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $userRoleModel = UserRole::findOrFail($id);

        $this->authorize('update', $userRoleModel);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $userRoleModel = UserRole::findOrFail($id);

        $this->authorize('delete', $userRoleModel);
    }
}
