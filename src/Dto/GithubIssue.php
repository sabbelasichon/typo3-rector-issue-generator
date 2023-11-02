<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Dto;

final readonly class GithubIssue
{
    private function __construct(
        private string $title,
        private string $message,
        private array $labels
    ) {
    }

    public static function fromChangelog(Changelog $changelog): self
    {
        return new self($changelog->getTitle(), $changelog->getMessage(), $changelog->getLabels());
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getLabels(): array
    {
        return $this->labels;
    }
}
