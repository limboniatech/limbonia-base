<?php
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
  public function testMakeDirDirAlreadyExists()
  {
    $this->assertTrue(\Limbonia\File::makeDir(__DIR__ . '/lib'));
  }

  public function testMakeDirNewDir()
  {
    $sNewDir = __DIR__ . '/lib/testdir/foo/bar';
    $this->assertTrue(\Limbonia\File::makeDir($sNewDir));
    $this->assertTrue(is_dir($sNewDir));
  }

  public function testRemoveDirDirAlreadyGone()
  {
    $this->assertTrue(\Limbonia\File::removeDir(__DIR__ . '/lib/gone'));
  }

  public function testRemoveDirValidDir()
  {
    $sNewDir = __DIR__ . '/lib/testdir/foo/bar';
    $this->assertTrue(\Limbonia\File::removeDir($sNewDir));
    $this->assertFalse(is_dir($sNewDir));
  }

  public function testOpenFileWithInvalidFile()
  {
    $this->expectException(\Limbonia\Exception::class);
    \Limbonia\File::openFile('invalid_dir/file.txt');
  }

  public function testOpenFileWithExistingFile()
  {
    $rFile = \Limbonia\File::openFile(__DIR__ . '/file.txt');
    $this->assertTrue(is_resource($rFile));

    //make sure we close this file after the test...
    fclose($rFile);
  }

  public function testOpenFileWithNewFile()
  {
    $sFile = __DIR__ . '/newfile.txt';
    $rFile = \Limbonia\File::openFile($sFile);
    $this->assertTrue(is_resource($rFile));

    //make sure we close this file after the test...
    fclose($rFile);
    unlink($sFile);
  }

  public function testCloseFile()
  {
    $rFile = \Limbonia\File::openFile(__DIR__ . '/file.txt');
    $this->assertTrue(\Limbonia\File::closeFile($rFile));
    $this->assertFalse(is_resource($rFile));
  }

  public function testLockWithInvalidFile()
  {
    $this->expectException(\Limbonia\Exception::class);
    \Limbonia\File::lock('invalid_dir/file.txt');
  }

  public function testLockWithExistingFile()
  {
    $sFile = __DIR__ . '/file.txt';
    $rLockedFile = \Limbonia\File::lock($sFile);
    $this->assertTrue(is_resource($rLockedFile));

    //if locking works then trying to lock the same
    //file again should throw an exception
    $this->expectException(\Limbonia\Exception::class);
    $rFileAgain = \Limbonia\File::lock($sFile);

    //make sure we close this file after the test...
    \Limbonia\File::closeFile($rLockedFile);
  }

  public function testUnlock()
  {
    $sFile = __DIR__ . '/file.txt';
    $rLockedFile = \Limbonia\File::lock($sFile);
    $this->assertTrue(\Limbonia\File::unlock($rLockedFile));

    //if it is still locked then locking it again will throw an exception
    $rLockedFile = \Limbonia\File::lock($sFile);

    //if not then just unlock it again and we're done...
    \Limbonia\File::unlock($rLockedFile);
  }
}