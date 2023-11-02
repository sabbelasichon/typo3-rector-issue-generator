<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Ssch\Typo3rectorIssueGenerator\Decider\CompositeChangelogDecider;
use Ssch\Typo3rectorIssueGenerator\Decider\NonFeatureDecider;
use Ssch\Typo3rectorIssueGenerator\Decider\NonIndexDecider;
use Ssch\Typo3rectorIssueGenerator\Dto\Changelog;
use Ssch\Typo3rectorIssueGenerator\Dto\Issue;
use Ssch\Typo3rectorIssueGenerator\Output\NullOutput;
use Ssch\Typo3rectorIssueGenerator\Repository\InMemoryChangelogRepository;
use Ssch\Typo3rectorIssueGenerator\Repository\InMemoryGithubIssueRepository;
use Ssch\Typo3rectorIssueGenerator\Repository\InMemoryIssueRepository;
use Ssch\Typo3rectorIssueGenerator\Service\IssueImportService;
use Ssch\Typo3rectorIssueGenerator\ValueObject\GithubIssueId;
use Ssch\Typo3rectorIssueGenerator\ValueObject\Version;

/**
 * @covers \Ssch\Typo3rectorIssueGenerator\Service\IssueImportService
 */
final class IssueImportServiceTest extends TestCase
{
    private IssueImportService $subject;

    private InMemoryIssueRepository $issueRepository;

    protected function setUp(): void
    {
        $this->issueRepository = new InMemoryIssueRepository();

        $this->subject = new IssueImportService(
            new InMemoryChangelogRepository(
                [
                    new Changelog('filename.rst', 'A message', new Version('12.4')),
                    new Changelog('Feature.rst', 'A message', new Version('12.4')),
                    new Changelog('Index.rst', 'A message', new Version('12.4')),
                ]
            ),
            $this->issueRepository,
            new InMemoryGithubIssueRepository(),
            new CompositeChangelogDecider([new NonIndexDecider(), new NonFeatureDecider()])
        );
    }

    public function test(): void
    {
        // Arrange
        $versions = [new Version('12.4')];

        // Act
        $this->subject->import($versions, new NullOutput());

        // Assert
        self::assertEquals([new Issue('5ff07446dd66fc824fed5555bbfacf79', new GithubIssueId(1))], $this->issueRepository->getIssues());
    }
}
