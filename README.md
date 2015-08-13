### Permafrost Git Deploy ###
---
Automatically deploy a git repository to a server after pushing with minimal configuration.

---
#####Basis#####
Permafrost Git Deploy is based on v1.3.1 of the "Simple PHP Git Deploy" script, which can be found at https://github.com/markomarkovic/simple-php-git-deploy/.

---

####Initial Installation####
---
Simply clone this repository into a web-accessable directory.

---
#### Local Configuration ####
---
Make a copy of `deploy-sample.php` and rename it (for example), `deploy.php`.
The PGD `\Deploy` class requires an instance of configuration class `\RepositoryInfo` to be passed in when executing the deploy (_see below for an example_).

The `\RepositoryInfo` class has the following properties that need to be configured:

  - _name_: Repository Name
  - _remoteRepository_: ssh or https link to the remote repository to be used for cloning/fetching.  If an https link is used in the format "https://user:password@...", the username and password are hidden from the output during deploy.
  - _branch_: the branch name that we're deploying
  - _targetDirectory_: the absolute path to the location to deploy the repository on the remote server.  Make sure to include a trailing slash.
  - _tempDirectory_: the absolute path to the temporary directory on the server used for staging the repository during deploy. Make sure to include a trailing slash.
  - _excludedDirectories_: array of relative directory names that are excluded from deployment, backups, and file deletion.
  - _versionFile_: the absolute path to a VERSION file that PGD requires to keep track of the latest commit hash.  A good value is: 
```php
     $ri->tempDirectory.'VERSION';
```
  - _backupDirectory_: the absolute path to the directory to store repository backups -- backups are performed of the repository before the deploy. Make sure to include a trailing slash.
  - _deleteFiles_: set to `true` to delete files in the target directory before deployment.  _Use of this setting can result in massive data loss if you're not careful!_
  - _cleanUp_: set to `true` to clean up files created in the temporary directory.  Setting this to `false` is recomended, as it enables only the changed files to be fetched from the remote repository during deployment.
  - _backUp_: set to `true` to enable creating a backup archive of the repository before deployment.
  - _useComposer_: set to `true` to enable [composer](http://getcomposer.org) support -- a `composer install` command will be run after staging the repository.
  - _deployTimer_: set to `true` to enable a deployment timer, which, after the deployment completes, will output the length of time in seconds the deployment took.

##### Sample Configuration & Execution #####
```php
<?php
  include('autoload.php');
  $ri = new \RepositoryInfo();
  $ri->name = "test repo";
  ...
  //see above for config properties--all properties must be configured!
  $deployer = new \Deploy(new \BrowserOutputHtml());
  $deployer->performDeploy($ri);
```
_--- see [deploy-sample.php](deploy-sample.php) for a complete deploy script example ---_

---
#### Repository Host Configuration ####
---
- You must configure webhooks to pull the url of your `deploy.php` post-push.

- #####Github#####
  @todo ---

- #####BitBucket#####
  @todo ---

---
#### Output ####
---
The `\Deploy` class will output the user it is running as, virtual host, and version strings of the required binaries.  
Next, it outputs the remote repository, branch name and the target deployment directory.
Finally, it will output all commands it executes, as it executes them, along with the output from those commands. Output buffering is used to allow for real-time command output.

If the `deployTimer` setting is enabled, it will output the length of time it took for the deploy to complete (in seconds) at the end of deployment execution.

HTML output styling is done entirely with CSS -- see [permafrost-deploy.css](permafrost-deploy.css).

_Note: If your remote repository is an https site with a user:pass in it, the Deploy class will automatically mask both the username and password during output._

---
#### Requirements ####
---
The server you run Permafrost Git Deploy on must have the following installed:

  - PHP 5.4+
  - whoami
  - which
  - git
  - rsync

And optionally, depending on your configuration:

  - tar: used to create backups.
  - composer: used if the `useComposer` property is set to true.

---
#### @todo ####
---

  - Implement a plugin system to allow for things like HipChat integration, mailing output logs, etc. I'd also like to convert the DeployTimer into a plugin.

  - ~~Create a basic authentication class that requires a secret key or something similar to be passed to the URL for deployment to occur.~~

  - Create a basic authentication class that implements [RFC 2617](http://www.ietf.org/rfc/rfc2617.txt) HTTP Basic and/or Digest Authentication. 

  - Create a class that loads multiple repository configurations and can execute deployment for any of them based on a URL parameter.

  - Create a `BrowserOutputText` class that outputs just text and a content type of text/plain.

  - Change the `BrowserOutput` requirement to just `Output` or something similar -- allow for default output not just to the browser, but log files, database, etc.

  - README - Repository host-specific configurations (github, etc.).

  - README - Generic repository configuration using hooks.

  - Unit Tests

  - Greater than 80% code coverage

---
#### License ####
---
Permafrost Git Deploy is open source software, available under the [MIT license](LICENSE).