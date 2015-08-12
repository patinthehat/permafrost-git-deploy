<?php

class RepositoryInfo {
  
  public $name;
  
  public $remoteRepository;
  public $branch;
  public $targetDirectory;
  public $tempDirectory;
  public $backupDirectory;
  public $excludedDirectories = array();
  public $deleteFiles = false;
  public $cleanUp = false;
  public $backup = false;
  public $versionFile;
  
  public $useComposer = false;
  public $composerOptions = '--no-dev';
  public $composerHome = false;
  public $deployTimer = true;
  
  function __construct() {
    //
  }
  
  public function extractRepositoryName()
  {
    return basename($this->remoteRepository, '.git');
  }
  
  public function getExcludedDirectories()
  {
    return serialize($this->excludedDirectories);
  }
  
  

}