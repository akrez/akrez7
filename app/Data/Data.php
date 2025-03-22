<?php

namespace App\Data;

use Illuminate\Validation\Validator;
use ReflectionClass;

abstract class Data
{
    abstract public function rules($context);

    public function data()
    {
        $result = [];
        //
        $class = new ReflectionClass(static::class);
        $constructor = $class->getConstructor();
        $parameters = $constructor->getParameters();
        //
        foreach ($parameters as $parameter) {
            $parameterName = $parameter->getName();
            $result[$parameterName] = $this->$parameterName;
        }

        return $result;
    }

    public function validate($context = null, $rulesParams = [], array $messages = [], array $attributes = []): Validator
    {
        foreach ($rulesParams as $rulesParamName => $rulesParamValue) {
            $this->$rulesParamName = $rulesParamValue;
        }

        return validator(
            $this->data(),
            $this->rules($context),
            $messages,
            $attributes
        );
    }
}
