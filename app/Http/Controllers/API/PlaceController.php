<?php

namespace App\Http\Controllers\API;

use App\Place;
use App\Interfaces\RestApiControllerInterface;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class PlaceController
 */
class PlaceController extends BaseController implements RestApiControllerInterface
{
    /**
     * Display a list of places
     *
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if (empty($request['userId'])) {
            // Check if user can view transactions of other users
            $this->authorize('viewAll', Place::class);
            $places = Place::getAll($request);
        } else {
            // Check if user can view own transactions
            $userId = $request['userId'] ?? Auth::user()->id;
            $userModel = User::findOrFail($userId);
            $this->authorize('viewOwn', [Place::class, $userModel]);

            $places = Place::getAll($request, $userModel);
        }

        return $places;
    }

    /**
     * Store a newly created place
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        // Fill fields
        $place = new Place();
        $place->fill($request->all());

        // Check permissions
        $this->authorize('create', $place);

        // Validate data
        $this->validate($request, [
            'name' => 'required|string|max:50',
            'user_id' => 'required|integer|exists:users,id',
            Place::IS_ARCHIVED => 'nullable|boolean',
        ]);

        $place->save();
    }

    /**
     * Display the specified place.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(int $id)
    {
        $placeModel = Place::findOrFail($id);

        $this->authorize('view', $placeModel);

        // Add user name as property
        $placeModel->user = $placeModel->user->name;

        return $placeModel;
    }

    /**
     * Update an place
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, int $id)
    {
        // Check permissions
        $placeModel = Place::findOrFail($id);

        $this->authorize('update', $placeModel);

        // Get form and validate
        $formData = $request->all();

        $this->validate($request, [
            'name' => 'required|string|max:50',
            'user_id' => 'required|integer',
            Place::IS_ARCHIVED => 'nullable|boolean',
        ]);

        $placeModel->fill($formData);
        $placeModel->save();
    }

    /**
     * Remove an place
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(int $id)
    {
        // Check permissions
        $placeModel = Place::findOrFail($id);

        $this->authorize('delete', $placeModel);

        $placeModel->delete();
    }
}
