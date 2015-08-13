<?php

namespace PGD\Output;

abstract class BrowserOutput 
{
  protected $contentType;
  protected $shortcuts = array();
    
  public function setContentType($contentType)
  {
    $this->contentType = $contentType;
  }
  
  public function sendContentType()
  {
    if (!headers_sent() && isset($this->contentType) && !empty($this->contentType))
      header('Content-type: '.$this->contentType);
  }
  
  public function write($data, $dataType = '')
  {
    $data = $this->processOutputData($data);
    echo $data;
    $this->flush();
  }
  
  public function writeRaw($data)
  {
    echo $data;
    $this->flush();
  }
  
  public function flush()
  {
    if (ob_get_length() !== false)
      ob_flush();
    flush();
  }
    
  public function addShortcut($name, $func)
  {
    $this->shortcuts[$name] = $func;
  }
  
  public function shortcut($name, $args = false)
  {
    if (!isset($this->shortcuts[$name]))
      return false;
  
    if ($args) {
      if (is_array($args)) {
        $ret = call_user_func_array($this->shortcuts[$name], $args);
      } else {
        $ret = call_user_func($this->shortcuts[$name], $args);
      }
    } else {
      $ret = call_user_func($this->shortcuts[$name]);
    }
    return $ret;
  }
  
  public function writeEOL()
  {
    $this->writeRaw(PHP_EOL);
  }
  
  public function httpResponse($status)
  {
    header("HTTP/1.0 $status");
  }
  
  public function http403Forbidden($status)
  {
    $this->httpResponse("403 Forbidden");
    die('Forbidden');
  }
  
  abstract protected function processOutputData($data);
}