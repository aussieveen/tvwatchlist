<?php

declare(strict_types=1);

namespace App\Document;

use DateTimeInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ODM\Document]
/**
 * @codeCoverageIgnore
 */
class History
{
    #[ODM\Id(type: 'int', strategy: 'INCREMENT')]
    private int $id;

    public function getId(): int
    {
        return $this->id;
    }

    #[Groups(['history:read','history:write'])]
    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    public string $seriesTitle;

    #[Groups(['history:read','history:write'])]
    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    public string $episodeTitle;

    #[Groups(['history:read','history:write'])]
    #[ODM\Field(type: 'string')]
    public ?string $universe;

    #[Groups(['history:read','history:write'])]
    #[ODM\Field(type: 'date')]
    #[Assert\NotBlank]
    public DateTimeInterface $airDate;

    #[Groups(['history:read','history:write'])]
    #[ODM\Field(type: 'date')]
    #[Assert\NotBlank]
    public DateTimeInterface $watchedAt;
}
