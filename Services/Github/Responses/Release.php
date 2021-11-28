<?php

declare(strict_types=1);

namespace Services\Github\Responses;



use Feast\Attributes\JsonItem;
use Feast\Date;

/** @psalm-suppress MissingConstructor */
class Release
{
    public ?string $url = null;
    
    #[JsonItem('assets_url')]
    public ?string $assetsUrl = null;


    #[JsonItem('upload_url')]
    public ?string $uploadUrl = null;
    public ?string $htmlUrl = null;
    
    public ?int $id = null;
    
    #[JsonItem]
    public Author $author;

    #[JsonItem('node_id')]
    public ?string $nodeId = null;

    #[JsonItem('tag_name')]
    public ?string $tagName = null;

    #[JsonItem('target_commitish')]
    public ?string $targetCommitish = null;
    
    public ?string $name = null;
    
    public ?bool $draft = null;
    
    public ?bool $prerelease = null;

    #[JsonItem('created_at')]
    public Date $createdAt;

    #[JsonItem('published_at')]
    public Date $publishedAt;

    #[JsonItem('tarball_url')]
    public ?string $tarballUrl = null;

    #[JsonItem('zipball_url')]
    public ?string $zipballUrl = null;
    
    public ?string $body = null;

}