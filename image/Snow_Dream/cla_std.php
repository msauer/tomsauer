<?

######################################################################
# phpRS Layout Engine 2.3.0 - verze: "FreeStyle"
#                           - clanek sablona: "Standard"
######################################################################

// Copyright (c) 2002-2004 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.
// Layout Snow_Dream uprava Jan Kul�k, 3/2004

if (!isset($rs_typ_clanku)): $rs_typ_clanku=""; endif;

switch ($rs_typ_clanku):
  case "kratky":
// ------------------------------------- [kratky clanek] -------------------------------------
echo "<div class=\"modryram\">
<table border=\"0\" width=\"98%\"><tr><td class=\"z\">
<a href=\"search.php?rsvelikost=uvod&amp;rstext=all-phpRS-all&amp;rstema=".$GLOBALS["clanek"]->Ukaz("tema_id")."\"><img src=\"".$GLOBALS["clanek"]->Ukaz("tema_obr")."\" border=\"0\" align=\"right\" alt=\"t�ma\" /></a>
<img src=\"image/freestyle/bullet1.gif\" border=\"0\" hspace=\"3\" alt=\"*\" /> <span class=\"clanadpis\">".$GLOBALS["clanek"]->Ukaz("titulek")."</span><br />
<span class=\"z1\">Vystaveno: ".$GLOBALS["clanek"]->Ukaz("datum")." | p��m� n�hled... </span><br /><br />
<span class=\"z2\">".$GLOBALS["clanek"]->Ukaz("uvod")."</span><br />
<span class=\"z1\">Vlo�il: <a href=\"".$GLOBALS["clanek"]->Ukaz("autor_mail")."\">".$GLOBALS["clanek"]->Ukaz("autor_jm")."</a> |
<a href=\"comment.php?akce=view&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\">Koment���</a>: ".$GLOBALS["clanek"]->Ukaz("pocet_kom") ." |
<a href=\"comment.php?akce=new&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\">Komentuj!</a> |
<a href=\"rservice.php?akce=info&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\" target=\"_blank\"><img src=\"image/freestyle/mail.gif\" height=\"22\" width=\"20\" border=\"0\" hspace=\"0\" vspace=\"1\" align=\"absmiddle\"
alt=\"Informa�n� e-mail\" /></a><a href=\"rservice.php?akce=tisk&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\" target=\"_blank\"><img src=\"image/freestyle/printer.gif\" height=\"22\" width=\"20\" border=\"0\" hspace=\"0\" vspace=\"1\" align=\"absmiddle\" alt=\"Vytisknout �l�nek\" /></a><br />
</span></td></tr></table>
</div>
\n";
// --------------------------------- [konec - kratky clanek] ---------------------------------
  break;
  case "nahled":
// ----------------------------------- [dl. clanek nahled] -----------------------------------
echo "<div class=\"modryram\">
<table border=\"0\" width=\"98%\"><tr><td class=\"z\">
<a href=\"search.php?rsvelikost=uvod&amp;rstext=all-phpRS-all&amp;rstema=".$GLOBALS["clanek"]->Ukaz("tema_id")."\"><img src=\"".$GLOBALS["clanek"]->Ukaz("tema_obr")."\" border=\"0\" align=\"right\" alt=\"t�ma\" /></a>
<img src=\"image/freestyle/bullet1.gif\" border=\"0\" hspace=\"3\" alt=\"*\" /> <a href=\"view.php?cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\" class=\"clanek\"><span class=\"clanadpis\">".$GLOBALS["clanek"]->Ukaz("titulek")."</span></a><br />
<span class=\"z1\">Vyd�no dne ".$GLOBALS["clanek"]->Ukaz("datum")." | p�e�teno: ".$GLOBALS["clanek"]->Ukaz("visit")."x</span><br /><br />
<span class=\"z2\">".$GLOBALS["clanek"]->Ukaz("uvod")."</span><br /><br />
<span class=\"z1\"><a href=\"view.php?cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\"><b>V�ce informac�>>></b></a> |
Vlo�il: <a href=\"".$GLOBALS["clanek"]->Ukaz("autor_mail")."\">".$GLOBALS["clanek"]->Ukaz("autor_jm")."</a> |
<a href=\"comment.php?akce=view&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\">Koment��:</a> ".$GLOBALS["clanek"]->Ukaz("pocet_kom") ."x <br />
</td></tr></table>
</div>
\n";
// ------------------------------- [konec - dl. clanek nahled] -------------------------------
  break;
  case "cely":
// ------------------------------------ [dl. clanek telo] ------------------------------------
echo "<div class=\"premodryram\">
<table border=\"0\"><tr><td class=\"z\">
<a href=\"search.php?rsvelikost=uvod&amp;rstext=all-phpRS-all&amp;rstema=".$GLOBALS["clanek"]->Ukaz("tema_id")."\"><img src=\"".$GLOBALS["clanek"]->Ukaz("tema_obr")."\" border=\"0\" align=\"right\" alt=\"t�ma\" /></a>
<img src=\"image/freestyle/bullet1.gif\" border=\"0\" hspace=\"3\" alt=\"*\" /> <span class=\"clanadpis\">".$GLOBALS["clanek"]->Ukaz("titulek")."</span><br />
<span class=\"z1\">Vyd�no dne ".$GLOBALS["clanek"]->Ukaz("datum")." | p�e�teno: ".$GLOBALS["clanek"]->Ukaz("visit_plus")."x</span><br /><br />
<span class=\"z2\">".$GLOBALS["clanek"]->Ukaz("uvod")."</span><br /><br />".$GLOBALS["clanek"]->Ukaz("text")."<br /><br />\n";
SouvisejiciCl($GLOBALS["clanek"]->Ukaz("link"));
HodnoceniCl($GLOBALS["clanek"]->Ukaz("link"));
echo "<span class=\"z1\">�l�nek otev�en! | Vlo�il: <a href=\"".$GLOBALS["clanek"]->Ukaz("autor_mail")."\">".$GLOBALS["clanek"]->Ukaz("autor_jm")."</a> |
<a href=\"comment.php?akce=view&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\">Komentov�no:</a> ".$GLOBALS["clanek"]->Ukaz("pocet_kom")."x |
<a href=\"comment.php?akce=new&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\">Komentuj!</a> |
<a href=\"rservice.php?akce=info&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\" target=\"_blank\"><img src=\"image/freestyle/mail.gif\" height=\"22\" width=\"20\" border=\"0\" hspace=\"0\" vspace=\"1\" align=\"absmiddle\"
alt=\"Informa�n� e-mail\" /></a><a href=\"rservice.php?akce=tisk&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\" target=\"_blank\"><img src=\"image/freestyle/printer.gif\" height=\"22\" width=\"20\" border=\"0\" hspace=\"0\" vspace=\"1\" align=\"absmiddle\" alt=\"Vytisknout �l�nek\" /></a> ";
// Pozor, jelikoz promenna "zdroj" nemusi obsahovat zadne udaje, je zde podminka, ktera zajistuje jeji (ne)zobrazeni
if ($GLOBALS["clanek"]->Ukaz("zdroj")!=""): echo "| Zdroj: ".$GLOBALS["clanek"]->Ukaz("zdroj")." "; endif;
echo "| <b><a href=\"index.php\">Zav��t �l�nek>>></a></b><br />
</td></tr></table>
</div>
<p></p>\n";
// -------------------------------- [konec - dl. clanek telo] --------------------------------
  break;
endswitch;
?>