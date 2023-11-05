<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Dto;

use Pandoc\Pandoc;
use Ssch\Typo3rectorIssueGenerator\Utility\GeneralUtility;
use Ssch\Typo3rectorIssueGenerator\ValueObject\Version;

final class Changelog
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

        preg_match('/\.\. index::(.*)/', $message, $matches);
        $tags = GeneralUtility::trimExplode(',', $matches[1] ?? '');

        $this->labels = array_filter([$version->__toString(), $version->getMajorVersion(), $type, ...$tags]);
        $this->title = $title;

        $fileNameWithHtml = str_replace('.rst', '.html', $fileName);
        $url = sprintf(self::PUBLIC_CHANGELOG_URL, $version, $fileNameWithHtml);

        $output = (new Pandoc)
            ->from('rst')
            ->to('gfm') // GitHub-Flavored Markdown
            ->input($message)
            ->run();

        $output = preg_replace('/See `(\d+)`/', 'See [$1](https://forge.typo3.org/issues/$1)', $output);

        $this->message = $title . "\n" . "\n" . $url . "\n" . "\n" . $output;
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
