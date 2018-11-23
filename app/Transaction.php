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
        'type_id', 'account_id', 'user_id', 'sum', 'date', 'comment',
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
            return $account->save();
        }

        return false;
    }

    /**
     * Cancel transaction before delete to update balances
     *
     * @return bool|void|null
     * @throws \Exception
     */
    public function delete()
    {
        $this->cancel();
        parent::delete();
    }

    public function cancel()
    {
        // Get account
        $account = $this->account;

        // Update the related account
        $this->sum = -$this->getSumWithSign(); // Get opposite value
        $account->balance += $this->sum;

        // Write down balance after transaction
        $this->balance_after = $this->balance_before;


        parent::save() && $account->save() && $this->updatePreviousTransactions();
    }

    protected function updatePreviousTransactions()
    {
        DB::table($this->getTable())
            ->where('id', '>', $this->id)
            ->where('account_id', '>', $this->account->id)
            ->increment('balance_before', $this->sum)
        ;

        DB::table($this->getTable())
            ->where('id', '>', $this->id)
            ->where('account_id', '>', $this->account->id)
            ->increment('balance_after', $this->sum)
        ;
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
}
