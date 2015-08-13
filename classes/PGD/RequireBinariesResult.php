<?php
/**
 * 
 * Stores the results of a \PGD\RequireBinaries->checkRequirements() call.
 */

namespace PGD;

class RequireBinariesResult {
  
  protected $result = false;
  protected $data = array();

  public function __construct($result, array $data)
  {
    $this->result = $result;
    $this->data = $data;
  }
  
  public function getResult()
  {
    return $this->result;
  }
  
  public function setResult($result)
  {
    $this->result = $result;
  }
  
  public function getData()
  {
    return $this->data;
  }
  
  public function setData(array $data) 
  {
    $this->data = $data;
  }
  
  public function addData($item)
  {
    $this->data[] = $item;
  }

}