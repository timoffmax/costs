<?php
declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * User account model
 *
 * @property int $id
 * @property string $name
 * @property int $type_id
 * @property int $user_id
 * @property float $balance
 * @property bool $calculate_costs
 * @property bool $is_archived
 * @property AccountType $type
 * @property User $user
 * @property Currency $currency
 */
class Account extends ParseRequestAbstractModel
{
    public const ID = 'id';
    public const NAME = 'name';
    public const TYPE_ID = 'type_id';
    public const CURRENCY_ID = 'currency_id';
    public const USER_ID = 'user_id';
    public const BALANCE = 'balance';
    public const CALCULATE_COSTS = 'calculate_costs';
    public const IS_ARCHIVED = 'is_archived';

    public const FIELDS = [
        self::ID,
        self::NAME,
        self::TYPE_ID,
        self::CURRENCY_ID,
        self::USER_ID,
        self::BALANCE,
        self::CALCULATE_COSTS,
        self::IS_ARCHIVED,
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
     * @inheritdoc
     */
    protected $fillable = [
        self::NAME,
        self::TYPE_ID,
        self::USER_ID,
        self::CURRENCY_ID,
        self::BALANCE,
        self::CALCULATE_COSTS,
        self::IS_ARCHIVED,
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
     * @return Transaction|Builder
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
     * @return Transaction|Builder
     */
    protected static function getUserModels(User $user)
    {
        $result = self::getAllModels();
        $result->orderBy(self::IS_ARCHIVED);
        $result->orderBy(self::NAME);
        $result->where([self::USER_ID => $user->id]);

        return $result;
    }

    /**
     * Get the account owner
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get type of the account
     *
     * @return BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(AccountType::class);
    }

    /**
     * Get currency of the account
     *
     * @return BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
