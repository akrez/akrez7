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

    public function prepareForValidation()
    {
        return $this->data();
    }

    public function messages()
    {
        return [];
    }

    public function attributes()
    {
        return [];
    }

    public function validate($context = null): Validator
    {
        return validator(
            $this->prepareForValidation(),
            $this->rules($context),
            $this->messages(),
            $this->attributes()
        );
    }
}
