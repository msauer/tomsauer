<?

######################################################################
# phpRS Engine 1.6.1
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_topic, rs_links, rs_links_sekce, rs_user

if (!defined('IN_CODE')): die('Nepovoleny pristup! / Hacking attempt!'); endif;

function Verze()
{
include_once("version.php");
echo "<div align=\"center\"><span class=\"nadpis\">".$phprsversion."</span><br />".$GLOBALS["layoutversion"]."</div>\n";
echo "<p></p>\n";
}

function ShowTopics()
{
$dotaztopic=mysql_query("select idt,nazev,obrazek from ".$GLOBALS["rspredpona"]."topic order by nazev",$GLOBALS["dbspojeni"]);
$pocettopic=mysql_num_rows($dotaztopic);
if ($pocettopic==0):
  echo "<p align=\"center\" class=\"z\">".RS_EN_TOPIC_ERR1."</p>\n";
else:
  $pocet_sl=3;
  $akt_sl=1;
  // start table
  echo "<table cellspacing=\"5\" border=\"0\" align=\"center\">\n";
  for ($pom=0;$pom<$pocettopic;$pom++):
    if ($akt_sl==1):
      echo "<tr>\n";
    endif;
    echo "<td align=\"center\" height=\"120\" width=\"120\" class=\"z\">";
    echo "<a href=\"search.php?rstext=all-phpRS-all&amp;rstema=".mysql_Result($dotaztopic,$pom,"idt")."\"><img src=\"".mysql_Result($dotaztopic,$pom,"obrazek")."\" border=\"0\" alt=\"".mysql_Result($dotaztopic,$pom,"nazev")."\" /></a><br /><br />";
    echo "<a href=\"search.php?rstext=all-phpRS-all&amp;rstema=".mysql_Result($dotaztopic,$pom,"idt")."\">".mysql_Result($dotaztopic,$pom,"nazev")."</a>\n";
    echo "</td>\n";
    if ($akt_sl==$pocet_sl):
      echo "</tr>\n";
      $akt_sl=1;
    else:
      $akt_sl++;
    endif;
  endfor;
  // dokonceni tabulky
  if ($akt_sl!=1&&$akt_sl<=$pocet_sl):
    for ($pom=$akt_sl;$pom<=$pocet_sl;$pom++):
      echo "<td></td>\n";
    endfor;
    echo "</tr>\n";
  endif;
  // konec table
  echo "</table>\n";
endif;
echo "<p></p>\n";
}

function AktSekce($aktid,$aktnazev,$testovaneid)
{
// funkce testujici shodu aktualne vybrane sekce se zaslanou sekci pres promenne $akt...
if ($aktid==$testovaneid):
  return $aktnazev;
else:
  return "<a href=\"index.php?akce=linky&amp;sekce=".$aktid."\">".$aktnazev."</a>";
endif;
}

function ShowLinks()
{
// test na existenci sekce
if (!isset($GLOBALS["sekce"])): $GLOBALS["sekce"]=0; endif; // 0 = neexistujici sekce
$GLOBALS["sekce"]=addslashes($GLOBALS["sekce"]); // korekce vstupu

$zobrazvypis=0; // inic. zobr. - ne
$mnozstvilinku=50; // nastaveni mnozstvi zobrazenych linku

echo "<p class=\"nadpis\">".RS_EN_LINKS_NADPIS."</p>\n"; // nadpis

// urceni omezeni
if ($GLOBALS["sekce"]==0):
  // neexistuje upresneni sekce - nacteni hl. sekce
  $dotazhl=mysql_query("select ids from ".$GLOBALS["rspredpona"]."links_sekce where hlavnisekce='1'",$GLOBALS["dbspojeni"]);
  if (mysql_num_rows($dotazhl)>0):
    $GLOBALS["sekce"]=mysql_Result($dotazhl,0,"ids"); // id hlavni sekce
    $promezeni="where idsekce='".$GLOBALS["sekce"]."' ";
    $zobrazvypis=1; // vypis - ano
  endif;
else:
  // existuje prom. sekce
  $promezeni="where idsekce='".$GLOBALS["sekce"]."' ";
  $zobrazvypis=1; // vypis - ano
endif;

// zobrazeni prehledu sekci
$prepinac=0;
$dotazsek=mysql_query("select ids,nazev from ".$GLOBALS["rspredpona"]."links_sekce order by nazev",$GLOBALS["dbspojeni"]);
$pocetsek=mysql_num_rows($dotazsek);
if ($pocetsek>1):
  echo "<table border=\"0\" align=\"center\">\n";
  for ($pom=0;$pom<$pocetsek;$pom++):
    if ($prepinac==0):
      echo "<tr class=\"z\"><td align=\"center\"><b>".AktSekce(mysql_Result($dotazsek,$pom,"ids"),mysql_Result($dotazsek,$pom,"nazev"),$GLOBALS["sekce"])."</b></td>\n";
      echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>"; // mezera mezi sloupci
      $prepinac=1;
    else:
      echo "<td align=\"center\"><b>".AktSekce(mysql_Result($dotazsek,$pom,"ids"),mysql_Result($dotazsek,$pom,"nazev"),$GLOBALS["sekce"])."</b></td></tr>\n";
      $prepinac=0;
    endif;
  endfor;
  if (($pocetsek>0)&&($prepinac==1)): echo "<td>&nbsp;</td></tr>\n"; endif;
  echo "</table>\n";
  echo "<p></p>\n";
endif;

// vypis linku
if ($zobrazvypis==1):
  $dotazlink=mysql_query("select titulek,adresa,komentar,zdroj_jm,zdroj_url,zobraz_zdroj from ".$GLOBALS["rspredpona"]."links ".$promezeni."order by datum desc limit 0,".$mnozstvilinku,$GLOBALS["dbspojeni"]);
  $pocetlink=mysql_num_rows($dotazlink);
  if ($pocetlink==0):
    echo "<p align=\"center\" class=\"z\">".RS_EN_LINKS_ERR1."</p>\n";
  else:
    for ($pom=0;$pom<$pocetlink;$pom++):
      $pole_data=mysql_fetch_assoc($dotazlink);
      echo "<p class=\"z\"><a href=\"".$pole_data["adresa"]."\" target=\"_blank\">".$pole_data["titulek"]."</a>\n";
      if ($pole_data["komentar"]!=""): // kdyz existuje komentar
        echo " - ".$pole_data["komentar"];
      endif;
      if ($pole_data["zobraz_zdroj"]==1): // kdyz se ma zobr. zdroj
        echo "&nbsp;&nbsp;<i>".RS_EN_LINKS_ZDROJ.": <a href=\"".$pole_data["zdroj_url"]."\" target=\"_blank\">".$pole_data["zdroj_jm"]."</a></i>";
      endif;
      echo "</p>\n";
    endfor;
  endif;
endif;
echo "<p></p>\n";
}

function ShowStatistics()
{
$mnozstvipolozek=15; // nastaveni mnozstvi zobrazenych clanku
$akt_cas=Date("Y-m-d H:i:s");

// nacteni seznamu uzivatelu(autoru) do pole "autori"
$dotazaut=mysql_query("select idu,jmeno,email from ".$GLOBALS["rspredpona"]."user order by idu",$GLOBALS["dbspojeni"]);
$pocetaut=mysql_num_rows($dotazaut);
for ($pom=0;$pom<$pocetaut;$pom++):
  $pole_user=mysql_fetch_assoc($dotazaut);
  $autori[$pole_user["idu"]][0]=$pole_user["jmeno"];
  //$autori[$pole_user["idu"]][1]="mailto:".$pole_user["email"];
endfor;

echo "<p class=\"nadpis\">".RS_EN_STAT_NADPIS."<p>\n"; // nadpis

// vypis n-nejctenejsich clanku
$dotaznej=mysql_query("select link,titulek,datum,autor,visit from ".$GLOBALS["rspredpona"]."clanky where visible='1' and datum<='".$akt_cas."' order by visit desc limit 0,".$mnozstvipolozek,$GLOBALS["dbspojeni"]);
$pocetnej=mysql_num_rows($dotaznej);
// overeni pritomnosti clanku
if ($pocetnej==0):
  echo "<p align=\"center\" class=\"z\">".RS_EN_STAT_ERR1."</p>\n";
else:
  for ($pom=0;$pom<$pocetnej;$pom++):
    $pole_data=mysql_fetch_assoc($dotaznej);
    echo "<p class=\"z\"><b><a href=\"view.php?cisloclanku=".$pole_data["link"]."\">".$pole_data["titulek"]."</a></b> (<i>".$pole_data["visit"]."x, ".MyDatetimeToDate($pole_data["datum"]);
    // kompilace autora
    if (isset($autori[$pole_data["autor"]][0])):
      echo ", ".$autori[$pole_data["autor"]][0];
    endif;
    echo "</i>)</p>\n";
  endfor;
endif;
echo "<p></p>\n";
}
?>
