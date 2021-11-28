<?php

/**
 * Copyright 2021 Jeremy Presutti <Jeremy@Presutti.us>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace Controllers;

use Feast\Attributes\Path;
use Feast\Collection\Set;
use Feast\HttpController;
use Feast\Session\Session;
use Mapper\FileMapper;
use Mapper\ReleaseMapper;
use Model\File;
use Model\Release;

class IndexController extends HttpController
{

    #[Path('version/:version/?:page', 'version')]
    public function versionGet(
        string $version,
        ?string $page,
        Session $session
    ): void {
        $this->storeVersionInSession($version, $session);
        if (empty($page)) {
            $this->externalRedirect('/');
            return;
        }
        $this->externalRedirect('/' . $page);
    }

    #[Path('/?:name', 'index')]
    public function indexGet(
        ?string $name,
        Session $session,

    ): void {
        if ($name === null || $name === 'index') {
            $name = 'index.md';
        }

        $release = $this->getRelease($session);

        $this->view->releases = $this->getReleasesForDocs();
        $this->view->release = $release;
        $file = $this->findFileForVersion($release, $name);
        if ($file === null) {
            $this->externalRedirect('/');
            return;
        }
        $this->view->contents = $file->html;
    }

    protected function findFileForVersion(Release $release, string $name): ?File
    {
        $hashFileName = APPLICATION_ROOT . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'files-to-releases' . DIRECTORY_SEPARATOR . $release->name . '.cache' . DIRECTORY_SEPARATOR . $name . '.cache';
        if (file_exists($hashFileName)) {
            $hash = file_get_contents(
                $hashFileName
            );
            $fileName = APPLICATION_ROOT . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $hash . '.cache';
            if (file_exists($fileName)) {
                /** @var File|null $file */
                $file = unserialize(file_get_contents($fileName));
                if ($file instanceof File) {
                    return $file;
                }
            }
        }
        return (new FileMapper())->findFileForVersion($release, $name);
    }

    protected function getReleasesForDocs(): Set
    {
        $releaseCacheFile = APPLICATION_ROOT . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'releases' . DIRECTORY_SEPARATOR . 'doc-releases.cache';
        if (file_exists($releaseCacheFile)) {
            /** @var Set|null $releases */
            $releases = unserialize(file_get_contents($releaseCacheFile));
            if ($releases instanceof Set) {
                return $releases;
            }
        }
        return (new ReleaseMapper())->getReleasesForDocs();
    }

    protected function getRelease(
        Session $session,
    ): Release {
        $versionInfo = $session->getNamespace('version');
        if (isset($versionInfo->version) && is_string($versionInfo->version)) {
            $cacheFile = APPLICATION_ROOT . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'releases' . DIRECTORY_SEPARATOR . $versionInfo->version . '.cache';
            if (file_exists($cacheFile)) {
                /** @var Release|null $release */
                $release = unserialize(file_get_contents($cacheFile));
                if ($release instanceof Release) {
                    return $release;
                }
            }
            /** @var Release|null $release */
            $release = (new ReleaseMapper())->findOneByField('tag', $versionInfo->version);
            if ($release instanceof Release) {
                return $release;
            }
        }

        /** @var Release */
        return $this->fetchLatestRelease();
    }

    protected function storeVersionInSession(string $version, Session $session): void
    {
        $session->getNamespace('version')->version = $version;
    }

    protected function fetchLatestRelease(): Release
    {
        $latestReleaseCacheFile = APPLICATION_ROOT . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'releases' . DIRECTORY_SEPARATOR . 'latest-release.cache';
        if (file_exists($latestReleaseCacheFile)) {
            /** @var Release|null $release */
            $release = unserialize(file_get_contents($latestReleaseCacheFile));
            if ($release instanceof Release) {
                return $release;
            }
        }
        /** @var Release */
        return (new ReleaseMapper())->fetchLatest();
    }

}
