<?php

declare(strict_types=1);

namespace Modules\CLI\Controllers;

use Feast\Attributes\Action;
use Feast\CliController;
use Feast\Interfaces\ConfigInterface;
use Mapper\FileMapper;
use Mapper\FilesToReleaseMapper;
use Mapper\ReleaseMapper;
use Model\File;
use Model\FilesToRelease;
use Model\Release;
use Services\Github;

class ReleaseController extends CliController
{

    #[Action(description: 'Fetch newest release documentation')]
    public function newestGet(
        ConfigInterface $config,
        ReleaseMapper $releaseMapper,
        FileMapper $fileMapper,
        FilesToReleaseMapper $filesToReleaseMapper
    ): void {
        $githubApi = new Github\Request($config);

        $release = $githubApi->getLatestRelease();
        $dbRelease = $releaseMapper->saveFromGithub($release);

        $this->getDocsForRelease($release, $dbRelease, $githubApi, $fileMapper, $filesToReleaseMapper);
    }

    #[Action(description: 'Fetch all release documentation')]
    public function generateGet(
        ConfigInterface $config,
        ReleaseMapper $releaseMapper,
        FileMapper $fileMapper,
        FilesToReleaseMapper $filesToReleaseMapper
    ): void {
        $githubApi = new Github\Request($config);

        $releases = $githubApi->getReleases();

        /** @var Github\Responses\Release $release */
        foreach ($releases->releases as $release) {
            $dbRelease = $releaseMapper->saveFromGithub($release);
            $this->getDocsForRelease($release, $dbRelease, $githubApi, $fileMapper, $filesToReleaseMapper);
        }
    }

    protected function getDocsForRelease(
        Github\Responses\Release $release,
        Release $dbRelease,
        Github\Request $githubApi,
        FileMapper $fileMapper,
        FilesToReleaseMapper $filesToReleaseMapper
    ): void {
        $docs = $githubApi->getDocsForRelease($release->tagName);

        foreach ($docs->files as $file) {
            if (str_ends_with($file->name, '.md') === false) {
                continue;
            }
            $dbFile = $fileMapper->findOneByField('sha', $file->sha);
            if ($dbFile === null) {
                $dbFile = new File();
                $dbFile->name = $file->name;
                $dbFile->url = $file->downloadUrl;
                $dbFile->sha = $file->sha;
                $dbFile->content = file_get_contents($file->downloadUrl);
                $dbFile->html = '';
                $dbFile->save();
            }

            $fileMapped = $filesToReleaseMapper->findOneByFields(
                [
                    'file_id' => $dbFile->file_id,
                    'release_id' => $dbRelease->release_id
                ]
            );
            if ($fileMapped === null) {
                $fileMapped = new FilesToRelease();
                $fileMapped->file_id = $dbFile->file_id;
                $fileMapped->release_id = $dbRelease->release_id;
                $fileMapped->save();
            }
        }
    }

}