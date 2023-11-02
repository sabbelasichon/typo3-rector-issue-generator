<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Output;

use Ssch\Typo3rectorIssueGenerator\Contract\OutputInterface;

final class NullOutput implements OutputInterface
{
    public function output(iterable|string $content): void
    {
    }

    public function start(int $count): void
    {
    }

    public function advance(): void
    {
    }

    public function finish(): void
    {
    }
}
