<?

######################################################################
# phpRS Admin System Command 1.0.1
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/

switch($GLOBALS['akce']):
  case "Logout": $Uzivatel->Odhlaseni();
       echo "<p align=\"center\" class=\"txt\">".RS_SYS_ROZ_LOGOUT."<br /><br /><a href=\"admin.html\">".RS_SYS_ROZ_LOGIN."</a></p>";
       break;
endswitch;


?>
