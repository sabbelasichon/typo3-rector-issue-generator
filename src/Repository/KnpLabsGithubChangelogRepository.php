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
        $remoteChangelogs = $repo->contents()->configure()->show('TYPO3', 'TYPO3.CMS', 'typo3/sysext/core/Documentation/Changelog/' . $version->__toString());

        $changelogs = [];

        if (! is_array($remoteChangelogs)) {
            return [];
        }

        foreach ($remoteChangelogs as $changelogPath) {
            $fileName = $changelogPath['name'];

            if ($changelogDecider($fileName) === false) {
                continue;
            }

            $remoteChangeLog = $repo->contents()->configure()->show('TYPO3', 'TYPO3.CMS', $changelogPath['path']);

            if (! is_string($remoteChangeLog)) {
                continue;
            }

            $changelogs[] = new Changelog($fileName, $remoteChangeLog, $version);
        }

        return $changelogs;
    }
}
