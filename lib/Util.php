<?php
namespace Limbonia;

/**
 * Limbonia Util Class
 *
 * @author Lonnie Blansett <lonnie@limbonia.tech>
 * @package Limbonia
 */
class Util
{
  /**
   * The config that comes from external sources
   *
   * @var array
   */
  protected static $hAutoConfig = [];

  /**
   * The format for timestamps
   *
   * @var string
   */
  protected static $sTimeStampFormat = "G:i:s M j Y";

  /**
   * List of configured directories
   *
   * @var array
   */
  protected $hDirectories =
  [
    'root' => '',
    'libs' => []
  ];

  /**
   * List of configuration data
   *
   * @var array
   */
  protected $hConfig = [];

  /**
   * Is the CLI running?
   *
   * @return boolean
   */
  public static function isCLI()
  {
    return preg_match("/cli/i", PHP_SAPI);
  }

  /**
   * Is this running from the web?
   *
   * @return boolean
   */
  public static function isWeb()
  {
    return !self::isCLI();
  }

  /**
   * Return the correct EOL for the current environment.
   *
   * @return string
   */
  public static function eol()
  {
    return self::isCLI() ? "\n" : "<br>\n";
  }

  /**
   * Set Util to use the specified format as the new default format for timestamps
   *
   * @param string $sNewFormat
   */
  public static function setTimeStampFormat($sNewFormat = NULL)
  {
    self::$sTimeStampFormat = empty($sNewFormat) ? 'r' : $sNewFormat;
  }

  /**
   * Format and return the specified UNIX timestamp using the default format
   *
   * @param integer $iTimeStamp
   * @param string $sFormat (optional) - Override the default format with this one, if it's is used
   * @return string
   */
  public static function formatTime($iTimeStamp, $sFormat = '')
  {
    $oTime = new \DateTime('@' . (integer)$iTimeStamp);
    $sFormat = empty($sFormat) ? self::$sTimeStampFormat : $sFormat;
    return $oTime->format($sFormat);
  }

  /**
   * Generate and return the current time in the default format
   *
   * @param string $sFormat (optional) - Override the default format with this one, if it's is used
   * @return string
   */
  public static function timeStamp($sFormat = NULL)
  {
    return self::formatTime(time(), $sFormat);
  }

  /**
   * Flatten the specified variable into a string and return it...
   *
   * @param mixed $xData
   * @return string
   */
  public static function flatten($xData)
  {
    return print_r($xData, true);
  }

  /**
   * Find and return the home directory of the current user
   *
   * @return string
   */
  public static function getHomeDir()
  {
    if (isset($_SERVER['HOME']))
    {
      return $_SERVER['HOME'];
    }

    $sHome = getenv('HOME');

    if (!empty($sHome))
    {
      return $sHome;
    }

    $hUser = posix_getpwuid(posix_getuid());
    return $hUser['dir'];
  }

  /**
   * Merge two arrays recursively and return it
   *
   * @param array $hOriginal
   * @param array $hOverride
   * @return array
   */
  public static function mergeArray(array $hOriginal, array $hOverride)
  {
    $hMerge = $hOriginal;

    foreach ($hOverride as $sKey => $xValue)
    {
      if (isset($hOriginal[$sKey]))
      {
        if (is_array($xValue) && is_array($hOriginal[$sKey]))
        {
          $hMerge[$sKey] = self::mergeArray($hOriginal[$sKey], $xValue);
        }
        else
        {
          $hMerge[$sKey] = $hOverride[$sKey];
        }
      }
      else
      {
        $hMerge[$sKey] = $xValue;
      }
    }

    return $hMerge;
  }

  /**
   * Add a new hash to the default config
   *
   * @param array $hNewConfig
   */
  public static function addAutoConfig(array $hNewConfig = [])
  {
    self::$hAutoConfig = self::mergeArray(self::$hAutoConfig, $hNewConfig);
  }

  /**
   * Generate and return a valid config array
   *
   * @param array $hConfig
   * @return array
   */
  public static function getConfig(array $hConfig = [])
  {
    if (is_file('/etc/limbonia/config.php'))
    {
      require_once '/etc/limbonia/config.php';
    }

    $sHome = self::getHomeDir();
    $sConfigFile = "$sHome/.limbonia/config.php";

    if (is_file($sConfigFile))
    {
      require_once $sConfigFile;
    }

    $hConfig = self::mergeArray(self::$hAutoConfig, $hConfig);
    return \array_change_key_case($hConfig, CASE_LOWER);
  }
}