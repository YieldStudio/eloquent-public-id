<?php

namespace YieldStudio\EloquentPublicId\Test\Models;

use Illuminate\Database\Eloquent\Model;
use YieldStudio\EloquentPublicId\HasPublicId;

class Tag extends Model
{
    use HasPublicId;

    public $timestamps = false;
}
