<?php
namespace Http;

class Request {
  protected $serverVars;
  protected $getVars;
  protected $postData;

  public function __construct($serverVars = array(), $getVars = array(), $postData = array()){
    $this->serverVars = $serverVars;
    $this->getVars    = $getVars;
    $this->postData   = $postData;
  }

  protected function parseAcceptType($rawType){
    $type = explode(';', $rawType, 2);

    if (isset($type[1])){
      //TODO: parse param properly
      $q = substr($type[1], 2);
    } else {
      $q = 1;
    }

    list($type, $subtype) = explode('/', $type[0]);
    if ($q == 0) return null;
    return (object) array(
      'type'    => $type,
      'subtype' => $subtype,
      'q'       => $q,
      'string'  => $type.'/'.$subtype
    );
  }

  public function getAcceptTypes(){
    // See http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
    $rawTypes = $this->getServerVar('HTTP_ACCEPT');
    if (is_null($rawTypes)) return array();

    $types = explode(',', $rawTypes);

    $types = array_filter(
      array_map(array($this, 'parseAcceptType'), $types)
    );

    $groupedTypes = array();
    foreach ($types as $type){
      if (!isset($groupedTypes[$type->q])){
        $groupedTypes[$type->q] = array();
      }
      $groupedTypes[$type->q][] = $type;
    }

    krsort($groupedTypes);
    return $groupedTypes;
  }

  protected function getServerVar($key){
    if (!isset($this->serverVars[$key])){
      return null;
    }
    return $this->serverVars[$key];
  }

}
