<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Decider;

use Ssch\Typo3rectorIssueGenerator\Contract\ChangelogDeciderInterface;

final readonly class ChangelogNumberDecider implements ChangelogDeciderInterface
{
    public function __construct(
        private int $number
    ) {
    }

    public function __invoke(string $fileName): bool
    {
        return str_contains($fileName, (string) $this->number);
    }
}
