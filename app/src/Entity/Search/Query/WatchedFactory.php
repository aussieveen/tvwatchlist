<?php

namespace App\Entity\Search\Query;

readonly class WatchedFactory
{
    public function build(): Watched
    {
        return new Watched(true);
    }
}