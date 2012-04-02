<?php
namespace Resource\Crypto\Blowfish;

class RandomSalt extends \Http\Resource {
  public function get(){
    $blowfish = new \Library\Crypto\Blowfish();
    
    $digitCost = (int) $this->request->getParam('digitCost');
    if (is_null($digitCost)){
      $digitCost = 11;
    }

    $this->response->setBody([
      'salt' => $blowfish->randomSalt($digitCost)
    ]);
  }
}

