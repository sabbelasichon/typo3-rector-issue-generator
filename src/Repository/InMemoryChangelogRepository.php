<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Repository;

use Ssch\Typo3rectorIssueGenerator\Contract\ChangelogDeciderInterface;
use Ssch\Typo3rectorIssueGenerator\Contract\ChangelogRepositoryInterface;
use Ssch\Typo3rectorIssueGenerator\Dto\Changelog;
use Ssch\Typo3rectorIssueGenerator\ValueObject\Version;

final readonly class InMemoryChangelogRepository implements ChangelogRepositoryInterface
{
    /**
     * @param Changelog[] $changelogs
     */
    public function __construct(
        private array $changelogs
    ) {
    }

    public function findByVersion(Version $version, ChangelogDeciderInterface $changelogDecider): array
    {
        $changelogs = [];
        foreach ($this->changelogs as $changelog) {
            if ($changelogDecider($changelog->getFileName()) === false) {
                continue;
            }

            $changelogs[] = $changelog;
        }

        return $changelogs;
    }
}
