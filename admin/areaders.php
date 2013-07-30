<?

######################################################################
# phpRS Administration Engine - Reader's section 1.2.7
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_ctenari

/*
  Tento soubor zajistuje spravu registrovanych ctenaru.
*/

if ($Uzivatel->StavSession!=1):
  echo "<html><body><div align=\"center\">Tento soubor neni urcen k vnejsimu spousteni!</div></body></html>";
  exit;
endif;

// ---[rozcestnik]------------------------------------------------------------------
switch($GLOBALS['akce']):
     // ctenari
     case "ShowReaders": AdminMenu();
          echo "<h2 align=\"center\">".RS_CTE_ROZ_SPRAVA_CTENARU."</h2><p align=\"center\">";
          ShowReaders();
          break;
     case "AcReaders": AdminMenu();
          echo "<h2 align=\"center\">".RS_CTE_ROZ_SPRAVA_CTENARU."</h2><p align=\"center\">";
          AcReaders();
          break;
endswitch;
echo "</p>\n"; // zakonceni P tagu

// ---[hlavni fce - Ctenari]--------------------------------------------------------

/*
  KolikCtenaru()
  ShowReaders()
  AcReaders()
*/

// funkce, ktera vraci aktualni pocet vsech registrovanych ctenaru
function KolikCtenaru()
{
$dotazct=mysql_query("select count(idc) as pocet from ".$GLOBALS["rspredpona"]."ctenari",$GLOBALS["dbspojeni"]);
if ($dotazct!=0):
  return mysql_Result($dotazct,0,"pocet");
else:
  return 0;
endif;
}

// vypis vsech ctenaru nalezicich do zadaneho limitu
function ShowReaders()
{
if (!isset($GLOBALS["prmin"])): $GLOBALS["prmin"]=0; endif;
if (!isset($GLOBALS["prmax"])): $GLOBALS["prmax"]=60; endif;
if (!isset($GLOBALS["prorderby"])): $GLOBALS["prorderby"]='reg'; endif;

$pocet=KolikCtenaru(); // celkovy pocet ctenaru

echo "<form action=\"admin.php\" method=\"post\">
<p align=\"center\" class=\"txt\">
<input type=\"hidden\" name=\"akce\" value=\"ShowReaders\" /><input type=\"hidden\" name=\"modul\" value=\"ctenari\" />
<input type=\"submit\" value=\" ".RS_CTE_SC_ZOBRAZ_CTE." \" class=\"tl\" />
".RS_CTE_SC_OD." <input type=\"text\" name=\"prmin\" value=\"".$GLOBALS["prmin"]."\" size=\"4\" class=\"textpole\" />
".RS_CTE_SC_DO." <input type=\"text\" name=\"prmax\" value=\"".$GLOBALS["prmax"]."\" size=\"4\" class=\"textpole\" />
".RS_CTE_SC_TRIDIT." <select name=\"prorderby\" size=\"1\">\n";
switch ($GLOBALS["prorderby"]):
  case 'reg': echo "<option value=\"reg\" selected>".RS_CTE_SC_DATA_REG."</option><option value=\"posledni\">".RS_CTE_SC_DATA_POSL_AKT."</option><option value=\"user\">".RS_CTE_SC_PREZDIVKY."</option>"; break;
  case 'posledni': echo "<option value=\"reg\">".RS_CTE_SC_DATA_REG."</option><option value=\"posledni\" selected>".RS_CTE_SC_DATA_POSL_AKT."</option><option value=\"user\">".RS_CTE_SC_PREZDIVKY."</option>"; break;
  case 'user': echo "<option value=\"reg\">".RS_CTE_SC_DATA_REG."</option><option value=\"posledni\">".RS_CTE_SC_DATA_POSL_AKT."</option><option value=\"user\" selected>".RS_CTE_SC_PREZDIVKY."</option>"; break;
endswitch;
echo "</select>
(".RS_CTE_SC_POCET_CTE.": ".$pocet.")
</p>
</form>
<p><hr width=\"600\"></p>\n";

// vypocet omezeni
if ($GLOBALS["prmin"]>0): $pruprmin=($GLOBALS["prmin"]-1); else: $pruprmin=0; endif;
$kolik=($GLOBALS["prmax"]-$pruprmin);
if ($kolik<0): $kolik=0; endif;

switch ($GLOBALS["prorderby"]):
  case 'reg': $akt_prorderby='datum desc'; break; // data registrace
  case 'posledni': $akt_prorderby='posledni_login desc'; break; // data poslední aktivity
  case 'user': $akt_prorderby='prezdivka'; break; // přezdívky
  default: $akt_prorderby='datum desc';
endswitch;

$dotazct=mysql_query("select idc,prezdivka,password,jmeno,email,datum,info,posledni_login from ".$GLOBALS["rspredpona"]."ctenari order by ".$akt_prorderby." limit ".$pruprmin.",".$kolik,$GLOBALS["dbspojeni"]);
$pocetct=mysql_num_rows($dotazct);

if ($pocetct==0):
  // CHYBA: Zadaný interval (od xxx do yyy) je prázdný!
  echo "<p align=\"center\" class=\"txt\">".RS_ADM_INTERVAL_C1." ".$GLOBALS["prmin"]." ".RS_ADM_INTERVAL_C2." ".$GLOBALS["prmax"].RS_ADM_INTERVAL_C3."</p>\n";
else:
  echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">\n";
  echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">\n";
  echo "<tr class=\"smltxt\" bgcolor=\"#E6E6E6\"><td align=\"center\"><b>".RS_CTE_SC_FORM_ID_CTE."</b></td>";
  echo "<td align=\"left\"><b>".RS_CTE_SC_FORM_PREZDIVAK."</b></td>";
  echo "<td align=\"left\"><b>".RS_CTE_SC_FORM_JMENO."</b></td>";
  echo "<td align=\"center\"><b>".RS_CTE_SC_FORM_EMAIL."</b></td>";
  echo "<td align=\"center\"><b>".RS_CTE_SC_FORM_INFO_CTE."</b></td>";
  echo "<td align=\"center\"><b>".RS_CTE_SC_FORM_REG."</b></td>";
  echo "<td align=\"center\"><b>".RS_CTE_SC_FORM_POSL_AKT."</b></td>";
  echo "<td align=\"center\"><b>".RS_CTE_SC_FORM_SMAZ."</b></td></tr>\n";
  for ($pom=0;$pom<$pocetct;$pom++):
    $pole_data=mysql_fetch_assoc($dotazct);
    echo "<tr class=\"smltxt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">\n";
    echo "<td align=\"center\">".$pole_data['idc']."</td>\n";
    echo "<td align=\"center\">".$pole_data['prezdivka']."</td>\n";
    echo "<td align=\"left\">".TestNaNic($pole_data['jmeno'])."</td>\n";
    echo "<td align=\"left\">".TestNaNic($pole_data['email'])."</td>\n";
    echo "<td align=\"left\">";
    if ($pole_data['info']==0):
      echo "<input type=\"radio\" name=\"prinfo[".$pom."]\" value=\"1\" /> ".RS_TL_ANO." <input type=\"radio\" name=\"prinfo[".$pom."]\" value=\"0\" checked /> ".RS_TL_NE;
    else:
      echo "<input type=\"radio\" name=\"prinfo[".$pom."]\" value=\"1\" checked /> ".RS_TL_ANO." <input type=\"radio\" name=\"prinfo[".$pom."]\" value=\"0\" /> ".RS_TL_NE;
    endif;
    echo "<td align=\"center\">".MyDatetimeToDate($pole_data['datum'])."</td>\n";
    echo "<td align=\"center\">".MyDatetimeToDate($pole_data['posledni_login'])."</td>\n";
    echo "<input type=\"hidden\" name=\"prinfopuvodni[".$pom."]\" value=\"".$pole_data['info']."\" /></td>\n";
    echo "<td align=\"center\"><input type=\"checkbox\" name=\"prpoledelid[]\" value=\"".$pole_data['idc']."\" /></td>\n";
    echo "<input type=\"hidden\" name=\"prpoleid[".$pom."]\" value=\"".$pole_data['idc']."\" />\n";
    echo "</tr>\n";
  endfor;
  echo "<tr class=\"txt\"><td align=\"right\" colspan=\"8\"><input type=\"submit\" value=\" ".RS_CTE_SC_AKTUALIZACE." \" class=\"tl\" /></td></tr>\n";
  echo "</table>\n";
  echo "<input type=\"hidden\" name=\"akce\" value=\"AcReaders\" /><input type=\"hidden\" name=\"modul\" value=\"ctenari\" />\n";
  echo "<input type=\"hidden\" name=\"prmin\" value=\"".$GLOBALS["prmin"]."\" /><input type=\"hidden\" name=\"prmax\" value=\"".$GLOBALS["prmax"]."\" />\n";
  echo "</form>\n";
endif;
}

function AcReaders()
{
$chyba=0; // inic. chyby

// ------ uloz zmeny ------
if (!isset($GLOBALS["prpoleid"])): // kdyz neexistuje vstup
  $pocetpole=0;
else:
  $pocetpole=count($GLOBALS["prpoleid"]);
endif;

for ($pom=0;$pom<$pocetpole;$pom++):
  $GLOBALS["prinfo"][$pom]=addslashes($GLOBALS["prinfo"][$pom]); // korekce vstupu

  if ($GLOBALS["prinfopuvodni"][$pom]!=$GLOBALS["prinfo"][$pom]): // porovnani akt. nastaveni s puvodnim
    @$error=mysql_query("update ".$GLOBALS["rspredpona"]."ctenari set info='".$GLOBALS["prinfo"][$pom]."' where idc='".addslashes($GLOBALS["prpoleid"][$pom])."'",$GLOBALS["dbspojeni"]);
    if (!$error):
      echo "<p align=\"center\" class=\"txt\">Error R1: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
      $chyba=1;
    endif;
  endif;
endfor;

// ------ smazani ------
if (!isset($GLOBALS["prpoledelid"])): // inic. prom.
  $pocetpoledel=0;
else:
  $pocetpoledel=count($GLOBALS["prpoledelid"]);
endif;

for ($pom=0;$pom<$pocetpoledel;$pom++):
  @$error=mysql_query("delete from ".$GLOBALS["rspredpona"]."ctenari where idc='".addslashes($GLOBALS["prpoledelid"][$pom])."'",$GLOBALS["dbspojeni"]);
  if (!$error):
    echo "<p align=\"center\" class=\"txt\">Error R2: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
    $chyba=1;
  endif;
endfor;

// ------ vysledek ------
if ($chyba==0): // vysledek globalniho stavu
  echo "<p align=\"center\" class=\"txt\">".RS_CTE_SC_OK_AKTUAL_CTE."</p>\n";
endif;

// navrat
ShowReaders();
}
?>
