<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Decider;

use Ssch\Typo3rectorIssueGenerator\Contract\ChangelogDeciderInterface;

final class NonIndexDecider implements ChangelogDeciderInterface
{
    public function __invoke(string $fileName): bool
    {
        return str_contains($fileName, 'Index') === false;
    }
}
