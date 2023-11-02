<?php
declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Contract;

use Ssch\Typo3rectorIssueGenerator\Dto\Changelog;
use Ssch\Typo3rectorIssueGenerator\Dto\Issue;

interface IssueRepositoryInterface
{
    public function exists(Changelog $changelog): bool;
    public function save(Issue $issue): void;
}