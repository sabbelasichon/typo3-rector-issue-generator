<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\ValueObject;

final readonly class Version implements \Stringable
{
    private string $majorVersion;

    public function __construct(
        private string $version
    ) {
        $minorParts = explode('.', $this->version);
        $this->majorVersion = array_shift($minorParts);
    }

    public function __toString(): string
    {
        return $this->version;
    }

    public function getMajorVersion(): string
    {
        return $this->majorVersion;
    }
}
