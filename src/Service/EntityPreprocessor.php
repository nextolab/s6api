<?php

namespace App\Service;

use App\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EntityPreprocessor
{
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function populateFromRequest(object $entity, Request $request): void
    {
        $this->serializer->deserialize($request->getContent(), $entity::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $entity,
        ]);
    }

    public function validate(object $entity, mixed $constraints = null, mixed $groups = null): void
    {
        $constraintViolations = $this->validator->validate($entity, $constraints, $groups);

        if ($constraintViolations->count()) {
            $errors = [];

            foreach ($constraintViolations as $constraintViolation) {
                $errors[$constraintViolation->getPropertyPath()] = $constraintViolation->getMessage();
            }

            throw new ValidationException('Request contains a not valid data.', $errors);
        }
    }
}
