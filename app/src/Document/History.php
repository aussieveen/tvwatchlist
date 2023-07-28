<?php

namespace App\Document;

use ApiPlatform\Metadata\ApiResource;
use DateTimeInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ODM\Document]
#[ApiResource(
    normalizationContext: [
        'groups' => ['history:read'],
        'skip_null_values' => true,
        'allow_extra_attributes' => false
    ],
    denormalizationContext: [
        'groups' => ['history:write']
    ],
    order: ['id' => 'DESC']
)]
class History
{
    #[ODM\Id(type: 'integer', strategy: 'INCREMENT')]
    private int $id;

    public function getId(): int
    {
        return $this->id;
    }

    #[Groups(['history:read','history:write'])]
    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    public string $showTitle;

    #[Groups(['history:read','history:write'])]
    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    public string $episodeTitle;

    #[Groups(['history:read','history:write'])]
    #[ODM\Field(type: 'date')]
    #[Assert\NotBlank]
    public DateTimeInterface $airDate;

    #[Groups(['history:read','history:write'])]
    #[ODM\Field(type: 'date')]
    #[Assert\NotBlank]
    public DateTimeInterface $watchedAt;
}
