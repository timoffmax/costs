<?php

namespace App\Http\Controllers\API;

use App\Currency;
use App\Interfaces\RestApiControllerInterface;
use Illuminate\Http\Request;

/**
 * Class CurrencyController
 */
class CurrencyController extends BaseController implements RestApiControllerInterface
{
    /**
     * Display a list of currencies
     *
     * @param Request $request
     * @return Currency[]|array|\Illuminate\Database\Eloquent\Collection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('viewAll', Currency::class);

        $types = Currency::all();

        return $types;
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
        $this->authorize('create', Currency::class);

        // Validate data
        $this->validate($request, [
            'name' => 'required|string|max:30',
            'sign' => 'required|string|max:3',
        ]);

        return Currency::create([
            'name' => $request['name'],
            'sign' => $request['sign'],
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
        $model = Currency::findOrFail($id);

        $this->authorize('view', $model);

        return $model;
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
        $model = Currency::findOrFail($id);

        $this->authorize('update', $model);

        // Validate data
        $this->validate($request, [
            'name' => 'required|string|max:30',
            'sign' => 'required|string|max:3',
        ]);

        // Save
        $formData = $request->all();
        $model->fill($formData);

        $model->save();
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
        $model = Currency::findOrFail($id);

        $this->authorize('delete', $model);

        $model->delete();
    }
}
