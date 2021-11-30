<?php

declare(strict_types=1);

namespace Services\Github;

use Feast\Collection\Set;
use Feast\Exception\ServerFailureException;
use Feast\Interfaces\ConfigInterface;
use Feast\Json;
use Feast\Service;
use Services\Github\Responses\Folder;
use Services\Github\Responses\Release;
use Services\Github\Responses\Releases;

class Request extends Service
{

    //protected string $auth;
    public function __construct(ConfigInterface $config)
    {
      
        parent::__construct();
        $this->httpRequest->authenticate((string)$config->getSetting('github.user'), (string)$config->getSetting('github.token'));
    }

    public function getLatestRelease(): Release {
        $this->httpRequest->get('https://api.github.com/repos/feastframework/framework/releases/latest');
        $this->httpRequest->makeRequest();
        $release = new Release();
        Json::unmarshal($this->httpRequest->getResponseAsString(),$release);
        return $release;
        
    }
    public function getReleases(): Releases
    {
        $page = 1;
        $items = [];
        $empty = false;
        do {
            $this->httpRequest->get('https://api.github.com/repos/feastframework/framework/releases?page=' . (string)$page);
            $this->httpRequest->makeRequest();
//            var_dump($this->httpRequest->getResponseAsString());

            /** @var array<array-key, \stdClass> $response */
            $response = $this->httpRequest->getResponseAsJson();
            if (empty($response)) {
                $empty = true;
            }
            $items = array_merge($items, $response);
            $page++;
        } while ($empty === false);
        return new Releases($items);
    }

    public function getDocsForRelease(string $tag): Folder
    {
            $this->httpRequest->get('https://api.github.com/repos/feastframework/framework/contents/docs?ref=' . $tag);
            $this->httpRequest->makeRequest();
            /** @var array<array-key, \stdClass> $response */
            $response = $this->httpRequest->getResponseAsJson();
          
            return new Folder($response);
    }
    
    public function getMarkdownToHtml(string $markdown): string
    {
        $this->httpRequest->postJson('https://api.github.com/markdown');
        $this->httpRequest->setArguments(
            [
                'text' => $markdown,
                'context' => 'feastframework/framework'
            ]
        );
        $this->httpRequest->makeRequest();
        return str_replace('user-content-','',$this->httpRequest->getResponseAsString());
    }

}