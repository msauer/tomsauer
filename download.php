<?

######################################################################
# phpRS Download 1.5.4
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

/*
Vstupni promenna "sekce" umoznuje omezit vypis na specifickou sekci.
Vstupni promenna "soubor" inicializuje presmerovani na odpovidajici soubor.
*/

// vyuzivane tabulky: rs_download, rs_download_sekce

define('IN_CODE',true); // inic. ochranne konstanty

include_once("config.php");

// odchyceni pozadavku na download konkretniho souboru
if (isset($GLOBALS["soubor"])):
  $GLOBALS["soubor"]=addslashes($GLOBALS["soubor"]); // bezpec. korekce
  $dotazsoubor=mysql_query("select furl,fjmeno from ".$GLOBALS["rspredpona"]."download where idd='".$GLOBALS["soubor"]."' and zobraz=1",$GLOBALS["dbspojeni"]);
  // test na uspesnost dotazu a existenci souboru
  if ($dotazsoubor!=0):
    if (mysql_num_rows($dotazsoubor)>0):
      @mysql_query("update ".$GLOBALS["rspredpona"]."download set pocitadlo=(pocitadlo+1) where idd='".$GLOBALS["soubor"]."'",$GLOBALS["dbspojeni"]); // zapocitani stazeni
      $pole_soubor=mysql_fetch_assoc($dotazsoubor);
      header("Content-Description: File Transfer");
      header("Content-Type: application/force-download");
      header("Content-Disposition: attachment; filename=\"".$pole_soubor["fjmeno"]."\"");
      header("Location: ".$pole_soubor["furl"]); // presmerovani stranky
      exit();
    endif;
  endif;
endif;

include_once("specfce.php");
include_once("myweb.php");
include_once("sl.php");
include_once("trlayout.php");
include_once($adrlayoutu);

// test na exist. sekce
if (!isset($GLOBALS["sekce"])): $GLOBALS["sekce"]=0; endif; // 0 = neexistujici sekce
// test na exist. akce
if (!isset($GLOBALS['akce'])): $GLOBALS['akce']="sekce"; endif;

// vypis menu
function VypisSekci($akt_sekce = 0)
{
$pocet_sloupcu=3; //

$dotazsek=mysql_query("select ids,nazev from ".$GLOBALS["rspredpona"]."download_sekce order by nazev",$GLOBALS["dbspojeni"]);
$pocetsek=mysql_num_rows($dotazsek);

// vypis se provadi pouze, kdyz jsou k dispozici dve a vice polozek
if ($pocetsek>1):
   // vypis tabulky
   echo "<table border=\"0\" align=\"center\">\n";
   for ($pom=0;$pom<$pocetsek;$pom++):
     $pole_data=mysql_fetch_assoc($dotazsek); // nacteni dat
     if (($pom % $pocet_sloupcu) == 0):
       echo "<tr class=\"z\">";
     else:
       echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>"; // mezera mezi sloupci
     endif;
     echo "<td align=\"center\">";
     if ($pole_data["ids"]==$akt_sekce):
       echo "<b>".$pole_data["nazev"]."</b>";
     else:
       echo "<a href=\"download.php?sekce=".$pole_data["ids"]."\">".$pole_data["nazev"]."</a>";
     endif;
     echo "</td>\n";
     if (($pom % $pocet_sloupcu) == ($pocet_sloupcu-1)):
       echo "</tr>\n";
     endif;
   endfor;
   // dokonceni tabulky
   $chybi=$pom % $pocet_sloupcu;
   if ($chybi > 0):
     for ($pom=0; $pom < ($pocet_sloupcu - $chybi); $pom++):
       echo "<td></td><td></td>";
     endfor;
     echo "</tr>\n";
   endif;
   echo "</table>\n";
endif;
}

// zobrazeni downloadu
function ShowDownload()
{
// bezpec. korekce
$GLOBALS["sekce"]=addslashes($GLOBALS["sekce"]);

$zobrazvypis=0; // vypis - ne

// urceni omezeni
if ($GLOBALS["sekce"]==0):
  // neexistuje upresneni sekce - nacteni hl. sekce
  $dotazhl=mysql_query("select ids from ".$GLOBALS["rspredpona"]."download_sekce where hlavnisekce='1'",$GLOBALS["dbspojeni"]);
  if ($dotazhl!=0):
    if (mysql_num_rows($dotazhl)>0):
      $GLOBALS["sekce"]=mysql_Result($dotazhl,0,"ids"); // id hlavni sekce
      $promezeni="idsekce='".$GLOBALS["sekce"]."' and ";
      $zobrazvypis=1; // vypis - ano
    endif;
  endif;
else:
  // existuje prom. sekce
  $promezeni="idsekce='".$GLOBALS["sekce"]."' and ";
  $zobrazvypis=1; // vypis - ano
endif;

VypisSekci($GLOBALS["sekce"]);
echo "<p></p>\n";

// dotaz na soubory
if ($zobrazvypis): // vypis = ano
  $dotazvyp=mysql_query("select idd,nazev,komentar,fjmeno,fsize,zdroj_jm,zdroj_adr,datum,verze,kat,pocitadlo from ".$GLOBALS["rspredpona"]."download where ".$promezeni."zobraz='1' order by datum desc",$GLOBALS["dbspojeni"]);
  $pocetvyp=mysql_num_rows($dotazvyp);
else:
  $pocetvyp=0; // nula znemozni zobrazeni
endif;

// prehlad souboru
if ($pocetvyp>0): // je zobrazen jen, kdyz existuje jedna a vice pol.
  echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\">
<tr class=\"z\"><td align=\"center\"><b>&nbsp;</b></td>
<td align=\"center\"><b>".RS_DW_JMENO_SB."</b></td>
<td align=\"center\"><b>".RS_DW_VEL_SB."</b></td>
<td align=\"center\"><b>".RS_DW_ZDROJ_SB."</b></td>
<td align=\"center\"><b>".RS_DW_DATUM_SB."</b></td>
<td align=\"center\"><b>".RS_DW_VER_SB."</b></td>
<td align=\"center\"><b>".RS_DW_KAT."</b></td></tr>\n";
  for ($pom=0;$pom<$pocetvyp;$pom++):
    $pole_data=mysql_fetch_assoc($dotazvyp);
    echo "<tr class=\"z\"><td align=\"center\">";
    echo "<a href=\"download.php?akce=detail&amp;id_detail=".$pole_data["idd"]."&amp;sekce=".$GLOBALS["sekce"]."\"><img src=\"image/info.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".RS_DW_KLIKNI."\" title=\"".RS_DW_KLIKNI."\" /></a>&nbsp;&nbsp;";
    echo "<a href=\"download.php?soubor=".$pole_data["idd"]."\"><img src=\"image/download.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".RS_DW_STAHNI."\" title=\"".RS_DW_STAHNI."\" /></a>";
    echo "</td>\n";
    echo "<td align=\"center\">".$pole_data["nazev"]."<br /><a href=\"download.php?soubor=".$pole_data["idd"]."\">".$pole_data["fjmeno"]."</a></td>\n";
    echo "<td align=\"center\">".$pole_data["fsize"]."</td>\n";
    if (($pole_data["zdroj_jm"]!="-")&&($pole_data["zdroj_adr"]!="-")):
      echo "<td align=\"center\"><a href=\"".$pole_data["zdroj_adr"]."\">".$pole_data["zdroj_jm"]."</a></td>\n";
    else:
      echo "<td align=\"center\">&nbsp;</td>\n";
    endif;
    echo "<td align=\"center\">".MyDatetimeToDate($pole_data["datum"])."</td>\n";
    echo "<td align=\"center\">".$pole_data["verze"]."</td>\n";
    echo "<td align=\"center\">".$pole_data["kat"]."</td></tr>\n";
  endfor;
  echo "</table>\n";
endif;

echo "<p></p>\n";
}

function ShowDetail()
{
// bezpec. korekce
$GLOBALS["sekce"]=addslashes($GLOBALS["sekce"]);
$GLOBALS["id_detail"]=addslashes($GLOBALS["id_detail"]);

echo "<p align=\"center\" class=\"z\"><a href=\"download.php?akce=sekce&amp;sekce=".$GLOBALS["sekce"]."\">".RS_DW_ZPET."</a></p>\n";

$dotazvyp=mysql_query("select idd,nazev,komentar,fjmeno,fsize,zdroj_jm,zdroj_adr,datum,verze,kat,pocitadlo from ".$GLOBALS["rspredpona"]."download where idd='".$GLOBALS["id_detail"]."' and zobraz='1'",$GLOBALS["dbspojeni"]);
$pocetvyp=mysql_num_rows($dotazvyp);

if ($pocetvyp==1):
  $pole_data=mysql_fetch_assoc($dotazvyp);
  echo "<div class=\"z\">\n";
  echo "<a href=\"download.php?soubor=".$pole_data["idd"]."\"><img src=\"image/download.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".RS_DW_STAHNI."\" title=\"".RS_DW_STAHNI."\" /></a> ";
  echo "<strong><a href=\"download.php?soubor=".$pole_data["idd"]."\">".$pole_data["nazev"]."</a></strong><br />\n";
  echo $pole_data["komentar"];
  echo "</div><br />\n";
  echo "<div class=\"z\">\n";
  echo RS_DW_VER_SB.": ".$pole_data["verze"]."<br />\n";
  echo RS_DW_DATUM_SB.": ".MyDatetimeToDate($pole_data["datum"])."<br />\n";
  echo RS_DW_KAT.": ".$pole_data["kat"]."<br />\n";
  echo RS_DW_VEL_SB.": ".$pole_data["fsize"]."<br />\n";
  echo RS_DW_POCET_STAZ.": ".$pole_data["pocitadlo"]."x<br />\n";
  echo "</div>\n";
endif;

echo "<p></p>\n";
}

// tvorba stranky
$vzhledwebu->Generuj();
ObrTabulka();  // Vlozeni layout prvku

echo "<p class=\"nadpis\">".RS_DW_NADPIS."</p>\n"; // nadpis

switch ($GLOBALS['akce']):
  case 'sekce': ShowDownload(); break; // vypsani download sekci
  case 'detail': ShowDetail(); break; // detail souboru
endswitch;

KonecObrTabulka();  // Vlozeni layout prvku
$vzhledwebu->Generuj();
?>