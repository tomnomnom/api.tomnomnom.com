<?php
namespace Renderer;

class Json extends \Renderer {
  public function render($body){
    echo json_encode($body);
  }
}
