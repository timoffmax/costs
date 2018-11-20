<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;


abstract class ParseRequestAbstractModel extends Model
{
    protected const DEFAULT_PAGE_SIZE = 50;

    /**
     * Returns array of accounts, filtered and paginated
     *
     * @param Request $request
     * @param User|null $user
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getAll(Request $request, ?User $user = null)
    {
        if (!empty($user)) {
            $query = static::getUserModels($user);
        } else {
            $query = static::getAllModels();
        }

        // Sort
        $query = static::applySort($request, $query);

        // Paginate
        $result = static::applyPagination($request, $query);

        return $result;
    }

    /**
     * Apply sort related to request params
     *
     * @param Request $request
     * @param Builder $query
     * @return Builder
     */
    protected static function applySort(Request $request, Builder $query)
    {
        if (!empty($request['sort'])) {
            switch ($request['sort']) {
                case 'latest':
                    $query = $query->latest();
                    break;

                default:
                    // No default sort
            }

        }

        return $query;
    }

    abstract protected static function getAllModels();
    abstract protected static function getUserModels(User $user);

    /**
     * Apply pagination related to request params
     *
     * @param Request $request
     * @param Builder $query
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected static function applyPagination(Request $request, Builder $query)
    {
        if (!empty($request['page'])) {
            $pageSize = $request['pageSize'] ?? self::DEFAULT_PAGE_SIZE;
            $result = $query->paginate($pageSize);
        } else {
            $result = $query->get();
        }

        return $result;
    }
}
