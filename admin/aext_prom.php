<?

######################################################################
# phpRS Extrakce GET a POST promennych a jejich zpracovani 1.0.0
######################################################################

// Copyright (c) 2001-2004 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// funkce pro vytvoreni reference mezi dvema poli
function Extract_prom($pole, &$target)
{
if (!is_array($pole)):
  return false;
else:
  $is_magic_quotes = get_magic_quotes_gpc();
  reset($pole);

  while (list($klic,$hodnota) = each($pole)):
    if (is_array($hodnota)):
      Extract_prom($hodnota,$target[$klic]);
    else:
      if ($is_magic_quotes):
        $target[$klic]=stripslashes($hodnota);
      else:
        $target[$klic]=$hodnota;
      endif;
    endif;
  endwhile;

  reset($pole);
  return true;
endif;
}

// je GET prazdne
if (!empty($_GET)):
  Extract_prom($_GET,$GLOBALS);
endif;
// je POST prazdne
if (!empty($_POST)):
  Extract_prom($_POST,$GLOBALS);
endif;

?>