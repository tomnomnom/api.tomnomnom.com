<?php
error_reporting(-1);

spl_autoload_register(function($class){
  $class = str_replace('\\', '/', $class);
  require __DIR__."/../{$class}.php";
});

