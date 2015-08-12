<?php
  include('autoload.php');
  
  $ri = new \RepositoryInfo();
  $ri->name = 'test-repo';
  $ri->remoteRepository = 'https://user:pass@bitbucket.org/test/test-repo.git';
  $ri->branch = 'master';
  $ri->targetDirectory = '/home/user/dev/test-repo-target-dir/';
  $ri->tempDirectory = '/home/user/dev/temp/pgd-'.md5($ri->remoteRepository).'/';
  $ri->excludedDirectories = array('.git', );
  $ri->versionFile = $ri->tempDirectory.'VERSION';
  $ri->backupDirectory = false;
  $ri->deleteFiles = false;
  $ri->cleanUp = false;
  $ri->backup = false;
  $ri->useComposer = false;
  
  $d = new \Deploy(new \BrowserOutputHtml());
  
  //please note there is no authentication mechanism in use here -- anyone browsing to this
  //file will cause the deploy to execute.
  $d->performDeploy($ri);