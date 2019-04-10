<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TransactionCategoryType
 * @property int $id
 * @property string $name
 * @property TransactionCategory[] $categories
 */
class TransactionCategoryType extends Model
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
    protected $table = 'transaction_category_type';

    /**
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categories()
    {
        return $this->hasMany('App\TransactionCategory', 'type_id');
    }
}
