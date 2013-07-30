<?

######################################################################
# phpRS Admin Standard library 1.0.6
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

/*
  -- function --
  KorekceHTML($text)
  KorekceNadpisu($str)
  JeToMSIE()
  OverDatum($str)
  MyDnesniDatum()
  MyDatetimeToDate($mysql_datum)
  MyDateToDate($datum = "")
  MyDateTimeToDateTime
  Me($velikost = 0,$sirkaintervalu = 1)
  GenerujSeznam($pocatecnihodnota = 0)
  GenerujSeznamSCestou($pocatecnihodnota = 0)
  TestNaAdresu($mail = "")
  TestNaNic($str = "")
  TestAnoNe($vstup)
  OptAutori($hledam = "")

  -- class --
  SezAutori()
*/

// ====================== FUNCTION

function KorekceHTML($text)
{
// tento radek umoznuje spravne zobrazit v editacnim poli vsechny zvlastni znaky zapsane jako &X;
return str_replace('&','&amp;',$text);
}

function KorekceNadpisu($str)
{
// tento radek nahrazuje uvozovky za - &quot;
return str_replace('"','&quot;',$str);
}

function JeToMSIE()
{
// test na typ prohlizece
$teststr="test".$_ENV["HTTP_USER_AGENT"];
return strpos($teststr,"MSIE"); // 1 = je to MSIE, 0 = neni to MSIE
}

function OverDatum($str)
{
// overeni platnosti data; vstup MySQL format
list($datum,$hodiny)=explode(" ",trim($str)); // dekompilace datumu
list($rok,$mes,$den)=explode("-",$datum);
list($hod,$min,$sek)=explode(":",$hodiny);
// sestaveni overeneho datumu
return date("Y-m-d H:i:s",mktime($hod,$min,$sek,$mes,$den,$rok));
}

function MyDnesniDatum()
{
// generuji dnesni datum i s casem v MySQL formatu
return date("Y-m-d H:i:s");
}

function MyDatetimeToDate($mysql_datum)
{
// preved MySQL datetime typ do formy bezneho teckou oddeleneho datumu
$rozlozenedatum=explode(" ",trim($mysql_datum)); // [0] - datum, [1] - cas
$vysledek=explode("-",$rozlozenedatum[0]);
return $vysledek[2].".".$vysledek[1].".".$vysledek[0]; // dd.mm.rrrr
}

function MyDateToDate($datum = "")
{
$rozloz=explode("-",$datum);
return $rozloz[2].".".$rozloz[1].".".$rozloz[0]; // vysledny format DD.MM.RRRR
}

function MyDateTimeToDateTime($udaj = "")
{
list($datum,$cas)=explode(" ",$udaj);
$rozloz=explode("-",$datum);
return $rozloz[2].".".$rozloz[1].".".$rozloz[0]." ".$cas; // vysledny format DD.MM.RRRR HH:MM:SS
}

function Me($velikost = 0,$sirkaintervalu = 1)
{
// generator pevne mezery
$vysledek='';
if ($velikost>0&&$sirkaintervalu>0):
  $mezera=str_repeat("&nbsp;",$sirkaintervalu);
  $vysledek=str_repeat($mezera,$velikost);
endif;
return $vysledek;
}

function GenerujSeznam($pocatecnihodnota = 0)
{
// generuje a tridi pole hierarchicky na sobe zavislych rubrik
$dotazsez=mysql_query("select idt,nazev,id_predka from ".$GLOBALS["rspredpona"]."topic order by level,nazev",$GLOBALS["dbspojeni"]);
$pocetsez=mysql_num_rows($dotazsez);

for ($pom=0;$pom<$pocetsez;$pom++):
  $pole_data = mysql_fetch_assoc($dotazsez);
  // pole informaci
  $vstdata[$pom][0]=$pole_data["idt"];       // id
  $vstdata[$pom][1]=$pole_data["nazev"];     // nazev polozky
  $vstdata[$pom][2]=$pole_data["id_predka"]; // id rodice
  $vstdata[$pom][3]=0;                       // prepinace pouzito pole
endfor;

if ($pocetsez>0): $trideni=1; else: $trideni=0; endif;

$polehist[0]=$pocatecnihodnota; // historie prohledavani
$polex=0; // poloha v poly historie prohledavani

$vysledekcislo=0; // akt. volna posledni pozice ve vysledkovem poli

while ($trideni==1):
  $nasel=0; // 0 = prvek nenalezen, 1 = prvek nalezen

  for ($pom=0;$pom<$pocetsez;$pom++):
    if ($vstdata[$pom][3]==0): // kdyz nebylo akt. radek jeste pouzit
      if ($vstdata[$pom][2]==$polehist[$polex]): // kdyz nalezi hledanemu predku
            // ulozeni vysledku
            $vysledek[$vysledekcislo][0]=$vstdata[$pom][0]; // id prvku
            $vysledek[$vysledekcislo][1]=$vstdata[$pom][1]; // nazev prvku
            $vysledek[$vysledekcislo][2]=$polex; // uroven vnoreni prvku
            // nastaveni dalsich promennych
            $vysledekcislo++; // prechod na dalsi radek ve vysledkovem poli
            $vstdata[$pom][3]=1; // nastaveni prepinace na pouzito
            $polex++; // prechod na vyssi uroven v historii
            $polehist[$polex]=$vstdata[$pom][0];
            $nasel=1;
            break;
      endif;
    endif;
  endfor;

  if ($nasel==0): // kdyz nebyl v celem poli nalezen zadny odpovidajici prvek
    if ($polehist[$polex]==$pocatecnihodnota):
      // vysledek hledani na zakladni urovni, ktera byla stanovena na zacatku, je prazdny -> neexistuje zadna dalsi vetev
      $trideni=0;
    else:
      $polex--; // prechod na nizsi uroven v historii
    endif;
  endif;
endwhile;

/*
   $vysledek[X][0] - id prkvu
               [1] - nazev prvku
               [2] - cislo urovne
*/
if ($pocetsez>0):
  return $vysledek;
else:
  return 0;
endif;
}

function GenerujSeznamSCestou($pocatecnihodnota = 0)
{
// generuje a tridi pole hierarchicky na sobe zavislych rubrik; vystup obsahuje uplnou cestu k jednotlivym rubrikam
$dotazsez=mysql_query("select idt,nazev,id_predka from ".$GLOBALS["rspredpona"]."topic order by level,nazev",$GLOBALS["dbspojeni"]);
$pocetsez=mysql_num_rows($dotazsez);

for ($pom=0;$pom<$pocetsez;$pom++):
  $pole_data = mysql_fetch_assoc($dotazsez);
  // pole informaci
  $vstdata[$pom][0]=$pole_data["idt"];       // id
  $vstdata[$pom][1]=$pole_data["nazev"];     // nazev polozky
  $vstdata[$pom][2]=$pole_data["id_predka"]; // id rodice
  $vstdata[$pom][3]=0;                       // prepinace pouzito pole
endfor;

if ($pocetsez>0): $trideni=1; else: $trideni=0; endif;

$polehist[0]=$pocatecnihodnota; // historie prohledavani
$polecesta[0]="";
$polex=0; // poloha v poly historie prohledavani

$vysledekcislo=0; // akt. volna posledni pozice ve vysledkovem poli

while ($trideni==1):
  $nasel=0; // 0 = prvek nenalezen, 1 = prvek nalezen

  for ($pom=0;$pom<$pocetsez;$pom++):
    if ($vstdata[$pom][3]==0): // kdyz nebylo akt. radek jeste pouzit
      if ($vstdata[$pom][2]==$polehist[$polex]): // kdyz nalezi hledanemu predku
            // ulozeni vysledku
            $vysledek[$vysledekcislo][0]=$vstdata[$pom][0]; // id prvku
            $vysledek[$vysledekcislo][1]=$polecesta[$polex].$vstdata[$pom][1]; // nazev prvku
            $vysledek[$vysledekcislo][2]=$polex; // uroven vnoreni prvku
            // nastaveni dalsich promennych
            $vysledekcislo++; // prechod na dalsi radek ve vysledkovem poli
            $vstdata[$pom][3]=1; // nastaveni prepinace na pouzito
            $polex++; // prechod na vyssi uroven v historii
            $polehist[$polex]=$vstdata[$pom][0];
            $polecesta[$polex]=$polecesta[$polex-1].$vstdata[$pom][1]." - ";
            $nasel=1;
            break;
      endif;
    endif;
  endfor;

  if ($nasel==0): // kdyz nebyl v celem poli nalezen zadny odpovidajici prvek
    if ($polehist[$polex]==$pocatecnihodnota):
      // vysledek hledani na zakladni urovni, ktera byla stanovena na zacatku, je prazdny -> neexistuje zadna dalsi vetev
      $trideni=0;
    else:
      $polex--; // prechod na nizsi uroven v historii
    endif;
  endif;
endwhile;

/*
   $vysledek[X][0] - id prkvu
               [1] - nazev prvku
               [2] - cislo urovne
*/
if ($pocetsez>0):
  return $vysledek;
else:
  return 0;
endif;
}

function TestNaAdresu($mail = "")
{
// tato funkce testuje platnost zadaneho e-mailu
if (ereg('^[_a-zA-Z0-9\.\-]+@[_a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,4}$',$mail)):
  return 1; // spravna struktura
else:
  return 0; // chybna struktura
endif;
}

function TestNaNic($str = "")
{
// v pripade, ze je vstupem prazdna promenna, tak je na vystupu vracena tvrda mezera
if ($str==""):
  return "&nbsp;";
else:
  return $str;
endif;
}

function TestAnoNe($vstup)
{
// tato fce prevadi logicky stav vstupni promenne na retezcove vyjadreni
switch ($vstup):
  case "0": return RS_TL_NE;  // "Ne";
  case "1": return RS_TL_ANO; //"Ano";
  default: return "chyba";
endswitch;
}

function OptAutori($hledam = '', $omezujici_list = '')
{
$str='';
$nalezl=0;

if (empty($omezujici_list)):
  // vsichni uziatele
  $dotaz="select idu,user from ".$GLOBALS["rspredpona"]."user order by user";
else:
  // uzivatele omezeni seznamem id
  $dotaz="select idu,user from ".$GLOBALS["rspredpona"]."user where idu in (".$omezujici_list.") order by user";
endif;

$dotazusr=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetusr=mysql_num_rows($dotazusr);

if ($pocetusr==0):
  $str.="<option value=\"0\">Prozatím nebyl definován žádný autor!</option>";
else:
  for ($pom=0;$pom<$pocetusr;$pom++):
    $str.="<option value=\"".mysql_Result($dotazusr,$pom,"idu")."\"";
    if ($hledam==mysql_Result($dotazusr,$pom,"idu")): $str.=" selected"; $nalezl=1; endif;
    $str.=">".mysql_Result($dotazusr,$pom,"user")."</option>\n";;
  endfor;
  // test na vysledek
  if ($nalezl==0&&$hledam!=""):
    $str.="<option value=\"".$hledam."\" selected>Systém nemůže identifikovat autora!</option>";
  endif;
endif;

return $str;
}

// ====================== CLASS

class SezAutori
{
var $pole_autori;

 function SezAutori()
 {
 $this->NactiAut();
 }

 function NactiAut()
 {
 $dotazusr=mysql_query("select idu,user,jmeno from ".$GLOBALS["rspredpona"]."user order by user",$GLOBALS["dbspojeni"]);
 $pocetusr=mysql_num_rows($dotazusr);

 while ($pole_data = mysql_fetch_assoc($dotazusr)):
   $this->pole_autori[$pole_data["idu"]][0]=$pole_data["user"];
   $this->pole_autori[$pole_data["idu"]][1]=$pole_data["jmeno"];
 endwhile;
 }

 function UkazUser($id = 0)
 {
 if (isset($this->pole_autori[$id])):
   return $this->pole_autori[$id][0];
 else:
   return "Systém nemůže identifikovat autora!";
 endif;
 }

 function UkazJmeno($id = 0)
 {
 if (isset($this->pole_autori[$id])):
   return $this->pole_autori[$id][1];
 else:
   return "Systém nemůže identifikovat autora!";
 endif;
 }
}

?>
