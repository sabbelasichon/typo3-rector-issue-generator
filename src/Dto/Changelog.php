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

    public function __construct(string $fileName, string $message, Version $version, string $url)
    {
        $nameParts = explode('-', $fileName);
        $type = array_shift($nameParts);

        $minorParts = explode('.', $version->__toString());
        $major = array_shift($minorParts);

        $labels = [$version, $major, $type];
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

        $this->labels = $labels;
        $this->title = $title;

        $this->message = $title.PHP_EOL.PHP_EOL.$url.PHP_EOL.implode(PHP_EOL, $issueBody);
        $this->hash = md5($fileName);
        $this->fileName = $fileName;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

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