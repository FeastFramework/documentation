<?php

declare(strict_types=1);

namespace Model\Generated;

use \Feast\BaseModel;
use \Mapper\ReleaseMapper;

class Release extends BaseModel
{
    protected const MAPPER_NAME = ReleaseMapper::class;

    public int $release_id;
    public string $name;
    public string $tag;
    public string $url;
    public string $zip_link;
    public string $tar_link;
    public string $minor_version;
    public string $sortable_version;
    public int $prerelease;
    public \Feast\Date $created_at;
    public \Feast\Date $published_at;

}
