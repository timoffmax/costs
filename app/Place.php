<?php

namespace App;

/**
 * @property string $name
 * @property int $user_id
 * @property bool $is_archived
 * @property User $user
 */
class Place extends ParseRequestAbstractModel
{
    public const ID = 'id';
    public const NAME = 'name';
    public const USER_ID = 'user_id';
    public const IS_ARCHIVED = 'is_archived';

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
        self::NAME,
        self::USER_ID,
        self::IS_ARCHIVED,
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
        $result = self::with('user');

        return $result;
    }

    /**
     * Get places of particular user
     *
     * @param User $user
     * @return Place|\Illuminate\Database\Eloquent\Builder
     */
    protected static function getUserModels(User $user)
    {
        $result = self::getAllModels();
        $result->orderBy(self::IS_ARCHIVED);
        $result->where([self::USER_ID => $user->id]);

        return $result;
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
