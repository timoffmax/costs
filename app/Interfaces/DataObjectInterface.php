<?php
declare(strict_types=1);

namespace App\Interfaces;

use App\Models\DataObject;

/**
 * Just a flag that model that implements this interface also extends @see DataObject
 */
interface DataObjectInterface
{
    /**
     * Object data getter
     *
     * If $key is not defined will return all the data as an array.
     * Otherwise it will return value of the element specified by $key.
     *
     * @param string $key
     * @return mixed
     */
    public function getData(string $key = '');

    /**
     * Overwrite data in the object.
     *
     * The $key parameter can be string or array.
     * If $key is string, the attribute value will be overwritten by $value
     *
     * If $key is an array, it will overwrite all the data in the object.
     *
     * @param string|array $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value = null): DataObjectInterface;

    /**
     * If $key is empty, checks whether there's any data in the object
     *
     * Otherwise checks if the specified attribute is set.
     *
     * @param string $key
     * @return bool
     */
    public function hasData(string $key = ''): bool;

    /**
     * Convert array of object data with to array with keys requested in $keys array
     *
     * @param array $keys array of required keys
     * @return array
     */
    public function toArray(array $keys = []): array;

    /**
     * Convert object data to JSON
     *
     * @param array $keys array of required keys
     * @return bool|string
     * @throws \InvalidArgumentException
     */
    public function toJson(array $keys = []);

    /**
     * Returns all the data as array
     * Also returns data of nested DataObjects as arrays
     *
     * @return array
     */
    public function getDataRecursively(): array;
}
