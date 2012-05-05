<?php
error_reporting(-1);
ini_set('display_errors', 'on');
ini_set('html_errors', 'off');

define('DEV_MODE', false);

require __DIR__.'/../Include/Init.php';


$request  = new \Http\Request($_SERVER, $_GET, file_get_contents('php://input'));
$response = new \Http\Response($request);

set_exception_handler([$response, 'exceptionHandler']);
set_error_handler([$response, 'errorHandler']);

$resources = [
  '#^/$#'                                 => '\\Resource\\Index',
  '#^/index$#'                            => '\\Resource\\Index',
  '#^/crypto/blowfish/randomsalt$#'       => '\\Resource\\Crypto\\Blowfish\\RandomSalt',
  '#^/twitter/tweet/retweetcount/(.*)$#'  => '\\Resource\\Twitter\\Tweet\\RetweetCount'
];

$path = $request->getPath();
  
$resourceClass = null;
foreach ($resources as $pattern => $class){
  if (preg_match($pattern, $path, $matches)){
    $resourceClass = $class;
    $request->setPathMatches($matches);
  }
}

if (!class_exists($resourceClass)){
  throw new \Http\Exception(
    "Could not find matching resource for path", 
    \Http\Response::NOT_FOUND
  );
}

$resource = new $resourceClass($request, $response);

$resource->dispatch();


