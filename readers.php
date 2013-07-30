<?
######################################################################
# phpRS Readers 1.5.3
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_ctenari

/*
  Pro funkcnost automatickeho rozpoznani prihlaseni je potreba trida CMyReader(), ktera je soucasti souboru "myweb.php".
*/

define('IN_CODE',true); // inic. ochranne konstanty

include_once("config.php");
include_once("specfce.php");
include_once("myweb.php");
include_once("sl.php");
include_once("trlayout.php");
include_once($adrlayoutu);

// overeni existence potrebnych promennych
if (!isset($GLOBALS['akce'])): $GLOBALS['akce']='logmenu'; endif;
// test na automaticke prihlaseni
if ($GLOBALS['akce']=='logmenu'):
  if ($prmyctenar->ctenarstav==1):
    $GLOBALS['akce']='autologin';
  endif;
endif;
// inic.
$GLOBALS['cte_modul_text']='';

// generovani ctenarskeho cookies; overeni tohoto cookies probiha v souboru myweb.php - trida CMyReader
function CtCook($idsess = 0,$ctsess = '')
{
$readerphprs=base64_encode('rs3*::*'.$idsess.'*::*'.$ctsess);
setcookie('readerphprs',$readerphprs,time()+864000);
}

function KorekceHTML($txt = '')
{
// tento radek umoznuje spravne zobrazit v editacnim poli vsechny zvlastni znaky zapsane jako &X;
return str_replace ('&','&amp;',$txt);
}

// zakladni login menu
function ZobrazLogin()
{
echo "<form action=\"readers.php\" method=\"post\">
<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
<tr class=\"z\"><td>".RS_CT_JMENO.":</td><td><input type=\"text\" size=\"15\" name=\"rjmeno\" class=\"textpole\" /></td></tr>
<tr class=\"z\"><td>".RS_CT_HESLO.":</td><td><input type=\"password\" size=\"15\" name=\"rheslo\" class=\"textpole\" /></td></tr>
</table>
<p align=\"center\"><input type=\"submit\" value=\"  ".RS_ODESLAT."  \" class=\"tl\" /></p>
<p align=\"center\" class=\"z\">
<a href=\"readers.php?akce=new\">".RS_CT_NAVIG_REG_NOVY."</a> -
<a href=\"readers.php?akce=del\">".RS_CT_NAVIG_ZRUSIT."</a> -
<a href=\"readers.php?akce=newpw\">".RS_CT_NAVIG_ZAPOMNEL."</a>
</p>
<input type=\"hidden\" name=\"akce\" value=\"login\" />
</form>
<p></p>\n";
}

// registrace noveho ctenare
function NovyCt()
{
// inic.
if (!isset($GLOBALS["rjmeno"])): $GLOBALS["rjmeno"]=''; endif;
if (!isset($GLOBALS["rheslo"])): $GLOBALS["rheslo"]=''; endif;
if (!isset($GLOBALS["rcelejmeno"])): $GLOBALS["rcelejmeno"]=''; endif;
if (!isset($GLOBALS["rmail"])): $GLOBALS["rmail"]='@'; endif;
if (!isset($GLOBALS["rjazyk"])): $GLOBALS["rjazyk"]='cz'; endif;
if (!isset($GLOBALS["rinfo"])): $GLOBALS["rinfo"]=1; endif;
if (!isset($GLOBALS["robsahmenu"])): $GLOBALS["robsahmenu"]=''; endif;
if (!isset($GLOBALS["rzobrazmenu"])): $GLOBALS["rzobrazmenu"]=''; endif;

$GLOBALS["typakce"]='insert'; // definice modu
$GLOBALS["typtlacitka"]=RS_CT_TL_ZAREG; // text tlacitko: Zaregistrovat

FormCtenari(); // formular
}

// uprava nastaveni ctenare
function EditujCt()
{
// bezp. kontrola
$GLOBALS["rjmeno"]=mysql_escape_string($GLOBALS["rjmeno"]);
$GLOBALS["rheslo"]=mysql_escape_string($GLOBALS["rheslo"]);
$GLOBALS["rheslo"]=md5($GLOBALS["rheslo"]); // heslo je sifrovane

$dotazcte=mysql_query("select idc,jmeno,email,info,data,visible,jazyk from ".$GLOBALS["rspredpona"]."ctenari where prezdivka='".$GLOBALS["rjmeno"]."' and password='".$GLOBALS["rheslo"]."'",$GLOBALS["dbspojeni"]);
$pocetcte=mysql_num_rows($dotazcte);

if ($pocetcte==0):
  // CHYBA: Špatné uživatelké jméno nebo heslo! - Nové přihlášení
  echo "<p align=\"center\" class=\"z\"><b>".RS_CT_ERR_1." - <a href=\"readers.php\">".RS_CT_NOVY_LOGIN."</a></b></p>\n";
else:
  $ctenardata=mysql_fetch_assoc($dotazcte); // nacteni dat

  $GLOBALS["ridc"]=$ctenardata["idc"];
  //$GLOBALS["rjmeno"] // zadana pri logovani
  //$GLOBALS["rheslo"] // zadana pri logovani
  $GLOBALS["rcelejmeno"]=$ctenardata["jmeno"];
  $GLOBALS["rmail"]=$ctenardata["email"];
  $GLOBALS["rjazyk"]=$ctenardata["jazyk"];
  $GLOBALS["rinfo"]=$ctenardata["info"];
  $GLOBALS["robsahmenu"]=$ctenardata["data"];
  $GLOBALS["rzobrazmenu"]=$ctenardata["visible"];

  $GLOBALS["typakce"]='save'; // definice modu
  $GLOBALS["typtlacitka"]=RS_CT_TL_ULOZ; // text tlatictko: Uložit změny

  FormCtenari(); // formular
endif;
}

// uprava nastaveni ctenare - automaticky rezim prihlaseni
function EditujAutoCt()
{
if ($GLOBALS["prmyctenar"]->ctenarstav==1):
  $akt_id_ctenar=$GLOBALS["prmyctenar"]->Ukaz("id");
  $akt_prezdivka=$GLOBALS["prmyctenar"]->Ukaz("username");

  $dotazcte=mysql_query("select idc,prezdivka,password,jmeno,email,info,data,visible,jazyk from ".$GLOBALS["rspredpona"]."ctenari where idc='".$akt_id_ctenar."' and prezdivka='".$akt_prezdivka."'",$GLOBALS["dbspojeni"]);
  $pocetcte=mysql_num_rows($dotazcte);

  if ($pocetcte==0):
    // CHYBA: Systém nemůže identifikovat čtenáře! - Nové přihlášení
    echo "<p align=\"center\" class=\"z\"><b>".RS_CT_ERR_2." - <a href=\"readers.php\">".RS_CT_NOVY_LOGIN."</a></b></p>\n";
  else:
    $ctenardata=mysql_fetch_assoc($dotazcte); // nacteni dat

    $GLOBALS["ridc"]=$ctenardata["idc"];
    $GLOBALS["rjmeno"]=$ctenardata["prezdivka"];
    $GLOBALS["rheslo"]=$ctenardata["password"];
    $GLOBALS["rcelejmeno"]=$ctenardata["jmeno"];
    $GLOBALS["rmail"]=$ctenardata["email"];
    $GLOBALS["rjazyk"]=$ctenardata["jazyk"];
    $GLOBALS["rinfo"]=$ctenardata["info"];
    $GLOBALS["robsahmenu"]=$ctenardata["data"];
    $GLOBALS["rzobrazmenu"]=$ctenardata["visible"];

    $GLOBALS["typakce"]='save'; // // definice modu
   $GLOBALS["typtlacitka"]=RS_CT_TL_ULOZ; // text tlatictko: Uložit změny

    FormCtenari(); // formular
  endif;

else:
  // CHYBA: Systém nemůže identifikovat čtenáře! - Nové přihlášení
  echo "<p align=\"center\" class=\"z\"><b>".RS_CT_ERR_2." - <a href=\"readers.php\">".RS_CT_NOVY_LOGIN."</a></b></p>\n";
endif;
}

function FormCtenari()
{
echo "<form action=\"readers.php\" method=\"post\">
<input type=\"hidden\" name=\"akce\" value=\"".$GLOBALS["typakce"]."\" />\n";
if ($GLOBALS["typakce"]=='save'): // jen save modu
  echo "<input type=\"hidden\" name=\"ridc\" value=\"".$GLOBALS["ridc"]."\" />\n<input type=\"hidden\" name=\"roldpass\" value=\"".$GLOBALS["rheslo"]."\" />\n";
endif;
if ($GLOBALS['cte_modul_text']!=''): // test na pritomnost komentare
  echo $GLOBALS['cte_modul_text'];
endif;
echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\n";
if ($GLOBALS["typakce"]=='save'): // jen save modu
  echo "<tr class=\"z\"><td>".RS_CT_JMENO.":</td><td>".$GLOBALS["rjmeno"]."<input type=\"hidden\" name=\"rjmeno\" value=\"".$GLOBALS["rjmeno"]."\" /></td></tr>\n";
  echo "<tr class=\"z\"><td>".RS_CT_HESLO.":</td><td><input type=\"password\" size=\"15\" name=\"rheslo\" value=\"\" class=\"textpole\" /><br />".RS_CT_INFO_HESLO."</td></tr>\n";
  echo "<tr class=\"z\"><td>".RS_CT_HESLO_KONTRLA.":</td><td><input type=\"password\" size=\"15\" name=\"rheslo2\" value=\"\" class=\"textpole\" /><br />".RS_CT_INFO_HESLO."</td></tr>\n";
else: // ostatni mody
  echo "<tr class=\"z\"><td>".RS_CT_JMENO.":</td><td><input type=\"text\" size=\"15\" name=\"rjmeno\" value=\"".$GLOBALS["rjmeno"]."\" class=\"textpole\" /></td></tr>\n";
  echo "<tr class=\"z\"><td>".RS_CT_HESLO.":</td><td><input type=\"password\" size=\"15\" name=\"rheslo\" value=\"\" class=\"textpole\" /></td></tr>\n";
  echo "<tr class=\"z\"><td>".RS_CT_HESLO_KONTRLA.":</td><td><input type=\"password\" size=\"15\" name=\"rheslo2\" value=\"\" class=\"textpole\" /></td></tr>\n";
endif;
echo "<tr class=\"z\"><td>".RS_CT_CELE_JMENO.":</td><td><input type=\"text\" size=\"40\" name=\"rcelejmeno\" value=\"".$GLOBALS["rcelejmeno"]."\" class=\"textpole\" /></td></tr>
<tr class=\"z\"><td>".RS_CT_EMAIL.":</td><td><input type=\"text\" size=\"40\" name=\"rmail\" value=\"".$GLOBALS["rmail"]."\" class=\"textpole\" /></td></tr>
<tr class=\"z\"><td>".RS_CT_JAZYK.":</td><td><select name=\"rjazyk\" size=\"1\">";
$adr = dir("lang");
while($prehledj=$adr->read()):
  if (substr($prehledj,0,3)=="sl_"):
    $jazykzkratka=substr($prehledj,3,2);
    echo "<option value=\"".$jazykzkratka."\"";
    if ($jazykzkratka==$GLOBALS["rjazyk"]): echo " selected"; endif; // test na shodu
    echo ">".$jazykzkratka."</option>";
  endif;
endwhile;
$adr->close();
echo "</select></td></tr>
</table>
<p align=\"center\" class=\"z\">".RS_CT_NOVINKY."<br />\n";
if ($GLOBALS["rinfo"]==1):
  echo "<input type=\"radio\" name=\"rinfo\" value=\"1\" checked />".RS_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"rinfo\" value=\"0\" />".RS_NE."\n";
else:
  echo "<input type=\"radio\" name=\"rinfo\" value=\"1\" />".RS_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"rinfo\" value=\"0\" checked />".RS_NE."\n";
endif;
echo "</p>
<p align=\"center\" class=\"z\">".RS_CT_VLASTNI_MENU.":<br />
<textarea name=\"robsahmenu\" cols=\"60\" rows=\"6\" wrap=\"yes\" class=\"textbox\">".KorekceHTML($GLOBALS["robsahmenu"])."</textarea></p>
<p align=\"center\" class=\"z\">".RS_CT_CHCI_ME_MENU."<br />\n";
if ($GLOBALS["rzobrazmenu"]==1):
  echo "<input type=\"radio\" name=\"rzobrazmenu\" value=\"1\" checked />".RS_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"rzobrazmenu\" value=\"0\" />".RS_NE."\n";
else:
  echo "<input type=\"radio\" name=\"rzobrazmenu\" value=\"1\" />".RS_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"rzobrazmenu\" value=\"0\" checked />".RS_NE."\n";
endif;
echo "</p>
<p align=\"center\"><input type=\"submit\" value=\" ".$GLOBALS["typtlacitka"]." \" class=\"tl\" />&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"reset\" value=\" Reset \" class=\"tl\" /></p>\n";
if ($GLOBALS["typakce"]=='insert'): // jen pri insert modu
  echo "<p align=\"center\" class=\"z\"><i>".RS_CT_INFO_POVINNA."</i></p>\n";
endif;
echo "</form>
<p></p>\n";
}

// pridani noveho ctenare
function PridejCt()
{
// bezp. kontrola
$GLOBALS["rjmeno"]=mysql_escape_string($GLOBALS["rjmeno"]);
$GLOBALS["rheslo"]=mysql_escape_string($GLOBALS["rheslo"]);
$GLOBALS["rheslo2"]=mysql_escape_string($GLOBALS["rheslo2"]);
$GLOBALS["rcelejmeno"]=mysql_escape_string($GLOBALS["rcelejmeno"]);
$GLOBALS["rmail"]=mysql_escape_string($GLOBALS["rmail"]);
$GLOBALS["rjazyk"]=mysql_escape_string($GLOBALS["rjazyk"]);
$GLOBALS["rinfo"]=mysql_escape_string($GLOBALS["rinfo"]);
$GLOBALS["robsahmenu"]=mysql_escape_string($GLOBALS["robsahmenu"]);
$GLOBALS["rzobrazmenu"]=mysql_escape_string($GLOBALS["rzobrazmenu"]);

// inic. chyba
$chyba=0;

// test na pritomnost vsech povinnych poli
if ($GLOBALS["rjmeno"]==''||$GLOBALS["rheslo"]==''):
  // CHYBA: Některé z povinných polí je prádné!
  $GLOBALS['cte_modul_text'].="<p align=\"center\" class=\"chybastredni\">".RS_CT_ERR_3."</p>\n";
  $GLOBALS['akce']='new'; // zmena akce
  $chyba=1; // chyba
endif;
// overeni jedinecnosti zvoleneho username
$dotazjmeno=mysql_query("select idc from ".$GLOBALS["rspredpona"]."ctenari where prezdivka='".$GLOBALS["rjmeno"]."'",$GLOBALS["dbspojeni"]);
$pocetjmeno=mysql_num_rows($dotazjmeno);
if ($pocetjmeno>0):
  // CHYBA: Vámi vybraná přezdívka X je již obsazená! Zvolte si jinou.
  $GLOBALS['cte_modul_text'].="<p align=\"center\" class=\"chybastredni\">".RS_CT_ERR_4_A." \"".$GLOBALS["rjmeno"]."\" ".RS_CT_ERR_4_B."</p>\n";
  $GLOBALS['akce']='new'; // zmena akce
  $chyba=1; // chyba
endif;
// test na shodu HESLA s jeho kontrolnim zadanim
if ($GLOBALS["rheslo"]!=$GLOBALS["rheslo2"]):
  // CHYBA: Zadaná hesla nejsou shodná!
  $GLOBALS['cte_modul_text'].="<p align=\"center\" class=\"chybastredni\">".RS_CT_ERR_12."</p>\n";
  $GLOBALS['akce']='new'; // zmena akce
  $chyba=1; // chyba
endif;

if ($chyba==0): // test na bezchybny stav
  // vlozeni noveho ctenare do databaze
  $dnesnidatum=date("Y-m-d H:i:s");
  $GLOBALS["rheslo"]=md5($GLOBALS["rheslo"]); // heslo je sifrovane
  @$dotazctenar=mysql_query("insert into ".$GLOBALS["rspredpona"]."ctenari values (null,'".$GLOBALS["rjmeno"]."','".$GLOBALS["rheslo"]."','".$GLOBALS["rcelejmeno"]."','".$GLOBALS["rmail"]."','".$dnesnidatum."','0','".$GLOBALS["rinfo"]."','".$GLOBALS["robsahmenu"]."','".$GLOBALS["rzobrazmenu"]."','".$GLOBALS["rjazyk"]."','".$dnesnidatum."')",$GLOBALS["dbspojeni"]);
  if ($dotazctenar==0):
    // chyba pri vkladani noveho ctenare
    // CHYBA: V průběhu zakládání vašeho profilu došlo k neočekávané chybě. Zopakujte akci.
    $GLOBALS['cte_modul_text'].="<p align=\"center\" class=\"z\">".RS_CT_ERR_5."</p>\n";
    $GLOBALS['akce']='showtxt'; // zmena akce
  else:
    // zjisteni id noveho ctenare
    $dotazid=mysql_query("select idc from ".$GLOBALS["rspredpona"]."ctenari where prezdivka='".$GLOBALS["rjmeno"]."' and password='".$GLOBALS["rheslo"]."'",$GLOBALS["dbspojeni"]);
    if (mysql_num_rows($dotazid)==1):
      $cisloctenare=mysql_result($dotazid,0,"idc"); // id ctenare
    else:
      $cisloctenare=0; // chyba id ctenare
    endif;
    $budoucidatum=Date("Y-m-d H:i:s",time()+864000); // vypocet platnosti session
    $rsession=md5($budoucidatum.$GLOBALS["rjmeno"].$GLOBALS["rheslo"].$GLOBALS["rcelejmeno"].$GLOBALS["rmail"]); // generovani session
    // ulozeni nove session
    @$dotazsession=mysql_query("insert into ".$GLOBALS["rspredpona"]."cte_session values (null,'".$rsession."','".$cisloctenare."','".$budoucidatum."')",$GLOBALS["dbspojeni"]);
    if ($dotazsession==1):
      // zjisteni id session
      $dotazidsession=mysql_query("select ids from ".$GLOBALS["rspredpona"]."cte_session where session='".$rsession."' and id_cte='".$cisloctenare."'",$GLOBALS["dbspojeni"]);
      $cislosession=mysql_result($dotazidsession,0,"ids"); // id session
      // generovani cookies
      CtCook($cislosession,$rsession);
      // finalni hlaseni - Vaše registrace byla úspěšně dokončena.
      $GLOBALS['cte_modul_text'].="<p align=\"center\" class=\"z\"><b>".RS_CT_REG_VSE_OK."</b></p>\n";
      $GLOBALS['cte_modul_text'].="<p align=\"center\" class=\"z\"><a href=\"index.php\">".RS_CT_HL_STR."</a> - <a href=\"readers.php\">".RS_CT_OSOBNI_UCET."</a></p>\n";
      $GLOBALS['akce']='showtxt'; // zmena akce
    else:
      // chyba pri vkladani ct. session
      // CHYBA: Neočekávaná chyba!
      $GLOBALS['cte_modul_text'].="<p align=\"center\" class=\"z\">".RS_CT_ERR_6."</p>\n";
      $GLOBALS['akce']='showtxt'; // zmena akce
    endif;
  endif;
endif;
}

// ulozeni nove konfigurace ctenare
function UlozCt()
{
// bezp. kontrola
$GLOBALS["rjmeno"]=mysql_escape_string($GLOBALS["rjmeno"]);
$GLOBALS["roldpass"]=mysql_escape_string($GLOBALS["roldpass"]);
$GLOBALS["ridc"]=mysql_escape_string($GLOBALS["ridc"]);
$GLOBALS["rheslo"]=mysql_escape_string($GLOBALS["rheslo"]);
$GLOBALS["rheslo2"]=mysql_escape_string($GLOBALS["rheslo2"]);
$GLOBALS["rcelejmeno"]=mysql_escape_string($GLOBALS["rcelejmeno"]);
$GLOBALS["rmail"]=mysql_escape_string($GLOBALS["rmail"]);
$GLOBALS["rjazyk"]=mysql_escape_string($GLOBALS["rjazyk"]);
$GLOBALS["rinfo"]=mysql_escape_string($GLOBALS["rinfo"]);
$GLOBALS["robsahmenu"]=mysql_escape_string($GLOBALS["robsahmenu"]);
$GLOBALS["rzobrazmenu"]=mysql_escape_string($GLOBALS["rzobrazmenu"]);

// inic. chyba
$chyba=0;

// test na pritomnost vsech povinnych poli
if ($GLOBALS["rjmeno"]==''):
  // CHYBA: Některé z povinných polí je prádné!
  $GLOBALS['cte_modul_text'].="<p align=\"center\" class=\"chybastredni\">".RS_CT_ERR_3."</p>\n";
  $GLOBALS['akce']='showtxt'; // zmena akce
  $chyba=1; // chyba
endif;
// overeni jedinecnosti zvoleneho username - z kontroly je odstranet samotny prihlaseny ctenar
$dotazjmeno=mysql_query("select idc from ".$GLOBALS["rspredpona"]."ctenari where prezdivka='".$GLOBALS["rjmeno"]."' and idc!='".$GLOBALS["ridc"]."'",$GLOBALS["dbspojeni"]);
$pocetjmeno=mysql_num_rows($dotazjmeno);
if ($pocetjmeno>0):
  // CHYBA: Vámi vybraná přezdívka X je již obsazená! Zvolte si jinou.
  $GLOBALS['cte_modul_text'].="<p align=\"center\" class=\"chybastredni\">".RS_CT_ERR_4_A." \"".$GLOBALS["rjmeno"]."\" ".RS_CT_ERR_4_B."</p>\n";
  $GLOBALS['akce']='showtxt'; // zmena akce
  $chyba=1; // chyba
endif;
// test na shodu HESLA s jeho kontrolnim zadanim
if ($GLOBALS["rheslo"]!=$GLOBALS["rheslo2"]):
  // CHYBA: Zadaná hesla nejsou shodná!
  $GLOBALS['cte_modul_text'].="<p align=\"center\" class=\"chybastredni\">".RS_CT_ERR_12."</p>\n";
  $GLOBALS['akce']='showtxt'; // zmena akce
  $chyba=1; // chyba
endif;

if ($chyba==0): // test na bezchybny stav
  // overeni existence upravovaneho ctenarskeho profilu
  $dotazjmeno=mysql_query("select idc from ".$GLOBALS["rspredpona"]."ctenari where idc='".$GLOBALS["ridc"]."' and password='".$GLOBALS["roldpass"]."'",$GLOBALS["dbspojeni"]);
  $pocetjmeno=mysql_num_rows($dotazjmeno);
  if ($pocetjmeno==1): // kontrola existence, vse OK
    // uprava existujiciho ctenare
    $dnesnidatum=date("Y-m-d H:i:s");
    // priprava hesla
    if ($GLOBALS["rheslo"]==''):
      $GLOBALS["rheslo"]=$GLOBALS["roldpass"]; // heslo je sifrovane; pouzito puvodni
    else:
      $GLOBALS["rheslo"]=md5($GLOBALS["rheslo"]); // heslo je sifrovane
    endif;
    // kvuli zakazu zmeny prezdivky odstranena cast SQL prikazu: prezdivka='".$GLOBALS["rjmeno"]."'
    @$dotazctenar=mysql_query("update ".$GLOBALS["rspredpona"]."ctenari set password='".$GLOBALS["rheslo"]."',jmeno='".$GLOBALS["rcelejmeno"]."',email='".$GLOBALS["rmail"]."',info='".$GLOBALS["rinfo"]."',data='".$GLOBALS["robsahmenu"]."',visible='".$GLOBALS["rzobrazmenu"]."',jazyk='".$GLOBALS["rjazyk"]."',posledni_login='".$dnesnidatum."' where idc='".$GLOBALS["ridc"]."' and password='".$GLOBALS["roldpass"]."'",$GLOBALS["dbspojeni"]);
    if ($dotazctenar==0):
      // chyba pri aktualizacei ctenare
      // CHYBA: V průběhu úpravy vašeho profilu došlo k neočekávané chybě. Zopakujte akci.
      $GLOBALS['cte_modul_text'].="<p align=\"center\" class=\"z\">".RS_CT_ERR_7."</p>\n";
      $GLOBALS['akce']='showtxt'; // zmena akce
    else:
      $cisloctenare=$GLOBALS["ridc"]; // id ctenare
      $budoucidatum=Date("Y-m-d H:i:s",time()+864000); // vypocet platnosti session
      $rsession=md5($budoucidatum.$GLOBALS["rjmeno"].$GLOBALS["rheslo"].$GLOBALS["rcelejmeno"].$GLOBALS["rmail"]); // generovani session
      // ulozeni nove session
      @$dotazsession=mysql_query("insert into ".$GLOBALS["rspredpona"]."cte_session values (null,'".$rsession."','".$cisloctenare."','".$budoucidatum."')",$GLOBALS["dbspojeni"]);
      if ($dotazsession==1):
        // zjisteni id session
        $dotazidsession=mysql_query("select ids from ".$GLOBALS["rspredpona"]."cte_session where session='".$rsession."' and id_cte='".$cisloctenare."'",$GLOBALS["dbspojeni"]);
        $cislosession=mysql_result($dotazidsession,0,"ids"); // id session
        // generovani noveho cookies
        CtCook($cislosession,$rsession);
        // finalni hlaseni - Vaše osobní nastavení bylo aktualizováno.
        $GLOBALS['cte_modul_text'].="<p align=\"center\" class=\"z\">".RS_CT_EDIT_VSE_OK."</p>\n";
        $GLOBALS['akce']='showtxt'; // zmena akce
      else:
        // chyba pri vkladani ct. session
        // CHYBA: Neočekávaná chyba!
        $GLOBALS['cte_modul_text'].="<p align=\"center\" class=\"z\">".RS_CT_ERR_6."</p>\n";
        $GLOBALS['akce']='showtxt'; // zmena akce
      endif;
    endif; // konec $dotazctenar
  else:
    // chyba test na $pocetjmeno; upravovany ctenar neexistuje
    // CHYBA: Neočekávaná chyba!
    $GLOBALS['cte_modul_text'].="<p align=\"center\" class=\"z\">".RS_CT_ERR_6."</p>\n";
    $GLOBALS['akce']='showtxt'; // zmena akce
  endif;
endif;

// navrat
$GLOBALS['cte_modul_text'].="<p align=\"center\" class=\"z\"><a href=\"readers.php\">".RS_CT_NOVY_LOGIN."</a></p>\n<p></p>\n";
$GLOBALS['akce']='showtxt'; // zmena akce
}

function JenLogin()
{
// bezp. kontrola
$GLOBALS["rjmeno"]=mysql_escape_string($GLOBALS["rjmeno"]);
$GLOBALS["rheslo"]=md5(mysql_escape_string($GLOBALS["rheslo"])); // heslo je sifrovane

if (!isset($GLOBALS["nacti"])): $GLOBALS["nacti"]="index.php"; endif; // pro pripad doplneni domeny lze pouzit promenne $GLOBALS["baseadr"]

// overeni existence ctenarskeho profilu
$dotazjmeno=mysql_query("select idc,jmeno,email from ".$GLOBALS["rspredpona"]."ctenari where prezdivka='".$GLOBALS["rjmeno"]."' and password='".$GLOBALS["rheslo"]."'",$GLOBALS["dbspojeni"]);
$pocetjmeno=mysql_num_rows($dotazjmeno);
if ($pocetjmeno==1): // kontrola existence, vse OK
  // nacteni dat o ctenari
  $pole_akt_data=mysql_fetch_assoc($dotazjmeno);
  // zpracovani dat
  $cisloctenare=$pole_akt_data["idc"]; // id ctenare
  $GLOBALS["rcelejmeno"]=$pole_akt_data["jmeno"]; // cele jmeno ctenare
  $GLOBALS["rmail"]=$pole_akt_data["email"]; // e-mail ctenare
  $dnesnidatum=date("Y-m-d H:i:s");
  $budoucidatum=date("Y-m-d H:i:s",time()+864000); // vypocet platnosti session
  $rsession=md5($budoucidatum.$GLOBALS["rjmeno"].$GLOBALS["rheslo"].$GLOBALS["rcelejmeno"].$GLOBALS["rmail"]); // generovani session
  // ulozeni nove session
  @$dotazsession=mysql_query("insert into ".$GLOBALS["rspredpona"]."cte_session values (null,'".$rsession."','".$cisloctenare."','".$budoucidatum."')",$GLOBALS["dbspojeni"]);
  if ($dotazsession==1):
    // aktualizace polozky "posledni_login"
    @mysql_query("update ".$GLOBALS["rspredpona"]."ctenari set posledni_login='".$dnesnidatum."' where idc='".$cisloctenare."' and prezdivka='".$GLOBALS["rjmeno"]."'",$GLOBALS["dbspojeni"]);
    // zjisteni id session
    $dotazidsession=mysql_query("select ids from ".$GLOBALS["rspredpona"]."cte_session where session='".$rsession."' and id_cte='".$cisloctenare."'",$GLOBALS["dbspojeni"]);
    $cislosession=mysql_result($dotazidsession,0,"ids"); // id session
    // generovani noveho cookies
    CtCook($cislosession,$rsession);
    // presmerovani na novou stranku
    header("Location: ".$GLOBALS["nacti"]);
    exit;
  else:
    // chyba pri generovani session
    // CHYBA: Neočekávaná chyba!
    $GLOBALS['cte_modul_text'].="<p align=\"center\" class=\"z\">".RS_CT_ERR_6."</p>\n";
    $GLOBALS['akce']='showtxt'; // zmena akce
  endif;
else:
  // chyba ctenar nenalezen
  // CHYBA: Špatné uživatelké jméno nebo heslo!
  $GLOBALS['cte_modul_text'].="<p align=\"center\" class=\"z\">".RS_CT_ERR_1."</p>\n";
  $GLOBALS['akce']='showtxt'; // zmena akce
endif;
}

function OdhlasCt()
{
if (!isset($GLOBALS["nacti"])): $GLOBALS["nacti"]="index.php"; endif; // pro pripad doplneni domeny lze pouzit promenne $GLOBALS["baseadr"]

if ($GLOBALS["prmyctenar"]->ctenarstav==1):
  mysql_query("delete from ".$GLOBALS["rspredpona"]."cte_session where ids='".$GLOBALS["prmyctenar"]->ctenaridsess."' and session='".$GLOBALS["prmyctenar"]->ctenarsess."'",$GLOBALS["dbspojeni"]);
  // presmerovani na novou stranku
  header("Location: ".$GLOBALS["nacti"]);
  exit;
endif;
}

function VymazatCt()
{
echo "<form action=\"readers.php\" method=\"post\">
<p align=\"center\" class=\"z\">".RS_CT_INFO_ZRUSIT."</p>
<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
<tr class=\"z\"><td>".RS_CT_JMENO.":</td><td><input type=\"text\" size=\"15\" name=\"rjmeno\" class=\"textpole\" /></td></tr>
<tr class=\"z\"><td>".RS_CT_HESLO.":</td><td><input type=\"password\" size=\"15\" name=\"rheslo\" class=\"textpole\" /></td></tr>
</table>
<p align=\"center\"><input type=\"submit\" value=\" ".RS_CT_TL_ZRUSIT." \" class=\"tl\" /></p>
<input type=\"hidden\" name=\"akce\" value=\"delreader\" />
</form>
<p></p>\n";
}

function AcVymazatCt()
{
// bezp. kontrola
$GLOBALS["rjmeno"]=mysql_escape_string($GLOBALS["rjmeno"]);
$GLOBALS["rheslo"]=mysql_escape_string($GLOBALS["rheslo"]);
$GLOBALS["rheslo"]=md5($GLOBALS["rheslo"]); // heslo je sifrovane

$dotazcte=mysql_query("select idc,prezdivka from ".$GLOBALS["rspredpona"]."ctenari where prezdivka='".$GLOBALS["rjmeno"]."' and password='".$GLOBALS["rheslo"]."'",$GLOBALS["dbspojeni"]);
$pocetcte=mysql_num_rows($dotazcte);

if ($pocetcte==0):
  // CHYBA: Špatné uživatelké jméno nebo heslo! - Nové přihlášení
  echo "<p align=\"center\" class=\"z\"><b>".RS_CT_ERR_1." - <a href=\"readers.php?akce=del\">".RS_CT_NOVY_LOGIN."</a></b></p>\n";
else:
  $ctenardata=mysql_fetch_assoc($dotazcte); // nacteni dat

  @$error=mysql_query("delete from ".$GLOBALS["rspredpona"]."ctenari where idc='".$ctenardata["idc"]."' and prezdivka='".$ctenardata["prezdivka"]."'",$GLOBALS["dbspojeni"]);
   if (!$error):
     // chyba pri ruseni registrace ctenare
     // CHYBA: V průběhu odstraňování registrace došlo k neočekávané chybě!
     echo "<p align=\"center\" class=\"chybastredni\">".RS_CT_ERR_8."</p>\n";
   else:
     // vse ok - Vaše registrace byla úspěšně zrušena!
     echo "<p align=\"center\" class=\"z\"><b>".RS_CT_DEL_VSE_OK."</b></p>\n";
   endif;
   // navrat
   echo "<p align=\"center\" class=\"z\"><a href=\"index.php\">".RS_CT_HL_STR."</a> - <a href=\"readers.php\">".RS_CT_OSOBNI_UCET."</a></p>\n";
endif;
}

function NoveHesloCt()
{
echo "<form action=\"readers.php\" method=\"post\">
<p align=\"center\" class=\"z\">".RS_CT_INFO_ZAPOMNEL."</p>
<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
<tr class=\"z\"><td>".RS_CT_JMENO.":</td><td><input type=\"text\" size=\"30\" name=\"rjmeno\" class=\"textpole\" /></td></tr>
<tr class=\"z\"><td>".RS_CT_DEL_EMAIL_ADR.":</td><td><input type=\"text\" size=\"30\" name=\"rmail\" class=\"textpole\" /></td></tr>
</table>
<p align=\"center\"><input type=\"submit\" value=\" ".RS_CT_TL_NOVE_HESLO." \" class=\"tl\" /></p>
<input type=\"hidden\" name=\"akce\" value=\"newpwsend\" />
</form>
<p></p>\n";
}

function AcNoveHesloCt()
{
// bezp. kontrola
$GLOBALS["rjmeno"]=mysql_escape_string($GLOBALS["rjmeno"]);
$GLOBALS["rmail"]=mysql_escape_string($GLOBALS["rmail"]);

$dotazcte=mysql_query("select idc,prezdivka,email from ".$GLOBALS["rspredpona"]."ctenari where prezdivka='".$GLOBALS["rjmeno"]."' and email='".$GLOBALS["rmail"]."'",$GLOBALS["dbspojeni"]);
$pocetcte=mysql_num_rows($dotazcte);

if ($pocetcte==0):
  // CHYBA: Špatné uživatelké jméno nebo e-mailová adresa! - Nové přihlášení
  echo "<p align=\"center\" class=\"z\"><b>".RS_CT_ERR_9." - <a href=\"readers.php?akce=newpw\">".RS_CT_NOVY_LOGIN."</a></b></p>\n";
else:
  // inic. generovani noveho hesla
  $pocitadlo=0;
  $nove_heslo='';
  $delka_hesla=6;
  $str_znaku="ABCDEFGHIJKLMNOPQRSTUVWXYZ"
            ."abcdefghijklmnopqrstuvwxyz"
            ."0123456789";
  $poc_str_znaku=strlen($str_znaku)-1; // odecita se navic 1, protoze s retezcem se dale pocita od 0

  // vygenerovani noveho hesla
  while($pocitadlo++ < $delka_hesla):
    $nove_heslo.=substr($str_znaku,mt_rand(0,$poc_str_znaku),1);
  endwhile;

  // nastaveni noveho hesla
  @$error=mysql_query("update ".$GLOBALS["rspredpona"]."ctenari set password='".md5($nove_heslo)."' where prezdivka='".$GLOBALS["rjmeno"]."' and email='".$GLOBALS["rmail"]."'",$GLOBALS["dbspojeni"]);
  if (!$error):
    // CHYBA: V průběhu ukládání nového hesla došlo k neočekávané chybě!
    echo "<p align=\"center\" class=\"chybastredni\">".RS_CT_ERR_10."</p>\n";
  else:
    // vse OK - Nastavení nového hesla bylo úspěšně provedeno!
    echo "<p align=\"center\" class=\"z\"><b>".RS_CT_NOVE_HESLO_VSE_OK."</b></p>\n";
  endif;

  // odeslani info e-mailu
  include_once('admin/astdlib_mail.php'); // vlozeni tridy CPosta()
  $postovni_sluzby = new CPosta();
  $postovni_sluzby->Nastav('adresat',$GLOBALS["rmail"]);
  $postovni_sluzby->Nastav('predmet',RS_CT_GNH_PREDMET);
  $postovni_sluzby->Nastav('obsah',RS_CT_GNH_OBS_1." \"".$GLOBALS["rjmeno"]."\" ".RS_CT_GNH_OBS_2.": ".$nove_heslo."\n");

  if ($postovni_sluzby->Odesilac()==1):
    // vse OK - Na vaši e-mailovou adresu byl úspěšně odeslán informační e-mail!
    echo "<p align=\"center\" class=\"z\"><b>".RS_CT_SEND_MAIL_VSE_OK."</b></p>\n";
  else:
    // CHYBA: V průběhu odesílání informačního e-mailu došlo k neočekávané chybě!
    echo "<p align=\"center\" class=\"chybastredni\"><b>".RS_CT_ERR_11."</b></p>\n";
  endif;

  // navrat
  echo "<p align=\"center\" class=\"z\"><a href=\"index.php\">".RS_CT_HL_STR."</a> - <a href=\"readers.php\">".RS_CT_OSOBNI_UCET."</a></p>\n";
endif;
}

// rozhodnuti o obsahu stranky
switch ($GLOBALS['akce']):
  case "insert": PridejCt(); break;
  case "save": UlozCt(); break;
  case "quicklog": JenLogin(); break;
  case "logout": OdhlasCt(); break;
endswitch;

$GLOBALS["vzhledwebu"]->Generuj();
ObrTabulka();  // Vlozeni layout prvku

echo "<p class=\"nadpis\">".RS_CT_NADPIS."</p>\n"; // nadpis

// rozhodnuti o obsahu stranky
switch($GLOBALS['akce']):
  case "logmenu": ZobrazLogin(); break;
  case "new": NovyCt(); break;
  case "login": EditujCt(); break;
  case "autologin": EditujAutoCt(); break;
  case "del": VymazatCt(); break;
  case "delreader": AcVymazatCt(); break;
  case "newpw": NoveHesloCt(); break;
  case "newpwsend": AcNoveHesloCt(); break;
  case "showtxt": echo $GLOBALS['cte_modul_text']; break;
endswitch;

// Dokonceni tvorby stranky
KonecObrTabulka();  // Vlozeni layout prvku
$vzhledwebu->Generuj();
?>