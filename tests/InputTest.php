<?php
use PHPUnit\Framework\TestCase;

class InputTest extends TestCase
{
  public function testSingletonInvalidType()
  {
    $this->expectException(\Limbonia\Exception::class);
    $oInput = \Limbonia\Input::singleton('invalid_type');
  }

  public function singletonProvider()
  {
    return
    [
      ['cookie'],
      ['env'],
      ['get'],
      ['post'],
      ['server']
    ];
  }

  /**
   * @dataProvider singletonProvider
   */
  public function testSingletonValidType($sType)
  {
    $oInput = \Limbonia\Input::singleton($sType);
    $this->assertTrue($oInput instanceof \Limbonia\Input);
  }
}