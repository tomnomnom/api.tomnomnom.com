<?php
namespace Http;

class Response {
  protected $contentTypes = array(
    'application/json',
    'text/html'
  );

  public function getContentTypes(){
    return $this->contentTypes;
  }
}

