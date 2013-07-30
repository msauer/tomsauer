<?

######################################################################
# phpRS HomePage 1.6.1
######################################################################

// Copyright (c) 2001-2004 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_config

// Spustit stopky, nastavit hlídání èasu
$fmtime = microtime();
$fmtime = explode(" ",$fmtime);
$fmtime = $fmtime[1] + $fmtime[0];
$starttime = $fmtime;
// Pøedat øízení chodu souboru index.php
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
    echo "<p align=\"center\" class=\"z\">Chyba pøi zobrazování èlánku èíslo ".$GLOBALS["clanek"]->Ukaz("link")."! Systém nemùže nalézt odpovídající šablonu!<p>\n";
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
    echo "<a href=\"?strana=1\">".RS_STIDX."</a>";
    // akt. rozmezi
    echo " | ".($odclanku+1)."-".($odclanku+$GLOBALS["pocetclanku"]);
    // predchozi
    if ($GLOBALS["strana"]>1):
      echo " | <a href=\"?strana=".($GLOBALS["strana"]-1)."\">".RS_STPRE."</a>";
    endif;
    // nasledujici
    if ($GLOBALS["strana"]<$pocet_str):
      echo " | <a href=\"?strana=".($GLOBALS["strana"]+1)."\">".RS_STNAS."</a>";
    endif;
    // celkovy pocet
    echo " | ".RS_STCEL1." ".$celkem_cla." ".RS_STCEL2;
    echo "</div>\n";
    echo "<br />\n";
  endif;
endif;

}

if (!isset($GLOBALS["akce"])):

  // zjisteni povoleneho poctu clanku pro index.php stranku
  $dotazmnozstvi=mysql_query("select hodnota from ".$GLOBALS["rspredpona"]."config where promenna='pocet_clanku'",$GLOBALS["dbspojeni"]);
  $pocetclanku=mysql_Result($dotazmnozstvi,0,"hodnota");
  // zjisteni nastaveni funkce strankovani hl. stranky
  $dotazstrank=mysql_query("select hodnota from ".$GLOBALS["rspredpona"]."config where promenna='povolit_str'",$GLOBALS["dbspojeni"]);
  $strankovani=mysql_Result($dotazstrank,0,"hodnota");
  // overeni stavu sluzby na hlidani platnosti clanku
  $dotazplatnost=mysql_query("select hodnota from ".$GLOBALS["rspredpona"]."config where promenna='hlidat_platnost'",$GLOBALS["dbspojeni"]);
  $hlidatplatnost=mysql_Result($dotazplatnost,0,"hodnota");

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
// Zistit, vyhodnotit a hodit na sklo namìøené hodnoty doby zpracování stránky, zastavit stopky
$fmtime = microtime();
$fmtime = explode(" ",$fmtime);
$fmtime = $fmtime[1] + $fmtime[0];
$endtime = $fmtime;
$totaltime = ($endtime - $starttime);
echo "<BR>\n";
printf("<center><font size=1 face=verdana>Stránka byla naètena a sestavena
za: <b>%f</b> sekundy</font></center>", $totaltime);
?>