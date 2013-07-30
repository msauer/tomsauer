<?

######################################################################
# phpRS Admin Standard Comment library 1.0.2
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

/*
  -- function --
  VycistiKoment($txt = '')
  PrelozKomZnacky($txt = '')
  KorekceVstupu($txt = '')
  KorekceVelikosti($txt = '')

*/

// ====================== FUNCTION

function VycistiKoment($txt = '')
{
if ($txt!=''):
  // ochrana proti nekterym nebezpecnych tagum: JavaScripty, BODY, APPLET, nevhodne META tagy
  $hledam = array('<script','<body','<applet','<meta');
  $nahrazuji = array('&lt;script','&lt;body','&lt;applet','&lt;meta');
  $txt=str_replace($hledam,$nahrazuji,$txt);
endif;

return $txt;
}

function PrelozKomZnacky($txt = '')
{
if ($txt!=''):
  // pole "komentarovych znacek"
  $hledam = array(
        "/\[url\]((http:\/\/|https:\/\/|ftp:\/\/|)([a-z0-9\.\-@:]+)[a-z0-9;\/\?:@=\&\$\-_\.\+!*'\(\),\#%~]*?)\[\/url\]/is",
        // "/\[url\]((http:\/\/|https:\/\/|ftp:\/\/|)([a-z0-9\.\-@:]+)[a-z0-9;\/\?:@=\&\$\-_\.\+!*'\(\),\#%~]*?)\[\/url\]/ies", - prepinac "e" umoznuje v ramci vysledku spustit PHP kod
        "/\[url=((http:\/\/|https:\/\/|ftp:\/\/|)[a-z0-9;\/\?:@=\&\$\-_\.\+!*'\(\),~%#]+?)\](.+?)\[\/url\]/is",
        "/\[email\]([a-z0-9\-_\.\+]+@[a-z0-9\-]+\.[a-z0-9\-\.]+?)\[\/email\]/is",
        "/\[color=([\#a-z0-9]+?)\](.+?)\[\/color\]/is",
        "/\[b\](.+?)\[\/b\]/is",
        "/\[u\](.+?)\[\/u\]/is",
        "/\[i\](.+?)\[\/i\]/is",
        "/\[code\](.+?)\[\/code\]/is");
  // pole prekladovych HTML alternativ
  $nahrazuji = array(
        "<a href=\"$1\">$1</a>",
        // "'<a href=\"'.test_adresa('$1').'\">'.test_adresa('$1').'</a>'", - napr. test na adresu (zde jde pouze o ukazku!)
        "<a href=\"$1\">$3</a>",
        "<a href=\"mailto:$1\">$1</a>",
        "<span style=\"color: $1\">$2</span>",
        "<b>$1</b>",
        "<u>$1</u>",
        "<i>$1</i>",
        "<pre>$1</pre>");
  $txt=preg_replace($hledam,$nahrazuji,$txt);
endif;

return $txt;
}

function KorekceVstupu($txt = '')
{
if ($txt!=''):
  // pole nepripustnych znaku
  $hledam = array ("'&(?!#)'i",
                 "'<'i",
                 "'>'i",
                 "'\"'i");
  // pole alternativ
  $nahrazuji = array ("&amp;",
                 "&lt;",
                 "&gt;",
                 "&quot;");
  $txt=preg_replace($hledam,$nahrazuji,$txt);
endif;

return $txt;
}

function KorekceVelikosti($txt = '')
{
if ($txt!=''):
  // max. delka celeho komentare
  if (empty($GLOBALS['rsconfig']['max_delka_komentare'])): $GLOBALS['rsconfig']['max_delka_komentare']=1000; endif;
  if (strlen($txt)>$GLOBALS['rsconfig']['max_delka_komentare']):
    $txt=substr($txt,0,$GLOBALS['rsconfig']['max_delka_komentare']);
  endif;
  // max. delka slova
  if (empty($GLOBALS['rsconfig']['max_delka_slova'])): $GLOBALS['rsconfig']['max_delka_slova']=50; endif;
  $txt=wordwrap($txt,$GLOBALS['rsconfig']['max_delka_slova']," ",1);
endif;

return $txt;
}

?>
