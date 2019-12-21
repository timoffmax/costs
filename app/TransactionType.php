<?php
declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $label
 * @property Transaction[] $transactions
 */
class TransactionType extends Model
{
    /**
     * @var string
     */
    public const ENTITY_TABLE = 'transaction_type';

    /**
     * Available types
     */
    public const TYPE_INCOME = 'income';
    public const TYPE_COST = 'cost';
    public const TYPE_TRANSFER = 'transfer';
    public const TYPE_DEPOSIT = 'deposit';
    public const TYPE_MONEYBOX = 'moneybox';
    public const TYPE_SAVING = 'saving';

    /**
     * @var array
     */
    public const TRANSFERABLE_TYPES = [
        self::TYPE_TRANSFER,
        self::TYPE_DEPOSIT,
        self::TYPE_MONEYBOX,
        self::TYPE_SAVING,
    ];

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
    public function transactions()
    {
        return $this->hasMany(Transaction::class, Transaction::TYPE_ID);
    }
}
