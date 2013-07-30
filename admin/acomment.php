<?

######################################################################
# phpRS Administration Engine - Comment's section 1.4.0
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_clanky, rs_komentare, rs_user

/*
  Tento soubor zajistuje obsluhu komentaru prirazenych ke clankum.
*/

if ($Uzivatel->StavSession!=1):
  echo "<html><body><div align=\"center\">Tento soubor neni urcen k vnejsimu spousteni!</div></body></html>";
  exit();
endif;

include_once("admin/astdlib_comment.php"); // standardni knihovna komentarovych funkci

// ---[rozcestnik]------------------------------------------------------------------
switch($GLOBALS['akce']):
     // komentare
     case "ShowComment": AdminMenu();
          echo "<h2 align=\"center\">".RS_KOM_ROZ_SHOW_KOMENT."</h2><p align=\"center\">";
          ShowComment();
          break;
     case "ViewComment": AdminMenu();
          echo "<h2 align=\"center\">".RS_KOM_ROZ_KOMENT."</h2><p align=\"center\">";
          ViewComment();
          break;
     case "DelComment": AdminMenu();
          echo "<h2 align=\"center\">".RS_KOM_ROZ_DEL_OBSAH."</h2><p align=\"center\">";
          DelComment();
          break;
     case "HardDelComment": AdminMenu();
          echo "<h2 align=\"center\">".RS_KOM_ROZ_DEL_KOMENT."</h2><p align=\"center\">";
          HardDelComment();
          break;
     case "EditComment": AdminMenu();
          echo "<h2 align=\"center\">".RS_KOM_ROZ_EDIT_KOMENT."</h2><p align=\"center\">";
          EditComment();
          break;
     case "AcEditComment": AdminMenu();
          echo "<h2 align=\"center\">".RS_KOM_ROZ_EDIT_KOMENT."</h2><p align=\"center\">";
          AcEditComment();
          break;
     case "ViewIPComment": AdminMenu();
          echo "<h2 align=\"center\">".RS_KOM_ROZ_KOMENT."</h2><p align=\"center\">";
          ViewIPComment();
          break;
endswitch;
echo "</p>\n"; // zakonceni P tagu

// ---[hlavni fce]------------------------------------------------------------------

/*
  ShowComment()
  ViewComment()
  DelComment()
  HardDelComment()
  EditComment()
  AcEditComment()
  ViewIPComment()
*/

function ShowComment()
{
// pocet vsech clanku s komentari
$dotazmnozstvi=mysql_query("select count(idc) as pocet from ".$GLOBALS["rspredpona"]."clanky where kom>0",$GLOBALS["dbspojeni"]);
if ($dotazmnozstvi!=0):
  $pocetmnozstvi=mysql_Result($dotazmnozstvi,0,"pocet"); // existuje vysledek
else:
  $pocetmnozstvi=0; // chyba
endif;

// kdyz neni definovan interval
if (!isset($GLOBALS["prmin"])):
  if($pocetmnozstvi<20):
    $GLOBALS["prmin"]=0;
    $GLOBALS["prmax"]=$pocetmnozstvi;
  else:
    $GLOBALS["prmin"]=0;
    $GLOBALS["prmax"]=20;
  endif;
endif;

echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\"><p align=\"center\" class=\"txt\">
<input type=\"hidden\" name=\"akce\" value=\"ShowComment\" /><input type=\"hidden\" name=\"modul\" value=\"comment\" />
<input type=\"submit\" value=\" ".RS_KOM_SK_ZOBRAZ_KOMENT." \" class=\"tl\" />
".RS_KOM_SK_OD." <input type=\"text\" name=\"prmin\" value=\"".$GLOBALS["prmin"]."\" size=\"4\" class=\"textpole\" />
".RS_KOM_SK_DO." <input type=\"text\" name=\"prmax\" value=\"".$GLOBALS["prmax"]."\" size=\"4\" class=\"textpole\" />
(".RS_KOM_SK_CELK_POCET.": ".$pocetmnozstvi.")
</p></form>
<hr width=\"600\" />
<p></p>\n";

// informace o vypisu komentaru
echo "<p align=\"center\" class=\"txt\"><i>".RS_KOM_SK_INTO_KOMENT."</i></p>\n";

// nacteni seznamu uzivatelu(autoru) do pole "autori"
$dotazaut=mysql_query("select idu,user from ".$GLOBALS["rspredpona"]."user order by idu",$GLOBALS["dbspojeni"]);
$pocetaut=mysql_num_rows($dotazaut);
for ($pom=0;$pom<$pocetaut;$pom++):
  $autori[mysql_result($dotazaut,$pom,"idu")]=mysql_result($dotazaut,$pom,"user");
endfor;

// vypocet omezeni
if ($GLOBALS["prmin"]==0): $upr_min=0; else: $upr_min=($GLOBALS["prmin"]-1); endif;
$kolik=$GLOBALS["prmax"]-$upr_min;

// sestaveni dotazu
$dotaz="select c.link,c.titulek,c.datum,c.autor,c.kom,max(k.datum) as posledni,k.od from ".$GLOBALS["rspredpona"]."clanky as c, ".$GLOBALS["rspredpona"]."komentare as k ";
$dotaz.="where c.link = k.clanek group by c.link ";
$dotaz.="order by posledni desc limit ".$upr_min.",".$kolik;

$dotazkom=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetkom=mysql_num_rows($dotazkom);

if ($pocetkom==0):
  // CHYBA: Zadaný interval (od xxx do yyy) je prázdný!
  echo "<p align=\"center\" class=\"txt\">".RS_ADM_INTERVAL_C1." ".$GLOBALS["prmin"]." ".RS_ADM_INTERVAL_C2." ".$GLOBALS["prmax"].RS_ADM_INTERVAL_C3."</p>\n";
else:
  echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">\n";
  echo "<tr class=\"txt\" bgcolor=\"#E6E6E6\">";
  echo "<td align=\"center\"><b>".RS_KOM_SK_LINK."</b></td>";
  echo "<td align=\"center\" width=\"300\"><b>".RS_KOM_SK_TITULEK."</b></td>";
  echo "<td align=\"center\"><b>".RS_KOM_SK_AKTUAL."</b></td>";
  echo "<td align=\"center\"><b>".RS_KOM_SK_AUTOR."</b></td>";
  echo "<td align=\"center\"><b>".RS_KOM_SK_DAT_VYDANI."</b></td>";
  echo "<td align=\"center\"><b>".RS_KOM_SK_POCET."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_KOM_SK_AKCE."</b></td></tr>\n";
  for ($pom=0;$pom<$pocetkom;$pom++):
    $pole_data=mysql_fetch_assoc($dotazkom);
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">\n";
    echo "<td align=\"center\">".$pole_data["link"]."</td>\n";
    echo "<td align=\"left\" width=\"300\">".$pole_data["titulek"]."</td>\n";
    echo "<td align=\"center\">".MyDatetimeToDate($pole_data["posledni"])."</td>\n";
    echo "<td align=\"center\">";
    if (isset($autori[$pole_data["autor"]])):
      echo $autori[$pole_data["autor"]];
    else:
      echo $pole_data["autor"];
    endif;
    echo "</td>\n";
    echo "<td align=\"center\">".MyDatetimeToDate($pole_data["datum"])."</td>\n";
    echo "<td align=\"center\">".$pole_data["kom"]."</td>\n";
    echo "<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ViewComment&amp;modul=comment&amp;prlink=".$pole_data["link"]."\">".RS_KOM_SK_ZOBRAZIT."</a></td></tr>\n";
  endfor;
  echo "</table>\n";
endif;
echo "<p></p>\n";
}

function ViewComment()
{
$GLOBALS["prlink"]=mysql_escape_string($GLOBALS["prlink"]);

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowComment&amp;modul=comment\">".RS_KOM_SK_ZPET."</a></p>\n";

// nalezeni nazvu clanku
$dotazcla=mysql_query("select titulek from ".$GLOBALS["rspredpona"]."clanky where link='".$GLOBALS["prlink"]."'",$GLOBALS["dbspojeni"]);
if (mysql_num_rows($dotazcla)>0):
  echo "<p align=\"center\" class=\"txt\"><strong><big>".mysql_result($dotazcla,0,"titulek")."</big></strong></p>\n";
endif;

// vypis nalezenych komentaru
$dotazkom=mysql_query("select * from ".$GLOBALS["rspredpona"]."komentare where clanek='".$GLOBALS["prlink"]."' order by idk desc",$GLOBALS["dbspojeni"]);
$pocetkom=mysql_num_rows($dotazkom);

if ($pocetkom==0):
  // CHYBA: K vybranému článku nejsou aktuálně přiřazeny žádné komentáře!
  echo "<p align=\"center\" class=\"txt\">".RS_KOM_SK_ZADNY_KOMENT."</p>\n";
else:
  for ($pom=0;$pom<$pocetkom;$pom++):
    $pole_data=mysql_fetch_assoc($dotazkom);
    echo "<table cellspacing=\"0\" cellpadding=\"2\" border=\"0\" align=\"center\" width=\"700\" class=\"ramsedy\" bgcolor=\"#E6E6E6\">\n";
    echo "<tr class=\"txt\"><td align=\"left\" width=\"180\"><b>".RS_KOM_SK_FORM_DATUM.":</b></td><td align=\"left\" width=\"420\">".$pole_data["datum"]."</td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_AUTOR.":</b></td><td align=\"left\">".$pole_data["od"]." ";
    if ($pole_data["registrovany"]==1): echo '[<b>'.$pole_data["reg_prezdivka"].'</b>]'; else: echo '['.RS_KOM_SK_FORM_NEREG_CTENAR.']'; endif;
    echo "</td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_EMAIL.":</b></td><td align=\"left\"><a href=\"mailto:".$pole_data["od_mail"]."\">".$pole_data["od_mail"]."</a></td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_IP_ADR.":</b></td><td align=\"left\">".$pole_data["od_ip"]." - <a href=\"".RS_VYKONNYSOUBOR."?akce=ViewIPComment&amp;modul=comment&amp;prip=".$pole_data["od_ip"]."\">".RS_KOM_SK_FORM_ALL_IP_KOMENT."</a></td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_TITULEK.":</b></td><td align=\"left\">".$pole_data["titulek"]."</td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"left\" colspan=\"2\"><hr /><b>".RS_KOM_SK_FORM_OBS_KOMENT.":</b><br /><div class=\"smltxt\">".$pole_data["obsah"]."</div><hr /></td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"center\" colspan=\"2\">";
    echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=EditComment&amp;modul=comment&amp;pridk=".$pole_data["idk"]."&amp;przpet=link&amp;prip=".$pole_data["od_ip"]."&amp;prlink=".$pole_data["clanek"]."\">".RS_KOM_SK_FORM_EDIT_KOMENT."</a> - ";
    echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=DelComment&amp;modul=comment&amp;pridk=".$pole_data["idk"]."&amp;przpet=link&amp;prip=".$pole_data["od_ip"]."&amp;prlink=".$pole_data["clanek"]."\">".RS_KOM_SK_FORM_DEL_OBSAH."</a> - ";
    echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=HardDelComment&amp;modul=comment&amp;pridk=".$pole_data["idk"]."&amp;przpet=link&amp;prip=".$pole_data["od_ip"]."&amp;prlink=".$pole_data["clanek"]."\">".RS_KOM_SK_FORM_DEL_KOMENT."</a>";
    echo "</td></tr>\n";
    echo "</table>\n";
    echo "<p></p>\n";
  endfor;
endif;
echo "<p></p>\n";
}

function DelComment()
{
$GLOBALS["pridk"]=mysql_escape_string($GLOBALS["pridk"]);
$GLOBALS["przpet"]=mysql_escape_string($GLOBALS["przpet"]);
$GLOBALS["prlink"]=mysql_escape_string($GLOBALS["prlink"]);
$GLOBALS["prip"]=mysql_escape_string($GLOBALS["prip"]);

// text nahrazujici obsah komentare
$novy_text=RS_KOM_NAHRAD_OBSAH;

@$error=mysql_query("update ".$GLOBALS["rspredpona"]."komentare set obsah='".$novy_text."' where idk='".$GLOBALS["pridk"]."'",$GLOBALS["dbspojeni"]);
if (!$error):
  echo "<p align=\"center\" class=\"txt\">Error C1: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_KOM_SK_OK_DEL_OBSAH."</p>\n";
endif;

// navrat
if ($GLOBALS["przpet"]=='link'):
  echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ViewComment&amp;modul=comment&amp;prlink=".$GLOBALS["prlink"]."\">".RS_KOM_SK_ZPET_KOMENT."</a></p>\n";
else:
  echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ViewIPComment&amp;modul=comment&amp;prip=".$GLOBALS["prip"]."\">".RS_KOM_SK_ZPET_KOMENT."</a></p>\n";
endif;
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowComment&amp;modul=comment\">".RS_KOM_SK_ZPET."</a></p>\n";
}

function HardDelComment()
{
$GLOBALS["pridk"]=mysql_escape_string($GLOBALS["pridk"]);
$GLOBALS["przpet"]=mysql_escape_string($GLOBALS["przpet"]);
$GLOBALS["prlink"]=mysql_escape_string($GLOBALS["prlink"]);
$GLOBALS["prip"]=mysql_escape_string($GLOBALS["prip"]);

// zjisteni linku na clanek
$dotazkom=mysql_query("select clanek from ".$GLOBALS["rspredpona"]."komentare where idk='".$GLOBALS["pridk"]."'",$GLOBALS["dbspojeni"]);
if (mysql_num_rows($dotazkom)>0):
  $cla_link=mysql_result($dotazkom,0,"clanek");
else:
  $cla_link='';
endif;

@$error=mysql_query("delete from ".$GLOBALS["rspredpona"]."komentare where idk='".$GLOBALS["pridk"]."'",$GLOBALS["dbspojeni"]);
if (!$error):
  echo "<p align=\"center\" class=\"txt\">Error C2: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_KOM_SK_OK_DEL_KOMENT."</p>\n";
  mysql_query("update ".$GLOBALS["rspredpona"]."clanky set kom=kom-1 where link='".$cla_link."'",$GLOBALS["dbspojeni"]);
endif;

// navrat
if ($GLOBALS["przpet"]=='link'):
  echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ViewComment&amp;modul=comment&amp;prlink=".$GLOBALS["prlink"]."\">".RS_KOM_SK_ZPET_KOMENT."</a></p>\n";
else:
  echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ViewIPComment&amp;modul=comment&amp;prip=".$GLOBALS["prip"]."\">".RS_KOM_SK_ZPET_KOMENT."</a></p>\n";
endif;
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowComment&amp;modul=comment\">".RS_KOM_SK_ZPET."</a></p>\n";
}

function EditComment()
{
$GLOBALS["pridk"]=mysql_escape_string($GLOBALS["pridk"]);
$GLOBALS["przpet"]=mysql_escape_string($GLOBALS["przpet"]);
$GLOBALS["prlink"]=mysql_escape_string($GLOBALS["prlink"]);
$GLOBALS["prip"]=mysql_escape_string($GLOBALS["prip"]);

// navrat
if ($GLOBALS["przpet"]=='link'):
  echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ViewComment&amp;modul=comment&amp;prlink=".$GLOBALS["prlink"]."\">".RS_KOM_SK_ZPET_KOMENT."</a></p>\n";
else:
  echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ViewIPComment&amp;modul=comment&amp;prip=".$GLOBALS["prip"]."\">".RS_KOM_SK_ZPET_KOMENT."</a></p>\n";
endif;

$dotazkom=mysql_query("select * from ".$GLOBALS["rspredpona"]."komentare where idk='".$GLOBALS["pridk"]."'",$GLOBALS["dbspojeni"]);
$pole_data=mysql_fetch_assoc($dotazkom);

// formular na upravu komentare
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy\" bgcolor=\"#E6E6E6\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_TITULEK."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prtitulek\" value=\"".$pole_data['titulek']."\" size=\"63\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_AUTOR_JMENO."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prjmeno\" value=\"".$pole_data['od']."\" size=\"63\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_AUTOR_EMAIL."</b></td>
<td align=\"left\"><input type=\"text\" name=\"premail\" value=\"".$pole_data['od_mail']."\" size=\"63\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_AUTOR_IP."</b></td>
<td align=\"left\">".$pole_data['od_ip']."</td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=\"2\"><b>".RS_KOM_SK_FORM_OBS_KOMENT."</b><br>
<textarea name=\"probsah\" rows=\"14\" cols=\"85\" class=\"textbox\">".KorekceHTML($pole_data['obsah'])."</textarea></td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcEditComment\" /><input type=\"hidden\" name=\"modul\" value=\"comment\" />
<input type=\"hidden\" name=\"pridk\" value=\"".$pole_data['idk']."\" /><input type=\"hidden\" name=\"przpet\" value=\"".$GLOBALS["przpet"]."\" />
<input type=\"hidden\" name=\"prlink\" value=\"".$GLOBALS["prlink"]."\" /><input type=\"hidden\" name=\"prip\" value=\"".$GLOBALS["prip"]."\" />
<p align=\"center\"><i>".RS_KOM_SK_MAX_DELKA_KOMENT.": ".$GLOBALS['rsconfig']['max_delka_komentare']." ".RS_KOM_SK_ZNAKU."; ".RS_KOM_SK_MAX_VEL_SLOVO.": ".$GLOBALS['rsconfig']['max_delka_slova']." ".RS_KOM_SK_ZNAKU."</i></p>
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\" /> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\" /></p>
</form>\n";
}

function AcEditComment()
{
$GLOBALS["pridk"]=mysql_escape_string($GLOBALS["pridk"]);
$GLOBALS["przpet"]=mysql_escape_string($GLOBALS["przpet"]);
$GLOBALS["prlink"]=mysql_escape_string($GLOBALS["prlink"]);
$GLOBALS["prip"]=mysql_escape_string($GLOBALS["prip"]);
$GLOBALS["prtitulek"]=mysql_escape_string($GLOBALS["prtitulek"]);
$GLOBALS["prjmeno"]=mysql_escape_string($GLOBALS["prjmeno"]);
$GLOBALS["premail"]=mysql_escape_string($GLOBALS["premail"]);
$GLOBALS["probsah"]=KorekceVelikosti($GLOBALS["probsah"]); // korekce velikosti komentare
$GLOBALS["probsah"]=mysql_escape_string($GLOBALS["probsah"]);

// uprava komentare
$dotaz="update ".$GLOBALS["rspredpona"]."komentare set ";
$dotaz.="obsah='".$GLOBALS["probsah"]."', od='".$GLOBALS["prjmeno"]."', od_mail='".$GLOBALS["premail"]."', titulek='".$GLOBALS["prtitulek"]."' ";
$dotaz.="where idk='".$GLOBALS["pridk"]."'";

@$error=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
if (!$error):
  echo "<p align=\"center\" class=\"txt\">Error C1: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_KOM_SK_OK_EDIT_KOMENT."</p>\n";
endif;

// navrat
if ($GLOBALS["przpet"]=='link'):
  echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ViewComment&amp;modul=comment&amp;prlink=".$GLOBALS["prlink"]."\">".RS_KOM_SK_ZPET_KOMENT."</a></p>\n";
else:
  echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ViewIPComment&amp;modul=comment&amp;prip=".$GLOBALS["prip"]."\">".RS_KOM_SK_ZPET_KOMENT."</a></p>\n";
endif;
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowComment&amp;modul=comment\">".RS_KOM_SK_ZPET."</a></p>\n";
}

function ViewIPComment()
{
$GLOBALS["prip"]=mysql_escape_string($GLOBALS["prip"]);

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowComment&amp;modul=comment\">".RS_KOM_SK_ZPET."</a></p>\n";
// nadpis
echo "<p align=\"center\" class=\"txt\"><strong><big>".RS_KOM_SK_NADPIS_IP_ADR.": ".$GLOBALS["prip"]."</big></strong></p>\n";

// sestaveni dotazu
$dotaz="select k.*,c.titulek as cla_titulek from ".$GLOBALS["rspredpona"]."komentare as k,".$GLOBALS["rspredpona"]."clanky as c ";
$dotaz.="where k.od_ip='".$GLOBALS["prip"]."' and c.link=k.clanek order by idk desc";
// vypis nalezenych komentaru
$dotazkom=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetkom=mysql_num_rows($dotazkom);

if ($pocetkom==0):
  // Databáze neobsahuje žádný komentář odpovídající zadané IP adrese!
  echo "<p align=\"center\" class=\"txt\">".RS_KOM_SK_ZADNY_KOMENT_IP."</p>\n";
else:
  for ($pom=0;$pom<$pocetkom;$pom++):
    $pole_data=mysql_fetch_assoc($dotazkom);
    echo "<table cellspacing=\"0\" cellpadding=\"2\" border=\"0\" align=\"center\" width=\"700\" class=\"ramsedy\" bgcolor=\"#E6E6E6\">\n";
    echo "<tr class=\"txt\"><td align=\"left\" width=\"180\"><b>".RS_KOM_SK_FORM_DATUM.":</b></td><td align=\"left\" width=\"420\">".$pole_data["datum"]."</td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_NAZEV_CLA.":</b></td><td align=\"left\">".$pole_data["cla_titulek"]."</td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_AUTOR.":</b></td><td align=\"left\">".$pole_data["od"]." ";
    if ($pole_data["registrovany"]==1): echo '[<b>'.$pole_data["reg_prezdivka"].'</b>]'; else: echo '['.RS_KOM_SK_FORM_NEREG_CTENAR.']'; endif;
    echo "</td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_EMAIL.":</b></td><td align=\"left\"><a href=\"mailto:".$pole_data["od_mail"]."\">".$pole_data["od_mail"]."</a></td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_IP_ADR.":</b></td><td align=\"left\">".$pole_data["od_ip"]."</td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_TITULEK.":</b></td><td align=\"left\">".$pole_data["titulek"]."</td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"left\" colspan=\"2\"><hr /><b>".RS_KOM_SK_FORM_OBS_KOMENT.":</b><br /><div class=\"smltxt\">".$pole_data["obsah"]."</div><hr /></td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"center\" colspan=\"2\">";
    echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=EditComment&amp;modul=comment&amp;pridk=".$pole_data["idk"]."&amp;przpet=ip&amp;prip=".$pole_data["od_ip"]."&amp;prlink=".$pole_data["clanek"]."\">".RS_KOM_SK_FORM_EDIT_KOMENT."</a> - ";
    echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=DelComment&amp;modul=comment&amp;pridk=".$pole_data["idk"]."&amp;przpet=ip&amp;prip=".$pole_data["od_ip"]."&amp;prlink=".$pole_data["clanek"]."\">".RS_KOM_SK_FORM_DEL_OBSAH."</a> - ";
    echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=HardDelComment&amp;modul=comment&amp;pridk=".$pole_data["idk"]."&amp;przpet=ip&amp;prip=".$pole_data["od_ip"]."&amp;prlink=".$pole_data["clanek"]."\">".RS_KOM_SK_FORM_DEL_KOMENT."</a>";
    echo "</td></tr>\n";
    echo "</table>\n";
    echo "<p></p>\n";
  endfor;
endif;
echo "<p></p>\n";
}
?>
