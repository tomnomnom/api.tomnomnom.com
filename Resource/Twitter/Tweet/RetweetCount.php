<?php
namespace Resource\Twitter\Tweet;

class RetweetCount extends \Http\Resource {
  public function get(){
    
    $pathMatches = $this->request->getPathMatches();
    if (!isset($pathMatches[1]) || trim($pathMatches[1]) == ''){
      throw new \RuntimeException("A Tweet ID must be specified as the last URL chunk");
    }
    $tweetId = $pathMatches[1];

    $url = "https://twitter.com/statuses/show/{$tweetId}.json";

    $httpClient = new \Library\Http\Client();
    $tweetData = json_decode($httpClient->get($url));

    $tweet = new \Library\Twitter\Tweet($tweetData);

    $this->response->setBody([
      'tweetId'      => $tweetId,
      'author'       => $tweet->getAuthor(),
      'retweetCount' => $tweet->getRetweetCount()
    ]);
  }
}

