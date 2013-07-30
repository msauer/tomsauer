<?

######################################################################
# phpRS Layout Engine 2.3.0 - verze: "Beach"
#                           - clanek sablona: "Press"
######################################################################

// Copyright (c) 2002-2004 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

if (!isset($rs_typ_clanku)): $rs_typ_clanku=""; endif;

switch ($rs_typ_clanku):
  case "kratky":
// ------------------------------------- [kratky clanek] -------------------------------------
echo "<div class=\"modryram\">
<div class=\"z\">
<!-- <a href=\"search.php?rsvelikost=uvod&rstext=all-phpRS-all&rstema=".$GLOBALS["clanek"]->Ukaz("tema_id")."\"><img src=\"".$GLOBALS["clanek"]->Ukaz("tema_obr")."\" border=\"0\" align=\"right\" /></a> -->
<img src=\"image/beach/bullet.gif\" border=\"0\" hspace=\"3\" /> <span class=\"clanadpis\">".$GLOBALS["clanek"]->Ukaz("titulek")."</span><br />
<span class=\"malemodre\">Vydáno dne ".$GLOBALS["clanek"]->Ukaz("datum")." (".$GLOBALS["clanek"]->Ukaz("visit")." přečtení)</span><br /><br />
".$GLOBALS["clanek"]->Ukaz("uvod")."<br /><br />
<!-- Tisková zpráva | -->
<a href=\"comment.php?akce=view&cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\">Komentáře</a>: ".$GLOBALS["clanek"]->Ukaz("pocet_kom") ." |
<a href=\"comment.php?akce=new&cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\">Přidat komentář</a> |
<a href=\"rservice.php?akce=info&cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\"><img src=\"image/beach/mail.gif\" height=\"22\" weidth=\"20\" border=\"0\" hspace=\"0\" vspace=\"1\" align=\"absmiddle\" alt=\"Informa�n� e-mail\" /></a> <a href=\"rservice.php?akce=tisk&cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\" target=\"_blank\"><img src=\"image/beach/printer.gif\" height=\"22\" weidth=\"20\" border=\"0\" hspace=\"0\" vspace=\"1\" align=\"absmiddle\" alt=\"Vytisknout �l�nek\" /></a><br />
</div>
</div>
<p></p>\n";
// --------------------------------- [konec - kratky clanek] ---------------------------------
  break;
  case "nahled":
// ----------------------------------- [dl. clanek nahled] -----------------------------------
echo "<div class=\"modryram\">
<div class=\"z\">
<!-- <a href=\"search.php?rsvelikost=uvod&rstext=all-phpRS-all&rstema=".$GLOBALS["clanek"]->Ukaz("tema_id")."\"><img src=\"".$GLOBALS["clanek"]->Ukaz("tema_obr")."\" border=\"0\" align=\"right\" /></a> -->
<img src=\"image/beach/bullet.gif\" border=\"0\" hspace=\"3\" /> <a href=\"view.php?cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\" class=\"clanek\"><span class=\"clanadpis\">".$GLOBALS["clanek"]->Ukaz("titulek")."</span></a><br />
<span class=\"malemodre\">Vydáno dne ".$GLOBALS["clanek"]->Ukaz("datum")." (".$GLOBALS["clanek"]->Ukaz("visit")." přečtení)</span><br /><br />
".$GLOBALS["clanek"]->Ukaz("uvod")."<br /><br />
<!-- Tisková zpráva |--> <a href=\"comment.php?akce=view&cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\">Komentáře</a>: ".$GLOBALS["clanek"]->Ukaz("pocet_kom") ." |
<a href=\"comment.php?akce=new&cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\">Přidat komentář</a> |
<a href=\"rservice.php?akce=info&cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\" target=\"_blank\"><img src=\"image/beach/mail.gif\" height=\"22\" weidth=\"20\" border=\"0\" hspace=\"0\" vspace=\"1\" align=\"absmiddle\" alt=\"Informa�n� e-mail\" /></a> <a href=\"rservice.php?akce=tisk&cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\" target=\"_blank\"><img src=\"image/beach/printer.gif\" height=\"22\" weidth=\"20\" border=\"0\" hspace=\"0\" vspace=\"1\" align=\"absmiddle\" alt=\"Vytisknout �l�nek\" /></a><br />
</div>
</div>
<p></p>\n";
// ------------------------------- [konec - dl. clanek nahled] -------------------------------
  break;
  case "cely":
// ------------------------------------ [dl. clanek telo] ------------------------------------
echo "<div class=\"premodryram\">
<div class=\"z\">
<!-- <a href=\"search.php?rsvelikost=uvod&rstext=all-phpRS-all&rstema=".$GLOBALS["clanek"]->Ukaz("tema_id")."\"><img src=\"".$GLOBALS["clanek"]->Ukaz("tema_obr")."\" border=\"0\" align=\"right\" /></a> -->
<img src=\"image/beach/bullet.gif\" border=\"0\" hspace=\"3\" /> <span class=\"clanadpis\">".$GLOBALS["clanek"]->Ukaz("titulek")."</span><br />
<span class=\"malemodre\">Vydáno dne ".$GLOBALS["clanek"]->Ukaz("datum")." (".$GLOBALS["clanek"]->Ukaz("visit_plus")." přečtení)</span><br /><br />
<span class=\"clatext\">".$GLOBALS["clanek"]->Ukaz("uvod")."<br /><br />".$GLOBALS["clanek"]->Ukaz("text")."</span><br /><br />\n";
SouvisejiciCl($GLOBALS["clanek"]->Ukaz("link"));
echo "
<a href=\"comment.php?akce=view&cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\">Komentáře</a>: ".$GLOBALS["clanek"]->Ukaz("pocet_kom")." |
<a href=\"comment.php?akce=new&cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\">Přidat komentář</a> |
<a href=\"rservice.php?akce=info&cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\" target=\"_blank\"><img src=\"image/beach/mail.gif\" height=\"22\" weidth=\"20\" border=\"0\" hspace=\"0\" vspace=\"1\" align=\"absmiddle\" alt=\"Informa�n� e-mail\" /></a> <a href=\"rservice.php?akce=tisk&cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\" target=\"_blank\"><img src=\"image/beach/printer.gif\" height=\"22\" weidth=\"20\" border=\"0\" hspace=\"0\" vspace=\"1\" align=\"absmiddle\" alt=\"Vytisknout �l�nek\" /></a> ";
// Pozor, jelikoz promenna "zdroj" nemusi obsahovat zadne udaje, je zde podminka, ktera zajistuje jeji (ne)zobrazeni
if ($GLOBALS["clanek"]->Ukaz("zdroj")!=""): echo "| Zdroj: ".$GLOBALS["clanek"]->Ukaz("zdroj")." "; endif;
echo "<br />
</div>
</div>
<p></p>\n";
// -------------------------------- [konec - dl. clanek telo] --------------------------------
  break;
endswitch;
?>