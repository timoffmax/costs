<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TransactionCategoryType
 * @property int $id
 * @property int $typeId
 * @property int $transactionTypeId
 * @property string $name
 * @property TransactionCategoryType $type
 * @property TransactionType $transactionType
 * @property Transaction[] $transactions
 */
class TransactionCategory extends Model
{
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
    protected $table = 'transaction_category';

    /**
     * @var array
     */
    protected $fillable = ['name', 'type_id', 'transaction_type_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany('App\Transaction', 'category_id');
    }

    /**
     * Get type of the category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(TransactionCategoryType::class);
    }

    /**
     * Get transaction type of the category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transactionType()
    {
        return $this->belongsTo(TransactionType::class);
    }
}
