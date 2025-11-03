<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class EntityDeserializer
{
    public function __construct(
        private readonly SerializerInterface $serializer,
    ) {
    }

    public function deserialize(
        Request $request,
        string $entityClass,
        mixed $sourceObject = null,
        ?string $group = null
    ): mixed {
        $context = [
            AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false,
        ];

        if ($sourceObject) {
            $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $sourceObject;
        }

        if ($group) {
            $context[AbstractNormalizer::GROUPS] = [$group];
        }

        try {
            return $this->serializer->deserialize(
                $request->getContent(),
                $entityClass,
                'json',
                $context
            );
        } catch (ExceptionInterface $e) {
            throw new \RuntimeException('Deserialization error: ' . $e->getMessage(), 0, $e);
        }
    }
}
