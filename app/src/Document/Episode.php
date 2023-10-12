<?php

namespace App\Document;

use ApiPlatform\Metadata\ApiResource;
use DateTimeInterface;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ODM\Document(
    indexes: [
        new ODM\Index(
            keys: ['showTitle' => 'asc', 'season' => 'asc', 'episode' => 'asc'],
            unique: true
        )
    ]
)]
#[ODM\HasLifecycleCallbacks]
#[Unique(fields: ['showTitle', 'season', 'episode'], message: 'Show, Season and Episode combination should be unique')]
#[ApiResource(
    normalizationContext: [
        'groups' => ['episode:read'],
        'skip_null_values' => true,
        'allow_extra_attributes' => false
    ],
    denormalizationContext: [
        'groups' => ['episode:write']
    ],
    order: ['airDate' => 'ASC']
)]
class Episode
{
    public final const VALID_STATUSES = [
        1 => 'airing', 2 => 'finished', 3 => 'upcoming'
    ];
    final const AVAILABLE_PLATFORMS = ['Plex','Netflix','Disney Plus','Amazon Prime'];

    #[Groups(['episode:read', 'history:read', 'identifier'])]
    #[ODM\Id(type: 'integer', strategy: 'INCREMENT')]
    private int $id;

    #[Groups(['episode:read','episode:write', 'history:read'])]
    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    public string $title;

    #[Groups(['episode:read','episode:write'])]
    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    public string $description;

    #[Groups(['episode:read','episode:write'])]
    #[ODM\Field(type: 'integer')]
    #[Assert\NotBlank]
    public int $season;

    #[Groups(['episode:read','episode:write'])]
    #[ODM\Field(type: 'integer')]
    #[Assert\NotBlank]
    public int $episode;

    #[Groups(['episode:read', 'episode:write'])]
    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    public string $showTitle;

    #[Groups(['episode:read','episode:write'])]
    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    #[Assert\Url]
    public string $poster;

    #[Groups(['episode:read','episode:write'])]
    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    public string $universe;

    #[Groups(['episode:read', 'episode:write'])]
    #[ODM\Field(type: 'string')]
    #[Assert\Choice(choices: self::AVAILABLE_PLATFORMS)]
    public string $platform;

    #[Groups(['episode:read', 'episode:write'])]
    #[ODM\Field(type: 'string')]
    #[Assert\Choice(choices: self::VALID_STATUSES)]
    public string $status;

    #[Groups(['episode:read','episode:write'])]
    #[ODM\Field(type: 'date')]
    #[Assert\NotBlank]
    public DateTimeInterface $airDate;

    #[Groups(['episode:read','episode:write'])]
    #[ODM\Field(type: 'boolean')]
    public bool $watched = false;

    public function getId(): int
    {
        return $this->id;
    }
}
