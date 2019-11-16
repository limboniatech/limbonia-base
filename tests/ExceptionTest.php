<?php
use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{
  public function testThrowException()
  {
    $this->expectException(\Limbonia\Exception::class);
    throw new \Limbonia\Exception('This is a test exception');
  }
}