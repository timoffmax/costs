<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Transaction
 *
 * @property int $type_id
 * @property int $user_id
 * @property float $sum
 * @property string $date
 * @property string $comment
 * @property TransactionType $type
 * @property User $user
 */
class Transaction extends Model
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
        'type_id', 'user_id', 'sum', 'date', 'comment',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];


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
     * Get type of the transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(TransactionType::class);
    }
}
