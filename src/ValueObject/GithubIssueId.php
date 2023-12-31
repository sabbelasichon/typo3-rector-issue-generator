<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\ValueObject;

final class GithubIssueId implements \Stringable
{
    public function __construct(
        private readonly int $id
    ) {
    }

    public function __toString()
    {
        return (string) $this->id;
    }
}
