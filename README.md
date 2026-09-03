TYPO3 Rector Issue Generator
============================

Repository to generate TYPO3 Rector Issues from Changelogs.

This tool uses pandoc to convert the changelogs from the TYPO3 Documentation to markdown.

## Build, Test & Lint Commands

### Setup
```bash
composer install
```

### Running Tests
```bash
# Run full test suite
./vendor/bin/phpunit

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

# Update issues
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

## Environment Setup

### Required
1. **Pandoc**: Must be installed on system (converts TYPO3 changelog HTML to Markdown)
   ```bash
   # Ubuntu/Debian
   wget https://github.com/jgm/pandoc/releases/download/3.11/pandoc-3.11-1-amd64.deb
   sudo dpkg -i pandoc-3.11-1-amd64.deb
   rm pandoc-3.11-1-amd64.deb
   ```

2. **GitHub Token**: Create `.env` from `.env.example` with `GITHUB_ACCESS_TOKEN`
    - Token needs `repo` scope
    - Required for changelog and issue operations

### Database
- `db/issues.db` is auto-created on first run

## Contribution

1. Run git clone `git@github.com:sabbelasichon/typo3-rector-issue-generator.git`
2. Run `composer install`
3. Create a .env file with your real secret credentials GITHUB_ACCESS_TOKEN to it. The GitHub Access Token needs to have the permission `repo`.
4. run the command `bin/import VERSION(S)` to import changelogs from different TYPO3 Versions. Available values are listed here: https://github.com/TYPO3/typo3/tree/main/typo3/sysext/core/Documentation/Changelog
5. Push changes back to repository via Pull Request