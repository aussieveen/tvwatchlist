<?php

declare(strict_types=1);

namespace App\Document;

use DateTimeInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ODM\Document]
class History
{
    public const string READ_GROUP = 'history:read';
    public const string WRITE_GROUP = 'history:write';
    #[ODM\Id(type: 'integer', strategy: 'INCREMENT')]
    private int $id;

    public function getId(): int
    {
        return $this->id;
    }

    #[Groups([self::READ_GROUP, self::WRITE_GROUP])]
    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    public string $seriesTitle;

    #[Groups([self::READ_GROUP, self::WRITE_GROUP])]
    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    public string $episodeTitle;

    #[Groups([self::READ_GROUP, self::WRITE_GROUP])]
    #[ODM\Field(type: 'string')]
    public ?string $universe;

    #[Groups([self::READ_GROUP, self::WRITE_GROUP])]
    #[ODM\Field(type: 'date')]
    #[Assert\NotBlank]
    public DateTimeInterface $airDate;

    #[Groups([self::READ_GROUP, self::WRITE_GROUP])]
    #[ODM\Field(type: 'date')]
    #[Assert\NotBlank]
    public DateTimeInterface $watchedAt;
}
