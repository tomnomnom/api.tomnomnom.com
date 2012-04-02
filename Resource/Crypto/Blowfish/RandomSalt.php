<?php
namespace Resource\Crypto\Blowfish;

class RandomSalt extends \Http\Resource {
  public function get(){
    $blowfish = new \Library\Crypto\Blowfish();

    $this->response->setBody([
      'salt' => $blowfish->randomSalt()
    ]);
  }
}

