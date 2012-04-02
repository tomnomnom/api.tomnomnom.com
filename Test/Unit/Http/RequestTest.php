<?php
namespace Test\Unit\Http;

class RequestTest extends \PHPUnit_Framework_TestCase {
  public function testAcceptHeadersSimple(){

    $invalidMsg = 'Wrong number of types returned';

    $r = new \Http\Request([
      'HTTP_ACCEPT' => 'application/xml,text/html,application/json,text/xhtml'
    ]);
    $this->assertEquals(sizeOf($r->getAcceptTypes()), 4, $invalidMsg);

    $r = new \Http\Request([
      'HTTP_ACCEPT' => 'application/xml'
    ]);
    $this->assertEquals(sizeOf($r->getAcceptTypes()), 1, $invalidMsg);

    $r = new \Http\Request([
      'HTTP_ACCEPT' => 'application/xml;q=0.9, text/html; level=1; q=0.8'
    ]);
    $this->assertEquals(sizeOf($r->getAcceptTypes()), 2, $invalidMsg);

    $r = new \Http\Request([
      'HTTP_ACCEPT' => 'application/xml;q=0.9'
    ]);
    $this->assertEquals(sizeOf($r->getAcceptTypes()), 1, $invalidMsg);
  }

  public function testParamParsing(){
    $r = new \Http\Request([
      'HTTP_ACCEPT' => 'text/html;q=1;level=4;foo=bar'
    ]);
    $type = $r->getAcceptTypes()[0];

    $this->assertEquals($type->q, 1, "'q' value was not parsed correctly");
    $this->assertEquals($type->params->level, 4, "'level' param was not parsed correctly");
    $this->assertEquals($type->params->foo, 'bar', "'foo' param was not parsed correctly");
  }

  public function testDefaultQValue(){
    $r = new \Http\Request(['HTTP_ACCEPT' => 'text/html']);
    $type = $r->getAcceptTypes()[0];

    $this->assertEquals($type->q, 1, "Default 'q' value should be '1'");
  }

  public function testSpecificity(){
    $invalidMsg = 'Specificity was not correctly determined';

    $r = new \Http\Request(['HTTP_ACCEPT' => 'text/html']);
    $this->assertEquals($r->getAcceptTypes()[0]->specificity, 2, $invalidMsg);

    $r = new \Http\Request(['HTTP_ACCEPT' => 'text/*']);
    $this->assertEquals($r->getAcceptTypes()[0]->specificity, 1, $invalidMsg);

    $r = new \Http\Request(['HTTP_ACCEPT' => '*/*']);
    $this->assertEquals($r->getAcceptTypes()[0]->specificity, 0, $invalidMsg);

    $r = new \Http\Request(['HTTP_ACCEPT' => 'text/html;level=1']);
    $this->assertEquals($r->getAcceptTypes()[0]->specificity, 3, $invalidMsg);

    $r = new \Http\Request(['HTTP_ACCEPT' => 'text/html;level=1;foo=bar']);
    $this->assertEquals($r->getAcceptTypes()[0]->specificity, 4, $invalidMsg);

    $r = new \Http\Request(['HTTP_ACCEPT' => 'text/html;q=1;level=1;foo=bar']);
    $this->assertEquals($r->getAcceptTypes()[0]->specificity, 4, $invalidMsg);
  }

  public function testTypeSubtypeSplit(){
    $invalidMsg = 'Type was not correctly split';

    $r = new \Http\Request(['HTTP_ACCEPT' => 'text/html']);
    $this->assertEquals($r->getAcceptTypes()[0]->type, 'text', $invalidMsg);
    $this->assertEquals($r->getAcceptTypes()[0]->subtype, 'html', $invalidMsg);
  }

  public function testGetAcceptType(){
    $invalidMsg = 'Incorrect content-type returned';
    $r = new \Http\Request([
      'HTTP_ACCEPT' => 'application/xml;q=1, application/json;q=0.8, text/xhtml, text/html;q=0.8, text/*;q=0.8'
    ]);
    
    $this->assertEquals(
      $r->getAcceptType(['application/json', 'text/html']),
      'application/json', $invalidMsg
    );

    $this->assertEquals(
      $r->getAcceptType(['application/json', 'text/xhtml']),
      'text/xhtml', $invalidMsg
    );

    $this->assertEquals(
      $r->getAcceptType(['application/xml', 'text/xhtml']),
      'application/xml', $invalidMsg
    );

    try {
      $r->getAcceptType(['audio/basic', 'audio/mp3']);
      $this->fail("No exception thrown when no matching type found");
    } catch (\Http\Exception $e){
      $this->assertEquals($e->getCode(), 406, "No matching type should throw HTTP 406");
    }

  }
}

