<?php

namespace PGD;

class RequireBinaries {

  /**
   * @var array
   */
  protected $required = array(); 
  
  public function reset()
  {
    $this->required = array();
  }
  
  public function addRequiredBinary($binaryName) 
  {
    if (!in_array($binaryName, $this->required)) {
      $this->required[] = $binaryName;
    }    
  }
  
  public function addRequiredBinaries(array $binaryNames)
  {
    foreach($binaryNames as $binary)
      $this->addRequiredBinary($binary);
  }
  
  public function removeRequiredBinary($binaryName)
  {
    if (in_array($binaryName, $this->required))
      foreach($this->required as &$v) {
        if (strcasecmp($binaryName, $v)===0) {
          $v = "";
          unset($v);
          return true;
        }
      }
    return false;
  }
  
  public function getRequiredBinaryList()
  {
    return $this->required;
  }
  
  /**
   * Check for required binaries
   * 
   * @param boolean $exitOnFailure Stop checking requirements on the first binary not found
   * @return \PGD\RequireBinariesResult
   */
  public function checkRequirements($exitOnFailure = true)
  {
    $results = new \PGD\RequireBinariesResult(true, array());
    
    foreach($this->required as $rb) {
      $path = trim(shell_exec("which $rb"));
      if ($path == '') {
        $results->setResult(false);
        $results->addData(new \PGD\ApplicationInfo($rb, "", false, ""));
        if ($exitOnFailure)
          return $results;
      } else {
        $version = explode("\n", shell_exec("$rb --version")."\n");
        $results->addData(new \PGD\ApplicationInfo($rb, $path, true, $version[0]));
      }      
    }
    return $results;
  }

}
