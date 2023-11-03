<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Dto;

final class Credentials
{
    public function __construct(
        private string $username,
        private string $repositoryName,
        private string $accessToken
    ) {
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getRepositoryName(): string
    {
        return $this->repositoryName;
    }
}
