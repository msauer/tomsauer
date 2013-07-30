<?php
######################################################################
# phpRS Plug-in modul: LoginKomplet v.1.0.4 , phpRS 2.6.5 kompatibilní
# spojuje v sobe pluginy 'logmenu' a 'showlogin'
######################################################################

// Plug-in:
// Copyright (c) 2003 by Jan Tichavsky (vlakpage@vlakpage.cz)
// http://www.vlakpage.cz/
// upravu na v.1.0.4 (c)2005-JaV administrator(at)hades.cz

// This program is free software. - Toto je bezplatny a svobodny software.
// phpRS:
// http://www.supersvet.cz/phprs/ by Jiri Lukas (jirilukas@supersvet.cz)

// cerpa promene z lang
//  RS_CT_JMENO; RS_CT_Heslo; RS_CT_NAVIG_REG_NOVY; RS_ODESLAT;
//
//  tyhle si ale musite pridat sami :-))
//  RS_CT_AKT_LOGIN; RS_CT_LOGOUT;

// zobrazeni loginu + vypis osobniho menu
function LoginKomplet()
{
if ($GLOBALS["prmyctenar"]->ctenarstav==1):
  // ctenar je nalogovan
  if ($GLOBALS["prmyctenar"]->Ukaz("jmeno")==""): // zjisteni jmena ctenare
    $prjmeno=$GLOBALS["prmyctenar"]->Ukaz("username");
  else:
    $prjmeno=$GLOBALS["prmyctenar"]->Ukaz("jmeno");
  endif;
  $retezec="<div style=\"text-align: center; width: 100%\" class=\"z\">".RS_CT_AKT_LOGIN." <b>".$prjmeno."</b><br /><br /><a href=\"readers.php?akce=logout\">".RS_CT_LOGOUT."<a/></div>\n"; // info pro prihlaseneho ctenare
  if ($GLOBALS["prmyctenar"]->Ukaz("zobrazitdata")==1): // test na zobrazeni osobniho menu
    $retezec.="<br />\n".stripslashes($GLOBALS["prmyctenar"]->Ukaz("databox"));
  endif;
else:
  // ctenar neni nalogovan, zobrazi se mu prihlasovaci formular
  $retezec="<center><img src=\"image/freestyle/nic.gif\" width=\"90%\" height=\"3\"><br /></center>
  <form style=\"display: inline;\" action=\"readers.php\" method=\"post\">
  <input type=\"hidden\" name=\"akce\" value=\"quicklog\" />
  <table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
  <tr class=\"z\" align=\"center\"><td>".RS_CT_JMENO."<br /><input type=\"text\" size=\"15\" name=\"rjmeno\" class=\"textpole\" /></td></tr>
  <tr class=\"z\" align=\"center\"><td>".RS_CT_HESLO."<br /><input type=\"password\" size=\"15\" name=\"rheslo\" class=\"textpole\" /></td></tr>
  <tr class=\"z\" align=\"center\"><td><img src=\"image/freestyle/nic.gif\" width=\"90%\" height=\"3\"><br /><input type=\"submit\" value=\"  ".RS_ODESLAT."  \" class=\"tl\" /></td></tr>
  <tr class=\"z\" align=\"center\"><td><img src=\"image/freestyle/nic.gif\" width=\"90%\" height=\"3\"><br /><a href=\"readers.php\">".RS_CT_OSOBNI_UCET."</a></td></tr>
  </table>
  </form>\n";
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
