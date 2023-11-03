<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Service;

use Ssch\Typo3rectorIssueGenerator\Contract\ChangelogDeciderInterface;
use Ssch\Typo3rectorIssueGenerator\Contract\ChangelogRepositoryInterface;
use Ssch\Typo3rectorIssueGenerator\Contract\GithubIssueRepositoryInterface;
use Ssch\Typo3rectorIssueGenerator\Contract\IssueRepositoryInterface;
use Ssch\Typo3rectorIssueGenerator\Contract\OutputInterface;
use Ssch\Typo3rectorIssueGenerator\Dto\GithubIssue;
use Ssch\Typo3rectorIssueGenerator\Dto\Issue;
use Ssch\Typo3rectorIssueGenerator\ValueObject\Version;

final class IssueImportService
{
    public function __construct(
        private readonly ChangelogRepositoryInterface   $changelogRepository,
        private readonly IssueRepositoryInterface       $issueRepository,
        private readonly GithubIssueRepositoryInterface $githubIssueRepository,
        private readonly ChangelogDeciderInterface $changelogDecider
    ) {
    }

    /**
     * @param Version[] $versions
     */
    public function import(array $versions, OutputInterface $output): void
    {
        $importedIssues = [];
        foreach ($versions as $version) {
            $output->write('Import changelogs for version: ' . $version->__toString());

            $changelogs = $this->changelogRepository->findByVersion($version, $this->changelogDecider);

            $output->start(count($changelogs));

            foreach ($changelogs as $changelog) {
                $output->advance();

                if ($this->issueRepository->exists($changelog)) {
                    continue;
                }

                $githubIssueId = $this->githubIssueRepository->save(GithubIssue::fromChangelog($changelog));

                $this->issueRepository->save(new Issue($changelog->getHash(), $githubIssueId));

                $importedIssues[] = sprintf('Inserted issue "%s" with file path "%s"', $githubIssueId, $changelog->getFileName());
            }

            $output->finish();
            $output->write('');
        }

        foreach ($importedIssues as $importedIssue) {
            $output->write($importedIssue);
        }
    }
}
