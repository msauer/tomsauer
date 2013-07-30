<?

######################################################################
# phpRS Layout Engine 2.6.0 - verze: "FreeStyle"
#                           - clanek sablona: "Tisk" / standardni tiskova sablona
######################################################################

// Copyright (c) 2002-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// ------------------------------------ [standardni clankova tiskova sablona] ------------------------------------
?>
<div class="z">
<h1 class="cla-nadpis"><? echo $GLOBALS["clanek"]->Ukaz("titulek"); ?></h1>
<span class="cla-informace-tisk"><i>
<? echo RS_CS_AUTOR; ?>: <? echo $GLOBALS["clanek"]->Ukaz("autor_jm"); ?> &lt;<? echo $GLOBALS["clanek"]->Ukaz("autor_jen_mail"); ?>&gt;,
<? echo RS_CS_TEMA; ?>: <? echo $GLOBALS["clanek"]->Ukaz("tema_jm"); ?>,
<? if ($GLOBALS["clanek"]->Ukaz("zdroj")!=''): echo RS_CS_ZDROJ.': '.$GLOBALS["clanek"]->Ukaz("zdroj").', '; endif; ?>
<? echo RS_CS_VYDANO_DNE; ?>: <? echo $GLOBALS["clanek"]->Ukaz("datum"); ?>
</i></span><br />
<hr />
<div class="cla-text"><? echo $GLOBALS["clanek"]->Ukaz("uvod"); ?><br /><br /><? echo $GLOBALS["clanek"]->Ukaz("text"); ?></div><br />
</div>
<?
// -------------------------------- [konec - standardni clankova tiskova sablona] --------------------------------

?>