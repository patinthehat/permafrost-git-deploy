<?php

namespace PGD;

class Deploy {

  /**
   * @var \\PGD\DeployTimer
   */
  protected $timer;
  
  /**
   * @var \\PGD\RequireBinaries
   */
  protected $requiredBinaries;
  
  /**
   * @var \\PGD\Output\BrowserOutput
   */
  protected $browserOutput;
  
  /**
   * @var \\PGD\Commands\CommandRunner
   */
  protected $commandRunner;
  
  public function __construct($browserOutput) {
    $this->timer = new \PGD\DeployTimer(false);
    $this->requiredBinaries = new \PGD\RequireBinaries();
    $this->commandRunner = new \PGD\Commands\CommandRunner($browserOutput, 30);
    
    $this->browserOutput = $browserOutput;
    
    $this->browserOutput->addShortcut('prompt', function() use ($browserOutput) { 
      $browserOutput->writeRaw('<i class="prompt"></i>'); 
      $browserOutput->flush();
    });
    $this->browserOutput->addShortcut('command', function($cmd) use ($browserOutput) { 
      $browserOutput->writeRaw("<span class=\"command\">".htmlentities($cmd)."</span>"); 
      $browserOutput->flush();
    });
    $this->browserOutput->addShortcut('error', function($msg) use ($browserOutput) {
      $browserOutput->writeRaw("<div class=\"error\">".htmlentities($msg)."</div>");
      $browserOutput->flush();
    }); 
    $this->browserOutput->addShortcut('output', function($data) use ($browserOutput) {
      $browserOutput->writeRaw("<div class=\"output\">".htmlentities($data)."</div>");
      $browserOutput->flush();
    });
  }
  
  public function getTimer()
  {
    return $this->timer;
  }
  
  public function getRequiredBinaries()
  {
    return $this->requiredBinaries;
  }
  
  public function getCommandRunner()
  {
    return $this->commandRunner;
  }
  
  public function getBrowserOutput()
  {
    return $this->browserOutput;
  }
  
  public function performDeploy(\PGD\RepositoryInfo $repositoryInfo)
  {
    ob_start();
    
    $this->getBrowserOutput()->sendContentType();
    $this->getBrowserOutput()->writeRaw('<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="robots" content="noindex">
	<title>Permafrost Git Repository Deploy</title>
  <link href="permafrost-deploy.css" rel="stylesheet">
</head>
<body>
<pre>
');
    
    $this->getBrowserOutput()->writeQueue();
    
    $this->getBrowserOutput()->writeRaw('
<div class="output inline highlight bold">Checking the environment ...</div>

');
    //running as a local script, not from an httpd
    if (!isset($_SERVER['SERVER_NAME']))
      $_SERVER['SERVER_NAME'] = 'localhost';
        
    if (is_dir($repositoryInfo->tempDirectory)) { 
      if (!chdir($repositoryInfo->tempDirectory)) {
        $this->getBrowserOutput()->write(sprintf("`chdir()` to temporary/staging directory '%s' failed.", $repositoryInfo->tempDirectory), "error");
        die();
      }
    } else {
      $this->getBrowserOutput()->write(sprintf("Temporary/staging directory '%s' not found.", $repositoryInfo->tempDirectory), "error");
      die();      
    }
    
    $this->getTimer()->start();
    
    $this->getBrowserOutput()->write("Running as ", "output inline highlight");
    $this->getCommandRunner()->executeSingleCommand(new \PGD\Commands\Command("whoami", true), "inline highlight bold");
    $this->getBrowserOutput()->writeRaw(".\n\n");
    
    $this->getBrowserOutput()->write(sprintf("Running on host %s", $_SERVER['SERVER_NAME']), "output inline highlight");
    $this->getBrowserOutput()->writeRaw(".\n\n");
    
    $this->getRequiredBinaries()->addRequiredBinaries(array('git','rsync'));
    
    if ($repositoryInfo->backup) {
      $this->getRequiredBinaries()->addRequiredBinary('tar');
      if (!is_dir($repositoryInfo->backupDirectory) || !is_writable($repositoryInfo->backupDirectory)) {
        $this->getBrowserOutput()->shortcut('error', 'Backup directory not found or not writable.');
        die();
      }        
    }
    
    if ($repositoryInfo->useComposer)
      $this->getRequiredBinaries()->addRequiredBinary('composer --no-ansi');
    
    $binreq = $this->getRequiredBinaries()->checkRequirements(true);
    
    if (!$binreq->getResult()) {
      $missingBinary = "";
      $d = $binreq->getData();
      if (!$d[count($d)-1]->getExists())
        $missingBinary = $d[count($d)-1]->getName();
      
      $this->getBrowserOutput()->writeEOL();
      $this->getBrowserOutput()->write("Required binary missing: `$missingBinary`", 'error');
      $this->getBrowserOutput()->writeEOL();
      die();
    }
    
    foreach($binreq->getData() as $appInfo) {
      $this->getBrowserOutput()->shortcut('output', $appInfo->getPath() . ": " . $appInfo->getVersion());
    }
    
    $this->getBrowserOutput()->write(PHP_EOL."Environment OK.".PHP_EOL, "output highlight");
    
    $remote_repo_masked = $repositoryInfo->remoteRepository;
    $remote_repo_masked = preg_replace('/https:\/\/[a-zA-Z0-9]{1,}\:[^@]{1,}@/i', 'https://***@', $remote_repo_masked);
  
    $this->getBrowserOutput()->write("
Deploying $remote_repo_masked `{$repositoryInfo->branch}`
to        {$repositoryInfo->targetDirectory} ...
", "output highlight");
    
    $this->getBrowserOutput()->writeEOL();
        
    //-------------------- finished environment check --------------------
    
    if (!is_dir($repositoryInfo->tempDirectory)) {
      //temp directory doesn't exist, so clone the remote repository into it.
      $this->getCommandRunner()->addCommand(\PGD\Commands\Command::createFmt(
                  'git clone --depth=1 --branch %s %s %s',
                  true,
                  $repositoryInfo->branch,
                  $repositoryInfo->remoteRepository,
                  $repositoryInfo->tempDirectory
                ));
    } else {
      // temp directory exists and hopefully already contains the correct remote origin
      // so we'll fetch the changes and reset the contents.
      
      $this->getCommandRunner()->addCommand(\PGD\Commands\Command::createFmt(
        'git --git-dir="%s.git" --work-tree="%s" fetch origin %s',
        true,
        $repositoryInfo->tempDirectory,
        $repositoryInfo->tempDirectory,
        $repositoryInfo->branch        
      ));
            
      $this->getCommandRunner()->addCommand(\PGD\Commands\Command::createFmt(
        'git --git-dir="%s.git" --work-tree="%s" reset --hard FETCH_HEAD',
        true,
        $repositoryInfo->tempDirectory,
        $repositoryInfo->tempDirectory
      ));
      
    }
    
    // Update the submodules
    $this->getCommandRunner()->addCommand(new \PGD\Commands\Command('git submodule update --init --recursive'));

    // Describe the deployed version
    if ($repositoryInfo->versionFile !== '') {
      $this->getCommandRunner()->addCommand(\PGD\Commands\Command::createFmt(
        'git --git-dir="%s.git" --work-tree="%s" describe --always > %s',
        false,
        $repositoryInfo->tempDirectory,
        $repositoryInfo->tempDirectory,
        $repositoryInfo->versionFile
      ));
    }
    
    
    // Backup the TARGET_DIR
    // without the BACKUP_DIR for the case when it's inside the TARGET_DIR
    if ($repositoryInfo->backup) {
      $backupFileDateFmt = 'YmdHis'; 
      $this->getCommandRunner()->addCommand(\PGD\Commands\Command::createFmt(
        "tar --exclude='%s*' -czf %s/%s-%s-%s.tar.gz %s*",
        true,
        $repositoryInfo->backupDirectory,
        $repositoryInfo->backupDirectory,
        basename($repositoryInfo->targetDirectory),
        md5($repositoryInfo->targetDirectory),
        date($backupFileDateFmt),
        $repositoryInfo->targetDirectory
      ));  
    }
    
    // Invoke composer
    if ($repositoryInfo->useComposer) {
      $this->getCommandRunner()->addCommand(\PGD\Commands\Command::createFmt(
        'composer --no-ansi --no-interaction --no-progress --working-dir=%s install %s',
        true,
        $repositoryInfo->tempDirectory,
        $repositoryInfo->composerOptions
      ));
      if ($repositoryInfo->composerHome !== false && $repositoryInfo->composerHome !== '' && is_dir($repositoryInfo->composerHome)) {
        putenv('COMPOSER_HOME='.$repositoryInfo->composerHome);
      }
    }
    // ==================================================[ Deployment ]===
    
    // Compile exclude parameters
    $exclude = '';
    foreach (unserialize($repositoryInfo->getExcludedDirectories()) as $exc) {
      $exclude .= " --exclude=$exc";
    }
    // Deployment command
    $this->getCommandRunner()->addCommand(\PGD\Commands\Command::createFmt(
      'rsync -rltgoDzvO %s %s %s %s',
      true,
      $repositoryInfo->tempDirectory,
      $repositoryInfo->targetDirectory,
      $repositoryInfo->deleteFiles ? '--delete-after' : '',
      $exclude
    ));
    
    // =======================================[ Post-Deployment steps ]===
    
    // Remove the TMP_DIR (depends on CLEAN_UP)
    if ($repositoryInfo->cleanUp) {
      //make sure the directory we want to delete exists, and is not /.
      if (is_dir($repositoryInfo->tempDirectory)) {
        if (count(explode(DIRECTORY_SEPARATOR, $repositoryInfo->tempDirectory))>1) {
          $this->getCommandRunner()->addCommand(\PGD\Commands\Command::createFmtNamed(
            'rm -rf %s',
            'cleanup',
            true,
            $repositoryInfo->tempDirectory
          ), 'cleanup');
        }
      }
    }
    
    // =======================================[ Run the command steps ]===
    
    $cr = $this->getCommandRunner();
    
    $execResults = $this->getCommandRunner()->execute('', PHP_EOL);
    $this->getBrowserOutput()->flush();
    
    $all_return_codes = 0;
    
    foreach($execResults as $er)
      $all_return_codes = $all_return_codes & $er->returnCode;
    
    if ($repositoryInfo->cleanUp) {
      
      $execResult = $this->getCommandRunner()->getCommandByName('cleanup')->execute();
      printf('
    
Cleaning up temporary files ...
    
<i class="prompt"></i><span class="command">%s</span>
<div class="output">%s</div>
',
        htmlentities(trim($this->getCommandRunner()->getCommandByName('cleanup')->getCommand())),
        htmlentities(trim($execResult->outputAsString()))
      );
    }
    
    if ($all_return_codes !== 0) {
      error_log(sprintf(
        'Deployment error on %s using %s!',
         (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost'),
         __FILE__
      ));
    }
    
    //-------------------- deploy finished --------------------
    $this->getTimer()->stop();    
    
    $this->getBrowserOutput()->writeEOL();
    $this->getBrowserOutput()->write('Done.'.PHP_EOL, "output highlight");
    $this->getBrowserOutput()->write(sprintf("Deploy took %s seconds.".PHP_EOL, $this->getTimer()->result(true)), "output highlight");
    $this->getBrowserOutput()->writeRaw('</pre></body></html>');
  }
  

}