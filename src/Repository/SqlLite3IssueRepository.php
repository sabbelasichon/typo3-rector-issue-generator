<?php

declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Repository;

use Ssch\Typo3rectorIssueGenerator\Contract\IssueRepositoryInterface;
use Ssch\Typo3rectorIssueGenerator\Dto\Changelog;
use Ssch\Typo3rectorIssueGenerator\Dto\Issue;

final class SqlLite3IssueRepository implements IssueRepositoryInterface
{
    public function __construct(
        private readonly \SQLite3 $database
    ) {
        $this->database->exec("CREATE TABLE IF NOT EXISTS issues(
            id INTEGER PRIMARY KEY AUTOINCREMENT, 
            github_issue_id INTEGER, 
            hash TEXT NOT NULL DEFAULT '0',
            type TEXT,
            title TEXT,
            issue_id INTEGER
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

    public function save(Issue $issue): void
    {
        $statement = $this->database->prepare("INSERT INTO issues (hash,github_issue_id,type,title,issue_id) VALUES (:hash, :github_issue_id, :type, :title, :issue_id)");
        if ($statement === false) {
            throw new \UnexpectedValueException('Could not prepare database statement');
        }

        $statement->bindValue(':hash', $issue->getHash());
        $statement->bindValue(':github_issue_id', $issue->getGithubIssueId());
        $statement->bindValue(':type', $issue->getType());
        $statement->bindValue(':title', $issue->getTitle());
        $statement->bindValue(':issue_id', $issue->getIssueId());

        $statement->execute();
    }
}
