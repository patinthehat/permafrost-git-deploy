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
  
  $browserOutput = new \PGD\Output\BrowserOutputHtml();
  $auth = new \PGD\Authentication\AuthenticationDriver(
    $browserOutput, 
    new \PGD\Authentication\AuthenticationSecretKey("mypassword", "sk", 'get')
  );
    
  //browse to deploy.php?sk=mypassword to authenticate  
  if ($auth->authenticate() === true) {
    $d = new \PGD\Deploy($browserOutput);
    $d->performDeploy($ri);    
  }