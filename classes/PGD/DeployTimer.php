<?php
/**
 * @author Patrick Organ
 * 
 * Simple timer class to time the duration of an automated 
 * repository deploy.
 *
 */

namespace PGD;

class DeployTimer {

  /**
   * @var float
   */
  protected $startTime  = 0.0;
  /**
   * @var float
   */
  protected $endTime    = 0.0;
  
  public function __construct($autoStart = false) {
    $this->reset();
    if ($autoStart)
      $this->start();
  }
  
  public function reset()
  {
    $this->endTime = 0.0;
    $this->startTime = 0.0;
  }

  public function start()
  {
    $this->reset();
    $this->startTime = microtime(true);
  }
  
  public function stop()
  {
    $this->endTime = microtime(true);
  }
  
  public function result($formatResult = true)
  {
    $ret = ($this->endTime - $this->startTime);
    if ($formatResult)
      $ret = number_format($ret, 2);    
    return $ret;
  }
  
}