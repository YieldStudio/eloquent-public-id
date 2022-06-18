<?php

namespace YieldStudio\EloquentPublicId\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use YieldStudio\EloquentPublicId\HasPublicId;

class Tag extends Model
{
    use HasPublicId;

    public $timestamps = false;
}
