<?php

namespace App\Serializer;

use App\Exception\ValidationException;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProblemNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        $data = [
            'code' => $object->getStatusCode(),
            'message' => $object->getMessage(),
        ];

        if (isset($context['exception']) && $context['exception'] instanceof ValidationException) {
            $data['errors'] = $context['exception']->getErrors();
        }

        if ($context['debug'] ?? false) {
            $data['class'] = $object->getClass();
            $data['trace'] = $object->getTrace();
        }

        return $data;
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof FlattenException && $format === 'json';
    }
}
