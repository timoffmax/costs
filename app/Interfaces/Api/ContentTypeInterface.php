<?php
declare(strict_types=1);

namespace App\Interfaces\Api;

/**
 * Describes available Content-type header values
 */
interface ContentTypeInterface
{
    public const TYPE_JSON = 'application/json';
    public const TYPE_FORM_URLENCODED = 'application/x-www-form-urlencoded';
}
