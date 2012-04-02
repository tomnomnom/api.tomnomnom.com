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

  public function getAcceptType($contentTypes = []){
    $acceptTypes = $this->getAcceptTypes();

    $contentTypes = array_filter(
      array_map([$this, 'parseAcceptType'], $contentTypes)
    );

    // Filter $acceptTypes to only include matches
    $acceptTypes = array_filter($acceptTypes, function($acceptType) use ($contentTypes){
      foreach ($contentTypes as $contentType){
        if ($this->matchAcceptType($acceptType, $contentType)){
          return true;
        }
      }
      return false;
    });

    // Sort by q then specificity
    usort($acceptTypes, function($a, $b){
      if ($a->q > $b->q) return -1;    
      if ($a->q < $b->q) return 1;    

      if ($a->specificity > $b->specificity) return -1;
      if ($a->specificity < $b->specificity) return 1;

      return 0;
    });

    // Take n values from head of list until q or specificity changes
    $bestAcceptTypes = array();
    $last = $acceptTypes[0];
    foreach ($acceptTypes as $acceptType){
      if ($acceptType->q != $last->q) break;
      if ($acceptType->specificity != $last->specificity) break;

      $bestAcceptTypes[] = $acceptType;
      $last = $acceptType;
    }

    // Iterate over contentTypes and return first one in taken list
    foreach ($contentTypes as $contentType){
      foreach ($bestAcceptTypes as $bestAcceptType){
        if ($this->matchAcceptType($bestAcceptType, $contentType)){
          return $contentType->raw;
        }
      }
    }

    // No matching type found
    throw new Exception("Not Acceptable", 406);
  }

  protected function matchAcceptType($acceptType, $contentType){
    if ($acceptType->type == '*') return true;     
    if ($acceptType->type != $contentType->type) return false;

    if ($acceptType->subtype == '*') return true;
    if ($acceptType->subtype != $contentType->subtype) return false;

    if ($acceptType->params == $contentType->params) return true;

    return false;
  }


  protected function getServerVar($key){
    if (!isset($this->serverVars[$key])){
      return null;
    }
    return $this->serverVars[$key];
  }

}
