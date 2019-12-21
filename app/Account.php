<?php
declare(strict_types=1);

namespace App;

/**
 * Class Account
 *
 * @property int $id
 * @property string $name
 * @property int $type_id
 * @property int $user_id
 * @property float $balance
 * @property bool $calculate_costs
 * @property AccountType $type
 * @property User $user
 * @property Currency $currency
 */
class Account extends ParseRequestAbstractModel
{
    /**
     * Field names
     */
    public const ID = 'id';
    public const NAME = 'name';
    public const TYPE_ID = 'type_id';
    public const USER_ID = 'user_id';
    public const BALANCE = 'balance';
    public const CALCULATE_COSTS = 'calculate_costs';

    public const FIELDS = [
        self::ID,
        self::NAME,
        self::TYPE_ID,
        self::USER_ID,
        self::BALANCE,
        self::CALCULATE_COSTS,
    ];

    /**
     * Don't use 'created_at' and 'updated_at' fields
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'account';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'type_id', 'user_id', 'currency_id', 'balance', 'calculate_costs'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * @var array
     */
    protected $with = ['currency', 'type'];

    /**
     * Get accounts of all users
     *
     * @return Transaction|\Illuminate\Database\Eloquent\Builder
     */
    protected static function getAllModels()
    {
        return self::with('user')
            ->with('type')
            ->with('currency')
        ;
    }

    /**
     * Get accounts of particular user
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
     * Get the account owner
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get type of the account
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(AccountType::class);
    }

    /**
     * Get currency of the account
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
