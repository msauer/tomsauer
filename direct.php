<?
######################################################################
# phpRS Direct 1.1.0
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_klik_ban

/*
Tento soubor je soucasti interniho reklamniho systemu a zarucuje presmerovani odkazu na cilovou adresu.
*/

define('IN_CODE',true); // inic. ochranne konstanty

include_once('config.php');
include_once('myweb.php');
include_once('sl.php');

// smerovac
function Ukazatel($url = '')
{
header('Location: '.$url);
}

// test na existenci aliasu
if (!empty($GLOBALS["kam"])):
  // bezpecnostni korekce
  $GLOBALS["kam"]=mysql_escape_string($GLOBALS["kam"]);
  // dotaz na cil
  $dotazdirect=mysql_query("select cil from ".$GLOBALS["rspredpona"]."klik_ban where idb='".$GLOBALS["kam"]."'",$GLOBALS["dbspojeni"]);
  if ($dotazdirect!=0): // dotaz uspesne proveden
    // navyseni pocitadla
    mysql_query("update ".$GLOBALS["rspredpona"]."klik_ban set pocitadlo=(pocitadlo+1) where idb='".$GLOBALS["kam"]."'",$GLOBALS["dbspojeni"]);
    // test na vysled. mnozstvi
    if (mysql_num_rows($dotazdirect)==1):
      // volani smerovace
      Ukazatel(mysql_Result($dotazdirect,0,"cil"));
      exit();
    else:
      // CHYBA: Pozor chyba! Volaný odkaz neexistuje!
      echo "<div align=\"center\">".RS_DI_ERR2."</div>\n";
    endif;
  else:
    // CHYBA: Pozor chyba! Systém nemůže nalézt potřebná zdrojová data!
    echo "<div align=\"center\">".RS_DI_ERR3."</div>\n";
  endif;
else:
  // CHYBA: Pozor chyba! Systém nemůže identifikovat cílovou oblast!
  echo "<div align=\"center\">".RS_DI_ERR1."</div>\n";
endif;
?>