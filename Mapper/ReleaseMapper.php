<?php

declare(strict_types=1);

namespace Mapper;

use \Feast\BaseMapper;
use Feast\Collection\Set;
use Feast\Exception\InvalidArgumentException;
use \Model\Release;

class ReleaseMapper extends BaseMapper
{
    protected const OBJECT_NAME = Release::class;
    protected const PRIMARY_KEY = 'release_id';
    public const TABLE_NAME = 'releases';

    /**
     * @param int|string $value
     * @param bool $validate
     * @return ?Release
     */
    public function findByPrimaryKey(int|string $value, bool $validate = false): ?Release
    {
        $record = parent::findByPrimaryKey($value, $validate);
        if ($record instanceof Release) {
            return $record;
        }

        return null;
    }

    /**
     * This method is called when a Model is saved.
     *
     * Update this to call actions on save.
     *
     * @param Release $record
     * @param bool $new
     */
    protected function onSave(\Feast\BaseModel|Release $record, bool $new = true): void
    {
    }

    /**
     * This method is called when a Model is deleted.
     *
     * Update this to call actions on deletion.
     *
     * @param Release $record
     */
    protected function onDelete(\Feast\BaseModel|Release $record): void
    {
    }

    public function saveFromGithub(\Services\Github\Responses\Release $release): Release
    {
        if ($release->tagName === null || $release->name === null || $release->url === null) {
            throw new InvalidArgumentException('Invalid release received');
        }

        /** @var Release $dbRelease */
        $dbRelease = $this->findOneByField('tag', $release->tagName) ?? new Release();
        $dbRelease->prerelease = (int)$release->prerelease;
        $dbRelease->name = $release->name;
        $dbRelease->tag = $release->tagName;
        $dbRelease->tar_link = (string)$release->tarballUrl;
        $dbRelease->zip_link = (string)$release->zipballUrl;
        $dbRelease->created_at = $release->createdAt;
        $dbRelease->published_at = $release->publishedAt;
        $dbRelease->url = $release->url;

        // Break the version apart into major.minor.patch
        [$major, $minor,$patch] = explode('.', $release->name);
        if ( str_contains($patch, '-')) {
            [$patch,] = explode('-',$patch);
        }
        $dbRelease->minor_version = $major . '.' . $minor;
        $dbRelease->sortable_version = str_pad(substr($major,1),5,'0', STR_PAD_LEFT) . str_pad($minor,5,'0', STR_PAD_LEFT). str_pad($patch,5,'0', STR_PAD_LEFT);
        $dbRelease->save();

        return $dbRelease;
    }

    public function fetchLatest(): ?Release
    {
        $release = $this->fetchOne(
            $this->getQueryBase()->where('prerelease = ?', 0)->orderBy('sortable_version desc')->limit(1)
        );
        if ($release instanceof Release) {
            return $release;
        }
        return null;
    }

    public function getReleasesForDocs(): Set
    {
        $currentVersion = null;
        $items = [];
        $releases = $this->fetchAll(
            $this->getQueryBase()
                ->where('prerelease = 0')
                ->orderBy('sortable_version desc')
        );
        
        /** @var Release $release */
        foreach($releases as $release) {
            if ( $release->minor_version === $currentVersion ) {
                continue;
            }
            $items[] = $release;
            $currentVersion = $release->minor_version;
        }

        return new Set(self::class,$items,preValidated: true);
    }

}
