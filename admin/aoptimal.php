<?

######################################################################
# phpRS Administration Engine - Optimalization's section 1.0.3
######################################################################

// Copyright (c) 2001-2003 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_stat_arch, rs_stat_data, rs_guard

function DBOptStat()
{
$optcas=(time()-3600);
$optdatum=Date("Y-m-d",$optcas);
$opthodina=Date("H",$optcas);

// presun stare statistky do archivni tabulky
@$error=mysql_query("insert into ".$GLOBALS["rspredpona"]."stat_arch select * from ".$GLOBALS["rspredpona"]."stat_data where datum<'".$optdatum."' or (datum='".$optdatum."' and hodina<='".$opthodina."')",$GLOBALS["dbspojeni"]);
if ($error): // vse OK
 // vymazani presunutych dat
 @mysql_query("delete from ".$GLOBALS["rspredpona"]."stat_data where datum<'".$optdatum."' or (datum='".$optdatum."' and hodina<='".$opthodina."')",$GLOBALS["dbspojeni"]);
endif;
}

function DBOptGuard()
{
// zjisteni platneho intervalu
if (isset($GLOBALS["platnostauth"])):
  $pocetsekund=time()-$GLOBALS["platnostauth"]-1000;
  $konecnycas=Date("Y-m-d H:i:s",$pocetsekund);
endif;

// vymazani starych session
@mysql_query("delete from ".$GLOBALS["rspredpona"]."guard where cas<='".$konecnycas."'",$GLOBALS["dbspojeni"]);
}

DBOptStat();
DBOptGuard();
?>
