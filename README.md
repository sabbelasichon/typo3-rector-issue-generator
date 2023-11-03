TYPO3 Rector Issue Generator
============================

Repository to generate TYPO3 Rector Issues from Changelogs. 

1. Run git clone `git@github.com:sabbelasichon/typo3-rector-issue-generator.git`
2. Run composer install
3. Create a .env file with your real secret credentials GITHUB_ACCESS_TOKEN to it. The GitHub Access Token needs to have the permission `repo`. 
4. run the command ./console VERSION(S) to import changelogs from different TYPO3 Versions.
5. Push changes back to repository via Pull Request