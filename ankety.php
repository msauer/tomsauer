<?

######################################################################
# phpRS Public Inquiry 1.5.1
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_ankety, rs_odpovedi

/*
$akce: "view" - zobrazeni ankety
       "prehled" - vypis vsech anket
       "nehlasuj" - chybova hlaska pri zjisteni opakovaneho hlasovani (vnitrni presmerovani)
       "vysledek" - zobrazeni vysledku pod odhlasovani (vnitrni presmerovani)
       "chyba" - zobrazeni textu chyby (vnitrni presmerovani)
$cil:  "index" - presmerovani na home page
       "vysledek" - zobrazeni vysledku
*/

define('IN_CODE',true); // inic. ochranne konstanty

include_once("config.php");
include_once("specfce.php");
include_once("myweb.php");
include_once("sl.php");
include_once("trlayout.php");
include_once($adrlayoutu);

// test existence nezb.prom.
if (!isset($GLOBALS['akce'])): $GLOBALS['akce']='prehled'; endif;
if (!isset($GLOBALS['cil'])): $GLOBALS['cil']='vysledek'; endif;

// jen hlasovani
function Jenhlasuj($hlas = 0)
{
// bezpecnostni fce - test na ciselnou hodnotu prom.
if (!ereg("^[0-9]*$",$hlas)):
  $hlas=0;
endif;

@$dotazhlasuj=mysql_query("update ".$GLOBALS["rspredpona"]."odpovedi set pocitadlo=(pocitadlo+1) where ido='".$hlas."'",$GLOBALS["dbspojeni"]);
if (!$dotazhlasuj):
  return 0; // chyba
else:
  return 1; // vse OK
endif;
}

// test na uzamceni ankety - 1 = uzamcena, 0 = otevrena
function TestNaUzamceniAnk($ank = 0)
{
$dotazanketa=mysql_query("select uzavrena from ".$GLOBALS["rspredpona"]."ankety where ida='".$ank."'",$GLOBALS["dbspojeni"]);
if ($dotazanketa!=0):
  return mysql_Result($dotazanketa,0,"uzavrena"); // realny stav
else:
  return 1; // chyba - defaultne se hlasi "uzamcena"
endif;
}

// nacteni ochranneho cookies
function AnkCookies_Nacti()
{
if (isset($_COOKIE["inquiry"])):
  // kdyz kontrolni cookies existuje
  $nacteno=base64_decode($_COOKIE["inquiry"]);
  return $nacteno;
else:
  return '';
endif;
}

// test na opakovane volani jedne ankety
function AnkCookies_JeReload($test_str = '', $ank = 0)
{
$vysledek = 0; // defaultne neobsahuje

if ($test_str!=''):
  $pom_pole=explode(":",$test_str);
  if (is_array($pom_pole)):
    if (in_array($ank,$pom_pole)):
      $vysledek=1; // obsahuje anketu
    endif;
  endif;
endif;

return $vysledek;
}

// ulozeni ochranneho cookies
function AnkCookies_UlozAnk($test_str = '', $ank = 0)
{
$pom_cookie_str='';

// pridani nove ankety do seznamu odhlasovanych anket
if ($test_str==''):
  $pom_cookie_str=$ank;
else:
  $pom_cookie_str.=':'.$ank;
endif;

// odeslani cookies
$zakodovany_str=base64_encode($pom_cookie_str);
setcookie("inquiry",$zakodovany_str,time()+315360000);
}

function Zo($x = 0)
{
return number_format($x,2,".",",");
}

// zobrazeni ankety bez moznosti hlasovat
function ZobrazAnketu()
{
$GLOBALS['anketa']=mysql_escape_string($GLOBALS['anketa']);

// zjisteni anketni otazky
$dotazotazka=mysql_query("select otazka from ".$GLOBALS["rspredpona"]."ankety where ida='".$GLOBALS["anketa"]."'",$GLOBALS["dbspojeni"]);
$pranketotazka=mysql_result($dotazotazka,0,"otazka");

// zjisteni celkoveho poctu hlasu
$dotazcelkem=mysql_query("select sum(pocitadlo) as soucet from ".$GLOBALS["rspredpona"]."odpovedi where anketa='".$GLOBALS["anketa"]."'",$GLOBALS["dbspojeni"]);
$prcelkemhlasu=mysql_result($dotazcelkem,0,"soucet");

// kolik dilku pripada na jden hlas
if ($prcelkemhlasu==0):
  $jedno_proc=0;
else:
  $jedno_proc=(100/$prcelkemhlasu);
endif;

// zjisteni odpovedi a jejich vypis
$dotazodp=mysql_query("select ido,odpoved,pocitadlo from ".$GLOBALS["rspredpona"]."odpovedi where anketa='".$GLOBALS["anketa"]."' order by ido",$GLOBALS["dbspojeni"]);
$pocetodp=mysql_num_rows($dotazodp);

echo "<p class=\"podnadpis\">".$pranketotazka."</p>
<table align=\"center\" width=\"420\" border=\"0\">
<tr><td class=\"z\">\n";
for ($pom=0;$pom<$pocetodp;$pom++):
  $pole_data=mysql_fetch_assoc($dotazodp);
  echo "<p>".$pole_data["odpoved"]." <i>(".RS_AN_POCET_HLA.": ".$pole_data["pocitadlo"].")</i><br />\n";
  $akt_procento=$jedno_proc*$pole_data["pocitadlo"];
  echo "<img src=\"".$GLOBALS["adrobrlayoutu"]."line_a.gif\" width=\"8\" height=\"15\" alt=\"\" />";
  echo "<img src=\"".$GLOBALS["adrobrlayoutu"]."line_b.gif\" width=".ceil(3*$akt_procento)." height=\"15\" alt=\"\" />";
  echo "<img src=\"".$GLOBALS["adrobrlayoutu"]."line_c.gif\" width=\"8\" height=\"15\" alt=\"\" /> (".Zo($akt_procento)." %)</p>\n";
endfor;
echo "</td></tr>
</table>
<p align=\"center\" class=\"z\">
<i>".RS_AN_CELKEM_HLA.": ".$prcelkemhlasu."</i><br /><br /><a href=\"ankety.php\">".RS_AN_ZOBRAZ_VSE."</a>
</p>
<p></p>\n";
}

// zobrazeni ankety + moznost hlasovani
function ZobrazHlasAnketu()
{
$GLOBALS['anketa']=mysql_escape_string($GLOBALS['anketa']);

// zjisteni anketni otazky
$dotazotazka=mysql_query("select otazka,zobrazit,uzavrena from ".$GLOBALS["rspredpona"]."ankety where ida='".$GLOBALS["anketa"]."'",$GLOBALS["dbspojeni"]);
if ($dotazotazka!=0):
  $akt_anketa=mysql_fetch_assoc($dotazotazka); // nacteni ankety
else:
  $akt_anketa=array(); // chyba
endif;

// test na aktivni stav ankety
if ($akt_anketa['zobrazit']==1):

  // zjisteni celkoveho poctu hlasu
  $dotazcelkem=mysql_query("select sum(pocitadlo) as soucet from ".$GLOBALS["rspredpona"]."odpovedi where anketa='".$GLOBALS["anketa"]."'",$GLOBALS["dbspojeni"]);
  $prcelkemhlasu=mysql_result($dotazcelkem,0,"soucet");

  // kolik dilku pripada na jden hlas
  if ($prcelkemhlasu==0):
    $jedno_proc=0;
  else:
    $jedno_proc=(100/$prcelkemhlasu);
  endif;

  // zjisteni odpovedi a jejich vypis
  $dotazodp=mysql_query("select ido,odpoved,pocitadlo from ".$GLOBALS["rspredpona"]."odpovedi where anketa='".$GLOBALS["anketa"]."' order by ido",$GLOBALS["dbspojeni"]);
  $pocetodp=mysql_num_rows($dotazodp);

else:

  // anketa neaktivni
  $pocetodp=0;

endif;

echo "<p class=\"podnadpis\">".$akt_anketa['otazka']."</p>
<form action=\"ankety.php\" method=\"post\">
<table align=\"center\" width=\"420\" border=\"0\">
<tr><td class=\"z\">\n";
for ($pom=0;$pom<$pocetodp;$pom++):
  $pole_data=mysql_fetch_assoc($dotazodp);
  echo "<p><input type=\"radio\" name=\"hlas\" value=\"".$pole_data["ido"]."\"";
  if ($pom==0): echo " checked"; endif;  // zaskrtnuti prvni polozky
  echo " />&nbsp;&nbsp;".$pole_data["odpoved"]." <i>(".RS_AN_POCET_HLA.": ".$pole_data["pocitadlo"].")</i><br />\n";
  $akt_procento=$jedno_proc*$pole_data["pocitadlo"];
  echo "<img src=\"".$GLOBALS["adrobrlayoutu"]."line_a.gif\" width=\"8\" height=\"15\" alt=\"\" />";
  echo "<img src=\"".$GLOBALS["adrobrlayoutu"]."line_b.gif\" width=".ceil(3*$akt_procento)." height=\"15\" alt=\"\" />";
  echo "<img src=\"".$GLOBALS["adrobrlayoutu"]."line_c.gif\" width=\"8\" height=\"15\" alt=\"\" /> (".Zo($akt_procento)." %)</p>\n";
endfor;
echo "</td></tr>
</table>
<p align=\"center\" class=\"z\">
<input type=\"submit\" value=\" ".RS_AN_TL_HLASUJ." \" class=\"tl\" /><br /><br />
<i>".RS_AN_CELKEM_HLA.": ".$prcelkemhlasu."</i><br /><br /><a href=\"ankety.php\">".RS_AN_ZOBRAZ_VSE."</a>
</p>
<input type=\"hidden\" name=\"akce\" value=\"hlasuj\" /><input type=\"hidden\" name=\"anketa\" value=\"".$GLOBALS['anketa']."\" />
</form>
<p></p>\n";
}

function Prehled()
{
// vypis vsech anket
$dotazankety=mysql_query("select ida,otazka,datum,uzavrena from ".$GLOBALS["rspredpona"]."ankety where zobrazit=1 order by datum desc",$GLOBALS["dbspojeni"]);
$pocetankety=mysql_num_rows($dotazankety);

echo "<div class=\"z\">\n";
for ($pom=0;$pom<$pocetankety;$pom++):
  $pole_data=mysql_fetch_assoc($dotazankety);
  echo "<p>".$pole_data["otazka"]." (".MyDatetimeToDate($pole_data["datum"]).")";
  if ($pole_data["uzavrena"]==0):
    // moznost hlasovani
    echo " -> <b><a href=\"ankety.php?akce=view&amp;anketa=".$pole_data["ida"]."\">".RS_AN_HLASUJ."</a></b>";
  else:
    // anketa je uzavrena
    echo " -> <b><a href=\"ankety.php?akce=vysledek&amp;anketa=".$pole_data["ida"]."\">".RS_AN_BLOKACE."</a></b>";
  endif;
  echo "</p>\n";
endfor;
echo "</div>\n";
echo "<p></p>\n";
}

function Nehlasuj()
{
echo "<p align=\"center\">".RS_AN_NELZE_HLASOVAT."<br /><br /><a href=\"ankety.php\">".RS_AN_ZOBRAZ_VSE."</a></p>\n";
echo "<p></p>\n";
}

function ZobrazChybu($info_str = '')
{
echo "<p align=\"center\">".$info_str."<br /><br /><a href=\"ankety.php\">".RS_AN_ZOBRAZ_VSE."</a></p>\n";
echo "<p></p>\n";
}

// inic. text chyba
$GLOBALS['ankteta_chyba_txt']='';

// odchyceni hlasovani
if ($GLOBALS['akce']=='hlasuj'):
  if (!isset($GLOBALS['hlas'])||!isset($GLOBALS['anketa'])):
    // chyba inic. faze
    $GLOBALS['akce']='chyba';
    $GLOBALS['ankteta_chyba_txt']=RS_AN_ERR2; // AnketnĂ­ subsystĂ©m nenĂ­ schopen identifikovat vybranou anketu!
  else:
    $GLOBALS['hlas']=mysql_escape_string($GLOBALS['hlas']); // id odpoved
    $GLOBALS['anketa']=mysql_escape_string($GLOBALS['anketa']); // id anketa
    // test na zamceni ankety; 1 = zamcena, 0 = otevrena
    if (TestNaUzamceniAnk($GLOBALS['anketa'])==1):
      $GLOBALS['akce']='chyba';
      $GLOBALS['ankteta_chyba_txt']=RS_AN_ERR3; // VybranĂˇ anketa je jiĹľ uzavĹ™ena!
    else:
      // test na opakujici se hlasovani
      $akt_obsah_cookies=AnkCookies_Nacti(); // nacteni ochranneho cookie
      // testovano pres cookies a pocitani IP adres
      if (AnkCookies_JeReload($akt_obsah_cookies,$GLOBALS['anketa'])==0&&TestNaOpakujiciIP('ank'.$GLOBALS['anketa'],$GLOBALS['rsconfig']['anketa_delka_omezeni'],$GLOBALS['rsconfig']['anketa_max_pocet_opak'])==0):
        // hlasovani povoleno
        if (Jenhlasuj($GLOBALS['hlas'])==1):
          // odhlasovano v poradku
          AnkCookies_UlozAnk($akt_obsah_cookies,$GLOBALS['anketa']);
          switch ($GLOBALS['cil']):
            case 'index':
              Header("Location: ".$GLOBALS["baseadr"]."index.php");
              exit();
              break;
            case 'vysledek':
              $GLOBALS['akce']='vysledek';
              break;
          endswitch;
        else:
          // chyba pri hlasovani
          $GLOBALS['akce']='chyba';
          $GLOBALS['ankteta_chyba_txt']=RS_AN_ERR1;
        endif;
      else:
        // zjisteno opakujici se hlasovani
        $GLOBALS['akce']='nehlasuj';
      endif;
    endif;
  endif;
endif;

// tvorba stranky
$vzhledwebu->Generuj();
ObrTabulka();  // Vlozeni layout prvku

echo "<p class=\"nadpis\">".RS_AN_NADPIS."<p>\n";
// volba akce
switch ($GLOBALS['akce']):
  case 'view': ZobrazHlasAnketu(); break;
  case 'vysledek': ZobrazAnketu(); break;
  case 'prehled': Prehled(); break;
  case 'nehlasuj': Nehlasuj(); break;
  case 'chyba': ZobrazChybu($GLOBALS['ankteta_chyba_txt']); break;
endswitch;

// dokonceni tvorby stranky
KonecObrTabulka();   // Vlozeni layout prvku
$vzhledwebu->Generuj();
?>