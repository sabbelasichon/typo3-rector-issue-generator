<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Decider;

use Ssch\Typo3rectorIssueGenerator\Contract\ChangelogDeciderInterface;

final class CompositeChangelogDecider implements ChangelogDeciderInterface
{
    /**
     * @param ChangelogDeciderInterface[] $deciders
     */
    public function __construct(
        private array $deciders
    ) {
    }

    public function __invoke(string $fileName): bool
    {
        foreach ($this->deciders as $decider) {
            if ($decider($fileName) === false) {
                return false;
            }
        }

        return true;
    }
}
