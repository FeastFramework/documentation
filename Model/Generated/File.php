<?php

declare(strict_types=1);

namespace Model\Generated;

use \Feast\BaseModel;
use \Mapper\FileMapper;

class File extends BaseModel
{
    protected const MAPPER_NAME = FileMapper::class;

    public int $file_id;
    public string $name;
    public string $sha;
    public string $url;
    public string $content;
    public string $html;

}
