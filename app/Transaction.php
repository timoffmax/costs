<?php

namespace App;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
     * Field names
     */
    public const ID = 'id';
    public const TYPE_ID = 'type_id';
    public const ACCOUNT_ID = 'account_id';
    public const USER_ID = 'user_id';
    public const SUM = 'sum';
    public const BALANCE_BEFORE = 'balance_before';
    public const BALANCE_AFTER = 'balance_after';
    public const DATE = 'date';
    public const COMMENT = 'comment';

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
        // Type is empty if record isn't saved yet
        $type = $this->type ?? TransactionType::findOrFail($this->type_id);

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

        if (TransactionType::TYPE_TRANSFER === $type->name) {
            $this->sum = abs($this->sum);
        }

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
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transaction account
     *
     * @return BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get type of the transaction
     *
     * @return BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(TransactionType::class);
    }

    /**
     * Get category of the transaction
     *
     * @return BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(TransactionCategory::class);
    }

    /**
     * Get place of the transaction
     *
     * @return BelongsTo
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

            $accountFrom = Account::findOrFail($request['account_from_id']);
            $accountTo = Account::findOrFail($request['account_to_id']);

            $commentFrom = "Transfer money to own account \"{$accountTo->name}\"";
            $commentTo = "Receive money from own account \"{$accountFrom->name}\"";
            $commentFee = "Transfer fee (\"{$accountFrom->name}\" -> \"{$accountTo->name}\")";

            $withExchange = !empty($request['exchange_course']);

            if ($withExchange) {
                $sum = $request['sum'];
                $exchangeCourse = $request['exchange_course'];
                $convertedSum = $sum * $exchangeCourse;
                $commentTo .= ". Course is {$exchangeCourse}";
            }

            $transactionFrom = new Transaction();
            $transactionFromData = [
                'transfer_type' => 'from',
                'category_id' => $transferFromCategory->id,
                'user_id' => $request['user_id'],
                'type_id' => $transferFromCategory->transaction_type_id,
                'account_id' => $accountFrom->id,
                'date' => $request['date'],
                'sum' => -$request['sum'],
                'comment' => $commentFrom,
            ];
            $transactionFrom->fill($transactionFromData);
            $transactionFrom->save();

            $transactionTo = new Transaction();
            $transactionToData = [
                'transfer_type' => 'to',
                'category_id' => $transferToCategory->id,
                'user_id' => $request['user_id'],
                'type_id' => $transferToCategory->transaction_type_id,
                'account_id' => $accountTo->id,
                'date' => $request['date'],
                'sum' => $convertedSum ?? $request['sum'],
                'comment' => $commentTo,
            ];
            $transactionTo->fill($transactionToData);
            $transactionTo->save();

            if (!empty($request['fee'])) {
                $transactionFee = new Transaction();
                $transactionFeeData = [
                    'category_id' => $transferFeeCategory->id,
                    'user_id' => $request['user_id'],
                    'type_id' => $transferFeeCategory->transaction_type_id,
                    'account_id' => $accountFrom->id,
                    'date' => $request['date'],
                    'sum' => $request['fee'],
                    'comment' => $commentFee,
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
        // Type is empty if record isn't saved yet
        $type = $this->type ?? TransactionType::findOrFail($this->type_id);

        switch ($type->name) {
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
