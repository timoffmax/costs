<?php

namespace App;

/**
 * Class Place
 *
 * @property string $name
 * @property int $user_id
 * @property User $user
 */
class Place extends ParseRequestAbstractModel
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
    protected $table = 'place';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'user_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get places of all users
     *
     * @return Place|\Illuminate\Database\Eloquent\Builder
     */
    protected static function getAllModels()
    {
        return self::with('user');
    }

    /**
     * Get places of particular user
     *
     * @param User $user
     * @return Place|\Illuminate\Database\Eloquent\Builder
     */
    protected static function getUserModels(User $user)
    {
        return self::getAllModels()->where(['user_id' => $user->id]);
    }

    /**
     * Get the place owner
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
