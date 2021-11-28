<?php

declare(strict_types=1);

namespace Services\Github\Responses;



use Feast\Collection\Set;
use Feast\Json;

class Releases
{
    public readonly Set $releases;

    /**
     * @param array<\stdClass> $releases
     * @throws \Feast\Exception\ServerFailureException
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public function __construct(array $releases) {
        $items = [];
        
        foreach($releases as $release) {
            $item = Json::unmarshal(json_encode($release), Release::class);
            $items[] = $item;
        }
        
        $this->releases = new Set(Release::class,$items,true,true);
    }
}