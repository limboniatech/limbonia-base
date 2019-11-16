<?php
use PHPUnit\Framework\TestCase;

class UtilTest extends TestCase
{
  public function testIsCli()
  {
    $this->assertEquals(preg_match("/cli/i", PHP_SAPI), \Limbonia\Util::isCLI());
  }

  public function testIsWeb()
  {
    $this->assertEquals(!preg_match("/cli/i", PHP_SAPI), \Limbonia\Util::isWeb());
  }

  public function testEol()
  {
    $sExcpected = \Limbonia\Util::isCLI() ? "\n" : "<br>\n";
    $this->assertEquals($sExcpected, \Limbonia\Util::eol());
  }

  public function testFormatTime()
  {
    $sTimeStr = '15:38:03 Nov 15 2019';
    $this->assertEquals($sTimeStr, \Limbonia\Util::formatTime(strtotime($sTimeStr)));
  }

  public function testFormatTimeWithFormat()
  {
    $sTimeStr = '15:38:03 Nov 15 2019';
    $this->assertEquals('Fri, 15 Nov 2019 15:38:03 +0000', \Limbonia\Util::formatTime(strtotime($sTimeStr), 'r'));
  }

  public function testSetTimeStampFormatWithNotFormat()
  {
    \Limbonia\Util::setTimeStampFormat();
    $oReflectedUtil = new \ReflectionClass(\Limbonia\Util::class);
    $hStaticProperites = $oReflectedUtil->getStaticProperties();

    $this->assertEquals('r', $hStaticProperites['sTimeStampFormat']);
  }

  public function testSetTimeStampFormatWithFormat()
  {
    $sFormat = 'G:i:s M j Y';
    \Limbonia\Util::setTimeStampFormat($sFormat);
    $oReflectedUtil = new \ReflectionClass(\Limbonia\Util::class);
    $hStaticProperites = $oReflectedUtil->getStaticProperties();

    $this->assertEquals($sFormat, $hStaticProperites['sTimeStampFormat']);
  }

  public function testTimeStampWithoutFormat()
  {
    $sTimeStamp = \Limbonia\Util::timeStamp();
    $iTimeStamp = strtotime($sTimeStamp);

    $this->assertEquals($sTimeStamp, \Limbonia\Util::formatTime($iTimeStamp));
  }

  public function testTimeStampWithFormat()
  {
    $sFormat = 'r';
    $sTimeStamp = \Limbonia\Util::timeStamp($sFormat);
    $iTimeStamp = strtotime($sTimeStamp);

    $this->assertEquals($sTimeStamp, \Limbonia\Util::formatTime($iTimeStamp, $sFormat));
  }

  public function testFlatten()
  {
    $hTest = ['a' => 1, 'b' => 'c', 'd' => ['e', 'f', 'g']];
    $this->assertEquals(print_r($hTest, true), \Limbonia\Util::flatten($hTest));
  }

  public function testGetHomeDirFromServer()
  {
    $sHome = '/test/home/from/server';
    
    if (!isset($_SERVER))
    {
      $_SERVER = [];
    }

    $_SERVER['HOME'] = $sHome;

    $this->assertEquals($sHome, \Limbonia\Util::getHomeDir());
  }
  
  public function testGetHomeDirFromEnv()
  {
    $sHome = '/test/home/from/env';
    unset($_SERVER['HOME']);
    putenv("HOME=$sHome");

    $this->assertEquals($sHome, \Limbonia\Util::getHomeDir());
  }
  
  public function testGetHomeDirFromPosix()
  {
    putenv("HOME");

    $this->assertIsString(\Limbonia\Util::getHomeDir());
  }
  
  public function testMergeArray()
  {
    $hOriginal =
    [
      'test1' => 'something',
      'test2' =>
      [
        'foo' => 'bar',
        'stuff' => 'thing'
      ],
      'test3' =>
      [
        'foo' => 'bar',
        'stuff' => 'thing'
      ],
      'test4' =>
      [
        'foo' => 'bar',
        'stuff' => 'thing'
      ],
      'test5' =>
      [
        'hello' =>
        [
          'this' => 'is',
          'an' => 'array'
        ]
        ],
      'test6' =>
      [
        'hello' =>
        [
          'this' => 'is',
          'an' => 'array'
        ]
      ]
    ];
    $hOverride =
    [
      'test1' => 'blah',
      'test2' =>
      [
        'foo' => 'baz',
      ],
      'test3' =>
      [
        'foo' =>
        [
          'totally' => 'different'
        ]
      ],
      'test4' =>
      [
        'other' =>
        [
          'different' => 'stuff'
        ]
      ],
      'test5' =>
      [
        'hello' =>
        [
          'this' => "isn't"
        ]
      ],
      'test6' =>
      [
        'hello' =>
        [
          'some' => 'test'
        ]
      ],
      'test7' =>
      [
        'bye' =>
        [
          'the' => "end"
        ]
      ]
    ];
    $hExpected =
    [
      'test1' => 'blah',
      'test2' =>
      [
        'foo' => 'baz',
        'stuff' => 'thing'
      ],
      'test3' =>
      [
        'foo' =>
        [
          'totally' => 'different'
        ],
        'stuff' => 'thing'
      ],
      'test4' =>
      [
        'foo' => 'bar',
        'stuff' => 'thing',
        'other' =>
        [
          'different' => 'stuff'
        ]
      ],
      'test5' =>
      [
        'hello' =>
        [
          'this' => "isn't",
          'an' => 'array'
        ]
      ],
      'test6' =>
      [
        'hello' =>
        [
          'this' => 'is',
          'an' => 'array',
          'some' => 'test'
        ]
      ],
      'test7' =>
      [
        'bye' =>
        [
          'the' => 'end'
        ]
      ]
    ];

    $this->assertEquals($hExpected, \Limbonia\Util::mergeArray($hOriginal, $hOverride));
  }

  public function testAddAutoConfig()
  {
    $hConfig =
    [
      'debug' => false,
      'master' =>
      [
        'User' => 'Master',
        'Password' => 'Test1234'
      ],
      'directories' =>
      [
        'share' => 'share/limbonia',
        'templates' => '../templates'
      ]
      ];
    \Limbonia\Util::addAutoConfig($hConfig);
    $oReflectedUtil = new \ReflectionClass(\Limbonia\Util::class);
    $hStaticProperites = $oReflectedUtil->getStaticProperties();

    $this->assertEquals($hConfig, $hStaticProperites['hAutoConfig']);
  }

  public function testGetConfig()
  {
    if (!isset($_SERVER))
    {
      $_SERVER = [];
    }

    $_SERVER['HOME'] = __DIR__;
    $hExpected =
    [
      'debug' => 1,
      'master' =>
      [
        'User' => 'Master',
        'Password' => 'NewPass'
      ],
      'directories' =>
      [
        'share' => 'share/limbonia',
        'templates' => '../templates',
        'cache' => '../cache',
      ],
      'database' =>
      [
        'default' =>
        [
          'driver' => 'mysql',
          'host' => 'localhost',
          'database' => 'limbonia',
          'user' => 'test',
          'password' => 'test'
        ]
      ]
    ];
    $this->assertEquals($hExpected, \Limbonia\Util::getConfig());
  }
}