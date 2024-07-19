<?php

declare(strict_types=1);

namespace YieldStudio\EloquentPublicId\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use YieldStudio\EloquentPublicId\HasPublicId;

class Category extends Model
{
    use HasPublicId;

    public $timestamps = false;
}
