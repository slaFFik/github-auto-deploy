Github Auto Deploy
==================

What it does
------------

On every push to a specified in branch your server will get the latest version of a project.

Only modified files will be saved (added/modified/deleted) - not the whole project. So deploy is really quick.

On every deploy the changed files will be overwritten.

How to use it
-------------

Working with the deployer is rather easy.

This is how I'm setting the deploy for own projects:

1. Create a public repo on Github and make a first push to some branch (for example, to `master`).
2. On your own site go to a folder you want your project files be placed in and put there a `deploy.php` script from this repository. This file should be reachable for Github pings.
3. Adjust settings (lines ~19-33) as you need. Make sure `log.txt` file is writeable (chmod and chown are correct).
4. Go to your repository admin area, **Service Hooks** page. In **Available Service Hooks** choose **WebHooks URLs** and insert there a URL to a `deploy.php` file. Save settings.
5. Do a commit and a push to that repository. Check upload folder - it should contain changed/added files, and removed files should be deleted too.

What to remember
----------------

Currently this deployer works for public repositories only. However, private repos are in plans.

Some debug info after each push to a repo is saved into `log.txt` file. All the logs are incremented. Here is the example:

`
2012.06.07@23:22:16 - NOTE - Deploy started
2012.06.07@23:22:16 - SUCCESS - Modified/added files: deploy.php
2012.06.07@23:22:16 - SUCCESS - Removed files: readme.txt
2012.06.07@23:22:16 - NOTE - Deploy finished
`

Please remember, that this file will become huge if you make lots of commits, so clear it from time to time.

Plans
-----

1. Support private repos
2. Exclude files/folders from a deploy