<?php

declare(strict_types=1);

namespace Exception;

class GithubException extends \Exception
{
    public function __construct(string $message = "", protected \stdClass $response = new \stdClass() )
    {
        parent::__construct($message);
    }
    
    public function getErrorMessage(): ?string
    {
        /** @var string|null $message */
        $message = $this->response->message;
        if ( $message != null ) {
            return $message;
        }
        return null;
    }
    
    public function getDocumentationUrl(): ?string
    {
        /** @var string|null $documentationUrl */
        $documentationUrl = $this->response->documentation_url;
        if ( $documentationUrl != null ) {
            return $documentationUrl;
        }
        return null;
    }
}