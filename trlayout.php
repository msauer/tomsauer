<?

######################################################################
# phpRS ClassLayout 2.0.6
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_sloupce, rs_bloky, rs_plugin

/*
   Definice Layout tridy v2, ktera slouzi ke kompilaci sablony v kombinaci se vsemi preddefinovanych info. sloupci a jejich naslednemu zobrazeni.

   *** Inic. cast: ***

   $text = "sablona";

   $vzhledwebu = new CLayout();
   $vzhledwebu->NactiTxtSablonu($text);
   $vzhledwebu->UlozPro("titulek","Welcome to the my home page");
   $vzhledwebu->Inic();

   *** Generujici/kompilacni faze: ***

   $vzhledwebu->Generuj();
   ... obsah hlavniho info. bloku - tento krok lze preskocit pouzitim fce UlozHlavniBlok(...) v inic. casti pro preddefinovani obsahu hl. info. bloku ...
   $vzhledwebu->Generuj();
*/

//include_once("config.php");
//$GLOBALS["dbspojeni"]=dbcon();

if (!defined('IN_CODE')): die('Nepovoleny pristup! / Hacking attempt!'); endif;

class CLayout
{
var $obsah_sab; // obsah sablony
var $nalez_prom; // nalezene promenne v sablone
var $poc_nalez_prom; // pocet nalezenych promennych
var $pole_prom; // pole znamych ulozenych promennych
var $poc_pole_prom; // pocet znamych ulozenych promennych
var $sloupec; // informace o sloupcich
var $poc_sloupcu; // pocet sloupcu
var $hlavnistranka; // prepinac hlavni stranka
var $hlavniblok; // obsah hlavniho bloku
var $bloknazev,$bloktyp; // spec. promenne, ktere nesou akt. hodnoty prave nacteneho bloku; vyuziti u plug-inu
var $stopfce_stav,$stopfce_sl,$stopfce_blok,$stopfce_iddotaz,$stopfce_pocetdotaz; // // spec. promenne, ktere umoznuji zastavit generovani vzhledove tabulky
var $cela_sab; // informace o zprac. cele sablony

/*
  CLayout()
  NactiTxtSablonu($obs_sab = "")
  NactiFileSablonu($jmeno_souboru = "")
  NajdiPro()
  UlozPro($jmeno = "", $obsah = "")
  UlozHlavniBlok($vstup = "")
  NactiSloupce()
  VratIdSloupce($sloupec_cislo = 0)
  AktBlokNazev()
  AktBlokTyp()
  GenerujSloupecStopFce($idsloupce = 0)
  Preklad()
  Inic()
  Generuj()
*/

 function CLayout($hlstr = 0) // kontruktor
 {
 $this->obsah_sab="";
 $this->nalez_prom = array(); // 0 - string, 1 - typ stringu (0 - obycejny, 1 - promenna)
 $this->poc_nalez_prom=0;
 $this->pole_prom = array(); // 0 - jmeno, 1 - obsah
 $this->poc_pole_prom=0;
 $this->poc_sloupcu=0;
 $this->sloupec = array(); // 0 - id sloupce, 1 - zobrazit obsah (0 - ne / 1 - ano)
 $this->hlavnistranka=$hlstr;
 $this->hlavniblok="";
 $this->cela_sab=0;
 }

 function NactiTxtSablonu($obs_sab = "") // nacteni sablony z retezce
 {
 // ulozeni sablony
 $this->obsah_sab=$obs_sab;
 }

 function NactiFileSablonu($jmeno_souboru = "") // nacteni sablony ze souboru
 {
 // test na existenci souboru
 if (file_exists($jmeno_souboru)==1):
   // nacteni sablony do pole
   $obssoubor=file($jmeno_souboru);
   $pocetobs=count($obssoubor);
   for($pom=0;$pom<$pocetobs;$pom++):
     $this->obsah_sab.=$obssoubor[$pom];
   endfor;
 endif;
 }

 function NajdiPro() // dekompilace sablony; zjisteni promennych
 {
 $pom_pole=explode("<*",$this->obsah_sab);
 $poc_pom_pole=count($pom_pole);

 for ($pom=0;$pom<$poc_pom_pole;$pom++):
   $end_poz=strpos($pom_pole[$pom],"*>");
   // test na existenci ukoncovaciho znaku
   if ($end_poz==0||$end_poz>20):
     $this->nalez_prom[$this->poc_nalez_prom][0]=$pom_pole[$pom];
     $this->nalez_prom[$this->poc_nalez_prom][1]=0;
     $this->poc_nalez_prom++;
   else:
     $delka=strlen($pom_pole[$pom]); // delka stringu
     // promenna
     $this->nalez_prom[$this->poc_nalez_prom][0]=strtolower(substr($pom_pole[$pom],0,$end_poz));
     $this->nalez_prom[$this->poc_nalez_prom][1]=1;
     $this->poc_nalez_prom++;
     // zbytek stringu
     $this->nalez_prom[$this->poc_nalez_prom][0]=substr($pom_pole[$pom],$end_poz+2,$delka-$end_poz-2);
     $this->nalez_prom[$this->poc_nalez_prom][1]=0;
     $this->poc_nalez_prom++;
   endif;
 endfor;
 }

 function UlozPro($jmeno = "", $obsah = "") // ulozeni znamych promennych do pameti
 {
 if ($jmeno!=""):
   $nova_pro=1; // prepinac nova prom.
   $jmeno=strtolower(trim($jmeno));
   // test na zpusob ulozeni promenne
   for ($pom=0;$pom<$this->poc_pole_prom;$pom++):
     if($this->pole_prom[$pom][0]==$jmeno): // test na shodu
       $this->pole_prom[$pom][0]=$jmeno;
       $this->pole_prom[$pom][1]=$obsah;
       $nova_pro=0;
       break;
     endif;
   endfor;
   if ($nova_pro==1):
     // ulozeni uplne nove promenne
     $this->pole_prom[$this->poc_pole_prom][0]=$jmeno;
     $this->pole_prom[$this->poc_pole_prom][1]=$obsah;
     $this->poc_pole_prom++;
   endif;
 endif;
 }

 function UlozHlavniBlok($vstup = "") // ulozeni obsahu hlavniho bloku do pameti
 {
 $this->hlavniblok=$vstup;
 }

 function NactiSloupce() // nacteni sloupcu do pameti
 {
 $dotazslo=mysql_query("select ids,zobrazit from ".$GLOBALS["rspredpona"]."sloupce order by ids",$GLOBALS["dbspojeni"]);
 $this->poc_sloupcu=mysql_num_rows($dotazslo);

 for ($pom=0;$pom<$this->poc_sloupcu;$pom++):
   $this->sloupce[$pom][0]=mysql_Result($dotazslo,$pom,"ids");
   $this->sloupce[$pom][1]=mysql_Result($dotazslo,$pom,"zobrazit");
 endfor;
 }

 function VratIdSloupce($sloupec_cislo = 0) // prepocita logicke poradove oznaceni sloupce na platne ID
 {
 $vysl=0;

 if ($sloupec_cislo>0&&$this->poc_sloupcu>=$sloupec_cislo):
   $zaznam_cislo=$sloupec_cislo-1;
   if ($this->sloupce[$zaznam_cislo][1]==1):
     $vysl=$this->sloupce[$zaznam_cislo][0];
   endif;
 endif;

 return $vysl;
 }

 function AktBlokNazev() // vraci aktulaniho nazvu prave nacteneho bloku
 {
 return $this->bloknazev;
 }

 function AktBlokTyp() // vraci aktulaniho typu prave nacteneho bloku
 {
 return $this->bloktyp;
 }

 function GenerujSloupecStopFce($idsloupce = 0) // generator jednotlivych sloupcu
 {
 if ($this->stopfce_stav==0): // tato podminka zajisti pouze jedno nacteni; pokud je aktivni fce stop, tak se jiz znovu nenacita
   // sestaveni podm. zobrazeni (zobrazit_kde: 0 = vsude, 1 = jen hl. str., 2 = vsude mimo hl. str.)
   if ($this->hlavnistranka): // hl. str. = true
     $podminka="id_sloupec='".$idsloupce."' and zobrazit=1 and zobrazit_kde!=2"; // zobrazit: vsude + jen na hl. str.
   else: // hl. str. = false
     $podminka="id_sloupec='".$idsloupce."' and zobrazit=1 and zobrazit_kde!=1"; // zobrazit: vsude + vsude mimo hl. str.
   endif;

   $this->stopfce_blok=0; // vynulovani hlidace pozice

   // nacteni bloku odpovidajicich podmince
   $this->stopfce_iddotaz=mysql_query("select nazev,obsah,typ,data_sys,sys_funkce from ".$GLOBALS["rspredpona"]."bloky where ".$podminka." order by hodnost desc,idb",$GLOBALS["dbspojeni"]);
   $this->stopfce_pocetdotaz=mysql_num_rows($this->stopfce_iddotaz);
 else:
   $this->stopfce_stav=0; // vrati stav na false, aby dalsi prubeh touto funkci mohl byt kompletni
 endif;

 if($this->stopfce_pocetdotaz>0):
   for ($pom=$this->stopfce_blok;$pom<$this->stopfce_pocetdotaz;$pom++):
     list($blonazev,$bloobsah,$blotyp,$blodatasys,$blosysfunkce)=mysql_fetch_row($this->stopfce_iddotaz);
     if ($blodatasys==0):
       // datove bloky
       switch($blotyp):
           case 1: Blok1($blonazev,$bloobsah);  break;
           case 2: Blok2($blonazev,$bloobsah);  break;
           case 3: Blok3($blonazev,$bloobsah);  break;
           case 4: Blok4($blonazev,$bloobsah);  break;
           case 5: Blok5($blonazev,$bloobsah);  break;
       endswitch;
     else:
       // ulozeni nazvu a typu bloku do pameti
       $this->bloknazev=$blonazev;
       $this->bloktyp=$blotyp;
       // systemove bloky
       switch($blosysfunkce): // ank, nov, rub, kal, hlb
           case 'ank': Anketa(); break;
           case 'nov': HotNews(); break;
           case 'rub': GenHlavMenu(); break;
           case 'kal': Kalendar(); break;
           case 'hlb': if ($this->hlavniblok==""):
                         $this->stopfce_stav=1; // aktivace stop fce
                         $this->stopfce_blok=($pom+1); // ulozeni navratove pozice v MySQL dotazu
                       else:
                         echo $this->hlavniblok;
                       endif;
                       break;
           default:
             $dotazplug=mysql_query("select inclsb_blok,funkce_blok from ".$GLOBALS["rspredpona"]."plugin where zkratka_blok='".$blosysfunkce."' and sys_blok='1'",$GLOBALS["dbspojeni"]);
             $pocetplug=mysql_num_rows($dotazplug);
             if ($pocetplug>0): // nasel odpovidajici plug-in
                include_once(mysql_Result($dotazplug,0,"inclsb_blok"));
                call_user_func(mysql_Result($dotazplug,0,"funkce_blok"));
             endif;
             break;
       endswitch;
     endif;

     if ($this->stopfce_stav==1): break; endif; // kdyz je aktivovana stop fce, tak ukonci beh for cyklu
   endfor; // konec $pocteslo

   $this->bloknazev="";
   $this->bloktyp="";
 endif; // konec $pocetslo
 }

 function Preklad() // preklad sablony do finalni podoby
 {
 for ($pom=$this->stopfce_preklad;$pom<$this->poc_nalez_prom;$pom++):
   if ($this->nalez_prom[$pom][1]==0):
     // obycejny string
     echo $this->nalez_prom[$pom][0];
   else:
     // promenna
     if (substr_count($this->nalez_prom[$pom][0],"syssl")==1): // test na systemovou promennou - sloupec
       $sys_sloupec=explode(":",$this->nalez_prom[$pom][0]); // 0 - identifikace (syssl), 1 - cislo sloupce
       $this->GenerujSloupecStopFce($this->VratIdSloupce($sys_sloupec[1])); // poradove cislo sloupce musi byt skrze fci VratIdSloupce() prelozeno na platne ID
       // test na stop stav
       if ($this->stopfce_stav==1):
         $this->stopfce_preklad=$pom;
         break;
       endif;
     else:
       // hledani odpovidajici promenne
       for ($p1=0;$p1<$this->poc_pole_prom;$p1++):
         if ($this->nalez_prom[$pom][0]==$this->pole_prom[$p1][0]):
           echo $this->pole_prom[$p1][1];
           break;
         endif;
       endfor;
       // konec - hledani odpovidajici promenne
     endif;
   endif;
 endfor;
 // test na probehnuti cele sablony
 if ($pom>=$this->poc_nalez_prom):
   $this->cela_sab=1;
 endif;
 }

 function Inic()
 {
 // prednacteni a zprac. potrebnych dat
 $this->NajdiPro();
 $this->NactiSloupce();
 // inic. inter. promennych
 $this->bloknazev="";
 $this->bloktyp="";
 // inic. stop fce
 $this->stopfce_stav=0; // stav stop fce
 $this->stopfce_sl=0; // akt. sloupec
 $this->stopfce_blok=0; // akt. blok cekajici na zpracovani
 $this->stopfce_iddotaz=0; // id MySQL dotazu
 $this->stopfce_pocetdotaz=0; // pocet radku ziskanych behem MySQL dotazu
 $this->stopfce_preklad=0; // akt. pozice v prekladu sablony
 }

 function Generuj()
 {
 // test na uplnost dokonceni prekladu sablony; zabranuje vice-nasobnemu volani
 if ($this->cela_sab==0):
   $this->Preklad();
 endif;
 }
}

?>