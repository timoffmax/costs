<?php

namespace App\Http\Controllers\API;

use App\Interfaces\RestApiControllerInterface;
use App\TransactionCategory;

use Illuminate\Http\Request;

/**
 * Class TransactionCategoryController
 */
class TransactionCategoryController extends BaseController implements RestApiControllerInterface
{
    /**
     * Display a list of types
     *
     * @param Request $request
     * @return TransactionCategory[]|array|\Illuminate\Database\Eloquent\Collection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('viewAll', TransactionCategory::class);

        $types = TransactionCategory::all();

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
        $this->authorize('create', TransactionCategory::class);

        // Validate data
        $this->validate($request, [
            'name' => 'required|string|max:50',
        ]);

        return TransactionCategory::create([
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
        $transactionCategoryModel = TransactionCategory::findOrFail($id);

        $this->authorize('view', $transactionCategoryModel);

        return $transactionCategoryModel;
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
        $transactionCategoryModel = TransactionCategory::findOrFail($id);

        $this->authorize('update', $transactionCategoryModel);

        // Validate data
        $this->validate($request, [
            'name' => 'required|string|max:50',
        ]);

        // Save
        $formData = $request->all();
        $transactionCategoryModel->fill($formData);

        $transactionCategoryModel->save();
    }

    /**
     * Remove model by ID
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(int $id)
    {
        $transactionCategoryModel = TransactionCategory::findOrFail($id);

        $this->authorize('delete', $transactionCategoryModel);

        $transactionCategoryModel->delete();
    }
}
