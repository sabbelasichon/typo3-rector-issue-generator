<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Contract;

interface OutputInterface
{
    public function write(string $content): void;

    public function start(int $count): void;

    public function advance(): void;

    public function finish(): void;
}
