<?php
use PHPUnit\Framework\TestCase;

//require the Test Hash so we can test the traits through it...
require 'lib/Test/Hash.php';

class TraitHashTest extends TestCase
{
  protected $hTestData =
  [
    'Test1' => 'This is a test',
    'Test2' => 'Another test'
  ];

  protected $sInvalidKey = 'ThisKeyDoesNotExist';

  protected function getKey()
  {
    $aKey = array_keys($this->hTestData);
    return $aKey[0];
  }

  public function testNewHashIsEmpty()
  {
    $oHash = new \Limbonia\Test\Hash();
    $this->assertEmpty($oHash->getRaw());
  }

  public function testGetRawReturnsArray()
  {
    $oHash = new \Limbonia\Test\Hash();
    $this->assertTrue(is_array($oHash->getRaw()));
  }

  public function testGetRawReturnsExpectedData()
  {
    $oHash = new \Limbonia\Test\Hash($this->hTestData);
    $hLowerCaseKeysData = [];

    foreach ($this->hTestData as $xKey => $xData)
    {
      $hLowerCaseKeysData[\strtolower($xKey)] = $xData;
    }

    $this->assertEquals($hLowerCaseKeysData, $oHash->getRaw());
  }

  public function testGetIfKeyExists()
  {
    $xKey = $this->getKey();
    $oHash = new \Limbonia\Test\Hash($this->hTestData);

    $this->assertEquals($this->hTestData[$xKey], $oHash->__get($xKey));
  }

  public function testGetIfKeyDoesNotExist()
  {
    $oHash = new \Limbonia\Test\Hash();

    $this->assertNull($oHash->__get($this->sInvalidKey));
  }

  public function testSet()
  {
    $xKey = $this->getKey();
    $oHash = new \Limbonia\Test\Hash();
    $oHash->__set($xKey, $this->hTestData[$xKey]);

    $this->assertEquals($this->hTestData[$xKey], $oHash->__get($xKey));
  }

  public function testIsSetIfKeyExists()
  {
    $xKey = $this->getKey();
    $oHash = new \Limbonia\Test\Hash();
    $oHash->__set($xKey, $this->hTestData[$xKey]);

    $this->assertTrue($oHash->__isset($xKey));
  }

  public function testIsSetIfKeyDoesNotExists()
  {
    $oHash = new \Limbonia\Test\Hash();

    $this->assertFalse($oHash->__isset($this->sInvalidKey));
  }

  public function testUnSet()
  {
    $xKey = $this->getKey();
    $oHash = new \Limbonia\Test\Hash();
    $oHash->__set($xKey, $this->hTestData[$xKey]);
    $oHash->__unset($xKey);

    $this->assertFalse($oHash->__isset($xKey));
  }

  public function testOffsetSet()
  {
    $xKey = $this->getKey();
    $oHash = new \Limbonia\Test\Hash();
    $oHash->offsetset($xKey, $this->hTestData[$xKey]);

    $this->assertEquals($this->hTestData[$xKey], $oHash->__get($xKey));
  }

  public function testOffsetUnSet()
  {
    $xKey = $this->getKey();
    $oHash = new \Limbonia\Test\Hash($this->hTestData);
    $oHash->offsetUnset($xKey);

    $this->assertFalse($oHash->__isset($xKey));
  }

  public function testOffsetExistsIfKeyExists()
  {
    $xKey = $this->getKey();
    $oHash = new \Limbonia\Test\Hash($this->hTestData);

    $this->assertTrue($oHash->offsetExists($xKey));
  }

  public function testOffsetExistsIfKeyDoesNotExists()
  {
    $oHash = new \Limbonia\Test\Hash();

    $this->assertFalse($oHash->offsetExists($this->sInvalidKey));
  }

  public function testOffsetGetIfKeyExists()
  {
    $xKey = $this->getKey();
    $oHash = new \Limbonia\Test\Hash($this->hTestData);

    $this->assertEquals($this->hTestData[$xKey], $oHash->offsetget($xKey));
  }

  public function testOffsetGetIfKeyDoesNotExist()
  {
    $oHash = new \Limbonia\Test\Hash();

    $this->assertNull($oHash->offsetget($this->sInvalidKey));
  }

  public function testCount()
  {
    $oHash = new \Limbonia\Test\Hash();

    //since it starts empty, the starting count should be 0
    $this->assertEquals(0, $oHash->count());

    $iTestCount = 10;

    for ($i = 0; $i < $iTestCount; $i++)
    {
      $oHash->__set(rand(), rand());
    }

    $this->assertEquals($iTestCount, $oHash->count());
  }

  public function testCurrent()
  {
    $xKey = $this->getKey();
    $oHash = new \Limbonia\Test\Hash($this->hTestData);

    $this->assertEquals($this->hTestData[$xKey], $oHash->current());
  }

  public function testKey()
  {
    $xKey = \strtolower($this->getKey());
    $oHash = new \Limbonia\Test\Hash($this->hTestData);

    $this->assertEquals($xKey, $oHash->key());
  }

  public function testNext()
  {
    $aKey = array_keys($this->hTestData);
    $xKey = $aKey[1];
    $oHash = new \Limbonia\Test\Hash($this->hTestData);

    //advance the internal pointer
    $oHash->next();

    $this->assertEquals($this->hTestData[$xKey], $oHash->current());
  }

  public function testRewind()
  {
    $xKey = $this->getKey();
    $oHash = new \Limbonia\Test\Hash($this->hTestData);

    //advance the internal pointer
    $oHash->next();

    //now rewind it to the beginning...
    $oHash->rewind();

    $this->assertEquals($this->hTestData[$xKey], $oHash->current());
  }

  public function testNotValidOnEmptyData()
  {
    $oHash = new \Limbonia\Test\Hash();

    $this->assertFalse($oHash->valid());
  }

  public function testNotValidOnPassedEndOfData()
  {
    $oHash = new \Limbonia\Test\Hash($this->hTestData);
    $iCount = $oHash->count();

    //we start on the first item, so advancing the pointer by the count should always be one off the end...
    for ($i = 0; $i < $iCount; $i++)
    {
      $oHash->next();
    }

    $this->assertFalse($oHash->valid());
  }

  public function testValidOnSetData()
  {
    $oHash = new \Limbonia\Test\Hash($this->hTestData);

    $this->assertTrue($oHash->valid());
  }

  public function testSeekOutOfBounds()
  {
    $oHash = new \Limbonia\Test\Hash();

    $this->expectException(\OutOfBoundsException::class);
    $oHash->seek($this->sInvalidKey);
  }

  public function testSeek()
  {
    $sKey = 'Foo';
    $sData = 'Bar';
    $oHash = new \Limbonia\Test\Hash($this->hTestData);
    $oHash->__set($sKey, $sData);
    $oHash->seek($sKey);

    $this->assertEquals($sData, $oHash->current());
  }
}