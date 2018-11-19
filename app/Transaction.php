<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
class Transaction extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transaction';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type_id', 'account_id', 'sum', 'date', 'comment',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Returns array of transactions, filtered and paginated
     *
     * @param Request $request
     * @param User|null $user
     * @return Transaction|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder
     */
    public static function getTransactions(Request $request, ?User $user = null)
    {
        $pageSize = $request['pageSize'] ?? self::DEFAULT_PAGE_SIZE;

        if (!empty($user)) {
            $result = self::getUserTransactions($user);
        } else {
            $result = self::getAllTransactions();
        }

        $result = $result->latest();
        $result = $result->paginate($pageSize);

        return $result;
    }

    /**
     * Get transactions of all users
     *
     * @return Transaction|\Illuminate\Database\Eloquent\Builder
     */
    protected static function getAllTransactions()
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
    protected static function getUserTransactions(User $user)
    {
        return self::with('user')
            ->with('account')
            ->with('type')
            ->where(['user_id' => $user])
        ;
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
