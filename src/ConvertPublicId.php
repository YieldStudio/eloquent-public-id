<?php

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
    private function resolvePublicId(string $mappingValue, $key, $value, array $data)
    {
        // If the Model isn't found, try to use as a key to get Morph relationship type
        if (! class_exists($mappingValue)) {
            $mappingValue = Arr::get($data, $mappingValue);
        }

        // If the Morph relationship type not matching with a class name, check in Morph map
        if (! class_exists($mappingValue)) {
            $mappingValue = Relation::getMorphedModel($mappingValue);
        }

        if (! class_exists($mappingValue)) {
            throw new NotFoundModel($key, $mappingValue);
        }

        return $mappingValue::findByPublicId($value, ['id']);
    }

    /**
     * @throws NotFoundModel
     */
    private function convertPublicId(array $data, $mapping): array
    {
        $mapping = $this->undot($mapping);
        foreach ($mapping as $key => $modelClass) {
            // Handle splat
            if ($key === '*') {
                foreach ($data as $splatIndex => $value) {
                    if (blank($value)) {
                        continue;
                    }

                    if (is_array($value)) {
                        $data[$splatIndex] = $this->convertPublicId($value, $modelClass);

                        continue;
                    }

                    if ($model = $this->resolvePublicId($modelClass, $splatIndex, $value, $data)) {
                        $data[$splatIndex] = $model->id;
                    }
                }

                continue;
            }

            $value = Arr::get($data, $key);
            if (blank($value)) {
                continue;
            }

            if (is_array($modelClass)) {
                $data[$key] = $this->convertPublicId($data[$key], $modelClass);

                continue;
            }

            if ($model = $this->resolvePublicId($modelClass, $key, $value, $data)) {
                $data[$key] = $model->id;
            }
        }

        return $data;
    }

    private function undot(array $input): array
    {
        $output = [];
        foreach ($input as $key => $value) {
            Arr::set($output, $key, $value);
        }

        return $output;
    }
}
