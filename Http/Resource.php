<?php
namespace Http;

abstract class Resource {
  protected $request;
  protected $response; 

  public function __construct(Request $request, Response $response){
    $this->request  = $request;
    $this->response = $response;
  }

  public function getResponse(){
    return $this->response;
  }

  public function head(){
    $this->response->setCode(Response::METHOD_NOT_ALLOWED);
  }

  public function get(){
    $this->response->setCode(Response::METHOD_NOT_ALLOWED);
  }

  public function post(){
    $this->response->setCode(Response::METHOD_NOT_ALLOWED);
  }

  public function put(){
    $this->response->setCode(Response::METHOD_NOT_ALLOWED);
  }

  public function delete(){
    $this->response->setCode(Response::METHOD_NOT_ALLOWED);
  }

  public function trace(){
    $this->response->setCode(Response::METHOD_NOT_ALLOWED);
  }

  public function options(){
    $this->response->setCode(Response::METHOD_NOT_ALLOWED);
  }

  public function connect(Request $request, Response $response){
    $this->response->setCode(Response::METHOD_NOT_ALLOWED);
  }

  public function patch(Request $request, Response $response){
    $this->response->setCode(Response::METHOD_NOT_ALLOWED);
  }
}

