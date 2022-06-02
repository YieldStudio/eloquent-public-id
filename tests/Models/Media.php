<?php

namespace YieldStudio\EloquentPublicId\Test\Models;

use Illuminate\Database\Eloquent\Model;
use YieldStudio\EloquentPublicId\HasPublicId;

class Media extends Model
{
    use HasPublicId;

    public $timestamps = false;

    public function getPublicIdName(): string
    {
        return 'uuid';
    }
}
