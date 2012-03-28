<?php
error_reporting(-1);
ini_set('display_errors', 'on');
ini_set('html_errors', 'off');
header('Content-Type: text/plain');

require __DIR__.'/../Http/Request.php';


$r = new \Http\Request($_SERVER, $_GET, file_get_contents('php://input'));


var_export($r->getAcceptTypes());
echo "\n\n";
var_export($r->getAcceptType(array('application/json', 'text/html')));

