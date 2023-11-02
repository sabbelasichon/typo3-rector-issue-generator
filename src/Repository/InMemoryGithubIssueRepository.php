<?php
declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Repository;

use Ssch\Typo3rectorIssueGenerator\Contract\GithubIssueRepositoryInterface;
use Ssch\Typo3rectorIssueGenerator\Dto\GithubIssue;
use Ssch\Typo3rectorIssueGenerator\ValueObject\GithubIssueId;

final class InMemoryGithubIssueRepository implements GithubIssueRepositoryInterface
{
    private array $githubIssues = [];

    public function save(GithubIssue $githubIssue): GithubIssueId
    {
        $this->githubIssues[] = $githubIssue;

        return new GithubIssueId(count($this->githubIssues));
    }
}