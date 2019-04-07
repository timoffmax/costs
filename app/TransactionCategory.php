<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TransactionCategoryType
 * @property int $id
 * @property int $typeId
 * @property int $transactionTypeId
 * @property string $name
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
    public function categories()
    {
        return $this->hasMany('App\Transaction', 'category_id');
    }
}
