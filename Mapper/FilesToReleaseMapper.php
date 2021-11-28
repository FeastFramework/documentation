<?php

declare(strict_types=1);

namespace Mapper;

use \Feast\BaseMapper;
use \Model\FilesToRelease;

class FilesToReleaseMapper extends BaseMapper
{
    protected const OBJECT_NAME = FilesToRelease::class;
    protected const PRIMARY_KEY = 'id';
    public const TABLE_NAME = 'files_to_releases';

    /**
     * @param int|string $value
     * @param bool $validate
     * @return ?FilesToRelease
     */
    public function findByPrimaryKey(int|string $value, bool $validate = false): ?FilesToRelease
    {
        $record = parent::findByPrimaryKey($value, $validate);
        if ($record instanceof FilesToRelease) {
            return $record;
        }

        return null;
    }

    /**
     * This method is called when a Model is saved.
     *
     * Update this to call actions on save.
     *
     * @param FilesToRelease $record
     * @param bool $new
     */
    protected function onSave(\Feast\BaseModel|FilesToRelease $record, bool $new = true): void
    {
    }

    /**
     * This method is called when a Model is deleted.
     *
     * Update this to call actions on deletion.
     *
     * @param FilesToRelease $record
     */
    protected function onDelete(\Feast\BaseModel|FilesToRelease $record): void
    {
    }
    
}
