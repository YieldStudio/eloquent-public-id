<?php

namespace YieldStudio\EloquentPublicId\Test\Models;

use Illuminate\Database\Eloquent\Model;
use YieldStudio\EloquentPublicId\HasPublicId;

class Category extends Model
{
    use HasPublicId;

    public $timestamps = false;
}