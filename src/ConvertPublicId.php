<?php

declare(strict_types=1);

namespace YieldStudio\EloquentPublicId;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;

trait ConvertPublicId
{
    public function prepareForValidation()
    {
        if ((bool) class_parents($this)) {
            parent::prepareForValidation();
        }

        $this->merge($this->convertPublicId($this->all(), $this->getPublicIdsToConvertAttributes()));
    }

    public function getPublicIdsToConvertAttributes(): array
    {
        return $this->publicIdsToConvert ?? [];
    }

    /**
     * @throws NotFoundModel
     */
    private function resolvePublicId(array $data, string $mapping, string $key, $value)
    {
        // If the Model isn't found, try to use as a key to get Morph relationship type
        if (! class_exists($mapping)) {
            $mapping = Arr::get($data, $mapping);
        }

        // If the Morph relationship type not matching with a class name, check in Morph map
        if ($mapping && ! class_exists($mapping)) {
            $mapping = Relation::getMorphedModel($mapping);
        }

        if (! $mapping || ! class_exists($mapping)) {
            throw new NotFoundModel($key, $mapping);
        }

        return $mapping::findByPublicId($value, ['id']);
    }

    /**
     * @throws NotFoundModel
     */
    private function convertPublicId(array $data, $attributesMapping): array
    {
        $attributesMapping = $this->expand($attributesMapping);
        foreach ($attributesMapping as $key => $mapping) {
            // Handle splat
            if ($key === '*') {
                foreach ($data as $splatIndex => $value) {
                    if (blank($value)) {
                        continue;
                    }

                    if (is_array($value)) {
                        $data[$splatIndex] = $this->convertPublicId($value, $mapping);

                        continue;
                    }

                    if ($model = $this->resolvePublicId($data, $mapping, (string) $splatIndex, $value)) {
                        $data[$splatIndex] = $model->id;
                    }
                }

                continue;
            }

            $value = Arr::get($data, $key);
            if (blank($value)) {
                continue;
            }

            if (is_array($mapping)) {
                $data[$key] = $this->convertPublicId($data[$key], $mapping);

                continue;
            }

            if ($model = $this->resolvePublicId($data, $mapping, $key, $value)) {
                $data[$key] = $model->id;
            }
        }

        return $data;
    }

    private function expand(array $input): array
    {
        $output = [];
        foreach ($input as $key => $value) {
            Arr::set($output, $key, $value);
        }

        return $output;
    }
}
