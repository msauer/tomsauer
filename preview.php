<?

######################################################################
# phpRS Preview 1.4.4
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

/*
  Tento script slouzi ke kompletnimu zobrazeni preview clanku, ktery je specifikovan pomoci promenne $cisloclanku
*/

// vyuzivane tabulky:

define('IN_CODE',true); // inic. ochranne konstanty

include_once("config.php");
include_once("autor.php"); // autorizace pristupu
include_once("specfce.php");
include_once("myweb.php");
include_once("sl.php");
include_once("trlayout.php");
include_once($adrlayoutu);

// test na pritomnost promenne $cisloclanku
if (!isset($GLOBALS["cisloclanku"])):
  echo "<html><body><p align=\"center\" class=\"z\">".RS_VW_ERR1."<p></body></html>\n";
  exit();
else:
  $GLOBALS["cisloclanku"]=mysql_escape_string($GLOBALS["cisloclanku"]);
endif;

include("trclanek.php");

$clanek = new CClanek();
$clanek->HlidatAktDatum(0); // vypnuti hlidani data vydani clanku
$clanek->NastavVydani(0); // vypnuti hlidani prepinace - clanek vydan
$error=$clanek->NactiClanek($GLOBALS["cisloclanku"]);

if ($error==1):
  if ($clanek->Ukaz("sablona")==""):
    // chybova hlaska: Chyba při zobrazování článku číslo XXXX! Systém nemůže nalézt odpovídající šablonu!
    echo "<p align=\"center\" class=\"z\">".RS_IN_ERR1_1." ".$GLOBALS["cisloclanku"]."! ".RS_IN_ERR1_2."<p>\n";
  else:
    // tvorba stranky
    $vzhledwebu->Generuj();
    $rs_typ_clanku="cely"; // urceni pozadovane varianty sablony
    include_once($clanek->Ukaz("sablona")); // vlozeni sablony
    $vzhledwebu->Generuj();
  endif;
else:
  // chybova hlaska: Chyba! Článek číslo XXXX neexistuje!
  echo "<p align=\"center\" class=\"z\">".RS_VW_ERR2_1." ".$GLOBALS["cisloclanku"]." ".RS_VW_ERR2_2."<p>\n";
endif;
?>