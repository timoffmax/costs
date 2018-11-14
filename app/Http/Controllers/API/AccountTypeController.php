<?php

namespace App\Http\Controllers\API;

use App\AccountType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountTypeController extends Controller
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
     * Display a list of types
     *
     * @param Request $request
     * @return AccountType[]|array|\Illuminate\Database\Eloquent\Collection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('viewAll', AccountType::class);

        $types = AccountType::all();

        if (isset($request['mode']) && $request['mode'] === 'array') {
            $arrayOfTypes = [];

            foreach ($types as $type) {
                $arrayOfTypes[$type->id] = $type->name;
            }
        }

        return $arrayOfTypes ?? $types;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        $this->authorize('create', AccountType::class);

        // Validate data
        $this->validate($request, [
            'name' => 'required|string|max:50',
        ]);

        return AccountType::create([
            'name' => $request['name'],
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(int $id)
    {
        $accountTypeModel = AccountType::findOrFail($id);

        $this->authorize('view', $accountTypeModel);

        return $accountTypeModel;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, int $id)
    {
        $accountTypeModel = AccountType::findOrFail($id);

        $this->authorize('update', $accountTypeModel);

        // Validate data
        $this->validate($request, [
            'name' => 'required|string|max:50',
        ]);

        // Save
        $formData = $request->all();
        $accountTypeModel->fill($formData);

        $accountTypeModel->save();
    }

    /**
     * Remove an account type
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(int $id)
    {
        $accountTypeModel = AccountType::findOrFail($id);

        $this->authorize('delete', $accountTypeModel);

        $accountTypeModel->delete();
    }
}
