<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Dto;

use Ssch\Typo3rectorIssueGenerator\ValueObject\GithubIssueId;
use Ssch\Typo3rectorIssueGenerator\ValueObject\Version;

final class Issue
{
    public function __construct(
        private readonly string $hash,
        private readonly GithubIssueId $githubIssueId,
        private readonly string $type,
        private readonly string $title,
        private readonly int $issueId,
        private readonly Version $version
    ) {
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getGithubIssueId(): GithubIssueId
    {
        return $this->githubIssueId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getIssueId(): int
    {
        return $this->issueId;
    }

    public function getVersion(): Version
    {
        return $this->version;
    }
}
