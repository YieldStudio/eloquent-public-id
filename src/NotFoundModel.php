<?php

namespace YieldStudio\EloquentPublicId;

use Exception;

final class NotFoundModel extends Exception
{
    public function __construct($key, $modelClass)
    {
        parent::__construct("[EloquentPublicId] Not found `$modelClass` Model for `$key`.");
    }
}
