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

        $rest = <<<'REST'
.. include:: /Includes.rst.txt

.. _breaking-92238:

==========================================================
Breaking: #92238 - Service injection in Extbase validators
==========================================================

See :issue:`92238`

Description
===========

With the deprecation and removal of objectManager usage in TYPO3, Extbase does not
use the objectManager to create validator instances any more.

.. note::

    This has been mitigated with recent TYPO3 v11 core releases: Extbase validators
    can use dependency injection again. See :doc:`this changelog <../11.5.x/Important-96332-ExtbaseValidatorsCanUseDependencyInjection>`
    for details.
    Additionally, all validators delivered by EXT:extbase and EXT:form will be marked
    :php:`final` in v12. Extensions can no longer extend validators but must extend abstract
    classes or implement the interfaces directly.

Impact
======

Validators that use dependency injection will experience non injected services for affected properties.


Affected Installations
======================

All installations that use dependency injection in Extbase validators.


Migration
=========

Instead of injecting services to the validator, use :php:`GeneralUtility::makeInstance`
to create an instance of required services.

Given the following example for a service injection in a validator:

.. code-block:: php

    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @param ConfigurationManagerInterface $configurationManager
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

Since the configurationManager is required globally in the class, :php:`GeneralUtility::makeInstance`
is used in the constructor of the validator to create an instance of the service.

.. code-block:: php

    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    public function __construct(array $options = [])
    {
        $this->configurationManager = GeneralUtility::makeInstance(ConfigurationManagerInterface::class);
        parent::__construct($options);
    }

In order to create instances of services that require dependency injection and which
are not already instantiated in the service container, it is required to declare those
services as :php:`public: true` in the :php:`Configuration/Services.yaml` of the given extension.

.. code-block:: yaml

    Vendor\MyExtension\Services\MyService:
      public: true


.. index:: TCA, NotScanned, ext:extbase
REST;


        $this->subject = new IssueImportService(
            new InMemoryChangelogRepository(
                [
                    new Changelog('Breaking-102108-TCATypesbitmask_Settings.rst', $rest, new Version('12.4')),
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
        self::assertEquals([new Issue('bfa5fbb11c3ea77b6e05ec9e5235c3d4', new GithubIssueId(1))], $this->issueRepository->getIssues());
    }
}
