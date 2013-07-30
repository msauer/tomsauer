<?

######################################################################
# phpRS View 1.4.8
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

/*
  Tento script slouzi ke kompletnimu zobrazeni clanku, ktery je specifikovan pomoci promenne $cisloclanku
*/

// vyuzivane tabulky:

define('IN_CODE',true); // inic. ochranne konstanty

include_once("config.php");
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

// znamkovani/hodnoceni clanku
function Znamkuj($id_clanek = "",$znamka = 0)
{
// bezpecnostni korekce
$id_clanek=mysql_escape_string($id_clanek);
$znamka=mysql_escape_string($znamka);
// inic.
$hlasuj=1; // true

if (isset($_COOKIE["znamkovani"])):
  // kdyz kontrolni cookie existuje
  $vstup=base64_decode($_COOKIE["znamkovani"]);
  $zakazna=explode(":",$vstup);
  $pocet_zak=count($zakazna);
  for ($pom=0;$pom<$pocet_zak;$pom++):
    if ($zakazna[$pom]==$id_clanek):
      $hlasuj=0; // false
      break;
    endif;
  endfor;
  if ($hlasuj==1):
    $str_cookie=base64_encode($vstup.":".$id_clanek);
    setcookie("znamkovani",$str_cookie,time()+315360000); // odeslani cookie
  endif;
else:
  // kdyz kontrolni cookie neexistuje
  $str_cookie=base64_encode($id_clanek);
  setcookie("znamkovani",$str_cookie,time()+315360000); // odeslani cookie
endif;

if ($hlasuj==1):
  if ($znamka>0&&$znamka<6): // test na plastnost znamky: 1 - 5
    @mysql_query("update ".$GLOBALS["rspredpona"]."clanky set hodnoceni=hodnoceni+".$znamka.", mn_hodnoceni=mn_hodnoceni+1 where link='".$id_clanek."'",$GLOBALS["dbspojeni"]);
  endif;
endif;
}

include_once("trclanek.php");

$clanek = new CClanek();
$error=$clanek->NactiClanek($GLOBALS["cisloclanku"]);

if ($error==1):
  if (TestNaOpakujiciIP('cla'.$GLOBALS["cisloclanku"],$GLOBALS['rsconfig']['cla_delka_omezeni'],$GLOBALS['rsconfig']['cla_max_pocet_opak'])==0):
    // navyseni pocitadla pristupu u zobrazeneho clanku
    mysql_query("update ".$GLOBALS["rspredpona"]."clanky set visit=(visit+1) where link='".$GLOBALS["cisloclanku"]."'",$GLOBALS["dbspojeni"]);
  endif;

  // hodnoceni clanku
  if (isset($GLOBALS["hlasovani"])):
    Znamkuj($GLOBALS["cisloclanku"],$GLOBALS["hlasovani"]);
  endif;

  if ($clanek->Ukaz("sablona")==""):
    // chybova hlaska: Chyba při zobrazování článku číslo XXXX! Systém nemůže nalézt odpovídající šablonu!
    echo "<p align=\"center\" class=\"z\">".RS_IN_ERR1_1." ".$GLOBALS["cisloclanku"]."! ".RS_IN_ERR1_2."<p>\n";
  else:
    $vzhledwebu->UlozPro("title",$clanek->Ukaz("titulek"));
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