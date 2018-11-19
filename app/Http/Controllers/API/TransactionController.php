<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\interfaces\RestApiControllerInterface;
use App\Transaction;
use Illuminate\Http\Request;

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
        if (isset($request['mode']) && $request['mode'] === 'admin') {
            // Check if user can view transactions of other users
            $this->authorize('viewAll', Transaction::class);
        } else {
            // Check if user can view own transactions
            $this->authorize('view', Transaction::class);
        }


        $pageSize = $request['pageSize'] ?? 50;
        $paginatedResult = Transaction::with('account')
            ->with('type')
            ->paginate($pageSize)
        ;

        return $paginatedResult;
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
        $this->authorize('create', Transaction::class);

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
