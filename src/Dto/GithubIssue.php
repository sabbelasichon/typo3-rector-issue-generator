<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Dto;

final readonly class GithubIssue
{
    /**
     * @param string[] $labels
     */
    private function __construct(
        private int $id,
        private string $title,
        private string $message,
        private array $labels
    ) {
    }

    public static function fromChangelog(Changelog $changelog, int $id = 0): self
    {
        return new self($id, $changelog->getTitle(), $changelog->getMessage(), $changelog->getLabels());
    }

    public function getId(): int
    {
        return $this->id;
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
