<?

######################################################################
# phpRS Administration Engine - Topic's section 1.2.9
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_topic

/*
  Tento soubor zajistuje definici rubrik/temat.
*/

if ($Uzivatel->StavSession!=1):
  echo "<html><body><div align=\"center\">Tento soubor neni urcen k vnejsimu spousteni!</div></body></html>";
  exit;
endif;

// ---[rozcestnik]------------------------------------------------------------------
switch($GLOBALS['akce']):
     // temata
     case "ShowTopic": AdminMenu();
          echo "<h2 align=\"center\">".RS_TOP_ROZ_VIEW_TEMA."</h2><p align=\"center\">";
          ShowTopic();
          break;
     case "AddTopic": AdminMenu();
          echo "<h2 align=\"center\">".RS_TOP_ROZ_ADD_TEMA."</h2><p align=\"center\">";
          AddTopic();
          break;
     case "AcAddTopic": AdminMenu();
          echo "<h2 align=\"center\">".RS_TOP_ROZ_ADD_TEMA."</h2><p align=\"center\">";
          AcAddTopic();
          break;
     case "DelTopic": AdminMenu();
          echo "<h2 align=\"center\">".RS_TOP_ROZ_DEL_TEMA."</h2><p align=\"center\">";
          DelTopic();
          break;
     case "EditTopic": AdminMenu();
          echo "<h2 align=\"center\">".RS_TOP_ROZ_EDIT_TEMA."</h2><p align=\"center\">";
          EditTopic();
          break;
     case "AcEditTopic": AdminMenu();
          echo "<h2 align=\"center\">".RS_TOP_ROZ_EDIT_TEMA."</h2><p align=\"center\">";
          AcEditTopic();
          break;
endswitch;
echo "</p>\n"; // zakonceni P tagu

// ---[hlavni fce]------------------------------------------------------------------

/*
  ShowTopic()
  AddTopic()
  AcAddTopic()
  DelTopic()
  EditTopic()
  AcEditTopic()
*/

function ShowTopic()
{
// linky
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=AddTopic&amp;modul=topic\">".RS_TOP_SR_PRIDAT_ANKETU."</a></p>\n";

// vystupni pole: 0 - id prkvu, 1 - nazev prvku, 2 - cislo urovne
$poletopic=GenerujSeznam();
if (!is_array($poletopic)): // neexistuji zadne rubriky
  echo "<p align=\"center\" class=\"txt\">".RS_TOP_SR_ZADNE_TEMA."</p>\n";
else:
  $pocettopic=count($poletopic); // pocet prvku v poli
  echo "<table border=\"0\" align=\"center\">\n";
  for ($pom=0;$pom<$pocettopic;$pom++):
    echo "<tr class=\"txt\"><td align=\"left\">";
    echo Me($poletopic[$pom][2],3);
    if ($poletopic[$pom][2]>0): echo "<img src=\"image/strom_c.gif\" width=\"11\" height=\"11\" align=\"middle\" />&nbsp;"; endif; // je kdyz je vetsi nez 0
    echo "<b>".$poletopic[$pom][1]."</b> [<a href=\"".RS_VYKONNYSOUBOR."?akce=EditTopic&amp;modul=topic&amp;pridt=".$poletopic[$pom][0]."\">".RS_TOP_SR_UPRAVIT."</a>]";
    echo "[<a href=\"".RS_VYKONNYSOUBOR."?akce=DelTopic&amp;modul=topic&amp;pridt=".$poletopic[$pom][0]."\">".RS_TOP_SR_SMAZ."</a>]</td></tr>\n";
  endfor;
  echo "</table>\n";
endif;
}

function AddTopic()
{
// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowTopic&amp;modul=topic\">Zpět na hlavní stránku sekce</a></p>\n";

// formular
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" bgcolor=\"#E6E6E6\" class=\"ramsedy\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_TOP_SR_FORM_NAZEV."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prnazev\" size=\"40\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=\"2\">

<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
<tr class=\"txt\"><td align=\"left\" valign=\"top\"><b>".RS_TOP_SR_FORM_POPIS."</b><br />
<textarea name=\"prpopis\" rows=\"8\" cols=\"50\" class=\"textbox\">".RS_TOP_SR_FORM_POPIS_INFO."</textarea></td>
<td align=\"left\" valign=\"top\">&nbsp;&nbsp;&nbsp;</td>
<td align=\"left\" valign=\"top\"><b>".RS_TOP_SR_FORM_POLOHA."</b><br />
<select size=\"7\" name=\"prpoloha\" class=\"sezobory\">
<option value=\"0-0\">".RS_TOP_SR_FORM_POLOHA_ZAKLAD."</option>\n";

// pridani rubriky:  uroven_vnoreni-id_predka
$poletopic=GenerujSeznam();
if (is_array($poletopic)):
  $pocettopic=count($poletopic);
  for ($pom=0;$pom<$pocettopic;$pom++):
    echo "<option value=\"".($poletopic[$pom][2]+1)."-".$poletopic[$pom][0]."\">";
    echo Me($poletopic[$pom][2],1);
    if ($poletopic[$pom][2]>0): echo "+ "; endif; // je kdyz je vetsi nez 0
    echo $poletopic[$pom][1]."</option>\n";
  endfor;
endif;

echo "</select>
</td></tr>
</table>

</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_TOP_SR_FORM_URL_OBR."</b></td>
<td align=\"left\">".$GLOBALS["baseadr"]." <input type=\"text\" name=\"probrazek\" value=\"image/topic/\" size=\"30\" class=\"textpole\" /></td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcAddTopic\" /><input type=\"hidden\" name=\"modul\" value=\"topic\" />
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_PRIDAT." \" class=\"tl\" /> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \"  class=\"tl\" />
</form>
<p></p>\n";
}

function AcAddTopic()
{
if (!isset($GLOBALS["prpoloha"])): $GLOBALS["prpoloha"]='0-0'; endif;

// bezpecnostni korekce
$GLOBALS["prnazev"]=mysql_escape_string($GLOBALS["prnazev"]);
$GLOBALS["prpopis"]=mysql_escape_string($GLOBALS["prpopis"]);
$GLOBALS["prpoloha"]=mysql_escape_string($GLOBALS["prpoloha"]);
$GLOBALS["probrazek"]=mysql_escape_string($GLOBALS["probrazek"]);

// dekompilace promenne $prpoloha na subcasti: level-id_predka
list($prlevel,$pridpredek)=explode('-',$GLOBALS["prpoloha"]);

if ($prlevel!=0):
  mysql_query("update ".$GLOBALS["rspredpona"]."topic set rodic='1' where idt='".$pridpredek."'",$GLOBALS["dbspojeni"]);
endif;

$dotaz="insert into ".$GLOBALS["rspredpona"]."topic ";
$dotaz.="values(null,'".$GLOBALS["prnazev"]."','".$GLOBALS["prpopis"]."','".$GLOBALS["probrazek"]."','0','".$prlevel."','0','".$pridpredek."')";

@$error=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
if (!$error):
  echo "<p align=\"center\" class=\"txt\">Error T3: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_TOP_SR_OK_ADD_TEMA."</p>\n"; // vse OK
endif;

// navrat
ShowTopic();
}

function DelTopic()
{
$chyba=0; // vse je OK

$GLOBALS["pridt"]=mysql_escape_string($GLOBALS["pridt"]);

// overeni existence podtemat
$dotazid=mysql_query("select count(idt) as pocet from ".$GLOBALS["rspredpona"]."topic where id_predka='".$GLOBALS["pridt"]."'",$GLOBALS["dbspojeni"]);
$existid=mysql_result($dotazid,0,"pocet");
// overeni existence clanku s timto tematem
$dotazcl=mysql_query("select count(tema) as pocet from ".$GLOBALS["rspredpona"]."clanky where tema='".$GLOBALS["pridt"]."'",$GLOBALS["dbspojeni"]);
$existcl=mysql_result($dotazcl,0,"pocet");
// zjisteni id cisla rodice
$dotazpr=mysql_query("select id_predka from ".$GLOBALS["rspredpona"]."topic where idt='".$GLOBALS["pridt"]."'",$GLOBALS["dbspojeni"]);
$cislopr=mysql_result($dotazpr,0,"id_predka");

if ($existcl>0):
  // CHYBA: Akci nelze provést, jelikož s tímto tématem jsou spolejny některé články!
  echo "<p align=\"center\" class=\"txt\">Error T1: ".RS_TOP_SR_ERR_AKTIVNI_TEMA."</p>\n";
  $chyba=1;
endif;
if ($existid>0):
  // CHYBA: Akci nelze provést, jelikož toto téma obsahuje další podtémata!
  echo "<p align=\"center\" class=\"txt\">Error T1: ".RS_TOP_SR_ERR_JE_RODIC."</p>\n";
  $chyba=1;
endif;

if ($chyba==0):
  @$error=mysql_query("delete from ".$GLOBALS["rspredpona"]."topic where idt='".$GLOBALS["pridt"]."'",$GLOBALS["dbspojeni"]);
  if (!$error):
    echo "<p align=\"center\" class=\"txt\">Error T2: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_TOP_SR_OK_DEL_TEMA."</p>\n"; // vse OK
  endif;

  // overeni existence rodicovstvi
  $dotazok=mysql_query("select count(idt) as pocet from ".$GLOBALS["rspredpona"]."topic where id_predka='".$cislopr."'",$GLOBALS["dbspojeni"]);
  $existok=mysql_result($dotazok,0,"pocet");
  if ($existok==0): // zruseni rodicovstvi
    mysql_query("update ".$GLOBALS["rspredpona"]."topic set rodic='0' where idt='".$cislopr."'",$GLOBALS["dbspojeni"]);
  endif;
endif;

// navrat
ShowTopic();
}

function EditTopic()
{
$GLOBALS["pridt"]=mysql_escape_string($GLOBALS["pridt"]);

$dotaztema=mysql_query("select nazev,popis,obrazek,pocitadlo,level from ".$GLOBALS["rspredpona"]."topic where idt='".$GLOBALS["pridt"]."'",$GLOBALS["dbspojeni"]);
$poleprom=mysql_fetch_assoc($dotaztema);

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowTopic&amp;modul=topic\">Zpět na hlavní stránku sekce</a></p>\n";

echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" bgcolor=\"#E6E6E6\" class=\"ramsedy\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_TOP_SR_FORM_NAZEV."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prnazev\" size=\"40\" class=\"textpole\" value=\"".$poleprom["nazev"]."\" /></td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=\"2\">

<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
<tr class=\"txt\"><td align=\"left\" valign=\"top\"><b>".RS_TOP_SR_FORM_POPIS."</b><br />
<textarea name=\"prpopis\" rows=\"8\" cols=\"50\" class=\"textbox\">".KorekceHTML($poleprom["popis"])."</textarea></td>
<td align=\"left\" valign=\"top\">&nbsp;&nbsp;&nbsp;</td>
<td align=\"left\" valign=\"top\"><b>".RS_TOP_SR_FORM_AKT_OBR."</b><br />\n";
if ($poleprom["obrazek"]!=""):
  echo "<img src=\"".$poleprom["obrazek"]."\" alt=\"obrázek / image\" />";
endif;
echo "</td></tr>
</table>

</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_TOP_SR_FORM_URL_OBR."</b></td>
<td align=\"left\">".$GLOBALS["baseadr"]." <input type=\"text\" name=\"probrazek\" value=\"".$poleprom["obrazek"]."\" size=\"30\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=left><b>".RS_TOP_SR_FORM_VNORENI."</b></td>
<td align=\"left\">";
if ($poleprom["level"]==0):
  echo RS_TOP_SR_FORM_VNORENI_ZAKLAD; // zakladni uroveni
else:
  echo $poleprom["level"].". ".RS_TOP_SR_FORM_VNORENI_DALSI; // X. uroven
endif;
echo "</td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcEditTopic\" /><input type=\"hidden\" name=\"pridt\" value=\"".$GLOBALS["pridt"]."\" />
<input type=\"hidden\" name=\"modul\" value=\"topic\" />
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\" /> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \"  class=\"tl\" />
</form>
<p></p>\n";
}

function AcEditTopic()
{
$GLOBALS["pridt"]=mysql_escape_string($GLOBALS["pridt"]);
$GLOBALS["prnazev"]=mysql_escape_string($GLOBALS["prnazev"]);
$GLOBALS["prpopis"]=mysql_escape_string($GLOBALS["prpopis"]);
$GLOBALS["probrazek"]=mysql_escape_string($GLOBALS["probrazek"]);

@$error=mysql_query("update ".$GLOBALS["rspredpona"]."topic set nazev='".$GLOBALS["prnazev"]."', popis='".$GLOBALS["prpopis"]."', obrazek='".$GLOBALS["probrazek"]."' where idt='".$GLOBALS["pridt"]."'",$GLOBALS["dbspojeni"]);
if (!$error):
  echo "<p align=\"center\" class=\"txt\">Error T4: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_TOP_SR_OK_EDIT_TEMA."</p>\n"; // vse OK
endif;

// navrat
ShowTopic();
}
?>
