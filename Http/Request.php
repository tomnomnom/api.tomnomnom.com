<?php
namespace Http;

class Request {
  protected $serverVars;
  protected $getVars;
  protected $postData;

  public function __construct($serverVars = array(), $getVars = array(), $postData = array()){
    $this->serverVars = $serverVars;
    $this->getVars = $getVars;
    $this->postData = $postData;
  }

  protected function parseAcceptType($rawType){
    $type = explode(';', $rawType, 2);

    if (isset($type[1])){
      $qf = substr($type[1], 2);
    } else {
      $qf = 1;
    }

    list($type, $subtype) = explode('/', $type[0]);
    if ($qf == 0) return null;
    return (object) array(
      'type'    => $type,
      'subtype' => $subtype,
      'qf'      => $qf
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

    usort($types, function($a, $b){
      // Sort on qf first
      if ($a->qf <  $b->qf) return 1;
      if ($a->qf >  $b->qf) return -1;

      // Both qfs are the same; sort on type
      if ($a->type == '*' && $b->type != '*') return 1;
      if ($a->type != '*' && $b->type == '*') return -1;

      // Both types are the same; sort on subtype
      if ($a->subtype == '*' && $b->subtype != '*') return 1;
      if ($a->subtype != '*' && $b->subtype == '*') return -1;

      // Equal validity one way or another
      return 0;
    });

    return $types;
  }

  protected function getServerVar($key){
    if (!isset($this->serverVars[$key])){
      return null;
    }
    return $this->serverVars[$key];
  }

  public function getAcceptType($availableTypes = array()){
    
    // Accept types are ordered by preference
    foreach ($this->getAcceptTypes() as $type){
      $acceptStr = $type->type.'/'.$type->subtype;

      // For a full wildcard, return the first available type
      if ($acceptStr == '*/*'){
        return array_pop($availableTypes);
      }
      
      foreach ($availableTypes as $availableType){
        // Exact match
        if ($acceptStr == $availableType){
          return $availableType;
        }

        if ($type->subtype == '*'){
          $availableTypeParts = explode('/', $availableType);
          if ($type->type == $availableTypeParts[0]){
            return $availableType;
          }
        }
      }
    }

    return null;
  }
}
