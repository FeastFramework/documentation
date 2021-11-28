<?php

declare(strict_types=1);

namespace Services\Github\Responses;



use Feast\Collection\Set;
use Feast\Json;

class Folder
{
    public readonly Set $files;

    /**
     * @param array<\stdClass> $folder
     * @throws \Feast\Exception\ServerFailureException
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public function __construct(array $folder) {
        $items = [];
        
        foreach($folder as $file) {
            $item = Json::unmarshal(json_encode($file), File::class);
            $items[] = $item;
        }
        
        $this->files = new Set(File::class,$items,true,true);
    }
}