<?php
declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\TransactionCategoryType;
use Illuminate\Http\Request;

/**
 * Transaction category type controller
 */
class TransactionCategoryTypeController extends BaseController
{
    /**
     * Display a list of types
     *
     * @param Request $request
     * @return TransactionCategoryType[]|array|\Illuminate\Database\Eloquent\Collection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('viewAll', TransactionCategoryType::class);

        $types = TransactionCategoryType::all();

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
        $this->authorize('create', TransactionCategoryType::class);

        // Validate data
        $this->validate($request, [
            'name' => 'required|string|max:50',
        ]);

        return TransactionCategoryType::create([
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
        $transactionCategoryTypeModel = TransactionCategoryType::findOrFail($id);

        $this->authorize('view', $transactionCategoryTypeModel);

        return $transactionCategoryTypeModel;
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
        $transactionCategoryTypeModel = TransactionCategoryType::findOrFail($id);

        $this->authorize('update', $transactionCategoryTypeModel);

        // Validate data
        $this->validate($request, [
            'name' => 'required|string|max:50',
        ]);

        // Save
        $formData = $request->all();
        $transactionCategoryTypeModel->fill($formData);

        $transactionCategoryTypeModel->save();
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
        $transactionCategoryTypeModel = TransactionCategoryType::findOrFail($id);

        $this->authorize('delete', $transactionCategoryTypeModel);

        $transactionCategoryTypeModel->delete();
    }
}
