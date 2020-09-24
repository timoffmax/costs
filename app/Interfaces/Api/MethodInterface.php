<?php
declare(strict_types=1);

namespace App\Models\Api;

/**
 * Describes HTTP request method types
 */
interface MethodInterface
{
    public const HTTP_METHOD_GET = 'GET';
    public const HTTP_METHOD_DELETE = 'DELETE';
    public const HTTP_METHOD_PUT = 'PUT';
    public const HTTP_METHOD_POST = 'POST';
}
