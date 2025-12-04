<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Repository;

use Ssch\Typo3rectorIssueGenerator\Contract\IssueRepositoryInterface;
use Ssch\Typo3rectorIssueGenerator\Dto\Changelog;
use Ssch\Typo3rectorIssueGenerator\Dto\Issue;
use Ssch\Typo3rectorIssueGenerator\ValueObject\GithubIssueId;
use Ssch\Typo3rectorIssueGenerator\ValueObject\Version;

final readonly class SqlLite3IssueRepository implements IssueRepositoryInterface
{
    public function __construct(
        private \SQLite3 $database
    ) {
        $this->database->exec("CREATE TABLE IF NOT EXISTS issues(
            id INTEGER PRIMARY KEY AUTOINCREMENT, 
            github_issue_id INTEGER, 
            hash TEXT NOT NULL DEFAULT '0',
            type TEXT,
            title TEXT,
            issue_id INTEGER,
            typo3_version TEXT
        )");
    }

    public function exists(Changelog $changelog): bool
    {
        $statement = $this->database->prepare("SELECT id FROM issues WHERE hash=:hash LIMIT 1");
        if ($statement === false) {
            throw new \UnexpectedValueException('Could not prepare database statement');
        }

        $statement->bindValue(':hash', $changelog->getHash());

        $result = $statement->execute();

        if ($result === false) {
            return false;
        }

        return $result->fetchArray(SQLITE3_ASSOC) !== false;
    }

    public function get(Changelog $changelog): ?Issue
    {
        $statement = $this->database->prepare("SELECT * FROM issues WHERE hash=:hash LIMIT 1");
        if ($statement === false) {
            throw new \UnexpectedValueException('Could not prepare database statement');
        }

        $statement->bindValue(':hash', $changelog->getHash());

        $result = $statement->execute();
        if ($result === false) {
            return null;
        }

        $row = $result->fetchArray(SQLITE3_ASSOC);
        if ($row === false) {
            return null;
        }

        return new Issue(
            $changelog->getHash(),
            new GithubIssueId($row['github_issue_id']),
            $changelog->getType(),
            $changelog->getTitle(),
            $changelog->getIssueId(),
            new Version($row['typo3_version'])
        );
    }

    public function save(Issue $issue): void
    {
        $statement = $this->database->prepare("INSERT INTO issues (hash,github_issue_id,type,title,issue_id,typo3_version) VALUES (:hash, :github_issue_id, :type, :title, :issue_id, :typo3_version)");
        if ($statement === false) {
            throw new \UnexpectedValueException('Could not prepare database statement');
        }

        $statement->bindValue(':hash', $issue->getHash());
        $statement->bindValue(':github_issue_id', $issue->getGithubIssueId());
        $statement->bindValue(':type', $issue->getType());
        $statement->bindValue(':title', $issue->getTitle());
        $statement->bindValue(':issue_id', $issue->getIssueId());
        $statement->bindValue(':typo3_version', (string)$issue->getVersion());

        $statement->execute();
    }

    public function update(Issue $issue): void
    {
        $statement = $this->database->prepare("UPDATE issues SET type = :type, title = :title, issue_id = :issue_id, typo3_version = :typo3_version WHERE hash = :hash");
        if ($statement === false) {
            throw new \UnexpectedValueException('Could not prepare database statement');
        }

        $statement->bindValue(':type', $issue->getType());
        $statement->bindValue(':title', $issue->getTitle());
        $statement->bindValue(':issue_id', $issue->getIssueId());
        $statement->bindValue(':typo3_version', (string)$issue->getVersion());
        $statement->bindValue(':hash', $issue->getHash());

        $statement->execute();
    }
}
