<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ValidationException extends BadRequestHttpException
{
    private array $errors;

    public function __construct(string $message = '', array $errors)
    {
        $this->errors = $errors;
        parent::__construct($message);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
