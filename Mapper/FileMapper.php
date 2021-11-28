<?php

declare(strict_types=1);

namespace Mapper;

use \Feast\BaseMapper;
use \Model\File;
use Model\Release;

class FileMapper extends BaseMapper
{
    protected const OBJECT_NAME = File::class;
    protected const PRIMARY_KEY = 'file_id';
    public const TABLE_NAME = 'files';

    /**
     * @param int|string $value
     * @param bool $validate
     * @return ?File
     */
    public function findByPrimaryKey(int|string $value, bool $validate = false): ?File
    {
        $record = parent::findByPrimaryKey($value, $validate);
        if ($record instanceof File) {
            return $record;
        }

        return null;
    }

    /**
     * This method is called when a Model is saved.
     *
     * Update this to call actions on save.
     *
     * @param File $record
     * @param bool $new
     */
    protected function onSave(\Feast\BaseModel|File $record, bool $new = true): void
    {
    }

    /**
     * This method is called when a Model is deleted.
     *
     * Update this to call actions on deletion.
     *
     * @param File $record
     */
    protected function onDelete(\Feast\BaseModel|File $record): void
    {
    }

    public function findFileForVersion(Release $release, string $name): ?File
    {
        $file = $this->fetchOne(
            $this->getQueryBase()->where(
                'file_id IN (select file_id FROM files_to_releases WHERE release_id = ?) and name = ?',
                $release->release_id,
                $name
            )
        );
        if ($file instanceof File) {
            return $file;
        }
        return null;
    }
    
}
