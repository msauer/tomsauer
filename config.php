<?
######################################################################
# phpRS Configuration 1.2.6
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

if (!defined('IN_CODE')): die('Nepovoleny pristup! / Hacking attempt!'); endif;

// prirazeni GET a POST vstupu do pole $GLOBALS; je pro mod register_globals = Off
include_once("admin/aext_prom.php");
// inic. konfiguracniho pole
$GLOBALS['rsconfig']=array();

//--[db server]-------------------------------------------------------
// adresa db serveru
$dbserver="localhost";
//  uzivatelske informace (user information)
$dbuser="cyklosauer.cz";
$dbpass="dreauelt";
// jmeno databaze
$dbname="cyklosauer_cz";
// rozlisujici db predpona phpRS
$rspredpona="rs_";

//--[http server]-----------------------------------------------------
// jmeno WWW serveru
$wwwname="cyklosauer.cz";
// zakladni URL adresa WWW serveru - napr.: http://www.supersvet.cz/ - nutno ukoncit lomitkem
$baseadr="http://www.cyklosauer.cz/";
// e-mailove adresy
$redakceadr="admin@cyklosauer.cz";
$infoadr="info@cyklosauer.cz";

//--[ankety]----------------------------------------------------------
// typ zakonceni hlasovani v pripade hlasovani ze systemoveho bloku: a] index = presmerovani na hl.stranku, b] vysledek = zobrazeni vysledku
$GLOBALS['rsconfig']['anketa_cil_str']="index";
// maximalni povoleny pocet hlasovani z jedne IP adresy za stanoveny casovy limit
$GLOBALS['rsconfig']['anketa_max_pocet_opak']=6;
// delka omezujiciho casoveho limitu; jde o dobu, po kterou lze provest pouze urcity pocet hlasovani z jedne konkretni IP adresy (uvedeno v sekundach)
$GLOBALS['rsconfig']['anketa_delka_omezeni']=3600;

//--[autorizace]------------------------------------------------------
// delka platnosti jednoho prihlaseni (uvedeno v sekundach)
$GLOBALS['rsconfig']['platnost_auth']=7200;
// maximalni pocet povolenych chyb v ramci jednoho prihlasovani; za chybu se pocita spatne zadane heslo
$GLOBALS['rsconfig']['auth_max_pocet_chyb']=3;

//--[interni galerie obrazku]-----------------------------------------
// defaultni sirka nahledu - jedna se pouze o orientacni sirku, ktera se automaticky prizpusobi konkretnimu obrazku
$GLOBALS['rsconfig']['img_nahled_sirka']=120;
// defaultni vyska nahledu - jedna se pouze o orientacni vysku, ktera se automaticky prizpusobi konkretnimu obrazku
$GLOBALS['rsconfig']['img_nahled_vyska']=96;
// stejne jako u sekce [http server] a [layout] je i zde nutne relativni adresarovou cestu ukoncit lomitkem
$GLOBALS['rsconfig']['img_adresar']="image/";

//--[cookies]---------------------------------------------------------
// tato volba urcuje odesilaci mod pro cookies: a] 0 = zakladni cookies bez specifikace domeny, b] 1 = rozsireny mod, ve kterem je pripojeno omezeni na konkretni domenovou adresu (nemusi fungovat na localhostu)
$cookiessdomenou=0;

//--[komentare]-------------------------------------------------------
// maximalni delka jednoho celeho komentare; delsi komentare bude automaticky zkraceny
$GLOBALS['rsconfig']['max_delka_komentare']=1000;
// maximalni povolena delka jednoho slova; vetsi slova budou automaticky rozdelena
$GLOBALS['rsconfig']['max_delka_slova']=50;

//--[clanky]----------------------------------------------------------
// maximalni povoleny pocet zaregistrovanych precteni clanku pripadajicich na jednu IP adresu za stanoveny casovy limit
$GLOBALS['rsconfig']['cla_max_pocet_opak']=6;
// delka omezujiciho casoveho limitu; jde o dobu, po kterou lze provest pouze urcity pocet zaregistrovanych precteni z jedne konkretni IP adresy (uvedeno v sekundach)
$GLOBALS['rsconfig']['cla_delka_omezeni']=3600;

//--[spojovaci fce]---------------------------------------------------
// funkce otvirajici konekci na db
function dbcon()
{
@$spojeni=mysql_connect($GLOBALS["dbserver"],$GLOBALS["dbuser"],$GLOBALS["dbpass"]);
if (!$spojeni):
  die('Spojeni se serverem nelze vytvorit! / Could not connect to database server!');
endif;
mysql_select_db($GLOBALS["dbname"],$spojeni);
return $spojeni;
}

$dbspojeni=dbcon();

$GLOBALS["dbspojeni"]=&$dbspojeni;
$GLOBALS["rspredpona"]=&$rspredpona;

//--[layout fce]------------------------------------------------------
// nacteni zakladni konfigurace layoutu z db - nastaveni globalni sablony
$dotazhod=mysql_query("select g.ident_sab,g.soubor_sab,g.adr_sab from ".$rspredpona."config as c,".$rspredpona."global_sab as g where c.promenna='global_sab' and c.hodnota=g.ids",$dbspojeni);
if ($dotazhod==0):
  die('Systém nemůže nalézt potřebné databázové tabulky! / Could not find database tables!');
  exit;
else:
  if (mysql_num_rows($dotazhod)==1):
    // globalni sablona je nastavena
    list($rs_main_sablona,$adrlayoutu,$adrobrlayoutu)=mysql_fetch_row($dotazhod); // cesta k layout souboru; cesta do layout adresare; identifikace pozadovane glob. sablony
  else:
    // globalni sablona neni nastavena
    if (!isset($rs_administrace)): // test na admin pristup
      die('Systém nemůže identifkovat vybranou globální šablonu!');
    endif;
  endif;
endif;
?>