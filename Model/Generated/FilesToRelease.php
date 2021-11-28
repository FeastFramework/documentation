<?php

declare(strict_types=1);

namespace Model\Generated;

use \Feast\BaseModel;
use \Mapper\FilesToReleaseMapper;

class FilesToRelease extends BaseModel
{
    protected const MAPPER_NAME = FilesToReleaseMapper::class;

    public int $id;
    public int $file_id;
    public int $release_id;

}
