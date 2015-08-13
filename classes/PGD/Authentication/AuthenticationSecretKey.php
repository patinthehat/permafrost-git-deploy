<?php
/**
 * @author Patrick Organ
 * @license MIT
 *
 * Authentication class that implements the standard "deploy script" security-- 
 * a "secret key" that must be passed via GET (or POST) in order to allow
 * a deploy to be initiated.
 */
 
namespace PGD\Authentication;

class AuthenticationSecretKey extends \PGD\Authentication\Authentication
{
  protected $paramName;
  protected $key;
  protected $method;

  /**
   * @param string $secretKey secret key required to authorize and initiate a deploy. 
   * @param string $parameterName Param name to find the either $_GET or $_POST.
   * @param string $getOrPost Indicate the parameter should be found in either 'get' or 'post'.
   */
  public function __construct($secretKey, $parameterName, $getOrPost = 'get') 
  {
    $this->key = $secretKey;
    $this->paramName = $parameterName;
    $this->method = (in_array(strtolower($getOrPost), array('get','post')) ? $getOrPost : 'get');
  }
  
  public function getKey()
  {
    return $this->key;
  }
  
  public function getParamName()
  {
    return $this->paramName;
  }
  
  public function getMethod()
  {
    return $this->method;
  }

  public function authenticate()
  {
    if (!$this->getParamName() || $this->getParamName() == '')
      return false;
    
    $methods = array('get' => $_GET, 'post' => $_POST);
    $data = (isset($methods[$this->getMethod()]) ? $methods[$this->getMethod()] : $methods['get']);
    
    
    if (isset($data[$this->getParamName()]) && 
      $data[$this->getParamName()] === $this->getKey()) {
        return true;  
      }
      
    return false;
  }
  
}
