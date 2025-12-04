<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Service;

use Ssch\Typo3rectorIssueGenerator\Contract\ChangelogDeciderInterface;
use Ssch\Typo3rectorIssueGenerator\Contract\ChangelogRepositoryInterface;
use Ssch\Typo3rectorIssueGenerator\Contract\GithubIssueRepositoryInterface;
use Ssch\Typo3rectorIssueGenerator\Contract\IssueRepositoryInterface;
use Ssch\Typo3rectorIssueGenerator\Contract\OutputInterface;
use Ssch\Typo3rectorIssueGenerator\Dto\Changelog;
use Ssch\Typo3rectorIssueGenerator\Dto\GithubIssue;
use Ssch\Typo3rectorIssueGenerator\ValueObject\Version;

final readonly class IssueUpdateService
{
    public function __construct(
        private ChangelogRepositoryInterface $changelogRepository,
        private IssueRepositoryInterface $issueRepository,
        private GithubIssueRepositoryInterface $githubIssueRepository,
        private ChangelogDeciderInterface $changelogDecider
    ) {
    }

    public function update(OutputInterface $output, string $changelogUrl): void
    {
        if (!str_starts_with($changelogUrl, 'https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/')) {
            $output->write('ERROR: URL does not start with https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/');
            return;
        }

        if (!str_ends_with($changelogUrl, '.html')) {
            $output->write('ERROR: URL does not end with .html');
            return;
        }

        $changelogFileName = str_replace(['https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/', '.html'],
            ['', '.rst'],
            $changelogUrl
        );

        [$version, $fileName] = explode('/', $changelogFileName, 2);

        if (empty($fileName)) {
            $output->write('ERROR: URL does not contain a file name');
            return;
        }
        if ($version === '') {
            $output->write('ERROR: URL does not contain a version number');
            return;
        }

        $output->write('Update changelog: ' . $changelogUrl);

        $version = new Version($version);
        $changelog = $this->changelogRepository->findByChangelogUrl($version, $fileName, $this->changelogDecider);
        if (!$changelog instanceof Changelog) {
            return;
        }

        if (!$this->issueRepository->exists($changelog)) {
            return;
        }

        $issue = $this->issueRepository->get($changelog);
        if ($issue === null) {
            return;
        }

        $githubIssueId = $issue->getGithubIssueId()->getId();
        $githubIssue = $this->githubIssueRepository->update(GithubIssue::fromChangelog($changelog, $githubIssueId), $githubIssueId);

        $output->write('');
        $output->write(sprintf('Updated issue "%s" with file path "%s"', $githubIssueId, $changelog->getFileName()));
    }
}
