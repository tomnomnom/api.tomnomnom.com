<?php
namespace Test\Unit\Http;

class RequestTest extends \PHPUnit_Framework_TestCase {
  public function testAcceptHeadersGrouping(){

    $invalidMsg = 'Wrong accept header grouping size';

    $r = new \Http\Request(array(
      'HTTP_ACCEPT' => 'application/xml,text/html,application/json,text/xhtml'
    ));
    $this->assertEquals(sizeOf($r->getAcceptTypes()), 1, $invalidMsg);

    $r = new \Http\Request(array(
      'HTTP_ACCEPT' => 'application/xml,text/html;q=0.3,application/json,text/xhtml;q=0.3'
    ));
    $this->assertEquals(sizeOf($r->getAcceptTypes()), 2, $invalidMsg);

    $r = new \Http\Request(array(
      'HTTP_ACCEPT' => 'application/xml,text/html;q=0.3,application/json;0.755,text/xhtml;q=0.3'
    ));
    $this->assertEquals(sizeOf($r->getAcceptTypes()), 3, $invalidMsg);
  }

  protected function mapToStrings($types){
    return array_map(function($type){
      return $type->string;
    }, $types);
  }
}

