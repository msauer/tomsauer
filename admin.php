<?

######################################################################
# phpRS Administration 1.7.1
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky:

/*
  Promenna $zobrazhlavicku umoznuje odstranit z vygenerovane stranky celou HTML hlavicku a ohranicujici HTML a BODY tagy.
*/

$rs_administrace=1; // promenna identifikujici administracni rozhrani
define('IN_CODE',true); // inic. ochranne konstanty

include_once('config.php');
include_once('autor.php');
include_once('admin/astdlib.php');
include_once('lang/admin_cfg_sl.php');

// definice vykonneho souboru
define('RS_VYKONNYSOUBOR','admin.php');

function AdminMenu()
{
$akt_je_admin=$GLOBALS['Uzivatel']->JeAdmin(); // test na admin opravneni

// sestaveni dotazu
if ($akt_je_admin==1):
  // vsechny moduly
  $dotaz="select idm,ident_modulu,all_prava_users,nazev_menu,link_menu,barva_bg from ".$GLOBALS["rspredpona"]."moduly_prava where blokovat_modul=0 order by poradi_menu";
else:
  // bez spec. admin modulu
  $dotaz="select idm,ident_modulu,all_prava_users,nazev_menu,link_menu,barva_bg from ".$GLOBALS["rspredpona"]."moduly_prava where blokovat_modul=0 and jen_admin_modul=0 order by poradi_menu";
endif;
$dotazmoduly=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
if ($dotazmoduly!=0):
  $pocetmoduly=mysql_num_rows($dotazmoduly); // pocet polozek
else:
  $pocetmoduly=0; // tabulka neexistuje
endif;

if ($pocetmoduly>0):
  // inic. pomocnych poli
  $pole_data=array();
  $admenulink=array();
  $admenutxt=array();
  $admenubg=array();
  for ($pom=0;$pom<$pocetmoduly;$pom++):
    $pole_data=mysql_fetch_assoc($dotazmoduly);
    // test na moznost pristupu prihlaseneho uziv. - hromadny pristup / jednotlivy pristup
    if ($pole_data['all_prava_users']==1): // test na hromadne povoleni
      // vse OK
      $admenulink[]=$pole_data['link_menu'].'&amp;modul='.$pole_data['ident_modulu'];
      $admenutxt[]=$pole_data['nazev_menu'];
      // test na podbarveni polozky v menu
      if (empty($pole_data['barva_bg'])): $admenubg[]=''; else: $admenubg[]=' style="background-color:#'.$pole_data['barva_bg'].'"'; endif;
    else:
      if ($GLOBALS['Uzivatel']->OvereniPravBool($pole_data['ident_modulu'])==1): // test na konkretniho uziv.
        // vse OK
        $admenulink[]=$pole_data['link_menu'].'&amp;modul='.$pole_data['ident_modulu'];
        $admenutxt[]=$pole_data['nazev_menu'];
        // test na podbarveni polozky v menu
        if (empty($pole_data['barva_bg'])): $admenubg[]=''; else: $admenubg[]=' style="background-color:#'.$pole_data['barva_bg'].'"'; endif;
      endif;
    endif;
  endfor;
endif;

// inic. prom.
$pocetpolozek=count($admenulink);
$pocitadlo=0;

// generovani menu
echo "<table border=\"0\" cellpadding=\"5\" cellspacing=\"0\" align=\"center\" class=\"rammodry\">\n";
for ($pom=0;$pom<$pocetpolozek;$pom++):
  switch($pocitadlo):
   case 0: echo "<tr class=\"menu\"><td".$admenubg[$pom]."><a href=\"".RS_VYKONNYSOUBOR."?".$admenulink[$pom]."\">".$admenutxt[$pom]."</a></td>\n"; $pocitadlo=1; break;
   case 1: echo "<td".$admenubg[$pom]."><a href=\"".RS_VYKONNYSOUBOR."?".$admenulink[$pom]."\">".$admenutxt[$pom]."</a></td>\n"; $pocitadlo=2; break;
   case 2: echo "<td".$admenubg[$pom]."><a href=\"".RS_VYKONNYSOUBOR."?".$admenulink[$pom]."\">".$admenutxt[$pom]."</a></td>\n"; $pocitadlo=3; break;
   case 3: echo "<td".$admenubg[$pom]."><a href=\"".RS_VYKONNYSOUBOR."?".$admenulink[$pom]."\">".$admenutxt[$pom]."</a></td>\n"; $pocitadlo=4; break;
   case 4: echo "<td".$admenubg[$pom]."><a href=\"".RS_VYKONNYSOUBOR."?".$admenulink[$pom]."\">".$admenutxt[$pom]."</a></td>\n"; $pocitadlo=5; break;
   case 5: echo "<td".$admenubg[$pom]."><a href=\"".RS_VYKONNYSOUBOR."?".$admenulink[$pom]."\">".$admenutxt[$pom]."</a></td></tr>\n"; $pocitadlo=0; break;
  endswitch;
endfor;
switch($pocitadlo):
  case 0: break;
  case 1: echo "<td colspan=\"5\">&nbsp;</td></tr>\n"; break;
  case 2: echo "<td colspan=\"4\">&nbsp;</td></tr>\n"; break;
  case 3: echo "<td colspan=\"3\">&nbsp;</td></tr>\n"; break;
  case 4: echo "<td colspan=\"2\">&nbsp;</td></tr>\n"; break;
  case 5: echo "<td>&nbsp;</td></tr>\n"; break;
endswitch;
echo "</table>\n";
}

function Logo()
{
?>
<br /><br /><br /><br /><br /><div align="center"><img src="image/phprs_logo.gif" alt="Logo phpRS" /></div><br />
<?
}

// nastaveni akt. jazyku
if (empty($GLOBALS['Uzivatel']->JazykRozhrani)):
  $GLOBALS['rsconfig']['akt_admin_lang']=$GLOBALS['rsconfig']['default_admin_lang'];
else:
  $GLOBALS['rsconfig']['akt_admin_lang']=$GLOBALS['Uzivatel']->JazykRozhrani;
endif;
// akt. adresa zakladniho administracniho slovniku
$akt_zakl_admin_sl='lang/'.$GLOBALS['rsconfig']['akt_admin_lang'].'/admin_sl_'.$GLOBALS['rsconfig']['akt_admin_lang'].'.php';
// test na existenci slovniku
if (file_exists($akt_zakl_admin_sl)==1):
  include_once($akt_zakl_admin_sl); // vlozeni slovniku
else:
  echo "<p align=\"center\">".RS_ADM_SB_SL_NE_EXIST."</p>\n";
endif;

// test na existenci promenne $zobrazhlavicku - povoluje/zakazuje zobrazeni HTML zahlavi a zapati
if (!isset($GLOBALS['zobrazhlavicku'])): $GLOBALS['zobrazhlavicku']=1; endif;

if ($GLOBALS['zobrazhlavicku']==1):
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
 <title><? echo RS_ADM_HTML_TITLE; ?></title>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
 <meta name="author" content="JiĹ™Ă­ LukĂˇĹˇ">
 <meta name="category" content="business">

 <script type="text/javascript" language="javascript">
 function setPointer(theRow, thePointerColor)
 {
    if (thePointerColor == '' || typeof(theRow.style) == 'undefined') {
        return false;
    }
    if (typeof(document.getElementsByTagName) != 'undefined') {
        var theCells = theRow.getElementsByTagName('td');
    }
    else if (typeof(theRow.cells) != 'undefined') {
        var theCells = theRow.cells;
    }
    else {
        return false;
    }

    var rowCellsCnt  = theCells.length;
    for (var c = 0; c < rowCellsCnt; c++) {
        theCells[c].style.backgroundColor = thePointerColor;
    }

    return true;
 }
 </script>

 <style type="text/css">
 <!--
 a { font-style: normal; font-variant: normal; font-family: "verdana","arial"; }
 .rammodry { background-color: #A0D0FF; border-style: solid; border-color: #0000FF; border-top-width: 2px; border-left-width: 2px; border-right-width: 2px; border-bottom-width: 2px; }
 .ramsedy { border-style: solid; border-color: #8C8C8C; border-top-width: 2px; border-left-width: 2px; border-right-width: 2px; border-bottom-width: 2px; }
 a:link { color: #0000FF; }
 a:visited { color: #0000FF; }
 a:hover { color: #FF0000; }
 a:active { color: #0000FF; }
 a.zahlavi:link { color: #000000; }
 a.zahlavi:visited { color: #000000; }
 a.zahlavi:hover { color: #FF0000; }
 a.zahlavi:active { color: #000000; }
 body { font-family: "verdana","arial"; font-size: 13px; color: #000000; background-color: #FFFFFF; margin: 0px; }
 .smltxt { font-style: normal; font-size: 11px; font-family: "verdana","arial"; }
 .txt { font-style: normal; font-size: 13px; font-family: "verdana","arial"; }
 .menu { font-style: normal; font-size: 11px; font-family: "verdana","arial"; }
 .textpole { border: 1px solid #000000; color: #000000; font-family: Verdana,Arial,Helvetica; font-size: 12px; }
 .textbox { background: transparent; background-color: White; border: 1px solid #000000; color: #000000; font-family: Verdana,Arial,Helvetica; font-size: 12px; text-align: left; scrollbar-face-color: #CCCCCC; scrollbar-shadow-color: #FFFFFF; scrollbar-highlight-color: #FFFFFF; scrollbar-3dlight-color: #FFFFFF; scrollbar-darkshadow-color: #FFFFFF; scrollbar-track-color: #FFFFFF; scrollbar-arrow-color: #000000; }
 .tl { background-color: #808080; color: #FFFFFF; font-family: Verdana,Arial,Helvetica; font-size: 11px; font-weight: bold; text-align: center; border: 1px solid #000000; }
 .sezobory { font-family: "Courier New", Courier, mono; font-size: 14px; }
 .loginprouzek { font-size: 11px; text-align: right; border-style: solid; border-color: #0000FF; background: #A0D0FF; border-top-width: 0px; border-left-width: 0px; border-right-width: 0px; border-bottom-width: 2px; }
 .blok-std { border: 2px solid #8C8C8C; background-color: #E6E6E6; width: 200px; height: 190px; }
 .blok-sys { border: 2px solid #8C8C8C; background-color: #EBD7FF; width: 200px; height: 190px; }
 -->
 </style>
</head>

<body>
<?

echo "<div class=\"loginprouzek\">".RS_ADM_NAVIG_LOGIN.": ".$Uzivatel->Ukaz("username")." - ".Date("d.m.Y")."&nbsp;</div><br />\n";

endif; // konec $zobrazhlavicku

if (!isset($GLOBALS['akce'])||!isset($GLOBALS['modul'])):
  AdminMenu();
  Logo();
  include_once("admin/aoptimal.php"); // optimalizacni rutina
else:
  // test na volany modul
  $GLOBALS['akce']=addslashes($GLOBALS['akce']);
  $GLOBALS['modul']=addslashes($GLOBALS['modul']);
  $dotazmodul=mysql_query("select idm,ident_modulu,fks_prava_users,all_prava_users,liclakce_menu from ".$GLOBALS["rspredpona"]."moduly_prava where ident_modulu='".$GLOBALS['modul']."' and blokovat_modul=0",$GLOBALS["dbspojeni"]);
  $pocetmodul=mysql_num_rows($dotazmodul);
  // test na existenci modulu
  if ($pocetmodul==1):
    // modul existuje
    $akt_modul_pole=array();
    $akt_modul_pole=mysql_fetch_assoc($dotazmodul);
    // akt. adresa k jazyk. slovniku akt. modulu
    $akt_admin_modul_sl='lang/'.$GLOBALS['rsconfig']['akt_admin_lang'].'/admin_sl_'.$akt_modul_pole['ident_modulu'].'_'.$GLOBALS['rsconfig']['akt_admin_lang'].'.php';
    // test na moznost pristupu prihlaseneho uziv. - hromadny pristup / jednotlivy pristup
    if ($akt_modul_pole['all_prava_users']==1): // test na hromadne povoleni
      // vse OK - muze se provest include souboru
      // test na existenci slovniku
      if (file_exists($akt_admin_modul_sl)==1):
        include_once($akt_admin_modul_sl); // vlozeni slovniku
      else:
        echo "<p align=\"center\" class=\"txt\">".RS_ADM_SB_SL_NE_EXIST."</p>\n";
      endif;
      // test na existenci modulu
      if (file_exists($akt_modul_pole['liclakce_menu'])==1):
        include_once($akt_modul_pole['liclakce_menu']); // vlozeni modulu
      else:
        echo "<p align=\"center\" class=\"txt\">".RS_ADM_SB_NE_EXIST."</p>\n";
      endif;
    else:
      if ($Uzivatel->OvereniPravBool($akt_modul_pole['ident_modulu'])==1): // test na konkretniho uziv.
        // vse OK - muze se provest include souboru
        // test na existenci slovniku
        if (file_exists($akt_admin_modul_sl)==1):
          include_once($akt_admin_modul_sl); // vlozeni slovniku
        else:
          echo "<p align=\"center\" class=\"txt\">".RS_ADM_SB_SL_NE_EXIST."</p>\n";
        endif;
        // test na existenci modulu
        if (file_exists($akt_modul_pole['liclakce_menu'])==1):
          include_once($akt_modul_pole['liclakce_menu']); // vlozeni modulu
        else:
          echo "<p align=\"center\" class=\"txt\">".RS_ADM_SB_NE_EXIST."</p>\n";
        endif;
      else:
        // uzivatel nema potrebna pristupova prava
        echo "<p align=\"center\" class=\"txt\">".RS_ADM_MODUL_NE_PRAVA."</p>\n";
      endif;
    endif;
  else:
    // chyba pri identifikaci modulu
    if ($pocetmodul==0):
      // modul neexistuje
      echo "<p align=\"center\" class=\"txt\">".RS_ADM_MODUL_NE_EXIST."</p>\n";
    else:
      // existuje vice modulu se stejnou identifikaci
      echo "<p align=\"center\" class=\"txt\">".RS_ADM_MODUL_NE_IDENT."</p>\n";
    endif;
  endif;
endif;

if ($GLOBALS['zobrazhlavicku']==1):
?>
</body>
</html>
<?
endif;
?>
