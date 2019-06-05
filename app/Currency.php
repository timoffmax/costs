<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

/**
 * Class Currency
 *
 * @property string $name
 * @property int $sign
 * @property Account[] $accounts
 */
class Currency extends Model
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
    protected $table = 'currency';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'sign',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get accounts with this currency
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accounts()
    {
        return $this->hasMany(Account::class);
    }
}
