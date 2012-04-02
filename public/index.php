<?php
error_reporting(-1);
ini_set('display_errors', 'on');
ini_set('html_errors', 'off');

require __DIR__.'/../Include/Init.php';


$request  = new \Http\Request($_SERVER, $_GET, file_get_contents('php://input'));
$response = new \Http\Response($request);


$response->sendHeaders();


