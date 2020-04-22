<?php
declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Interfaces\RestApiControllerInterface;
use App\UserRole;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * User role REST controller
 */
class UserRoleController extends BaseController implements RestApiControllerInterface
{
    /**
     * Display a listing of the roles
     *
     * @param Request $request
     * @return array|Collection
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
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
     * @return Response|void
     * @throws AuthorizationException
     */
    public function store(Request $request)
    {
        $this->authorize('create', UserRole::class);
    }


    /**
     * Display the specified user role
     *
     * @param int $id
     * @return Response
     * @throws AuthorizationException
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
     * @return Response|void
     * @throws AuthorizationException
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
     * @return Response|void
     * @throws AuthorizationException
     */
    public function destroy(int $id)
    {
        $userRoleModel = UserRole::findOrFail($id);

        $this->authorize('delete', $userRoleModel);
    }
}
