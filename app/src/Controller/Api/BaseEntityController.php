<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\EntityDeserializer;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseEntityController extends AbstractController
{
    public function __construct(
        private readonly DocumentManager $dm,
        private readonly EntityDeserializer $entityDeserializer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    abstract protected function getEntityClass(): string;
    abstract protected function getWriteGroup(): string;

    protected function update(int $id, Request $request): JsonResponse
    {
        $doc = $this->findEntityById($id);
        if (!$doc) {
            throw $this->createNotFoundException("Entity with ID $id not found.");
        }

        return $this->writeDocument($request, $doc);
    }

    protected function create(Request $request): JsonResponse
    {
        return $this->writeDocument($request, null);
    }

    private function writeDocument(Request $request, mixed $doc): JsonResponse
    {
        $deserializedDoc = $this->entityDeserializer->deserialize(
            $request,
            $this->getEntityClass(),
            $doc,
            $this->getWriteGroup()
        );

        $violations = $this->validator->validate($deserializedDoc);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = [
                    'property' => $violation->getPropertyPath(),
                    'value' => $violation->getInvalidValue(),
                    'message' => $violation->getMessage(),
                ];
            }
            return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
        }

        $this->dm->persist($deserializedDoc);
        $this->dm->flush();

        return $this->json($deserializedDoc, Response::HTTP_OK);
    }

    private function findEntityById(int $id): ?object
    {
        return $this->dm->find($this->getEntityClass(), $id);
    }
}
