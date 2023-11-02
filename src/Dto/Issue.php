<?php
declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Dto;

use Ssch\Typo3rectorIssueGenerator\ValueObject\GithubIssueId;

final readonly class Issue
{
    public function __construct(private string $hash, private GithubIssueId $githubIssueId)
    {
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getGithubIssueId(): GithubIssueId
    {
        return $this->githubIssueId;
    }
}