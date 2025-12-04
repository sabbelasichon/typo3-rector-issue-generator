<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Dto;

use Ssch\Typo3rectorIssueGenerator\ValueObject\GithubIssueId;
use Ssch\Typo3rectorIssueGenerator\ValueObject\Version;

final readonly class Issue
{
    public function __construct(
        private string $hash,
        private GithubIssueId $githubIssueId,
        private string $type,
        private string $title,
        private int $issueId,
        private Version $version
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
