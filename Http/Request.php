<?php
namespace Http;

class Request {
  protected $serverVars;
  protected $getVars;
  protected $postData;

  public function __construct($serverVars = [], $getVars = [], $postData = []){
    $this->serverVars = $serverVars;
    $this->getVars    = $getVars;
    $this->postData   = $postData;
  }

  protected function parseAcceptType($rawType){
    $rawType = trim($rawType);
    $rawParams = explode(';', $rawType);
    $type = array_shift($rawParams);

    // Default quality factor is '1'
    $q = 1;

    $params = [];
    foreach ($rawParams as $rawParam){
      list($key, $value) = explode('=', $rawParam);
      $key = trim($key);
      $value = trim($value);
      if ($key == 'q'){
        $q = $value; 
      } else {
        $params[$key] = $value; 
      }
    }

    if ($q == 0){
      // We specifically don't want this type
      return null;
    }

    list($type, $subtype) = explode('/', $type);

    // 0 is the least specific
    $specificity = 0;
    if ($type    != '*') $specificity++;
    if ($subtype != '*') $specificity++;
    $specificity += sizeOf($params);

    return (object) [
      'type'        => $type,
      'subtype'     => $subtype,
      'q'           => $q,
      'raw'         => $rawType,
      'specificity' => $specificity,
      'params'      => (object) $params
    ];
  }

  public function getAcceptTypes(){
    // See http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
    $rawTypes = $this->getServerVar('HTTP_ACCEPT');
    if (is_null($rawTypes)) return [];

    $types = explode(',', $rawTypes);

    $types = array_filter(
      array_map([$this, 'parseAcceptType'], $types)
    );

    return $types;
  }

  protected function getServerVar($key){
    if (!isset($this->serverVars[$key])){
      return null;
    }
    return $this->serverVars[$key];
  }

}
