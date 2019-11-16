<?php
use PHPUnit\Framework\TestCase;

//set up the DireTemplate...

class DomainTest extends TestCase
{
  public function testSetDirTemplate()
  {
    $sDirTemplate = __DIR__ . '/testdomains/__DOMAIN__/__SUB__/html';
    \Limbonia\Domain::setDirTemplate($sDirTemplate);
    $oReflectedDomain = new \ReflectionClass(\Limbonia\Domain::class);
    $hStaticProperites = $oReflectedDomain->getStaticProperties();

    $this->assertEquals($sDirTemplate, $hStaticProperites['sDomainDirTemplate']);
  }

  public function testGeneratePathForInvalidDomain()
  {
    $this->expectException(\Limbonia\Exception::class);
    $sDomainPath = \Limbonia\Domain::generatePath('domain');
  }

  public function testGeneratePathForBaseDomain()
  {
    $this->assertEquals(__DIR__ . '/testdomains/domain.com/www/html', \Limbonia\Domain::generatePath('domain.com'));
  }

  public function testGeneratePathForSubDomain()
  {
    $this->assertEquals(__DIR__ . '/testdomains/domain.com/sub/html', \Limbonia\Domain::generatePath('sub.domain.com'));
  }

  public function testGenerateNameForInvalidDomain()
  {
    $this->expectException(\Limbonia\Exception::class);
    $sDomainPath = \Limbonia\Domain::generateName('/bad/path');
  }

  public function testGenerateNameForBaseDomain()
  {
    $this->assertEquals('domain.com', \Limbonia\Domain::generateName(__DIR__ . '/testdomains/domain.com/www/html'));
  }

  public function testGenerateNameForSubDomain()
  {
    $this->assertEquals('sub.domain.com', \Limbonia\Domain::generateName(__DIR__ . '/testdomains/domain.com/sub/html'));
  }

  public function testProtocolIfNoServerHttps()
  {
    $oServer = \Limbonia\Input::singleton('server');
    unset($oServer->https);

    $this->assertEquals('http', \Limbonia\Domain::protocol());
  }

  public function testProtocolIfServerHttpsSetOff()
  {
    $oReflectedDomain = new \ReflectionClass(\Limbonia\Domain::class);
    $oReflectedProtocol = $oReflectedDomain->getProperty('sProtocol');
    $oReflectedProtocol->setAccessible(true);
    $oReflectedProtocol->setValue('');
    $oServer = \Limbonia\Input::singleton('server');
    $oServer->https = 'off';

    $this->assertEquals('http', \Limbonia\Domain::protocol());
  }

  public function testProtocolIfServerHttpsSetOn()
  {
    $oReflectedDomain = new \ReflectionClass(\Limbonia\Domain::class);
    $oReflectedProtocol = $oReflectedDomain->getProperty('sProtocol');
    $oReflectedProtocol->setAccessible(true);
    $oReflectedProtocol->setValue('');
    $oServer = \Limbonia\Input::singleton('server');
    $oServer->https = 'on';

    $this->assertEquals('https', \Limbonia\Domain::protocol());
  }

  public function testContructorWithDomainOnly()
  {
    $oDomain = new \Limbonia\Domain('domain.com');
    $this->assertTrue($oDomain instanceof \Limbonia\Domain);
  }

  public function testContructorWithUri()
  {
    $oDomain = new \Limbonia\Domain('domain.com/test');
    $this->assertTrue($oDomain instanceof \Limbonia\Domain);
  }

  public function testContructorWithDomainAndPath()
  {
    $oDomain = new \Limbonia\Domain('domain.com', '/path/to/test');
    $this->assertTrue($oDomain instanceof \Limbonia\Domain);
  }

  public function testFactoryWithInvalidDomain()
  {
    $this->expectException(\Limbonia\Exception::class);
    $oDomain = \Limbonia\Domain::factory('invalid_domain');
  }

  public function testFactoryWithValidDomain()
  {
    $oDomain = \Limbonia\Domain::factory('domain.com');
    $this->assertTrue($oDomain instanceof \Limbonia\Domain);
  }

  public function testGetByDirectoryWithInvalidPath()
  {
    $this->expectException(\Limbonia\Exception::class);
    $oDomain = \Limbonia\Domain::getByDirectory('/path/to/test');
  }

  public function testGetByDirectoryWithValidButNonexistantPath()
  {
    $oDomain = \Limbonia\Domain::getByDirectory(__DIR__ . '/testdomains/doesnotexist.com/www/html');
    $this->assertTrue($oDomain instanceof \Limbonia\Domain);
  }

  public function testGetByDirectoryCheckingForNonexistantPath()
  {
    $this->expectException(\Limbonia\Exception::class);
    $oDomain = \Limbonia\Domain::getByDirectory(__DIR__ . '/testdomains/doesnotexist.com/www/html', true);
  }

  public function testGetByDirectoryWithValidPath()
  {
    $oDomain = \Limbonia\Domain::getByDirectory(__DIR__ . '/testdomains/domain.com/www/html');
    $this->assertTrue($oDomain instanceof \Limbonia\Domain);
  }

  public function gettersDomainWithoutUriProvider()
  {
    return
    [
      ['protocol', 'https'],
      ['name', 'domain.com'],
      ['path', __DIR__ . '/testdomains/domain.com/www/html'],
      ['uri', ''],
      ['url', '//domain.com'],
      ['currenturl', 'https://domain.com'],
      ['secureurl', 'https://domain.com']
    ];
  }

  /**
   * @dataProvider gettersDomainWithoutUriProvider
   */
  public function testGettersDomainWithoutUri($sName, $sExpected)
  {
    $oDomain = \Limbonia\Domain::factory('domain.com');
    $this->assertEquals($sExpected, $oDomain->__get($sName));
  }

  public function gettersDomainWithUriProvider()
  {
    return
    [
      ['protocol', 'https'],
      ['name', 'domain.com'],
      ['path', __DIR__ . '/testdomains/domain.com/www/html'],
      ['uri', '/test/uri'],
      ['url', '//domain.com/test/uri'],
      ['currenturl', 'https://domain.com/test/uri'],
      ['secureurl', 'https://domain.com/test/uri']
    ];
  }

  /**
   * @dataProvider gettersDomainWithUriProvider
   */
  public function testGettersDomainWithUri($sName, $sExpected)
  {
    $oDomain = new \Limbonia\Domain('domain.com/test/uri');
    $this->assertEquals($sExpected, $oDomain->__get($sName));
  }

  public function testToString()
  {
    $oDomain = new \Limbonia\Domain('domain.com/test/uri');
    $this->assertEquals('domain.com', $oDomain->__toString());
  }
}