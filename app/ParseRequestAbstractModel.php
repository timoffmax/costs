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

        // Filter values
        $query = static::applyFilters($request, $query);

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
    protected static function applySort(Request $request, Builder $query): Builder
    {
        $field = $request['sortField'] ?? null;
        $type = $request['sortType'] ?? 'ASC';

        if (!empty($field)) {
            $query = $query->orderBy($field, $type);
        } elseif (!empty($request['sort'])) {
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
            $pageSize = $request['perPage'] ?? self::DEFAULT_PAGE_SIZE;
            $result = $query->paginate($pageSize);
        } else {
            $result = $query->get();
        }

        return $result;
    }

    /**
     * Processes filters from the request
     *
     * @param Request $request
     * @param Builder $query
     * @return Builder
     */
    protected static function applyFilters(Request $request, Builder $query): Builder
    {
        $filters = $request->get('columnFilters');

        if (!empty($filters)) {
            $filters = json_decode($filters, true);
        }

        if (is_array($filters)) {
            foreach ($filters as $column => $value) {
                if (is_array($value)) {
                    // Support of nested object with extended settings
                    $operatorType = $value['operatorType'] ?? '=';
                    $valueToSearch = $value['value'] ?? null;
                } else {
                    $operatorType = '=';
                    $valueToSearch = $value;
                }

                if (!empty($valueToSearch)) {
                    $query->where($column, $operatorType, $valueToSearch);
                }
            }
        }

        return $query;
    }

    /**
     * @return mixed
     */
    abstract protected static function getAllModels();

    /**
     * @param User $user
     * @return mixed
     */
    abstract protected static function getUserModels(User $user);
}
