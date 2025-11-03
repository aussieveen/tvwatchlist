<?php

declare(strict_types=1);

namespace App\Document;

use DateTimeInterface;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ODM\Document(
    indexes: [
        new ODM\Index(
            keys: ['seriesTitle' => 'asc', 'season' => 'asc', 'episode' => 'asc'],
            unique: true
        ),
    ]
)]
#[ODM\HasLifecycleCallbacks]
#[Unique(
    fields: ['seriesTitle', 'season', 'episode'],
    message: 'Series, Season and Episode combination should be unique'
)]
class Episode
{
    public const string READ_GROUP = 'episode:read';
    public const string WRITE_GROUP = 'episode:write';
    #[Groups([self::READ_GROUP, 'identifier'])]
    #[ODM\Id(type: 'integer', strategy: 'INCREMENT')]
    private int $id;

    #[Groups([self::READ_GROUP])]
    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    public string $title;

    #[Groups([self::READ_GROUP])]
    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    public string $description;

    #[Groups([self::READ_GROUP])]
    #[ODM\Field(type: 'integer')]
    #[Assert\NotBlank]
    public int $season;

    #[Groups([self::READ_GROUP])]
    #[ODM\Field(type: 'integer')]
    #[Assert\NotBlank]
    public int $episode;

    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    public string $tvdbEpisodeId;

    #[Groups([self::READ_GROUP])]
    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    public string $seriesTitle;

    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    public string $tvdbSeriesId;

    #[Groups([self::READ_GROUP])]
    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    #[Assert\Url]
    public string $poster;

    #[Groups([self::READ_GROUP])]
    #[ODM\Field(type: 'string')]
    public string $universe;

    #[Groups([self::READ_GROUP])]
    #[ODM\Field(type: 'string')]
    #[Assert\Choice(callback: [EpisodePlatforms::class, 'values'])]
    public string $platform;

    #[Groups([self::READ_GROUP])]
    #[ODM\Field(type: 'string')]
    #[Assert\Choice(callback: [EpisodeStatus::class, 'values'])]
    public string $status;

    #[Groups([self::READ_GROUP])]
    #[ODM\Field(type: 'date')]
    public ?DateTimeInterface $airDate;

    #[Groups([self::READ_GROUP, self::WRITE_GROUP])]
    #[ODM\Field(type: 'boolean')]
    public bool $watched = false;

    public function getId(): int
    {
        return $this->id;
    }
}
