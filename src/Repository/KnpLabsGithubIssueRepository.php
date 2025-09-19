<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Repository;

use Github\Api\Issue;
use Github\Client;
use Ssch\Typo3rectorIssueGenerator\Contract\GithubIssueRepositoryInterface;
use Ssch\Typo3rectorIssueGenerator\Dto\Credentials;
use Ssch\Typo3rectorIssueGenerator\Dto\GithubIssue;
use Ssch\Typo3rectorIssueGenerator\ValueObject\GithubIssueId;

final readonly class KnpLabsGithubIssueRepository implements GithubIssueRepositoryInterface
{
    public function __construct(
        private Client $client,
        private Credentials $credentials
    ) {
    }

    public function save(GithubIssue $githubIssue): GithubIssueId
    {
        /** @var Issue $issueApi */
        $issueApi = $this->client->api('issue');

        $remoteIssue = $issueApi->create($this->credentials->getUsername(), $this->credentials->getRepositoryName(), [
            'title' => $githubIssue->getTitle(),
            'body' => $githubIssue->getMessage(),
        ]);

        usleep(500000);

        foreach ($githubIssue->getLabels() as $label) {
            $issueApi->labels()->add($this->credentials->getUsername(), $this->credentials->getRepositoryName(), $remoteIssue['number'], $label);
            usleep(500000);
        }

        return new GithubIssueId($remoteIssue['number']);
    }
}
