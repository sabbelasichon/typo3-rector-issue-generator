<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Dto;

use Ssch\Typo3rectorIssueGenerator\ValueObject\Version;

final readonly class Changelog
{
    /**
     * @var array<int, string>
     */
    private array $labels;

    private string $message;

    private string $hash;

    private string $fileName;

    private string $title;

    private const PUBLIC_CHANGELOG_URL = 'https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/%s/%s';

    public function __construct(string $fileName, string $message, Version $version)
    {
        $nameParts = explode('-', $fileName);
        $type = array_shift($nameParts);
        $issueBody = explode("\n", $message);
        $nextLineIsTitle = false;
        $title = '';

        foreach ($issueBody as $line) {
            if ($nextLineIsTitle) {
                $title = $line;
                break;
            }
            if (str_starts_with($line, '===============')) {
                $nextLineIsTitle = true;
            }
        }

        $this->labels = [$version->__toString(), $version->getMajorVersion(), $type];
        $this->title = $title;

        $fileNameWithHtml = str_replace('.rst', '.html', $fileName);
        $url = sprintf(self::PUBLIC_CHANGELOG_URL, $version, $fileNameWithHtml);

        $this->message = $title . "\n" . "\n" . $url . "\n" . implode("\n", $issueBody);
        $this->hash = md5($fileName);
        $this->fileName = $fileName;
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

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
