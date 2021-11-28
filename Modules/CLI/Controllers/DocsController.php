<?php

declare(strict_types=1);

namespace Modules\CLI\Controllers;

use Feast\Attributes\Action;
use Feast\CliController;
use Feast\Interfaces\ConfigInterface;
use Mapper\FileMapper;
use Model\File;
use Services\Github\Request;

class DocsController extends CliController
{

    #[Action(description: 'Convert unparsed documentation')]
    public function parseGet(
        ConfigInterface $config,
        FileMapper $fileMapper,
    ): void {
        $files = $fileMapper->fetchAll();
        /** @var File $file */
        $githubApi = new Request($config);

        foreach ($files as $file) {
            if (str_ends_with($file->name, '.md')) {
                $file->html = $githubApi->getMarkdownToHtml(utf8_decode($file->content));
                $file->save();
            }
        }
    }

}