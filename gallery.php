<?php
######################################################################
# phpRS Gallery 0.99.500
######################################################################
// phpRS Copyright (c) 2001-2004 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// Gallery Copyright (c) 2004 by Michal Safus (michalsafus@gmail.com)
// http://www.phprs.cz/magazin/gallery
// This program is free software. - Toto je bezplatny a svobodny software.

define("IN_CODE",true); // inic. ochranne konstanty

include_once("config.php");
include_once("specfce.php");
include_once("myweb.php");
include_once("sl.php");
include_once("trlayout.php");
include_once($adrlayoutu);

include_once("gal_funkce/gal_fnc_kom.php");
include_once("gal_funkce/gal_fnc_spec.php");
include_once("gal_funkce/gal_fnc_galerie.php");
include_once("gal_funkce/gal_fnc_obrazky.php");
include_once("gal_funkce/gal_fnc_interni.php");
include_once("gal_funkce/gal_fnc_kategorie.php");
include_once("gal_funkce/gal_fnc_pristup.php");
include_once("gal_funkce/gal_fnc_lang_cz.php");


// Pokud chcete nastavit galerii svuj jazyk, musite vytvorit soubor napriklad
// gal_fnc_lang_sk.php, zkopirovat do nej obsah souboru gal_fnc_lang_cz.php
// a vsechny hlasky prelozit. Pote upravite cestu na hornim radku.
// Pokud chcete přeložit i šablony, musíte si zkopírovat obsah adresáře gal_sablony_cz
// do adresare napr gal_sablony_sk, prelozit a nastavit si dale v promenne adresu
// k vasemu novemu adresari

$GLOBALS["galerie"]["phprs"]=NactiKonfigHod("phprs_verze","varchar");
$GLOBALS["galerie"]["verze"]="0.99.500";
$GLOBALS["ftp"]["admin"]=NactiKonfigHod("ftp_admin","varchar");
$GLOBALS["ftp"]["autor"]=NactiKonfigHod("ftp_autor","varchar");
$GLOBALS["ftp"]["ctenar"]=NactiKonfigHod("ftp_ctenar","varchar");
$GLOBALS["galerie"]["interni"]=NactiKonfigHod("galerie_interni","varchar");
$GLOBALS["velikost_obrazek"]=NactiKonfigHod("velikost_obrazek","varchar");
$GLOBALS["velikost_galerie"]=NactiKonfigHod("velikost_galerie","varchar");
$GLOBALS["galerie"]["pocet"]["admin"]=NactiKonfigHod("pocet_gal_admin","varchar");
$GLOBALS["galerie"]["pocet"]["redaktor"]=NactiKonfigHod("pocet_gal_redaktor","varchar");
$GLOBALS["galerie"]["pocet"]["autor"]=NactiKonfigHod("pocet_gal_autor","varchar");
$GLOBALS["galerie"]["pocet"]["ctenar"]=NactiKonfigHod("pocet_gal_ctenar","varchar");
$GLOBALS["obrazek"]["top"]=NactiKonfigHod("top_prehled","varchar");
$GLOBALS["galerie"]["strankovani"]=NactiKonfigHod("strankovani","varchar");
$GLOBALS["sablony"]["cesta"]=NactiKonfigHod("sablony","varchar");
$GLOBALS["gallery_dir"]=NactiKonfigHod("gallery_dir","varchar");
$GLOBALS["hromadne_pridani_obrazku"]=NactiKonfigHod("hromadne_pridani","varchar");
$GLOBALS["thumb_max_width"]=NactiKonfigHod("nahled_sirka","varchar");
$GLOBALS["thumb_max_height"]=NactiKonfigHod("nahled_vyska","varchar");
$GLOBALS["pocet_znaku"]=NactiKonfigHod("pocet_znaku","varchar");


$GLOBALS["vyrazne"]="<span class=\"gal_vyrazne\">"; // jak se oznacuje zvyraznena chyba?
$GLOBALS["/vyrazne"]="</span>";   // konec zvyraznane chyby
$GLOBALS["kdo"][""]=""; // bezpecnosti opatreni - nemenit


  // znamkovani - ulozeni cookies
  if (isset($GLOBALS["hlasovani"])):
    ZnamkujObr($GLOBALS["media_id"],$GLOBALS["hlasovani"]);
  endif;
 
 if(!JePovolena()): $GLOBALS["akce"]="nepovolena"; endif; // pokud nemame galerii nainstalovanou
  
  // nastaveni kvuli zobrazovani okolnich sloupcu
  if(!isset($GLOBALS["akce"])): $GLOBALS["akce"]=""; endif; 
  if($GLOBALS["akce"]=="obrazek_ukaz" OR $GLOBALS["akce"]=="obrazek_ukaz_interni" OR $GLOBALS["akce"]=="comment_add" OR $GLOBALS["akce"]=="comment_re" OR $GLOBALS["akce"]=="comment_fullview"):
   $sloupce=0;
  else:
   $sloupce=1;
  endif;  
if($sloupce):
 if($GLOBALS["galerie"]["phprs"]=="235"):
  $vzhledwebu = new CLayout(); // inic. vzhledove tridy  
  $vzhledwebu->Hlavicka();
  $vzhledwebu->GenerujTabulkuPred();
  ObrTabulka();  // Vlozeni layout prvku 
 else:
  $vzhledwebu->UlozPro("styl2","\n  <link rel=\"stylesheet\" href=\"image/bgv/css/gallery.css.php\" type=\"text/css\">");
  $vzhledwebu->Generuj();
  //ObrTabulka();  // Vlozeni layout prvku
  ObrTabulka(4);  // Vlozeni layout prvku
 endif; 
endif; 


 /* Akce tykajici se prace s galeriemi */
 switch ($GLOBALS["akce"]):
  case "nepovolena":
   $GLOBALS["chyba"]="<center>Administrátor galerii nenainstaloval/neaktivoval.</center>";
   Formular("galerie_chyba");
  break;

  /* Akce tykajici se prace s kategoriemi */
  case "kategorie_prehled": // Prehled galerii
   echo "<div class=\"gal_nadpis\">".GAL_NADPIS_KAT_PREHLED."</div><hr class=\"gal_cara\" /><center>";
   KategoriePrehled();
  break;
  case "kategorie_ukaz": // Prehled galerii
   echo "<center>";
   KategorieUkaz();
  break;  

  /* Akce tykajici se prase s interni galerii */
  case "galerie_prehled_interni": // Prehled galerii
   echo "<div class=\"gal_nadpis\">".GAL_NADPIS_GAL_PREHLED_INT."</div><hr class=\"gal_cara\" /><center>";
   GaleriePrehledInterni();
  break;
  case "galerie_ukaz_interni": // Zobrazeni vybrane galerie
   echo "<center>";
   GalerieUkazInterni($GLOBALS["galerie_id"],$GLOBALS["gal_ukaz_order"]);
  break;   
  case "obrazek_ukaz_interni": // Zobrazeni obrazku
   ObrazekUkazInterni($GLOBALS["media_id"], "picture_show");
  break;  

  /* Akce tykajici se prace s galerii */
  case "galerie_prehled": // Prehled galerii
   echo "<div class=\"gal_nadpis\">".GAL_NADPIS_GAL_PREHLED."</div><hr class=\"gal_cara\" /><center>";
   GaleriePrehled();
  break;
  case "galerie_nova": // Pridani nove galerie
   echo "<div class=\"gal_nadpis\">".GAL_NADPIS_GAL_NOVA."</div><hr class=\"gal_cara\" /><center>";
   GalerieNova();
  break;
  case "galerie_ukaz": // Zobrazeni vybrane galerie
   echo "<center>";
   GalerieUkaz($GLOBALS["galerie_id"],$GLOBALS["gal_ukaz_order"]);
  break; 
  case "galerie_uprav": // Upraveni vybrane galerie
   echo "<div class=\"gal_nadpis\">".GAL_NADPIS_GAL_UPRAV."</div><hr class=\"gal_cara\" /><center>";
   GalerieUprav($GLOBALS["galerie_id"]);
  break;
  case "galerie_smaz": // Smazani vybrane galerie
   echo "<div class=\"gal_nadpis\">".GAL_NADPIS_GAL_SMAZ."</div><hr class=\"gal_cara\" /><center>";
   GalerieSmaz($GLOBALS["galerie_id"]);
  break;
  
  /* Akce tykajici se prace s obrazky */
  case "obrazek_ukaz": // Zobrazeni obrazku
   ObrazekUkaz($GLOBALS["media_id"], "picture_show");
  break;
  case "obrazek_uprav": // Upraveni obrazku
   echo "<div class=\"gal_nadpis\">".GAL_NADPIS_OBR_UPRAV."</div><hr class=\"gal_cara\" /><center>";
   ObrazekUprav($GLOBALS["media_id"]);
  break;  
  case "obrazek_smaz": // Smazani obrazku
   echo "<div class=\"gal_nadpis\">".GAL_NADPIS_OBR_SMAZ."</div><hr class=\"gal_cara\" /><center>";
   ObrazekSmaz();
  break;  
  case "obrazek_novy": // Pridani jednoho obrazku
   echo "<div class=\"gal_nadpis\">".GAL_NADPIS_OBR_NOVY."</div><hr class=\"gal_cara\" /><center>";
   ObrazekPridejZkontroluj("jeden");
  break;
  case "obrazek_novy_rozcest": // Rozcestnik pro pridani obrazku
   echo "<div class=\"gal_nadpis\">".GAL_NADPIS_OBR_NOVY_ROZCEST."</div><hr class=\"gal_cara\" /><center>";
   ObrazekPridejZkontroluj("rozcest");
  break;
  case "obrazek_novy_auto": // Automaticke pridani vice obrazku
   echo "<div class=\"gal_nadpis\">".GAL_NADPIS_OBR_NOVY_AUTO."</div><hr class=\"gal_cara\" /><center>";
   ObrazekPridejZkontroluj("auto");
  break;
  case "obrazek_novy_manual": // Manualni pridani vice obrazku
   echo "<div class=\"gal_nadpis\">".GAL_NADPIS_OBR_NOVY_MANUAL."</div><hr class=\"gal_cara\" /><center>";
   ObrazekPridejZkontroluj("manual");
  break;
  case "obrazek_novy_ftp": // Manualni pridani vice obrazku
   echo "<div class=\"gal_nadpis\">".GAL_NADPIS_OBR_NOVY_MANUAL."</div><hr class=\"gal_cara\" /><center>";
   ObrazekPridejZkontroluj("ftp");
  break;  
  case "obrazek_top": // Prehled nej obrazku
   echo "<center>";
   ObrazekTop();
  break;
  case "verze": // Zobrazeni verze galerie
   echo "<center>";
   Verze();
  break;  

  /* Akce tykajici se komentaru */  
  case "comment_add": // Pridani noveho komentare
   ObrazekUkaz($GLOBALS["media_id"], "comment_add");
  break;
  case "comment_re": // Odpoved na komentar
   ObrazekUkaz($GLOBALS["media_id"], "comment_re");
  break;
  case "comment_fullview": // Zobrazeni vsech komentaru
   ObrazekUkaz($GLOBALS["media_id"], "comment_fullview");
  break;
  
  default: // Defaultne prehled vsech galerii
   echo "<div class=\"gal_nadpis\">".GAL_NADPIS_GAL_PREHLED."</div><hr class=\"gal_cara\" /><center>";
   GaleriePrehled();
  break; 
 endswitch; 
 
 /* Zobrazeni navigace, vice podrobnosti u funkce Navigace() - nekde nahore */

if($sloupce):
 Navigace($GLOBALS["akce"]);
 echo "</center>";
 if($GLOBALS["galerie"]["phprs"]=="235"):
  KonecObrTabulka();   // Vlozeni layout prvku
  $vzhledwebu->GenerujTabulkuZa();
  $vzhledwebu->Paticka();
 else:
  //KonecObrTabulka();  // Vlozeni layout prvku
  KonecObrTabulka(4);  // Vlozeni layout prvku
  // dokonceni tvorby stranky
  $vzhledwebu->Generuj();
 endif; 
endif; 
?>
