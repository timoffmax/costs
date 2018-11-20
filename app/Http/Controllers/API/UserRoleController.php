<?php

namespace App\Http\Controllers\API;

use App\Interfaces\RestApiControllerInterface;
use App\UserRole;
use Illuminate\Http\Request;

class UserRoleController extends BaseController implements RestApiControllerInterface
{
    /**
     * Display a listing of the roles
     *
     * @param Request $request
     * @return array|\Illuminate\Database\Eloquent\Collection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
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
     * Create a user role
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        $this->authorize('create', UserRole::class);
    }


    /**
     * Display the specified user role
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(int $id)
    {
        $userRoleModel = UserRole::findOrFail($id);

        $this->authorize('view', $userRoleModel);

        return $userRoleModel;
    }


    /**
     * Display the specified user role
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response|void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, int $id)
    {
        $userRoleModel = UserRole::findOrFail($id);

        $this->authorize('update', $userRoleModel);
    }


    /**
     * Remove the specified user role
     *
     * @param int $id
     * @return \Illuminate\Http\Response|void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(int $id)
    {
        $userRoleModel = UserRole::findOrFail($id);

        $this->authorize('delete', $userRoleModel);
    }
}
