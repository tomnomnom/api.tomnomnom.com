<?php
namespace Test\Unit\Http;

class RequestTest extends \PHPUnit_Framework_TestCase {
  public function testAcceptHeadersSimple(){

    $invalidMsg = 'Wrong accept header grouping size';

    $r = new \Http\Request([
      'HTTP_ACCEPT' => 'application/xml,text/html,application/json,text/xhtml'
    ]);
    $this->assertEquals(sizeOf($r->getAcceptTypes()), 4, $invalidMsg);
  }

}

