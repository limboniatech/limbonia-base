<?php

namespace Limbonia\Test;

class Hash
{
  /**
   * Inherit the Hash trait
   */
  use \Limbonia\Traits\Hash;

  public function __construct(array $hData = [])
  {
    foreach ($hData as $xKey => $xData)
    {
      $this->hData[\strtolower($xKey)] = $xData;
    }
  }
}