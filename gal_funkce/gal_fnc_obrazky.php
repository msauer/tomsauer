<?php
######################################################################
# phpRS Gallery 0.99.500
######################################################################
// phpRS Copyright (c) 2001-2004 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// Gallery Copyright (c) 2004 by Michal Safus (michalsafus@gmail.com)
// http://www.phprs.cz/magazin/gallery
// This program is free software. - Toto je bezplatny a svobodny software.

/* Funkce na nahrání obrázku na server */
function ObrazekNahraj($odkud,$kam,$kam2,$jak) {
 if(isset($odkud) and isset($kam) and isset($kam2)):
  //if(is_file($odkud)): // nefunguje na ceskyhosting.cz
   switch($jak):
    case "move": // pokud chceme pouzit funkci move_uploaded_file
     move_uploaded_file($odkud,$kam); // provedeme
     copy($kam,$kam2); // provedeme zkopirovani obrazku kvuli nahledu
     $over=file_exists($kam);
     $over2=file_exists($kam2);
    break;
    case "copy": // pokud chceme pouzit funkci copy
     copy($odkud,$kam); // provedeme
     copy($kam,$kam2); // provedeme zkopirovani obrazku kvuli nahledu
     $over=file_exists($kam);
     $over2=file_exists($kam2);
    break;
    default: return 0;
   endswitch; 
    if($over and $over2):
     return 1; // vse se povedlo
    else:
     return 0; // nepovedlo se uploadnout obrazek
    endif;   
  //else: // to co chceme nahrat neni soubor
   //return 0;
  //endif; 
 else: // neni zadana cesta k obrazku (nebo kam se ma obrazek nahrat)
  return 0;
 endif; 


}

/* Funkce na generovani nahledu obrazku */
function ObrazekGenerujNahled($cesta) {
 $nahled=getimagesize($cesta);
 $sirka=$nahled[0];
 $vyska=$nahled[1];
   /* Pomer mezi sirkou a vyskou obrazku (vyuziva se pro tvorbu nahledu - pro zachovani pomeru stran) */
    if($sirka>$vyska):                // kdyz sirka vetsi nez vyska
     $pomer = $sirka/$vyska;     // pomer sirka/vyska
     $sirka_nova=NactiKonfigHod("nahled_sirka","varchar");
     $vyska_nova=NactiKonfigHod("nahled_vyska","varchar")/$pomer;
    endif;
    if($sirka<$vyska):                  // kdyz vyska vetsi nez sirka
     $pomer = $vyska/$sirka;       // pomer vyska/sirka
     $sirka_nova=NactiKonfigHod("nahled_sirka","varchar")/$pomer;
     $vyska_nova=NactiKonfigHod("nahled_vyska","varchar");
    endif;
    if($sirka==$vyska):                   // kdyz vyska je stejna jako sirka
     $sirka_nova=NactiKonfigHod("nahled_sirka","varchar");
     $vyska_nova=NactiKonfigHod("nahled_vyska","varchar");
    endif;
    if(ObrazekOverPriponu($cesta) and ObrazekOverTyp($cesta)):
     $jak=NactiKonfigHod("gd_verze","varchar"); // nacteni konfigurace z databaze
     $nahled_jak=ZjistiGD($jak); // prohnani pres funkci na zjisteni gd - kvuli auto zjisteni
     if ($nahled_jak=="gd1" AND (ObrazekZjistiPriponu($cesta)=="jpeg" or ObrazekZjistiPriponu($cesta)=="jpg")):
       $src_img = imagecreatefromjpeg($cesta);
       $dst_img = imagecreate($sirka_nova, $vyska_nova);
       imagecopyresized ($dst_img, $src_img, 0, 0, 0, 0, $sirka_nova, $vyska_nova, $sirka, $vyska);
       imagejpeg($dst_img, $cesta, 90);
      elseif ($nahled_jak=="gd2" AND (ObrazekZjistiPriponu($cesta)=="jpeg" or ObrazekZjistiPriponu($cesta)=="jpg")):
       $src_img = imagecreatefromjpeg($cesta);
       $dst_img = imagecreatetruecolor($sirka_nova, $vyska_nova);
       imagecopyresampled ($dst_img, $src_img, 0, 0, 0, 0, $sirka_nova, $vyska_nova, $sirka, $vyska); 
       imagejpeg($dst_img, $cesta, 90);
      elseif ($nahled_jak=="gd1" AND ObrazekZjistiPriponu($cesta)=="gif"):
       $src_img = imagecreatefromgif($cesta);
       $dst_img = imagecreate($sirka_nova, $vyska_nova);
       imagecopyresized ($dst_img, $src_img, 0, 0, 0, 0, $sirka_nova, $vyska_nova, $sirka, $vyska);
       imagejpeg($dst_img, $cesta, 90);
      elseif ($nahled_jak=="gd2" AND ObrazekZjistiPriponu($cesta)=="gif"):
       $src_img = imagecreatefromgif($cesta);
       $dst_img = imagecreatetruecolor($sirka_nova, $vyska_nova);
       imagecopyresampled ($dst_img, $src_img, 0, 0, 0, 0, $sirka_nova, $vyska_nova, $sirka, $vyska);
       imagejpeg($dst_img, $cesta, 90);
      elseif ($nahled_jak=="gd1" AND ObrazekZjistiPriponu($cesta)=="png"):
       $src_img = imagecreatefrompng ($cesta);
       $dst_img = imagecreate($sirka_nova, $vyska_nova);
       imagecopyresized ($dst_img, $src_img, 0, 0, 0, 0, $sirka_nova, $vyska_nova, $sirka, $vyska);
       imagepng($dst_img, $cesta, 90);
      elseif ($nahled_jak=="gd2" AND ObrazekZjistiPriponu($cesta)=="png"):
       $src_img = imagecreatefrompng ($cesta);
       $dst_img = imagecreatetruecolor($sirka_nova, $vyska_nova);
       imagecopyresampled ($dst_img, $src_img, 0, 0, 0, 0, $sirka_nova, $vyska_nova, $sirka, $vyska);
       imagepng($dst_img, $cesta, 90);    
     endif;
     return 1;
    else:
     return 0;
    endif;
}

function ObrazekZjistiTyp($nazev) {
 $obrazek=getimagesize($nazev);
 $typ=$obrazek[2];
 switch($typ):
  case "1": $prip="gif"; break;
  case "2": $prip="jpeg"; break;
  case "3": $prip="png"; break;
  case "4": $prip="swf"; break;
  case "5": $prip="psd"; break;
  case "6": $prip="bmp"; break;
  case "7": $prip="tiff"; break;
  case "8": $prip="tiff"; break;
  case "9": $prip="jpc"; break;
  case "10": $prip="jp2"; break;
  case "11": $prip="jpx"; break;
  case "12": $prip="jb2"; break;
  case "13": $prip="swc"; break;
  case "14": $prip="iff"; break;
  default: $prip="nic"; break;
 endswitch; 
 return $prip;
}

function ObrazekZjistiPriponu($nazev) {
 $casti=explode(".",$nazev);
 $pocet=count($casti);
 $pripona=$casti[$pocet-1];
 return $pripona;
}

function ObrazekOverTyp($nazev) {
 if(ObrazekZjistiTyp($nazev)=="jpeg" or ObrazekZjistiTyp($nazev)=="png" or ObrazekZjistiTyp($nazev)=="gif"):
  return 1;
 else:
  return 0;
 endif;  
}

function ObrazekOverPriponu($nazev) {
 /* Nastavime pripony souboru, ktere se daji uploadovat */
 $povolene_pripony=array("jpg","jpeg","png","gif");
 $pripona=ObrazekZjistiPriponu($nazev);
 if(in_array($pripona,$povolene_pripony)):
  return 1;
 else:
  return 0;
 endif;
}

function ObrazekVelikostOver($velikost) {
 if($velikost/1024<=NactiKonfigHod("velikost_obrazek","varchar")):
  return 1;
 else:
  return 0;
 endif;
}

function ObrazekVytvorNazev($jmeno,$co) {
 $file_time=time(); // vygeneruje nam aktualni cas, ktery se pak prida na zacatek souboru
 $file_postfix=explode(".",$jmeno); // zjistime nazev a priponu souboru (rozdelenim nazvu na casti okolo "tecky")
 $file_head=explode(" ", trim($file_postfix[0])); // pokud nazev souboru obsahuje mezery, vybereme pouze prvni cast do mezery
 $file_prefix=strtolower($file_time."_".$file_head[0].".".ObrazekZjistiPriponu($jmeno));    // sestavime cely nazev obrazku

 switch($co):
  case "obrazek":
   return NactiKonfigHod("gallery_dir","varchar").$file_prefix;
  break;
  case "nahled":
   return NactiKonfigHod("gallery_dir","varchar")."t".$file_prefix;
  break;
 endswitch; 
}

/* Funkce na hlavni kontrolu obrazku */
function ObrazekPridejZkontroluj($co) {
 $chyba=0;
 if(Uzivatel("je_admin") or Uzivatel("je_redaktor") or Uzivatel("je_autor") or Uzivatel("je_ctenar")):
  $chyba=0; else: $chyba=1; endif;
 if(is_dir(NactiKonfigHod("gallery_dir","varchar"))):
  $chyba=0; else: $chyba=2; endif;
 
 if(!isset($GLOBALS["galerie_id"])): $GLOBALS["galerie_id"]=""; endif; // nastavime default honodtu promenne
 if(!isset($GLOBALS["obrazek_cislovani"]) or $GLOBALS["obrazek_cislovani"]<=0): $GLOBALS["obrazek_cislovani"]="1"; endif; // nastavime default honodtu promenne
 $GLOBALS["VypisGalerie"]=VypisGalerie($GLOBALS["galerie_id"]); // do promenne vlozime vypis galerii
 $GLOBALS["VypisKategorie"]=VypisKategorie();  // do promenne vlozime vypis kategorii
 
 if($chyba==0): // pokud nedoslo k zadne chybe
  switch($co):
   /*  PRIDANI JEDNOHO OBRAZKU  */
   case "jeden":
    $formular="obrazek_novy";
    if($_POST["do_it"]): // pokud jiz chceme pridat obrazek
     if($_POST["galerie_id"]!="" and $_FILES["obrazek_url"]["name"]!="" and $_POST["obrazek_titulek"]!="" and $_POST["obrazek_popis"]!=""): // zkontrolujeme vyplneni udaju
       $media_file=ObrazekVytvorNazev($_FILES["obrazek_url"]["name"],"obrazek");
       $thumbnail_file=ObrazekVytvorNazev($_FILES["obrazek_url"]["name"],"nahled");
      if(ObrazekOverPriponu($media_file)): // overime priponu
        if(ObrazekVelikostOver($_FILES["obrazek_url"]["size"])): // overime velikost obrazku
         $nahraj=ObrazekNahraj($_FILES["obrazek_url"]["tmp_name"],$media_file,$thumbnail_file,"move"); // provedeme nahrani obrazku
         if($nahraj): // obrazek se uspesne nahral, pridame ho do databaze
          $GLOBALS["obrazek_titulek"]=$_POST["obrazek_titulek"];
          $GLOBALS["galerie_id"]=$_POST["galerie_id"];
          $GLOBALS["obrazek_popis"]=$_POST["obrazek_popis"];
          $GLOBALS["kategorie_id"]=$_POST["kategorie_id"][0];
          $obr=getimagesize($media_file);
           if(ObrazekGenerujNahled($thumbnail_file)): // povedlo se vygenerovat nahled
            $nahl=getimagesize($thumbnail_file);
            $velikost=ceil($_FILES["obrazek_url"]["size"]/1024);
            $pridej=mysql_query("insert into ".$GLOBALS["rspredpona"]."media values('','".$GLOBALS["galerie_id"]."','".$media_file."','".$thumbnail_file."','".$nahl[0]."','".$nahl[1]."','".$GLOBALS["obrazek_titulek"]."','".$GLOBALS["kategorie_id"]."','".$GLOBALS["obrazek_popis"]."','".$velikost."','".$obr[0]."','".$obr[1]."','','','','')",$GLOBALS["dbspojeni"]); 
             if($pridej):
              $obrazek=mysql_query("select media_id,media_gallery_id from ".$GLOBALS["rspredpona"]."media where media_thumbnail='".$thumbnail_file."' and media_smazano!='1'",$GLOBALS["dbspojeni"]);
              $obrazek=mysql_fetch_array($obrazek);
              $GLOBALS["obrazek_titulek"]=Obrazek("obrazek_titulek",$obrazek["media_id"]);
              $GLOBALS["obrazek_popis"]=Obrazek("obrazek_popis",$obrazek["media_id"]);
              $GLOBALS["obrazek_rozmery"]=Obrazek("obrazek_sirka",$obrazek["media_id"])."*".Obrazek("obrazek_vyska",$obrazek["media_id"])." px";
              $GLOBALS["obrazek_velikost"]=Obrazek("obrazek_velikost",$obrazek["media_id"])." kB";
              $GLOBALS["galerie_nazev"]=Galerie("titulek",$obrazek["media_gallery_id"]);
              $GLOBALS["galerie_velikost"]=(round(Galerie("velikost",Obrazek("galerie_id",$obrazek["media_id"]))/1024,2))." MB";
              $GLOBALS["galerie_pocet_obrazku"]=Galerie("pocetobr",Obrazek("galerie_id",$obrazek["media_id"]));;
              $GLOBALS["nahled_rozmery"]=Obrazek("nahled_sirka",$obrazek["media_id"])."*".Obrazek("nahled_vyska",$obrazek["media_id"])." px";
              $GLOBALS["nahled_url"]=Obrazek("nahled_cesta",$obrazek["media_id"]);
              $GLOBALS["nahled_width"]=Obrazek("nahled_sirka",$obrazek["media_id"]);
              $GLOBALS["nahled_height"]=Obrazek("nahled_vyska",$obrazek["media_id"]);
              $GLOBALS["obrazek_id"]=$obrazek["media_id"]; 
              $GLOBALS["chyba"]=GAL_OK_NOVY_PRIDANI;
              Formular("obrazek_novy_ok"); 
             else: // nepovedlo se pridat obrazek
              @unlink($media_file); // smazeme nepovedeny soubor z webu
              @unlink($thumbnail_file); // smazeme nepovedeny soubor z webu
              $chyba=8;
             endif;
           else: // nepovedlo se vygenerovat nahled
            @unlink($media_file); // smazeme nepovedeny soubor z webu
            @unlink($thumbnail_file); // smazeme nepovedeny soubor z webu
            $chyba=7;
           endif;
         else: // obrazek se z nejakeho duvodu nepodarilo nahrat na web
          $chyba=6;    
         endif;
        else: // obrazek je moc velky
         $chyba=4;     
        endif;      
      else: // spatna pripona
       $chyba=5;  
      endif;
     else: // uzivatel nevyplnil vsechny udaje
      $chyba=3;
     endif;    
    else: // zatim chceme pouze zobrazit formular pro pridani jednoho obrazku
     Formular($formular);
    endif;
   break;
 
   /*  PRIDANI VICE OBRAZKU AUTOMATICKY  */
   case "auto":
    $formular="obrazek_novy_auto";
    if(!isset($GLOBALS["number"]) or $GLOBALS["number"]<=0 or $GLOBALS["number"]>NactiKonfigHod("hromadne_pridani","varchar")): $GLOBALS["number"]=5; endif;
    for($pom=0;$pom<$GLOBALS["number"];$pom++): // henerovani vkladaciho pole
     $GLOBALS["pridej_obrazky"].="<span class=\"gal_tucne\">".GAL_OBRAZKY_OBRAZEK." ".($pom+1)."</span><br /><div class=\"gal_formular\"><input size=\"40\" type=\"file\" name=\"obrazek_url[]\" class=\"textpole\"></div><br />";
    endfor;
    
    if($_POST["do_it"]): // chceme pridat obrazky
     if($_POST["galerie_id"]!="" and $_POST["obrazek_titulek"]!="" and $_POST["obrazek_popis"]!=""): // zkontrolujeme vyplneni udaju
      $pocet_obr=count($_FILES["obrazek_url"]["name"]); // pocet obrazku ktere chce uzivatel pridat
      for($pom=0;$pom<$pocet_obr;$pom++):
       if($_FILES["obrazek_url"]["name"][$pom]!=""): // pokud se jmeno necemu rovna
       $media_file=ObrazekVytvorNazev($_FILES["obrazek_url"]["name"][$pom],"obrazek");
       $thumbnail_file=ObrazekVytvorNazev($_FILES["obrazek_url"]["name"][$pom],"nahled");
        if(ObrazekOverPriponu($media_file)): // overime priponu
         if(ObrazekVelikostOver($_FILES["obrazek_url"]["size"][$pom])):
         $nahraj=ObrazekNahraj($_FILES["obrazek_url"]["tmp_name"][$pom],$media_file,$thumbnail_file,"move"); // provedeme nahrani obrazku
         if($nahraj): // obrazek se uspesne nahral, pridame ho do databaze
          $GLOBALS["obrazek_titulek"]=$_POST["obrazek_titulek"]." - ".($GLOBALS["obrazek_cislovani"]);
          $GLOBALS["galerie_id"]=$_POST["galerie_id"];
          $GLOBALS["obrazek_popis"]=$_POST["obrazek_popis"];
          $GLOBALS["kategorie_id"]=$_POST["kategorie_id"][0];
          $obr=getimagesize($media_file);
           if(ObrazekGenerujNahled($thumbnail_file)): // povedlo se vygenerovat nahled
            $nahl=getimagesize($thumbnail_file);
            $velikost=ceil($_FILES["obrazek_url"]["size"][$pom]/1024);
            $pridej=mysql_query("insert into ".$GLOBALS["rspredpona"]."media values('','".$GLOBALS["galerie_id"]."','".$media_file."','".$thumbnail_file."','".$nahl[0]."','".$nahl[1]."','".$GLOBALS["obrazek_titulek"]."','".$GLOBALS["kategorie_id"]."','".$GLOBALS["obrazek_popis"]."','".$velikost."','".$obr[0]."','".$obr[1]."','','','','')",$GLOBALS["dbspojeni"]); 
             if($pridej):
              $obrazek=mysql_query("select media_id,media_gallery_id from ".$GLOBALS["rspredpona"]."media where media_thumbnail='".$thumbnail_file."' and media_caption='".$GLOBALS["obrazek_titulek"]."' and media_smazano!='1'",$GLOBALS["dbspojeni"]);
              $obrazek=mysql_fetch_array($obrazek);
              $GLOBALS["obrazek_titulek"]=Obrazek("obrazek_titulek",$obrazek["media_id"]);
              $GLOBALS["obrazek_popis"]=Obrazek("obrazek_popis",$obrazek["media_id"]);
              $GLOBALS["obrazek_rozmery"]=Obrazek("obrazek_sirka",$obrazek["media_id"])."*".Obrazek("obrazek_vyska",$obrazek["media_id"])." px";
              $GLOBALS["obrazek_velikost"]=Obrazek("obrazek_velikost",$obrazek["media_id"])." kB";
              $GLOBALS["galerie_nazev"]=Galerie("titulek",$obrazek["media_gallery_id"]);
              $GLOBALS["galerie_velikost"]=(round(Galerie("velikost",Obrazek("galerie_id",$obrazek["media_id"]))/1024,2))." MB";
              $GLOBALS["galerie_pocet_obrazku"]=Galerie("pocetobr",Obrazek("galerie_id",$obrazek["media_id"]));;
              $GLOBALS["nahled_rozmery"]=Obrazek("nahled_sirka",$obrazek["media_id"])."*".Obrazek("nahled_vyska",$obrazek["media_id"])." px";
              $GLOBALS["nahled_url"]=Obrazek("nahled_cesta",$obrazek["media_id"]);
              $GLOBALS["nahled_width"]=Obrazek("nahled_sirka",$obrazek["media_id"]);
              $GLOBALS["nahled_height"]=Obrazek("nahled_vyska",$obrazek["media_id"]);
              $GLOBALS["obrazek_id"]=$obrazek["media_id"]; 
              $GLOBALS["chyba"]=GAL_OK_NOVY_PRIDANI;
              $GLOBALS["obrazek_cislovani"]++;
              Formular("obrazek_novy_ok"); 
             else: // nepovedlo se pridat obrazek
              $chyba=10;
             endif;
           else: // nepovedlo se vygenerovat nahled
            @unlink($media_file); // smazeme nepovedeny soubor z webu
            @unlink($thumbnail_file); // smazeme nepovedeny soubor z webu
            $chyba=10;
           endif;
         else: // obrazek se z nejakeho duvodu nepodarilo nahrat na web
          $chyba=10;    
         endif;
         else:
          $chyba=10;
         endif;
        else:
         $chyba=10;          
        endif; 
       endif;
      endfor;      
     else:
      $chyba=10;
     endif;   
    else: // pouze zobrazeni formulare
     Formular($formular);
    endif;
   break;

   /*  PRIDANI VICE OBRAZKU PODLE UZIVATELE  */
   case "manual":
    $formular="obrazek_novy_manual";
    if(!isset($GLOBALS["number"]) or $GLOBALS["number"]<=0 or $GLOBALS["number"]>NactiKonfigHod("hromadne_pridani","varchar")): $GLOBALS["number"]=5; endif;
    for($pom=0;$pom<$GLOBALS["number"];$pom++): // henerovani vkladaciho pole
     $GLOBALS["pridej_obrazky"].="
     <span class=\"gal_tucne\">".GAL_OBRAZKY_OBRAZEK." ".($pom+1)."</span><br />
       <span class=\"gal_tucne\">Titulek obrázku:</span><br />
  <div class=\"gal_formular\">  
    <input class=\"textpole\" type=\"text\" size=\"40\" name=\"obrazek_titulek[]\" value=\"".$GLOBALS["obrazek_titulek"]."\">
  </div><br /> 
    <span class=\"gal_tucne\">Popis obrázků:</span><br />
  <div class=\"gal_formular\">  
   <textarea class=\"textbox\" rows=\"2\" cols=\"40\" name=\"obrazek_popis[]\">".$GLOBALS["obrazek_popis"]."</textarea>
  </div><br />
  
  <span class=\"gal_tucne\">Kategorie:</span><br />
  <div class=\"gal_formular\">  
    ".$GLOBALS["VypisKategorie"]."
  </div><br/>

     <div class=\"gal_formular\"><input size=\"40\" type=\"file\" name=\"obrazek_url[]\" class=\"textpole\"></div><hr class=\"gal_cara\" />";
    endfor;
    
    if($_POST["do_it"]): // chceme pridat obrazky
     if($_POST["galerie_id"]!=""): // zkontrolujeme vyplneni udaju
      $pocet_obr=count($_FILES["obrazek_url"]["name"]); // pocet obrazku ktere chce uzivatel pridat
      for($pom=0;$pom<$pocet_obr;$pom++):
       if($_FILES["obrazek_url"]["name"][$pom]!="" and  $_POST["obrazek_titulek"][$pom]!="" and $_POST["obrazek_popis"][$pom]!=""): // pokud se jmeno necemu rovna
       $media_file=ObrazekVytvorNazev($_FILES["obrazek_url"]["name"][$pom],"obrazek");
       $thumbnail_file=ObrazekVytvorNazev($_FILES["obrazek_url"]["name"][$pom],"nahled");
        if(ObrazekOverPriponu($media_file)): // overime priponu
         if(ObrazekVelikostOver($_FILES["obrazek_url"]["size"][$pom])):
         $nahraj=ObrazekNahraj($_FILES["obrazek_url"]["tmp_name"][$pom],$media_file,$thumbnail_file,"move"); // provedeme nahrani obrazku
         if($nahraj): // obrazek se uspesne nahral, pridame ho do databaze
          $GLOBALS["obrazek_titulek"]=$_POST["obrazek_titulek"][$pom];
          $GLOBALS["galerie_id"]=$_POST["galerie_id"];
          $GLOBALS["obrazek_popis"]=$_POST["obrazek_popis"][$pom];
          $GLOBALS["kategorie_id"]=$_POST["kategorie_id"][$pom];
          $obr=getimagesize($media_file);
           if(ObrazekGenerujNahled($thumbnail_file)): // povedlo se vygenerovat nahled
            $nahl=getimagesize($thumbnail_file);
            $velikost=ceil($_FILES["obrazek_url"]["size"][$pom]/1024);
            $pridej=mysql_query("insert into ".$GLOBALS["rspredpona"]."media values('','".$GLOBALS["galerie_id"]."','".$media_file."','".$thumbnail_file."','".$nahl[0]."','".$nahl[1]."','".$GLOBALS["obrazek_titulek"]."','".$GLOBALS["kategorie_id"]."','".$GLOBALS["obrazek_popis"]."','".$velikost."','".$obr[0]."','".$obr[1]."','','','','')",$GLOBALS["dbspojeni"]); 
             if($pridej):
              $obrazek=mysql_query("select media_id,media_gallery_id from ".$GLOBALS["rspredpona"]."media where media_thumbnail='".$thumbnail_file."' and media_caption='".$GLOBALS["obrazek_titulek"]."' and media_smazano!='1'",$GLOBALS["dbspojeni"]);
              $obrazek=mysql_fetch_array($obrazek);
              $GLOBALS["obrazek_titulek"]=Obrazek("obrazek_titulek",$obrazek["media_id"]);
              $GLOBALS["obrazek_popis"]=Obrazek("obrazek_popis",$obrazek["media_id"]);
              $GLOBALS["obrazek_rozmery"]=Obrazek("obrazek_sirka",$obrazek["media_id"])."*".Obrazek("obrazek_vyska",$obrazek["media_id"])." px";
              $GLOBALS["obrazek_velikost"]=Obrazek("obrazek_velikost",$obrazek["media_id"])." kB";
              $GLOBALS["galerie_nazev"]=Galerie("titulek",$obrazek["media_gallery_id"]);
              $GLOBALS["galerie_velikost"]=(round(Galerie("velikost",Obrazek("galerie_id",$obrazek["media_id"]))/1024,2))." MB";
              $GLOBALS["galerie_pocet_obrazku"]=Galerie("pocetobr",Obrazek("galerie_id",$obrazek["media_id"]));;
              $GLOBALS["nahled_rozmery"]=Obrazek("nahled_sirka",$obrazek["media_id"])."*".Obrazek("nahled_vyska",$obrazek["media_id"])." px";
              $GLOBALS["nahled_url"]=Obrazek("nahled_cesta",$obrazek["media_id"]);
              $GLOBALS["nahled_width"]=Obrazek("nahled_sirka",$obrazek["media_id"]);
              $GLOBALS["nahled_height"]=Obrazek("nahled_vyska",$obrazek["media_id"]);
              $GLOBALS["obrazek_id"]=$obrazek["media_id"]; 
              $GLOBALS["chyba"]=GAL_OK_NOVY_PRIDANI;
              $GLOBALS["obrazek_cislovani"]++;
              Formular("obrazek_novy_ok"); 
             else: // nepovedlo se pridat obrazek
              $chyba=10;
             endif;
           else: // nepovedlo se vygenerovat nahled
            @unlink($media_file); // smazeme nepovedeny soubor z webu
            @unlink($thumbnail_file); // smazeme nepovedeny soubor z webu
            $chyba=10;
           endif;
         else: // obrazek se z nejakeho duvodu nepodarilo nahrat na web
          $chyba=10;    
         endif;
         else:
          $chyba=10;
         endif;
        else:
         $chyba=10;          
        endif; 
       endif;
      endfor;      
     else:
      $chyba=10;
     endif;   
    else: // pouze zobrazeni formulare
     Formular($formular);
    endif;
   break;   
   
   /* PRIDANI VICE OBRAZKU PRES FTP */
   case "ftp":
    $formular="obrazek_novy_ftp";
    if((Uzivatel("je_admin") and NactiKonfigHod("ftp_admin","varchar")) or (Uzivatel("je_redaktor") and NactiKonfigHod("ftp_redaktor","varchar")) or (Uzivatel("je_autor") and NactiKonfigHod("ftp_autor","varchar")) or (Uzivatel("je_ctenar") and NactiKonfigHod("ftp_ctenar","varchar"))):
     if($_POST["do_it"]==1):
       $cesta=$GLOBALS["obrazek_adresar"]."/";
      if(is_dir($cesta)):
       $adresar=opendir($cesta);
       $pom=1;
        while($file=readdir($adresar)):
         if($file!="." AND $file!=".."):
          $cela_cesta=$cesta.$file;
          $size=filesize($cela_cesta);
           if(ObrazekOverPriponu($file)):
            $GLOBALS["galerie"]=$_POST["galerie_id"];                      // do jake galerie pridat
            $GLOBALS["titulek"]=$_POST["obrazek_titulek"]." ".$pom;                  // titulek obrazku
            $GLOBALS["popis"]=$_POST["obrazek_popis"]." ".$pom;                       // popis obrazku
            $GLOBALS["popis"]= WordWrap($GLOBALS["popis"], 20, "\n", 1);      // popis obrazku
            $GLOBALS["kategorie"]=$_POST["kategorie_id"][0];                      // do jake kategorie pridat
            $GLOBALS["obrazek_jmeno"]=$file;           // originalni nazev
            $GLOBALS["obrazek_velikost"]=$size;         // velikost obrazky v bytech
            $GLOBALS["obrazek_docasne"]=$cela_cesta;       // docasne ulozeni obrazku
            $media_file=ObrazekVytvorNazev($file,"obrazek");
            $thumbnail_file=ObrazekVytvorNazev($file,"nahled");
             if(ObrazekVelikostOver($size)):
              if(ObrazekNahraj($cela_cesta,$media_file,$thumbnail_file,"copy")):
               if(ObrazekGenerujNahled($thumbnail_file)):
                $GLOBALS["obrazek_titulek"]=$_POST["obrazek_titulek"]." - ".$pom;
                $GLOBALS["galerie_id"]=$_POST["galerie_id"];
                $GLOBALS["obrazek_popis"]=$_POST["obrazek_popis"];
                $GLOBALS["kategorie_id"]=$_POST["kategorie_id"][0];
                $obr=getimagesize($media_file);
                $nahl=getimagesize($thumbnail_file);
                $velikost=ceil($size/1024);
                $pridej=mysql_query("insert into ".$GLOBALS["rspredpona"]."media values('','".$GLOBALS["galerie_id"]."','".$media_file."','".$thumbnail_file."','".$nahl[0]."','".$nahl[1]."','".$GLOBALS["obrazek_titulek"]."','".$GLOBALS["kategorie_id"]."','".$GLOBALS["obrazek_popis"]."','".$velikost."','".$obr[0]."','".$obr[1]."','','','','')",$GLOBALS["dbspojeni"]); 
                 if($pridej):
                  $obrazek=mysql_query("select media_id,media_gallery_id from ".$GLOBALS["rspredpona"]."media where media_thumbnail='".$thumbnail_file."' and media_caption='".$GLOBALS["obrazek_titulek"]."' and media_smazano!='1'",$GLOBALS["dbspojeni"]);
                  $obrazek=mysql_fetch_array($obrazek);
                  $GLOBALS["obrazek_titulek"]=Obrazek("obrazek_titulek",$obrazek["media_id"]);
                  $GLOBALS["obrazek_popis"]=Obrazek("obrazek_popis",$obrazek["media_id"]);
                  $GLOBALS["obrazek_rozmery"]=Obrazek("obrazek_sirka",$obrazek["media_id"])."*".Obrazek("obrazek_vyska",$obrazek["media_id"])." px";
                  $GLOBALS["obrazek_velikost"]=Obrazek("obrazek_velikost",$obrazek["media_id"])." kB";
                  $GLOBALS["galerie_nazev"]=Galerie("titulek",$obrazek["media_gallery_id"]);
                  $GLOBALS["galerie_velikost"]=(round(Galerie("velikost",Obrazek("galerie_id",$obrazek["media_id"]))/1024,2))." MB";
                  $GLOBALS["galerie_pocet_obrazku"]=Galerie("pocetobr",Obrazek("galerie_id",$obrazek["media_id"]));;
                  $GLOBALS["nahled_rozmery"]=Obrazek("nahled_sirka",$obrazek["media_id"])."*".Obrazek("nahled_vyska",$obrazek["media_id"])." px";
                  $GLOBALS["nahled_url"]=Obrazek("nahled_cesta",$obrazek["media_id"]);
                  $GLOBALS["nahled_width"]=Obrazek("nahled_sirka",$obrazek["media_id"]);
                  $GLOBALS["nahled_height"]=Obrazek("nahled_vyska",$obrazek["media_id"]);
                  $GLOBALS["obrazek_id"]=$obrazek["media_id"]; 
                  $GLOBALS["chyba"]=GAL_OK_NOVY_PRIDANI;
                  $GLOBALS["obrazek_cislovani"]++;
                  if($GLOBALS["obrazek_smazat"]): // smazeme obrazek z adresare
                  @unlink($cela_cesta);
                  endif;
                  Formular("obrazek_novy_ok"); 
                 else:
                  @unlink($media_file); // smazeme nepovedeny soubor z webu
                  @unlink($thumbnail_file); // smazeme nepovedeny soubor z webu
                  $chyba=10;
                 endif;
               else:
                @unlink($media_file); // smazeme nepovedeny soubor z webu
                @unlink($thumbnail_file); // smazeme nepovedeny soubor z webu
                $chyba=10;
               endif;               
              else:
               $chyba=10;
              endif;
             else:
              $chyba=10;
             endif;
           else:
            $chyba=10;
           endif;  
         endif;  
         $pom++;
        endwhile;
       else:
        $chyba=10;
       endif; 
     else:
      Formular("obrazek_novy_ftp");
     endif;
    else:
     $chyba=9;
    endif;
   break;  
   
   case "rozcest": Formular("obrazek_novy_rozcest"); break;
   default: Formular("obrazek_novy_rozcest"); break;
  endswitch;
 endif; 

 switch($chyba):
  case "0": break; // zadna chyba
  case "1": $GLOBALS["chyba"]=GAL_CHYBA_NOVY_PRIHLASENI; Formular("galerie_chyba"); break; // uzivatel neni prihlasen
  case "2": $GLOBALS["chyba"]=GAL_CHYBA_NOVY_ADRESAR; Formular("galerie_chyba"); break; // adresar pro nahrani galerie neexistuje
  case "3": $GLOBALS["chyba"]=GAL_CHYBA_NOVY_UDAJE; Formular($formular); break; // nevyplnene udaje
  case "4": $GLOBALS["chyba"]=GAL_CHYBA_NOVY_VELIKOST; Formular($formular); break; // obrazek je moc velky
  case "5": $GLOBALS["chyba"]=GAL_CHYBA_NOVY_PRIPONA; Formular($formular); break; // obrazek ma spatnou priponu
  case "6": $GLOBALS["chyba"]=GAL_CHYBA_NOVY_KOPIE; Formular($formular); break; // nepodarilo se zkopirovat na web
  case "7": $GLOBALS["chyba"]=GAL_CHYBA_NOVY_NAHLED; Formular($formular); break; // nepodarilo se vygenerovat nahled
  case "8": $GLOBALS["chyba"]=GAL_CHYBA_NOVY_DATABAZE; Formular($formular); break; // nepodarilo se pridat obrazek do databaze
  case "9": $GLOBALS["chyba"]=GAL_CHYBA_NOVY_NE; Formular("galerie_chyba"); break; // uzivatel nemuze udelat tohle
  case "10": $GLOBALS["chyba"]=GAL_CHYBA_NOVY_SPATNE; Formular("galerie_chyba"); break; // neco neprobehlo dobre
  case "11": $GLOBALS["chyba"]=GAL_FTP_ADRESAR_NE; Formular($formular); break; // neco neprobehlo dobre
 endswitch;  


}

/* Funkce na zjistovani vseho o obrazku */
function Obrazek($co,$id) {
 $obrazek=mysql_query("select * from ".$GLOBALS["rspredpona"]."media where media_id='".$id."' and media_smazano!='1'",$GLOBALS["dbspojeni"]); 
 $obrazek=mysql_fetch_array($obrazek);
 $galerie=mysql_query("select gallery_delete from ".$GLOBALS["rspredpona"]."gallery where gallery_id='".$obrazek["media_gallery_id"]."' ",$GLOBALS["dbspojeni"]); 
 $galerie=mysql_fetch_array($galerie);

 $nasledujici=mysql_query("select media_id from ".$GLOBALS["rspredpona"]."media where media_id>'".$id."' and media_smazano!='1' and media_gallery_id='".$obrazek["media_gallery_id"]."' order by media_id asc",$GLOBALS["dbspojeni"]);
 $predchozi=mysql_query("select media_id from ".$GLOBALS["rspredpona"]."media where media_id<'".$id."' and media_smazano!='1' and media_gallery_id='".$obrazek["media_gallery_id"]."' order by media_id desc",$GLOBALS["dbspojeni"]);
 $prvni=mysql_query("select media_id from ".$GLOBALS["rspredpona"]."media where media_id<'".$id."' and media_smazano!='1' and media_gallery_id='".$obrazek["media_gallery_id"]."' order by media_id asc limit 1",$GLOBALS["dbspojeni"]);
 $posledni=mysql_query("select media_id from ".$GLOBALS["rspredpona"]."media where media_id>'".$id."' and media_smazano!='1' and media_gallery_id='".$obrazek["media_gallery_id"]."' order by media_id desc limit 1",$GLOBALS["dbspojeni"]);
switch($co):
 case "obrazek_titulek": return $obrazek["media_caption"]; break;
 case "obrazek_popis": return $obrazek["media_description"]; break;
 case "obrazek_sirka": return $obrazek["media_width"]; break;
 case "obrazek_vyska": return $obrazek["media_height"]; break;
 case "obrazek_cesta": return $obrazek["media_file"]; break;
 case "galerie_id": return $obrazek["media_gallery_id"]; break;
 case "galerie_smazana": return $galerie["gallery_delete"]; break;
 case "nahled_cesta": return $obrazek["media_thumbnail"]; break;
 case "nahled_sirka": return $obrazek["media_thumbnail_width"]; break;
 case "nahled_vyska": return $obrazek["media_thumbnail_height"]; break;
 case "kategorie_id": return $obrazek["media_category"]; break;
 case "obrazek_pocet_zobrazeni": return $obrazek["media_view"]; break;
 case "obrazek_pocet_znamkovani": return $obrazek["media_hodnotilo"]; break;
 case "obrazek_znamka": return $obrazek["media_znamka"]; break;
 case "obrazek_velikost": return $obrazek["media_size"]; break;
 
 case "obrazek_prvni":
    if(mysql_num_rows($prvni)>0):
     $prvni="<a href=\"gallery.php?akce=obrazek_ukaz&amp;media_id=".mysql_result($prvni,0,"media_id")."\" width=\"11\" height=\"11\"><img alt=\"na první\" title=\"na první\" src=\"".NactiKonfigHod("sablony","varchar")."/sipky/obrazek_ukaz_prvni.gif\" width=\"17\" height=\"11\"></a>";
    else:
     $prvni="<img alt=\"na první\" title=\"na první\" src=\"".NactiKonfigHod("sablony","varchar")."/sipky/obrazek_ukaz_prvni_prazdny.gif\" width=\"17\" height=\"11\">";
    endif;
    return $prvni;
 break;
 
 case "obrazek_posledni":
    if(mysql_num_rows($posledni)>0):
     $posledni="<a href=\"gallery.php?akce=obrazek_ukaz&amp;media_id=".mysql_result($posledni,0,"media_id")."\" width=\"11\" height=\"11\"><img alt=\"na poslední\" title=\"na poslední\" src=\"".NactiKonfigHod("sablony","varchar")."/sipky/obrazek_ukaz_posledni.gif\" width=\"17\" height=\"11\"></a>";
    else:
     $posledni="<img alt=\"na poslední\" title=\"na poslední\" src=\"".NactiKonfigHod("sablony","varchar")."/sipky/obrazek_ukaz_posledni_prazdny.gif\" width=\"17\" height=\"11\">";
    endif;
    return $posledni; 
 break;
 
 case "obrazek_nasledujici":
    if(mysql_num_rows($nasledujici)>0):
     $nasledujici="<a href=\"gallery.php?akce=obrazek_ukaz&amp;media_id=".mysql_result($nasledujici,0,"media_id")."\" width=\"11\" height=\"11\"><img alt=\"na následující\" title=\" na následující\" src=\"".NactiKonfigHod("sablony","varchar")."/sipky/obrazek_ukaz_dalsi.gif\" width=\"11\" height=\"11\"></a>";
    else:
     $nasledujici="<img alt=\"na následující\" title=\"na následující\" src=\"".NactiKonfigHod("sablony","varchar")."/sipky/obrazek_ukaz_dalsi_prazdny.gif\">";
    endif;
    return $nasledujici;
 break;
 case "obrazek_predchozi": 
    if(mysql_num_rows($predchozi)>0):
     $predchozi="<a href=\"gallery.php?akce=obrazek_ukaz&amp;media_id=".mysql_result($predchozi,0,"media_id")."\" width=\"11\" height=\"11\"><img alt=\"na předchozí\" title=\"na předchozí\" src=\"".NactiKonfigHod("sablony","varchar")."/sipky/obrazek_ukaz_predchozi.gif\" width=\"11\" height=\"11\"></a>";
    else:
     $predchozi="<img alt=\"na předchozí\" title=\"na předchozí\" src=\"".NactiKonfigHod("sablony","varchar")."/sipky/obrazek_ukaz_predchozi_prazdny.gif\" width=\"11\" height=\"11\">"; 
    endif;
    return $predchozi;
 break;
 case "obrazek_aktpozice":
  $pocet=mysql_num_rows($predchozi);
  return $pocet+1;
 break;
endswitch; 






}

/* Funkce na zobrazovani NEJ obrazku... */
function ObrazekTop() {
 /* Vybereme si nejzobazovanejsi obrazky */
 $nejzobrazovanejsi=mysql_query("select * from ".$GLOBALS["rspredpona"]."media where media_smazano!='1' order by media_view desc limit ".$GLOBALS["obrazek"]["top"]."",$GLOBALS["dbspojeni"]);
 while($obrazek=mysql_fetch_array($nejzobrazovanejsi)):
  $GLOBALS["top_nazev"]=GAL_TOP_NEJZOBRAZOVANEJSI;
  $GLOBALS["top_obrazek_id"][]=$obrazek["media_id"];
  $GLOBALS["top_obrazek_nazev"][]=$obrazek["media_caption"];
  $GLOBALS["top_obrazek_popis"][]=$obrazek["media_description"];
  $GLOBALS["top_obrazek_width"][]=$obrazek["media_thumbnail_width"];
  $GLOBALS["top_obrazek_height"][]=$obrazek["media_thumbnail_height"];
  $GLOBALS["top_obrazek_src"][]=$obrazek["media_thumbnail"];
 endwhile;

 $kolikobrazku=count($GLOBALS["top_obrazek_src"]);
$pocet_sloupcu=3;
if ($kolikobrazku>0):
   $GLOBALS["obrazky_ukaz"].="<table cellspacing=\"8\" cellpadding=\"0\" class=\"gal_ukaz_galerie_obrazek\" align=\"center\">\n";
   for ($pom=0;$pom<$kolikobrazku;$pom++):
     if (($pom % $pocet_sloupcu) == 0):
       $GLOBALS["obrazky_ukaz"].="<tr class=\"z\">";
     else:
       $GLOBALS["obrazky_ukaz"].="<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>"; // mezera mezi sloupci
     endif;
     $GLOBALS["obrazky_ukaz"].="<td width=\"130\" class=\"gal_galerie_ukaz_titulek\" align=\"center\"><a target=\"_blank\" href=\"gallery.php?akce=obrazek_ukaz&amp;media_id=".$GLOBALS["top_obrazek_id"][$pom]."\">".$GLOBALS["top_obrazek_nazev"]["$pom"]."<br /><img src=\"".$GLOBALS["top_obrazek_src"][$pom]."\" width=\"".$GLOBALS["top_obrazek_width"][$pom]."\" height=\"".$GLOBALS["top_obrazek_height"][$pom]."\" alt=\"".$GLOBALS["top_obrazek_popis"][$pom]."\" title=\"".$GLOBALS["top_obrazek_popis"][$pom]."\"></a>";

     $GLOBALS["obrazky_ukaz"].="</td>\n";
     if (($pom % $pocet_sloupcu) == ($pocet_sloupcu-1)):
       $GLOBALS["obrazky_ukaz"].="</tr>\n";
     endif;
   endfor;

   $chybi=$pom % $pocet_sloupcu;
   if ($chybi > 0):
     for ($pom=0; $pom < ($pocet_sloupcu - $chybi); $pom++):
       $GLOBALS["obrazky_ukaz"].="<td></td><td></td>";
     endfor;
     $GLOBALS["obrazky_ukaz"].="</tr>\n";
   endif;
   $GLOBALS["obrazky_ukaz"].="</table>\n";
endif;

  Formular("obrazek_top"); echo "<hr class=\"gal_cara\">";
  $GLOBALS["top_nazev"]="";
  $GLOBALS["top_obrazek_id"]="";
  $GLOBALS["top_obrazek_nazev"]="";
  $GLOBALS["top_obrazek_popis"]="";
  $GLOBALS["top_obrazek_width"]="";
  $GLOBALS["top_obrazek_height"]="";
  $GLOBALS["top_obrazek_src"]="";
  $GLOBALS["obrazky_ukaz"]="";  
  
 /* Vybereme si nejzobazovanejsi obrazky */
 $nejnezobrazovanejsi=mysql_query("select * from ".$GLOBALS["rspredpona"]."media where media_smazano!='1' order by media_view asc limit ".$GLOBALS["obrazek"]["top"]."",$GLOBALS["dbspojeni"]);
 while($obrazek=mysql_fetch_array($nejnezobrazovanejsi)):
  $GLOBALS["top_nazev"]=GAL_TOP_NEJNEZOBRAZOVANEJSI;
  $GLOBALS["top_obrazek_id"][]=$obrazek["media_id"];
  $GLOBALS["top_obrazek_nazev"][]=$obrazek["media_caption"];
  $GLOBALS["top_obrazek_popis"][]=$obrazek["media_description"];
  $GLOBALS["top_obrazek_width"][]=$obrazek["media_thumbnail_width"];
  $GLOBALS["top_obrazek_height"][]=$obrazek["media_thumbnail_height"];
  $GLOBALS["top_obrazek_src"][]=$obrazek["media_thumbnail"];
 endwhile;
 
 $kolikobrazku=count($GLOBALS["top_obrazek_src"]);
$pocet_sloupcu=3;
if ($kolikobrazku>0):
   $GLOBALS["obrazky_ukaz"].="<table cellspacing=\"8\" cellpadding=\"0\" class=\"gal_ukaz_galerie_obrazek\" align=\"center\">\n";
   for ($pom=0;$pom<$kolikobrazku;$pom++):
     if (($pom % $pocet_sloupcu) == 0):
       $GLOBALS["obrazky_ukaz"].="<tr class=\"z\">";
     else:
       $GLOBALS["obrazky_ukaz"].="<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>"; // mezera mezi sloupci
     endif;
     $GLOBALS["obrazky_ukaz"].="<td width=\"130\" class=\"gal_galerie_ukaz_titulek\" align=\"center\"><a target=\"_blank\" href=\"gallery.php?akce=obrazek_ukaz&amp;media_id=".$GLOBALS["top_obrazek_id"][$pom]."\">".$GLOBALS["top_obrazek_nazev"]["$pom"]."<br /><img src=\"".$GLOBALS["top_obrazek_src"][$pom]."\" width=\"".$GLOBALS["top_obrazek_width"][$pom]."\" height=\"".$GLOBALS["top_obrazek_height"][$pom]."\" alt=\"".$GLOBALS["top_obrazek_popis"][$pom]."\" title=\"".$GLOBALS["top_obrazek_popis"][$pom]."\"></a>";

     $GLOBALS["obrazky_ukaz"].="</td>\n";
     if (($pom % $pocet_sloupcu) == ($pocet_sloupcu-1)):
       $GLOBALS["obrazky_ukaz"].="</tr>\n";
     endif;
   endfor;

   $chybi=$pom % $pocet_sloupcu;
   if ($chybi > 0):
     for ($pom=0; $pom < ($pocet_sloupcu - $chybi); $pom++):
       $GLOBALS["obrazky_ukaz"].="<td></td><td></td>";
     endfor;
     $GLOBALS["obrazky_ukaz"].="</tr>\n";
   endif;
   $GLOBALS["obrazky_ukaz"].="</table>\n";
endif;

  Formular("obrazek_top");   
}

function ObrazekUprav($media_id) {
 if($media_id!=""): // pokud je zadano cislo obrazku
  $obrazek=mysql_query("SELECT * FROM ".$GLOBALS["rspredpona"]."media WHERE media_id='$media_id' and media_smazano!='1'",$GLOBALS["dbspojeni"]); //vybereme obrazek z databaze
  $spocti=mysql_numrows($obrazek); // pocet obrazku
  if($spocti!="0"): // pokud se pocet obrazku nerovna nule
   $galid=mysql_result($obrazek,0,"media_gallery_id"); // ID galerie
   $over=mysql_query("SELECT gallery_admin,gallery_user_id FROM ".$GLOBALS["rspredpona"]."gallery WHERE gallery_id='".$galid."' AND gallery_delete!='1'",$GLOBALS["dbspojeni"]);
   $pole=mysql_fetch_array($over);
  if($pole["gallery_admin"]!=""):
   if($pole["gallery_admin"]==1):
    if((Uzivatel("je_admin") and $pole["gallery_user_id"]==Uzivatel("id_admin")) or (Uzivatel("je_redaktor") and $pole["gallery_user_id"]==Uzivatel("id_redaktor")) or (Uzivatel("je_autor") and $pole["gallery_user_id"]==Uzivatel("id_autor"))):
     $ok=1; else: $ok=0; endif;
   elseif($pole["gallery_admin"]==0):
    if((Uzivatel("je_ctenar") and $pole["gallery_user_id"]==Uzivatel("id_ctenar"))):
     $ok=1; else: $ok=0; endif;
   endif;
    endif;
   if($GLOBALS["do_it"]!="1"): // pokud jeste nechceme upravit obrazek
     if($ok):
      $GLOBALS["media_caption"]=mysql_result($obrazek,0,media_caption);                    // titulek obrazku
      $GLOBALS["media_description"]=mysql_result($obrazek,0,media_description);            // popis obrazku
      $GLOBALS["media_id"]=mysql_result($obrazek,0,media_id);                              // ID obrazku
      $GLOBALS["media_thumbnail"]=mysql_result($obrazek,0,media_thumbnail);                // jmeno nahledu
      $GLOBALS["media_thumbnail_height"]=mysql_result($obrazek,0,media_thumbnail_height);  // sirka nahledu
      $GLOBALS["media_thumbnail_width"]=mysql_result($obrazek,0,media_thumbnail_width);    // vyska nahledu
      $GLOBALS["cislo_kategorie"]=mysql_result($obrazek,0,media_category);
      $GLOBALS["kategorie"]=VypisKategorie();
      Formular("obrazek_uprav");
     else:
      $GLOBALS["chyba"].=GAL_OBR_UPRAVA_NE_VLASTNIK; // chybova hlaska
      Formular("galerie_chyba");
     endif;
   else:
     if($ok): // pokud je vse OK
      if($GLOBALS["media_caption"]!="" AND $GLOBALS["media_description"]!=""): // kdyz je obsah a titulek vyplnen
       $aktualizuj_obrazek=mysql_query("UPDATE ".$GLOBALS["rspredpona"]."media SET media_category='".$GLOBALS["kategorie_id"]."',media_caption='".$GLOBALS["media_caption"]."',media_description='".$GLOBALS["media_description"]."' WHERE media_id='$media_id'",$GLOBALS["dbspojeni"]);
        if($aktualizuj_obrazek): // vse probehlo uspesne
         $GLOBALS["chyba"].=GAL_OBR_UPRAVA_OK;
         Formular("galerie_chyba");
        else: // neco se zkazilo v databazi
         $GLOBALS["chyba"].=GAL_OBR_UPRAVA_KO;
         Formular("galerie_chyba");
        endif; 
      else: // uzivatel nevyplnil vsechny udaje
       $GLOBALS["chyba"].=GAL_OBR_UPRAVA_UDAJE;
       Formular("obrazek_uprav"); 
      endif; 
     else: // uzivatel se pokusil nejakym zpusobem obejit zabezpeceni galerie
      $GLOBALS["chyba"].=GAL_OBR_UPRAVA_NE_NE;
      Formular("galerie_chyba");
     endif;
   endif;  
  else: // obrazek neexistuje
    $GLOBALS["chyba"].=GAL_OBR_UPRAVA_NEEX;
    Formular("galerie_chyba");
  endif; 
 else: // uzivatel neurcil obrazek ktery chce editovat
  $GLOBALS["chyba"].=GAL_OBR_UPRAVA_NENI;
  Formular("galerie_chyba");
 endif;

}

/* Funkce na zobrazeni obrazku*/
function ObrazekUkaz($media_id, $akce) {
 if($media_id!=""):
  $obrazek_vyber=mysql_query("select * from ".$GLOBALS["rspredpona"]."media WHERE media_id='$media_id' and media_smazano!='1'",$GLOBALS["dbspojeni"]); //vybereme obrazek z databaze 
  $provedeno=0;
   while($obrazek=mysql_fetch_array($obrazek_vyber)):
    $provedeno=1;
   $GLOBALS["picture_show_media_gallery_id"]=$obrazek["media_gallery_id"];   // ID galerie
   $GLOBALS["picture_show_media_gallery_title"]=Galerie("titulek",$obrazek["media_gallery_id"]);
   $GLOBALS["obrazek_celkem"]=Galerie("pocetobr",$obrazek["media_gallery_id"]);
   $GLOBALS["picture_show_media_file"]=$obrazek["media_file"];               // jmeno obrazku
   $GLOBALS["picture_show_media_caption"]=$obrazek["media_caption"];         // titulek obrazku
   $GLOBALS["picture_show_media_category"]=$obrazek["media_category"];       // kategorie
   $GLOBALS["picture_show_media_description"]=$obrazek["media_description"]; // popis obrazku
   $GLOBALS["picture_show_media_width"]=$obrazek["media_width"];             // sirka obrazku
   $GLOBALS["picture_show_media_height"]=$obrazek["media_height"];           // vyska obrazku
   $GLOBALS["picture_show_media_view"]=$obrazek["media_view"];               // pocet zobrazeni obrazku
   $GLOBALS["picture_show_media_znamka"]=$obrazek["media_znamka"];           // celk. znamka
   $GLOBALS["picture_show_media_hodnotilo"]=$obrazek["media_hodnotilo"];     // celk. pocet. hodn.
   $GLOBALS["obrazek_nasledujici"]=Obrazek("obrazek_nasledujici",$media_id);
   $GLOBALS["obrazek_predchozi"]=Obrazek("obrazek_predchozi",$media_id);
   $GLOBALS["obrazek_prvni"]=Obrazek("obrazek_prvni",$media_id);
   $GLOBALS["obrazek_posledni"]=Obrazek("obrazek_posledni",$media_id);
   $GLOBALS["obrazek_aktpozice"]=Obrazek("obrazek_aktpozice",$media_id);  
   $GLOBALS["picture_show_akce"]=$akce;                                      // akce
    if(NactiKonfigHod("phprs_verze","varchar")=="265"): // pokud mame verzi 265 pouzijeme funkci na opakujici se IP
     if (TestNaOpakujiciIP('obr'.$media_id,$GLOBALS['rsconfig']['cla_delka_omezeni'],$GLOBALS['rsconfig']['cla_max_pocet_opak'])==0):
      mysql_query("update ".$GLOBALS["rspredpona"]."media set media_view=(media_view+1) where media_id='".$media_id."'",$GLOBALS["dbspojeni"]);
     endif;
    else: // nemame 265 a tak pouze bezhlave pricteme jedno zobrazeni
      mysql_query("update ".$GLOBALS["rspredpona"]."media set media_view=(media_view+1) where media_id='".$media_id."'",$GLOBALS["dbspojeni"]);
    endif; 

   endwhile;  
    $GLOBALS["trida_neviditelne"]="neviditelne";
    if ($GLOBALS["akce"]=="obrazek_ukaz"):
     $GLOBALS["komentare"]=ZobrazKom(); 
    elseif ($GLOBALS["akce"]=="comment_fullview"):
     $GLOBALS["komentare"]=ZobrazKoKom(); $GLOBALS["trida_neviditelne"]="";
    elseif ($GLOBALS["akce"]=="comment_add"):
     $GLOBALS["komentare"]=NovyPridejKom(); $GLOBALS["trida_neviditelne"]="";
    elseif ($GLOBALS["akce"]=="comment_re"):
     $GLOBALS["komentare"]=NovyReFormKom(); $GLOBALS["trida_neviditelne"]="";
    endif; 

    $GLOBALS["pocet_zobrazeni"]= ZobrazeniPocet($GLOBALS["media_id"]);
    $GLOBALS["hodnoceni"]=HodnoceniObr($GLOBALS["media_id"]); 

 
 if($provedeno==0):
   $GLOBALS["chyba"]=GAL_OBR_UKAZ_NEEX;
   Formular("galerie_chyba"); 
 else:
   Formular("obrazek_ukaz");
 endif;  
 
 else:
  $GLOBALS["chyba"]=GAL_OBR_UKAZ_NENI;
  Formular("galerie_chyba"); 
 endif;

    
}

/* Funkce na smazani vybraneho obrazku */
function ObrazekSmaz() {
 $pocetobr=count($GLOBALS["picture_delete_check"]); //pocet obrazku na smazani
 for ($pom=0;$pom<$pocetobr;$pom++):
  $media_id=$GLOBALS["picture_delete_check"][$pom];
  $obrazek=mysql_query("SELECT media_gallery_id,media_file,media_thumbnail FROM ".$GLOBALS["rspredpona"]."media WHERE media_id='$media_id' and media_smazano!='1'",$GLOBALS["dbspojeni"]); //vybereme obrazek z databaze
  $galid=mysql_result($obrazek,0,"media_gallery_id");
  $over=mysql_query("SELECT gallery_admin,gallery_user_id FROM ".$GLOBALS["rspredpona"]."gallery WHERE gallery_id='".$galid."' AND gallery_delete!='1'",$GLOBALS["dbspojeni"]); //vybereme obrazek z databaze
  $id_user=mysql_result($over,0,"gallery_user_id");
  if (mysql_result($over,0,"gallery_admin")==1): // pokud galerie neni zalozena adminem
    if((Uzivatel("je_admin") and $id_user==Uzivatel("id_admin")) or (Uzivatel("je_redaktor") and $id_user==Uzivatel("id_redaktor")) or (Uzivatel("je_autor") and $id_user==Uzivatel("id_autor"))):
     $ok=1; else: $ok=0; endif;
   elseif(mysql_result($over,0,"gallery_admin")==0):
    if((Uzivatel("je_ctenar") and $id_user==Uzivatel("id_ctenar"))):
     $ok=1; else: $ok=0; endif;
   endif;
  if($ok):
   $delete=mysql_query("update ".$GLOBALS["rspredpona"]."media set media_smazano='1' WHERE media_id='$media_id'",$GLOBALS["dbspojeni"]); //smazeme obrazek z databaze
    if($delete): // kdyz se vsecho povedlo
     $GLOBALS["chyba"].=GAL_OBR_SMAZ_OK.$media_id.GAL_OBR_SMAZ_OK2."<br />";
    else: // kdyz se neco nepovedlo...
     $GLOBALS["chyba"].=GAL_OBR_SMAZ_KO.$media_id.GAL_OBR_SMAZ_KO2;
    endif; 
  else: // uzivatel neni vlastnikem obrazku
    $GLOBALS["chyba"].=GAL_OBR_SMAZ_NE;
  endif; 
 endfor; 
Formular("obrazek_smaz"); 
}

?>