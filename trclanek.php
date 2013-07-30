<?

######################################################################
# phpRS ClassClanek 1.2.4
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_user, rs_topic, rs_clanky, rs_imggal_obr

if (!defined('IN_CODE')): die('Nepovoleny pristup! / Hacking attempt!'); endif;

class CClanek
{
var $aktivni; // stavovy ukazatel tridy; 1 = jsou nacteny clanky, 0 = trida je prazdna
var $poleautori,$poletema,$polesab; // pomocne pole: autori, temata, sablony
var $pocetautori,$pocettema,$pocetsab; // pocet prvku v pom. polich
var $stavautori,$stavtema,$stavsab; // stavove prepinace pomocnych poli
var $clanek; // assoc. pole clanek
var $pocetclanku,$aktpozice,$dotazclanek; // pocet clanku ulozenych v pameti, akt. pozice v pameti, id dotazu na clanek(y)
var $testplat,$vydani,$kontroladata; // testovat na platnost, kontrola vydani clanku, kontrola data vydani

/*
  Dostupne funkce ve tride CClanek:

  CClanek()                             - konstruktor
  NactiAutory()                         - hromadne nacteni autoru
  ZjistiAutora($id = 0,$co = "")        - preklad autora
  AntiSpam($str = "")                   - upravuje formu e-mailove adresy
  NactiTemata()                         - hromadne nacteni temat
  ZjistiTema($id = 0,$co = "")          - preklad temata
  NactiSab()                            - hromadne nacteni clankovych sablon
  ZjistiSab($id = 0)                    - preklad sablony
  HlidatPlatnost($stav = 1)             - nastaveni hlidani platnosti cl. (defautl NE)
  NastavVydani($stav = 1)               - nastaveni kontroly vydani cl. (default ANO)
  HlidatAktDatum($stav = 1)             - nastaveni hlidani data vydani (default ANO)
  Dekoduj($text = "")                   - dekodovaci fce pro phpRS znacky
  NactiClanek($link = 0)                - nacteni jednoho clanku do pameti; navratova hodnota oznamuje uspesnost akce
  NactiClanky($mnozstvi = 0, $od = 0)   - nacteni X mnoz. clanku do pameti; navratova hodnota oznamuje uspesnost akce
  NactiZdrojCla($id_zdroj = 0)          - import zdroje nactenych clanku; navratova hodnota oznamuje uspesnost akce
  DalsiRadek()                          - nastaveni pozice v pameti clanku
  CelkemClanku()                        - zjisteni celkoveho poctu clanku odpovidajicich pripadne podmince
  Ukaz($co = "")                        - zobrazeni dostpnych atributu clanku
*/

 function CClanek()
 {
 $this->aktivni=0;
 // inic. pomocnych promennych
 $this->stavautori=0;
 $this->stavtema=0;
 $this->stavsab=0;
 $this->testplat=0;
 $this->vydani=1;
 $this->kontroladata=1;
 $this->clanek=array();
 }

 function NactiAutory()
 {
 // nacteni seznamu uzivatelu(autoru) do pole "poleautori"
 if ($this->stavautori==0):
   $dotazaut=mysql_query("select idu,jmeno,email,im_ident from ".$GLOBALS["rspredpona"]."user order by idu",$GLOBALS["dbspojeni"]);
   $this->pocetautori=mysql_num_rows($dotazaut);
   if ($this->pocetautori>0):
     $this->stavautori=1; // nastaveni aktivniho stavu
     while($data_aut = mysql_fetch_row($dotazaut)):
       $this->poleautori[$data_aut[0]][0]=$data_aut[1];
       $this->poleautori[$data_aut[0]][1]=$this->AntiSpam($data_aut[2]);
       $this->poleautori[$data_aut[0]][2]=$data_aut[3];
     endwhile;
   endif;
 endif;
 }

 function ZjistiAutora($id = 0,$co = '')
 {
 $vysl_jm='';
 $vysl_mail='';
 $vysl_im='';

 if ($id!=0):
   if ($this->stavautori==1):
     // existuje aktivni pole s autory ve tride
     if (isset($this->poleautori[$id])): // autor nalezen
       $vysl_jm=$this->poleautori[$id][0]; // jmeno
       $vysl_mail=$this->poleautori[$id][1]; // mail
       $vysl_im=$this->poleautori[$id][2]; // instant messaging
     endif;
   else:
     // musi se zjisti primo v DB
     $dotazaut=mysql_query("select jmeno,email,im_ident from ".$GLOBALS["rspredpona"]."user where idu='".$id."'",$GLOBALS["dbspojeni"]);
     if (mysql_num_rows($dotazaut)==1):
       $pole_data=mysql_fetch_assoc($dotazaut);
       $vysl_jm=$pole_data["jmeno"];
       $vysl_mail=$this->AntiSpam($pole_data["email"]);
       $vysl_im=$pole_data["im_ident"];
     endif;
   endif;

   // vypis vysledku
   switch($co):
     case 'jm': return $vysl_jm; break;
     case 'mail': return $vysl_mail; break;
     case 'im': return $vysl_im; break;
     default: return ''; break;
   endswitch;
 else:
   return ''; // chyba
 endif;
 }

 function AntiSpam($str = '')
 {
 return str_replace ("@","(at)",$str);
 }

 function NactiTemata()
 {
 if ($this->stavtema==0):
   // nacteni seznamu temat do pole "rubriky"
   $dotazrub=mysql_query("select idt,nazev,obrazek from ".$GLOBALS["rspredpona"]."topic order by idt",$GLOBALS["dbspojeni"]);
   $this->pocettema=mysql_num_rows($dotazrub);
   if ($this->pocettema>0):
     $this->stavtema=1; // nastaveni aktivniho stavu
     while ($data_tem = mysql_fetch_row($dotazrub)):
       $this->poletema[$data_tem[0]][1]=$data_tem[1];
       $this->poletema[$data_tem[0]][2]=$data_tem[2];
     endwhile;
   endif;
 endif;
 }

 function ZjistiTema($id = 0,$co = '')
 {
 $vysl_jm='';
 $vysl_obr='';

 if ($id!=0):
   if ($this->stavtema==1):
     // existuje aktivni pole s tematy ve tride
     if (isset($this->poletema[$id])): // tema nalezeno
       $vysl_jm=$this->poletema[$id][1];
       $vysl_obr=$this->poletema[$id][2];
     endif;
   else:
     // musi se zjisti primo v DB
     $dotazrub=mysql_query("select nazev,obrazek from ".$GLOBALS["rspredpona"]."topic where idt='".$id."'",$GLOBALS["dbspojeni"]);
     if (mysql_num_rows($dotazrub)==1):
       $vysl_jm=mysql_result($dotazrub,0,"nazev");
       $vysl_obr=mysql_result($dotazrub,0,"obrazek");
     endif;
   endif;

   // vypis vysledku
   switch($co):
     case 'jm': return $vysl_jm; break;
     case 'obr': return $vysl_obr; break;
     default: return ''; break;
   endswitch;
 else:
   return ''; // chyba
 endif;
 }

 function NactiSab()
 {
 if ($this->stavsab==0):
   // nacteni seznamu temat do pole "rubriky"
   $dotazsab=mysql_query("select ids,soubor_cla_sab from ".$GLOBALS["rspredpona"]."cla_sab order by ids",$GLOBALS["dbspojeni"]);
   $this->pocetsab=mysql_num_rows($dotazsab);
   for ($pom=0;$pom<$this->pocetsab;$pom++):
     $this->polesab[mysql_result($dotazsab,$pom,"ids")]=mysql_result($dotazsab,$pom,"soubor_cla_sab");
   endfor;
   if ($this->pocetsab>0): $this->stavsab=1; endif; // aktivni stav
 endif;
 }

 function ZjistiSab($id = 0)
 {
 $vysl_sab="";

 if ($id!=0):
   if ($this->stavsab==1):
     // existuje aktivni pole s sabl. ve tride
     if (isset($this->polesab[$id])): // sablona nalezena
       $vysl_sab=$this->polesab[$id];
     endif;
   else:
     // musi se zjisti primo v DB
     $dotazsab=mysql_query("select ids,soubor_cla_sab from ".$GLOBALS["rspredpona"]."cla_sab where ids='".$id."'",$GLOBALS["dbspojeni"]);
     if (mysql_num_rows($dotazsab)==1):
       $vysl_sab=mysql_result($dotazsab,0,"soubor_cla_sab");
     endif;
   endif;

   // vypis vysledku
   return $vysl_sab;
 else:
   return ""; // chyba
 endif;
 }

 function HlidatPlatnost($stav = 1)
 {
 if ($stav==1):
   $this->testplat=1; // bude se testovat platnost cl.
 else:
   $this->testplat=0; // nebude se testovat
 endif;
 }

 function NastavVydani($stav = 1)
 {
 if ($stav==0):
   $this->vydani=0; // na stavu clanku nezalezi
 else:
   $this->vydani=1; // clanek musi byt vydan
 endif;
 }

 function HlidatAktDatum($stav = 1)
 {
 if ($stav==0):
   $this->kontroladata=0; // nebude se testovat stari clanku
 else:
   $this->kontroladata=1; // datum publikovani clanku musi byt starsi nebo shodne s aktualnim datem
 endif;
 }

 function Dekoduj($text = "")
 {
 // $polenalezzn[][XXX] - XXX: 0 - znacka, 1 - id obr, 2 - zarovnani
 if ($text!=""):
  $pozice=strpos("x".$text,"<obrazek");
  if ($pozice>0): // exsituje min. 1 znacky
    $pozice--; // prevod na realnou pozici
    $rotuj=1; // inic. rotace
    while ($rotuj):
      $retezec=substr($text,$pozice,60); // vykopirovani
      $konec=strpos($retezec,">"); // konec znacky
      $konec++; // pricitam 1 kvuli pocitani pozice od 0
      $znacka=substr($retezec,0,$konec); // znacka
      // zpracovani znacky
      $atributy=str_replace("<obrazek ","",$znacka); // co,cim,kde
      $atributy=str_replace(">","",$atributy);
      $atributy=str_replace('"',"",$atributy);
      $atributy=trim($atributy);
      // sestaveni pole s atributy
      $pole_atr=explode(" ",$atributy);
      $pocet_atr=count($pole_atr);
      // zpracovani pole atributu
      $idobrazku=0;
      $zaobrazku='center'; // prednastaveni atributu zarovnani
      $nahled='ne'; // prednastaveni atributu nahled
      $externi='ne'; // prednastaveni atributu externi
      for ($pom=0;$pom<$pocet_atr;$pom++):
        $jmeno="";
        list($jmeno,$hodnota)=explode("=",$pole_atr[$pom]);
        switch($jmeno):
          case 'id': $idobrazku=mysql_escape_string($hodnota); break;
          case 'zarovnani':
             switch($hodnota):
                case 'nastred': $zaobrazku='center'; break;
                case 'vlevo': $zaobrazku='left'; break;
                case 'vpravo': $zaobrazku='right'; break;
             endswitch;
             break;
          case 'externi':
             switch($hodnota):
                case 'ano': $externi='ano'; break;
                case 'ne': $externi='ne'; break;
             endswitch;
             break;
          case 'nahled': $nahled=$hodnota; break;
        endswitch;
      endfor;
      // dotaz na obrazek
      if($externi=='ano'):
        // data ziskana z externi galerie
        $dotazobr=mysql_query("select media_id,media_caption as nazev,media_file as obr_poloha,media_width as obr_width,media_height as obr_height,media_thumbnail as nahl_poloha,media_thumbnail_width as nahl_width,media_thumbnail_height as nahl_height from ".$GLOBALS["rspredpona"]."media where media_id='".$idobrazku."'",$GLOBALS["dbspojeni"]);
        $pocetobr=mysql_num_rows($dotazobr);
        if($pocetobr>0):
          $pole_obrazek=mysql_fetch_assoc($dotazobr);
          $odkaz_obrazek="gallery.php?akce=obrazek_ukaz&amp;media_id=".$pole_obrazek["media_id"];
        endif;
      else:
        // data ziskana z interni galerie
        $dotazobr=mysql_query("select nazev,obr_poloha,obr_width,obr_height,nahl_poloha,nahl_width,nahl_height from ".$GLOBALS["rspredpona"]."imggal_obr where ido='".$idobrazku."'",$GLOBALS["dbspojeni"]);
        $pocetobr=mysql_num_rows($dotazobr);
        if($pocetobr>0):
          $pole_obrazek=mysql_fetch_assoc($dotazobr);
          $odkaz_obrazek=$pole_obrazek["obr_poloha"];
        endif;
      endif;
      // zpracovani obrazku
      if ($pocetobr==1):
        if ($nahled=="ano"):
          // nahled
          if ($zaobrazku=="center"):
            $sestavenyobr="<div align=\"center\"><a href=\"".$odkaz_obrazek."\" target=\"_blank\"><img src=\"".$pole_obrazek["nahl_poloha"]."\" width=\"".$pole_obrazek["nahl_width"]."\" height=\"".$pole_obrazek["nahl_height"]."\" alt=\"".$pole_obrazek["nazev"]."\" title=\"".$pole_obrazek["nazev"]."\" /></a></div>";
          else:
            $sestavenyobr="<a href=\"".$odkaz_obrazek."\" target=\"_blank\"><img src=\"".$pole_obrazek["nahl_poloha"]."\" align=\"".$zaobrazku."\" width=\"".$pole_obrazek["nahl_width"]."\" height=\"".$pole_obrazek["nahl_height"]."\" alt=\"".$pole_obrazek["nazev"]."\" title=\"".$pole_obrazek["nazev"]."\" /></a>";
          endif;
        else:
          // bez nahledu
          if ($zaobrazku=="center"):
            $sestavenyobr="<div align=\"center\"><img src=\"".$pole_obrazek["obr_poloha"]."\" width=\"".$pole_obrazek["obr_width"]."\" height=\"".$pole_obrazek["obr_height"]."\" alt=\"".$pole_obrazek["nazev"]."\" title=\"".$pole_obrazek["nazev"]."\" /></div>";
          else:
            $sestavenyobr="<img src=\"".$pole_obrazek["obr_poloha"]."\" align=\"".$zaobrazku."\" width=\"".$pole_obrazek["obr_width"]."\" height=\"".$pole_obrazek["obr_height"]."\" alt=\"".$pole_obrazek["nazev"]."\" title=\"".$pole_obrazek["nazev"]."\" />";
          endif;
        endif;
        $text=str_replace($znacka,$sestavenyobr,$text);
      else:
        $text=str_replace($znacka,"<!-- obrazek id ".$idobrazku." nenalezen -->",$text);
      endif;
      // test na existenci dalsi znacky
      $pozice=strpos($text,"<obrazek");
      if ($pozice==0): $rotuj=0; endif; // konec kompilace znacek
    endwhile;
  endif;
 endif;

 return $text;
 }

 function NactiClanek($link = 0)
 {
 // inic.
 $omezenidotazu="";
 $dnesnidatum=Date("Y-m-d H:i:s");
 // kontrola vydani (publikovani) clanku; test na visible
 if ($this->vydani==1):
   $omezenidotazu.=" and visible='1'";
 endif;
 // test na stari clanku; datum publikovani clanku musi byt starsi nebo shodne s aktualnim datem
 if ($this->kontroladata==1):
   $omezenidotazu.=" and datum<='".$dnesnidatum."'";
 endif;
 // aplikace omezeni zobrazeni dle data
 if ($this->testplat==1):
   $omezenidotazu.=" and datum_pl>'".$dnesnidatum."'";
 endif;
 if ($link!=0): // kdyz existuje link
   // sestaveni dotazu
   $select="idc,link,titulek,uvod,text,tema,date_format(datum,'%d. %m. %Y') as vyslden,autor,kom,visit,visible,zdroj,skupina_cl,znacky,typ_clanku,sablona";
   // dotaz
   $this->dotazclanek=mysql_query("select ".$select." from ".$GLOBALS["rspredpona"]."clanky where link='".$link."'".$omezenidotazu,$GLOBALS["dbspojeni"]);
   $this->pocetclanku=mysql_num_rows($this->dotazclanek);
   if ($this->pocetclanku==1):
     // vse OK
     $this->clanek=mysql_fetch_assoc($this->dotazclanek);
     $this->aktpozice=0;
     $this->aktivni=1; // trida je aktivni
   else:
     // chyba
     $this->aktivni=0; // trida je v neaktivnim stavu
   endif;
 endif;

 return $this->aktivni;
 }

 function NactiClanky($mnozstvi = 0, $od = 0)
 {
 // prednacteni pomocnych poli
 $this->NactiAutory();
 $this->NactiTemata();
 $this->NactiSab();

 // inic.
 $omezenidotazu="";
 $spojka="where";
 $dnesnidatum=Date("Y-m-d H:i:s");
 // kontrola vydani (publikovani) clanku; test na visible
 if ($this->vydani==1):
   $omezenidotazu.=" ".$spojka." visible='1'";
   $spojka="and";
 endif;
 // test na stari clanku; datum publikovani clanku musi byt starsi nebo shodne s aktualnim datem
 if ($this->kontroladata==1):
   $omezenidotazu.=" ".$spojka." datum<='".$dnesnidatum."'";
   $spojka="and";
 endif;
 // aplikace omezeni zobrazeni dle data
 if ($this->testplat==1):
   $omezenidotazu.=" ".$spojka." datum_pl>'".$dnesnidatum."'";
   $spojka="and";
 endif;
 if ($mnozstvi>0): // kdyz je definovano mnoz. cl.
   // sestaveni dotazu
   $select="idc,link,titulek,uvod,text,tema,date_format(datum,'%d. %m. %Y') as vyslden,autor,kom,visit,visible,zdroj,skupina_cl,znacky,typ_clanku,sablona";
   // dotaz
   $this->dotazclanek=mysql_query("select ".$select." from ".$GLOBALS["rspredpona"]."clanky".$omezenidotazu." order by priority desc,datum desc limit ".$od.",".$mnozstvi,$GLOBALS["dbspojeni"]);
   $this->pocetclanku=mysql_num_rows($this->dotazclanek);
   if ($this->pocetclanku>0):
     // vse OK
     $this->clanek=mysql_fetch_assoc($this->dotazclanek);
     $this->aktpozice=0;
     $this->aktivni=1; // trida je aktivni
   else:
     // chyba
     $this->aktivni=0; // trida je v neaktivnim stavu
   endif;
 endif;

 return $this->aktivni;
 }

 function NactiZdrojCla($id_zdroj = 0)
 {
 if (is_resource($id_zdroj)):
   // prednacteni pomocnych poli
   $this->NactiAutory();
   $this->NactiTemata();
   $this->NactiSab();

   $this->dotazclanek=$id_zdroj;
   $this->pocetclanku=mysql_num_rows($this->dotazclanek);
   if ($this->pocetclanku>0):
     // vse OK
     $this->clanek=mysql_fetch_assoc($this->dotazclanek);
     $this->aktpozice=0;
     $this->aktivni=1; // trida je aktivni
   else:
     // chyba
     $this->aktivni=0; // trida je v neaktivnim stavu
   endif;
 endif;
 }

 function DalsiRadek()
 {
 if ($this->aktivni==1&&($this->aktpozice+1)<$this->pocetclanku): // pokud je trida aktivni a je fyzicky mozne prejit na dalsi existujici radek
   $this->aktpozice++;
   if (mysql_data_seek($this->dotazclanek,$this->aktpozice)):
     // nacteni dalsiho radku dat
     $this->clanek=mysql_fetch_assoc($this->dotazclanek);
   else:
     // chyba - zpet na puvodni hodnotu
     $this->aktpozice--;
   endif;
 endif;
 }

 function CelkemClanku()
 {
 // inic.
 $omezenidotazu="";
 $spojka="where";
 $dnesnidatum=Date("Y-m-d H:i:s");
 // kontrola vydani (publikovani) clanku; test na visible
 if ($this->vydani==1):
   $omezenidotazu.=" ".$spojka." visible='1'";
   $spojka="and";
 endif;
 // test na stari clanku; datum publikovani clanku musi byt starsi nebo shodne s aktualnim datem
 if ($this->kontroladata==1):
   $omezenidotazu.=" ".$spojka." datum<='".$dnesnidatum."'";
   $spojka="and";
 endif;
 // aplikace omezeni zobrazeni dle data
 if ($this->testplat==1):
   $omezenidotazu.=" ".$spojka." datum_pl>'".$dnesnidatum."'";
   $spojka="and";
 endif;

 // dotaz
 $dotazcelkem=mysql_query("select count(idc) as pocet from ".$GLOBALS["rspredpona"]."clanky".$omezenidotazu,$GLOBALS["dbspojeni"]);
 if (mysql_num_rows($dotazcelkem)==1):
   // vse OK
   return mysql_Result($dotazcelkem,0,"pocet");
 else:
   // chyba
   return 0;
 endif;
 }

 function Ukaz($co = "")
 {
 // promenne "uvod" a "text" mohou pri nekterych nastaveni MySQL databaze vyzadovat jeste korekci funkci - stripslashes
 if ($this->aktivni==1): // kdyz je trida aktivni
  switch($co):
    case "pozice": return $this->aktpozice; break;
    case "pocetclanku": return $this->pocetclanku; break;
    case "idc": return $this->clanek["idc"]; break;
    case "link": return $this->clanek["link"]; break;
    case "titulek": return $this->clanek["titulek"]; break;
    case "uvod":
         if ($this->clanek["znacky"]==1): // kdyz jsou povoleny phpRS znacky
           return $this->Dekoduj($this->clanek["uvod"]);
         else:
           return $this->clanek["uvod"];
         endif;
         break;
    case "text":
         if ($this->clanek["znacky"]==1): // kdyz jsou povoleny phpRS znacky
           return $this->Dekoduj($this->clanek["text"]);
         else:
           return $this->clanek["text"];
         endif;
         break;
    case "tema_id": return $this->clanek["tema"]; break;
    case "tema_jm": return $this->ZjistiTema($this->clanek["tema"],"jm"); break;
    case "tema_obr": return $this->ZjistiTema($this->clanek["tema"],"obr"); break;
    case "datum": return $this->clanek["vyslden"]; break;
    case "autor_id": return $this->clanek["autor"]; break;
    case "autor_jm": return $this->ZjistiAutora($this->clanek["autor"],"jm"); break;
    case "autor_mail": return "mailto:".$this->ZjistiAutora($this->clanek["autor"],"mail"); break;
    case "autor_jen_mail": return $this->ZjistiAutora($this->clanek["autor"],"mail"); break;
    case "autor_im": return $this->ZjistiAutora($this->clanek["autor"],"im"); break;
    case "pocet_kom": return $this->clanek["kom"]; break;
    case "visit": return $this->clanek["visit"]; break;
    case "visit_plus": return ($this->clanek["visit"]+1); break;
    case "visible": return $this->clanek["visible"]; break;
    case "zdroj": return $this->clanek["zdroj"]; break;
    case "skupina": return $this->clanek["skupina_cl"]; break;
    case "znacky": return $this->clanek["znacky"]; break;
    case "typ_clanku": return $this->clanek["typ_clanku"]; break; // 1 - standardni, 2 - kratky
    case "sablona": return $this->ZjistiSab($this->clanek["sablona"]); break;
    default: return ""; // neznamy dotaz
  endswitch;
 endif;
 }
}
?>