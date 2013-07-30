<?php
######################################################################
# phpRS Gallery 0.99.500
######################################################################
// phpRS Copyright (c) 2001-2004 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// Gallery Copyright (c) 2004 by Michal Safus (michalsafus@gmail.com)
// http://www.phprs.cz/magazin/gallery
// This program is free software. - Toto je bezplatny a svobodny software.


function UkazObrazek() {
 $jaky_obrazek=mysql_query("SELECT ");
 $rozdel=explode("_",$GLOBALS["vzhledwebu"]->AktBlokNazev());
 $nazev_bloku=$rozdel[1];
 switch($rozdel[0]):
  case "nahodny": $podminka="where media_smazano!='1' ORDER BY RAND() LIMIT 1"; break;
  case "nejnovejsi": $podminka="where media_smazano!='1' ORDER BY 'media_id' DESC LIMIT 1"; break;
//  case "nejhodnoceny": break;
 endswitch;

 $obrazek=mysql_query("SELECT * FROM ".$GLOBALS["rspredpona"]."media ".$podminka,$GLOBALS["dbspojeni"]);
 $obrazek=mysql_fetch_array($obrazek);
 $obr_cesta=$obrazek["media_thumbnail"];
 $obr_id=$obrazek["media_id"];
 $obr_width=$obrazek["media_thumbnail_width"];
 $obr_height=$obrazek["media_thumbnail_height"];
 $obr_popis=$obrazek["media_description"];
 $obr_titulek=$obrazek["media_caption"];
 $obr_zobrazeni=$obrazek["media_view"];
 $obr_mnozstvi=$obrazek["media_hodnotilo"];
 $obr_znamka=$obrazek["media_znamka"];
 
 if ($obr_mnozstvi>0):
  $obr_znamka=number_format(($obr_znamka/$obr_mnozstvi),2,'.','');
 else:
  $obr_znamka="0";
 endif;
 
 $obrazek="<div style=\"font-size: 11px; font-weight: bold; text-align: center; margin: auto;\">".$obr_titulek."</div>";
 $obrazek.="<div style=\"font-size: 10px; margin: auto; text-align: center; padding: 2px;\"><a target=\"_blank\" href=\"gallery.php?akce=obrazek_ukaz&amp;media_id=".$obr_id."\"><img style=\"border: 1px solid #000000;\" width=\"".$obr_width."\" height=\"".$obr_height."\" src=\"".$obr_cesta."\" alt=\"".$obr_popis."\" title=\"".$obr_popis."\"></a>";
 $obrazek.="<br />zobrazení: ".$obr_zobrazeni."<br />známka: ".$obr_znamka."</div>";

  switch ($GLOBALS["vzhledwebu"]->AktBlokTyp()):
    case 1: Blok1($nazev_bloku,$obrazek); break;
    case 2: Blok2($nazev_bloku,$obrazek); break;
    case 3: Blok3($nazev_bloku,$obrazek); break;
    case 4: Blok4($nazev_bloku,$obrazek); break;
    case 5: Blok5($nazev_bloku,$obrazek); break;
    case 6: Blok6($nazev_bloku,$obrazek); break;
    default: Blok1($nazev_bloku,$obrazek); break;
  endswitch;


}






?>