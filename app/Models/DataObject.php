<?php
declare(strict_types=1);

namespace App\Models;

use App\Interfaces\DataObjectInterface;

/**
 * Universal data container with array access implementation
 *
 * Simplified version of Magento data object
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class DataObject implements \ArrayAccess, DataObjectInterface
{
    /**
     * Object attributes
     *
     * @var array
     */
    protected $data = [];

    /**
     * Setter/Getter underscore transformation cache
     *
     * @var array
     */
    protected static $underscoreCache = [];

    /**
     * DataObject constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Set/Get attribute wrapper
     *
     * @param   string $method
     * @param   array $args
     * @return  mixed
     * @throws \Exception
     */
    public function __call($method, $args)
    {
        switch (substr($method, 0, 3)) {
            case 'get':
                $key = $this->underscore(substr($method, 3));
                return $this->getData($key);

            case 'set':
                $key = $this->underscore(substr($method, 3));
                $value = isset($args[0]) ? $args[0] : null;
                return $this->setData($key, $value);

            case 'has':
                $key = $this->underscore(substr($method, 3));
                return isset($this->data[$key]);
        }

        throw new \Exception("Invalid method " . get_class($this) . "::{$method}");
    }

    /**
     * @inheritdoc
     */
    public function setData($key, $value = null): DataObjectInterface
    {
        if ($key === (array)$key) {
            $this->data = $key;
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getData(string $key = '')
    {
        if ('' === $key) {
            $result = $this->data;
        } else {
            $result = $this->getDataByKey($key);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function hasData(string $key = ''): bool
    {
        if (empty($key) || !is_string($key)) {
            return !empty($this->data);
        }

        return array_key_exists($key, $this->data);
    }

    /**
     * @inheritdoc
     */
    public function toArray(array $keys = []): array
    {
        if (empty($keys)) {
            return $this->data;
        }

        $result = [];

        foreach ($keys as $key) {
            if (isset($this->data[$key])) {
                $result[$key] = $this->data[$key];
            } else {
                $result[$key] = null;
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function toJson(array $keys = [])
    {
        $data = $this->toArray($keys);
        $result = json_encode($data);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]) || array_key_exists($offset, $this->data);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        if (isset($this->data[$offset])) {
            return $this->data[$offset];
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getDataRecursively(): array
    {
        $result = [];
        $allData = $this->getData();

        foreach ($allData as $key => $value) {
            if ($value instanceof DataObjectInterface) {
                $value = $value->getDataRecursively();
            } elseif (is_array($value)) {
                foreach ($value as $valueKey => $valueItem) {
                    if ($valueItem instanceof DataObjectInterface) {
                        $value[$valueKey] = $valueItem->getDataRecursively();
                    } else {
                        $value[$valueKey] = $valueItem;
                    }
                }
            }

            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * Get object data by particular key
     *
     * @param string $key
     * @return mixed
     */
    protected function getDataByKey($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return null;
    }

    /**
     * Converts field names for setters and getters
     *
     * $this->setMyField($value) === $this->setData('my_field', $value)
     * Uses cache to eliminate unnecessary preg_replace
     *
     * @param string $name
     * @return string
     */
    protected function underscore($name)
    {
        if (isset(self::$underscoreCache[$name])) {
            return self::$underscoreCache[$name];
        }

        $result = strtolower(trim(preg_replace('/([A-Z]|[0-9]+)/', "_$1", $name), '_'));
        self::$underscoreCache[$name] = $result;

        return $result;
    }
}
