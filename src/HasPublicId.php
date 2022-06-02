<?php

namespace YieldStudio\EloquentPublicId;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasPublicId
{
    public static function bootHasPublicId()
    {
        static::creating(function (Model $model) {
            if (! $model->getPublicId()) {
                $model->setAttribute($model->getPublicIdName(), $model->generatePublicId());
            }
        });
    }

    public static function findByPublicId(string $publicId, array $columns = ['*']): ?self
    {
        return static::query()->select($columns)->where((new static())->getPublicIdName(), $publicId)->first();
    }

    public function scopeWherePublicId($query, string $publicId): Builder
    {
        return $query->where($this->getPublicIdName(), $publicId);
    }

    public function getHidden(): array
    {
        return [...$this->hidden, $this->getKeyName()];
    }

    public function getGuarded(): array
    {
        if ($this->guarded === false) {
            return [];
        }

        return [...$this->guarded, $this->getPublicIdName()];
    }

    public function getPublicId(): ?string
    {
        return $this->getAttribute($this->getPublicIdName());
    }

    public function getPublicIdName(): string
    {
        return 'public_id';
    }

    public function generatePublicId(): string
    {
        return (string) Str::orderedUuid();
    }

    public function getRouteKeyName(): string
    {
        return $this->getPublicIdName();
    }
}
