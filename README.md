TYPO3 Rector Issue Generator
============================

Repository to generate TYPO3 Rector Issues from Changelogs.

This tool uses pandoc to convert the changelogs from the TYPO3 Documentation to markdown.
It is an external tool that needs to be installed on your system like so (if you use Ubuntu):

```bash
wget https://github.com/jgm/pandoc/releases/download/2.15/pandoc-2.15-1-amd64.deb
sudo dpkg -i pandoc-2.15-1-amd64.deb
```

1. Run git clone `git@github.com:sabbelasichon/typo3-rector-issue-generator.git`
2. Run composer install
3. Create a .env file with your real secret credentials GITHUB_ACCESS_TOKEN to it. The GitHub Access Token needs to have the permission `repo`. 
4. run the command ./console VERSION(S) to import changelogs from different TYPO3 Versions.
5. Push changes back to repository via Pull Request