<?php

namespace App;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class Transaction
 *
 * @property int $type_id
 * @property int $account_id
 * @property int $user_id
 * @property float $sum
 * @property float $balance_before
 * @property float $balance_after
 * @property string $date
 * @property string $comment
 * @property TransactionType $type
 * @property TransactionCategory $category
 * @property Account $account
 * @property Place $place
 */
class Transaction extends ParseRequestAbstractModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transaction';

    /**
     * Automatically fill 'created_at' and 'updated_at' fields
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type_id',
        'account_id',
        'category_id',
        'place_id',
        'user_id',
        'sum',
        'date',
        'comment',
    ];

    protected $with = ['user', 'account', 'type', 'category', 'place'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get transactions of all users
     *
     * @return Transaction|\Illuminate\Database\Eloquent\Builder
     */
    protected static function getAllModels()
    {
        return self::with(['user', 'type', 'account', 'place', 'category']);
    }

    /**
     * Get transactions of particular user
     *
     * @param User $user
     * @return Transaction|\Illuminate\Database\Eloquent\Builder
     */
    protected static function getUserModels(User $user)
    {
        return self::getAllModels()
            ->where(['user_id' => $user->id])
        ;
    }

    /**
     * Save transaction with updating balances
     *
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        // Get account
        $account = $this->account;

        // Write down balance before transaction (only for new records!)
        if (!$this->exists) {
            $this->balance_before = $account->balance;
        }

        // Update the related account
        $transactionSum = $this->getSumWithSign();
        $account->balance += $transactionSum;

        // Write down balance after transaction
        $this->balance_after = $this->balance_before + $transactionSum;

        if (parent::save($options)) {
            // If it's update
            if ($this->exists) {
                return $account->save() && $this->updateFollowingTransactions(false);
            }

            // If it's a new record
            return $account->save();
        }

        return false;
    }

    /**
     * Delete with updating the related account and all following transactions
     *
     * @return bool|void|null
     * @throws \Exception
     */
    public function delete()
    {
        // Get account
        $account = $this->account;

        // Update the related account
        $sumToRevert = -$this->getSumWithSign();
        $account->balance += $sumToRevert;

        // Save account and update all transaction after this one
        parent::delete() && $account->save() && $this->updateFollowingTransactions();
    }

    /**
     * Get the transaction owner
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transaction account
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get type of the transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(TransactionType::class);
    }

    /**
     * Get category of the transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(TransactionCategory::class);
    }

    /**
     * Get place of the transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    /**
     * Revert transaction
     */
    public function cancel()
    {
        // Get account
        $account = $this->account;

        // Update the related account
        $sumToRevert = -$this->getSumWithSign();
        $account->balance += $sumToRevert;

        // Write down balance after transaction
        $this->balance_after = $this->balance_before;

        // Save account and update all transaction after this one
        $account->save() && $this->updateFollowingTransactions();
    }

    /**
     * Place several transactions in a row to process transfer between two account
     *
     * @param Request $request
     * @throws \Exception
     */
    public static function processTransfer(Request $request)
    {
        DB::beginTransaction();

        try {
            $transferFromCategory = TransactionCategory::where(['name' => 'transfer from account'])->firstOrFail();
            $transferToCategory = TransactionCategory::where(['name' => 'transfer to account'])->firstOrFail();
            $transferFeeCategory = TransactionCategory::where(['name' => 'transfer fee'])->firstOrFail();

            $transactionFrom = new Transaction();
            $transactionFromData = [
                'category_id' => $transferFromCategory->id,
                'user_id' => $request['user_id'],
                'type_id' => $request['type_id'],
                'account_id' => $request['account_from_id'],
                'date' => $request['date'],
                'sum' => -$request['sum'],
                'comment' => "Transfer money to own account",
            ];
            $transactionFrom->fill($transactionFromData);
            $transactionFrom->save();

            $transactionTo = new Transaction();
            $transactionToData = [
                'category_id' => $transferToCategory->id,
                'user_id' => $request['user_id'],
                'type_id' => $request['type_id'],
                'account_id' => $request['account_to_id'],
                'date' => $request['date'],
                'sum' => $request['sum'],
                'comment' => "Receive money from own account",
            ];
            $transactionTo->fill($transactionToData);
            $transactionTo->save();

            if (!empty($request['fee'])) {
                $transactionFee = new Transaction();
                $transactionFeeData = [
                    'category_id' => $transferFeeCategory->id,
                    'user_id' => $request['user_id'],
                    'type_id' => $transferFeeCategory->transaction_type_id,
                    'account_id' => $request['account_from_id'],
                    'date' => $request['date'],
                    'sum' => -$request['fee'],
                    'comment' => "Transaction fee",
                ];
                $transactionFee->fill($transactionFeeData);
                $transactionFee->save();
            }
        } catch (\Throwable $t) {
            DB::rollBack();
            throw new \Exception('Error during creating transfer transactions');
        }

        DB::commit();
    }

    /**
     * Returns negative or positive sum regards to transaction type
     *
     * @return float
     */
    protected function getSumWithSign()
    {
        switch ($this->type->name) {
            case TransactionType::TYPE_COST:
                $sum = -$this->sum;
                break;

            case TransactionType::TYPE_INCOME:
            case TransactionType::TYPE_TRANSFER:
            default:
                $sum = $this->sum;
        }

        return (float)$sum;
    }

    /**
     * Update balances in every transaction for the same account after this one
     *
     * @return int
     */
    protected function updateFollowingTransactions(bool $revertMode = true)
    {
        if ($revertMode) {
            // Get opposite value of transaction amount to revert it
            $transactionSum = -$this->getSumWithSign();
        } else {
            // Get normal value with sign
            $transactionSum = $this->getSumWithSign();
        }

        $result = DB::table($this->getTable())
            ->where('id', '>', $this->id)
            ->where('account_id', '=', $this->account->id)
            ->update([
                'balance_before' => DB::raw("balance_before + {$transactionSum}"),
                'balance_after' => DB::raw("balance_after + {$transactionSum}"),
            ])
        ;

        return $result;
    }
}
