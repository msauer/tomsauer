<?php
######################################################################
# phpRS Gallery 0.99.500
######################################################################
// phpRS Copyright (c) 2001-2004 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// Gallery Copyright (c) 2004 by Michal Safus (michalsafus@gmail.com)
// http://www.phprs.cz/magazin/gallery
// This program is free software. - Toto je bezplatny a svobodny software.

function ObrazekCestina($pocet) {
 switch ($pocet):
  case "0": return GAL_VYPIS_OBRAZKY_0; break;
  case "1": return GAL_VYPIS_OBRAZKY_1; break;
  case "2": return GAL_VYPIS_OBRAZKY_2; break;
  case "3": return GAL_VYPIS_OBRAZKY_3; break;
  case "4": return GAL_VYPIS_OBRAZKY_4; break;
  default: return $pocet.GAL_VYPIS_OBRAZKY_5; break;
 endswitch; 
}

function JePovolena() {
 include_once("version.php");
 if($phprsversion_kod=="265alfa" or $phprsversion_kod=="265beta" or $phprsversion_kod=="265"):
  $mysql=mysql_query("select blokovat_modul from ".$GLOBALS["rspredpona"]."moduly_prava where ident_modulu='gallery'",$GLOBALS["dbspojeni"]); 
  $povoleno=0;
  $povoleno=mysql_result($mysql,0,"blokovat_modul");
  if(!$povoleno):
   return 1;
  else:
   return 0;
  endif;
 else:
  return 1;
 endif;  
}

function Navigace($jaka) {
$galerie_prehled=        "<a href=\"gallery.php\">".GAL_NAV_PREHLED."</a> - ";
$kategorie_prehled=      "<a href=\"gallery.php?akce=kategorie_prehled\">".GAL_NAV_KATEGORIE."</a> - ";

if(NactiKonfigHod("galerie_interni","varchar")==1):
 $galerie_prehled_interni="<a href=\"gallery.php?akce=galerie_prehled_interni\">".GAL_NAV_PREHLED_INTERNI."</a> - ";
else:
 $galerie_prehled_interni="";
endif;


if(Uzivatel("muze_zalozit_galerii")):
 $galerie_nova=           "<a href=\"gallery.php?akce=galerie_nova\">".GAL_NAV_NOVA."</a> - ";
else:
 $galerie_nova="";
endif;

if(Uzivatel("muze_pridat_obrazek")):
 $obrazek_novy=           "<a href=\"gallery.php?akce=obrazek_novy_rozcest\">".GAL_NAV_NOVY."</a> - ";
else:
 $obrazek_novy="";
endif;
$obrazek_top=            "<a href=\"gallery.php?akce=obrazek_top\">".GAL_NAV_TOP."</a>";

 switch($jaka):
  default: echo "<br /><br /><hr class=\"gal_cara\" /><div class=\"gal_navigace\">$galerie_prehled $galerie_prehled_interni $kategorie_prehled $galerie_nova $obrazek_novy $obrazek_top</div>"; break;
 endswitch;
}

/* Funkce na zjistovani GD knihovny. */
function ZjistiGD($jak) {
 if($jak=="auto"):
  if(function_exists("gd_info")):
   $verze=gd_info(); $mystring=$verze["GD Version"]; $findme = '(2.'; $pos = strpos($mystring, $findme);
   if ($pos === false): return "gd1"; else: return "gd2"; endif;
  else:
   return "gd1";
  endif;
 elseif($jak=="gd1"): return "gd1";
 elseif($jak=="gd2"): return "gd2";
 else: return "gd1" ;
 endif; 
}

function Uzivatel($co) {
switch($co):
 case "id_admin":
  return $GLOBALS["GalUzivatel"]->Ukaz("id"); 
 break;
 case "id_redaktor":
  return $GLOBALS["GalUzivatel"]->Ukaz("id"); 
 break;
 case "id_autor":
  return $GLOBALS["GalUzivatel"]->Ukaz("id"); 
 break;
 case "id_ctenar":
  return $GLOBALS["prmyctenar"]->Ukaz("id");
 break;
 
 case "je_admin":
  if($GLOBALS["GalUzivatel"]->OvereniTypuBool(2) AND Uzivatel("id_admin")!=""): return 1; else: return 0; endif;
 break;
 case "je_redaktor":
  if($GLOBALS["GalUzivatel"]->OvereniTypuBool(1) AND Uzivatel("id_redaktor")!=""): return 1; else: return 0; endif;
 break;
 case "je_autor":
  if($GLOBALS["GalUzivatel"]->OvereniTypuBool(0) AND Uzivatel("id_autor")!=""): return 1; else: return 0; endif;
 break; 
 case "je_ctenar":
  if($GLOBALS["prmyctenar"]->ctenarstav AND Uzivatel("id_ctenar")!=""): return 1; else: return 0; endif;
 break;
 
 case "pocet_galerii_admin":
  $mysql=mysql_query("select count(gallery_id) as pocet from ".$GLOBALS["rspredpona"]."gallery where gallery_user_id='".Uzivatel("id_admin")."' and gallery_admin='1' and gallery_delete!='1' ",$GLOBALS["dbspojeni"]); 
  return mysql_result($mysql,0,"pocet");
 break;
 case "pocet_galerii_redaktor":
  $mysql=mysql_query("select count(gallery_id) as pocet from ".$GLOBALS["rspredpona"]."gallery  where gallery_user_id='".Uzivatel("id_admin")."' and gallery_admin='1' and gallery_delete!='1' ",$GLOBALS["dbspojeni"]); 
  return mysql_result($mysql,0,"pocet");
 break;
 case "pocet_galerii_autor":
  $mysql=mysql_query("select count(gallery_id) as pocet from ".$GLOBALS["rspredpona"]."gallery  where gallery_user_id='".Uzivatel("id_admin")."' and gallery_admin='1' and gallery_delete!='1' ",$GLOBALS["dbspojeni"]); 
  return mysql_result($mysql,0,"pocet");
 break;  
 case "pocet_galerii_ctenar":
  $mysql=mysql_query("select count(gallery_id) as pocet from ".$GLOBALS["rspredpona"]."gallery  where gallery_user_id='".Uzivatel("id_ctenar")."' and gallery_admin='0' and gallery_delete!='1' ",$GLOBALS["dbspojeni"]); 
  return mysql_result($mysql,0,"pocet");
 break; 
 
 case "muze_zalozit_galerii":
  if(Uzivatel("je_admin")):
   if(Uzivatel("pocet_galerii_admin")<=NactiKonfigHod("pocet_gal_admin","varchar") and NactiKonfigHod("pocet_gal_admin","varchar")>0): return 1; else: return 0; endif;
  elseif(Uzivatel("je_redaktor")):
   if(Uzivatel("pocet_galerii_redaktor")<=NactiKonfigHod("pocet_gal_redaktor","varchar") and NactiKonfigHod("pocet_gal_redaktor","varchar")>0): return 1; else: return 0; endif;
  elseif(Uzivatel("je_autor")):
   if(Uzivatel("pocet_galerii_autor")<=NactiKonfigHod("pocet_gal_autor","varchar") and NactiKonfigHod("pocet_gal_autor","varchar")>0): return 1; else: return 0; endif;
  elseif(Uzivatel("je_ctenar")):
   if(Uzivatel("pocet_galerii_ctenar")<=NactiKonfigHod("pocet_gal_ctenar","varchar") and NactiKonfigHod("pocet_gal_ctenar","varchar")>0): return 1; else: return 0; endif;      
  else:
   return 0;
  endif; 
 break;
 case "muze_pridat_obrazek":
  if((Uzivatel("je_admin") and Uzivatel("pocet_galerii_admin")>0 and NactiKonfigHod("pocet_gal_admin","varchar")>0) or (Uzivatel("je_redaktor") and Uzivatel("pocet_galerii_redaktor")>0 and NactiKonfigHod("pocet_gal_redaktor","varchar")>0) or (Uzivatel("je_autor") and Uzivatel("pocet_galerii_autor")>0 and NactiKonfigHod("pocet_gal_autor","varchar")>0) or (Uzivatel("je_ctenar") and Uzivatel("pocet_galerii_ctenar")>0 and NactiKonfigHod("pocet_gal_ctenar","varchar")>0)):
   return 1;
  else:
   return 0;
  endif; 
 break;
 
 case "pocet_galerii_zbyva":
  $pocet=0;
  if(Uzivatel("je_admin")):
   $pocet=$pocet+NactiKonfigHod("pocet_gal_admin","varchar")-Uzivatel("pocet_galerii_admin");
  elseif(Uzivatel("je_redaktor")):
   $pocet=$pocet+NactiKonfigHod("pocet_gal_redaktor","varchar")-Uzivatel("pocet_galerii_redaktor");
  elseif(Uzivatel("je_autor")):
   $pocet=$pocet+NactiKonfigHod("pocet_gal_autor","varchar")-Uzivatel("pocet_galerii_autor");
  endif;  
  if(Uzivatel("je_ctenar")):
   $pocet=$pocet+NactiKonfigHod("pocet_gal_ctenar","varchar")-Uzivatel("pocet_galerii_ctenar");
  endif;  
  return $pocet;
 break;
endswitch; 

}


/* Funkce zajistujici strankovani */
function Strankovani($co,$celkpocet,$pokolika,$aktstrana) {
 switch($co):
  case "galerie_prehled":
   $odkaz="akce=galerie_prehled&amp;";
   $text=GAL_STRANKOVANI_CELKEM2_GAL;
  break;
  case "galerie_prehled_interni":
   $odkaz="akce=galerie_prehled_interni&amp;";
   $text=GAL_STRANKOVANI_CELKEM2_GAL;
  break;  
  case "galerie_ukaz":
   $odkaz="akce=galerie_ukaz&amp;gal_ukaz_order=".$GLOBALS["gal_ukaz_order"]."&amp;gal_ukaz_pocet=".$GLOBALS["gal_ukaz_pocet"]."&amp;galerie_id=".$GLOBALS["galerie_id"]."&amp;";
   $text=GAL_STRANKOVANI_CELKEM2_OBR;
  break; 
  case "galerie_ukaz_interni":
   $odkaz="akce=galerie_ukaz_interni&amp;gal_ukaz_order=".$GLOBALS["gal_ukaz_order"]."&amp;gal_ukaz_pocet=".$GLOBALS["gal_ukaz_pocet"]."&amp;galerie_id=".$GLOBALS["galerie_id"]."&amp;";
   $text=GAL_STRANKOVANI_CELKEM2_OBR;
  break;   
  case "kategorie_prehled":
   $odkaz="akce=kategorie_prehled&amp;";
   $text=GAL_STRANKOVANI_CELKEM2_GAL;
  break;   
  case "kategorie_ukaz":
   $odkaz="akce=kategorie_ukaz&amp;kat_ukaz_order=".$GLOBALS["kat_ukaz_order"]."&amp;kat_ukaz_pocet=".$GLOBALS["kat_ukaz_pocet"]."&amp;kat_id=".$GLOBALS["kat_id"]."&amp;";
   $text=GAL_STRANKOVANI_CELKEM2_OBR;
  break;    
 endswitch; 

 $po=6; // kolik okolnich cisel priblizne zobrazovat
  if($aktstrana<round($po/2)): $pozs=round($po/2);
  elseif($aktstrana>($celkpocet-round($po/2))): $pozs=$celkpocet-round($po/2);
  else: $pozs = $aktstrana;
  endif;
  
  for($i=1;$i<=ceil($celkpocet/$pokolika);$i++):
    if((($i>$pozs-round($po/2)) and ($i<$pozs+round($po/2))) or $i==1 or $i==ceil($celkpocet/$pokolika)):    
      if($i==ceil($celkpocet/$pokolika) and $aktstrana<ceil($celkpocet/$pokolika)-round($po/2)):
       $GLOBALS["str_cisla"].=" &hellip; ";
      endif;
      if($aktstrana!=$i):
       $GLOBALS["str_cisla"].="<a href=\"?".$odkaz."strana=".$i."\">".$i."</a>";
      else:
       $GLOBALS["str_cisla"].="<strong>".$i."</strong>";
      endif;
      if($i==1 and $aktstrana>(0+round($po/2))):
       $GLOBALS["str_cisla"].=" &hellip; "; 
      else:
       $GLOBALS["str_cisla"].=" ";
      endif;
    endif;
  endfor;
    $od=$aktstrana*$pokolika-$pokolika+1;
    $do=min($aktstrana*$pokolika,$celkpocet);
    $GLOBALS["str_celkem"]=GAL_STRANKOVANI_CELKEM.$celkpocet.$text;
    $GLOBALS["str_zobrazeno"]=GAL_STRANKOVANI_ZOBRAZENO.$od." - ".$do;
    Formular("strankovani");
}



/* Funkce na hodnoceni obrazku (to byste si netipli, co?) */
function HodnoceniObr($id_obrazek = 0) //(ukradene ze specfce.php - diky Jirko :-))
{
 $id_obrazek=addslashes($id_obrazek); // vstupni korekce
 $dotazhod=mysql_query("select media_znamka,media_hodnotilo from ".$GLOBALS["rspredpona"]."media where media_id='".$id_obrazek."' and media_smazano!='1'",$GLOBALS["dbspojeni"]);
  if (mysql_num_rows($dotazhod)>0): // clanek existuje
   $hodnoceni=mysql_Result($dotazhod,0,"media_znamka"); // hodnoceni
   $mnozstvi=mysql_Result($dotazhod,0,"media_hodnotilo"); // mnozstvi hodnot
    $vypis.="<div align=\"center\">";
    $vypis.="<form action=\"gallery.php\" method=\"post\" class=\"gal_inline\">";
    $vypis.="<input type=\"hidden\" name=\"media_id\" value=\"".$id_obrazek."\" />\n";
    $vypis.="<input type=\"hidden\" name=\"akce\" value=\"obrazek_ukaz\" />\n";
    $vypis.="<input type=\"submit\" name=\"hlasovani\" value=\"1\" class=\"gal_tl\" title=\"".GAL_ZNAMKA_1."\"/> ";
    $vypis.="<input type=\"submit\" name=\"hlasovani\" value=\"2\" class=\"gal_tl\" title=\"".GAL_ZNAMKA_2."\"/> ";
    $vypis.="<input type=\"submit\" name=\"hlasovani\" value=\"3\" class=\"gal_tl\" title=\"".GAL_ZNAMKA_3."\"/> ";
    $vypis.="<input type=\"submit\" name=\"hlasovani\" value=\"4\" class=\"gal_tl\" title=\"".GAL_ZNAMKA_4."\"/> ";
    $vypis.="<input type=\"submit\" name=\"hlasovani\" value=\"5\" class=\"gal_tl\" title=\"".GAL_ZNAMKA_5."\"/> ";
     if ($mnozstvi>0):
      $vypis.="[".GAL_ZNAMKA.": ".number_format(($hodnoceni/$mnozstvi),2,'.','')."] ";
     else:
      $vypis.="[".GAL_ZNAMKA.": 0] ";
     endif;
    $vypis.="</form></div>\n";
  return $vypis;
  endif;
}

/* Funkce vypise pocet zobrazeni obrazku */
function ZobrazeniPocet($id) {
 $vyber=mysql_query("select media_view from ".$GLOBALS["rspredpona"]."media where media_id='".$id."' and media_smazano!='1'", $GLOBALS["dbspojeni"]);
 if(mysql_num_rows($vyber)==1):
 $kolik=mysql_result($vyber,0,media_view);
 switch($kolik):
  case "0": return GAL_POCET_ZOBRAZENI_0; break; // teoreticky se nemuze stat
  case "1": return GAL_POCET_ZOBRAZENI_1; break;
  case "2": return GAL_POCET_ZOBRAZENI_2; break;
  case "3": return GAL_POCET_ZOBRAZENI_3; break;
  case "4": return GAL_POCET_ZOBRAZENI_4; break;
  default: return GAL_POCET_ZOBRAZENI_5_1.$kolik.GAL_POCET_ZOBRAZENI_5_2; break;
 endswitch;
 endif;
}


/* Funkce na znamkovani obrazku - "ukradeno" od Jirky :-) Dik */
function ZnamkujObr($id_obrazek = "",$znamka = 0)
{
 $id_obrazek=addslashes($id_obrazek); // bezpecnostni korekce
 $znamka=addslashes($znamka); // bezpecnostni korekce
 $hlasuj=1; // true
 if (isset($_COOKIE["znamkovani_obrazek"])):
  // kdyz kontrolni cookie existuje
   $vstup=base64_decode($_COOKIE["znamkovani_obrazek"]);
   $zakazna=explode(":",$vstup);
   $pocet_zak=count($zakazna);
    for ($pom=0;$pom<$pocet_zak;$pom++):
     if ($zakazna[$pom]==$id_obrazek):
      $hlasuj=0; // false
      break;
     endif;
    endfor;
  if ($hlasuj==1):
    $str_cookie=base64_encode($vstup.":".$id_obrazek);
    setcookie("znamkovani_obrazek",$str_cookie,time()+315360000); // odeslani cookie
  endif;
 else:
  // kdyz kontrolni cookie neexistuje
  $str_cookie=base64_encode($id_obrazek);
  setcookie("znamkovani_obrazek",$str_cookie,time()+315360000); // odeslani cookie
 endif;
 if ($hlasuj==1):
  if ($znamka>0&&$znamka<6): // test na platnost znamky: 1 - 5
    @mysql_query("update ".$GLOBALS["rspredpona"]."media set media_znamka=media_znamka+".$znamka.", media_hodnotilo=media_hodnotilo+1 where media_id='".$id_obrazek."'",$GLOBALS["dbspojeni"]);
  endif;
 endif;
}

function Verze() {
include_once("version.php");
echo "<div class=\"nadpis\">".GAL_VERZE_INFO."</div>";
echo "<br />".GAL_VERZE_GALERIE.": ".$GLOBALS["galerie"]["verze"];
echo "<br />".GAL_VERZE_VERZE." gallery.php: ".$GLOBALS["galerie"]["phprs"];
echo "<br />".GAL_VERZE_VERZE." version.php: ".$phprsversion;
echo "<br /><br /><a href=\"http://www.phprs.cz/magazin/gallery/index.php?verze=".$GLOBALS["galerie"]["verze"]."\">".GAL_VERZE_NOVA."</a>";

}

function Formular($sablona) {
 $fp=fopen($GLOBALS["sablony"]["cesta"].$sablona.".php", "r");
 $page=fread($fp, filesize($GLOBALS["sablony"]["cesta"].$sablona.".php"));
 fclose($fp);
 $pom_pole=explode("<*",$page);
 $poc_pom_pole=count($pom_pole);
 for($pom=0;$pom<$poc_pom_pole;$pom++):
  if((substr_count($pom_pole[$pom],"*>"))>0):
   $orez=explode("*>",$pom_pole[$pom]);
   $nahrad[]=trim($orez[0]);
  endif;
 endfor;
 $count=count($nahrad);
 for($pom=0;$pom<$count;$pom++):
  $najdi="<*".$nahrad[$pom]."*>";
  $cim=$GLOBALS[$nahrad[$pom]];
  $page=str_replace ($najdi,$cim,$page);
 endfor;
 echo $page;
} 

function NactiKonfigHod($promenna = '', $typ = '')
{
 $promenna=addslashes($promenna);
 switch ($typ):
   case 'varchar': $dotaz="select idk,hodnota from ".$GLOBALS["rspredpona"]."gallery_konfigurace where nazev='".$promenna."'"; break;
 endswitch;
 $dotazhod=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
 if ($dotazhod==0):
   // promenna neexistuje
   $vysledek='';
 else:
  if (mysql_num_rows($dotazhod)==1):
    // promenna nactena
    $vysledek=mysql_fetch_row($dotazhod);
    $vysledek=$vysledek[1];
  else:
    // promenna neexistuje
    $vysledek='';
  endif;
 endif;
 return $vysledek; // pole: 0 = id promenne, 1 = hodnota promenne
}
?>