<?php

namespace PGD;

class RepositoryInfoCollection implements \Countable
{

  protected $items = array();
  
  public function __construct() 
  {
    $this->reset();
  }
  
  public function reset()
  {
    $this->items = array();
    return $this;
  }

  public function add(\PGD\RepositoryInfo $repositoryInfo)
  {
    $this->items[$repositoryInfo->name] = $repositoryInfo;
    return $this;
  }

  /**
   * Return a stored repository based on its name, or FALSE on error.
   * @param string $repositoryName
   * @return \PGD\RepositoryInfo|boolean
   */
  public function get($repositoryName)
  {
    foreach($this->getItems() as $k=>$v) {
      if ($v->name == $repositoryName) {
        return $v;
      }
    }
    return false;
  }

  public function has($repositoryName)
  {
    if (!$repositoryName || $repositoryName == '' || !\is_string($repositoryName))
      return false;

    return isset($this->items["$repositoryName"]);
  }
  
  public function getItems()
  {
    return $this->items;
  }
  
  /* Countable */
  public function count()
  {
    return count($this->getItem());
  }
  
}
