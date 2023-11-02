<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Contract;

interface ChangelogDeciderInterface
{
    public function __invoke(string $fileName): bool;
}
