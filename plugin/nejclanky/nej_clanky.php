<?
######################################################################
# phpRS Plug-in modul: Nej články v1.1.6
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// prehled nejctenejsich clanku
function NejClanky()
{
// ---[ definice zakladnich parametru ]-----------------------
$mnozstviclanku=6; // pocet zobrazenych hodnot (clanku)
$pocetmesicu=2;    // tato promenna omezuje vyhledavaci proces pouze na clanky stare maximalne zadany pocet mesicu
// -----------------------------------------------------------

// sestaveni casoveho omezeni
$dnesnidatum=date("Y-m-d H:i:s");
list($dnrok,$dnmes,$dnden)=explode("-",date("Y-m-d"));
$omezujicidatum=date("Y-m-d",mktime(0,0,0,$dnmes-$pocetmesicu,$dnden,$dnrok));

// vypis X nejctenejsich clanku
$dotazclanky=mysql_query("select link,titulek,date_format(datum,'%d. %m. %Y') as vyslden,visit from ".$GLOBALS["rspredpona"]."clanky where visible='1' and visit>0 and datum>='".$omezujicidatum."' and datum<='".$dnesnidatum."' order by visit desc limit 0,".$mnozstviclanku,$GLOBALS["dbspojeni"]);
if ($dotazclanky==0):
  $retezec="<p align=\"center\" class=\"z\">Chybí zdrojová databáze!</p>\n";
  $pocetclanky=0;
else:
  $pocetclanky=mysql_num_rows($dotazclanky);
endif;

// overeni pritomnosti clanku
if ($pocetclanky==0):
  $retezec="<p align=\"center\" class=\"z\">Neexistují vhodná data!</p>\n";
else:
  // vypis clanku
  $retezec="";
  for ($pom=0;$pom<$pocetclanky;$pom++):
    $pole_data=mysql_fetch_assoc($dotazclanky);
    $retezec .="<span class=\"z\"><a href=\"view.php?cisloclanku=".$pole_data["link"]."\">".$pole_data["titulek"]."</a><br />\n";
    $retezec .="(".$pole_data["vyslden"].", ".$pole_data["visit"]."x)</span><br />\n";
  endfor;
endif;

// zobrazeni menu
switch ($GLOBALS["vzhledwebu"]->AktBlokTyp()):
  case 1: Blok1($GLOBALS["vzhledwebu"]->AktBlokNazev(),$retezec); break;
  case 2: Blok2($GLOBALS["vzhledwebu"]->AktBlokNazev(),$retezec); break;
  case 3: Blok3($GLOBALS["vzhledwebu"]->AktBlokNazev(),$retezec); break;
  case 4: Blok4($GLOBALS["vzhledwebu"]->AktBlokNazev(),$retezec); break;
  case 5: Blok5($GLOBALS["vzhledwebu"]->AktBlokNazev(),$retezec); break;
  default: Blok1($GLOBALS["vzhledwebu"]->AktBlokNazev(),$retezec); break;
endswitch;
}
?>