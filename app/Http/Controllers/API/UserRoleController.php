<?php
declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Resources\UserRoleResource;
use App\UserRole;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * User role REST controller
 */
class UserRoleController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->authorizeResource(UserRole::class);
    }

    /**
     * Display a listing of the roles
     *
     * @return array
     * @throws AuthorizationException
     */
    public function index(): array
    {
        $roles = UserRole::all();
        $arrayOfRoles = [];

        foreach ($roles as $role) {
            $arrayOfRoles[$role->id] = $role->name;
        }

        return $arrayOfRoles;
    }

    /**
     * @return JsonResponse
     */
    public function store(): JsonResponse
    {
        return response()->json(
            [
                'error' => 'Creating new roles is not allowed.'
            ],
            403
        );
    }

    /**
     * @param UserRole $role
     * @return UserRoleResource
     */
    public function show(UserRole $role): UserRoleResource
    {
        return new UserRoleResource($role);
    }

    /**
     * @param Request $request
     * @param UserRole $role
     * @return UserRoleResource
     */
    public function update(Request $request, UserRole $role)
    {
        $role->update($request->only(['name']));

        return new UserRoleResource($role);
    }

    /**
     * @param UserRole $role
     * @return Response
     * @throws \Exception
     */
    public function destroy(UserRole $role)
    {
        $role->delete();

        return response()->noContent();
    }
}
