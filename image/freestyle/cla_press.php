<?

######################################################################
# phpRS Layout Engine 2.6.0 - verze: "FreeStyle"
#                           - clanek sablona: "Press"
######################################################################

// Copyright (c) 2002-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

if (!isset($rs_typ_clanku)): $rs_typ_clanku=""; endif;

switch ($rs_typ_clanku):
  case "kratky":
// ------------------------------------- [kratky clanek] -------------------------------------
?>
<div class="modryram">
<div class="z">
<h1 class="cla-nadpis"><img src="image/freestyle/bullet.gif" border="0" hspace="3" alt="*" /> <? echo $GLOBALS["clanek"]->Ukaz("titulek"); ?></h1>
<span class="cla-informace">Vydáno dne <? echo $GLOBALS["clanek"]->Ukaz("datum"); ?> (<? echo $GLOBALS["clanek"]->Ukaz("visit"); ?> přečtení)</span><br /><br />
<div class="cla-text"><? echo $GLOBALS["clanek"]->Ukaz("uvod"); ?></div><br />
<strong>Tisková zpráva</strong> |
<a href="comment.php?akce=view&amp;cisloclanku=<? echo $GLOBALS["clanek"]->Ukaz("link"); ?>">Počet komentářů</a>: <? echo $GLOBALS["clanek"]->Ukaz("pocet_kom"); ?> |
<a href="comment.php?akce=new&amp;cisloclanku=<? echo $GLOBALS["clanek"]->Ukaz("link"); ?>">Přidat komentář</a> |
<a href="rservice.php?akce=info&amp;cisloclanku=<? echo $GLOBALS["clanek"]->Ukaz("link"); ?>" target="_blank"><img src="image/freestyle/mail.gif" height="22" width="20" border="0" hspace="0" vspace="1" align="middle" alt="Informační e-mail" /></a><a href="rservice.php?akce=tisk&amp;cisloclanku=<? echo $GLOBALS["clanek"]->Ukaz("link"); ?>" target="_blank"><img src="image/freestyle/printer.gif" height="22" width="20" border="0" hspace="0" vspace="1" align="middle" alt="Vytisknout článek" /></a><br />
</div>
</div>
<p></p>
<?
// --------------------------------- [konec - kratky clanek] ---------------------------------
  break;
  case "nahled":
// ----------------------------------- [dl. clanek nahled] -----------------------------------
?>
<div class="modryram">
<div class="z">
<h1 class="cla-nadpis"><img src="image/freestyle/bullet.gif" border="0" hspace="3" alt="*" /> <a href="view.php?cisloclanku=<? echo $GLOBALS["clanek"]->Ukaz("link"); ?>" class="clanek"><? echo $GLOBALS["clanek"]->Ukaz("titulek"); ?></a></h1>
<span class="cla-informace">Vydáno dne <? echo $GLOBALS["clanek"]->Ukaz("datum"); ?> (<? echo $GLOBALS["clanek"]->Ukaz("visit"); ?> přečtení)</span><br /><br />
<div class="cla-text"><? echo $GLOBALS["clanek"]->Ukaz("uvod"); ?></div><br />
<a href="view.php?cisloclanku=<? echo $GLOBALS["clanek"]->Ukaz("link"); ?>"><strong>Celý tisková zpráva...</strong></a> |
<a href="comment.php?akce=view&amp;cisloclanku=<? echo $GLOBALS["clanek"]->Ukaz("link"); ?>">Počet komentářů</a>: <? echo $GLOBALS["clanek"]->Ukaz("pocet_kom"); ?> |
<a href="comment.php?akce=new&amp;cisloclanku=<? echo $GLOBALS["clanek"]->Ukaz("link"); ?>">Přidat komentář</a> |
<a href="rservice.php?akce=info&amp;cisloclanku=<? echo $GLOBALS["clanek"]->Ukaz("link"); ?>" target="_blank"><img src="image/freestyle/mail.gif" height="22" width="20" border="0" hspace="0" vspace="1" align="middle" alt="Informační e-mail" /></a><a href="rservice.php?akce=tisk&amp;cisloclanku=<? echo $GLOBALS["clanek"]->Ukaz("link"); ?>" target="_blank"><img src="image/freestyle/printer.gif" height="22" width="20" border="0" hspace="0" vspace="1" align="middle" alt="Vytisknout článek" /></a><br />
</div>
</div>
<p></p>
<?
// ------------------------------- [konec - dl. clanek nahled] -------------------------------
  break;
  case "cely":
// ------------------------------------ [dl. clanek telo] ------------------------------------
?>
<div class="premodryram">
<div class="z">
<h1 class="cla-nadpis"><img src="image/freestyle/bullet.gif" border="0" hspace="3" alt="*" /> <? echo $GLOBALS["clanek"]->Ukaz("titulek"); ?></h1>
<span class="cla-informace">Vydáno dne <? echo $GLOBALS["clanek"]->Ukaz("datum"); ?> (<? echo $GLOBALS["clanek"]->Ukaz("visit"); ?> přečtení)</span><br /><br />
<div class="cla-text"><? echo $GLOBALS["clanek"]->Ukaz("uvod"); ?><br /><br /><? echo $GLOBALS["clanek"]->Ukaz("text"); ?></div><br />
<?
SouvisejiciCl($GLOBALS["clanek"]->Ukaz("link"));
?>
<strong>Celá tisková zpráva</strong> |
<a href="comment.php?akce=view&amp;cisloclanku=<? echo $GLOBALS["clanek"]->Ukaz("link"); ?>">Počet komentářů</a>: <? echo $GLOBALS["clanek"]->Ukaz("pocet_kom"); ?> |
<a href="comment.php?akce=new&amp;cisloclanku=<? echo $GLOBALS["clanek"]->Ukaz("link"); ?>">Přidat komentář</a> |
<a href="rservice.php?akce=info&amp;cisloclanku=<? echo $GLOBALS["clanek"]->Ukaz("link"); ?>" target="_blank"><img src="image/freestyle/mail.gif" height="22" width="20" border="0" hspace="0" vspace="1" align="middle" alt="Informační e-mail" /></a><a href="rservice.php?akce=tisk&amp;cisloclanku=<? echo $GLOBALS["clanek"]->Ukaz("link"); ?>" target="_blank"><img src="image/freestyle/printer.gif" height="22" width="20" border="0" hspace="0" vspace="1" align="middle" alt="Vytisknout článek" /></a>
<?
// Pozor, jelikoz promenna "zdroj" nemusi obsahovat zadne udaje, je zde podminka, ktera zajistuje jeji (ne)zobrazeni
if ($GLOBALS["clanek"]->Ukaz("zdroj")!=''): echo '| Zdroj: '.$GLOBALS["clanek"]->Ukaz("zdroj").' '; endif;
?>
<br />
</div>
</div>
<p></p>
<?
// -------------------------------- [konec - dl. clanek telo] --------------------------------
  break;
endswitch;
?>