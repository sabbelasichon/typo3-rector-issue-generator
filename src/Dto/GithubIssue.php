<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Dto;

final class GithubIssue
{
    /**
     * @param string[] $labels
     */
    private function __construct(
        private readonly string $title,
        private readonly string $message,
        private readonly array $labels
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

    /**
     * @return string[]
     */
    public function getLabels(): array
    {
        return $this->labels;
    }
}
