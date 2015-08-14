<?php

namespace PGD\Authentication;

class AuthenticationDriver 
{

  /**
   * @var \PGD\Authentication\Authentication
   */
  protected $authenticator;
  
  /**
   * @var \PGD\Output\BrowserOutput
   */
  protected $output;
  
  public function __construct(\PGD\Output\BrowserOutput $output, \PGD\Authentication\Authentication $auth) 
  {
    $this->authenticator = $auth;
    $this->output = $output;
  }

  public function authenticate()
  {
    $result = $this->authenticator->authenticate();
    
    if ($result) {
      $this->output->writeQueued('Authorization succeeded.'.PHP_EOL, 'output highlight');
    } else {
      $this->output->writeQueued('Authorization failed.'.PHP_EOL, 'error highlight');
    }
    
    return $result;      
  }
  
}
