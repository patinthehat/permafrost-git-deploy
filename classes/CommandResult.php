<?php

class CommandResult 
{
  public $output;
  public $returnCode;
  
  public function __construct() 
  {
    //
  }
  
  public function succeeded()
  {
    return ($this->returnCode === 0);
  }
  
  public function failed()
  {
    return ($this->returnCode !== 0);
  }
  
  public function outputAsString()
  {
    if (is_array($this->output))
      return implode("\n", $this->output);
    
    return $this->output;
  }

}
