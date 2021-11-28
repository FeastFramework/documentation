<?php

declare(strict_types=1);

namespace Services\Github\Responses;

use Feast\Attributes\JsonItem;

/** @psalm-suppress MissingConstructor */
class File
{
    public ?string $name = null;
    
    public ?string $path = null;
    
    public ?string $sha = null;
    
    public ?int $size = null;
    
    public ?string $url = null;
    
    #[JsonItem('html_url')]
    public ?string $htmlUrl = null;
    
    #[JsonItem('git_url')]
    public ?string $gitUrl = null;
    
    #[JsonItem('download_url')]
    public ?string $downloadUrl = null;
    
    public string $type;
    
    public FileLinks $links;
    
}