<?php
declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Interfaces\RestApiControllerInterface;
use App\Transaction;
use App\TransactionType;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Transactions REST controller
 */
class TransactionController extends BaseController implements RestApiControllerInterface
{
    /**
     * @inheritDoc
     */
    public function index(Request $request)
    {
        if (empty($request['userId'])) {
            $this->authorize('viewAll', Transaction::class);
            $transactions = Transaction::getAll($request);
        } else {
            $userId = $request['userId'] ?? Auth::user()->id;
            $userModel = User::findOrFail($userId);
            $this->authorize('viewOwn', [Transaction::class, $userModel]);

            $transactions = Transaction::getAll($request, $userModel);
        }

        return $transactions;
    }

    /**
     * @inheritDoc
     */
    public function store(Request $request)
    {
        $result = new Transaction();
        $result->fill($request->all());

        $this->authorize('create', $result);

        if ($result->type && in_array($result->type->name, TransactionType::TRANSFERABLE_TYPES)) {
            $this->validate($request, [
                Transaction::USER_ID => 'required|integer|exists:users,id',
                Transaction::TYPE_ID => 'required|integer|exists:transaction_type,id',
                Transaction::DATE => 'required|date|date_format:Y-m-d',
                Transaction::SUM => 'required|numeric|between:0,9999999.99',
                'account_from_id' => 'required|integer|exists:account,id',
                'account_to_id' => 'required|integer|exists:account,id',
                'exchange_course' => 'nullable|numeric|between:0,9999999.99',
                'fee' => 'nullable|numeric|between:0,9999999.99',
            ]);

            Transaction::processTransfer($request);
        } else {
            $this->validate($request, [
                Transaction::USER_ID => 'required|integer|exists:users,id',
                Transaction::TYPE_ID => 'required|integer|exists:transaction_type,id',
                Transaction::ACCOUNT_ID => 'required|integer|exists:account,id',
                Transaction::CATEGORY_ID => 'required|integer|exists:transaction_category,id',
                Transaction::PLACE_ID => 'nullable|integer|exists:place,id',
                Transaction::DATE => 'required|date|date_format:Y-m-d',
                Transaction::SUM => 'required|numeric|between:0,9999999.99',
                Transaction::COMMENT => 'nullable|string|max:300',
            ]);

            $result->save();
        }

        return $result;
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function update(Request $request, int $id)
    {
        /** @var Transaction $transactionModel */
        $transactionModel = Transaction::findOrFail($id);

        $this->authorize('update', $transactionModel);

        $formData = $request->all();

        $this->validate($request, [
            Transaction::USER_ID => 'required|integer|exists:users,id',
            Transaction::TYPE_ID => 'required|integer|exists:transaction_type,id',
            Transaction::ACCOUNT_ID => 'required|integer|exists:account,id',
            Transaction::CATEGORY_ID => 'required|integer|exists:transaction_category,id',
            Transaction::PLACE_ID => 'nullable|integer|exists:place,id',
            Transaction::DATE => 'required|date|date_format:Y-m-d',
            Transaction::SUM => 'required|numeric|between:0,9999999.99',
            Transaction::COMMENT => 'nullable|string|max:300',
        ]);

        // "Rollback" transaction
        $transactionModel->cancel();

        // Save with updating account
        $transactionModel->fill($formData);
        $transactionModel->save();
    }

    /**
     * @inheritDoc
     */
    public function destroy(int $id)
    {
        /** @var Transaction $transactionModel */

        $transactionModel = Transaction::findOrFail($id);

        $this->authorize('delete', $transactionModel);

        $transactionModel->delete();
    }
}
