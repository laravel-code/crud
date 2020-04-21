<?php

namespace LaravelCode\Crud\Contracts;

interface ChainedEvents
{
    public function setRequestParams(array $params);

    public function addModelId($name, $value = null);

    public function getModelIds(): array;

    public function getRequestParams(): array;
}
