<?php

namespace PGD\Authentication;

class AuthenticationDriver 
{

  /**
   * @var \PGD\Authentication\Authentication
   */
  protected $authenticator;
  
  public function __construct(\PGD\Authentication\Authentication $auth) 
  {
    $this->authenticator = $auth;
  }

  public function authenticate()
  {
    return $this->authenticator->authenticate();    
  }
  
}
