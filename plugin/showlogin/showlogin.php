<?
######################################################################
# phpRS Plug-in modul: ShowLogin v1.0.2
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// zobrazeni loginu + vypis osobniho menu
function ShowLogin()
{
if ($GLOBALS["prmyctenar"]->ctenarstav==1):
  // ctenar je nalogovan
  if ($GLOBALS["prmyctenar"]->Ukaz("jmeno")==""): // zjisteni jmena ctenare
    $prjmeno=$GLOBALS["prmyctenar"]->Ukaz("username");
  else:
    $prjmeno=$GLOBALS["prmyctenar"]->Ukaz("jmeno");
  endif;
  $retezec="<b>Vítej ".$prjmeno."</b><br />\n";
  $retezec.="(<a href=\"readers.php?akce=logout\">Odhlášení čtenáře</a>)\n";
  if ($GLOBALS["prmyctenar"]->Ukaz("zobrazitdata")==1): // test na zobrazeni osobniho menu
    $retezec.="<br /><br />".stripslashes($GLOBALS["prmyctenar"]->Ukaz("databox"))."\n";
  endif;
else:
  // ctenar neni nalogovan
  $retezec="Neznámý čtenář";
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