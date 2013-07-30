<?

######################################################################
# phpRS Administration Engine - Download's section 1.5.2
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_download, rs_download_sekce

/*
  Tento soubor zajistuje obsluhu download sekce.
*/

if ($Uzivatel->StavSession!=1):
  echo "<html><body><div align=\"center\">Tento soubor neni urcen k vnejsimu spousteni!</div></body></html>";
  exit;
endif;

// ---[rozcestnik]------------------------------------------------------------------
switch($GLOBALS['akce']):
     // download
     case "ShowFile": AdminMenu();
          echo "<h2 align=\"center\">".RS_FIL_ROZ_SHOW_FILES."</h2><p align=\"center\">";
          ShowFile();
          break;
     case "AddFile": AdminMenu();
          echo "<h2 align=\"center\">".RS_FIL_ROZ_ADD_FILES."</h2><p align=\"center\">";
          AddFile();
          break;
     case "AcAddFile": AdminMenu();
          echo "<h2 align=\"center\">".RS_FIL_ROZ_ADD_FILES."</h2><p align=\"center\">";
          AcAddFile();
          break;
     case "DelFile": AdminMenu();
          echo "<h2 align=\"center\">".RS_FIL_ROZ_DEL_FILES."</h2><p align=\"center\">";
          DelFile();
          break;
     case "EditFile": AdminMenu();
          echo "<h2 align=\"center\">".RS_FIL_ROZ_EDIT_FILES."</h2><p align=\"center\">";
          EditFile();
          break;
     case "AcEditFile": AdminMenu();
          echo "<h2 align=\"center\">".RS_FIL_ROZ_EDIT_FILES."</h2><p align=\"center\">";
          AcEditFile();
          break;
     // download sekce
     case "ShowFileSek": AdminMenu();
          echo "<h2 align=\"center\">".RS_FIL_ROZ_SEKCE_FILES."</h2><p align=\"center\">";
          ShowFileSek();
          break;
     case "AcAddFileSek": AdminMenu();
          echo "<h2 align=\"center\">".RS_FIL_ROZ_SEKCE_FILES."</h2><p align=\"center\">";
          AcAddFileSek();
          break;
     case "NastFileSek": AdminMenu();
          echo "<h2 align=\"center\">".RS_FIL_ROZ_SEKCE_FILES."</h2><p align=\"center\">";
          NastFileSek();
          break;
     case "DelFileSek": AdminMenu();
          echo "<h2 align=\"center\">".RS_FIL_ROZ_SEKCE_FILES."</h2><p align=\"center\">";
          DelFileSek();
          break;
     case "EditFileSek": AdminMenu();
          echo "<h2 align=\"center\">".RS_FIL_ROZ_SEKCE_FILES."</h2><p align=\"center\">";
          EditFileSek();
          break;
     case "AcEditFileSek": AdminMenu();
          echo "<h2 align=\"center\">".RS_FIL_ROZ_SEKCE_FILES."</h2><p align=\"center\">";
          AcEditFileSek();
          break;
     // informacni mail
     case "InfoDownDopis": AdminMenu();
          echo "<h2 align=\"center\">".RS_FIL_ROZ_POSTA_FILES."</h2><p align=\"center\">";
          InfoDownDopis();
          break;
     case "AcInfoDownDopis": AdminMenu();
          echo "<h2 align=\"center\">".RS_FIL_ROZ_POSTA_FILES."</h2><p align=\"center\">";
          include_once('admin/astdlib_mail.php'); // vlozeni STD. MAIL LIBRARY
          AcInfoDownDopis();
          break;
endswitch;
echo "</p>\n"; // zakonceni P tagu

// ---[download - pomocne fce]------------------------------------------------------

function OptDwnSek($hledam = 0)
{
$vysl='';

$dotazsek=mysql_query("select ids,nazev from ".$GLOBALS["rspredpona"]."download_sekce order by nazev",$GLOBALS["dbspojeni"]);
$pocetsek=mysql_num_rows($dotazsek);
if ($pocetsek==0):
  $vysl.="<option value=\"0\">".RS_FIL_DS_ERR_ZADNA_SEKCE."</option>\n";
else:
  while ($pole_data = mysql_fetch_assoc($dotazsek)):
    $vysl.="<option value=\"".$pole_data["ids"]."\"";
    if ($pole_data["ids"]==$hledam): $vysl.=" selected"; endif;
    $vysl.=">".$pole_data["nazev"]."</option>\n";
  endwhile;
endif;

return $vysl;
}

// ---[download - hlavni fce]-------------------------------------------------------

/*
  ShowFile()
  AddFile()
  AcAddFile()
  DelFile()
  EditFile()
  AcEditFile()
*/

function ShowFile()
{
// test na existenci nastaveni filtru
if (!isset($GLOBALS['prfilter'])): $GLOBALS['prfilter']='id'; endif;

switch($GLOBALS['prfilter']):
  case "id": $prorderby=" order by d.idd desc"; break;
  case "nazev": $prorderby=" order by d.nazev"; break;
  case "soubor": $prorderby=" order by d.fjmeno"; break;
  case "datum": $prorderby=" order by d.datum desc"; break;
  case "pocet": $prorderby=" order by d.pocitadlo desc"; break;
  case "sekce": $prorderby=" order by s.nazev,d.datum desc"; break;
  default: $prorderby=" order by d.idd desc"; break;
endswitch;

// linky
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=AddFile&amp;modul=files\">".RS_FIL_SS_PRIDAT_FILES."</a> -
<a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFileSek&amp;modul=files\">".RS_FIL_SS_SPRAVA_SEKCI."</a></p>\n";

// sestaveni dotazu
$dotaz="select d.idd,d.nazev,d.furl,d.fjmeno,d.fsize,date_format(d.datum,'%d. %m. %Y') as updatum,d.verze,d.pocitadlo,s.nazev as jmenosek ";
$dotaz.="from ".$GLOBALS['rspredpona']."download as d,".$GLOBALS['rspredpona']."download_sekce as s ";
$dotaz.="where d.idsekce=s.ids".$prorderby;
// dotaz na DB
$dotazsoubory=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetsoubory=mysql_num_rows($dotazsoubory);
// vypis existujich souboru
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" width=\"96%\" class=\"ramsedy\">
<tr class=\"txt\" bgcolor=\"#E6E6E6\">
<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files&amp;prfilter=nazev\" class=\"zahlavi\"><b>".RS_FIL_SS_NAZEV."</b></a></td>
<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files&amp;prfilter=soubor\" class=\"zahlavi\"><b>".RS_FIL_SS_SOUBOR."</b></a></td>
<td align=\"center\"><b>".RS_FIL_SS_VELIKOST."</b></td>
<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files&amp;prfilter=datum\" class=\"zahlavi\"><b>".RS_FIL_SS_DATUM."</b></a></td>
<td align=\"center\"><b>".RS_FIL_SS_VERZE."</b></td>
<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files&amp;prfilter=pocet\" class=\"zahlavi\"><b>".RS_FIL_SS_POCET_STAZ."</b></a></td>
<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files&amp;prfilter=sekce\" class=\"zahlavi\"><b>".RS_FIL_SS_SEKCE."</b></a></td>
<td align=\"center\"><b>".RS_FIL_SS_AKCE."</b></td>
<td align=\"center\"><b>".RS_FIL_SS_SMAZ."</b></td></tr>\n";
if ($pocetsoubory==0):
  echo "<tr class=\"txt\"><td colspan=\"9\" align=\"center\"><b>".RS_FIL_SS_ZADNY_FILES."</b></td></tr>\n";
else:
  while ($pole_data = mysql_fetch_assoc($dotazsoubory)):
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    echo "<td align=\"center\">".$pole_data["nazev"]."</td>\n";
    echo "<td align=\"center\"><a href=\"".$pole_data["furl"]."\" target=\"_blank\">".$pole_data["fjmeno"]."</a></td>\n";
    echo "<td align=\"center\">".$pole_data["fsize"]."</td>\n";
    echo "<td align=\"center\">".$pole_data["updatum"]."</td>\n";
    echo "<td align=\"center\">".TestNaNic($pole_data["verze"])."</td>\n";
    echo "<td align=\"center\">".$pole_data["pocitadlo"]."</td>\n";
    echo "<td align=\"center\">".$pole_data["jmenosek"]."</td>\n";
    echo "<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=EditFile&amp;modul=files&amp;pridd=".$pole_data["idd"]."\">".RS_FIL_SS_UPRAVIT."</a></td>";
    echo "<td align=\"center\"><input type=\"checkbox\" name=\"prpoleid[]\" value=\"".$pole_data["idd"]."\" /></td></tr>\n";
  endwhile;
  echo "<tr class=\"txt\"><td align=\"right\" colspan=\"9\"><input type=\"submit\" value=\" ".RS_FIL_SS_SMAZ_OZNAC." \" class=\"tl\" /></td></tr>\n";
endif;
echo "</table>
<input type=\"hidden\" name=\"akce\" value=\"DelFile\" /><input type=\"hidden\" name=\"modul\" value=\"files\" />
</form>
<p></p>\n";
}

function AddFile()
{
// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files\">".RS_FIL_SS_ZPET."</a> -
<a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFileSek&amp;modul=files\">".RS_FIL_SS_SPRAVA_SEKCI."</a></p>\n";

$pom_zdroj_adresa=$GLOBALS['baseadr'].'storage/'; // pomocna zdrojova adresa

// formular
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy\" bgcolor=\"#E6E6E6\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_NAZEV."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prnazev\" size=\"50\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_SEKCE."</b></td>
<td align=\"left\"><select name=\"prsekce\" size=\"1\">".OptDwnSek(0)."</select></td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=\"2\"><b>".RS_FIL_SS_FORM_OBSAH."</b><br />
<textarea name=\"prkomentar\" rows=\"6\" cols=\"75\" class=\"textbox\">".RS_FIL_SS_FORM_OBSAH_INFO."</textarea></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_URL_SB."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prurl\" value=\"".$pom_zdroj_adresa."\" size=\"50\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_JMENO_SB."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prjmeno\" size=\"50\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_VELIKOST."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prsize\" value=\"kB\" size=\"10\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_JMENO_ZDROJ." <sup>*</sup></b></td>
<td align=\"left\"><input type=\"text\" name=\"przdrojjm\" value=\"-\" size=\"50\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_ADR_ZDROJ." <sup>*</sup></b></td>
<td align=\"left\"><input type=\"text\" name=\"przdrojadr\" value=\"http:// nebo mailto:\" size=\"50\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_VERZE."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prverze\" size=\"10\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_SLOVNI_KAT."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prkat\" value=\"slovní definice\" size=\"50\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_ZOBRAZIT."</b></td>
<td align=\"left\"><input type=\"radio\" name=\"przobrazit\" value=\"1\" checked /> ".RS_TL_ANO." &nbsp; <input type=\"radio\" name=\"przobrazit\" value=\"0\" /> ".RS_TL_NE."</td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcAddFile\" /><input type=\"hidden\" name=\"modul\" value=\"files\" />
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_PRIDAT." \" class=\"tl\" /> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \"  class=\"tl\" /></p>
</form>
<p align=\"center\" class=\"txt\"><sup>*</sup> ".RS_FIL_SS_ZADNY_ZDROJ_INFO."</p>
<p></p>\n";
}

function AcAddFile()
{
// bezpecnostni korekce
$_POST['prnazev']=mysql_escape_string($_POST['prnazev']);
$_POST['prkomentar']=mysql_escape_string($_POST['prkomentar']);
$_POST['prurl']=mysql_escape_string($_POST['prurl']);
$_POST['prjmeno']=mysql_escape_string($_POST['prjmeno']);
$_POST['prsize']=mysql_escape_string($_POST['prsize']);
$_POST['przdrojjm']=mysql_escape_string($_POST['przdrojjm']);
$_POST['przdrojadr']=mysql_escape_string($_POST['przdrojadr']);
$_POST['prverze']=mysql_escape_string($_POST['prverze']);
$_POST['prkat']=mysql_escape_string($_POST['prkat']);
$_POST['przobrazit']=mysql_escape_string($_POST['przobrazit']);
$_POST['prsekce']=mysql_escape_string($_POST['prsekce']);

$dnesnidatum=Date("Y-m-d H:i:s");

// sestaveni dotazu
$dotaz="insert into ".$GLOBALS['rspredpona']."download values ";
$dotaz.="(null,'".$_POST['prnazev']."','".$_POST['prkomentar']."','".$_POST['prurl']."','".$_POST['prjmeno']."','".$_POST['prsize']."','".$_POST['przdrojjm']."',";
$dotaz.="'".$_POST['przdrojadr']."','".$dnesnidatum."','".$_POST['prverze']."','".$_POST['prkat']."',0,'".$_POST['przobrazit']."','".$_POST['prsekce']."')";
// pridani polozky
@$error=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
if (!$error):
  echo "<p align=\"center\" class=\"txt\">Error D1: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else:
  $cislosouboru=mysql_insert_id();
  echo "<p align=\"center\" class=\"txt\">".RS_FIL_SS_OK_ADD_FILES."</p>\n";
  echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=InfoDownDopis&amp;modul=files&amp;cislosouboru=".$cislosouboru."\">".RS_FIL_SS_ODESLAT_MAIL."</a></p>\n";
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files\">".RS_FIL_SS_ZPET."</a></p>\n";
}

function DelFile()
{
$chyba=0; // inic. chyby

if (!isset($GLOBALS["prpoleid"])): // test na prazdny vyber
  echo "<p align=\"center\" class=\"txt\">".RS_FIL_SS_DEL_POCET_NULA."</p>\n";
  $chyba=1;
else:
  $pocet_id=count($GLOBALS["prpoleid"]); // pocet prvku v poli
  for ($pom=0;$pom<$pocet_id;$pom++):
    @$error=mysql_query("delete from ".$GLOBALS["rspredpona"]."download where idd=".mysql_escape_string($GLOBALS["prpoleid"][$pom]),$GLOBALS["dbspojeni"]);
    if (!$error):
      echo "<p align=\"center\" class=\"txt\">Error D2: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
      $chyba=1;
    endif;
  endfor;
endif;

// globalni vysledek
if ($chyba==0):
  if ($pocet_id>1): // mnoz. cislo
    echo "<p align=\"center\" class=\"txt\">".RS_FIL_SS_OK_DEL_FILES_VICE."</p>\n";
  else: // jednotne cislo
    echo "<p align=\"center\" class=\"txt\">".RS_FIL_SS_OK_DEL_FILES."</p>\n";
  endif;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files\">".RS_FIL_SS_ZPET."</a></p>\n";
}

function EditFile()
{
// bezpecnostni korekce
$GLOBALS["pridd"]=mysql_escape_string($GLOBALS["pridd"]);

// link
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files\">".RS_FIL_SS_ZPET."</a></p>\n";

// dotaz na data
$dotazsoubor=mysql_query("select * from ".$GLOBALS["rspredpona"]."download where idd=".$GLOBALS["pridd"],$GLOBALS["dbspojeni"]);
$pole_data=mysql_fetch_assoc($dotazsoubor);
// editacni formular
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy\" bgcolor=\"#E6E6E6\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_NAZEV."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prnazev\" value=\"".$pole_data["nazev"]."\" size=\"50\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_SEKCE."</b></td>
<td align=\"left\"><select name=\"prsekce\" size=\"1\">".OptDwnSek($pole_data["idsekce"])."</select></td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=\"2\"><b>".RS_FIL_SS_FORM_OBSAH."</b><br />
<textarea name=\"prkomentar\" rows=\"6\" cols=\"75\" class=\"textbox\">".KorekceHTML($pole_data["komentar"])."</textarea></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_URL_SB."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prurl\" value=\"".$pole_data["furl"]."\" size=\"50\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_JMENO_SB."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prjmeno\" value=\"".$pole_data["fjmeno"]."\" size=\"50\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_VELIKOST."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prsize\" value=\"".$pole_data["fsize"]."\" size=\"10\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_JMENO_ZDROJ." <sup>*</sup></b></td>
<td align=\"left\"><input type=\"text\" name=\"przdrojjm\" value=\"".$pole_data["zdroj_jm"]."\" size=\"50\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_ADR_ZDROJ." <sup>*</sup></b></td>
<td align=\"left\"><input type=\"text\" name=\"przdrojadr\" value=\"".$pole_data["zdroj_adr"]."\" size=\"50\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_DATUM."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prdatum\" value=\"".$pole_data["datum"]."\" size=\"50\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_VERZE."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prverze\" value=\"".$pole_data["verze"]."\" size=\"10\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_SLOVNI_KAT."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prkat\" value=\"".$pole_data["kat"]."\" size=\"50\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_POCET."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prpocitadlo\" value=\"".$pole_data["pocitadlo"]."\" size=\"10\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_ZOBRAZIT."</b></td>
<td align=\"left\">";
if ($pole_data["zobraz"]==1):
  echo "<input type=\"radio\" name=\"przobrazit\" value=\"1\" checked /> ".RS_TL_ANO." &nbsp; <input type=\"radio\" name=\"przobrazit\" value=\"0\" /> ".RS_TL_NE;
else:
  echo "<input type=\"radio\" name=\"przobrazit\" value=\"1\" /> ".RS_TL_ANO." &nbsp; <input type=\"radio\" name=\"przobrazit\" value=\"0\" checked /> ".RS_TL_NE;
endif;
echo "</td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcEditFile\" /><input type=\"hidden\" name=\"modul\" value=\"files\" />
<input type=\"hidden\" name=\"pridd\" value=\"".$pole_data["idd"]."\" />
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\" /> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\" /></p>
</form>
<p align=\"center\" class=\"txt\"><sup>*</sup> ".RS_FIL_SS_ZADNY_ZDROJ_INFO."</p>
<p></p>\n";
}

function AcEditFile()
{
// bezpecnostni korekce
$_POST["pridd"]=mysql_escape_string($_POST["pridd"]);
$_POST['prnazev']=mysql_escape_string($_POST['prnazev']);
$_POST['prkomentar']=mysql_escape_string($_POST['prkomentar']);
$_POST['prurl']=mysql_escape_string($_POST['prurl']);
$_POST['prjmeno']=mysql_escape_string($_POST['prjmeno']);
$_POST['prsize']=mysql_escape_string($_POST['prsize']);
$_POST['przdrojjm']=mysql_escape_string($_POST['przdrojjm']);
$_POST['przdrojadr']=mysql_escape_string($_POST['przdrojadr']);
$_POST['prverze']=mysql_escape_string($_POST['prverze']);
$_POST['prkat']=mysql_escape_string($_POST['prkat']);
$_POST['przobrazit']=mysql_escape_string($_POST['przobrazit']);
$_POST['prsekce']=mysql_escape_string($_POST['prsekce']);

// kontrola datumu
if (isset($_POST["prdatum"])):
  $upr_datum=OverDatum($_POST["prdatum"]);
else:
  $upr_datum=date("Y-m-d H:i:s");
endif;

// uprava polozky
$dotaz="update ".$GLOBALS["rspredpona"]."download set ";
$dotaz.="nazev='".$_POST["prnazev"]."', komentar='".$_POST["prkomentar"]."', furl='".$_POST["prurl"]."', fjmeno='".$_POST["prjmeno"]."', ";
$dotaz.="fsize='".$_POST["prsize"]."', zdroj_jm='".$_POST["przdrojjm"]."', zdroj_adr='".$_POST["przdrojadr"]."', datum='".$upr_datum."', ";
$dotaz.="verze='".$_POST["prverze"]."', kat='".$_POST["prkat"]."', pocitadlo='".$_POST["prpocitadlo"]."', zobraz='".$_POST["przobrazit"]."', ";
$dotaz.="idsekce='".$_POST["prsekce"]."' ";
$dotaz.="where idd=".$_POST["pridd"];

@$error=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
if (!$error):
  echo "<p align=\"center\">Error D3: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_FIL_SS_OK_EDIT_FILES."</p>\n";
  echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=InfoDownDopis&amp;modul=files&amp;cislosouboru=".$GLOBALS["pridd"]."\">".RS_FIL_SS_ODESLAT_MAIL."</a></p>\n";
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files\">".RS_FIL_SS_ZPET."</a></p>\n";
}

// -----[download sekce - hlavni fce]---------------------------------------------------------------------------

/*
  ShowFileSek()
  AcAddFileSek()
  NastFileSek()
  DelFileSek()
  EditFileSek()
  AcEditFileSek()
*/

function ShowFileSek()
{
// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files\">".RS_FIL_SS_ZPET."</a></p>\n";

// dotaz na data
$dotazsek=mysql_query("select ids,nazev,hlavnisekce from ".$GLOBALS["rspredpona"]."download_sekce order by nazev",$GLOBALS["dbspojeni"]);
$pocetsek=mysql_num_rows($dotazsek);

// vypis download sekci
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">
<tr class=\"txt\" bgcolor=\"#E6E6E6\"><td align=\"center\"><b>".RS_FIL_DS_NAZEV."</b></td>
<td align=\"center\"><b>".RS_FIL_DS_HL_SEKCE."</b></td>
<td align=\"center\"><b>".RS_FIL_DS_AKCE."</b></td></tr>\n";
if ($pocetsek==0):
  echo "<tr class=\"txt\"><td align=\"center\" colspan=\"3\">".RS_FIL_DS_ZADNA_SEKCE."</td></tr>\n";
else:
  while ($pole_data = mysql_fetch_assoc($dotazsek)):
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    echo "<td align=\"left\">".$pole_data["nazev"]."</td>\n";
    echo "<td align=\"center\"><input type=\"radio\" name=\"prids\" value=\"".$pole_data["ids"]."\"";
    if ($pole_data["hlavnisekce"]==1): echo " checked"; endif; // test na hlavni sekci
    echo " /></td>\n";
    echo "<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=EditFileSek&amp;modul=files&amp;prids=".$pole_data["ids"]."\">".RS_FIL_DS_UPRAVIT."</a> / ";
    echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=DelFileSek&amp;modul=files&amp;prids=".$pole_data["ids"]."\">".RS_FIL_DS_SMAZ."</a></td></tr>\n";
  endwhile;
  echo "<tr class=\"txt\"><td align=\"right\" colspan=\"3\"><input type=\"submit\" value=\" ".RS_FIL_DS_NASTAV_HL_SEKCI." \" class=\"tl\" /></td></tr>\n";
endif;
echo "</table>
<input type=\"hidden\" name=\"akce\" value=\"NastFileSek\" /><input type=\"hidden\" name=\"modul\" value=\"files\" />
</form>\n";

// upozorneni
echo "<p align=\"center\" class=\"txt\">".RS_FIL_DS_NASTAV_INFO."</p>\n";

// nadpis
echo "<p align=\"center\" class=\"txt\"><big><strong>".RS_FIL_DS_NADPIS_ADD."</strong></big></p>\n";
// formular na pridani nove sekce
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy\" bgcolor=\"#E6E6E6\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_DS_FORM_NAZEV."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prnazev\" value=\"\" size=\"40\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_DS_FORM_HL_SEKCE."</b></td>
<td align=\"left\"><input type=\"radio\" name=\"prhlavni\" value=\"1\" checked /> ".RS_TL_ANO." &nbsp; <input type=\"radio\" name=\"prhlavni\" value=\"0\" /> ".RS_TL_NE."</td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcAddFileSek\" /><input type=\"hidden\" name=\"modul\" value=\"files\" />
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_PRIDAT." \" class=\"tl\" /> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\" /></p>
</form>\n";
}

function AcAddFileSek()
{
$GLOBALS["prnazev"]=KorekceNadpisu($GLOBALS["prnazev"]);
// bezpecnostni korekce
$GLOBALS["prnazev"]=mysql_escape_string($GLOBALS["prnazev"]);
$GLOBALS["prhlavni"]=mysql_escape_string($GLOBALS["prhlavni"]);

// zmena nastaveni hl. sekce
if ($GLOBALS["prhlavni"]==1):
  @mysql_query("update ".$GLOBALS["rspredpona"]."download_sekce set hlavnisekce='0'",$GLOBALS["dbspojeni"]);
endif;
// pridani nove polozky
@$error=mysql_query("insert into ".$GLOBALS["rspredpona"]."download_sekce values(null,'".$GLOBALS["prnazev"]."','".$GLOBALS["prhlavni"]."')",$GLOBALS["dbspojeni"]);
if (!$error):
  echo "<p align=\"center\" class=\"txt\">Error D4: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_FIL_DS_OK_ADD_SEKCE."</p>\n";
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFileSek&amp;modul=files\">".RS_FIL_DS_ZPET_SEKCE."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files\">".RS_FIL_SS_ZPET."</a></p>\n";
}

function NastFileSek()
{
// bezpecnostni korekce
$GLOBALS["prids"]=mysql_escape_string($GLOBALS["prids"]);

$chyba=0; // inic. chyby

@$error=mysql_query("update ".$GLOBALS["rspredpona"]."download_sekce set hlavnisekce='0'",$GLOBALS["dbspojeni"]);
if (!$error): $chyba=1; endif;
@$error=mysql_query("update ".$GLOBALS["rspredpona"]."download_sekce set hlavnisekce='1' where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
if (!$error): $chyba=1; endif;

if ($chyba==1): // chyba
  echo "<p align=\"center\" class=\"txt\">Error D5: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else: // vse OK
  echo "<p align=\"center\" class=\"txt\">".RS_FIL_DS_OK_NASTAV_SEKCE."</p>\n";
endif;

// navrat
ShowFileSek();
}

function DelFileSek()
{
// bezpecnostni korekce
$GLOBALS["prids"]=mysql_escape_string($GLOBALS["prids"]);

$dotazpocradku=mysql_query("select count(idd) as pocet from ".$GLOBALS["rspredpona"]."download where idsekce='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
$pocetpocradku=mysql_result($dotazpocradku,0,"pocet"); // pocet souboru nalezicich do $prids sekce

if ($pocetpocradku>0):
  // chyba - sekce neni prazdna
  echo "<p align=\"center\" class=\"txt\">Error D6: ".RS_FIL_DS_ERR_PLNA_SEKCE."</p>\n";
else:
  // neexistuje zadny odpovidajici soubor
  @$error=mysql_query("delete from ".$GLOBALS["rspredpona"]."download_sekce where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
  if (!$error):
    echo "<p align=\"center\" class=\"txt\">Error D7: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_FIL_DS_OK_DEL_SEKCE."</p>\n";
  endif;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFileSek&amp;modul=files\">".RS_FIL_DS_ZPET_SEKCE."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files\">".RS_FIL_SS_ZPET."</a></p>\n";
}

function EditFileSek()
{
// bezpecnostni korekce
$GLOBALS["prids"]=mysql_escape_string($GLOBALS["prids"]);

// linky
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFileSek&amp;modul=files\">".RS_FIL_DS_ZPET_SEKCE."</a></p>\n";

$dotazsek=mysql_query("select ids,nazev from ".$GLOBALS["rspredpona"]."download_sekce where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
$pole_data=mysql_fetch_assoc($dotazsek);

// formular na upravu sekce
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy\" bgcolor=\"#E6E6E6\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_DS_NAZEV."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prnazev\" value=\"".$pole_data['nazev']."\" size=\"40\" class=\"textpole\" /></td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcEditFileSek\" /><input type=\"hidden\" name=\"modul\" value=\"files\" />
<input type=\"hidden\" name=\"prids\" value=\"".$pole_data['ids']."\" />
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\" /> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\" /></p>
</form>\n";
}

function AcEditFileSek()
{
$GLOBALS["prnazev"]=KorekceNadpisu($GLOBALS["prnazev"]);
// bezpecnostni korekce
$GLOBALS["prids"]=mysql_escape_string($GLOBALS["prids"]);
$GLOBALS["prnazev"]=mysql_escape_string($GLOBALS["prnazev"]);

@$error=mysql_query("update ".$GLOBALS["rspredpona"]."download_sekce set nazev='".$GLOBALS["prnazev"]."' where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
if (!$error):
  echo "<p align=\"center\" class=\"txt\">Error D8: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_FIL_DS_OK_EDIT_SEKCE."</p>\n";
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFileSek&amp;modul=files\">".RS_FIL_DS_ZPET_SEKCE."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files\">".RS_FIL_SS_ZPET."</a></p>\n";
}

// ---[download - posta]------------------------------------------------------------

/*
  InfoDownDopis()
  AcInfoDownDopis()
*/

function InfoDownDopis()
{
// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files\">".RS_FIL_SS_ZPET."</a></p>\n";

// bezpecnostni korekce
$GLOBALS["cislosouboru"]=mysql_escape_string($GLOBALS["cislosouboru"]);

$dotazdown=mysql_query("select nazev,komentar from ".$GLOBALS["rspredpona"]."download where idd='".$GLOBALS["cislosouboru"]."'",$GLOBALS["dbspojeni"]);
if ($dotazdown!=0):
  $pocetdown=mysql_num_rows($dotazdown);
else:
  $pocetdown=0;
endif;

if ($pocetdown==1): // test na jedinecnost clanku
  // nazev souboru
  $soubor_nazev=mysql_result($dotazdown,0,"nazev");
  // komentar k soubour
  $soubor_kom=mysql_result($dotazdown,0,"komentar");
  // predmet mailu
  $mail_titulek=$GLOBALS['wwwname'].' '.RS_FIL_PC_PREDMET_MAIL.' '.date("d.m.Y");
  // obsah mailu
  $mail_obsah=RS_FIL_PC_OBS_MAIL_1.":\n=========\n\n".$soubor_nazev.":\n".$soubor_kom."\n".$GLOBALS['baseadr']."download.php?soubor=".$GLOBALS["cislosouboru"]."\n\n".RS_FIL_PC_OBS_MAIL_2."\n".RS_FIL_PC_OBS_MAIL_3;
  // formular
  echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">\n";
  echo "<p align=\"center\" class=\"txt\">\n";
  echo "<input type=\"hidden\" name=\"akce\" value=\"AcInfoDownDopis\" /><input type=\"hidden\" name=\"modul\" value=\"files\" />\n";
  echo RS_FIL_PC_FROM_PREDMET.":<br /><input type=\"text\" name=\"prtitulek\" value=\"".$mail_titulek."\" size=\"60\" class=\"textpole\" /><br /><br />\n";
  echo RS_FIL_PC_FORM_OBSAH."Obsah e-mailu:<br /><textarea name=\"probsah\" rows=\"7\" cols=\"60\" class=\"textbox\">".$mail_obsah."</textarea><br /><br />\n";
  echo "<input type=\"submit\" value=\" ".RS_FIL_PC_TL_ODELAT_MAIL." \" class=\"tl\" />\n";
  echo "</p>\n";
  echo "</form>\n";
else:
  echo "<p align=\"center\" class=\"txt\">Error P1: ".RS_FIL_DS_ERR_NEEXISTUJE_SB."</p>\n";
endif;
}

function AcInfoDownDopis()
{
// odeslani e-mailu
$odeslani_posty = new CPosta();
$odeslani_posty->NastavInfoMail();
$odeslani_posty->Nastav("predmet",$GLOBALS['prtitulek']);
$odeslani_posty->Nastav("obsah",$GLOBALS['probsah']);
$odeslani_posty->Odesilac();

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files\">".RS_FIL_SS_ZPET."</a></p>\n";
}
?>
