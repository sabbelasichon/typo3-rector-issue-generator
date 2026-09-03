# Copilot Instructions for TYPO3 Rector Issue Generator

## Build, Test & Lint Commands

### Setup
```bash
composer install
```

### Running Tests
```bash
# Run full test suite
composer run phpunit

# Run a specific test file
./vendor/bin/phpunit tests/Unit/Service/IssueImportServiceTest.php

# Run a specific test method
./vendor/bin/phpunit tests/Unit/Service/IssueImportServiceTest.php::testName
```

### Static Analysis & Linting
```bash
# Check code style (dry run)
composer run check-style

# Fix code style issues
composer run fix-style

# Run PHPStan (max level)
composer run phpstan
```

### Running the Application
```bash
# Import changelogs for specific TYPO3 versions
bin/import 11.5 12.4

# Import with features included
bin/import 11.5 --with-feature

# Update existing issues in the database
bin/import 11.5 --update

# Update issues (shorthand)
bin/update 11.5
```

## High-Level Architecture

### Purpose
This tool automatically generates GitHub issues for the [typo3-rector](https://github.com/sabbelasichon/typo3-rector) repository by parsing TYPO3 official changelogs and extracting relevant deprecations, breaking changes, and features.

### Data Flow
1. **Changelog Retrieval**: Fetches raw changelog files from TYPO3's official repository (Pandoc-formatted HTML)
2. **Filtering & Parsing**: Applies deciders to filter entries (excludes index/feature entries by default)
3. **Issue Generation**: Creates standardized GitHub issues with structured metadata
4. **Persistence**: Stores imported issues locally in SQLite to track what's been imported
5. **GitHub Sync**: Publishes issues to the typo3-rector repository via GitHub API

### Key Components

#### Repositories (Data Access Layer)
- **`ChangelogRepository`**: Fetches raw changelogs from TYPO3 via Knp/github-api
- **`IssueRepository`**: Persists imported issues to SQLite3 (local DB tracking)
- **`GithubIssueRepository`**: Publishes issues to GitHub via Knp/github-api
- Each repository implements a corresponding interface in `Contract/`

#### Services (Business Logic)
- **`IssueImportService`**: Main orchestrator that coordinates the import pipeline
  - Takes changelog entries, filters them, creates Issue DTOs, persists and publishes
  - Supports update mode to refresh existing issues

#### Deciders (Filtering Strategy)
- **`ChangelogDeciderInterface`**: Filters which changelog entries to process
- **`NonIndexDecider`**: Excludes meta entries (index sections)
- **`NonFeatureDecider`**: Excludes feature entries (only used by default)
- **`CompositeChangelogDecider`**: Applies multiple deciders in chain

#### DTOs & Value Objects
- **`Issue`**: Internal representation of an importable issue
- **`GithubIssue`**: GitHub-specific issue payload
- **`Credentials`**: GitHub authentication (username, repo, token)
- **`Version`**: TYPO3 version (e.g., "11.5", "12.4")

#### Output & Interfaces
- **`OutputInterface`**: Abstraction for progress reporting
- **`SymfonyConsoleOutput`**: CLI progress bar implementation
- **`NullOutput`**: No-op output for testing

### Database Schema
SQLite database at `db/issues.db` tracks imported issues:
- Stores changelog hash to prevent duplicate imports
- Records GitHub issue IDs for update operations

## Code Conventions

### Strict PHP Standards
- **Always declare types**: All functions use strict `declare(strict_types=1);`
- **Use readonly classes**: DTOs and immutable value objects use `final readonly`
- **Type hints everywhere**: Parameters, return types, and properties are fully typed
- **PSR-12 compliance**: Enforced via ECS with Symplify ruleset

### Architecture Patterns
- **Dependency Injection**: Services receive dependencies via constructor; no service locator
- **Interface-driven**: All external dependencies are interfaces (Repository, Output, Decider)
- **Value Objects**: Encapsulate domain concepts (Version, GithubIssueId, Credentials)
- **Immutability**: Use `readonly` properties; avoid mutable shared state

### Namespace Organization
```
Ssch\Typo3rectorIssueGenerator\
├── Contract\       # Interfaces
├── Service\        # Main business logic
├── Repository\     # Data access (GitHub, SQLite)
├── Decider\        # Filtering strategies
├── Dto\            # Data transfer objects
├── ValueObject\    # Domain value objects
├── Output\         # Output abstractions
└── Utility\        # Helpers
```

### Testing Patterns
- **Unit tests in `tests/Unit/`** mirror src structure
- **Test classes end with `Test`** suffix
- **PHPUnit 10.4+** with strict coverage requirements (`requireCoverageMetadata`)
- Tests use interfaces/in-memory repositories for isolation

### PHP Version
- **PHP 8.3.x required** (see composer.json)
- Use modern PHP 8.3 features: named arguments, readonly, match expressions

## Environment Setup

### Required
1. **Pandoc**: Must be installed on system (converts TYPO3 changelog HTML to Markdown)
   ```bash
   # Ubuntu/Debian
   wget https://github.com/jgm/pandoc/releases/download/3.11/pandoc-3.11-1-amd64.deb
   sudo dpkg -i pandoc-3.11-1-amd64.deb
   ```

2. **GitHub Token**: Create `.env` from `.env.example` with `GITHUB_ACCESS_TOKEN`
   - Token needs `repo` scope
   - Required for changelog and issue operations

### Database
- `db/issues.db` is auto-created on first run
- Never commit to version control (in .gitignore)

## When Modifying Code

- **Adding new filtering logic**: Implement `ChangelogDeciderInterface` and chain via `CompositeChangelogDecider`
- **Adding new output handler**: Implement `OutputInterface` (e.g., for JSON export)
- **Adding new repository**: Implement corresponding interface and inject into `IssueImportService`
- **Always run**: `composer run fix-style && composer run phpstan` before committing
- **Add tests** if adding new public methods or changing business logic
