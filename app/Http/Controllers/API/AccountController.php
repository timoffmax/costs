<?php
declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Account;
use App\Interfaces\RestApiControllerInterface;
use App\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * User accounts controller
 */
class AccountController extends BaseController implements RestApiControllerInterface
{
    /**
     * Display a list of accounts
     *
     * @param Request $request
     * @return mixed
     * @throws AuthorizationException
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
     * @param Request $request
     * @return Response
     * @throws AuthorizationException
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
            Account::NAME => 'required|string|max:50',
            Account::USER_ID => 'required|integer|exists:users,id',
            Account::TYPE_ID => 'required|integer|exists:account_type,id',
            Account::CURRENCY_ID => 'nullable|integer|exists:currency,id',
            Account::BALANCE => 'required|numeric|between:0,9999999.99',
            Account::CALCULATE_COSTS => 'nullable|boolean',
        ]);

        $account->save();
    }

    /**
     * Display the specified account.
     *
     * @param  int  $id
     * @return Response
     * @throws AuthorizationException
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
     * @param Request $request
     * @param  int  $id
     * @return Response
     * @throws AuthorizationException
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
            'user_id' => 'required|integer|exists:users,id',
            'type_id' => 'required|integer|exists:account_type,id',
            'balance' => 'required|numeric|between:0,9999999.99',
            'calculate_costs' => 'nullable|boolean',
        ]);

        $accountModel->fill($formData);
        $accountModel->save();
    }

    /**
     * Remove an account
     *
     * @param  int  $id
     * @return Response
     * @throws AuthorizationException
     */
    public function destroy(int $id)
    {
        // Check permissions
        $accountModel = Account::findOrFail($id);

        $this->authorize('delete', $accountModel);

        $accountModel->delete();
    }
}
