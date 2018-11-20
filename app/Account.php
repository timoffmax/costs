<?php

namespace App;


/**
 * Class Account
 *
 * @property string $name
 * @property int $type_id
 * @property int $user_id
 * @property float $balance
 * @property AccountType $type
 * @property User $user
 */
class Account extends ParseRequestAbstractModel
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
    protected $table = 'account';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'type_id', 'user_id', 'balance',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get accounts of all users
     *
     * @return Transaction|\Illuminate\Database\Eloquent\Builder
     */
    protected static function getAllModels()
    {
        return self::with('user')
            ->with('type')
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
        return self::with('type')
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
}
