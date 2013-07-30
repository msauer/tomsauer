<?
#####################################################################
# phpRS Slovnikovy manager - version 1.0.5
#####################################################################

// Copyright (c) 2001-2003 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.
// 
// include("sl.php");

if ($GLOBALS["prmyctenar"]->ctenarstav==1):
  if ($GLOBALS["prmyctenar"]->Ukaz("jazyk")!=""):
    $vybranyjazyk=$GLOBALS["prmyctenar"]->Ukaz("jazyk");
  else:
    $vybranyjazyk="cz";
  endif;
else:
  $vybranyjazyk="cz";
endif;

$jazykadresa="lang/sl_".$vybranyjazyk.".php";
include($jazykadresa);
?>
