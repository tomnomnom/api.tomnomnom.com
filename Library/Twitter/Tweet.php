<?php
namespace Library\Twitter;

class Tweet {
  protected $tweetData;

  public function __construct(\StdClass $tweetData = null){
    if (!is_null($tweetData)){
      $this->setTweetData($tweetData);
    }
  }

  public function setTweetData(\StdClass $tweetData){
    $this->tweetData = $tweetData;
  }

  public function getRetweetCount(){
    if (!isset($this->tweetData->retweet_count)){
      throw new \RuntimeException("Tweet data does not include retweet count");
    }
    return (int) $this->tweetData->retweet_count;
  }

  public function getAuthor(){
    if (!isset($this->tweetData->user->screen_name)){
      throw new \RuntimeException("Tweet data does not include screen name");
    }
    return $this->tweetData->user->screen_name;
  }
}
