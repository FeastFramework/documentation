<?php

declare(strict_types=1);

namespace Modules\CLI\Controllers;

use Feast\Attributes\Action;
use Feast\CliController;
use Feast\Collection\Set;
use Mapper\FileMapper;
use Mapper\FilesToReleaseMapper;
use Mapper\ReleaseMapper;
use Model\File;
use Model\FilesToRelease;
use Model\Release;

class CacheController extends CliController
{

    private $cacheDirectory = APPLICATION_ROOT . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR;

    #[Action(description: 'Cache all')]
    public function cacheAllGet(
        FilesToReleaseMapper $filesToReleaseMapper,
        FileMapper $fileMapper,
        ReleaseMapper $releaseMapper
    ): void {
        $this->releasesGet($releaseMapper);
        $this->fileToReleaseGet($filesToReleaseMapper, $fileMapper, $releaseMapper);
        $this->filesGet($fileMapper);
    }

    #[Action(description: 'Cache all releases')]
    public function releasesGet(ReleaseMapper $releaseMapper): void
    {
        $releases = $releaseMapper->fetchAll();
        file_put_contents(
            $this->cacheDirectory . 'releases' . DIRECTORY_SEPARATOR . 'releases.cache',
            serialize(
                $releases
            )
        );
        $this->cacheAllReleases($releases);

        $releases = $releaseMapper->getReleasesForDocs();
        file_put_contents(
            $this->cacheDirectory . 'releases' . DIRECTORY_SEPARATOR . 'doc-releases.cache',
            serialize(
                $releases
            )
        );

        $release = $releaseMapper->fetchLatest();
        file_put_contents(
            $this->cacheDirectory . 'releases' . DIRECTORY_SEPARATOR . 'latest-release.cache',
            serialize(
                $release
            )
        );

        $this->terminal->command('Releases cached!');
    }

    #[Action(description: 'Cache all file to release mappings')]
    public function fileToReleaseGet(
        FilesToReleaseMapper $filesToReleaseMapper,
        FileMapper $fileMapper,
        ReleaseMapper $releaseMapper
    ) {
        $releases = $releaseMapper->fetchAll();
        /** @var Release $release */
        foreach ($releases as $release) {
            $cacheDirectory = $this->cacheDirectory . 'files-to-releases' . DIRECTORY_SEPARATOR . $release->name . '.cache';
            if (!is_dir($cacheDirectory)) {
                mkdir($cacheDirectory);
            }
            $ftrs = $filesToReleaseMapper->findAllByField('release_id', $release->release_id);
            /** @var FilesToRelease $ftr */
            foreach ($ftrs as $ftr) {
                $file = $fileMapper->findByPrimaryKey($ftr->file_id);
                if ($file === null) {
                    continue;
                }
                file_put_contents(
                    $cacheDirectory . DIRECTORY_SEPARATOR . $file->name . '.cache',
                    $file->sha
                );
            }
        }
        $this->terminal->command('Files to releases cached!');
    }

    #[Action(description: 'Cache all files')]
    public function filesGet(FileMapper $fileMapper): void
    {
        $files = $fileMapper->fetchAll();
        /** @var File $file */
        foreach ($files as $file) {
            file_put_contents(
                $this->cacheDirectory . 'files' . DIRECTORY_SEPARATOR . $file->sha . '.cache',
                serialize(
                    $file
                )
            );
        }
        $this->terminal->command('Files cached!');
    }

    #[Action(description: 'Clear cache')]
    public function clearAllGet(): void
    {
        $this->clearReleasesGet();
        $this->clearFileToReleaseGet();
        $this->clearFilesGet();
    }

    #[Action(description: 'Clear release cache')]
    public function clearReleasesGet(): void
    {
        $this->clearCacheDirectory($this->cacheDirectory . 'releases');
        $this->terminal->command('Release cache cleared!');
    }

    #[Action(description: 'Clear files cache')]
    public function clearFilesGet(): void
    {
        $this->clearCacheDirectory($this->cacheDirectory . 'files');
        $this->terminal->command('File cache cleared!');
    }

    #[Action(description: 'Clear files to release cache')]
    public function clearFileToReleaseGet(): void
    {
        $this->clearCacheDirectory($this->cacheDirectory . 'files-to-releases');
        $this->terminal->command('File to release cache cleared!');
    }

    protected function cacheAllReleases(Set $releases): void
    {
        /** @var Release $release */
        foreach ($releases as $release) {
            file_put_contents(
                $this->cacheDirectory . 'releases' . DIRECTORY_SEPARATOR . $release->name . '.cache',
                serialize(
                    $release
                )
            );
        }
    }

    /**
     * Modified from https://stackoverflow.com/a/59034371
     *
     * @param string $folder
     * @return void
     */
    protected function clearCacheDirectory(string $folder): void
    {
        foreach (new \DirectoryIterator($folder) as $file) {
            if (str_ends_with(
                    $file->getFilename(),
                    '.cache'
                ) === false) {
                continue;
            }
            if ($file->isFile()) {
                unlink($file->getPathname());
            } elseif ($file->isDir()) {
                $this->clearCacheDirectory($file->getPathname());
            }
        }
        rmdir($folder);
    }

}