<?php

namespace PGD\Authentication;

abstract class Authentication 
{

  public function __construct() 
  {
    //
  }
  
  abstract public function authenticate();

}
