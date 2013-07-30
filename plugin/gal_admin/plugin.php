<?php
######################################################################
# phpRS Gallery 0.99.500
######################################################################
// phpRS Copyright (c) 2001-2004 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// Gallery Copyright (c) 2004 by Michal Safus (michalsafus@gmail.com)
// http://www.phprs.cz/magazin/gallery
// This program is free software. - Toto je bezplatny a svobodny software.

// jmeno plug-inu
$plugin_nazev="phpRS Gallery";
// pristupova prava; 1 = jen admin. modul, 2 = admin. a autorsky modul
$pi_pristup="1";
// pridat polozku do hlavniho administracniho menu; ano = 1, ne = 0
$pi_menu="1";
// nazev noveho tlacitka v admin. menu
$pi_nazev_menu="phpRS Gallery";
// relativni cesta k souboru s "rozcestnikem" k admin. sekci
$pi_inclakce_menu="plugin/gal_admin/rozcestnik.php";
// identifikacni retezec modulu (max. 15 znaku)
$pi_indent_modulu='gallery'; 
// volaci link zakladni funce
$pi_link_menu="akce=gal_prehled";
// pridat aktivacni polozku do seznamu systemovych bloku; ano = 1, ne = 0
$pi_sys_blok="1";
// nazev systemoveho bloku
$pi_nazev_blok="Systémový blok: Gallery - obrázek";
// identifikacni zkratka systemoveho bloku (3 znaky)
$pi_zkratka_blok="gob";
// relativni cesta k vykonnemu soubour
$pi_inclsb_blok="plugin/gal_admin/ablok.php";
// nazev vyvolane systemove funkce - nutno zapisovat bez prazdnych kulatych zavorek na konci
$pi_funkce_blok="UkazObrazek";
?>
