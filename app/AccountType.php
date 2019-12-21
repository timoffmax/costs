<?php
declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $label
 * @property Account[] $accounts
 */
class AccountType extends Model
{
    public const ENTITY_TABLE = 'account_type';

    /**
     * Available types
     */
    public const TYPE_CASH = 'cash';
    public const TYPE_CARD = 'card';
    public const TYPE_MONEYBOX = 'moneybox';
    public const TYPE_DEPOSIT = 'deposit';
    public const TYPE_SAVING = 'saving';

    /**
     * Field names
     */
    public const ID = 'id';
    public const NAME = 'name';
    public const LABEL = 'label';

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
    protected $table = self::ENTITY_TABLE;

    /**
     * @var array
     */
    protected $fillable = [self::NAME, self::LABEL];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accounts()
    {
        return $this->hasMany(Account::class, Account::TYPE_ID);
    }
}
