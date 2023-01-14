<?php

declare(strict_types=1);

namespace Modules\CLI\Controllers;

use Exception\GithubException;
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
        try {
            $releases = $githubApi->getReleases();
        } catch(GithubException $exception) {
            $this->displayGithubError($exception);
            return;
        }
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
        try {
            $docs = $githubApi->getDocsForRelease($release->tagName);
        } catch(GithubException $exception) {
            $this->displayGithubError($exception);
            return;
        }
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

    protected function displayGithubError(GithubException $exception): void
    {
        $message = $exception->getErrorMessage();
        $documentationUrl = $exception->getDocumentationUrl();
        if ($message) {
            $this->terminal->error('Error fetching documentation from Github. The error information from Github is below.');
            $this->terminal->message('');
            $this->terminal->command('Error: ' . $message);
            if ($documentationUrl !== null) {
                $this->terminal->command('Documentation: ' . $documentationUrl);
            }
            $this->terminal->message('');
            $this->terminal->error('Check the README.md file for info on setting up Github Personal Access tokens.');
        } else {
            $this->terminal->error('Unknown error occured while fetching documentation from Github.');
        }
    }

}