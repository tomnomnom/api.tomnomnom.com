<?php
namespace Resource;

class Index extends \Http\Resource {
  public function get(){
    $this->response->setBody([
      'resources' => [
        '/crypto/blowfish/randomsalt',
        '/twitter/tweet/retweetcount/:tweetId'
      ]
    ]);
  }
}

