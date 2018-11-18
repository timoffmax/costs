<?php

namespace App\Http\Controllers\API;

use App\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
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
     * Display a list of transactions
     *
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('viewAll', Transaction::class);

        $pageSize = $request['pageSize'] ?? 50;
        $paginatedResult = Transaction::with('user')
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
