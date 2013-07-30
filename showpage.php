<?

######################################################################
# phpRS ShowPage 1.1.8
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_alias

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

// preklad aliasu
if (!empty($GLOBALS["name"])):
  $GLOBALS["name"]=mysql_escape_string($GLOBALS["name"]); // bezpecnostni korekce
  $dotazpage=mysql_query("select hodnota from ".$GLOBALS["rspredpona"]."alias where alias='".$GLOBALS["name"]."' and typ='sablona'",$GLOBALS["dbspojeni"]);
  if (mysql_num_rows($dotazpage)>0):
    // zobrazeni textoveho souboru
    $prchyba=ReadFile(mysql_Result($dotazpage,0,"hodnota"));
    if ($prchyba==0):
      // CHYBA: Požadovaná stránka nenalezena!
      echo "<div align=\"center\">".RS_SW_ERR2."</div>\n";
    endif;
  else:
    // CHYBA: Systém nemůže identifikovat požadovanou stránku!
    echo "<div align=\"center\">".RS_SW_ERR1."</div>\n";
  endif;
else:
  // CHYBA: Systém nemůže identifikovat požadovanou stránku!
  echo "<div align=\"center\">".RS_SW_ERR1."</div>\n";
endif;

// dokonceni tvorby stranky
KonecObrTabulka();  // Vlozeni layout prvku
$vzhledwebu->Generuj();
?>