<?php

namespace Salehhashemi\LaravelIntelliDb\Console;

use InvalidArgumentException;

trait ModelHelperTrait
{
    protected function qualifyModel(string $model): string
    {
        $model = ltrim($model, '\\');

        $namespaceModel = $this->laravel->getNamespace().'Models\\'.$model;

        if (class_exists($namespaceModel)) {
            return $namespaceModel;
        }

        if (class_exists($model)) {
            return $model;
        }

        throw new InvalidArgumentException("Model '{$model}' does not exist.");
    }
}
