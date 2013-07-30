<?

######################################################################
# phpRS Layout Engine 2.3.0 - verze: "Bewell3"
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
<!-- <a href=\"search.php?rsvelikost=uvod&amp;rstext=all-phpRS-all&amp;rstema=".$GLOBALS["clanek"]->Ukaz("tema_id")."\"><img src=\"".$GLOBALS["clanek"]->Ukaz("tema_obr")."\" border=\"0\" align=\"right\" alt=\"téma\" /></a> -->
<img src=\"image/bewell3/bullet.gif\" border=\"0\" hspace=\"3\" alt=\"*\" /> <span class=\"clanadpis\">".$GLOBALS["clanek"]->Ukaz("titulek")."</span><br />
<span class=\"malesede\">Vydáno dne ".$GLOBALS["clanek"]->Ukaz("datum")." (".$GLOBALS["clanek"]->Ukaz("visit")." pøeètení)</span><br /><br />
".$GLOBALS["clanek"]->Ukaz("uvod")."<br /><br />
<span class=\"malesede\">( <b>Tisková zpráva</b> |
<a href=\"comment.php?akce=view&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\">Poèet komentáøù</a>: ".$GLOBALS["clanek"]->Ukaz("pocet_kom") ." |
<a href=\"comment.php?akce=new&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\">Pøidat komentáø</a> |
<a href=\"rservice.php?akce=info&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\" target=\"_blank\"><img src=\"image/bewell3/mail.gif\" height=\"19\" width=\"28\" border=\"0\" hspace=\"0\" vspace=\"1\" align=\"absmiddle\" alt=\"Informaèní e-mail\" /></a><a href=\"rservice.php?akce=tisk&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\" target=\"_blank\"><img src=\"image/bewell3/print.gif\" height=\"19\" width=\"28\" border=\"0\" hspace=\"0\" vspace=\"1\" align=\"absmiddle\" alt=\"Vytisknout èlánek\" /></a> )<br />
</span>
</div>
</div>
<p></p>\n";
// --------------------------------- [konec - kratky clanek] ---------------------------------
  break;
  case "nahled":
// ----------------------------------- [dl. clanek nahled] -----------------------------------
echo "<div class=\"modryram\">
<div class=\"z\">
<!-- <a href=\"search.php?rsvelikost=uvod&amp;rstext=all-phpRS-all&amp;rstema=".$GLOBALS["clanek"]->Ukaz("tema_id")."\"><img src=\"".$GLOBALS["clanek"]->Ukaz("tema_obr")."\" border=\"0\" align=\"right\" alt=\"téma\" /></a> -->
<img src=\"image/bewell3/bullet.gif\" border=\"0\" hspace=\"3\" alt=\"*\" /> <a href=\"view.php?cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\" class=\"clanek\"><span class=\"clanadpis\">".$GLOBALS["clanek"]->Ukaz("titulek")."</span></a><br />
<span class=\"malesede\">Vydáno dne ".$GLOBALS["clanek"]->Ukaz("datum")." (".$GLOBALS["clanek"]->Ukaz("visit")." pøeètení)</span><br /><br />
".$GLOBALS["clanek"]->Ukaz("uvod")."<br /><br />
<span class=\"malesede\">( <b>Tisková zpráva</b> |
<a href=\"view.php?cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\">Celá zpráva...</a> |
<a href=\"comment.php?akce=view&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\">Poèet komentáøù</a>: ".$GLOBALS["clanek"]->Ukaz("pocet_kom") ." |
<a href=\"comment.php?akce=new&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\">Pøidat komentáø</a> |
<a href=\"rservice.php?akce=info&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\" target=\"_blank\"><img src=\"image/bewell3/mail.gif\" height=\"19\" width=\"28\" border=\"0\" hspace=\"0\" vspace=\"1\" align=\"absmiddle\" alt=\"Informaèní e-mail\" /></a><a href=\"rservice.php?akce=tisk&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\" target=\"_blank\"><img src=\"image/bewell3/print.gif\" height=\"19\" width=\"28\" border=\"0\" hspace=\"0\" vspace=\"1\" align=\"absmiddle\" alt=\"Vytisknout èlánek\" /></a> )<br />
</span>
</div>
</div>
<p></p>\n";
// ------------------------------- [konec - dl. clanek nahled] -------------------------------
  break;
  case "cely":
// ------------------------------------ [dl. clanek telo] ------------------------------------
echo "<div class=\"premodryram\">
<div class=\"z\">
<!-- <a href=\"search.php?rsvelikost=uvod&amp;rstext=all-phpRS-all&amp;rstema=".$GLOBALS["clanek"]->Ukaz("tema_id")."\"><img src=\"".$GLOBALS["clanek"]->Ukaz("tema_obr")."\" border=\"0\" align=\"right\" alt=\"téma\" /></a> -->
<img src=\"image/bewell3/bullet.gif\" border=\"0\" hspace=\"3\" alt=\"*\" /> <span class=\"clanadpis\">".$GLOBALS["clanek"]->Ukaz("titulek")."</span><br />
<span class=\"malesede\">Vydáno dne ".$GLOBALS["clanek"]->Ukaz("datum")." (".$GLOBALS["clanek"]->Ukaz("visit_plus")." pøeètení)</span><br /><br />
<span class=\"clatext\">".$GLOBALS["clanek"]->Ukaz("uvod")."<br /><br />".$GLOBALS["clanek"]->Ukaz("text")."</span><br /><br />\n";
SouvisejiciCl($GLOBALS["clanek"]->Ukaz("link"));
echo "<span class=\"malesede\">( <b>Celá tisková zpráva</b> |
<a href=\"comment.php?akce=view&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\">Poèet komentáøù</a>: ".$GLOBALS["clanek"]->Ukaz("pocet_kom")." |
<a href=\"comment.php?akce=new&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\">Pøidat komentáø</a> |
<a href=\"rservice.php?akce=info&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\" target=\"_blank\"><img src=\"image/bewell3/mail.gif\" height=\"19\" width=\"28\" border=\"0\" hspace=\"0\" vspace=\"1\" align=\"absmiddle\" alt=\"Informaèní e-mail\" /></a><a href=\"rservice.php?akce=tisk&amp;cisloclanku=".$GLOBALS["clanek"]->Ukaz("link")."\" target=\"_blank\"><img src=\"image/bewell3/print.gif\" height=\"19\" width=\"28\" border=\"0\" hspace=\"0\" vspace=\"1\" align=\"absmiddle\" alt=\"Vytisknout èlánek\" /></a> ";
// Pozor, jelikoz promenna "zdroj" nemusi obsahovat zadne udaje, je zde podminka, ktera zajistuje jeji (ne)zobrazeni
if ($GLOBALS["clanek"]->Ukaz("zdroj")!=""): echo "| Zdroj: ".$GLOBALS["clanek"]->Ukaz("zdroj")." "; endif;
echo ")<br />
</span>
</div>
</div>
<p></p>\n";
// -------------------------------- [konec - dl. clanek telo] --------------------------------
  break;
endswitch;
?>