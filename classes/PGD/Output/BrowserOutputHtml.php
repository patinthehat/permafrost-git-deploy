<?php

namespace PGD\Output;

class BrowserOutputHtml extends \PGD\Output\BrowserOutput
{
 
  public function __construct() 
  {
    $this->setContentType('text/html');
  }
  
  public function write($data, $cssClass, $rawOutput = false)
  {
    $data = ($rawOutput ? $data : "<div class=\"$cssClass\">".htmlentities($data)."</div>");
    parent::write($data);
  }

  protected function processOutputData($data)
  {
    $result = $data;
   
    //replace `text` with "<code>text</code>", like markdown
    $result = preg_replace('/`([^`]{0,})`/', "<code>\$1</code>", $result);
  
    return $result;
  }
}
