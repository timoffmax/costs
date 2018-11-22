<?php

namespace App;


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
     * @return bool
     */
    public function saveWithAccount()
    {
        // Get account
        $account = $this->account;

        // Write down balance before transaction
        $this->balance_before = $account->balance;

        // Update account
        switch ($this->type->name) {
            case self::TYPE_COST:
                $account->balance -= $this->sum;
                break;

            case self::TYPE_INCOME:
                $account->balance += $this->sum;
                break;

            default:
                // Do nothing
        }

        // Write down balance after transaction
        $this->balance_after = $account->balance;

        if ($this->save()) {
            return $account->save();
        }

        return false;
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
