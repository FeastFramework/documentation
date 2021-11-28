<?php

declare(strict_types=1);

namespace Services\Github\Responses;

use Feast\Attributes\JsonItem;

class Author
{
    public ?string $login = null;

    public ?int $id = null;

    #[JsonItem('node_id')]
    public ?string $nodeId = null;

    #[JsonItem('avatar_url')]
    public ?string $avatarUrl = null;

    #[JsonItem('gravatar_id')]
    public ?string $gravatarId = null;
    #[JsonItem('url')]
    public ?string $url = null;
    #[JsonItem('followers_url')]
    public ?string $followersUrl = null;
    #[JsonItem('following_url')]
    public ?string $followingUrl = null;
    #[JsonItem('gists_url')]
    public ?string $gistsUrl = null;
    #[JsonItem('starred_url')]
    public ?string $starredUrl = null;
    #[JsonItem('subscriptions_url')]
    public ?string $subscriptionsUrl = null;
    #[JsonItem('organizations_url')]
    public ?string $organizationsUrl = null;
    #[JsonItem('repos_url')]
    public ?string $reposUrl = null;
    #[JsonItem('events_url')]
    public ?string $eventsUrl = null;
    #[JsonItem('received_events_url')]
    public ?string $receivedEventsUrl = null;

    public ?string $type = null;

    #[JsonItem('site_admin')]
    public ?bool $siteAdmin = null;
}