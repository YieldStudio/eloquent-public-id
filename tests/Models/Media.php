<?php

namespace YieldStudio\EloquentPublicId\Tests\Models;

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
