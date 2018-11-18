<?php

namespace App\Http\Controllers\API;

use App\TransactionType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionTypeController extends Controller
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
     * @return TransactionType[]|array|\Illuminate\Database\Eloquent\Collection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('viewAll', TransactionType::class);

        $types = TransactionType::all();

        // Prepare a simple list (id => name)
        if (isset($request['mode']) && $request['mode'] === 'simple') {
            $simplifiedList = [];

            foreach ($types as $type) {
                $simplifiedList[$type->id] = $type->name;
            }
        }

        return $simplifiedList ?? $types;
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
        $this->authorize('create', TransactionType::class);

        // Validate data
        $this->validate($request, [
            'name' => 'required|string|max:50',
        ]);

        return TransactionType::create([
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
        $transactionTypeModel = TransactionType::findOrFail($id);

        $this->authorize('view', $transactionTypeModel);

        return $transactionTypeModel;
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
        $transactionTypeModel = TransactionType::findOrFail($id);

        $this->authorize('update', $transactionTypeModel);

        // Validate data
        $this->validate($request, [
            'name' => 'required|string|max:50',
        ]);

        // Save
        $formData = $request->all();
        $transactionTypeModel->fill($formData);

        $transactionTypeModel->save();
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
        $transactionTypeModel = TransactionType::findOrFail($id);

        $this->authorize('delete', $transactionTypeModel);

        $transactionTypeModel->delete();
    }
}
