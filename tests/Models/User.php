<?php

namespace YieldStudio\EloquentPublicId\Test\Models;

use Illuminate\Database\Eloquent\Model;
use YieldStudio\EloquentPublicId\HasPublicId;

class User extends Model
{
    use HasPublicId;

    public $timestamps = false;
}
