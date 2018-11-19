<?php

namespace App\Http\Controllers\API;

use App\Account;
use App\Http\Controllers\API\interfaces\RestApiControllerInterface;
use Illuminate\Http\Request;

class AccountController extends BaseController implements RestApiControllerInterface
{
    /**
     * Display a list of accounts
     *
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('viewAll', Account::class);

        $pageSize = $request['pageSize'] ?? 10;
        $paginatedResult = Account::with('user')
            ->with('type')
            ->paginate($pageSize)
        ;

        return $paginatedResult;
    }

    /**
     * Store a newly created account
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        $this->authorize('create', Account::class);

        // Validate data
        $this->validate($request, [
            'name' => 'required|string|max:50',
            'user_id' => 'required|integer',
            'type_id' => 'required|integer',
            'balance' => 'required|numeric|between:0,9999999.99',
        ]);

        return Account::create([
            'name' => $request['name'],
            'user_id' => $request['user_id'],
            'type_id' => $request['type_id'],
            'balance' => $request['balance'],
        ]);
    }

    /**
     * Display the specified account.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(int $id)
    {
        $accountModel = Account::findOrFail($id);

        $this->authorize('view', $accountModel);

        // Add user name as property
        $accountModel->user = $accountModel->user->name;

        return $accountModel;
    }

    /**
     * Update an account
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, int $id)
    {
        // Check permissions
        $accountModel = Account::findOrFail($id);

        $this->authorize('update', $accountModel);

        // Get form and validate
        $formData = $request->all();

        $this->validate($request, [
            'name' => 'required|string|max:50',
            'user_id' => 'required|integer',
            'type_id' => 'required|integer',
            'balance' => 'required|numeric|between:0,9999999.99',
        ]);

        $accountModel->fill($formData);
        $accountModel->save();
    }

    /**
     * Remove an account
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(int $id)
    {
        // Check permissions
        $accountModel = Account::findOrFail($id);

        $this->authorize('delete', $accountModel);

        $accountModel->delete();
    }
}
