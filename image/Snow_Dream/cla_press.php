<?

######################################################################
# phpRS Layout Engine 2.3.0 - verze: "FreeStyle"
#                           - clanek sablona: "Press"
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
<table border=\"0\"><tr><td class=\"z\">
<!-- <a href=\"search.php?rsvelikost=uvod&amp;rstext=all-phpRS-all&amp;rstema=".$GLOBALS["clanek"]->Ukaz("tema_id")."\"><img src=\"".$GLOBALS["clanek"]->Ukaz("tema_obr")."\" border=\"0\" align=\"right\" alt=\"t�ma\" /></a> -->
<img src=\"image/freestyle/bullet2.jpg\" border=\"0\" hspace=\"3\" alt=\"*\" /> <span class=\"short\">".$GLOBALS["clanek"]->Ukaz("titulek")."</span><br />
<span class=\"z1\">Vyd�no dne ".$GLOBALS["clanek"]->Ukaz("datum")." | Cel� zpr�va...</span><br /><br />
<span class=\"z2\">".$GLOBALS["clanek"]->Ukaz("uvod")."<br /></span><br />
<span class=\"z1\">Tiskov� zpr�va |
<a href=\"comment.php?akce=view&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\">Koment��e:</a> ".$GLOBALS["clanek"]->Ukaz("pocet_kom") ." |
<a href=\"comment.php?akce=new&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\">Komentuj!</a> |
<a href=\"rservice.php?akce=info&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\" target=\"_blank\"><img src=\"image/freestyle/mail.gif\" height=\"22\" width=\"20\" border=\"0\" hspace=\"0\" vspace=\"1\" align=\"absmiddle\" alt=\"Informa�n� e-mail\" /></a><a href=\"rservice.php?akce=tisk&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\" target=\"_blank\"><img src=\"image/freestyle/printer.gif\" height=\"22\" width=\"20\" border=\"0\" hspace=\"0\" vspace=\"1\" align=\"absmiddle\" alt=\"Vytisknout �l�nek\" /></a> )<br />
</span></td></tr></table>
</div>
\n";
// --------------------------------- [konec - kratky clanek] ---------------------------------
  break;
  case "nahled":
// ----------------------------------- [dl. clanek nahled] -----------------------------------
echo "<div class=\"modryram\">
<table border=\"0\"><tr><td class=\"z\">
<!-- <a href=\"search.php?rsvelikost=uvod&amp;rstext=all-phpRS-all&amp;rstema=".$GLOBALS["clanek"]->Ukaz("tema_id")."\"><img src=\"".$GLOBALS["clanek"]->Ukaz("tema_obr")."\" border=\"0\" align=\"right\" alt=\"t�ma\" /></a> -->
<img src=\"image/freestyle/bullet2.jpg\" border=\"0\" hspace=\"3\" alt=\"*\" /> <a href=\"view.php?cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\" class=\"clanek\"><span class=\"clanadpis\">".$GLOBALS["clanek"]->Ukaz("titulek")."</span></a><br />
<span class=\"z1\">Vyd�no dne ".$GLOBALS["clanek"]->Ukaz("datum")." | p�e�teno: ".$GLOBALS["clanek"]->Ukaz("visit")."x | komentov�no: ".$GLOBALS["clanek"]->Ukaz("pocet_kom") ."x</span><br /><br />
<span class=\"z2\">".$GLOBALS["clanek"]->Ukaz("uvod")."</span><br />
<span class=\"z1\"><a href=\"view.php?cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\"><b>Tiskov� zpr�va>>></b></a></b>

</td></tr></table>
</div>
\n";
// ------------------------------- [konec - dl. clanek nahled] -------------------------------
  break;
  case "cely":
// ------------------------------------ [dl. clanek telo] ------------------------------------
echo "<div class=\"premodryram\">
<table border=\"0\"><tr><td class=\"z\">
<!-- <a href=\"search.php?rsvelikost=uvod&amp;rstext=all-phpRS-all&amp;rstema=".$GLOBALS["clanek"]->Ukaz("tema_id")."\"><img src=\"".$GLOBALS["clanek"]->Ukaz("tema_obr")."\" border=\"0\" align=\"right\" alt=\"t�ma\" /></a> -->
<img src=\"image/freestyle/bullet2.jpg\" border=\"0\" hspace=\"3\" alt=\"*\" /> <span class=\"clanadpis\">".$GLOBALS["clanek"]->Ukaz("titulek")."</span><br />
<span class=\"z1\">Vyd�no dne ".$GLOBALS["clanek"]->Ukaz("datum")." | p�e�teno: ".$GLOBALS["clanek"]->Ukaz("visit_plus")."x</span><br /><br />
<span class=\"z2\">".$GLOBALS["clanek"]->Ukaz("uvod")."</span><br /><br />
<span class=\"clatext\">".$GLOBALS["clanek"]->Ukaz("text")."</span><br /><br />\n";
SouvisejiciCl($GLOBALS["clanek"]->Ukaz("link"));
echo "<span class=\"z1\">Cel� tiskov� zpr�va... |
<a href=\"comment.php?akce=view&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\">Komentov�no:</a> ".$GLOBALS["clanek"]->Ukaz("pocet_kom")."x |
<a href=\"comment.php?akce=new&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\">Komentuj!</a> |
<a href=\"rservice.php?akce=info&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\" target=\"_blank\"><img src=\"image/freestyle/mail.gif\" height=\"22\" width=\"20\" border=\"0\" hspace=\"0\" vspace=\"1\" align=\"absmiddle\" alt=\"Informa�n� e-mail\" /></a><a href=\"rservice.php?akce=tisk&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\" target=\"_blank\"><img src=\"image/freestyle/printer.gif\" height=\"22\" width=\"20\" border=\"0\" hspace=\"0\" vspace=\"1\" align=\"absmiddle\"
alt=\"Vytisknout �l�nek\" /></a> ";
// Pozor, jelikoz promenna "zdroj" nemusi obsahovat zadne udaje, je zde podminka, ktera zajistuje jeji (ne)zobrazeni
if ($GLOBALS["clanek"]->Ukaz("zdroj")!=""): echo "| Zdroj: ".$GLOBALS["clanek"]->Ukaz("zdroj")." "; endif;
echo "<b><a href=\"index.php\">Zav�i tiskovou zpr�vu>>></a><br />
</td></tr></table>
</div>
<p></p>\n";
// -------------------------------- [konec - dl. clanek telo] --------------------------------
  break;
endswitch;
?>