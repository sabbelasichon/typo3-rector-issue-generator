<?php
declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\ValueObject;

final readonly class Version implements \Stringable
{
    public function __construct(private string $version)
    {
    }

    public function __toString(): string
    {
        return $this->version;
    }
}