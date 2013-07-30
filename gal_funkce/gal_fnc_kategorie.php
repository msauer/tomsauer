<?php
######################################################################
# phpRS Gallery 0.99.500
######################################################################
// phpRS Copyright (c) 2001-2004 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// Gallery Copyright (c) 2004 by Michal Safus (michalsafus@gmail.com)
// http://www.phprs.cz/magazin/gallery
// This program is free software. - Toto je bezplatny a svobodny software.

function Kategorie($co,$id) {
 $udaje=mysql_query("select kat_nazev,kat_popis from ".$GLOBALS["rspredpona"]."gallery_kategorie where idk='".$id."'",$GLOBALS["dbspojeni"]);
 $kategorie=mysql_fetch_array($udaje);
 $udaje2=mysql_query("SELECT COUNT(media_id) AS pocet_obrazku, SUM(media_size) AS velikost_obrazku from ".$GLOBALS["rspredpona"]."media where media_category='".$id."' and media_smazano!='1'",$GLOBALS["dbspojeni"]);
 $obrazky=mysql_fetch_array($udaje2);
 
 switch($co):
  case "kategorie_nazev": return $kategorie["kat_nazev"]; break;
  case "kategorie_popis": return $kategorie["kat_popis"]; break;
  case "kategorie_velikost": return $obrazky["velikost_obrazku"]; break;
  case "kategorie_pocetobr": return $obrazky["pocet_obrazku"]; break;
 endswitch;
}



function KategoriePrehled() {
 $mysql=mysql_query("select idk,kat_nazev,kat_popis from ".$GLOBALS["rspredpona"]."gallery_kategorie order by idk",$GLOBALS["dbspojeni"]); 
 $pocet_mysql=mysql_num_rows($mysql);
 while($kat=mysql_fetch_array($mysql)):
  $vyber=mysql_query("select COUNT(media_id) AS pocet, SUM(media_size) AS velikost from ".$GLOBALS["rspredpona"]."media where media_category='".$kat["idk"]."' and media_smazano!='1'",$GLOBALS["dbspojeni"]);
  $pocet=mysql_fetch_array($vyber);
  $pocet=$pocet["pocet"];
  if($pocet>0):
   $GLOBALS["kat_kategorie_pocet"]=Kategorie("kategorie_pocetobr",$kat["idk"]);
   $GLOBALS["kat_kategorie_velikost"]=round(Kategorie("kategorie_velikost",$kat["idk"])/1024,2)." MB";
   $vyber_obrazek=mysql_query("select media_id from ".$GLOBALS["rspredpona"]."media where media_category='".$kat["idk"]."' and media_smazano!='1' ORDER BY media_id desc LIMIT 0,1",$GLOBALS["dbspojeni"]);
   $obrazek=mysql_fetch_array($vyber_obrazek); 
   if(Obrazek("galerie_smazana",$obrazek["media_id"])==0):   
   $GLOBALS["kat_obrazek_id"]=$obrazek["media_id"];              // ID obrazku
   $GLOBALS["kat_obrazek_nadpis"]=Obrazek("obrazek_titulek",$obrazek["media_id"]);      // Titulek obrazku
   $GLOBALS["kat_obrazek_src"]=Obrazek("nahled_cesta",$obrazek["media_id"]);        // Adresa obrazku
   $GLOBALS["kat_obrazek_descr"]=Obrazek("nahled_popis",$obrazek["media_id"]);     // Popis obrazku
   $GLOBALS["kat_obrazek_width"]=Obrazek("nahled_sirka",$obrazek["media_id"]);  // Sirka obrazku
   $GLOBALS["kat_obrazek_height"]=Obrazek("nahled_vyska",$obrazek["media_id"]); // Vyska obrazku
   $GLOBALS["kat_kategorie_jmeno"]=Kategorie("kategorie_nazev",$kat["idk"]);
   $GLOBALS["kat_kategorie_cislo"]=$kat["idk"];
   Formular("kategorie_prehled"); 
   endif;
  endif;
 endwhile;
}

function KategorieUkaz() {
 if($GLOBALS["kat_id"]!=""):
 $mysql=mysql_query("select idk,kat_nazev,kat_popis from ".$GLOBALS["rspredpona"]."gallery_kategorie where idk=".$GLOBALS["kat_id"],$GLOBALS["dbspojeni"]); 
 $kat=mysql_fetch_array($mysql);
 if($kat["idk"]!=""):
 $GLOBALS["kategorie_ukaz_title"]=$kat["kat_nazev"];
 $GLOBALS["kategorie_ukaz_popis"]=$kat["kat_popis"];
 
 if(!isset($GLOBALS["kat_ukaz_pocet"])): $GLOBALS["kat_ukaz_pocet"]="20"; endif;
 if(!isset($GLOBALS["kat_ukaz_order"])): $GLOBALS["kat_ukaz_order"]="date"; endif;
 switch ($GLOBALS["kat_ukaz_order"]): // jak chceme zobrazit obrazky razene?
  case "date_up": $order="media_id DESC"; $GLOBALS["date_up"]="selected"; break;  // podle id
  case "date_down": $order="media_id ASC"; break; $GLOBALS["date_down"]="selected";  // podle id
  case "view_up": $order="media_view DESC"; break; $GLOBALS["view_up"]="selected"; // podle poctu zobrazeni
  case "view_down": $order="media_view ASC"; break; $GLOBALS["view_down"]="selected"; // podle poctu zobrazeni
  case "hodn_up": $order="(media_znamka/media_hodnotilo) DESC"; $GLOBALS["hodn_up"]="selected"; break; // podle hodnoceni
  case "hodn_down": $order="(media_znamka/media_hodnotilo) ASC"; $GLOBALS["hodn_down"]="selected"; break; // podle hodnoceni
  default: $order="media_id"; break; // defaultne podle id
 endswitch;
 $odgalerie=0;
  if (isset($GLOBALS["strana"])):
   $odgalerie=($GLOBALS["strana"]-1)*$GLOBALS["kat_ukaz_pocet"];
  else:
   $GLOBALS["strana"]=1;
  endif;
 $vyber_obrazky=mysql_query("select * from ".$GLOBALS["rspredpona"]."media WHERE media_category='".$GLOBALS["kat_id"]."' and media_smazano!='1' ORDER BY ".$order." LIMIT ".$odgalerie.",".$GLOBALS["kat_ukaz_pocet"]."",$GLOBALS["dbspojeni"]); //vybereme obrazek z databaze
  $spocti_obrazky=mysql_NumRows($vyber_obrazky); // spocitame si vybrane souvisejici obrazky
  for ($pom=0;$pom<$spocti_obrazky;$pom++):
   // do poli vlozime udaje o obrazcich pred
   $GLOBALS["obrazek_id"][]=mysql_result($vyber_obrazky,$pom,"media_id"); // pole s ID obrazku
   $GLOBALS["obrazek_src"][]=mysql_result($vyber_obrazky,$pom,"media_thumbnail"); // pole s adresami obrazku
   $GLOBALS["obrazek_width"][]=mysql_result($vyber_obrazky,$pom,"media_thumbnail_width"); // pole s sirkami obrazku
   $GLOBALS["obrazek_height"][]=mysql_result($vyber_obrazky,$pom,"media_thumbnail_height"); // pole s vyskami obrazku
   $GLOBALS["obrazek_title"][]=mysql_result($vyber_obrazky,$pom,"media_caption"); //  pole s nadpisy obrazku
  endfor; 
  
 $strankovani_pocet_obrazku=mysql_query("select count(*) as pocet from ".$GLOBALS["rspredpona"]."media WHERE media_category='".$GLOBALS["kat_id"]."' and media_smazano!='1'",$GLOBALS["dbspojeni"]); //vybereme obrazek z databaze
 $strankovani_pocet_obrazku=mysql_result($strankovani_pocet_obrazku,0,pocet);
 
 
 $kolikobrazku=count($GLOBALS["obrazek_src"]);
 $pocet_sloupcu=3;
 if ($kolikobrazku>0):
   $GLOBALS["ukaz_obrazky"].="<form action=\"gallery.php?akce=obrazek_smaz\" method=\"post\">\n";
   $GLOBALS["ukaz_obrazky"].="<table cellspacing=\"8\" cellpadding=\"0\" class=\"gal_ukaz_galerie_obrazek\" align=\"center\">\n";
   for ($pom=0;$pom<$kolikobrazku;$pom++):
     if (($pom % $pocet_sloupcu) == 0):
       $GLOBALS["ukaz_obrazky"].="<tr class=\"z\">";
     else:
       $GLOBALS["ukaz_obrazky"].="<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>"; // mezera mezi sloupci
     endif;
     $GLOBALS["ukaz_obrazky"].="<td width=\"130\" class=\"gal_galerie_ukaz_titulek\" align=\"center\"><a target=\"_blank\" href=\"gallery.php?akce=obrazek_ukaz&amp;media_id=".$GLOBALS["obrazek_id"][$pom]."\">".$GLOBALS["obrazek_title"]["$pom"]."<br /><img src=\"".$GLOBALS["obrazek_src"][$pom]."\" width=\"".$GLOBALS["obrazek_width"][$pom]."\" height=\"".$GLOBALS["obrazek_height"][$pom]."\" alt=\"".$GLOBALS["obrazek_title"][$pom]."\" title=\"".$GLOBALS["obrazek_title"][$pom]."\"></a>";

     $GLOBALS["ukaz_obrazky"].="</td>\n";
     if (($pom % $pocet_sloupcu) == ($pocet_sloupcu-1)):
       $GLOBALS["ukaz_obrazky"].="</tr>\n";
     endif;
   endfor;

   $chybi=$pom % $pocet_sloupcu;
   if ($chybi > 0):
     for ($pom=0; $pom < ($pocet_sloupcu - $chybi); $pom++):
       $GLOBALS["ukaz_obrazky"].="<td></td><td></td>";
     endfor;
     $GLOBALS["ukaz_obrazky"].="</tr>\n";
   endif;
   $GLOBALS["ukaz_obrazky"].="</table>\n";
 endif; 

    Formular("kategorie_ukaz");
    Strankovani("kategorie_ukaz",$strankovani_pocet_obrazku,$GLOBALS["kat_ukaz_pocet"],$GLOBALS["strana"]);
 else:
  echo GAL_CHYBA_KAT_NEEX;
 endif;
 else:
  echo GAL_CHYBA_KAT_NENI;
 endif; 
}

function VypisKategorie() {
 $mysql=mysql_query("select idk,kat_nazev,kat_popis from ".$GLOBALS["rspredpona"]."gallery_kategorie order by idk",$GLOBALS["dbspojeni"]); 
 $vypis.="<select class=\"gal_input\" name=\"kategorie_id[]\" size=\"1\"> "; 
  while($kat=mysql_fetch_array($mysql)):
   $vypis.="<option value=\"".$kat["idk"]."\">".$kat["kat_nazev"]."</option>";
  endwhile;
 $vypis.="</select>";
 return $vypis;
}

?>