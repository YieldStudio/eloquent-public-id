<?php

declare(strict_types=1);

namespace YieldStudio\EloquentPublicId\Exceptions;

use Exception;

final class NotFoundModel extends Exception
{
    public function __construct($key, $modelClass)
    {
        parent::__construct("[EloquentPublicId] Not found `$modelClass` Model for `$key`.");
    }
}
