<?

######################################################################
# phpRS Reader's service 1.3.3
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

/*
  Tento script slouzi k obsluze nekterych ctenarskych sluzeb
  Napr: zasilani informacniho mailu, tisk clanku, atd.
*/

// vyuzivane tabulky: rs_clanky

define('IN_CODE',true); // inic. ochranne konstanty

include_once("config.php");
include_once("specfce.php");
include_once("myweb.php");
include_once("sl.php");
include_once("trlayout.php");
include_once($adrlayoutu);

// test na pritomnost promenne $akce
if (!isset($GLOBALS["akce"])):
  echo "<html><body><div align=\"center\">".RS_AKCE_ERR."</div></body></html>\n";
  exit();
endif;
// test na pritomnost promenne $cisloclanku
if (!isset($GLOBALS["cisloclanku"])):
  echo "<html><body><div align=\"center\">".RS_VW_ERR1."</div></body></html>\n";
  exit();
endif;

function TestNaAdresu($mail = '')
{
// tato funkce testuje platnost zadaneho e-mailu
if (ereg('^[_a-zA-Z0-9\.\-]+@[_a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,4}$',$mail)):
  return 1; // spravna struktura
else:
  return 0; // chybna struktura
endif;
}

function NovyMail()
{
// bezpecnostni korekce
$GLOBALS["cisloclanku"]=mysql_escape_string($GLOBALS["cisloclanku"]);

// zjisteni titulku
$dotazclanek=mysql_query("select titulek from ".$GLOBALS["rspredpona"]."clanky where link='".$GLOBALS["cisloclanku"]."'",$GLOBALS["dbspojeni"]);
if (mysql_num_rows($dotazclanek)>0):
  $pole_clanek=mysql_fetch_assoc($dotazclanek);
else:
  $pole_clanek['titulek']='';
endif;

?>
<p align="center" class="nadpis"><? echo RS_CS_NADPIS; ?></p>
<p align="center" class="z"><strong><? echo RS_CS_CLANEK; ?>: <? echo $pole_clanek['titulek']; ?></strong></p>
<form action="rservice.php" method="post">
<input type="hidden" name="akce" value="sendinfo" />
<input type="hidden" name="cisloclanku" value="<? echo $GLOBALS["cisloclanku"]; ?>" />
<input type="hidden" name="prtitulek" value="<? echo $pole_clanek['titulek']; ?>" />
<table border="0" cellspacing="0" cellpadding="0" align="center">
<tr class="z"><td><? echo RS_CS_PRIJEMCE; ?>:</td><td><input type="text" size="40" name="prprijemce" value="@" class="textpole" /></td></tr>
<tr class="z"><td><? echo RS_CS_ODESILATEL; ?>:</td><td><input type="text" size="40" name="prodesilatel" value="@" class="textpole" /></td></tr>
<tr class="z"><td colspan="2" align="center"><br /><? echo RS_CS_TEXT_ZPR; ?><br /><textarea name="przprava" cols="50" rows="4" wrap="yes" class="textbox"></textarea></td></tr>
</table>
<p align="center"><input type="submit" value=" <? echo RS_CS_ODESLAT; ?> " class="tl" /></p>
<p align="center" class="z"><? echo RS_CS_INFO_TEXT; ?></p>
</form>
<p></p>
<?
}

function OdeslaniMailu()
{
$error=0; // chybova promenna

// test na chyby
if (TestNaAdresu($GLOBALS["prprijemce"])==0):
  $error=1;
endif;
if (TestNaAdresu($GLOBALS["prodesilatel"])==0):
  $error=2;
endif;

// text zobrazeny na strance
echo "<p align=\"center\" class=\"nadpis\">".RS_CS_NADPIS."</p>\n";
echo "<p align=\"center\" class=\"z\">\n";

if ($error==0):
  // priprava e-mailu
  $przprava=RS_CS_MAIL1." ".$GLOBALS["prtitulek"]."\n".$GLOBALS["baseadr"]."view.php?cisloclanku=".$GLOBALS["cisloclanku"]."\n\n".RS_CS_MAIL2."\n".$GLOBALS["przprava"];
  $prtitulek=RS_CS_MAIL_PREDMET." ".$GLOBALS["wwwname"];

  include_once('admin/astdlib_mail.php'); // nacteni postovni tridy

  $odeslani_posty = new CPosta();
  $odeslani_posty->Nastav("predmet",$prtitulek);
  $odeslani_posty->Nastav("obsah",$przprava);
  $odeslani_posty->Nastav("adresat",$GLOBALS["prprijemce"]);
  $odeslani_posty->Nastav("odesilatel_mail",$GLOBALS["prodesilatel"]);
  $odeslani_posty->Nastav("odesilatel_txt",'');
  if ($odeslani_posty->Odesilac()==1):
    echo RS_CS_DOPIS_OK; // vse OK
  else:
    echo RS_CS_ERR1; // chyba
  endif;
else:
  // chyba - spatne e-maly
  echo RS_CS_ERR2;
endif;

echo "</p>\n";
echo "<p align=\"center\" class=\"z\"><a href=\"view.php?cisloclanku=".$GLOBALS["cisloclanku"]."\">".RS_CS_ZOBRAZ_CLA."</a></p>\n";
echo "<p></p>\n";
}

function Tisk()
{
include_once('trclanek.php');

// bezpecnostni korekce
$GLOBALS['cisloclanku']=mysql_escape_string($GLOBALS['cisloclanku']);

$GLOBALS['clanek'] = new CClanek();
$chyba_clanek=$GLOBALS['clanek']->NactiClanek($GLOBALS['cisloclanku']);

// test na existenci clanku
if ($chyba_clanek==1):
// tvorba print stranky
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=<? echo $GLOBALS['layoutkodovani']; ?>">
  <title><? echo $GLOBALS['wwwname']; ?></title>
  <? echo $GLOBALS['layoutcss']; ?>
</head>

<body bgcolor="#FFFFFF">

<?
// vlozeni specialni tiskove clankove sablony
$spec_tisk_sablona=$GLOBALS['adrobrlayoutu'].'cla_tisk.php';
// test na existenci tiskove sablony
if (file_exists($spec_tisk_sablona)):
  include_once($spec_tisk_sablona);
else:
  // CHYBA: Chyba při zobrazování článku číslo XXX! Systém nemůže nalézt odpovídající šablonu!
  echo "<p align=\"center\" class=\"z\">".RS_IN_ERR1_1." ".$GLOBALS['cisloclanku']."! ".RS_IN_ERR1_2."<p>\n";;
endif;
?>

<p></p>
<div align="center">
<form>
<input type="button" value="<? echo RS_CS_TISK; ?>" onclick="if (window.print()==0) { alert('<? echo RS_CS_ERR_TISK; ?>'); }" class="tl" />
</form>
</div>
<p></p>

</body>
</html>
<?
// konec - tvorba print stranky
else:
  // CHYBA: Chyba! Článek číslo XXX neexistuje!
  echo "<p align=\"center\" class=\"z\">".RS_VW_ERR2_1." ".$GLOBALS['cisloclanku']." ".RS_VW_ERR2_2."<p>\n";
endif;
}

// rozcestnik akci
switch ($GLOBALS["akce"]):
  // *** tisk clanku
  case "tisk":
    Tisk();
  break;
  // *** sestaveni informacniho mailu
  case "info":
    // Tvorba stranky
    $vzhledwebu->Generuj();
    ObrTabulka();  // Vlozeni layout prvku
    // Informacni e-mail
    NovyMail();
    // Dokonceni tvorby stranky
    KonecObrTabulka();  // Vlozeni layout prvku
    $vzhledwebu->Generuj();
  break;
  // *** odeslani informacniho mailu
  case "sendinfo":
    // Tvorba stranky
    $vzhledwebu->Generuj();
    ObrTabulka();  // Vlozeni layout prvku
    // Informacni e-mail
    OdeslaniMailu();
    // Dokonceni tvorby stranky
    KonecObrTabulka();  // Vlozeni layout prvku
    $vzhledwebu->Generuj();
  break;
endswitch;
?>