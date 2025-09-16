TYPO3 Rector Issue Generator
============================

Repository to generate TYPO3 Rector Issues from Changelogs.

This tool uses pandoc to convert the changelogs from the TYPO3 Documentation to markdown.
It is an external tool that needs to be installed on your system like so (if you use Ubuntu):

```bash
wget https://github.com/jgm/pandoc/releases/download/3.8/pandoc-3.8-1-amd64.deb
sudo dpkg -i pandoc-3.8-1-amd64.deb
rm pandoc-3.8-1-amd64.deb
```

1. Run git clone `git@github.com:sabbelasichon/typo3-rector-issue-generator.git`
2. Run `composer install`
3. Create a .env file with your real secret credentials GITHUB_ACCESS_TOKEN to it. The GitHub Access Token needs to have the permission `repo`. 
4. run the command `bin/console VERSION(S)` to import changelogs from different TYPO3 Versions. Available values are listed here: https://github.com/TYPO3/typo3/tree/main/typo3/sysext/core/Documentation/Changelog
5. Push changes back to repository via Pull Request
