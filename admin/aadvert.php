<?

######################################################################
# phpRS Administration Engine - Advertisement system section 1.3.1
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_klik_kampan, rs_klik_ban, rs_klik_rekl

/*
tabulka rs_klik_rekl: pozice -> 1 = horni pozice, 2 = dolni pozice
tabulka rs_klik_ban: druh -> 0 = banner, 1 = text, 2 = reklamni kod
*/

if ($Uzivatel->StavSession!=1):
  echo "<html><body><div align=\"center\">Tento soubor neni urcen k vnejsimu spousteni!</div></body></html>";
  exit;
endif;

// ---[rozcestnik]------------------------------------------------------------------
switch($GLOBALS['akce']):
     // reklamni subsystem
     case "ShowAdvert": AdvertMenu();
          Logo();
          break;
     case "BackToMain": AdminMenu();
          Logo();
          break;
     case "UpAdvert": AdvertMenu();
          echo "<h2 align=\"center\">".RS_REK_ROZ_HORNI_POZICE."</h2><p align=\"center\">";
          HorniReklama();
          break;
     case "SaveUpAdvert": AdvertMenu();
          echo "<h2 align=\"center\">".RS_REK_ROZ_HORNI_POZICE."</h2><p align=\"center\">";
          UlozReklamu();
          break;
     case "DownAdvert": AdvertMenu();
          echo "<h2 align=\"center\">".RS_REK_ROZ_DOLNI_POZICE."</h2><p align=\"center\">";
          DolniReklama();
          break;
     case "SaveDownAdvert": AdvertMenu();
          echo "<h2 align=\"center\">".RS_REK_ROZ_DOLNI_POZICE."</h2><p align=\"center\">";
          UlozReklamu();
          break;
     // kampan
     case "ShowCamp": AdvertMenu();
          echo "<h2 align=\"center\">".RS_REK_ROZ_SHOW_KAMPAN."</h2><p align=\"center\">";
          ShowCamp();
          break;
     case "AddCamp": AdvertMenu();
          echo "<h2 align=\"center\">".RS_REK_ROZ_ADD_KAMPAN."</h2><p align=\"center\">";
          AddCamp();
          break;
     case "DelCamp": AdvertMenu();
          echo "<h2 align=\"center\">".RS_REK_ROZ_DEL_KAMPAN."</h2><p align=\"center\">";
          DelCamp();
          break;
     // bannery
     case "Banner": AdvertMenu();
          echo "<h2 align=\"center\">".RS_REK_ROZ_SHOW_REKL."</h2><p align=\"center\">";
          VypisBannery();
          break;
     case "AddBanner": AdvertMenu();
          echo "<h2 align=\"center\">".RS_REK_ROZ_ADD_REKL."</h2><p align=\"center\">";
          PridejBanner();
          break;
     case "AcAddBanner": AdvertMenu();
          echo "<h2 align=\"center\">".RS_REK_ROZ_ADD_REKL."</h2><p align=\"center\">";
          AcPridejBanner();
          break;
     case "EditBanner": AdvertMenu();
          echo "<h2 align=\"center\">".RS_REK_ROZ_EDIT_REKL."</h2><p align=\"center\">";
          UpravBanner();
          break;
     case "AcEditBanner": AdvertMenu();
          echo "<h2 align=\"center\">".RS_REK_ROZ_EDIT_REKL."</h2><p align=\"center\">";
          AcUpravBanner();
          break;
     case "UseBanner": AdvertMenu();
          echo "<h2 align=\"center\">".RS_REK_ROZ_USE_REKL."</h2><p align=\"center\">";
          PouzijBanner();
          break;
     case "AcUseBanner": AdvertMenu();
          echo "<h2 align=\"center\">".RS_REK_ROZ_USE_REKL."</h2><p align=\"center\">";
          AcPouzijBanner();
          break;
     case "DeleteBanner": AdvertMenu();
          echo "<h2 align=\"center\">".RS_REK_ROZ_DEL_REKL."</h2><p align=\"center\">";
          SmazBanner();
          break;
endswitch;
echo "</p>\n"; // zakonceni P tagu

// ---[hlavni fce]------------------------------------------------------------------
function AdvertMenu()
{
echo "<table border=\"0\" cellpadding=\"5\" cellspacing=\"0\" align=\"center\" bgcolor=\"#A0D0FF\" class=\"rammodry\">
<tr class=\"menu\">
<td align=\"center\"><a href=\"admin.php?akce=UpAdvert&amp;modul=reklama\">".RS_REK_SUB_HORNI_POZICE."</a></td>
<td align=\"center\"><a href=\"admin.php?akce=DownAdvert&amp;modul=reklama\">".RS_REK_SUB_DOLNI_POZICE."</a></td>
<td align=\"center\"><a href=\"admin.php?akce=ShowCamp&amp;modul=reklama\">".RS_REK_SUB_KAMPAN."</a></td>
<td align=\"center\"><a href=\"admin.php?akce=Banner&amp;modul=reklama\">".RS_REK_SUB_REKLAMA."</a></td>
<td align=\"center\"><a href=\"admin.php?akce=BackToMain&amp;modul=reklama\">".RS_REK_SUB_ZPET."</a></td>
</tr>
</table>\n";
}

function VsechnyKorekce($text = '')
{
// tento radek umoznuje spravne zobrazit v editacnim poli vsechny zvlastni znaky zapsane jako &X;
$text=str_replace('&','&amp;',$text);
// tento radek nahrazuje uvozovky v nadpise za - &quot;
return str_replace('"','&quot;',$text);
}

function HorniReklama()
{
$dotazkod=mysql_query("select idr,kod from ".$GLOBALS["rspredpona"]."klik_rekl where pozice='1'",$GLOBALS["dbspojeni"]);
$pole_data=mysql_fetch_assoc($dotazkod);

echo "<form action=\"admin.php\" method=\"post\">
<p align=\"center\" class=\"txt\">
<strong>".RS_REK_HD_REKL_KOD."</strong><br />
<textarea name=\"prkod\" rows=\"8\" cols=\"50\" class=\"textbox\">".KorekceHTML($pole_data['kod'])."</textarea>
</p>
<input type=\"hidden\" name=\"akce\" value=\"SaveUpAdvert\" /><input type=\"hidden\" name=\"modul\" value=\"reklama\" />
<input type=\"hidden\" name=\"pridr\" value=\"".$pole_data['idr']."\" />
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\" /> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\" />
</form>\n";
}

function DolniReklama()
{
$dotazkod=mysql_query("select idr,kod from ".$GLOBALS["rspredpona"]."klik_rekl where pozice='2'",$GLOBALS["dbspojeni"]);
$pole_data=mysql_fetch_assoc($dotazkod);

echo "<form action=\"admin.php\" method=\"post\">
<p align=\"center\" class=\"txt\">
<strong>".RS_REK_HD_REKL_KOD."</strong><br />
<textarea name=\"prkod\" rows=\"8\" cols=\"50\" class=\"textbox\">".KorekceHTML($pole_data['kod'])."</textarea>
</p>
<input type=\"hidden\" name=\"akce\" value=\"SaveDownAdvert\" /><input type=\"hidden\" name=\"modul\" value=\"reklama\" />
<input type=\"hidden\" name=\"pridr\" value=\"".$pole_data['idr']."\" />
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\" /> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\" />
</form>\n";
}

function UlozReklamu()
{
// bezpecnostni kontrola
$GLOBALS["pridr"]=mysql_escape_string($GLOBALS["pridr"]);
$GLOBALS["prkod"]=mysql_escape_string($GLOBALS["prkod"]);

@$error=mysql_query("update ".$GLOBALS["rspredpona"]."klik_rekl set kod='".$GLOBALS["prkod"]."' where idr='".$GLOBALS["pridr"]."'",$GLOBALS["dbspojeni"]);
if (!$error):
  echo "<p align=\"center\" class=\"txt\">Error AS101: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_REK_HD_OK_EDIT_REKL_KOD."</p>\n"; // vse OK
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=UpAdvert&amp;modul=reklama\">".RS_REK_HD_UPRAVIT_HORNI."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=DownAdvert&amp;modul=reklama\">".RS_REK_HD_UPRAVIT_DOLNI."</a></p>\n";
}

// --- [Kampan] --------------------------------------------------------------------------

function ShowCamp()
{
// link
echo "<p align=\"center\" class=\"txt\"><a href=\"#pridejkampan\">".RS_REK_KM_PRIDAT."</a><p>\n";

// vypis
$dotazkamp=mysql_query("select idk,alias,email,info from ".$GLOBALS["rspredpona"]."klik_kampan",$GLOBALS["dbspojeni"]);
$pocetkamp=mysql_num_rows($dotazkamp);
echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">
<tr class=\"txt\" bgcolor=\"#E6E6E6\">
<td align=\"center\"><b>".RS_REK_KM_KAMPAN."</b></td>
<td align=\"center\"><b>".RS_REK_KM_EMAIL."</b></td>
<td align=\"center\"><b>".RS_REK_KM_POZNAMKA."</b></td>
<td align=\"center\"><b>".RS_REK_KM_AKCE."</b></td></tr>\n";
if ($pocetkamp==0):
  // zadna kampan
  echo "<tr class=\"txt\"><td colspan=\"4\" align=\"center\">".RS_REK_KM_ZADNA_KAMPAN."</td></tr>\n";
else:
  while ($pole_data = mysql_fetch_assoc($dotazkamp)):
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">\n";
    echo "<td align=\"left\">".$pole_data["alias"]."</td>\n";
    echo "<td align=\"left\">".TestNaNic($pole_data["email"])."</td>\n";
    echo "<td align=\"left\">".TestNaNic($pole_data["info"])."</td>\n";
    echo "<td align=\"center\"><a href=\"admin.php?akce=DelCamp&amp;modul=reklama&amp;pridk=".$pole_data["idk"]."\">".RS_REK_KM_SMAZ."</a></td></tr>\n";
  endwhile;
endif;
echo "</table>\n";
echo "<p></p>\n";

// pridavaci formular
echo "<a name=\"pridejkampan\"></a>
<p align=\"center\" class=\"txt\"><big><strong>".RS_REK_KM_NADPIS_ADD_KAMPAN."</strong></big></p>
<form action=\"admin.php\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\">
<tr class=\"txt\"><td><b>".RS_REK_KM_FORM_KAMPAN."</b></td><td><input type=\"text\" name=\"pralias\" size=\"30\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_KM_FORM_POZNAMKA."</b></td><td><input type=\"text\" name=\"prinfo\" size=\"30\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_KM_FORM_EMAIL."</b></td><td><input type=\"text\" name=\"premail\" value=\"@\" size=\"30\" class=\"textpole\" /></td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AddCamp\" /><input type=\"hidden\" name=\"modul\" value=\"reklama\" />
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_PRIDAT." \" class=\"tl\" /> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\" /></p>
</form>\n";
}

function AddCamp()
{
// uprava vstupu
if ($GLOBALS["premail"]=='@'): $GLOBALS["premail"]=''; endif;
// bezpecnostni korekce
$GLOBALS["pralias"]=mysql_escape_string($GLOBALS["pralias"]);
$GLOBALS["prinfo"]=mysql_escape_string($GLOBALS["prinfo"]);
$GLOBALS["premail"]=mysql_escape_string($GLOBALS["premail"]);

// pridani polozky
@$error=mysql_query("insert into ".$GLOBALS["rspredpona"]."klik_kampan values(null,'".$GLOBALS["pralias"]."','".$GLOBALS["prinfo"]."','".$GLOBALS["premail"]."')",$GLOBALS["dbspojeni"]);
if (!$error):
  echo "<p align=\"center\" class=\"txt\">Error AS102: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_REK_KM_OK_ADD_KAMPAN."</p>\n"; // vse OK
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowCamp&amp;modul=reklama\">".RS_REK_KM_ZPET."</a></p>\n";
}

function DelCamp()
{
// bezpecnostni korekce
$GLOBALS["pridk"]=mysql_escape_string($GLOBALS["pridk"]);

$chyba=0; // default false

// integrigni kontrola
$dotazkontr=mysql_query("select count(idb) as pocet from ".$GLOBALS["rspredpona"]."klik_ban where id_kampan='".$GLOBALS["pridk"]."'",$GLOBALS["dbspojeni"]);
if (mysql_result($dotazkontr,0,"pocet")>0):
  // kampan je aktivni - obsahuje nejake reklamni polozky
  echo "<p align=\"center\" class=\"txt\">".RS_REK_KM_ERR_AKTIVNI_KAMPAN."</p>\n";
  $chyba=1; // chyba
endif;

// test na chybu
if ($chyba==0):
  @$error= mysql_query("delete from ".$GLOBALS["rspredpona"]."klik_kampan where idk='".$GLOBALS["pridk"]."'",$GLOBALS["dbspojeni"]);
  if (!$error):
    echo "<p align=\"center\" class=\"txt\">Error AS103: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_REK_KM_OK_DEL_KAMPAN."</p>\n"; // vse Ok
  endif;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowCamp&amp;modul=reklama\">".RS_REK_KM_ZPET."</a></p>\n";
}

// --- [Bannery] -------------------------------------------------------------------------

function OptKampan($hledam = 0)
{
$vysl='';

$dotazkamp=mysql_query("select idk,alias from ".$GLOBALS["rspredpona"]."klik_kampan order by alias",$GLOBALS["dbspojeni"]);

while ($pole_data = mysql_fetch_assoc($dotazkamp)):
  $vysl.='<option value="'.$pole_data["idk"].'"';
  if ($pole_data["idk"]==$hledam): $vysl.=' selected'; endif;
  $vysl.='>'.$pole_data["alias"]."</option>\n";
endwhile;

return $vysl;
}

function VypisBannery()
{
// link
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=AddBanner&amp;modul=reklama\">".RS_REK_RP_PRIDAT."</a></p>\n";

// sestaveni dotazu
$dotaz="select b.idb,b.id_kampan,b.datum,b.alias,b.pocitadlo,k.alias as alias_kampan from ".$GLOBALS["rspredpona"]."klik_ban as b, ".$GLOBALS["rspredpona"]."klik_kampan as k ";
$dotaz.="where b.id_kampan=k.idk order by b.id_kampan";
$dotazban=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetban=mysql_num_rows($dotazban);
// vypis banneru
echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">
<tr class=\"txt\" bgcolor=\"#E6E6E6\">
<td align=\"center\"><b>".RS_REK_RP_BANNER."</b></td>
<td align=\"center\"><b>".RS_REK_RP_DATUM."</b></td>
<td align=\"center\"><b>".RS_REK_RP_POC."</b></td>
<td align=\"center\"><b>".RS_REK_RP_AKCE."</b></td></tr>\n";
if ($pocetban==0):
  // zadna polozka
  echo "<tr class=\"txt\"><td colspan=\"4\" align=\"center\">".RS_REK_RP_ZADNA_POLOZKA."</td></tr>\n";
else:
  $akt_kampan=0; // inic. aktivni kampane
  for ($pom=0;$pom<$pocetban;$pom++):
    $pole_data=mysql_fetch_assoc($dotazban);
    if ($pole_data["id_kampan"]!=$akt_kampan):
      echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#E9EC7D')\" onmouseout=\"setPointer(this, '#FFFFFF')\"><td align=\"center\" colspan=\"4\">";
      echo "<b>".$pole_data["alias_kampan"]." - <a href=\"admin.php?akce=UseBanner&amp;modul=reklama&amp;prtyp=kam&amp;pridtyp=".$pole_data["id_kampan"]."\">".RS_REK_RP_POUZIJ."</a></b>";
      echo "</td></tr>\n";
      $akt_kampan=$pole_data["id_kampan"]; // nastaveni nove aktivni kampane
    endif;
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">\n";
    echo "<td align=\"left\">".$pole_data["alias"]."</td>\n";
    echo "<td align=\"center\">".TestNaNic($pole_data["datum"])."</td>\n";
    echo "<td align=\"center\">".TestNaNic($pole_data["pocitadlo"])."</td>\n";
    echo "<td align=\"center\"><a href=\"admin.php?akce=EditBanner&amp;modul=reklama&amp;pridb=".$pole_data["idb"]."\">".RS_REK_RP_UPRAVIT."</a> / ";
    echo "<a href=\"admin.php?akce=UseBanner&amp;modul=reklama&amp;prtyp=ban&amp;pridtyp=".$pole_data["idb"]."\">".RS_REK_RP_POUZIJ."</a></td></tr>\n";
  endfor;
endif;
echo "</table>\n";
echo "<p></p>\n";
}

function PridejBanner()
{
// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=Banner&amp;modul=reklama\">".RS_REK_RP_ZPET."</a></p>";

// pridavaci formular
echo "<form action=\"admin.php\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy\" bgcolor=\"#E6E6E6\">
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_KAMPAN."</b></td><td><select name=\"prkampan\" size=\"1\">".OptKampan(0)."</select></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_NAZEV_REKL."</b></td><td><input type=\"text\" name=\"pralias\" size=\"30\" class=\"textpole\" /></td></tr>
<tr><td colspan=\"2\"><hr /></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_FORMA_REKL."</b></td><td><input type=\"radio\" name=\"prdruh\" value=\"0\" checked />".RS_REK_RP_FORM_BANNER."</td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_URL_ADR."</b></td><td><input type=\"text\" name=\"prbanner1\" size=\"30\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_CIL_ADR."</b></td><td><input type=\"text\" name=\"prcil1\" size=\"30\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_PRIDAV_TEXT."</b></td><td><input type=\"text\" name=\"prtext1\" size=\"30\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_SIRKA."</b></td><td><input type=\"text\" name=\"prwidth\" size=\"30\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_VYSKA."</b></td><td><input type=\"text\" name=\"prheight\" size=\"30\" class=\"textpole\" /></td></tr>
<tr><td colspan=\"2\"><hr /></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_FORMA_REKL."</b></td><td><input type=\"radio\" name=\"prdruh\" value=\"1\" />".RS_REK_RP_FORM_TEXT."</td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_HLA_TEXT."</b></td><td><input type=\"text\" name=\"prbanner2\" size=\"30\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_CIL_ADR."</b></td><td><input type=\"text\" name=\"prcil2\" size=\"30\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_BUBL_TEXT."</b></td><td><input type=\"text\" name=\"prtext2\" size=\"30\" class=\"textpole\" /></td></tr>
<tr><td colspan=\"2\"><hr /></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_FORMA_REKL."</b></td><td><input type=\"radio\" name=\"prdruh\" value=\"2\" />".RS_REK_RP_FORM_REKL_KOD."</td></tr>
<tr class=\"txt\"><td colspan=\"2\"><textarea name=\"prbanner3\" rows=\"5\" cols=\"55\" class=\"textbox\" /></textarea></td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcAddBanner\" /><input type=\"hidden\" name=\"modul\" value=\"reklama\" />
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_PRIDAT." \" class=\"tl\" /> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\" /></p>
</form>\n";
}

function AcPridejBanner()
{
$GLOBALS["prdruh"]=mysql_escape_string($GLOBALS["prdruh"]);
$GLOBALS["prbanner2"]=mysql_escape_string($GLOBALS["prbanner2"]);
$GLOBALS["prtext2"]=mysql_escape_string($GLOBALS["prtext2"]);
$GLOBALS["prbanner3"]=mysql_escape_string($GLOBALS["prbanner3"]);

$prdatum=Date("Y-m-d");

// druhy: 0 = banner, 1 = text, 2 = reklamni kod
switch($GLOBALS["prdruh"]):
  case 0:
    @$error=mysql_query("insert into ".$GLOBALS["rspredpona"]."klik_ban values(null,'".$GLOBALS["prkampan"]."','".$prdatum."','".$GLOBALS["pralias"]."','".$GLOBALS["prtext1"]."','".$GLOBALS["prbanner1"]."','".$GLOBALS["prcil1"]."','".$GLOBALS["prwidth"]."','".$GLOBALS["prheight"]."','".$GLOBALS["prdruh"]."','0')",$GLOBALS["dbspojeni"]);
    $prpomtext=RS_REK_RP_OK_ADD_REKL_C2B; // reklamni banner
    break;
  case 1:
    @$error=mysql_query("insert into ".$GLOBALS["rspredpona"]."klik_ban values(null,'".$GLOBALS["prkampan"]."','".$prdatum."','".$GLOBALS["pralias"]."','".$GLOBALS["prtext2"]."','".$GLOBALS["prbanner2"]."','".$GLOBALS["prcil2"]."','0','0','".$GLOBALS["prdruh"]."','0')",$GLOBALS["dbspojeni"]);
    $prpomtext=RS_REK_RP_OK_ADD_REKL_C2B; // reklamni text
    break;
  case 2:
    @$error=mysql_query("insert into ".$GLOBALS["rspredpona"]."klik_ban values(null,'".$GLOBALS["prkampan"]."','".$prdatum."','".$GLOBALS["pralias"]."','','".$GLOBALS["prbanner3"]."','','0','0','".$GLOBALS["prdruh"]."','0')",$GLOBALS["dbspojeni"]);
    $prpomtext=RS_REK_RO_OK_ADD_REKL_C2C; // reklamni kod
    break;
endswitch;

// globalni vysledek
if (!$error):
  echo "<p align=\"center\" class=\"txt\">Error AS104: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_REK_RP_OK_ADD_REKL_C1." ".$prpomtext."</p>\n"; // vse OK
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=Banner&amp;modul=reklama\">".RS_REK_RP_ZPET."</a></p>";
}

function UpravBanner()
{
// bezpecnostni kontrola
$GLOBALS["pridb"]=mysql_escape_string($GLOBALS["pridb"]);

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=Banner&amp;modul=reklama\">".RS_REK_RP_ZPET."</a></p>";

$dotazban=mysql_query("select id_kampan,datum,alias,text,banner,cil,width,height,druh,pocitadlo from ".$GLOBALS["rspredpona"]."klik_ban where idb='".$GLOBALS["pridb"]."'",$GLOBALS["dbspojeni"]);
$pole_data=mysql_fetch_assoc($dotazban);

// vymazani reklamniho prvku
echo "<div align=\"center\">
<form action=\"admin.php\" method=\"post\">
<input type=\"hidden\" name=\"akce\" value=\"DeleteBanner\" /><input type=\"hidden\" name=\"modul\" value=\"reklama\" />
<input type=\"hidden\" name=\"pridb\" value=\"".$GLOBALS["pridb"]."\" />
<input type=\"submit\" value=\" ".RS_REK_RP_TL_SMAZ_REKL." \" class=\"tl\" />
</form>
</div>
<p></p>\n";

// editacni formular
echo "<form action=\"admin.php\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy\" bgcolor=\"#E6E6E6\">
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_DATUM."</b></td><td>".$pole_data["datum"]."</td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_KAMPAN."</b></td><td><select name=\"prkampan\" size=\"1\">".OptKampan($pole_data["id_kampan"])."</select></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_NAZEV_REKL."</b></td><td><input type=\"text\" name=\"pralias\" value=\"".$pole_data["alias"]."\" class=\"textpole\" /></td></tr>
<tr><td colspan=\"2\"><hr /></td></tr>\n";
// banner
if ($pole_data["druh"]==0):
  echo "<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_FORMA_REKL."</b></td><td><input type=\"radio\" name=\"prdruh\" value=\"0\" checked />".RS_REK_RP_FORM_BANNER."</td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_URL_ADR."</b></td><td><input type=\"text\" name=\"prbanner1\" value=\"".$pole_data["banner"]."\" size=\"30\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_CIL_ADR."</b></td><td><input type=\"text\" name=\"prcil1\" value=\"".$pole_data["cil"]."\" size=\"30\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_PRIDAV_TEXT."</b></td><td><input type=\"text\" name=\"prtext1\" value=\"".$pole_data["text"]."\" size=\"30\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_SIRKA."</b></td><td><input type=\"text\" name=\"prwidth\" value=\"".$pole_data["width"]."\" size=\"30\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_VYSKA."</b></td><td><input type=\"text\" name=\"prheight\" value=\"".$pole_data["height"]."\" size=\"30\" class=\"textpole\" /></td></tr>\n";
else:
  echo "<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_FORMA_REKL."</b></td><td><input type=\"radio\" name=\"prdruh\" value=\"0\" />".RS_REK_RP_FORM_BANNER."</td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_URL_ADR."</b></td><td><input type=\"text\" name=\"prbanner1\" value=\"\" size=\"30\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_CIL_ADR."</b></td><td><input type=\"text\" name=\"prcil1\" value=\"\" size=\"30\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_PRIDAV_TEXT."</b></td><td><input type=\"text\" name=\"prtext1\" value=\"\" size=\"30\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_SIRKA."</b></td><td><input type=\"text\" name=\"prwidth\" value=\"\" size=\"30\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_VYSKA."</b></td><td><input type=\"text\" name=\"prheight\" value=\"\" size=\"30\" class=\"textpole\" /></td></tr>\n";
endif;
// konec banneru
echo "<tr><td colspan=\"2\"><hr /></td></tr>\n";
// text
if ($pole_data["druh"]==1):
  echo "<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_FORMA_REKL."</b></td><td><input type=\"radio\" name=\"prdruh\" value=\"1\" checked />".RS_REK_RP_FORM_TEXT."</td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_HLA_TEXT."</b></td><td><input type=\"text\" name=\"prbanner2\" value=\"".$pole_data["banner"]."\" size=\"30\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_CIL_ADR."</b></td><td><input type=\"text\" name=\"prcil2\" value=\"".$pole_data["cil"]."\" size=\"30\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_BUBL_TEXT."</b></td><td><input type=\"text\" name=\"prtext2\" value=\"".$pole_data["text"]."\" size=\"30\" class=\"textpole\" /></td></tr>\n";
else:
  echo "<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_FORMA_REKL."</b></td><td><input type=\"radio\" name=\"prdruh\" value=\"1\" />".RS_REK_RP_FORM_TEXT."</td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_HLA_TEXT."</b></td><td><input type=\"text\" name=\"prbanner2\" value=\"\" size=\"30\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_CIL_ADR."</b></td><td><input type=\"text\" name=\"prcil2\" value=\"\" size=\"30\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_BUBL_TEXT."</b></td><td><input type=\"text\" name=\"prtext2\" value=\"\" size=\"30\" class=\"textpole\" /></td></tr>\n";
endif;
// konec textu
echo "<tr><td colspan=\"2\"><hr /></td></tr>\n";
// reklamni kod
if ($pole_data["druh"]==2):
  echo "<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_FORMA_REKL."</b></td><td><input type=\"radio\" name=\"prdruh\" value=\"2\" checked />".RS_REK_RP_FORM_REKL_KOD."</td></tr>
<tr class=\"txt\"><td colspan=\"2\"><textarea name=\"prbanner3\" rows=\"5\" cols=\"55\" class=\"textbox\" />".KorekceHTML($pole_data["banner"])."</textarea></td></tr>\n";
else:
  echo "<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_FORMA_REKL."</b></td><td><input type=\"radio\" name=\"prdruh\" value=\"2\" />".RS_REK_RP_FORM_REKL_KOD."</td></tr>
<tr class=\"txt\"><td colspan=\"2\"><textarea name=\"prbanner3\" rows=\"5\" cols=\"55\" class=\"textbox\" /></textarea></td></tr>\n";
endif;
// konec reklamni kod
echo "</table>
<input type=\"hidden\" name=\"akce\" value=\"AcEditBanner\" /><input type=\"hidden\" name=\"modul\" value=\"reklama\" />
<input type=\"hidden\" name=\"pridb\" value=\"".$GLOBALS["pridb"]."\" />
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\" /> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\" /></p>
</form>
<p></p>\n";
}

function AcUpravBanner()
{
// bezpecnostni kontrola
$GLOBALS["pridb"]=mysql_escape_string($GLOBALS["pridb"]);
$GLOBALS["prdruh"]=mysql_escape_string($GLOBALS["prdruh"]);
$GLOBALS["prbanner2"]=mysql_escape_string($GLOBALS["prbanner2"]);
$GLOBALS["prtext2"]=mysql_escape_string($GLOBALS["prtext2"]);
$GLOBALS["prbanner3"]=mysql_escape_string($GLOBALS["prbanner3"]);

// druhy: 0 = banner, 1 = text, 2 = reklamni kod
switch ($GLOBALS["prdruh"]):
  case 0:
    @$error=mysql_query("update ".$GLOBALS["rspredpona"]."klik_ban set id_kampan='".$GLOBALS["prkampan"]."', alias='".$GLOBALS["pralias"]."', text='".$GLOBALS["prtext1"]."', banner='".$GLOBALS["prbanner1"]."', cil='".$GLOBALS["prcil1"]."', width='".$GLOBALS["prwidth"]."', height='".$GLOBALS["prheight"]."', druh='0' where idb='".$GLOBALS["pridb"]."'",$GLOBALS["dbspojeni"]);
    $prpomtext=RS_REK_RP_OK_EDIT_REKL_C2A; // reklamni banner
    break;
  case 1:
    @$error=mysql_query("update ".$GLOBALS["rspredpona"]."klik_ban set id_kampan='".$GLOBALS["prkampan"]."', alias='".$GLOBALS["pralias"]."', text='".$GLOBALS["prtext2"]."', banner='".$GLOBALS["prbanner2"]."', cil='".$GLOBALS["prcil2"]."', druh='1' where idb='".$GLOBALS["pridb"]."'",$GLOBALS["dbspojeni"]);
    $prpomtext=RS_REK_RP_OK_EDIT_REKL_C2B; // reklamni text
    break;
  case 2:
    @$error=mysql_query("update ".$GLOBALS["rspredpona"]."klik_ban set id_kampan='".$GLOBALS["prkampan"]."', alias='".$GLOBALS["pralias"]."', banner='".$GLOBALS["prbanner3"]."', druh='2' where idb='".$GLOBALS["pridb"]."'",$GLOBALS["dbspojeni"]);
    $prpomtext=RS_REK_RO_OK_EDIT_REKL_C2C; // reklamni kod
    break;
endswitch;

// globalni vysledek
if (!$error):
  echo "<p align=\"center\" class=\"txt\">Error AS105: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_REK_RP_OK_EDIT_REKL_C1." ".$prpomtext."</p>\n"; // vse OK
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=Banner&amp;modul=reklama\">".RS_REK_RP_ZPET."</a></p>";
}

function PouzijBanner()
{
// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=Banner&amp;modul=reklama\">".RS_REK_RP_ZPET."</a></p>";

// $prtyp ma dva stavy: ban - banner, kam - kampan
switch($GLOBALS["prtyp"]):
  case 'ban': $dotazali=mysql_query("select alias from ".$GLOBALS["rspredpona"]."klik_ban where idb='".$GLOBALS["pridtyp"]."'",$GLOBALS["dbspojeni"]);
              list($pralias)=mysql_fetch_row($dotazali);
              $pralias=RS_REK_RP_TYP_REKL." - ".$pralias;
              break;
  case 'kam': $dotazali=mysql_query("select alias from ".$GLOBALS["rspredpona"]."klik_kampan where idk='".$GLOBALS["pridtyp"]."'",$GLOBALS["dbspojeni"]);
              list($pralias)=mysql_fetch_row($dotazali);
              $pralias=RS_REK_RP_TYP_KAMPAN." - ".$pralias;
              break;
endswitch;

echo "<div align=\"center\">
<span class=\"txt\"><strong>\"".$pralias."\"</strong></span>
<p></p>
<form action=\"admin.php\" method=\"post\">
<input type=\"hidden\" name=\"akce\" value=\"AcUseBanner\" /><input type=\"hidden\" name=\"pridtyp\" value=\"".$GLOBALS["pridtyp"]."\" />
<input type=\"hidden\" name=\"prco\" value=\"horni\" /><input type=\"hidden\" name=\"prtyp\" value=\"".$GLOBALS["prtyp"]."\" />
<input type=\"hidden\" name=\"modul\" value=\"reklama\" />
<input type=\"submit\" value=\" ".RS_REK_RP_TL_APL_HORNI_POZICE." \" class=\"tl\" />
</form>
<p></p>
<form action=\"admin.php\" method=\"post\">
<input type=\"hidden\" name=\"akce\" value=\"AcUseBanner\" /><input type=\"hidden\" name=\"pridtyp\" value=\"".$GLOBALS["pridtyp"]."\" />
<input type=\"hidden\" name=\"prco\" value=\"dolni\" /><input type=\"hidden\" name=\"prtyp\" value=\"".$GLOBALS["prtyp"]."\" />
<input type=\"hidden\" name=\"modul\" value=\"reklama\" />
<input type=\"submit\" value=\" ".RS_REK_RP_TL_APL_DOLNI_POZICE." \" class=\"tl\" />
</form>\n";
if ($GLOBALS["prtyp"]=='ban'):
  echo "<p></p>
<form action=\"admin.php\" method=\"post\">
<input type=\"hidden\" name=\"akce\" value=\"AcUseBanner\" /><input type=\"hidden\" name=\"pridtyp\" value=\"".$GLOBALS["pridtyp"]."\" />
<input type=\"hidden\" name=\"prco\" value=\"generuj\" /><input type=\"hidden\" name=\"prtyp\" value=\"".$GLOBALS["prtyp"]."\" />
<input type=\"hidden\" name=\"modul\" value=\"reklama\" />
<input type=\"submit\" value=\" ".RS_REK_RP_TL_GENERUJ_KOD." \" class=\"tl\" />
</form>\n";
endif;
echo "</div>\n";
}

function AcPouzijBanner()
{
/*
  (rs_klik_ban) druhy: banner = 0, text = 1
  $prco: horni, dolni, generuj
  (rs_klik_rekl) pozice: 1 = horni pozice, 2 = dolni pozice
  $prtyp: ban, kam
  smerovaci soubor: direct.php?kam=id_cislo_banneru
*/

// aplikace reklamniho prvku
if ($GLOBALS["prtyp"]=='ban'):
  $dotaztyp=mysql_query("select text,banner,width,height,druh from ".$GLOBALS["rspredpona"]."klik_ban where idb='".$GLOBALS["pridtyp"]."'",$GLOBALS["dbspojeni"]);
  if ($dotaztyp!=0):
    list($prtext,$prbanner,$prwidth,$prheight,$prdruh)=mysql_fetch_row($dotaztyp);
  endif;

  // typ reklamniho prvku
  switch ($prdruh):
    case 0: // reklama - banner
      $prkod="<span class=\"bannerpod\"><a href=\"direct.php?kam=".$GLOBALS["pridtyp"]."\" target=\"_blank\"><img src=\"".$prbanner."\" width=\"".$prwidth."\" height=\"".$prheight."\" alt=\"".$prtext."\" />";
      if ($prtext!=''): $prkod.="<br />".$prtext; endif;
      $prkod.="</a></span>";
      break;
    case 1: // reklama - text
      $prkod="<span class=\"banner\"><a href=\"direct.php?kam=".$GLOBALS["pridtyp"]."\" title=\"".$prtext."\" target=\"_blank\">".$prbanner."</a></span>";
      break;
    case 2: // reklama - reklamni kod
      $prkod=$prbanner;
      break;
  endswitch;
endif;

// aplikace kampane kampane
if ($GLOBALS["prtyp"]=='kam'):
  $prkod="<kampan>".$GLOBALS["pridtyp"]."</kampan>";
endif;

// proved akci X
switch($GLOBALS["prco"]):
  case "horni":
       @$error=mysql_query("update ".$GLOBALS["rspredpona"]."klik_rekl set kod='".$prkod."' where pozice='1'",$GLOBALS["dbspojeni"]);
       if (!$error):
         echo "<p align=\"center\" class=\"txt\">Error AS106: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
       else:
         echo "<p align=\"center\" class=\"txt\">".RS_REK_RP_OK_USE_HORNI_POZICE."</p>\n"; // vse OK
       endif;
       break;
  case "dolni":
       @$error=mysql_query("update ".$GLOBALS["rspredpona"]."klik_rekl set kod='".$prkod."' where pozice='2'",$GLOBALS["dbspojeni"]);
       if (!$error):
         echo "<p align=\"center\" class=\"txt\">Error AS107: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
       else:
         echo "<p align=\"center\" class=\"txt\">".RS_REK_RP_OK_USE_DOLNI_POZICE."</p>\n"; // vse OK
       endif;
       break;
  case "generuj":
       echo "<p align=\"center\" class=\"txt\"><strong>".RS_REK_RP_REKL_KOD."</strong><br />\n";
       echo "<textarea name=\"prkod\" rows=\"8\" cols=\"50\" class=\"textbox\">".KorekceHTML($prkod)."</textarea></p>\n";
       break;
endswitch;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=Banner&amp;modul=reklama\">".RS_REK_RP_ZPET."</a></p>";
}

function SmazBanner()
{
// bezpecnostni kontrola
$GLOBALS["pridb"]=mysql_escape_string($GLOBALS["pridb"]);

@$error=mysql_query("delete from ".$GLOBALS["rspredpona"]."klik_ban where idb='".$GLOBALS["pridb"]."'",$GLOBALS["dbspojeni"]);
if (!$error):
  echo "<p align=\"center\" class=\"txt\">Error AS108: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_REK_RP_OK_DEL_REKL."</p>\n";
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=Banner&amp;modul=reklama\">".RS_REK_RP_ZPET."</a></p>";
}
?>
