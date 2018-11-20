<?php

namespace App\Http\Controllers\API;

use App\Account;
use App\Interfaces\RestApiControllerInterface;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        if (empty($request['userId'])) {
            // Check if user can view transactions of other users
            $this->authorize('viewAll', Account::class);
            $accounts = Account::getAll($request);
        } else {
            // Check if user can view own transactions
            $userId = $request['userId'] ?? Auth::user()->id;
            $userModel = User::findOrFail($userId);
            $this->authorize('viewOwn', [Account::class, $userModel]);

            $accounts = Account::getAll($request, $userModel);
        }

        return $accounts;
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
        // Fill fields
        $account = new Account();
        $account->fill($request->all());

        // Check permissions
        $this->authorize('create', $account);

        // Validate data
        $this->validate($request, [
            'name' => 'required|string|max:50',
            'user_id' => 'required|integer|exists:users,id',
            'type_id' => 'required|integer|exists:account_type,id',
            'balance' => 'required|numeric|between:0,9999999.99',
        ]);

        $account->save();
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
