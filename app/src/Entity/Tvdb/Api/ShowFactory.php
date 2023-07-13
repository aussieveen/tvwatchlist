<?php

namespace App\Entity\Tvdb\Api;

class ShowFactory
{
    public function create(Array $show): ?Show
    {
        if(empty($show)) {
            return null;
        }

        if($show['type'] !== 'series') {
            return null;
        }

        return new Show(
            $show['tvdb_id'],
            $show['translations']['eng'] ?? $show['name'],
            $show['overviews']['eng'] ?? $show['overview'] ??'No overview available',
            $show['image_url'] ?? '',
            $show['year'] ?? null
        );
    }
}