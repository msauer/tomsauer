<?

######################################################################
# phpRS Administration Engine - Main section 1.5.8
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_sloupce, rs_bloky, rs_user, rs_topic, rs_plugin

/*
  Tento soubor zajistuje obsluhu blokoveho subsystemu. Definice bloku a sloupcu.
*/

if ($Uzivatel->StavSession!=1):
  echo "<html><body><div align=\"center\">Tento soubor neni urcen k vnejsimu spousteni!</div></body></html>";
  exit;
endif;

// ---[rozcestnik]------------------------------------------------------------------
switch($GLOBALS['akce']):
     // sloupce
     case "Columns": AdminMenu();
          echo "<h2 align=\"center\">".RS_BLO_ROZ_SPRAVA_BLOK_SL."</h2><p align=\"center\">";
          VypisSloupcu();
          break;
     case "NewColumn": AdminMenu();
          echo "<h2 align=\"center\">".RS_BLO_ROZ_ADD_SL."</h2><p align=\"center\">";
          FormPrSloupec();
          break;
     case "AcNewColumn": AdminMenu();
          echo "<h2 align=\"center\">".RS_BLO_ROZ_ADD_SL."</h2><p align=\"center\">";
          PridatSloupec();
     case "DeleteColumn": AdminMenu();
          echo "<h2 align=\"center\">".RS_BLO_ROZ_DEL_SL."</h2><p align=\"center\">";
          SmazaniSloupce();
          break;
     case "EditColumn": AdminMenu();
          echo "<h2 align=\"center\">".RS_BLO_ROZ_EDIT_SL."</h2><p align=\"center\">";
          FormUprSloupec();
          break;
     case "AcEditColumn": AdminMenu();
          echo "<h2 align=\"center\">".RS_BLO_ROZ_EDIT_SL."</h2><p align=\"center\">";
          UpravaSloupce();
          break;
     // blokz
     case "EditBlock": AdminMenu();
          echo "<h2 align=\"center\">".RS_BLO_ROZ_EDIT_BLOK."</h2><p align=\"center\">";
          VypisBloku();
          break;
     case "AcEditBlock": AdminMenu();
          echo "<h2 align=\"center\">".RS_BLO_ROZ_EDIT_BLOK."</h2><p align=\"center\">";
          UpravaBloku();
          break;
     case "NewBlock": AdminMenu();
          echo "<h2 align=\"center\">".RS_BLO_ROZ_ADD_BLOK."</h2><p align=\"center\">";
          FormPrBloku();
          break;
     case "AcNewBlock": AdminMenu();
          echo "<h2 align=\"center\">".RS_BLO_ROZ_ADD_BLOK."</h2><p align=\"center\">";
          PridaniBloku();
          break;
     case "BlockDelete": AdminMenu();
          echo "<h2 align=\"center\">".RS_BLO_ROZ_DEL_BLOK."</h2><p align=\"center\">";
          SmazaniBloku();
          break;
endswitch;
echo "</p>\n"; // zakonceni P tagu

// ---[hlavni fce - sloupce]--------------------------------------------------------

/*
  VypisSloupcu()
  SmazaniSloupce()
  FormPrSloupec()
  FormularSloupec()
  PridatSloupec()
  UpravaSloupce()
*/

function VypisSloupcu()
{
// navigace
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=NewBlock&amp;modul=bloky\">".RS_BLO_SL_PRIDAT_BLOK."</a> -
<a href=\"".RS_VYKONNYSOUBOR."?akce=NewColumn&amp;modul=bloky\">".RS_BLO_SL_PRIDAT_SL."</a></p>\n";

$dotazslo=mysql_query("select ids,zobrazit from ".$GLOBALS["rspredpona"]."sloupce order by ids",$GLOBALS["dbspojeni"]);
$pocetslo=mysql_num_rows($dotazslo);

// zobrazeni sloupcu
echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"5\" align=\"center\">\n<tr>\n";
for ($pom=0;$pom<$pocetslo;$pom++):
  $sloupec=mysql_fetch_assoc($dotazslo); // nacteni dat o sloupci

  echo "<td width=\"200\" align=\"center\" valign=\"top\">\n";
  // stav sloupce
  echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">\n";
  echo "<table cellspacing=\"2\" cellpadding=\"3\" border=\"0\" width=\"100%\" class=\"ramsedy\">\n";
  echo "<tr class=\"txt\"><td align=\"center\"><b>".($pom+1).". ".RS_BLO_SL_SLOUPEC."</b></td></tr>\n";
  echo "<tr class=\"txt\"><td align=\"center\"><b>".RS_BLO_SL_ZOBRAZIT_SL." ".TestAnoNe($sloupec["zobrazit"])."</b></td></tr>\n";
  echo "<tr class=\"txt\"><td align=\"center\" colspan=\"2\"><select name=\"akce\" size=\"1\"><option value=\"EditColumn\">".RS_BLO_SL_UPRAVIT_SL."</option><option value=\"DeleteColumn\">".RS_BLO_SL_VYMAZAT_SL."</option></select>";
  echo "&nbsp;&nbsp;<input type=\"submit\" value=\" ".RS_TL_OK." \" class=\"tl\" /></td></tr>\n";
  echo "</table><br />\n";
  echo "<input type=\"hidden\" name=\"prids\" value=\"".$sloupec["ids"]."\" /><input type=\"hidden\" name=\"modul\" value=\"bloky\" />\n";
  echo "</form>\n";
  // konec - stav sloupec

  // bloky odpovidajici sloupci
  $dotazblok=mysql_query("select idb,nazev,obsah,hodnost,data_sys,zobrazit from ".$GLOBALS["rspredpona"]."bloky where id_sloupec='".$sloupec["ids"]."' order by hodnost desc,idb",$GLOBALS["dbspojeni"]);
  $pocetblok=mysql_num_rows($dotazblok);
  for ($pom2=0;$pom2<$pocetblok;$pom2++):
    $blokprvky=mysql_fetch_assoc($dotazblok);
    echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">\n";
    if ($blokprvky["data_sys"]==1): // systemovy blok
      echo "<table cellspacing=\"2\" cellpadding=\"3\" class=\"blok-sys\">\n";
      echo "<tr class=\"txt\"><td align=\"left\" colspan=\"2\"><b>".RS_BLO_SL_NADPIS_BLOK."</b><br />".$blokprvky["nazev"]."</td></tr>\n";
      echo "<tr class=\"txt\"><td align=\"left\" colspan=\"2\"><b>".RS_BLO_SL_JM_SYS_BLOK."</b><br />".$blokprvky["obsah"]."</td></tr>\n";
    else: // std. blok
      echo "<table cellspacing=\"2\" cellpadding=\"3\" class=\"blok-std\">\n";
      echo "<tr class=\"txt\"><td align=\"left\" colspan=\"2\"><b>".RS_BLO_SL_NADPIS_BLOK."</b><br />".$blokprvky["nazev"]."</td></tr>\n";
      echo "<tr class=\"txt\"><td align=\"left\" colspan=\"2\"><br /><br /></td></tr>";
    endif;
    echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_BLO_SL_ZOBRAZIT_BLOK."</b></td><td align=\"left\">".TestAnoNe($blokprvky["zobrazit"])."</td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_BLO_SL_PRIORITA_BLOK."</b></td><td align=\"left\">".$blokprvky["hodnost"]."</td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"center\" colspan=\"2\"><select name=\"akce\" size=\"1\"><option value=\"EditBlock\">".RS_BLO_SL_UPRAVIT_BLOK."</option><option value=\"BlockDelete\">".RS_BLO_SL_VYMAZAT_BLOK."</option></select>";
    echo "&nbsp;&nbsp;<input type=\"submit\" value=\" ".RS_TL_OK." \" class=\"tl\" /></td></tr>\n";
    echo "</table><br />\n";
    echo "<input type=\"hidden\" name=\"pridb\" value=\"".$blokprvky["idb"]."\" /><input type=\"hidden\" name=\"modul\" value=\"bloky\" />\n";
    echo "</form>\n";
  endfor;
  // konec - bloky odpovidajici sloupci

  echo "</td>\n";
endfor;
echo "</tr>\n</table>\n";
echo "<p></p>\n";
}

function SmazaniSloupce()
{
$GLOBALS["prids"]=mysql_escape_string($GLOBALS["prids"]);

$chyba=0;

// test na existenci prirazenych bloku
$dotazblok=mysql_query("select count(idb) as pocet from ".$GLOBALS["rspredpona"]."bloky where id_sloupec='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
if (mysql_Result($dotazblok,0,"pocet")>0):
  // sloupec obsahuje nejake bloky
  echo "<p align=\"center\" class=\"txt\">".RS_BLO_SL_ERR_NENI_PRAZDNY."</p>\n";
  $chyba=1;
endif;

if ($chyba==0):
  // vymazani sloupce
  @$error=mysql_query("delete from ".$GLOBALS["rspredpona"]."sloupce where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
  if (!$error):
     echo "<p align=\"center\" class=\"txt\">Error E1: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
  else:
     echo "<p align=\"center\" class=\"txt\">".RS_BLO_SL_OK_DEL_SL."</p>\n"; // vse OK
  endif;
endif;

VypisSloupcu(); // navrat
}

function FormPrSloupec()
{
$GLOBALS["przobrazit"]=1;
$GLOBALS["prids"]=0;

$GLOBALS["typ_akce"]="AcNewColumn";
$GLOBALS["tlacitko"]=RS_TL_PRIDAT; // pridat

FormularSloupec(); // nacteni formulare
}

function FormUprSloupec()
{
$GLOBALS["prids"]=mysql_escape_string($GLOBALS["prids"]);

$dotazslo=mysql_query("select * from ".$GLOBALS["rspredpona"]."sloupce where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
$data_slo=mysql_fetch_assoc($dotazslo);

$GLOBALS["przobrazit"]=$data_slo["zobrazit"];
$GLOBALS["prids"]=$data_slo["ids"];

$GLOBALS["typ_akce"]="AcEditColumn";
$GLOBALS["tlacitko"]=RS_TL_ULOZ; // ulozit

FormularSloupec(); // nacteni formulare
}

function FormularSloupec()
{
// link
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=Columns&amp;modul=bloky\">".RS_BLO_SL_ZPET."</a></p>\n";

echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy\" bgcolor=\"#E6E6E6\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_BLO_SL_FORM_ZOBRAZIT_SL."</b></td>
<td align=\"left\">";
if ($GLOBALS["przobrazit"]==1):
  echo "<input type=\"radio\" name=\"przobrazit\" value=\"1\" checked /> ".RS_TL_ANO." <input type=\"radio\" name=\"przobrazit\" value=\"0\" /> ".RS_TL_NE;
else:
  echo "<input type=\"radio\" name=\"przobrazit\" value=\"1\" /> ".RS_TL_ANO." <input type=\"radio\" name=\"przobrazit\" value=\"0\" checked /> ".RS_TL_NE;
endif;
echo "</td></tr>
</table>
<p align=\"center\"><input type=\"submit\" value=\" ".$GLOBALS["tlacitko"]." \" class=\"tl\" /> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\" /></p>
<input type=\"hidden\" name=\"akce\" value=\"".$GLOBALS["typ_akce"]."\" /><input type=\"hidden\" name=\"prids\" value=\"".$GLOBALS["prids"]."\" />
<input type=\"hidden\" name=\"modul\" value=\"bloky\" />
</form>\n";
}

function PridatSloupec()
{
// pridani sloupce
@$error=mysql_query("insert into ".$GLOBALS["rspredpona"]."sloupce values (null,'".$GLOBALS["przobrazit"]."')",$GLOBALS["dbspojeni"]);
if (!$error):
 echo "<p align=\"center\" class=\"txt\">Error E2: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
 echo "<p align=\"center\" class=\"txt\">".RS_BLO_SL_OK_ADD_SL."</p>\n"; // vse OK
endif;

VypisSloupcu(); // navrat
}

function UpravaSloupce()
{
$GLOBALS["prids"]=mysql_escape_string($GLOBALS["prids"]);

// uprava sloupce
@$error=mysql_query("update ".$GLOBALS["rspredpona"]."sloupce set zobrazit='".$GLOBALS["przobrazit"]."' where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
if (!$error):
 echo "<p align=\"center\" class=\"txt\">Error E3: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
 echo "<p align=\"center\" class=\"txt\">".RS_BLO_SL_OK_EDIT_SL."</p>\n"; // vse OK
endif;

VypisSloupcu(); // navrat
}

// ---[pomocne fce - bloky]---------------------------------------------------------

function OptSloupce($hledam = 0)
{
$str="";

$dotazslo=mysql_query("select ids from ".$GLOBALS["rspredpona"]."sloupce order by ids",$GLOBALS["dbspojeni"]);
$pocetslo=mysql_num_rows($dotazslo);

for ($pom=0;$pom<$pocetslo;$pom++):
  $str.="<option value=\"".mysql_Result($dotazslo,$pom,"ids")."\"";
  if ($hledam==mysql_Result($dotazslo,$pom,"ids")): $str.=" selected"; endif;
  $str.=">".($pom+1)."</option>\n";
endfor;

return $str;
}

// ---[hlavni fce - bloky]----------------------------------------------------------

/*
  VypisBloku()
  UpravaBloku()
  FormPrBloku()
  PridaniBloku()
  SmazaniBloku()
*/

function VypisBloku()
{
// bezpecnostni korekce
$GLOBALS["pridb"]=mysql_escape_string($GLOBALS["pridb"]);

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=Columns&amp;modul=bloky\">".RS_BLO_BL_ZPET."</a></p>\n";

// uprava bloku
$dotazblok=mysql_query("select * from ".$GLOBALS["rspredpona"]."bloky where idb='".$GLOBALS["pridb"]."'",$GLOBALS["dbspojeni"]);
$blokprvky=mysql_fetch_assoc($dotazblok);

echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy\" bgcolor=\"#E6E6E6\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_BLO_BL_NADPIS_BLOK."</b></td>
<td align=\"left\" colspan=\"2\"><input type=\"text\" name=\"prnazev\" size=\"58\" value=\"".$blokprvky["nazev"]."\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=\"3\"><b>".RS_BLO_BL_OBSAH."</b><br />\n";
if ($blokprvky["data_sys"]==1): // systemovy blok
  echo "<textarea name=\"probsah\" rows=\"10\" cols=\"80\" class=\"textbox\" disabled>".$blokprvky["obsah"]."</textarea><input type=\"hidden\" name=\"probsah\" value=\"".$blokprvky["obsah"]."\" />";
else: // standardni blok
  echo "<textarea name=\"probsah\" rows=\"10\" cols=\"80\" class=\"textbox\">".$blokprvky["obsah"]."</textarea>";
endif;
echo "</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_BLO_BL_SLOUPEC."</b></td>
<td align=\"left\"colspan=\"2\"><select name=\"pridsloupce\" size=\"1\">".OptSloupce($blokprvky["id_sloupec"])."</select></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_BLO_BL_TYP_BLOK."</b></td>
<td align=\"left\" colspan=\"2\"><select name=\"prtyp\" size=\"1\">";
switch ($blokprvky["typ"]):
  case 1: echo "<option value=\"1\" selected>".RS_BLO_BL_TYP_1."</option><option value=\"2\">".RS_BLO_BL_TYP_2."</option><option value=\"3\">".RS_BLO_BL_TYP_3."</option><option value=\"4\">".RS_BLO_BL_TYP_4."</option><option value=\"5\">".RS_BLO_BL_TYP_5."</option>"; break;
  case 2: echo "<option value=\"1\">".RS_BLO_BL_TYP_1."</option><option value=\"2\" selected>".RS_BLO_BL_TYP_2."</option><option value=\"3\">".RS_BLO_BL_TYP_3."</option><option value=\"4\">".RS_BLO_BL_TYP_4."</option><option value=\"5\">".RS_BLO_BL_TYP_5."</option>"; break;
  case 3: echo "<option value=\"1\">".RS_BLO_BL_TYP_1."</option><option value=\"2\">".RS_BLO_BL_TYP_2."</option><option value=\"3\" selected>".RS_BLO_BL_TYP_3."</option><option value=\"4\">".RS_BLO_BL_TYP_4."</option><option value=\"5\">".RS_BLO_BL_TYP_5."</option>"; break;
  case 4: echo "<option value=\"1\">".RS_BLO_BL_TYP_1."</option><option value=\"2\">".RS_BLO_BL_TYP_2."</option><option value=\"3\">".RS_BLO_BL_TYP_3."</option><option value=\"4\" selected>".RS_BLO_BL_TYP_4."</option><option value=\"5\">".RS_BLO_BL_TYP_5."</option>"; break;
  case 5: echo "<option value=\"1\">".RS_BLO_BL_TYP_1."</option><option value=\"2\">".RS_BLO_BL_TYP_2."</option><option value=\"3\">".RS_BLO_BL_TYP_3."</option><option value=\"4\">".RS_BLO_BL_TYP_4."</option><option value=\"5\" selected>".RS_BLO_BL_TYP_5."</option>"; break;
  default: echo "<option value=\"1\" selected>".RS_BLO_BL_TYP_1."</option><option value=\"2\">".RS_BLO_BL_TYP_2."</option><option value=\"3\">".RS_BLO_BL_TYP_3."</option><option value=\"4\">".RS_BLO_BL_TYP_4."</option><option value=\"5\">".RS_BLO_BL_TYP_5."</option>"; break;
endswitch;
echo "</select> ".RS_BLO_BL_TYP_BLOK_INFO."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_BLO_BL_PRIOR_BLOK."</b></td>
<td align=\"left\" colspan=\"2\"><input type=\"text\" name=\"prhodnost\" size=\"4\" value=\"".$blokprvky["hodnost"]."\" style=\"text-align:right\" class=\"textpole\"> ".RS_BLO_BL_PRIOR_BLOK_INFO."</td></tr>\n";
echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_BLO_BL_ZOBRAZIT_BLOK."</b></td>
<td align=\"left\" colspan=\"2\">";
if ($blokprvky["zobrazit"]==1):
  echo "<input type=\"radio\" name=\"przobrazit\" value=\"1\" checked /> ".RS_TL_ANO." &nbsp; <input type=\"radio\" name=\"przobrazit\" value=\"0\" /> ".RS_TL_NE;
else:
  echo "<input type=\"radio\" name=\"przobrazit\" value=\"1\" /> ".RS_TL_ANO." &nbsp; <input type=\"radio\" name=\"przobrazit\" value=\"0\" checked /> ".RS_TL_NE;
endif;
echo "</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_BLO_BL_ZPUSOB_ZOBR."</b></td>
<td align=\"left\" colspan=\"2\"><select name=\"przobrazitkde\" size=\"1\">";
switch ($blokprvky["zobrazit_kde"]):
  case 0: echo "<option value=\"0\" selected>".RS_BLO_BL_ZZ_VSUDE."</option><option value=\"1\">".RS_BLO_BL_ZZ_JEN_NA_HL."</option><option value=\"2\">".RS_BLO_BL_ZZ_MIMO_HL."</option>"; break;
  case 1: echo "<option value=\"0\">".RS_BLO_BL_ZZ_VSUDE."</option><option value=\"1\" selected>".RS_BLO_BL_ZZ_JEN_NA_HL."</option><option value=\"2\">".RS_BLO_BL_ZZ_MIMO_HL."</option>"; break;
  case 2: echo "<option value=\"0\">".RS_BLO_BL_ZZ_VSUDE."</option><option value=\"1\">".RS_BLO_BL_ZZ_JEN_NA_HL."</option><option value=\"2\" selected>".RS_BLO_BL_ZZ_MIMO_HL."</option>"; break;
  default: echo "<option value=\"0\" selected>".RS_BLO_BL_ZZ_VSUDE."</option><option value=\"1\">".RS_BLO_BL_ZZ_JEN_NA_HL."</option><option value=\"2\">".RS_BLO_BL_ZZ_MIMO_HL."</option>"; break;
endswitch;
echo "</select></td></tr>
</table>
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\" /> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\" /></p>
<input type=\"hidden\" name=\"pridb\" value=\"".$blokprvky["idb"]."\" /><input type=\"hidden\" name=\"prdatasys\" value=\"".$blokprvky["data_sys"]."\" />
<input type=\"hidden\" name=\"akce\" value=\"AcEditBlock\" /><input type=\"hidden\" name=\"modul\" value=\"bloky\" />
</form>
<p></p>\n";
}

function UpravaBloku()
{
// bezpecnostni korekce
$GLOBALS["prnazev"]=mysql_escape_string($GLOBALS["prnazev"]);
$GLOBALS["probsah"]=mysql_escape_string($GLOBALS["probsah"]);
$GLOBALS["prtyp"]=mysql_escape_string($GLOBALS["prtyp"]);
$GLOBALS["prhodnost"]=mysql_escape_string($GLOBALS["prhodnost"]);
$GLOBALS["przobrazit"]=mysql_escape_string($GLOBALS["przobrazit"]);
$GLOBALS["przobrazitkde"]=mysql_escape_string($GLOBALS["przobrazitkde"]);
$GLOBALS["pridsloupce"]=mysql_escape_string($GLOBALS["pridsloupce"]);
$GLOBALS["pridb"]=mysql_escape_string($GLOBALS["pridb"]);

// vytvoreni prikazu
if ($GLOBALS["prdatasys"]==0): // jen data
 $prset="nazev='".$GLOBALS["prnazev"]."', obsah='".$GLOBALS["probsah"]."', typ='".$GLOBALS["prtyp"]."', hodnost='".$GLOBALS["prhodnost"]."', zobrazit='".$GLOBALS["przobrazit"]."', zobrazit_kde='".$GLOBALS["przobrazitkde"]."', id_sloupec='".$GLOBALS["pridsloupce"]."'";
else: // sys. blok
 $prset="nazev='".$GLOBALS["prnazev"]."', typ='".$GLOBALS["prtyp"]."', hodnost='".$GLOBALS["prhodnost"]."', zobrazit='".$GLOBALS["przobrazit"]."', zobrazit_kde='".$GLOBALS["przobrazitkde"]."', id_sloupec='".$GLOBALS["pridsloupce"]."'";
endif;
// uprava bloku
@$error=mysql_query("update ".$GLOBALS["rspredpona"]."bloky set ".$prset." where idb='".$GLOBALS["pridb"]."'",$GLOBALS["dbspojeni"]);
if (!$error):
 echo "<p align=\"center\" class=\"txt\">Error E1: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
 echo "<p align=\"center\" class=\"txt\">".RS_BLO_BL_OK_EDIT_BLOK."</p>\n"; // vse OK
endif;

VypisSloupcu(); // navrat
}

function FormPrBloku()
{
// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=Columns&amp;modul=bloky\">".RS_BLO_BL_ZPET."</a></p>\n";

// inic. pole - standardni sablony
$polesabzkr[]='ank';
$polesabtxt[]='Systémový blok: Ankety';
$polesabzkr[]='nov';
$polesabtxt[]='Systémový blok: Novinky';
$polesabzkr[]='rub';
$polesabtxt[]='Systémový blok: Seznam rubrik';
$polesabzkr[]='kal';
$polesabtxt[]='Systémový blok: Kalendář';
$polesabzkr[]='hlb';
$polesabtxt[]='Systémový blok: Hlavní blok';
// nacteni plug-in sablon
$dotazplug=mysql_query("select nazev_blok,zkratka_blok from ".$GLOBALS["rspredpona"]."plugin where sys_blok='1' order by idp",$GLOBALS["dbspojeni"]);
// doplneni pole o plu-iny
if (mysql_num_rows($dotazplug)>0):
  while ($pole_data = mysql_fetch_assoc($dotazplug)):
    $polesabzkr[]=$pole_data['zkratka_blok'];
    $polesabtxt[]=$pole_data['nazev_blok'];
  endwhile;
endif;
// pocet prvku v poli
$pocet_polesab=count($polesabzkr);

// formular
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy\" bgcolor=\"#E6E6E6\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_BLO_BL_NADPIS_BLOK."</b></td>
<td align=\"left\" colspan=\"2\"><input type=\"text\" name=\"prnazev\" size=\"58\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=\"3\"><b>".RS_BLO_BL_OBSAH."</b><br>
<textarea name=\"probsah\" rows=\"10\" cols=\"80\" class=\"textbox\">".RS_BLO_BL_OBSAH_INFO."</textarea></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_BLO_BL_SABLONA."</b></td>
<td align=\"left\" colspan=\"2\"><select name=\"prsablona\" size=\"1\">
<option value=\"1:---\">".RS_BLO_BL_SAB_ZADNA."</option>
<option value=\"2:---\">".RS_BLO_BL_SAB_VYHLED_BOX."</option>
<option value=\"3:---\">".RS_BLO_BL_SAB_BEZ_UROVENE_RUBRIKY."</option>\n";
// zobrazeni pole sablon a plug-inu
for ($pom=0;$pom<$pocet_polesab;$pom++):
  echo "<option value=\"4:".$polesabzkr[$pom]."\">".$polesabtxt[$pom]."</option>\n";;
endfor;
echo "</select></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_BLO_BL_SLOUPEC."</b></td>
<td align=\"left\" colspan=\"2\"><select name=\"pridsloupce\" size=\"1\">".OptSloupce($blokprvky["id_sloupec"])."</select></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_BLO_BL_TYP_BLOK."</b></td>
<td align=\"left\" colspan=\"2\">
<select name=\"prtyp\" size=\"1\"><option value=\"1\">".RS_BLO_BL_TYP_1."</option><option value=\"2\">".RS_BLO_BL_TYP_2."</option><option value=\"3\">".RS_BLO_BL_TYP_3."</option><option value=\"4\">".RS_BLO_BL_TYP_4."</option><option value=\"5\">".RS_BLO_BL_TYP_5."</option></select> ".RS_BLO_BL_TYP_BLOK_INFO."
</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_BLO_BL_PRIOR_BLOK."</b></td>
<td align=\"left\" colspan=\"2\"><input type=\"text\" name=\"prhodnost\" size=\"4\" value=\"1\" style=\"text-align:right\" class=\"textpole\" /> ".RS_BLO_BL_PRIOR_BLOK_INFO."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_BLO_BL_ZOBRAZIT_BLOK."</b></td>
<td align=\"left\" colspan=\"2\"><input type=\"radio\" name=\"przobrazit\" value=\"1\" checked />Ano &nbsp;&nbsp; <input type=\"radio\" name=\"przobrazit\" value=\"0\" />Ne</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_BLO_BL_ZPUSOB_ZOBR."</b></td>
<td align=\"left\" colspan=\"2\">
<select name=\"przobrazitkde\" size=\"1\"><option value=\"0\">".RS_BLO_BL_ZZ_VSUDE."</option><option value=\"1\">".RS_BLO_BL_ZZ_JEN_NA_HL."</option><option value=\"2\">".RS_BLO_BL_ZZ_MIMO_HL."</option></select>
</td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcNewBlock\" /><input type=\"hidden\" name=\"modul\" value=\"bloky\" />
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_PRIDAT." \" class=\"tl\" /> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\" /></p>
</form>
<p></p>\n";
// poznamka
echo "<p align=\"center\" class=\"txt\">".RS_BLO_BL_UPOZORNENI."</p>\n";
}

function PridaniBloku()
{
// bezpecnostni korekce
$GLOBALS["prnazev"]=mysql_escape_string($GLOBALS["prnazev"]);
$GLOBALS["probsah"]=mysql_escape_string($GLOBALS["probsah"]);
$GLOBALS["prtyp"]=mysql_escape_string($GLOBALS["prtyp"]);
$GLOBALS["prhodnost"]=mysql_escape_string($GLOBALS["prhodnost"]);
$GLOBALS["przobrazit"]=mysql_escape_string($GLOBALS["przobrazit"]);
$GLOBALS["przobrazitkde"]=mysql_escape_string($GLOBALS["przobrazitkde"]);
$GLOBALS["pridsloupce"]=mysql_escape_string($GLOBALS["pridsloupce"]);
$GLOBALS["prsablona"]=mysql_escape_string($GLOBALS["prsablona"]);

$chyba=0; // inic. chyby
$testnahlb=0; // promenna, ktera urcuje, zda je nutne pred zalozenim bloku otestovat existenci Hlavniho bloku

/*
 data_sys ..... 0 - data, 1 - sys  --> $prdatasys
 sys_funkce ... null, ank, nov, rub, kal   --> $prsysfce
 zobrazit ..... 0 - ne, 1 - ano
 zobrazit_kde . 0 - vsude, 1 - jen index.php, 2 - vsude mimo index.php
*/

list($typsablony,$jmenosablony)=explode(":",$GLOBALS["prsablona"]); // dekompilace sablony

// kompilace sablon
switch($typsablony):
  // zadna sablona; neprepisuje se probsah
  case 1: $prdatasys=0;
          $prsysfce="";
          break;
  // vyhledavaci box; prepisuje se probsah
  case 2: $GLOBALS["probsah"]="<form action=\"search.php\" method=\"post\" title=\"Vyhledávání ve všech článcích!\"><div align=\"center\">\n<b>Hledej!</b><br />\n<input type=\"text\" name=\"rstext\" size=\"15\" /><br />\n<input type=\"image\" src=\"image/ss_hledej.gif\" />\n</div></form>\n";
          $prdatasys=0;
          $prsysfce="";
          break;
  // seznam rubrik; prepisuje se probsah
  case 3: $dotazrub=mysql_query("select idt,nazev,popis from ".$GLOBALS["rspredpona"]."topic order by nazev",$GLOBALS["dbspojeni"]);
          $pocetrub=mysql_num_rows($dotazrub);
          $GLOBALS["probsah"]=""; // inic.
          for($pom=0;$pom<$pocetrub;$pom++):
            $pole_rub=mysql_fetch_assoc($dotazrub);
            $GLOBALS["probsah"].="<a href=\"search.php?rsvelikost=uvod&amp;rstext=all-phpRS-all&amp;rstema=".$pole_rub["idt"]."&amp;stromhlmenu=".$pole_rub["idt"]."\" alt=\"".$pole_rub["popis"]."\">".$pole_rub["nazev"]."</a><br />\n";
          endfor;
          $prdatasys=0;
          $prsysfce="";
          break;
  // systemove bloky; prepisuje se probsah
  case 4: $prdatasys=1;
          $prsysfce=$jmenosablony;
          switch($jmenosablony): // standardni sablony
            case "ank": $GLOBALS["probsah"]="Systémový blok: Ankety"; break;
            case "nov": $GLOBALS["probsah"]="Systémový blok: Novinky"; break;
            case "rub": $GLOBALS["probsah"]="Systémový blok: Seznam rubrik"; break;
            case "kal": $GLOBALS["probsah"]="Systémový blok: Kalendář"; break;
            case "hlb": $GLOBALS["probsah"]="Systémový blok: Hlavní blok"; $testnahlb=1; break;
            default: // plug-in sablony
              $dotazplug=mysql_query("select nazev_blok from ".$GLOBALS["rspredpona"]."plugin where zkratka_blok='".$jmenosablony."' and sys_blok='1'",$GLOBALS["dbspojeni"]);
              $pocetplug=mysql_num_rows($dotazplug);
              if ($pocetplug>0): $GLOBALS["probsah"]=mysql_Result($dotazplug,0,"nazev_blok"); endif;
              break;
          endswitch;
endswitch;

if ($testnahlb==1):
  $dotazbloky=mysql_query("select idb from ".$GLOBALS["rspredpona"]."bloky where data_sys='1' and sys_funkce='hlb'",$GLOBALS["dbspojeni"]);
  if (mysql_num_rows($dotazbloky)>0):
    // Systemovy blok "Hlavni blok" jiz existuje; muze byt pouze jeden
    echo "<p align=\"center\" class=\"txt\">".RS_BLO_BL_ERR_VICE_HL_SYS_BLOKU."</p>\n";
    $chyba=1;
  endif;
endif;

if ($chyba==0):
  // pridani bloku
  @$error=mysql_query("insert into ".$GLOBALS["rspredpona"]."bloky values(null,'".$GLOBALS["prnazev"]."','".$GLOBALS["probsah"]."','".$GLOBALS["prtyp"]."','".$GLOBALS["prhodnost"]."','".$prdatasys."','".$prsysfce."','".$GLOBALS["przobrazit"]."','".$GLOBALS["przobrazitkde"]."','".$GLOBALS["pridsloupce"]."')",$GLOBALS["dbspojeni"]);
  if (!$error):
    echo "<p align=\"center\" class=\"txt\">Error E2: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_BLO_BL_OK_ADD_BLOK."</p>\n"; // vse OK
  endif;
endif;

VypisSloupcu(); // navrat
}

function SmazaniBloku()
{
// bezpecnostni korekce
$GLOBALS["pridb"]=mysql_escape_string($GLOBALS["pridb"]);

@$error=mysql_query("delete from ".$GLOBALS["rspredpona"]."bloky where idb='".$GLOBALS["pridb"]."'",$GLOBALS["dbspojeni"]);
if (!$error):
   echo "<p align=\"center\" class=\"txt\">Error E3: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
   echo "<p align=\"center\" class=\"txt\">".RS_BLO_BL_OK_DEL_BLOK."</p>\n"; // vse OK
endif;

VypisSloupcu(); // navrat
}
?>
