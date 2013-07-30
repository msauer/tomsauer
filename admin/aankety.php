<?
######################################################################
# phpRS Administration Engine - Public inquiry's section 1.4.2
######################################################################

// Copyright (c) 2001-2004 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_ankety, rs_odpovedi, rs_config

/*
  Tento soubor zajistuje spravu anketniho subsystemu.
*/

if ($Uzivatel->StavSession!=1):
  echo "<html><body><div align=\"center\">Tento soubor neni urcen k vnejsimu spousteni!</div></body></html>";
  exit;
endif;

// ---[rozcestnik]------------------------------------------------------------------
switch($GLOBALS['akce']):
     // ankety
     case "ShowInquiry": AdminMenu();
          echo "<h2 align=\"center\">".RS_AKT_ROZ_VIEW_AKT."</h2><p align=\"center\">";
          ShowInquiry();
          break;
     case "AddInquiry": AdminMenu();
          echo "<h2 align=\"center\">".RS_AKT_ROZ_ADD_AKT."</h2><p align=\"center\">";
          AddInquiry();
          break;
     case "AcAddInquiry": AdminMenu();
          echo "<h2 align=\"center\">".RS_AKT_ROZ_ADD_AKT."</h2><p align=\"center\">";
          AcAddInquiry();
          break;
     case "DelInquiry": AdminMenu();
          echo "<h2 align=\"center\">".RS_AKT_ROZ_DEL_AKT."</h2><p align=\"center\">";
          DelInquiry();
          break;
     case "EditInquiry": AdminMenu();
          echo "<h2 align=\"center\">".RS_AKT_ROZ_EDIT_AKT."</h2><p align=\"center\">";
          EditInquiry();
          break;
     case "AcEditInquiry": AdminMenu();
          echo "<h2 align=\"center\">".RS_AKT_ROZ_EDIT_AKT."</h2><p align=\"center\">";
          AcEditInquiry();
          break;
     case "AcEdit2Inquiry": AdminMenu();
          echo "<h2 align=\"center\">".RS_AKT_ROZ_EDIT_AKT."</h2><p align=\"center\">";
          AcEdit2Inquiry();
          break;
     // nastaveni aktivni ankety
     case "NastInquiry": AdminMenu();
          echo "<h2 align=\"center\">".RS_AKT_ROZ_NASTAV_AKT."</h2><p align=\"center\">";
          NastInquiry();
          break;
     case "AcNastInquiry": AdminMenu();
          echo "<h2 align=\"center\">".RS_AKT_ROZ_NASTAV_AKT."</h2><p align=\"center\">";
          AcNastInquiry();
          break;
endswitch;
echo "</p>\n"; // zakonceni P tagu

// ---[pomocne fce - ankety]--------------------------------------------------------

function AnkNavigBox()
{
if (!isset($GLOBALS["prmin"])): $GLOBALS["prmin"]=0; endif;
if (!isset($GLOBALS["prmax"])): $GLOBALS["prmax"]=20; endif;

$dotazpocet=mysql_query("select count(ida) as pocet from ".$GLOBALS["rspredpona"]."ankety",$GLOBALS["dbspojeni"]);
if ($dotazpocet!=0):
  $pocet=mysql_Result($dotazpocet,0,"pocet");
else:
  $pocet=0;
endif;

echo "<form action=\"admin.php\" method=\"post\"><p align=\"center\" class=\"txt\">
<input type=\"hidden\" name=\"akce\" value=\"ShowInquiry\" /><input type=\"hidden\" name=\"modul\" value=\"ankety\" />
<input type=\"submit\" value=\" ".RS_AKT_SA_ZOBRAZ_ANKETU." \" class=\"tl\" />
".RS_AKT_SA_OD." <input type=\"text\" name=\"prmin\" value=\"".$GLOBALS["prmin"]."\" size=\"4\" class=\"textpole\">
".RS_AKT_SA_DO." <input type=\"text\" name=\"prmax\" value=\"".$GLOBALS["prmax"]."\" size=\"4\" class=\"textpole\">
(".RS_AKT_SA_CELK_POCET.": ".$pocet.")
</p></form>
<hr width=\"600\">
<p></p>\n";
}

function OptSezAnk($hledam = 0)
{
$str='';

$dotazpom=mysql_query("select ida,titulek from ".$GLOBALS["rspredpona"]."ankety order by titulek",$GLOBALS["dbspojeni"]);
$pocetpom=mysql_num_rows($dotazpom);

$str.="<option value=\"false\">-- žádná anketa není aktivní --</option>\n";
while($pole_data = mysql_fetch_assoc($dotazpom)):
  $str.="<option value=\"".$pole_data["ida"]."\"";
  if ($pole_data["ida"]==$hledam): $str.=" selected"; endif;
  $str.=">".$pole_data["titulek"]."</option>\n";
endwhile;

return $str;
}

function NactiKonfigHod($str = '')
{
$dotazhod=mysql_query("select idc,hodnota from ".$GLOBALS["rspredpona"]."config where promenna='".mysql_escape_string($str)."'",$GLOBALS["dbspojeni"]);
if (mysql_num_rows($dotazhod)==1):
  // promenna nactena
  $vysledek=mysql_fetch_row($dotazhod);
else:
  // promenna neexistuje
  $vysledek[0]=0;
  $vysledek[1]='';
endif;

return $vysledek; // pole: 0 = id promenne, 1 = hodnota promenne
}

// ---[hlavni fce - ankety]---------------------------------------------------------

/*
  ShowInquiry()
  AddInquiry()
  AcAddInquiry()
  DelInquiry()
  EditInquiry()
  AcEditInquiry()
  AcEdit2Inquiry()
  NastInquiry()
  AcNastInquiry()
*/

function ShowInquiry()
{
if (!isset($GLOBALS["prmin"])): $GLOBALS["prmin"]=0; endif;
if (!isset($GLOBALS["prmax"])): $GLOBALS["prmax"]=20; endif;

$autori=new SezAutori();

// navigacni box
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=NastInquiry&amp;modul=ankety\">".RS_AKT_SA_NASTAV_ANKETU."</a> -
<a href=\"".RS_VYKONNYSOUBOR."?akce=AddInquiry&amp;modul=ankety\">".RS_AKT_SA_PRIDAT_ANKETU."</a></p>\n";
AnkNavigBox();

// vypocet omezeni
if ($GLOBALS["prmin"]>0): $pruprmin=($GLOBALS["prmin"]-1); else: $pruprmin=0; endif;
$kolik=($GLOBALS["prmax"]-$pruprmin);
// dotaz
$dotazank=mysql_query("select * from ".$GLOBALS["rspredpona"]."ankety order by datum desc limit ".$pruprmin.",".$kolik,$GLOBALS["dbspojeni"]);
$pocetank=mysql_num_rows($dotazank);

if ($pocetank==0):
  // CHYBA: Zadaný interval (od xxx do yyy) je prázdný!
  echo "<p align=\"center\" class=\"txt\">".RS_ADM_INTERVAL_C1." ".$GLOBALS["prmin"]." ".RS_ADM_INTERVAL_C2." ".$GLOBALS["prmax"].RS_ADM_INTERVAL_C3."</p>\n";
else:
  echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">\n";
  echo "<tr bgcolor=\"#E6E6E6\" class=\"txt\"><td align=\"center\"><b>".RS_AKT_SA_TITULEK."</b></td>\n";
  echo "<td align=\"center\" width=\"300\"><b>".RS_AKT_SA_OTAZKA."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_AKT_SA_DATUM."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_AKT_SA_ZOBRAZIT."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_AKT_SA_UZAMCENO."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_AKT_SA_AUTOR."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_AKT_SA_AKCE."</b></td></tr>\n";
  while ($pole_data = mysql_fetch_assoc($dotazank)):
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    echo "<td align=\"left\">".$pole_data["titulek"]."</td>\n";
    echo "<td align=\"left\" width=\"300\">".$pole_data["otazka"]."</td>\n";
    echo "<td align=\"center\">".MyDatetimeToDate($pole_data["datum"])."</td>\n";
    echo "<td align=\"center\">".TestAnoNe($pole_data["zobrazit"])."</td>\n";
    echo "<td align=\"center\">".TestAnoNe($pole_data["uzavrena"])."</td>\n";
    echo "<td align=\"center\">".$autori->UkazUser($pole_data["kdo"])."</td>\n";
    echo "<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=EditInquiry&amp;modul=ankety&amp;prida=".$pole_data["ida"]."&amp;prmin=".$GLOBALS["prmin"]."&amp;prmax=".$GLOBALS["prmax"]."\">".RS_AKT_SA_UPRAVIT."</a> / ";
    echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=DelInquiry&amp;modul=ankety&amp;prida=".$pole_data["ida"]."&amp;prmin=".$GLOBALS["prmin"]."&amp;prmax=".$GLOBALS["prmax"]."\">".RS_AKT_SA_SMAZ."</a></td></tr>\n";
  endwhile;
  echo "</table>\n";
endif;

echo "<p></p>\n";
}

function AddInquiry()
{
// link
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowInquiry&amp;modul=ankety\">".RS_AKT_SA_ZPET."</a></p>\n";

// definice ankety
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy\" bgcolor=\"#E6E6E6\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_TITULEK."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prtitulek\" size=\"45\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=\"2\">".RS_AKT_SA_FORM_TITULEK_INFO."</td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=\"2\"><b>".RS_AKT_SA_FORM_OTAZKA."</b><br>
<textarea name=\"protazka\" rows=\"8\" cols=\"65\" class=\"textbox\">".RS_AKT_SA_FORM_OTAZKA_INFO."</textarea></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_ZOBRAZIT."</b></td>
<td align=\"left\"><input type=\"radio\" name=\"przobrazit\" value=\"1\" checked /> ".RS_TL_ANO." <input type=\"radio\" name=\"przobrazit\" value=\"0\" /> ".RS_TL_NE."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_STAV."</b></td>
<td align=\"left\"><input type=\"radio\" name=\"pruzamcena\" value=\"0\" checked /> ".RS_AKT_SA_FORM_STAV_OPEN." <input type=\"radio\" name=\"przobrazit\" value=\"1\" /> ".RS_AKT_SA_FORM_STAV_CLOSE."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_DATUM."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prdatum\" value=\"".Date("Y-m-d H:i:s")."\" size=\"25\" class=\"textpole\" /><br />".RS_AKT_SA_FORM_DATUM_INFO."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_AUTOR."</b></td>
<td align=\"left\"><select name=\"prautor\" size=\"1\">".OptAutori($GLOBALS["Uzivatel"]->IdUser)."</select></td></tr>
</table>\n";
// nadpis
echo "<p align=\"center\" class=\"txt\"><big><strong>".RS_AKT_SA_ADD_NADPIS_ODPOVEDI."</strong></big><br />".RS_AKT_SA_ADD_ODPOVEDI_INFO."</p>\n";
// definice anketnich odpovedi
echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy\" bgcolor=\"#E6E6E6\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_ODPOVED_CIS." 1</b></td>
<td align=\"left\"><input type=\"text\" name=\"prodpoved[]\" size=\"43\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_ODPOVED_CIS." 2</b></td>
<td align=\"left\"><input type=\"text\" name=\"prodpoved[]\" size=\"43\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_ODPOVED_CIS." 3</b></td>
<td align=\"left\"><input type=\"text\" name=\"prodpoved[]\" size=\"43\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_ODPOVED_CIS." 4</b></td>
<td align=\"left\"><input type=\"text\" name=\"prodpoved[]\" size=\"43\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_ODPOVED_CIS." 5</b></td>
<td align=\"left\"><input type=\"text\" name=\"prodpoved[]\" size=\"43\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_ODPOVED_CIS." 6</b></td>
<td align=\"left\"><input type=\"text\" name=\"prodpoved[]\" size=\"43\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_ODPOVED_CIS." 7</b></td>
<td align=\"left\"><input type=\"text\" name=\"prodpoved[]\" size=\"43\" class=\"textpole\"></td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcAddInquiry\" /><input type=\"hidden\" name=\"modul\" value=\"ankety\" />
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_PRIDAT." \" class=\"tl\" /> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\" /></p>
</form>
<p></p>\n";
}

function AcAddInquiry()
{
// korekce titulku na uvozovky
$GLOBALS["prtitulek"]=KorekceNadpisu($GLOBALS["prtitulek"]);
// bezpecnostni korekce
$GLOBALS["prtitulek"]=mysql_escape_string($GLOBALS["prtitulek"]);
$GLOBALS["protazka"]=mysql_escape_string($GLOBALS["protazka"]);
$GLOBALS["przobrazit"]=mysql_escape_string($GLOBALS["przobrazit"]);
$GLOBALS["pruzamcena"]=mysql_escape_string($GLOBALS["pruzamcena"]);
$GLOBALS["prdatum"]=mysql_escape_string($GLOBALS["prdatum"]);
$GLOBALS["prautor"]=mysql_escape_string($GLOBALS["prautor"]);

$dotaz="insert into ".$GLOBALS["rspredpona"]."ankety ";
$dotaz.="values(null,'".$GLOBALS["prtitulek"]."','".$GLOBALS["protazka"]."','".$GLOBALS["prdatum"]."','".$GLOBALS["prautor"]."','".$GLOBALS["przobrazit"]."',";
$dotaz.="'".$GLOBALS["pruzamcena"]."')";

@$prankety=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
if (!$prankety):
   echo "<p align=\"center\" class=\"txt\">Error Q1: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
   echo "<p align=\"center\" class=\"txt\">".RS_AKT_SA_OK_ADD_AKT."</p>\n"; // vse OK
endif;

// zjisteni id prave vlozene ankety
$dotaznaid=mysql_query("select ida from ".$GLOBALS["rspredpona"]."ankety where titulek='".$GLOBALS["prtitulek"]."' and datum='".$GLOBALS["prdatum"]."'",$GLOBALS["dbspojeni"]);
if (mysql_num_rows($dotaznaid)==1):
  $anketa_id=mysql_result($dotaznaid,0,"ida");
else:
  $anketa_id=0;
endif;

// vlozeni odpovedi
if (isset($GLOBALS["prodpoved"])&&$anketa_id>0):
  $pocet_odp=count($GLOBALS["prodpoved"]);
  for ($pom=0;$pom<$pocet_odp;$pom++):
    if (!empty($GLOBALS["prodpoved"][$pom])):
      $akt_odpoved=mysql_escape_string(trim($GLOBALS["prodpoved"][$pom]));
      mysql_query("insert into ".$GLOBALS["rspredpona"]."odpovedi values(null,".$anketa_id.",'".$akt_odpoved."',0)",$GLOBALS["dbspojeni"]);
    endif;
  endfor;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowInquiry&amp;modul=ankety\">".RS_AKT_SA_ZPET."</a></p>\n";
}

function DelInquiry()
{
// bezpecnostni korekce
$GLOBALS["prida"]=mysql_escape_string($GLOBALS["prida"]);

$chyba=0; // inic. chyby

// test na pouziti ankety
$dotazkontrola=mysql_query("select count(idc) as pocet from ".$GLOBALS["rspredpona"]."config where promenna='aktivni_anketa' and hodnota='".$GLOBALS["prida"]."'",$GLOBALS["dbspojeni"]);
if ($dotazkontrola!=0):
  if (mysql_result($dotazkontrola,0,"pocet")!=0):
    echo "<p align=\"center\" class=\"txt\">".RS_AKT_SA_ERR_AKTIVNI_AKT."</p>\n";
    echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=NastInquiry&amp;modul=ankety\">".RS_AKT_SA_DEAKTIV_ANKETU."</a></p>\n";
    $chyba=1;
  endif;
endif;

// test na existenci chyby
if ($chyba==0):
  // vymazani ankety
  @$smanketu=mysql_query("delete from ".$GLOBALS["rspredpona"]."ankety where ida='".$GLOBALS["prida"]."'",$GLOBALS["dbspojeni"]);
  if (!$smanketu):
    echo "<p align=\"center\" class=\"txt\">Error Q2: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_AKT_SA_OK_DEL_AKT."</p>\n"; // vse OK
  endif;
  // vymazani odpovedi
  @$smodpovedi=mysql_query("delete from ".$GLOBALS["rspredpona"]."odpovedi where anketa='".$GLOBALS["prida"]."'",$GLOBALS["dbspojeni"]);
  if (!$smodpovedi):
    echo "<p align=\"center\" class=\"txt\">Error Q3: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_AKT_SA_OK_DEL_DATA_AKT."</p>\n"; // vse OK
  endif;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowInquiry&amp;modul=ankety\">".RS_AKT_SA_ZPET."</a></p>\n";
}

function EditInquiry()
{
// bezpecnostni korekce
$GLOBALS["prida"]=mysql_escape_string($GLOBALS["prida"]);
$GLOBALS["prmin"]=mysql_escape_string($GLOBALS["prmin"]);
$GLOBALS["prmax"]=mysql_escape_string($GLOBALS["prmax"]);

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowInquiry&amp;modul=ankety&amp;prmin=".$GLOBALS["prmin"]."&amp;prmax=".$GLOBALS["prmax"]."\">".RS_AKT_SA_ZPET."</a></p>\n";

$dotazank=mysql_query("select * from ".$GLOBALS["rspredpona"]."ankety where ida='".$GLOBALS["prida"]."'",$GLOBALS["dbspojeni"]);
$pole_anketa=mysql_fetch_assoc($dotazank);

// uprava ankety
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy\" bgcolor=\"#E6E6E6\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_TITULEK."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prtitulek\" value=\"".$pole_anketa['titulek']."\" size=\"45\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=\"2\"><b>".RS_AKT_SA_FORM_OTAZKA."</b><br />
<textarea name=\"protazka\" rows=\"8\" cols=\"65\" class=\"textbox\">".KorekceHTML($pole_anketa['otazka'])."</textarea></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_ZOBRAZIT."</b></td>
<td align=\"left\">";
if ($pole_anketa['zobrazit']==1):
  echo "<input type=\"radio\" name=\"przobrazit\" value=\"1\" checked /> ".RS_TL_ANO." <input type=\"radio\" name=\"przobrazit\" value=\"0\" /> ".RS_TL_NE;
else:
  echo "<input type=\"radio\" name=\"przobrazit\" value=\"1\" /> ".RS_TL_ANO." <input type=\"radio\" name=\"przobrazit\" value=\"0\" checked /> ".RS_TL_NE;
endif;
echo "</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_STAV."</b></td>
<td align=\"left\">";
if ($pole_anketa['uzavrena']==0):
  echo "<input type=\"radio\" name=\"pruzamcena\" value=\"0\" checked /> ".RS_AKT_SA_FORM_STAV_OPEN." <input type=\"radio\" name=\"pruzamcena\" value=\"1\" /> ".RS_AKT_SA_FORM_STAV_CLOSE;
else:
  echo "<input type=\"radio\" name=\"pruzamcena\" value=\"0\" /> ".RS_AKT_SA_FORM_STAV_OPEN." <input type=\"radio\" name=\"pruzamcena\" value=\"1\" checked /> ".RS_AKT_SA_FORM_STAV_CLOSE;
endif;
echo "</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_DATUM."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prdatum\" value=\"".$pole_anketa['datum']."\" size=\"25\" class=\"textpole\" /><br />".RS_AKT_SA_FORM_DATUM_INFO."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_AUTOR."</b></td>
<td align=\"left\"><select name=\"prautor\" size=\"1\">".OptAutori($pole_anketa['kdo'])."</select></td></tr>
</table>
<input type=\"hidden\" name=\"prida\" value=\"".$pole_anketa['ida']."\" />
<input type=\"hidden\" name=\"akce\" value=\"AcEditInquiry\" /><input type=\"hidden\" name=\"modul\" value=\"ankety\" />
<input type=\"hidden\" name=\"prmin\" value=\"".$GLOBALS["prmin"]."\" /><input type=\"hidden\" name=\"prmax\" value=\"".$GLOBALS["prmax"]."\" />
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\" /> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\" /></p>
</form>\n";

// uprava anketnich odpovedi
echo "<p align=\"center\" class=\"txt\"><big><strong>".RS_AKT_SA_EDIT_NADPIS_ODPOVEDI."</strong></big><br />".RS_AKT_SA_EDIT_ODPOVEDI_INFO."</p>\n";

$dotazodp=mysql_query("select * from ".$GLOBALS["rspredpona"]."odpovedi where anketa='".$GLOBALS["prida"]."' order by ido",$GLOBALS["dbspojeni"]);
$pocetodp=mysql_num_rows($dotazodp);

echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy\" bgcolor=\"#E6E6E6\">\n";
for ($pom=0;$pom<$pocetodp;$pom++):
  $pole_odpovedi=mysql_fetch_assoc($dotazodp);
  echo "<tr class=\"txt\">\n";
  echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">\n";
  echo "<input type=\"hidden\" name=\"modul\" value=\"ankety\" />\n";
  echo "<input type=\"hidden\" name=\"akce\" value=\"AcEdit2Inquiry\" /><input type=\"hidden\" name=\"prido\" value=\"".$pole_odpovedi["ido"]."\" />\n";
  echo "<input type=\"hidden\" name=\"prmin\" value=\"".$GLOBALS["prmin"]."\" /><input type=\"hidden\" name=\"prmax\" value=\"".$GLOBALS["prmax"]."\" />\n";
  echo "<td align=\"left\"><input type=\"text\" name=\"prodp\" value=\"".$pole_odpovedi["odpoved"]."\" size=\"50\" class=\"textpole\"></td>\n";
  echo "<td align=\"left\"> ".RS_AKT_SA_FORM_POCET_HLA.": ".$pole_odpovedi["pocitadlo"]."</td>\n";
  echo "<td align=\"right\"><select name=\"prukol\" size=\"1\"><option value=\"save\">".RS_AKT_SA_FORM_ULOZ_ZMENY."</option><option value=\"del\">".RS_AKT_SA_FORM_VYMAZ_ODPOVED."</option></select> &nbsp;<input type=\"submit\" value=\" ".RS_TL_OK." \" class=\"tl\" /></td>\n";
  echo "</form></tr>\n";
endfor;

// pridani nove odpovedi
echo "<tr><form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<input type=\"hidden\" name=\"modul\" value=\"ankety\" /><input type=\"hidden\" name=\"prukol\" value=\"insert\" />
<input type=\"hidden\" name=\"akce\" value=\"AcEdit2Inquiry\" /><input type=\"hidden\" name=\"prida\" value=\"".$GLOBALS["prida"]."\" />
<input type=\"hidden\" name=\"prmin\" value=\"".$GLOBALS["prmin"]."\" /><input type=\"hidden\" name=\"prmax\" value=\"".$GLOBALS["prmax"]."\" />
<td align=\"left\" colspan=\"2\"><input type=\"text\" name=\"prodp\" value=\"".RS_AKT_SA_FORM_VLOZ_INFO."\" size=\"50\" class=\"textpole\" /></td>
<td align=\"right\"> &nbsp;<input type=\"submit\" value=\" ".RS_AKT_SA_TL_VLOZ_ODPOVED." \" class=\"tl\" /></td>
</form></tr>
</table>
<p></p>\n";
}

function AcEditInquiry()
{
// korekce titulku na uvozovky
$GLOBALS["prtitulek"]=KorekceNadpisu($GLOBALS["prtitulek"]);
// bezpecnostni korekce
$GLOBALS["prida"]=mysql_escape_string($GLOBALS["prida"]);
$GLOBALS["prtitulek"]=mysql_escape_string($GLOBALS["prtitulek"]);
$GLOBALS["protazka"]=mysql_escape_string($GLOBALS["protazka"]);
$GLOBALS["przobrazit"]=mysql_escape_string($GLOBALS["przobrazit"]);
$GLOBALS["pruzamcena"]=mysql_escape_string($GLOBALS["pruzamcena"]);
$GLOBALS["prdatum"]=mysql_escape_string($GLOBALS["prdatum"]);
$GLOBALS["prautor"]=mysql_escape_string($GLOBALS["prautor"]);

$dotaz="update ".$GLOBALS["rspredpona"]."ankety ";
$dotaz.="set titulek='".$GLOBALS["prtitulek"]."',otazka='".$GLOBALS["protazka"]."',datum='".$GLOBALS["prdatum"]."',kdo='".$GLOBALS["prautor"]."',";
$dotaz.="zobrazit='".$GLOBALS["przobrazit"]."',uzavrena='".$GLOBALS["pruzamcena"]."' ";
$dotaz.="where ida='".$GLOBALS["prida"]."'";

@$error=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
if (!$error):
  echo "<p align=\"center\" class=\"txt\">Error Q4: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_AKT_SA_OK_EDIT_AKT."</p>\n";
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowInquiry&amp;modul=ankety&amp;prmin=".$GLOBALS["prmin"]."&amp;prmax=".$GLOBALS["prmax"]."\">".RS_AKT_SA_ZPET."</a></p>\n";
}

function AcEdit2Inquiry()
{
// rozcestnik ukolu
switch ($GLOBALS["prukol"]):
  case "save":
       // bezpecnostni korekce
       $GLOBALS["prido"]=mysql_escape_string($GLOBALS["prido"]);
       $GLOBALS["prodp"]=mysql_escape_string($GLOBALS["prodp"]);
       // dotaz
       @$error=mysql_query("update ".$GLOBALS["rspredpona"]."odpovedi set odpoved='".$GLOBALS["prodp"]."' where ido='".$GLOBALS["prido"]."'",$GLOBALS["dbspojeni"]);
       if (!$error):
         echo "<p align=\"center\" class=\"txt\">Error Q5: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
       else:
         echo "<p align=\"center\" class=\"txt\">".RS_AKT_SA_OK_ODP_EDIT_AKT."</p>\n"; // vse OK
       endif;
       break;
  case "del":
       // bezpecnostni korekce
       $GLOBALS["prido"]=mysql_escape_string($GLOBALS["prido"]);
       // dotaz
       @$error=mysql_query("delete from ".$GLOBALS["rspredpona"]."odpovedi where ido='".$GLOBALS["prido"]."'",$GLOBALS["dbspojeni"]);
       if (!$error):
         echo "<p align=\"center\" class=\"txt\">Error Q6: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
       else:
         echo "<p align=\"center\" class=\"txt\">".RS_AKT_SA_OK_ODP_DEL_AKT."</p>\n"; // vse OK
       endif;
       break;
  case "insert":
       // bezpecnostni korekce
       $GLOBALS["prida"]=mysql_escape_string($GLOBALS["prida"]);
       $GLOBALS["prodp"]=mysql_escape_string($GLOBALS["prodp"]);
       // dotaz
       @$error=mysql_query("insert into ".$GLOBALS["rspredpona"]."odpovedi values(null,'".$GLOBALS["prida"]."','".$GLOBALS["prodp"]."',0)",$GLOBALS["dbspojeni"]);
       if (!$error):
         echo "<p align=\"center\" class=\"txt\">Error Q7: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
       else:
         echo "<p align=\"center\" class=\"txt\">".RS_AKT_SA_OK_ODP_ADD_AKT."</p>\n"; // vse OK
       endif;
       break;
endswitch;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowInquiry&amp;modul=ankety&amp;prmin=".$GLOBALS["prmin"]."&amp;prmax=".$GLOBALS["prmax"]."\">".RS_AKT_SA_ZPET."</a></p>\n";
}

function NastInquiry()
{
// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowInquiry&amp;modul=ankety\">".RS_AKT_SA_ZPET."</a></p>\n";

// inic. promenne aktivni_anketa
$konf_aktanket=NactiKonfigHod("aktivni_anketa");

// zobrazeni promenne aktivni_anketa
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<div align=\"center\">
<strong>".RS_AKT_SA_FORM_AKTIV_AKT.":</strong> <select name=\"prhodnota\" size=\"1\">".OptSezAnk($konf_aktanket[1])."</select>
<input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\" />
<input type=\"hidden\" name=\"akce\" value=\"AcNastInquiry\" /><input type=\"hidden\" name=\"modul\" value=\"ankety\" />
<input type=\"hidden\" name=\"pridc\" value=\"".$konf_aktanket[0]."\" />
</div>
</form>\n";
}

function AcNastInquiry()
{
$GLOBALS["pridc"]=mysql_escape_string($GLOBALS["pridc"]);
$GLOBALS["prhodnota"]=mysql_escape_string($GLOBALS["prhodnota"]);

@$error=mysql_query("update ".$GLOBALS["rspredpona"]."config set hodnota='".$GLOBALS["prhodnota"]."' where idc='".$GLOBALS["pridc"]."'",$GLOBALS["dbspojeni"]);
if (!$error):
  echo "<p align=\"center\" class=\"txt\">Error Q8: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_AKT_SA_OK_NASTAV_AKT."</p>\n"; // vse OK
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowInquiry&amp;modul=ankety\">".RS_AKT_SA_ZPET."</a></p>\n";
}
?>
