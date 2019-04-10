<?php

namespace App;


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
 */
class Transaction extends ParseRequestAbstractModel
{
    const TYPE_INCOME = 'income';
    const TYPE_COST = 'cost';

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
        'type_id', 'account_id', 'category_id', 'user_id', 'sum', 'date', 'comment',
    ];

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
        return self::with('user')
            ->with('type')
            ->with('account')
            ->with('category')
        ;
    }

    /**
     * Get transactions of particular user
     *
     * @param User $user
     * @return Transaction|\Illuminate\Database\Eloquent\Builder
     */
    protected static function getUserModels(User $user)
    {
        return self::with('user')
            ->with('account')
            ->with('type')
            ->with('category')
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

    /**
     * Returns negative or positive sum regards to transaction type
     *
     * @return float
     */
    protected function getSumWithSign()
    {
        switch ($this->type->name) {
            case self::TYPE_COST:
                $sum = -$this->sum;
                break;

            case self::TYPE_INCOME:
            default:
                $sum = $this->sum;
        }

        return (float)$sum;
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
}
