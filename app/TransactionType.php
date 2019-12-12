<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property Transaction[] $transactions
 */
class TransactionType extends Model
{
    /**
     * Available types
     */
    public const TYPE_INCOME = 'income';
    public const TYPE_COST = 'cost';
    public const TYPE_TRANSFER = 'transfer';

    /**
     * Field names
     */
    public const ID = 'id';
    public const NAME = 'name';

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
    protected $table = 'transaction_type';

    /**
     * @var array
     */
    protected $fillable = [self::NAME];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany('App\Transaction', 'type_id');
    }
}
