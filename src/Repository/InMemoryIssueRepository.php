<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Repository;

use Ssch\Typo3rectorIssueGenerator\Contract\IssueRepositoryInterface;
use Ssch\Typo3rectorIssueGenerator\Dto\Changelog;
use Ssch\Typo3rectorIssueGenerator\Dto\Issue;

final class InMemoryIssueRepository implements IssueRepositoryInterface
{
    /**
     * @var Issue[]
     */
    private array $issues = [];

    public function save(Issue $issue): void
    {
        $this->issues[] = $issue;
    }

    /**
     * @return Issue[]
     */
    public function getIssues(): array
    {
        return $this->issues;
    }

    public function exists(Changelog $changelog): bool
    {
        foreach ($this->issues as $issue) {
            if ($issue->getHash() === $changelog->getHash()) {
                return true;
            }
        }

        return false;
    }

    public function update(Issue $issue): void
    {
        foreach ($this->issues as $index => $i) {
            if ($i->getHash() === $issue->getHash()) {
                $this->issues[$index] = $issue;
                return;
            }
        }
    }
}
