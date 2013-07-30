<?php
######################################################################
# phpRS Gallery 0.99.500
######################################################################
// phpRS Copyright (c) 2001-2004 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// Gallery Copyright (c) 2004 by Michal Safus (michalsafus@gmail.com)
// http://www.phprs.cz/magazin/gallery
// This program is free software. - Toto je bezplatny a svobodny software.

/* Funkce, ktera tvori prehled galerii */
function GaleriePrehled() {
 $pocet_galerie=mysql_query("select count(distinct gallery_id) as pocet from ".$GLOBALS["rspredpona"]."gallery,".$GLOBALS["rspredpona"]."media as med where gallery_delete!=1 and ".$GLOBALS["rspredpona"]."gallery.gallery_id=med.media_gallery_id and med.media_smazano!=1",$GLOBALS["dbspojeni"]); 
 $pocet_galerie=mysql_result($pocet_galerie,0,"pocet");
  if($pocet_galerie>0):
   if(!isset($GLOBALS["strana"])): $GLOBALS["strana"]=1; endif; // nastaveni strany pro strankovani
   $kdezacit=$GLOBALS["galerie"]["strankovani"]*$GLOBALS["strana"]-$GLOBALS["galerie"]["strankovani"]; // kde se ma zacit s vyberem galerii
   $dotaz_galerie=mysql_query("select distinct gal.gallery_id,gal.gallery_zobrazeni,gal.gallery_title,gal.gallery_user_id,gal.gallery_description,gal.gallery_admin,gal.gallery_zalozeni,gal.gallery_uprava,gal.gallery_zobrazit from ".$GLOBALS["rspredpona"]."gallery as gal,".$GLOBALS["rspredpona"]."media as med where med.media_smazano!=1 and gal.gallery_delete!=1 AND gal.gallery_id=med.media_gallery_id order by gal.gallery_id limit $kdezacit,".$GLOBALS["galerie"]["strankovani"]."",$GLOBALS["dbspojeni"]);
    while($galerie=mysql_fetch_array($dotaz_galerie)):
     switch($galerie["gallery_zobrazit"]):
      case "1": $obrazek_jaky="(media_znamka/media_hodnotilo)"; break;
      case "2": $obrazek_jaky="media_view"; break;
      case "3": $obrazek_jaky="rand()"; break;
      default: $obrazek_jaky="rand()"; break;
     endswitch; 
    $obrazek=mysql_query("select * from ".$GLOBALS["rspredpona"]."media where media_gallery_id='".$galerie["gallery_id"]."' and media_smazano!='1' order by ".$obrazek_jaky." desc limit 1",$GLOBALS["dbspojeni"]);
    $obrazek=mysql_fetch_array($obrazek);
    $GLOBALS["prehled_id"]=$galerie["gallery_id"] ; // id galerie
    $GLOBALS["prehled_titulek"]=Galerie("titulek",$galerie["gallery_id"]) ; // titulek galerie
    $GLOBALS["prehled_zobrazeni"]=Galerie("pocetzobr",$galerie["gallery_id"]); // pocet zobrazeni galerie
    $GLOBALS["prehled_obrazek_id"]=$obrazek["media_id"]; // id obrazku
    $GLOBALS["prehled_obrazek_src"]=$obrazek["media_thumbnail"]; // cesta k nahledu
    $GLOBALS["prehled_obrazek_width"]=$obrazek["media_thumbnail_width"]; // sirka nahledu
    $GLOBALS["prehled_obrazek_height"]=$obrazek["media_thumbnail_height"]; // vyska nahledu
    $GLOBALS["prehled_obrazek_descr"]=$obrazek["media_description"]; // vyska nahledu
    $GLOBALS["prehled_vlastnik_prezdivka"]=Galerie("vlastnik_pr",$galerie["gallery_id"]);
    $GLOBALS["prehled_vlastnik_jmeno"]=Galerie("vlastnik_jm",$galerie["gallery_id"]);
    $GLOBALS["prehled_pocet"]=Galerie("pocetobr",$galerie["gallery_id"]);
    $GLOBALS["prehled_velikost"]=round(Galerie("velikost",$galerie["gallery_id"])/1024,2);
    $GLOBALS["prehled_posledni_zmena"]=Galerie("uprava",$galerie["gallery_id"]);
    $GLOBALS["prehled_zalozeni"]=Galerie("zalozeni",$galerie["gallery_id"]);
    $popis=explode(" ",wordwrap($galerie["gallery_description"],18," ",1)); $cislo=0;
     while($popis[$cislo]!=""): $GLOBALS["prehled_popis"].=$popis[$cislo]." ";
      if(strlen($GLOBALS["prehled_popis"])>$GLOBALS["pocet_znaku"]): $GLOBALS["prehled_popis"].="&hellip;"; break; endif; $cislo++;
     endwhile;
     if((Uzivatel("id_ctenar")==$galerie["gallery_user_id"] and $galerie["gallery_admin"]==0) or (Uzivatel("id_admin")==$galerie["gallery_user_id"] and $galerie["gallery_admin"]==1)):
      $GLOBALS["admintext"]=GAL_ADMINISTRACE; 
      $GLOBALS["administrace"]="<a href=\"gallery.php?akce=galerie_uprav&amp;galerie_id=".$GLOBALS["prehled_id"]."\">".GAL_EDITUJ."</a>
       - <a href=\"gallery.php?akce=galerie_smaz&amp;do_it=0&amp;galerie_id=".$GLOBALS["prehled_id"]."\">".GAL_SMAZ."</a>"; 
     else:
      $GLOBALS["admintext"]=""; $GLOBALS["administrace"]="";
     endif;
    Formular("galerie_prehled");
    $GLOBALS["prehled_popis"]="";
    endwhile;
    Strankovani("galerie_prehled",$pocet_galerie,$GLOBALS["galerie"]["strankovani"],$GLOBALS["strana"]);
  else: // zadna galerie nenalezena
   $GLOBALS["chyba"]=GAL_PREHLED_ZADNA;
   Formular("galerie_chyba");
  endif;
}

/* Fukce na zalozeni nove galerie */
function GalerieNova() {
 if(Uzivatel("je_admin") OR Uzivatel("je_redaktor") OR Uzivatel("je_autor")): $admin=1; $konec=0;
 elseif(Uzivatel("je_ctenar")): $admin=0; $konec=0;
 else: $konec=1; 
 endif;
  if($konec==0):
   $GLOBALS["galerie_pocet"]=Uzivatel("pocet_galerii_zbyva");
   if($GLOBALS["galerie_pocet"]>0):
    $GLOBALS["galerie_velikost"]=NactiKonfigHod("velikost_galerie","varchar");
    if($_POST["do_it"]):
     if($GLOBALS["galerie_titulek"]!=""  AND $GLOBALS["galerie_popis"]!="" AND $GLOBALS["galerie_obrazek_jaky"]!="" AND $GLOBALS["galerie_verejna"]!=""):
      $GLOBALS["galerie_titulek"]=htmlspecialchars($GLOBALS["galerie_titulek"]);
      $GLOBALS["galerie_popis"]=htmlspecialchars($GLOBALS["galerie_popis"]);
      if(Uzivatel("je_admin")):
       $admin=1; $user=Uzivatel("id_admin");
      elseif(Uzivatel("je_redaktor")):
       $admin=1; $user=Uzivatel("id_redaktor");
      elseif(Uzivatel("je_autor")):
       $admin=1; $user=Uzivatel("id_autor");
      elseif(Uzivatel("je_ctenar")):
       $admin=0; $user=Uzivatel("id_ctenar");
      endif;
      $mysql=mysql_query("insert into ".$GLOBALS["rspredpona"]."gallery values('','".$GLOBALS["galerie_titulek"]."','".$user."','".$GLOBALS["galerie_popis"]."','".$admin."',NOW(),NOW(),'".$GLOBALS["galerie_obrazek_jaky"]."','0','0','".$GLOBALS["galerie_verejna"]."') ",$GLOBALS["dbspojeni"]); 
      if($mysql): // uspesne se ulozilo do databaze
       $GLOBALS["chyba"]=GAL_OK_DATABAZE;
       $GLOBALS["galerie_zalozeni"]=date("d.m.Y");
       Formular("galerie_nova_ok"); 
      else: // nepodarilo se ulozit do databaze
       $GLOBALS["chyba"]=GAL_CHYBA_DATABAZE;
       Formular("galerie_chyba");
      endif;    
     else: // uzivatel nevyplnil vsechny udaje
      $GLOBALS["chyba"]=GAL_CHYBA_UDAJE; 
      Formular("galerie_nova");
     endif;
    else: // chceme jen zobrazit formular
     Formular("galerie_nova");
    endif;
   else: // uzivatel neni prihlasen
    $GLOBALS["chyba"]=GAL_CHYBA_MAX;
    Formular("galerie_chyba");
   endif;
  else: // uzivatel neni prihlasen
   $GLOBALS["chyba"]=GAL_CHYBA_NOVA;
   Formular("galerie_chyba");
  endif;
}

/* Funkce na upraveni galerie */
function GalerieUprav($id) {
 $over=mysql_query("SELECT gallery_admin,gallery_user_id FROM ".$GLOBALS["rspredpona"]."gallery WHERE gallery_id='".$id."' AND gallery_delete!='1'",$GLOBALS["dbspojeni"]);
 $pole=mysql_fetch_array($over);
  if($pole["gallery_admin"]!=""):
   if($pole["gallery_admin"]==1):
    if((Uzivatel("je_admin") and $pole["gallery_user_id"]==Uzivatel("id_admin")) or (Uzivatel("je_redaktor") and $pole["gallery_user_id"]==Uzivatel("id_redaktor")) or (Uzivatel("je_autor") and $pole["gallery_user_id"]==Uzivatel("id_autor"))):
     $ok=1; else: $ok=0; endif;
   elseif($pole["gallery_admin"]==0):
    if((Uzivatel("je_ctenar") and $pole["gallery_user_id"]==Uzivatel("id_ctenar"))):
     $ok=1; else: $ok=0; endif;
   endif;
   
   if($ok):
    $udaje=mysql_query("SELECT gallery_zalozeni,gallery_uprava,gallery_title,gallery_description,gallery_zobrazit,gallery_verejna,gallery_zobrazeni FROM ".$GLOBALS["rspredpona"]."gallery WHERE gallery_id='".$id."' AND gallery_delete!='1'",$GLOBALS["dbspojeni"]); // vybereme udaje z databaze
    $GLOBALS["galerie_uprava"]=mysql_result($udaje,0,"gallery_uprava"); // kdy byla galerie naposledy upravena
    $GLOBALS["galerie_zalozeni"]=mysql_result($udaje,0,"gallery_zalozeni"); // datum zalozeni galerie
    if(!isset($GLOBALS["galerie_titulek"])):  $GLOBALS["galerie_titulek"]=mysql_result($udaje,0,"gallery_title"); endif;// titulek galerie
    if(!isset($GLOBALS["galerie_popis"])):  $GLOBALS["galerie_popis"]=mysql_result($udaje,0,"gallery_description"); endif;// popis galerie
    if(!isset($GLOBALS["galerie_verejna"])):  $GLOBALS["galerie_verejna"]=mysql_result($udaje,0,"gallery_verejna"); endif; // je galerie verejna?
    if(!isset($GLOBALS["galerie_obrazek_ukaz"])):  $GLOBALS["galerie_obrazek_ukaz"]=mysql_result($udaje,0,"gallery_zobrazit"); endif; // jaky obrazek zobrazit?      
       switch($GLOBALS["galerie_verejna"]): // ulozime do promenne verejna0 nebo verejna1 hodnotu podle databaze
        case "0": $GLOBALS["verejna0"]="checked";  break;
        case "1": $GLOBALS["verejna1"]="checked"; break;
       endswitch;     
       switch($GLOBALS["galerie_obrazek_ukaz"]): // ulozime do promenne 1, 2 nebo 3 hodnotu jaky obrazek zobrazit
        case "1": $GLOBALS["1"]="checked"; break;
        case "2": $GLOBALS["2"]="checked"; break;
        case "3": $GLOBALS["3"]="checked"; break;
       endswitch;
    if($_POST["do_it"]!="1"): // pokud uzivatel jeste nechce galerii aktualizovat
      Formular("galerie_uprav");
    else: // uzivatel jiz chce upravit galerii odeslanymi udaji
     if($GLOBALS["galerie_titulek"]!="" AND $GLOBALS["galerie_popis"]!="" AND $GLOBALS["galerie_obrazek_ukaz"]!=""): // kontrola udaju
      $uprav_databazi=mysql_query("UPDATE ".$GLOBALS["rspredpona"]."gallery SET gallery_verejna='".$GLOBALS["galerie_verejna"]."', gallery_title='".$GLOBALS["galerie_titulek"]."', gallery_description='".$GLOBALS["galerie_popis"]."', gallery_zobrazit='".$GLOBALS["galerie_obrazek_ukaz"]."' WHERE gallery_id='".$id."'",$GLOBALS["dbspojeni"]);
       if($uprav_databazi): // pokud se podarilo udaje ulozit do databaze
        $GLOBALS["chyba"]=GAL_OK_DATABAZE;
        Formular("galerie_chyba");
       else: // neco v databazi se porouchalo :-) PS: ja vim, to neni k smichu, je to k placi :-)
        $GLOBALS["chyba"]=GAL_CHYBA_DATABAZE;
        Formular("galerie_uprav");
       endif;      
     else: // uzivatel nevyplnil vsechny udaje
      $GLOBALS["chyba"]=GAL_CHYBA_UDAJE;
      Formular("galerie_uprav");
     endif;    
    endif;
   else: // uzivatel nema opravneni editovat galerii
    $GLOBALS["chyba"]=GAL_CHYBA_UPRAV_NE; 
    Formular("galerie_uprav_ok");
   endif;
  else: // pocet galerii je 0 - tzn. galerie neexistuje
   $GLOBALS["chyba"]=GAL_CHYBA_UPRAV_EXIST; 
   Formular("galerie_uprav_ok");
  endif; 
}

/* Funkce na mazani galerie */
function GalerieSmaz($id) {
 $over=mysql_query("SELECT gallery_admin,gallery_user_id FROM ".$GLOBALS["rspredpona"]."gallery WHERE gallery_id='".$id."'",$GLOBALS["dbspojeni"]); //vybereme obrazek z databaze   
 $pole=mysql_fetch_array($over); 
  if((Uzivatel("je_admin") and Uzivatel("id_admin")==$pole["gallery_user_id"]) or (Uzivatel("je_redaktor") and Uzivatel("id_redaktor")==$pole["gallery_user_id"]) or (Uzivatel("je_autor") and Uzivatel("id_autor")==$pole["gallery_user_id"]) or (Uzivatel("je_ctenar") and Uzivatel("id_ctenar")==$pole["gallery_user_id"])): 
   if($_GET["do_it"]==1): // kdyz uz chceme upravit obrazek
    $delete=mysql_query("UPDATE ".$GLOBALS["rspredpona"]."gallery set gallery_delete='1' WHERE gallery_id='".$id."'",$GLOBALS["dbspojeni"]);
    $delete.=mysql_query("UPDATE ".$GLOBALS["rspredpona"]."media set media_smazano='1' WHERE media_gallery_id='".$id."'",$GLOBALS["dbspojeni"]);
    $GLOBALS["chyba"].=GAL_SMAZ_OK_OK;
    Formular("galerie_smaz");
   else: // otazka, zda OPRAVDU, ale OPRAVDU chete galerii smazat
    $GLOBALS["chyba"].=GAL_SMAZ_OK;
    $GLOBALS["chyba"].="<a href=\"gallery.php?akce=galerie_smaz&amp;do_it=1&amp;galerie_id=".$GLOBALS["galerie_id"]."\">".GAL_ANO."</a> - <a href=\"gallery.php\">".GAL_NE."</a>";
    Formular("galerie_smaz");
   endif; 
  else:
   $GLOBALS["chyba"].=GAL_SMAZ_NE;
   Formular("galerie_chyba");
  endif; 
}


/* Funkce na vypis galerii pri pridavani obrazku */
function VypisGalerie($galerie) {
$retezec="";
 if(Uzivatel("je_admin")):
  $galerie_admin=mysql_query("select gallery_title,gallery_id from ".$GLOBALS["rspredpona"]."gallery where gallery_user_id=".Uzivatel("id_admin")." and gallery_delete!='1' and gallery_verejna!='1'",$GLOBALS["dbspojeni"]); 
  $admintext="Administrátor";
 elseif(Uzivatel("je_redaktor")):
  $galerie_admin=mysql_query("select gallery_title,gallery_id from ".$GLOBALS["rspredpona"]."gallery where gallery_user_id=".Uzivatel("id_redaktor")." and gallery_delete!='1' and gallery_verejna!='1'",$GLOBALS["dbspojeni"]); 
  $admintext="Redaktor";
 elseif(Uzivatel("je_autor")):
  $galerie_admin=mysql_query("select gallery_title,gallery_id from ".$GLOBALS["rspredpona"]."gallery where gallery_user_id=".Uzivatel("id_autor")." and gallery_delete!='1' and gallery_verejna!='1'",$GLOBALS["dbspojeni"]); 
  $admintext="Autor";
 endif;
 if(Uzivatel("je_ctenar")):
  $galerie_ctenar=mysql_query("select gallery_title,gallery_id from ".$GLOBALS["rspredpona"]."gallery where gallery_user_id=".Uzivatel("id_ctenar")." and gallery_delete!='1' and gallery_verejna!='1'",$GLOBALS["dbspojeni"]); 
 endif;
  $galerie_verejna=mysql_query("select gallery_title,gallery_id from ".$GLOBALS["rspredpona"]."gallery where gallery_verejna='1' and gallery_delete!='1'",$GLOBALS["dbspojeni"]); 
  $retezec.="<select class=\"gal_navigace\" name=\"galerie_id\">";
 if(isset($galerie_admin)):

  $cislo=0; 
  while($pole=mysql_fetch_array($galerie_admin)):
  $udaje="(".ObrazekCestina(Galerie("pocetobr",$pole["gallery_id"])).", ".round(((Galerie("velikost",$pole["gallery_id"]))/1024),2)." MB)";
   if($cislo==0): $retezec.="<optgroup label=\"".$admintext."\">"; endif;
    if($pole["gallery_id"]==$galerie): $selected="selected"; else: $selected=""; endif;
   $retezec.="<option ".$selected." value=\"".$pole["gallery_id"]."\">".$pole["gallery_title"]." ".$udaje."</option>";
   $cislo++;
  endwhile;
 endif;
 
 if(isset($galerie_ctenar)):
 $cislo=0; 
  while($pole=mysql_fetch_array($galerie_ctenar)):
  $udaje="(".ObrazekCestina(Galerie("pocetobr",$pole["gallery_id"])).", ".round(((Galerie("velikost",$pole["gallery_id"]))/1024),2)." MB)";
   if($cislo==0): $retezec.="<optgroup label=\"Čtenář\">"; endif;
    if($pole["gallery_id"]==$galerie): $selected="selected"; else: $selected=""; endif;
   $retezec.="<option ".$selected." value=\"".$pole["gallery_id"]."\">".$pole["gallery_title"]." ".$udaje."</option>";
   $cislo++;
  endwhile; 
 endif; 
 
 if(isset($galerie_verejna)):
 $cislo=0; 
  while($pole=mysql_fetch_array($galerie_verejna)):
  $udaje="(".ObrazekCestina(Galerie("pocetobr",$pole["gallery_id"])).", ".round(((Galerie("velikost",$pole["gallery_id"]))/1024),2)." MB)";
   if($cislo==0): $retezec.="<optgroup label=\"Veřejné\">"; endif;
    if($pole["gallery_id"]==$galerie): $selected="selected"; else: $selected=""; endif;
   $retezec.="<option ".$selected." value=\"".$pole["gallery_id"]."\">".$pole["gallery_title"]." ".$udaje."</option>";
   $cislo++;
  endwhile; 
 endif; 
 
 $retezec.="</select>";
return $retezec;

}


/* Funkce na zobrazovani vybrane galerie */
function GalerieUkaz($cislogalerie, $order) {
 if(!isset($GLOBALS["gal_ukaz_pocet"]) or $GLOBALS["gal_ukaz_pocet"]<=0): $GLOBALS["gal_ukaz_pocet"]="20"; endif;
 if(!isset($GLOBALS["strana"])): $GLOBALS["strana"]=1; endif;
 switch ($order): // jak chceme zobrazit obrazky razene?
 case "date_up": $order="media_id DESC"; $GLOBALS["date_up"]="selected"; break;  // podle id
  case "date_down": $order="media_id ASC"; $GLOBALS["date_down"]="selected"; break;  // podle id
  case "view_up": $order="media_view DESC"; $GLOBALS["view_up"]="selected"; break; // podle poctu zobrazeni
  case "view_down": $order="media_view ASC"; $GLOBALS["view_down"]="selected"; break; // podle poctu zobrazeni
  case "hodn_up": $order="(media_znamka/media_hodnotilo) DESC"; $GLOBALS["hodn_up"]="selected"; break; // podle hodnoceni
  case "hodn_down": $order="(media_znamka/media_hodnotilo) ASC"; $GLOBALS["hodn_down"]="selected"; break; // podle hodnoceni
  default: $order="media_id"; $GLOBALS["media_id"]="selected"; break; // defaultne podle id
 endswitch;
 $galerie=mysql_query("select * from ".$GLOBALS["rspredpona"]."gallery where gallery_id='".$cislogalerie."' and gallery_delete!='1' ",$GLOBALS["dbspojeni"]); 
 $galerie=mysql_fetch_array($galerie);
 $kdezacit=$GLOBALS["gal_ukaz_pocet"]*$GLOBALS["strana"]-$GLOBALS["gal_ukaz_pocet"]; // kde se ma zacit s vyberem galerii
 $vyber_obrazky=mysql_query("select * from ".$GLOBALS["rspredpona"]."media WHERE media_gallery_id='".$cislogalerie."' and media_smazano!='1' ORDER BY ".$order." LIMIT ".$kdezacit.",".$GLOBALS["gal_ukaz_pocet"]."",$GLOBALS["dbspojeni"]); //vybereme obrazek z databaze
 $spocti_obrazky=mysql_NumRows($vyber_obrazky); // spocitame si vybrane souvisejici obrazky
  for ($pom=0;$pom<$spocti_obrazky;$pom++):
   // do poli vlozime udaje o obrazcich pred
   $GLOBALS["obrazek_id"][]=mysql_result($vyber_obrazky,$pom,"media_id"); // pole s ID obrazku
   $GLOBALS["obrazek_src"][]=mysql_result($vyber_obrazky,$pom,"media_thumbnail"); // pole s adresami obrazku
   $GLOBALS["obrazek_width"][]=mysql_result($vyber_obrazky,$pom,"media_thumbnail_width"); // pole s sirkami obrazku
   $GLOBALS["obrazek_height"][]=mysql_result($vyber_obrazky,$pom,"media_thumbnail_height"); // pole s vyskami obrazku
   $GLOBALS["obrazek_title"][]=mysql_result($vyber_obrazky,$pom,"media_caption"); //  pole s nadpisy obrazku
  endfor; 
  $GLOBALS["galerie_ukaz_user"]=Galerie("vlastnik_pr",$cislogalerie); // jmeno uzivatele
  $GLOBALS["galerie_ukaz_title"]=Galerie("titulek",$cislogalerie); // nadpis galerie
  $GLOBALS["galerie_ukaz_id"]=$cislogalerie; // ID galerie
  $GLOBALS["galerie_ukaz_description"]=Galerie("popis",$cislogalerie); //popis galerie

  if ($galerie["gallery_admin"]=="0"): // pokud galerie neni zalozena adminem
   if (Uzivatel("je_ctenar") AND $galerie["gallery_user_id"]==Uzivatel("id_ctenar")): $ok="1";
   else: $ok="0";
   endif; 
  else: // pokud galerie je zalozena adminem
   if ((Uzivatel("je_admin") and $galerie["gallery_user_id"]==Uzivatel("id_admin")) OR (Uzivatel("je_redaktor") and $galerie["gallery_user_id"]==Uzivatel("id_redaktor")) or (Uzivatel("je_autor") and $galerie["gallery_user_id"]==Uzivatel("id_autor"))): $ok="1";
   else: $ok="0"; 
   endif;   
  endif;
  $kolikobrazku=$spocti_obrazky;
  if ($kolikobrazku>0):
   $GLOBALS["ukaz_obrazky"].="<form action=\"gallery.php?akce=obrazek_smaz\" method=\"post\">\n";
   $GLOBALS["ukaz_obrazky"].="<table cellspacing=\"8\" cellpadding=\"0\" class=\"gal_ukaz_galerie_obrazek\" align=\"center\">\n";
    for ($pom=0;$pom<$kolikobrazku;$pom++):
     if (($pom % NactiKonfigHod("pocet_sloupcu","varchar")) == 0):
      $GLOBALS["ukaz_obrazky"].="<tr class=\"z\">";
     else:
      $GLOBALS["ukaz_obrazky"].="<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>"; // mezera mezi sloupci
     endif;
     $GLOBALS["ukaz_obrazky"].="<td width=\"130\" class=\"gal_galerie_ukaz_titulek\" align=\"center\"><a target=\"_blank\" href=\"gallery.php?akce=obrazek_ukaz&amp;media_id=".$GLOBALS["obrazek_id"][$pom]."\">".$GLOBALS["obrazek_title"]["$pom"]."<br /><img src=\"".$GLOBALS["obrazek_src"][$pom]."\" width=\"".$GLOBALS["obrazek_width"][$pom]."\" height=\"".$GLOBALS["obrazek_height"][$pom]."\" alt=\"".$GLOBALS["obrazek_title"][$pom]."\" title=\"".$GLOBALS["obrazek_title"][$pom]."\"></a>";
     if($ok):
      $GLOBALS["ukaz_obrazky"].="<br /><a href=\"gallery.php?akce=obrazek_uprav&amp;media_id=".$GLOBALS["obrazek_id"][$pom]."\">".GAL_EDITUJ."</a> / ".GAL_SMAZ." <input class=\"textpole\" type=\"checkbox\" name=\"picture_delete_check[]\" value=\"".$GLOBALS["obrazek_id"][$pom]."\">";
     endif;  
     $GLOBALS["ukaz_obrazky"].="</td>\n";
     if (($pom % NactiKonfigHod("pocet_sloupcu","varchar")) == ($pocet_sloupcu-1)):
      $GLOBALS["ukaz_obrazky"].="</tr>\n";
     endif;
    endfor;
    $chybi=$pom % NactiKonfigHod("pocet_sloupcu","varchar");
    if ($chybi > 0):
     for ($pom=0; $pom < (NactiKonfigHod("pocet_sloupcu","varchar") - $chybi); $pom++):
      $GLOBALS["ukaz_obrazky"].="<td></td><td></td>";
     endfor;
     $GLOBALS["ukaz_obrazky"].="</tr>\n";
    endif;
    $GLOBALS["ukaz_obrazky"].="</table>\n";
    if($ok): 
     $GLOBALS["ukaz_obrazky"].="<input type=\"submit\" value=\"".GAL_SMAZ_VYBRANE."\" class=\"tl\">";
    endif; 
    $GLOBALS["ukaz_obrazky"].="</form>";
    Formular("galerie_ukaz");
   if(NactiKonfigHod("phprs_verze","varchar")=="265"): // pokud mame verzi 265 pouzijeme funkci na opakujici se IP
    if (TestNaOpakujiciIP('gal'.$cislogalerie,$GLOBALS['rsconfig']['cla_delka_omezeni'],$GLOBALS['rsconfig']['cla_max_pocet_opak'])==0):
     $mysql=mysql_query("update ".$GLOBALS["rspredpona"]."gallery set gallery_zobrazeni=(gallery_zobrazeni+1) where gallery_id='".$cislogalerie."' ",$GLOBALS["dbspojeni"]); 
    endif;
   else: // nemame 265 a tak pouze bezhlave pricteme jedno zobrazeni
     $mysql=mysql_query("update ".$GLOBALS["rspredpona"]."gallery set gallery_zobrazeni=(gallery_zobrazeni+1) where gallery_id='".$cislogalerie."' ",$GLOBALS["dbspojeni"]); 
   endif; 
    Strankovani("galerie_ukaz",Galerie("pocetobr",$cislogalerie),$GLOBALS["gal_ukaz_pocet"],$GLOBALS["strana"]);
  else:
   $GLOBALS["ukaz_obrazky"]=GAL_UKAZ_ZADNY_OBR;
   Formular("galerie_ukaz");
  endif;
}

/* Funkce na zjistovani vseho o galerii */
function Galerie($co,$id) {
 $mysql=mysql_query("select * from ".$GLOBALS["rspredpona"]."gallery where gallery_id='".$id."' and gallery_delete='0'",$GLOBALS["dbspojeni"]); 
 $galerie=mysql_fetch_array($mysql);
 switch($galerie["gallery_admin"]):
  case "1":
   $mysql=mysql_query("select user as prezdivka,jmeno from ".$GLOBALS["rspredpona"]."user where idu='".$galerie["gallery_user_id"]."' ",$GLOBALS["dbspojeni"]); 
   $vlastnik=mysql_fetch_array($mysql);
  break;
  case "0":
   $mysql=mysql_query("select prezdivka,jmeno from ".$GLOBALS["rspredpona"]."ctenari where idc='".$galerie["gallery_user_id"]."'",$GLOBALS["dbspojeni"]); 
   $vlastnik=mysql_fetch_array($mysql);
  break;  
 endswitch;
 $obrazek=mysql_query("select sum(media_size) as velikost, count(media_id) as pocet from ".$GLOBALS["rspredpona"]."media where media_gallery_id='".$id."' and media_smazano!='1'",$GLOBALS["dbspojeni"]);
 $obrazek=mysql_fetch_array($obrazek);
 
 switch($co):
  case "titulek": return $galerie["gallery_title"]; break;
  case "popis": return $galerie["gallery_description"]; break;
  case "zalozeni": return MyDateTimeToDate($galerie["gallery_zalozeni"]); break;
  case "uprava": return MyDateTimeToDate($galerie["gallery_uprava"]); break;
  case "smazana": return $galerie["gallery_smazana"]; break;
  case "verejna": return $galerie["gallery_verejna"]; break;
  case "pocetzobr": return $galerie["gallery_zobrazeni"]; break;
  case "obrazekzobr": return $galerie["gallery_zobrazit"]; break;  
  case "vlastnik_id": return $galerie["gallery_user_id"]; break; //cislo
  case "vlastnik_jm": return $vlastnik["jmeno"]; break; //jmeno
  case "vlastnik_pr": return $vlastnik["prezdivka"]; break; // prezdivka
  case "vlastnik_ad": return $galerie["gallery_admin"]; break; // je admin?
  case "velikost": return $obrazek["velikost"]; break;
  case "pocetobr": return $obrazek["pocet"]; break;
 endswitch; 
}
?>