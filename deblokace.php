<?

######################################################################
# phpRS Deblokace 1.0.0
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_user

/*
  ukazkovy link: deblokace.php?blok_user=admin&blok_string=7249755721b0d5e3f1ded2e43ea85bcc
*/

define('IN_CODE',true); // inic. ochranne konstanty

include_once("config.php");
include_once("specfce.php");
include_once("myweb.php");
include_once("sl.php");
include_once("trlayout.php");
include_once($adrlayoutu);

// tvorba stranky
$vzhledwebu->Generuj();
ObrTabulka();  // Vlozeni layout prvku

// test na uplnost vstupu
if (isset($GLOBALS['blok_user'])&&isset($GLOBALS['blok_string'])):
  // bezpecnostni kontrola
  $GLOBALS['blok_user']=mysql_escape_string($GLOBALS['blok_user']);
  $GLOBALS['blok_string']=mysql_escape_string($GLOBALS['blok_string']);
  // dotaz
  $dotazucet=mysql_query("select idu from ".$GLOBALS["rspredpona"]."user where user='".$GLOBALS['blok_user']."' and pom_str='".$GLOBALS['blok_string']."'",$GLOBALS["dbspojeni"]);
  if ($dotazucet!=0):
    if (mysql_num_rows($dotazucet)==1):
      $akt_pole_data=mysql_fetch_assoc($dotazucet);
      mysql_query("update ".$GLOBALS["rspredpona"]."user set blokovat=0, pocet_chyb=0, pom_str='' where idu='".$akt_pole_data['idu']."' and user='".$GLOBALS['blok_user']."'",$GLOBALS["dbspojeni"]);
      // vse ok - Váš účet byl úspěšně odblokován!
      echo "<p align=\"center\" class=\"z\">".RS_DE_VSE_OK."</p>\n";
    else:
      // CHYBA: Systém nemůže nalézt požadovaného uživatele!
      echo "<p align=\"center\" class=\"z\">".RS_DE_ERR2."</p>\n";
    endif;
  else:
    // CHYBA: Systém nemůže nalézt požadovaného uživatele!
    echo "<p align=\"center\" class=\"z\">".RS_DE_ERR2."</p>\n";
  endif;
else:
  // CHYBA: Systém nemůže nalézt veškeré nezbytné vstupní proměnné!
  echo "<p align=\"center\" class=\"z\">".RS_DE_ERR1."</p>\n";
endif;

// dokonceni tvorby stranky
KonecObrTabulka();  // Vlozeni layout prvku
$vzhledwebu->Generuj();
?>