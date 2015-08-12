<?php

/**
 *
 * Basic data structure to store information about an application,
 * including its name, path, and version information.
 *
 */
class ApplicationInfo {

  /**
   * @var boolean
   */
  protected $exists = false;
  /**
   * @var string
   */
  protected $name = '';
  /**
   * @var string
   */
  protected $path = '';
  /**
   * @var string
   */
  protected $version = '';

  /**
   * Store information about an application: 
   * binary name (basename), full path, exists, version string
   * @param string $name
   * @param string $path
   * @param boolean $exists
   * @param string $version
   */
  public function __construct($name, $path, $exists, $version)
  {
    $this->name = $name;
    $this->path = $path;
    $this->exists = $exists;
    $this->version = $version;
  }
  
  public function getName()
  {
    return $this->name;
  }
  
  public function getPath()
  {
    return $this->path;
  }
  
  public function getVersion()
  {
    return $this->version;
  }

  public function getExists()
  {
    return $this->exists;
  }
  
}