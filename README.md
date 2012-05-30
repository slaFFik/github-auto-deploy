Github Auto Deploy
==================

What it does
------------

On every push to a specified in configs branch  your server will get the latest version of a project.

Only modified files will be saved (added/modified/deleted) - not the whole project. So deploy is really quick.

On every deploy the changed files will be overwritten.

How to use it
-------------

Working with the deployer is rather easy.

This is how I'm setting the deploy for own projects:

1. Create a public repo on Github and make a first push to some branch (for example, to `master`).
2. On your own site go to a folder you want your project files be placed in and create there a folder called any way you want (`deploy` or `ScoobyDoo` or whatever). Files from this folder should be reachable for Github pings.
3. Upload files taken from `github-auto-deploy` to that folder and open `config.php` file. Adjust settings as you need. Make sure `log.txt` file is writeable (0666).
4. Go to your repository admin area, **Service Hooks** page. In **Available Service Hooks** choose **WebHooks URLs** and insert there a URL to a `github.php` file. Save settings.
5. Do commit and a push to that repository. Check upload folder - it should contain changed/added files, and removed files should be deleted too.

What to remember
----------------

Currently this deployer works for public repositories only. However, private repos are in plans.

Please take care of deploy folder privacy. Although it doesn't contain any sensitive information - but who knows?..

Some debug info after each push to a repo is saved into `log.txt` file. But the content is overwritten every time the push is made. Remember this when debugging things.

Plans
-----

1. Support private repos
2. Exclude files/folders from a deploy
3. Improve the code