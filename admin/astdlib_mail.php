<?

######################################################################
# phpRS Admin Standard Mail library 1.0.2
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

/*
  -- class --
  CPosta
*/

// ====================== CLASS

// trida CPosta
class CPosta
{
var $hlavicka; // hlavicka mailu
var $obsah; // obsah mailu
var $adresat; // adresat mailu
var $skryty_adresat; // skyty adresat
var $predmet; // predmet mailu
var $odesilatel_mail; // odesilatel mailu
var $odesilatel_txt; // odesilatel text
var $seznam_ctenaru; // seznam ctenaru
var $stav_seznam_ctenaru; // stav seznamu ctenaru

 /*
   CPosta()
   Reset()
   Nastav($co = '', $hodnota = '')
   TestNaMailAdr($mail = '')
   NactiCtenare()
   NastavInfoMail();
   win1250_to_ascii($str = '')
   win1250_to_iso88592($str = '')
   Odesilac()
 */

 function CPosta() // konstruktor
 {
 $this->Reset();
 }

 function Reset() // reset internich promenych
 {
 $this->hlavicka='';
 $this->obsah='';
 $this->adresat='';
 $this->skryty_adresat='';
 $this->predmet='';
 $this->odesilatel_mail=$GLOBALS['redakceadr'];
 $this->odesilatel_txt=$GLOBALS['wwwname'];
 $this->seznam_ctenaru='';
 $this->stav_seznam_ctenaru=0;
 }

 function Nastav($co = '', $hodnota = '') // nastaveni promennych
 {
 switch($co):
   case "hlavicka": $this->hlavicka=$hodnota; break;
   case "obsah": $this->obsah=$hodnota; break;
   case "adresat": $this->adresat=$hodnota; break;
   case "skryta_kopie": $this->skryty_adresat=$hodnota; break;
   case "predmet": $this->predmet=$hodnota; break;
   case "odesilatel_mail": $this->odesilatel_mail=$hodnota; break;
   case "odesilatel_txt": $this->odesilatel_txt=$hodnota; break;
 endswitch;
 }

 function TestNaMailAdr($mail = '') // test na platnost zadaneho e-mailu
 {
 if (ereg('^[_a-zA-Z0-9\.\-]+@[_a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,4}$',$mail)):
   return 1; // spravna struktura
 else:
   return 0; // chybna struktura
 endif;
 }

 function NactiCtenare() // nacteni seznamu ctenaru
 {
 // test na aktivnost seznamu ctenaru
 if ($this->stav_seznam_ctenaru==0):
   $dotazmail=mysql_query("select email from ".$GLOBALS["rspredpona"]."ctenari where info='1'",$GLOBALS["dbspojeni"]);
   $pocetmail=mysql_num_rows($dotazmail);

   $pom_str='';
   $pom_spojka='';

   for ($pom=0;$pom<$pocetmail;$pom++):
      $akt_mail=mysql_result($dotazmail,$pom,"email");
      if ($this->TestNaMailAdr($akt_mail)): // test na korektnost adresy
        $pom_str.=$pom_spojka.$akt_mail;
        $pom_spojka=',';
      endif;
   endfor;

   $this->seznam_ctenaru=$pom_str; // ulozeni vysledku
   $this->stav_seznam_ctenaru=1; // stav seznamu ctenaru = true
 endif;
 }

 function NastavInfoMail() // nastaveni defaultniho stavu redakcniho info e-mailu
 {
 $this->NactiCtenare();
 $this->skryty_adresat=$this->seznam_ctenaru; // prednastaveni skryteho adresata
 $this->adresat=$GLOBALS['infoadr']; // prednastaveni adresata
 }

 function win1250_to_ascii($str = '') // prekodovani z Win-1250 do ASCII
 {
 $diak ="ěščřžýáíéťňďúůóöüĚŠČŘŽÝÁÍÉŤŇĎÚŮÓÖÜ";
 $diak.="\x97\x96\x91\x92\x84\x93\x94\xAB\xBB";
 $ascii="escrzyaietnduuoouESCRZYAIETNDUUOOU";
 $ascii.="\x2D\x2D\x27\x27\x22\x22\x22\x22\x22";
 return StrTr($str,$diak,$ascii);
 }

 function win1250_to_iso88592($str = '') // prekodovani z Win-1250 do ISO-8859-2
 {
 return StrTr($str,"\x8A\x8D\x8E\x9A\x9D\x9E","\xA9\xAB\xAE\xB9\xBB\xBE");
 }

 function Odesilac() // odesilac e-mailu
 {
 $chyba=0; // inic. chyba
 $konec_radku_hlavicka="\n";

 // obsah
 $probsah=$this->win1250_to_iso88592($this->obsah);
 $probsah=Base64_Encode($probsah);
 // hlavicka
 $prhlavicka='';
 if ($this->odesilatel_mail!=''): // mail odesilatele
   $prhlavicka .='From: ';
   if ($this->odesilatel_txt!=''): // textovy popis odesilatele
     $prhlavicka .='"'.$this->odesilatel_txt.'" ';
   endif;
   $prhlavicka .='<'.$this->odesilatel_mail.'>'.$konec_radku_hlavicka; // mail odesilatele
 endif;
 if ($this->skryty_adresat!=''): // test na skrytou kopii
   $prhlavicka .='Bcc: '.$this->skryty_adresat.$konec_radku_hlavicka;
 endif;
 $prhlavicka .='MIME-Version: 1.0'.$konec_radku_hlavicka;
 $prhlavicka .='Content-Type: text/plain; charset="iso-8859-2"'.$konec_radku_hlavicka;
 $prhlavicka .='Content-Transfer-Encoding: base64'.$konec_radku_hlavicka;
 // predmet
 if ($this->predmet==''):
   $chyba=1;
 else:
   // zpracovani predmetu
   $pomocny_predmet=$this->win1250_to_iso88592($this->predmet);
   $this->predmet='=?ISO-8859-2?B?'.Base64_Encode($pomocny_predmet).'?=';
 endif;
 // adresar
 if ($this->adresat==''):
   $chyba=1;
 endif;

 // odeslani e-mailu
 if ($chyba==0):
   if (Mail($this->adresat,$this->predmet,$probsah,$prhlavicka)):
     return 1; // vse OK
   else:
     return 0; // chyba
   endif;
 else:
   return 0; // chyba
 endif;
 }
}
?>
