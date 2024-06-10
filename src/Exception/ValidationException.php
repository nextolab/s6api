<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ValidationException extends HttpException
{
    private array $errors;

    public function __construct(string $message = '', array $errors = [])
    {
        $this->errors = $errors;
        parent::__construct(422, $message);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
