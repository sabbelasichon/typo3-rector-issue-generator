#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';

use Github\AuthMethod;
use Github\Client;
use Ssch\Typo3rectorIssueGenerator\Dto\Changelog;
use Ssch\Typo3rectorIssueGenerator\Dto\Credentials;
use Ssch\Typo3rectorIssueGenerator\Output\SymfonyConsoleOutput;
use Ssch\Typo3rectorIssueGenerator\Repository\InMemoryChangelogRepository;
use Ssch\Typo3rectorIssueGenerator\Repository\InMemoryGithubIssueRepository;
use Ssch\Typo3rectorIssueGenerator\Repository\InMemoryIssueRepository;
use Ssch\Typo3rectorIssueGenerator\Repository\KnpLabsGithubChangelogRepository;
use Ssch\Typo3rectorIssueGenerator\Repository\KnpLabsGithubIssueRepository;
use Ssch\Typo3rectorIssueGenerator\Repository\SqlLite3IssueRepository;
use Ssch\Typo3rectorIssueGenerator\Service\IssueImportService;
use Ssch\Typo3rectorIssueGenerator\ValueObject\Version;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;

(new SingleCommandApplication())
    ->setName('Import TYPO3 Changelogs')
    ->setVersion('1.0.0')
    ->addArgument('versions', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'The TYPO3 Versions')
    ->setCode(function (InputInterface $input, OutputInterface $output): int {

        $client = new Client();
        $client->authenticate('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', null, AuthMethod::ACCESS_TOKEN);

        $importService = new IssueImportService(
            new KnpLabsGithubChangelogRepository($client),
            new SqlLite3IssueRepository(new SQLite3(__DIR__ . '/db/issues.db')),
            new KnpLabsGithubIssueRepository($client, new Credentials('sabbelasichon', 'typo3-rector'))
        );

        $versions = array_map(fn(string $version) => new Version($version), $input->getArgument('versions'));

        $importService->import($versions, new SymfonyConsoleOutput($output));

        return Command::SUCCESS;
    })
    ->run();