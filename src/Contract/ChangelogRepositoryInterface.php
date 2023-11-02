<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Contract;

use Ssch\Typo3rectorIssueGenerator\Dto\Changelog;
use Ssch\Typo3rectorIssueGenerator\ValueObject\Version;

interface ChangelogRepositoryInterface
{
    /**
     * @return Changelog[]
     */
    public function findByVersion(Version $version, ChangelogDeciderInterface $changelogDecider): array;
}
