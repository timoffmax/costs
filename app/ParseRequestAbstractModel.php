<?php
declare(strict_types=1);

namespace App;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Class ParseRequestAbstractModel
 */
abstract class ParseRequestAbstractModel extends Model
{
    protected const DEFAULT_PAGE_SIZE = 50;

    /**
     * Returns array of entities, filtered and paginated
     *
     * @param Request $request
     * @param User|null $user
     * @return LengthAwarePaginator|Builder[]|Collection
     */
    public static function getAll(Request $request, ?User $user = null)
    {
        if (!empty($user)) {
            $query = static::getUserModels($user);
        } else {
            $query = static::getAllModels();
        }

        // Filter values
        $query = static::applyQueryFilters($request, $query);
        $query = static::applyColumnFilters($request, $query);

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
                    $query = $query->latest()
                        ->orderBy('id', 'DESC')
                    ;
                    break;

                case 'dateIdDesc':
                    $query = $query->latest('date')
                        ->orderBy('id', 'DESC')
                    ;
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
     * @return LengthAwarePaginator|Builder[]|Collection
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
     * Applies filters from the query string
     *
     * @param Request $request
     * @param Builder $query
     * @return Builder
     */
    protected static function applyQueryFilters(Request $request, Builder $query): Builder
    {
        if (defined('static::FIELDS')) {
            foreach (static::FIELDS as $fieldName) {
                $filterValue = $request[$fieldName] ?? null;

                if (null === $filterValue || 'null' === $filterValue) {
                    continue;
                }

                $decodedValue = json_decode($filterValue);

                if (is_array($decodedValue) && count($decodedValue) === 2) {
                    $query->whereBetween($fieldName, $decodedValue);
                } elseif (is_array($decodedValue) && count($decodedValue) > 2) {
                    $query->whereIn($fieldName, $decodedValue);
                } else {
                    $query->where([$fieldName => $filterValue]);
                }
            }
        }

        return $query;
    }

    /**
     * Processes filters from the request
     *
     * @param Request $request
     * @param Builder $query
     * @return Builder
     */
    protected static function applyColumnFilters(Request $request, Builder $query): Builder
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
