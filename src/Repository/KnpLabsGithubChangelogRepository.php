<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Repository;

use Github\Api\Repo;
use Github\Client;
use Ssch\Typo3rectorIssueGenerator\Contract\ChangelogDeciderInterface;
use Ssch\Typo3rectorIssueGenerator\Contract\ChangelogRepositoryInterface;
use Ssch\Typo3rectorIssueGenerator\Dto\Changelog;
use Ssch\Typo3rectorIssueGenerator\ValueObject\Version;

final readonly class KnpLabsGithubChangelogRepository implements ChangelogRepositoryInterface
{
    public function __construct(
        private Client $client
    ) {
    }

    public function findByVersion(Version $version, ChangelogDeciderInterface $changelogDecider): array
    {
        $repo = new Repo($this->client);
        $remoteChangelogs = $repo->contents()->configure()->show('TYPO3', 'typo3', 'typo3/sysext/core/Documentation/Changelog/' . $version->__toString());

        $changelogs = [];

        if (! is_array($remoteChangelogs)) {
            return [];
        }

        foreach ($remoteChangelogs as $changelogPath) {
            $fileName = $changelogPath['name'];

            if ($changelogDecider($fileName) === false) {
                continue;
            }

            $remoteChangelog = $repo->contents()->configure()->show('TYPO3', 'typo3', $changelogPath['path']);
            if (! is_string($remoteChangelog)) {
                continue;
            }

            usleep(5000);

            $changelogs[] = new Changelog($fileName, $remoteChangelog, $version);
        }

        return $changelogs;
    }

    public function findByChangelogUrl(Version $version, string $changelogFileName, ChangelogDeciderInterface $changelogDecider): ?Changelog
    {
        if ($changelogDecider($changelogFileName) === false) {
            return null;
        }

        $repo = new Repo($this->client);

        $remoteChangelog = $repo->contents()->configure()->show('TYPO3', 'typo3', 'typo3/sysext/core/Documentation/Changelog/' . $version->__toString() . '/' . $changelogFileName);
        if (! is_string($remoteChangelog)) {
            return null;
        }

        return new Changelog($changelogFileName, $remoteChangelog, $version);
    }
}
