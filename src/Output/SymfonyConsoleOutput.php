<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Output;

use Ssch\Typo3rectorIssueGenerator\Contract\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

final class SymfonyConsoleOutput implements OutputInterface
{
    private ?ProgressBar $progressBar = null;

    public function __construct(
        private readonly \Symfony\Component\Console\Output\OutputInterface $output
    ) {
    }

    public function write(string $content): void
    {
        $this->output->writeln($content);
    }

    public function start(int $count): void
    {
        if ($this->progressBar !== null) {
            throw new \LogicException('The ProgressBar should have not been initialized.');
        }

        $this->progressBar = new ProgressBar($this->output, $count);
    }

    public function advance(): void
    {
        if ($this->progressBar === null) {
            throw new \LogicException('The ProgressBar should have been initialized before.');
        }

        $this->progressBar->advance();
    }

    public function finish(): void
    {
        if ($this->progressBar === null) {
            throw new \LogicException('The ProgressBar should have been initialized before.');
        }

        $this->progressBar->finish();
        $this->progressBar = null;
    }
}
