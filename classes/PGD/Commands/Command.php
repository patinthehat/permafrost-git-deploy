<?php

/**
 * A basic class that runs a shell command and returns the result of execution.
 * @see \CommandResult
 */
 
namespace PGD\Commands;

class Command 
{
  protected $command;
  protected $name;

  /**
   * @param string $command The shell command to execute.
   * @param boolean $escapeCommand Escapes the $command parameter using escapeshellcmd().
   */
  public function __construct($command, $escapeCommand = true) 
  {
    $this->command = ($escapeCommand ? escapeshellcmd($command) : $command);
  }
  
  public static function create($command, $escapeCommand = true)
  {
    $c = new self($command, $escapeCommand);
    return $c;
  }
  
  public static function createFmt($commandFmt, $escapeCommand = true, $args)
  {
    $n = func_num_args();
    $argv = array();
    for($i=2; $i < $n; $i++)
      $argv[] = func_get_arg($i);
    $c = new self(vsprintf($commandFmt, $argv), $escapeCommand);
    return $c;
  }
  
  public static function createFmtNamed($commandFmt, $commandName, $escapeCommand = true, $args) 
  {
    $c = self::createFmt($commandFmt, $escapeCommand, $args);
    $c->setName($commandName);
    return $c;
  }
  
  public function setName($name)
  {
    $this->name = $name;
  }
  
  public function getName()
  {
    return $this->name;
  }
  
  public function getCommand()
  {
    return $this->command;
  }
  
  public function execute()
  {
    $result = new \PGD\Commands\CommandResult();    
    exec($this->getCommand().' 2>&1', $result->output, $result->returnCode);
    return $result;
  }
  
}
