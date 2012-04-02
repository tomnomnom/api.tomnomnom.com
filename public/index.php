<?php
error_reporting(-1);
ini_set('display_errors', 'on');
ini_set('html_errors', 'off');

require __DIR__.'/../Include/Init.php';


$request  = new \Http\Request($_SERVER, $_GET, file_get_contents('php://input'));
$response = new \Http\Response($request);

$resources = [
  '#^/crypto/blowfishsalt$#' => '\\Resource\\Crypto\\BlowfishSalt'
];

$path = $request->getPath();
  
$resourceClass = null;
foreach ($resources as $pattern => $class){
  if (preg_match($pattern, $path)){
    $resourceClass = $class;
  }
}

if (is_null($resourceClass)){
  throw new \Exception("Could not find matching class for path");
}

if (!class_exists($resourceClass)){
  throw new \Exception("Could not find matching class for path");
}

$resource = new $resourceClass($request, $response);

$resource->dispatch();


