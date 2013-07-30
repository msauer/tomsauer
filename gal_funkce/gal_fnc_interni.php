<?php
######################################################################
# phpRS Gallery 0.99.500
######################################################################
// phpRS Copyright (c) 2001-2004 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// Gallery Copyright (c) 2004 by Michal Safus (michalsafus@gmail.com)
// http://www.phprs.cz/magazin/gallery
// This program is free software. - Toto je bezplatny a svobodny software.

// Prace s interni galerii ------------------------------------------------------------------------

function GaleriePrehledInterni() {
if($GLOBALS["galerie"]["interni"]):
 $spocti_galerie=mysql_query("select count(*) as pocet from ".$GLOBALS["rspredpona"]."imggal_sekce WHERE prava='1:0:0' OR prava='1:1:0' OR prava='1:1:1' OR prava='1:0:1' order by ids" ,$GLOBALS["dbspojeni"]); // spocitame si vsechny galerie
 $pocet_galerie=mysql_fetch_array($spocti_galerie); // spocitame si vsechny galerie
 $kolik_s_obrazky=$pocet_galerie["pocet"];
 if($kolik_s_obrazky>0):

  $odgalerie=0;
  if (isset($GLOBALS["strana"])): $odgalerie=($GLOBALS["strana"]-1)*$GLOBALS["galerie"]["strankovani"];
  else: $GLOBALS["strana"]=1; endif;
  if ($spocti_galerie>0): $GLOBALS["pocet_str"]=ceil($kolik_s_obrazky/$GLOBALS["galerie"]["strankovani"]);
  else: $GLOBALS["pocet_str"]=ceil($kolik_s_obrazky/10); // defaultni mnozstvi galerii na str. 10
  endif;

  $vyber_galerie=mysql_query("select * from ".$GLOBALS["rspredpona"]."imggal_sekce WHERE prava='1:0:0' OR prava='1:1:0' OR prava='1:1:1' OR prava='1:0:1' order by ids LIMIT ".$odgalerie.",".$GLOBALS["galerie"]["strankovani"]."",$GLOBALS["dbspojeni"]); //dotaz na databazi
  $spocti_galerie=mysql_num_rows($vyber_galerie);
for($pom=0;$pom<$spocti_galerie;$pom++):
 $id=mysql_result($vyber_galerie,$pom,"ids");
 $vyber_obrazky=mysql_query("SELECT COUNT(ido) AS pocet_obrazku, SUM(obr_vel) AS velikost_obrazku FROM ".$GLOBALS["rspredpona"]."imggal_obr WHERE sekce='".$id."'",$GLOBALS["dbspojeni"]);    
 $admin_jmeno=mysql_query("select jmeno from ".$GLOBALS["rspredpona"]."user where idu='".mysql_result($vyber_galerie,$pom,vlastnik)."'",$GLOBALS["dbspojeni"]);
 $admin_jmeno=mysql_fetch_array($admin_jmeno);
 $velobr=mysql_fetch_array($vyber_obrazky);
 if($velobr["pocet_obrazku"]>0):
 $GLOBALS["prehled_id"]=$id;
 $GLOBALS["prehled_pocet"]=$velobr["pocet_obrazku"];
 $GLOBALS["prehled_velikost"]=round(($velobr["velikost_obrazku"]/1048576),2)." MB";
 $GLOBALS["prehled_vlastnik_jmeno"]=$admin_jmeno["jmeno"];
 $GLOBALS["prehled_titulek"]=mysql_result($vyber_galerie,$pom,"nazev");
 $GLOBALS["prehled_popis"]=mysql_result($vyber_galerie,$pom,"popis");
 $vyber_obrazek=mysql_query("select * from ".$GLOBALS["rspredpona"]."imggal_obr where sekce='".$id."' order by ido",$GLOBALS["dbspojeni"]);
 $GLOBALS["prehled_obrazek_id"]=mysql_result($vyber_obrazek,0,"ido");              // ID obrazku
 $GLOBALS["prehled_obrazek_nadpis"]=mysql_result($vyber_obrazek,0,"nazev");      // Titulek obrazku
 $GLOBALS["prehled_obrazek_src"]=mysql_result($vyber_obrazek,0,"nahl_poloha");        // Adresa obrazku
 $GLOBALS["prehled_obrazek_descr"]=mysql_result($vyber_obrazek,0,"popis");     // Popis obrazku
 $GLOBALS["prehled_obrazek_width"]=mysql_result($vyber_obrazek,0,"nahl_width");  // Sirka obrazku
 $GLOBALS["prehled_obrazek_height"]=mysql_result($vyber_obrazek,0,"nahl_height"); // Vyska obrazku
 Formular("galerie_prehled_interni");
 endif;
endfor;

Strankovani("galerie_prehled_interni",$kolik_s_obrazky,$GLOBALS["galerie"]["strankovani"],$GLOBALS["strana"]);

else:
 echo GAL_INTERNI_NEEX;
endif;
else:
 echo GAL_INTERNI_NE;
endif;

}



/* Funkce na zobrazovani vybrane galerie */
function GalerieUkazInterni($cislogalerie, $order) {
if($GLOBALS["galerie"]["interni"]):
 if(!isset($GLOBALS["gal_ukaz_pocet"])): $GLOBALS["gal_ukaz_pocet"]="20"; endif;
 switch ($order): // jak chceme zobrazit obrazky razene?
  case "date_up": $order="ido DESC"; $GLOBALS["date_up"]="selected"; break;  // podle id
  case "date_down": $order="ido ASC"; $GLOBALS["date_down"]="selected"; break;  // podle id
//  case "view": $order="media_view"; break; // podle poctu zobrazeni
//  case "hodn": $order="(media_znamka/media_hodnotilo)"; break; // podle hodnoceni
  default: $order="ido"; break; // defaultne podle id
 endswitch;

 $odgalerie=0;
  if (isset($GLOBALS["strana"])):
   $odgalerie=($GLOBALS["strana"]-1)*$GLOBALS["gal_ukaz_pocet"];
  else:
   $GLOBALS["strana"]=1;
  endif;
 $vyber_obrazky=mysql_query("select * from ".$GLOBALS["rspredpona"]."imggal_obr WHERE sekce='".$cislogalerie."' ORDER BY ".$order." LIMIT ".$odgalerie.",".$GLOBALS["gal_ukaz_pocet"]."",$GLOBALS["dbspojeni"]); //vybereme obrazek z databaze
 $spocti_obrazky=mysql_NumRows($vyber_obrazky); // spocitame si vybrane souvisejici obrazky
  for ($pom=0;$pom<$spocti_obrazky;$pom++):
   // do poli vlozime udaje o obrazcich pred
   $GLOBALS["obrazek_id"][]=mysql_result($vyber_obrazky,$pom,"ido"); // pole s ID obrazku
   $GLOBALS["obrazek_src"][]=mysql_result($vyber_obrazky,$pom,"nahl_poloha"); // pole s adresami obrazku
   $GLOBALS["obrazek_width"][]=mysql_result($vyber_obrazky,$pom,"nahl_width"); // pole s sirkami obrazku
   $GLOBALS["obrazek_height"][]=mysql_result($vyber_obrazky,$pom,"nahl_height"); // pole s vyskami obrazku
   $GLOBALS["obrazek_title"][]=mysql_result($vyber_obrazky,$pom,"nazev"); //  pole s nadpisy obrazku
  endfor; 
 $vyber_udaje=mysql_query("select * from ".$GLOBALS["rspredpona"]."imggal_sekce where ids='".$cislogalerie."'",$GLOBALS["dbspojeni"]);
 $admin_jmeno=mysql_query("select jmeno from ".$GLOBALS["rspredpona"]."user where idu='".mysql_result($vyber_udaje,0,vlastnik)."'",$GLOBALS["dbspojeni"]);
 $admin_jmeno=mysql_fetch_array($admin_jmeno);
 $GLOBALS["galerie_ukaz_user"]=$admin_jmeno["jmeno"]; // jmeno uzivatele
 $GLOBALS["galerie_ukaz_title"]=mysql_result($vyber_udaje,0,"nazev"); // nadpis galerie
 $GLOBALS["galerie_ukaz_id"]=mysql_result($vyber_udaje,0,"ids"); // ID galerie
 $GLOBALS["galerie_ukaz_description"]=mysql_result($vyber_udaje,0,"popis"); //popis galerie

 $strankovani_pocet_obrazku=mysql_query("select count(*) as pocet from ".$GLOBALS["rspredpona"]."imggal_obr WHERE sekce='".$cislogalerie."'",$GLOBALS["dbspojeni"]); //vybereme obrazek z databaze
 $strankovani_pocet_obrazku=mysql_result($strankovani_pocet_obrazku,0,pocet);
 
 

 
 $kolikobrazku=count($GLOBALS["obrazek_src"]);
$pocet_sloupcu=3;
if ($kolikobrazku>0):
   $GLOBALS["ukaz_obrazky"].="<table cellspacing=\"8\" cellpadding=\"0\" class=\"gal_ukaz_galerie_obrazek\" align=\"center\">\n";
   for ($pom=0;$pom<$kolikobrazku;$pom++):
     if (($pom % $pocet_sloupcu) == 0):
       $GLOBALS["ukaz_obrazky"].="<tr class=\"z\">";
     else:
       $GLOBALS["ukaz_obrazky"].="<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>"; // mezera mezi sloupci
     endif;
     $GLOBALS["ukaz_obrazky"].="<td width=\"130\" class=\"gal_galerie_ukaz_titulek\" align=\"center\"><a target=\"_blank\" href=\"gallery.php?akce=obrazek_ukaz_interni&amp;media_id=".$GLOBALS["obrazek_id"][$pom]."\">".$GLOBALS["obrazek_title"]["$pom"]."<br /><img src=\"".$GLOBALS["obrazek_src"][$pom]."\" width=\"".$GLOBALS["obrazek_width"][$pom]."\" height=\"".$GLOBALS["obrazek_height"][$pom]."\" alt=\"".$GLOBALS["obrazek_title"][$pom]."\" title=\"".$GLOBALS["obrazek_title"][$pom]."\"></a>";
     $GLOBALS["ukaz_obrazky"].="</td>\n";
     if (($pom % $pocet_sloupcu) == ($pocet_sloupcu-1)):
       $GLOBALS["ukaz_obrazky"].="</tr>\n";
     endif;
   endfor;

   $chybi=$pom % $pocet_sloupcu;
   if ($chybi > 0):
     for ($pom=0; $pom < ($pocet_sloupcu - $chybi); $pom++):
       $GLOBALS["ukaz_obrazky"].= "<td></td><td></td>";
     endfor;
     $GLOBALS["ukaz_obrazky"].="</tr>\n";
   endif;
   $GLOBALS["ukaz_obrazky"].="</table>\n";
 Formular("galerie_ukaz_interni");
 Strankovani("galerie_ukaz_interni",$strankovani_pocet_obrazku,$GLOBALS["gal_ukaz_pocet"],$GLOBALS["strana"]);
 endif;
 else:
 echo GAL_INTERNI_NE;
endif;
}

/* Funkce na zobrazeni obrazku*/
function ObrazekUkazInterni($media_id, $akce) {
if($GLOBALS["galerie"]["interni"]):
 if ($media_id!=""): // pokud je urcen obrazek
  $ukaz_obrazek=mysql_query("select * from ".$GLOBALS["rspredpona"]."imggal_obr WHERE ido='$media_id'",$GLOBALS["dbspojeni"]); //vybereme obrazek z databaze
  $spocti_obrazek=mysql_NumRows($ukaz_obrazek); // spocitame si obrazky = musi vyjit 1
  if ($spocti_obrazek==1):
    $GLOBALS["picture_show_media_gallery_id"]=mysql_result($ukaz_obrazek,0,"ido");   // ID galerie
    $GLOBALS["picture_show_media_file"]=mysql_result($ukaz_obrazek,0,"obr_poloha");               // jmeno obrazku
    $GLOBALS["picture_show_media_caption"]=mysql_result($ukaz_obrazek,0,"nazev");         // titulek obrazku
    //$GLOBALS["picture_show_media_category"]=mysql_result($ukaz_obrazek,0,"media_category");       // kategorie
    $GLOBALS["picture_show_media_description"]=mysql_result($ukaz_obrazek,0,"popis"); // popis obrazku
    $GLOBALS["picture_show_media_width"]=mysql_result($ukaz_obrazek,0,"obr_width");             // sirka obrazku
    $GLOBALS["picture_show_media_height"]=mysql_result($ukaz_obrazek,0,"obr_height");           // vyska obrazku
    //$GLOBALS["picture_show_media_view"]=mysql_result($ukaz_obrazek,0,"media_view");               // pocet zobrazeni obrazku
    //$GLOBALS["picture_show_media_znamka"]=mysql_result($ukaz_obrazek,0,"media_znamka");           // celk. znamka
    //$GLOBALS["picture_show_media_hodnotilo"]=mysql_result($ukaz_obrazek,0,"media_hodnotilo");     // celk. pocet. hodn.
    $GLOBALS["picture_show_akce"]=$akce;                                                          // akce
    // mysql_query("update ".$GLOBALS["rspredpona"]."media set media_view=(media_view+1) where media_id='".$media_id."'",$GLOBALS["dbspojeni"]);
 else: // obrazek se nepodarilo v databazi najit
  $GLOBALS["chyba"]=GAL_OBR_UKAZ_NEEX;
 endif;
else: // nebyl urcen obrazek k zobrazeni
 $GLOBALS["chyba"]=GAL_OBR_UKAZ_NENI;
endif;
Formular("obrazek_ukaz_interni");
else:
 echo GAL_INTERNI_NE;
endif;
}

// Konec prace s interni galerii ------------------------------------------------------------------------


?>