<?php

class CommandRunner 
{
  protected $outputter;
  protected $commandTimeLimit;
  protected $commands = array();

  public function __construct(\BrowserOutput $outputter, $commandTimeLimit = 30) 
  {
    $this->outputter = $outputter;
    $this->commandTimeLimit = $commandTimeLimit;
  }

  public function getCommands()
  {
    return $this->commands;
  }
  
  /**
   * @param string $name
   * @return \\Command|boolean
   */
  public function getCommandByName($name)
  {
    if (isset($this->getCommands()[$name]))
      return $this->getCommands()[$name];
    return false;
  }
  
  /**
   * @return \\BrowserOutput
   */
  public function getOutputter()
  {
    return $this->outputter;
  }
  
  public function addCommand(\Command $cmd, $commandName = "")
  {
    if (!$commandName || trim($commandName)=='') {
      $this->commands[] = $cmd;
    } else {
      $this->commands[$commandName] = $cmd;
    }
  }
  
  public function execute($addlDataType = '', $writeAfterEachCommand = '')
  {
    $results = array();
    foreach($this->getCommands() as $cmd) {
      $ret = $cmd->execute();
      if (trim($addlDataType)!=='')
        $addlDataType = " $addlDataType";
      
      $this->getOutputter()->writeRaw('<i class="prompt"></i>');
      $this->getOutputter()->write($cmd->getCommand(), 'command inline');
      $this->getOutputter()->write($ret->outputAsString(), "output$addlDataType");
      if ($writeAfterEachCommand !== '')
        $this->getOutputter()->writeRaw($writeAfterEachCommand);
      $results[] = $ret;
    }
    return $results;
  }
  
  public function executeSingleCommand(\Command $cmd, $addlDataType = '')
  {
    if (trim($addlDataType)!=='')
      $addlDataType = " $addlDataType";
    
    $ret = $cmd->execute();
    $this->getOutputter()->write($ret->outputAsString(), "output$addlDataType");
  }
  
}
