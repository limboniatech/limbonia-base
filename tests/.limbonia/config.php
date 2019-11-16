<?php
\Limbonia\Util::addAutoConfig
([
  'debug' => true,
  'master' => ['Password' => 'NewPass'],
  'directories' =>
  [
    'cache' => '../cache'
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
]);