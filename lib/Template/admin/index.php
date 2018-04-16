<?php
$sAdminNav = "No modules were found!\n";
$sModuleNav = '';
$iGroup = count($_SESSION['ModuleGroups']);
$sPageTitle = 'Limbonia Admin';

if ($iGroup > 0)
{
  $sAdminNav = '';
  $iMinGroups = array_key_exists('Hidden', $_SESSION['ModuleGroups']) ? 2 : 1;

  foreach ($_SESSION['ModuleGroups'] as $sGroup => $hModuleList)
  {
    if ($iGroup > $iMinGroups && $sGroup !== 'Hidden')
    {
      $sAdminNav .= "      <div class=\"group\">$sGroup</div>\n";
    }

    foreach ($hModuleList as $sLabel => $sModuleName)
    {
      $sLowerModule = strtolower($sModuleName);
      $oModule = $controller->moduleFactory($sModuleName);

      if ($sGroup !== 'Hidden')
      {
        $sAdminNav .= "      <div class=\"module $sLowerModule\" style=\"display: none\">\n";
        $sAdminNav .= "        <div class=\"title\">" . preg_replace("/([A-Z])/", " $1", $oModule->getType()) . "</div>\n";

        $hQuickSearch = $oModule->getQuickSearch();

        if (!empty($hQuickSearch) && $oModule->allow('search'))
        {
          $sModuleType = $oModule->getType();

          foreach ($hQuickSearch as $sColumn => $sTitle)
          {
            $sAdminNav .= "        <form name=\"QuickSearch\" action=\"" . $oModule->generateUri('search', 'quick') . "\" method=\"post\">$sTitle:<input type=\"text\" name=\"{$sModuleType}[{$sColumn}]\" id=\"{$sModuleType}{$sColumn}\"></form>\n";
          }
        }

        $sAdminNav .= "      </div>\n";
        $sAdminNav .= "      <a class=\"$sLowerModule\" href=\"" . $controller->generateUri($sLabel) . "\">" . preg_replace("/([A-Z])/", " $1", $sModuleName) . "</a>\n";
      }

      foreach ($oModule->getMenuItems() as $sMenuAction => $sMenuTitle)
      {
        if (!$oModule->allow($sMenuAction))
        {
          continue;
        }

        if ($sMenuAction !== 'item')
        {
          $sCurrent = isset($method) && $method == $sMenuAction ? 'current ' : '';
          $sDisplay = isset($module) && $oModule->getType() == $module->getType() ? '' : ' style="display: none"';
          $sModuleNav .= "        <a class=\"item {$sCurrent}tab $sLowerModule $sMenuAction\"$sDisplay href=\"" . $oModule->generateUri($sMenuAction) . "\">$sMenuTitle</a>\n";
        }
      }
    }
  }
}

if (isset($moduleOutput))
{
  $aAdminOutput = $module->getAdminOutput();

  if (!empty($aAdminOutput))
  {
    $moduleOutput = "<script type=\"text/javascript\">
   updateAdminNav('" . $module->getType() . "');
   buildItem(" . json_encode($aAdminOutput) . ");
   $('#item > #page').html(" . json_encode($moduleOutput) . ");
</script>\n";
  }
  else
  {
    $sPageTitle = ucwords($module->getType() . " > $method");
    $moduleOutput .= "<script type=\"text/javascript\">updateAdminNav('" . $module->getType() . "');</script>\n";
  }
}
else
{
  $moduleOutput = '';
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="Lonnie Blansett">
  <meta name="generator" content="Limbonia <?= \Limbonia\Controller::version() ?>">
  <meta name="build-date" content="<?= \Limbonia\Controller::buildDate() ?>">
  <title><?= $sPageTitle ?></title>
  <link rel="stylesheet" type="text/css" href="<?= $controller->domain->uri . '/' . $controller->getDir('share') ?>/admin.css" />
  <script type="text/javascript" src="<?= $controller->domain->uri . '/' . $controller->getDir('share') ?>/admin-all-min.js"></script>
  <script type="text/javascript">
  $(function()
  {
    var slideout = new Slideout
    ({
      'panel': document.getElementById('content'),
      'menu': document.getElementById('menu'),
      'padding': 1,
      'tolerance': 70
    });
    $('.hamburger').on('click', function()
    {
      slideout.toggle();
    });
  });
  </script>
</head>
<body>
  <header><span class="hamburger">☰</span><span>User: <?= $controller->oUser->name ?></span><span class="tools"><a class="item" href="<?= $controller->generateUri('profile') ?>">Profile</a> | <a href="<?= $controller->generateUri('logout') ?>" target="_top">Logout</a></span></header>
  <section id="admin">
    <nav class="moduleList" id="menu">
<?= $sAdminNav ?>
    </nav>
    <section id="content">
      <nav class="tabSet">
<?= $sModuleNav ?>
      </nav>
      <div id="moduleOutput">
<?= $moduleOutput ?>
      </div>
    </section>
  </section>
</body>
</html>