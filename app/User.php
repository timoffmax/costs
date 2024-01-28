<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

/**
 * Class User
 *
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $photo
 * @property int $role_id
 * @property UserRole $role
 * @property Account[] $accounts
 * @property Place[] $places
 * @method static findOrFail($userId)
 */
class User extends Authenticatable
{
    use Notifiable;
    use HasApiTokens;

    /**
     * Automatically fill 'created_at' and 'updated_at' fields
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'photo', 'role_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Load user with related data
     *
     * @var array
     */
    protected $with = ['role', 'accounts', 'places'];

    /**
     * Get role
     *
     * @return BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(UserRole::class);
    }

    /**
     * Get list of accounts
     *
     * @return HasMany
     */
    public function accounts()
    {
        $result = $this->hasMany(Account::class, Account::USER_ID);
        $result->orderBy(Account::NAME);
        $result->where([Account::IS_ARCHIVED => 0]);

        return $result;
    }

    /**
     * Get list of places
     *
     * @return HasMany
     */
    public function places()
    {
        $result = $this->hasMany(Place::class, Place::USER_ID);
        $result->orderBy(Place::NAME);
        $result->where([Place::IS_ARCHIVED => 0]);

        return $result;
    }

    /**
     * Get list of transactions
     *
     * @return HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id')
            ->latest()
            ->orderBy('id', "DESC")
        ;
    }
}
