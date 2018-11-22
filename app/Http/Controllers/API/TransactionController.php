<?php

namespace App\Http\Controllers\API;

use App\Interfaces\RestApiControllerInterface;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends BaseController implements RestApiControllerInterface
{
    /**
     * Display a list of transactions
     *
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if (empty($request['userId'])) {
            // Check if user can view transactions of other users
            $this->authorize('viewAll', Transaction::class);
            $transactions = Transaction::getAll($request);
        } else {
            // Check if user can view own transactions
            $userId = $request['userId'] ?? Auth::user()->id;
            $userModel = User::findOrFail($userId);
            $this->authorize('viewOwn', [Transaction::class, $userModel]);

            $transactions = Transaction::getAll($request, $userModel);
        }

        return $transactions;
    }

    /**
     * Store a newly created transaction
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        // Fill fields
        $transaction = new Transaction();
        $transaction->fill($request->all());

        // Check permissions
        $this->authorize('create', $transaction);

        // Validate data
        $this->validate($request, [
            'user_id' => 'required|integer|exists:users,id',
            'type_id' => 'required|integer|exists:transaction_type,id',
            'account_id' => 'required|integer|exists:account,id',
            'date' => 'required|date|date_format:Y-m-d',
            'sum' => 'required|numeric|between:0,9999999.99',
            'comment' => 'nullable|string|max:300',
        ]);

        // Save and update account amount
        $transaction->saveWithAccount();
    }

    /**
     * Display the specified transaction.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(int $id)
    {
        $transactionModel = Transaction::findOrFail($id);

        $this->authorize('view', $transactionModel);

        // Add user name as property
        $transactionModel->user = $transactionModel->user->name;

        return $transactionModel;
    }

    /**
     * Update an transaction
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, int $id)
    {
        // Check permissions
        $transactionModel = Transaction::findOrFail($id);

        $this->authorize('update', $transactionModel);

        // Get form and validate
        $formData = $request->all();

        $this->validate($request, [
            'user_id' => 'required|integer|exists:users,id',
            'type_id' => 'required|integer|exists:transaction_type,id',
            'account_id' => 'required|integer|exists:account,id',
            'date' => 'required|date|date_format:Y-m-d',
            'sum' => 'required|numeric|between:0,9999999.99',
            'comment' => 'string|max:300',
        ]);

        $transactionModel->fill($formData);
        $transactionModel->save();
    }

    /**
     * Remove an transaction
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(int $id)
    {
        // Check permissions
        $transactionModel = Transaction::findOrFail($id);

        $this->authorize('delete', $transactionModel);

        $transactionModel->delete();
    }
}
