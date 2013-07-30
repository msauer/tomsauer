<?
header( 'Location: http://www.cyklosauer.cz/shop/index.php' ) ;

/*
######################################################################
# phpRS HomePage 1.6.2
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: *

define('IN_CODE',true); // inic. ochranne konstanty

include_once("config.php");
include_once("specfce.php");
include_once("myweb.php");
include_once("sl.php");
include_once("trlayout.php");
include_once($adrlayoutu);
include_once("engine.php");

// zobrazeni hlavniho bloku
function HlavniBlok()
{
// $pocetclanku - pocet cl. zobrazenych na hl. strane
// $strankovani - povoluje/zakazuje moznost strankovani hl. stranky
// $hlidatplatnost - umoznuje casove omezit dobu zobrazovani cl. na hl. strane

// zpracovani strankovani
$odclanku=0;
if ($GLOBALS["strankovani"]==1):
  // vypocet startovni pozice
  if (isset($GLOBALS["strana"])):
    $odclanku=($GLOBALS["strana"]-1)*$GLOBALS["pocetclanku"];
  else:
    $GLOBALS["strana"]=1;
  endif;
endif;

// nacteni tridy clanky
include_once("trclanek.php");

$GLOBALS["clanek"] = new CClanek();
$GLOBALS["clanek"]->HlidatPlatnost($GLOBALS["hlidatplatnost"]);
$GLOBALS["clanek"]->NactiClanky($GLOBALS["pocetclanku"],$odclanku);

for ($pom=0;$pom<$GLOBALS["clanek"]->Ukaz("pocetclanku");$pom++):
  // urceni pozadovane varianty sablony
  if ($GLOBALS["clanek"]->Ukaz("typ_clanku")==2): // 1 - standardni, 2 - kratky
    $rs_typ_clanku="kratky"; // urceni pozadovane varianty sablony
  else:
    $rs_typ_clanku="nahled"; // urceni pozadovane varianty sablony
  endif;
  // volani sablony
  if ($GLOBALS["clanek"]->Ukaz("sablona")==""):
    // chybova hlaska: Chyba při zobrazování článku číslo xxxx! Systém nemůže nalézt odpovídající šablonu!
    echo "<p align=\"center\" class=\"z\">".RS_IN_ERR1_1." ".$GLOBALS["clanek"]->Ukaz("link")."! ".RS_IN_ERR1_2."<p>\n";
  else:
    include($GLOBALS["clanek"]->Ukaz("sablona")); // vlozeni sablony; pozor, musi byt povoleno vice-nasobne vlozeni sablony
  endif;
  $GLOBALS["clanek"]->DalsiRadek(); // prechod na dalsi radek
endfor;

// navigacniho menu
if ($GLOBALS["strankovani"]==1):
  // vypocet mnozstvi rotaci
  $celkem_cla=$GLOBALS["clanek"]->CelkemClanku();
  if ($GLOBALS["pocetclanku"]>0):
    $pocet_str=ceil($celkem_cla/$GLOBALS["pocetclanku"]);
  else:
    $pocet_str=ceil($celkem_cla/10); // defaultni mnozstvi clanku na str. 10
  endif;
  // sestaveni listy
  if ($pocet_str>1):
    echo "<div align=\"right\" class=\"strankovani\">\n";
    // index
    echo "<a href=\"?strana=1\">".RS_IN_IDX."</a>";
    // akt. rozmezi
    echo " | ".($odclanku+1)."-".($odclanku+$GLOBALS["pocetclanku"]);
    // predchozi
    if ($GLOBALS["strana"]>1):
      echo " | <a href=\"?strana=".($GLOBALS["strana"]-1)."\">".RS_IN_PRED."</a>";
    endif;
    // nasledujici
    if ($GLOBALS["strana"]<$pocet_str):
      echo " | <a href=\"?strana=".($GLOBALS["strana"]+1)."\">".RS_IN_NASL."</a>";
    endif;
    // celkovy pocet
    echo " | ".RS_IN_CELKEM_1." ".$celkem_cla." ".RS_IN_CELKEM_2;
    echo "</div>\n";
    echo "<br />\n";
  endif;
endif;

}

if (!isset($GLOBALS["akce"])):

  // zjisteni povoleneho poctu clanku pro index.php stranku
  $pocetclanku=NactiConfigProm('pocet_clanku',0);
  // zjisteni nastaveni funkce strankovani hl. stranky
  $strankovani=NactiConfigProm('povolit_str',0);
  // overeni stavu sluzby na hlidani platnosti clanku
  $hlidatplatnost=NactiConfigProm('hlidat_platnost',0);

  $vzhledwebu->hlavnistranka=1; // aktivuje nastaveni hlavni stranka

  // standardni zobrazeni
  $vzhledwebu->Generuj();
  HlavniBlok();
  $vzhledwebu->Generuj();

else:

  $vzhledwebu->Generuj();
  ObrTabulka();  // Vlozeni layout prvku
    // volba akce
    switch($GLOBALS["akce"]):
      case "verze": Verze(); break;
      case "temata": ShowTopics(); break;
      case "linky": ShowLinks(); break;
      case "statistika": ShowStatistics(); break;
    endswitch;
  KonecObrTabulka();   // Vlozeni layout prvku
  $vzhledwebu->Generuj();

endif;
 *
 */
?>