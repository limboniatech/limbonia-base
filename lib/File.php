<?php
namespace Limbonia;

/**
 * Limbonia Programming API Class
 *
 * It defines a host of needed functionality including a standard method for
 * writing that allows for web and cli environments, opening and closing files,
 * timestamps as well as the version information.
 *
 * NOTE: All properties and methods are static.
 *
 * @author Lonnie Blansett <lonnie@limbonia.tech>
 * @version $Revision: 1.3 $
 * @package Limbonia
 */
class File
{
  /**
   * Make a directory structure from the given path.
   *
   * @param string $sPath - the path of the directory to make
   * @param number $nMode
   * @throws \Limbonia\Exception on failure
   */
  static public function makeDir($sPath, $nMode = 0777)
  {
    if (is_dir($sPath))
    {
      return true;
    }

    $nOldMask = umask(0000);

    try
    {
      self::makeDir(dirname($sPath), $nMode);
    }
    catch (\Exception $e)
    {
      umask($nOldMask);
      throw $e;
    }

    $sNewDir = basename($sPath);
    $sDir = dirname($sPath);
    $sCurrentDir = getcwd();
    chdir($sDir);

    ob_start();
    $bSuccess = mkdir($sNewDir, $nMode);
    $sError = ob_get_clean();

    chdir($sCurrentDir);
    umask($nOldMask);

    if (!$bSuccess)
    {
      throw new \Limbonia\Exception("Error creating $sNewDir: $sError");
    }

    return true;
  }

  /**
   * Remove the specified directory.
   *
   * @param string $sPath
   * @return boolean
   */
  static public function removeDir($sPath)
  {
    if (!empty($sPath))
    {
      if (is_file($sPath))
      {
        unlink($sPath);
      }
      else
      {
        $sTempPath = is_dir($sPath) ? "$sPath/*" : $sPath;
        $aPath = glob($sTempPath);

        if ($aPath && $aPath[0] != $sPath)
        {
          foreach ($aPath as $sFile)
          {
            self::removeDir($sFile);
          }
        }

        if (is_dir($sPath))
        {
          rmdir($sPath);
        }
      }
    }

    return true;
  }

  /**
   * Open the specified file and return a file resource based on that file.
   *
   * @param string $sFilePath - the path to the file to open
   * @param string $sMode (optional) - the mode to open the file in
   * @throws \Limbonia\Exception
   * @return resource
   */
  static public function openFile($sFilePath, $sMode = 'a')
  {
    if (preg_match("#php://(std(in|out|err))#", $sFilePath, $aMatch) && \defined(strtoupper($aMatch[1])))
    {
      return constant(strtoupper($aMatch[1]));
    }

    set_error_handler(function($iSeverity, $sMessage, $sFile, $iLine)
    {
      if (!(error_reporting() & $iSeverity))
      {
        // This error code is not included in error_reporting
        return;
      }

      throw new \Limbonia\Exception($sMessage);
    });

    try
    {
      $rFile = fopen($sFilePath, $sMode);
    }
    finally
    {
      restore_error_handler();
    }

    if (!is_resource($rFile))
    {
      throw new \Limbonia\Exception('Failed to open file: Unknown Error');
    }

    return $rFile;
  }

  /**
   * Close the specified file resource.
   *
   * @param resource $rFilePath
   * @return boolean
   */
  static public function closeFile($rFilePath)
  {
    if (!is_resource($rFilePath))
    {
      return true;
    }

    // if this is one of the standard ones file handles don't even *try* to
    // close it, just return true and let PHP handle it when the script ends...
    if (($rFilePath === STDIN || $rFilePath === STDOUT || $rFilePath === STDERR))
    {
      return true;
    }

    return fclose($rFilePath);
  }

  /**
   * Open and lock the specified file then return a handle to the openend file
   *
   * @param string $sFilePath
   * @param string $sMode
   * @throws \Limbonia\Exception
   * @return resource - A file resource on success
   */
  static public function lock($sFilePath, $sMode = 'a')
  {
    $rFile = self::openFile($sFilePath, $sMode . 'b');

    if (flock($rFile, preg_match("/r/", $sMode) ? LOCK_SH | LOCK_NB : LOCK_EX | LOCK_NB))
    {
      return $rFile;
    }

    self::closeFile($rFile);
    throw new \Limbonia\Exception('Failed to lock file: $sFilePath');
  }

  /**
   * Unlock a file so it can be accessed freely
   *
   * @param resource $rFile - the file resource to unlock
   * @return boolean
   */
  static public function unlock($rFile)
  {
    if (!flock($rFile, LOCK_UN))
    {
      return false;
    }

    // even if closing the file fails, we'll return true
    self::closeFile($rFile);
    return true;
  }
}