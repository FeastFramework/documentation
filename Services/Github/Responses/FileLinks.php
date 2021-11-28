<?php

declare(strict_types=1);

namespace Services\Github\Responses;

use Feast\Attributes\JsonItem;
use Feast\Date;

class FileLinks
{
    public ?string $self = null;
    
    public ?string $git = null;
    
    public ?string $html = null;
    
}