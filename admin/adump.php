<?

######################################################################
# phpRS Administration Engine - Dump section 1.1.8
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: *

/*
  Tento soubor zajistuje provoz zalozniho subsystemu.
*/

if ($Uzivatel->StavSession!=1):
  echo "<html><body><div align=\"center\">Tento soubor neni urcen k vnejsimu spousteni!</div></body></html>";
  exit();
endif;

// ---[rozcestnik]------------------------------------------------------------------
switch($GLOBALS['akce']):
     // zaloha DB
     case "ShowDump": AdminMenu();
          echo "<h2 align=\"center\">".RS_DBS_ROZ_EXPORT_DB."</h2><p align=\"center\">";
          ShowDump();
          break;
     case "SaveDump":
          if ($GLOBALS["zobrazhlavicku"]==1):
            AdminMenu();
            echo "<h2 align=\"center\">".RS_DBS_ROZ_EXPORT_DB."</h2><p align=\"center\">";
          endif;
          SaveDump();
          break;
     case "ImportDump": AdminMenu();
          echo "<h2 align=\"center\">".RS_DBS_ROZ_IMPORT_DB."</h2><p align=\"center\">";
          ImportDump();
          break;
endswitch;
if ($GLOBALS["zobrazhlavicku"]==1):
  echo "</p>\n"; // zakonceni P tagu
endif;

// ---[pomocne fce]-----------------------------------------------------------------

function Struktura($jmenotab = '')
{
$vysledek=''; // inic.
$novy_radek="\r\n";

if (!empty($jmenotab)): // kdyz obsahuje jmeno tabulky
  $dotazstr=mysql_query("show columns from ".$jmenotab." from ".$GLOBALS["dbname"],$GLOBALS["dbspojeni"]);
  $pocetstr=mysql_num_rows($dotazstr);
  $pocetslp=mysql_num_fields($dotazstr);
  // "drop table"
  $vysledek.='drop table if exists '.$jmenotab.';'.$novy_radek.$novy_radek;
  // zacatek "create table"
  $vysledek.='create table '.$jmenotab.$novy_radek.'('.$novy_radek;
  for($pom=0;$pom<$pocetstr;$pom++):
    // inic.
    $prfield='';
    $prtype='';
    $prnull='';
    $prkey='';
    $prdefault='';
    $prextra='';
    // nacteni atributu tabulky
    for($p1=0;$p1<$pocetslp;$p1++):
      $jmenoradku=mysql_field_name($dotazstr,$p1);
      switch($jmenoradku):
        case 'Field': $prfield=mysql_Result($dotazstr,$pom,$jmenoradku); break;
        case 'Type': $prtype=mysql_Result($dotazstr,$pom,$jmenoradku); break;
        case 'Null': $prnull=mysql_Result($dotazstr,$pom,$jmenoradku); break;
        case 'Key': $prkey=mysql_Result($dotazstr,$pom,$jmenoradku); break;
        case 'Default': $prdefault=mysql_Result($dotazstr,$pom,$jmenoradku); break;
        case 'Extra': $prextra=mysql_Result($dotazstr,$pom,$jmenoradku); break;
      endswitch;
    endfor;
    // sestaveni radku
    $vysledek.=$prfield.' '.$prtype;
    if (strtoupper($prnull)!="YES"): $vysledek.=" not null"; endif;
    if (strtoupper($prdefault)!="NULL"): $vysledek.=" default '".$prdefault."'"; endif;
    if (strtoupper($prextra)=="AUTO_INCREMENT"): $vysledek.=" auto_increment"; endif;
    if (strtoupper($prkey)=="PRI"): $vysledek.=" primary key"; endif;
    if (($pom+1)<$pocetstr): $vysledek.=','.$novy_radek; else: $vysledek.=$novy_radek; endif;
  endfor;
  // konec "create table"
  $vysledek.=');'.$novy_radek;
endif;

return $vysledek;
}

function Hlavicka()
{
// zjisteni prohlize
if (ereg('MSIE ([0-9].[0-9]{1,2})',$_ENV["HTTP_USER_AGENT"])):
  define("RS_PROHLIZEC","IE");
elseif (ereg('Opera(/| )([0-9].[0-9]{1,2})',$_ENV["HTTP_USER_AGENT"])):
  define("RS_PROHLIZEC","OPERA");
elseif (ereg('OmniWeb/([0-9].[0-9]{1,2})',$_ENV["HTTP_USER_AGENT"])):
  define("RS_PROHLIZEC","OMNIWEB");
elseif (ereg('Mozilla/([0-9].[0-9]{1,2})',$_ENV["HTTP_USER_AGENT"])):
  define("RS_PROHLIZEC","MOZILLA");
elseif (ereg('Konqueror/([0-9].[0-9]{1,2})',$_ENV["HTTP_USER_AGENT"])):
  define("RS_PROHLIZEC","KONQUEROR");
else:
  define("RS_PROHLIZEC","DALSI");
endif;

// 'application/octet-stream' is the registered IANA type but MSIE and Opera seems to prefer 'application/octetstream'
if (RS_PROHLIZEC=="IE"||RS_PROHLIZEC=="OPERA"):
 $typsouboru="application/octetstream";
else:
 $typsouboru="application/octet-stream";
endif;

// jmeno souboru
$jmenosouboru=$GLOBALS["dbname"].'_'.Date("Y-m-d").'.sql';

// sestaveni hlavicky
Header("Content-Type: ".$typsouboru);
if (RS_PROHLIZEC=="IE"):
  Header('Content-Disposition: inline; filename="'.$jmenosouboru.'"');
  Header('Expires: 0');
  Header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
  Header('Pragma: public');
else:
  Header('Content-Disposition: attachment; filename="'.$jmenosouboru.'"');
  Header('Expires: 0');
  Header('Pragma: no-cache');
endif;
}

function OdstranHTML($text)
{
$pom=ereg_replace("<","&lt;",$text);
return ereg_replace(">","&gt;",$pom);
}

// ---[hlavni fce]------------------------------------------------------------------

function ShowDump()
{
// sestaveni dotazu na tabulky v DB
//$dotaz="show tables";
$dotaz="show tables from ".$GLOBALS["dbname"];

$dotazobs=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetobs=mysql_num_rows($dotazobs);

if ($pocetobs==0):
  // DB je prazdna
  // CHYBA: Databáze XXX neobsahuje žádné tabulky.
  echo "<p align=\"center\" class=\"txt\">".RS_DBS_SD_ZADNE_TAB_C1." \"".$GLOBALS["dbname"]."\" ".RS_DBS_SD_ZADNE_TAB_C2."</p>\n";
else:
  // DB obsahuje tabulky
  $nazev_prvni_pole=mysql_field_name($dotazobs,0);
  $seznam_tabulek='';
  for ($pom=0;$pom<$pocetobs;$pom++):
    $seznam_tabulek.="<option value=\"".mysql_Result($dotazobs,$pom,$nazev_prvni_pole)."\">".mysql_Result($dotazobs,$pom,$nazev_prvni_pole)."</option>\n";
  endfor;

  // formular pro export
  echo "<form action=\"admin.php\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_DBS_SD_CO_ZALOHOVAT."</b></td>
<td align=\"left\"><input type=\"radio\" name=\"prtyp\" value=\"1\" checked />".RS_DBS_SD_JEN_DATA."<br />
<input type=\"radio\" name=\"prtyp\" value=\"2\" />".RS_DBS_SD_JEN_DEF."<br />
<input type=\"radio\" name=\"prtyp\" value=\"3\" />".RS_DBS_SD_DATA_A_DEF."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_DBS_SD_ZVOLTE_TAB."</b></td>
<td align=\"left\"><select name=\"prtabulky[]\" size=\"10\" multiple>".$seznam_tabulek."</select></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_DBS_SD_JAK_ZALOHOVAT."</b></td>
<td align=\"left\"><input type=\"radio\" name=\"zobrazhlavicku\" value=\"1\" />".RS_DBS_SD_OBRAZ."<br />
<input type=\"radio\" name=\"zobrazhlavicku\" value=\"0\" checked />".RS_DBS_SD_SOUBOR."</td></tr>
</table>
<p align=\"center\"><input type=\"submit\" value=\" ".RS_DBS_SD_TL_ZALOHA." \" class=\"tl\" /> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\" /></p>
<input type=\"hidden\" name=\"akce\" value=\"SaveDump\" /><input type=\"hidden\" name=\"modul\" value=\"dump\" />
</form>\n";
endif;

// oddelovaci pruh
echo "<hr width=\"600\" />\n";
echo "<p></p>\n";

// formular pro import
echo "<h3 align=\"center\">".RS_DBS_SD_NAPIS_IMPORT."</h3>
<form action=\"admin.php\" method=\"post\" enctype=\"multipart/form-data\">
<p align=\"center\" class=\"txt\">".RS_DBS_SD_ZDROJ_SB." <input type=\"file\" name=\"prsoubor\" class=\"textpole\" /></p>
<p align=\"center\"><input type=\"submit\" value=\" ".RS_DBS_SD_TL_IMPORT." \" class=\"tl\" /></p>
<input type=\"hidden\" name=\"akce\" value=\"ImportDump\" /><input type=\"hidden\" name=\"modul\" value=\"dump\" />
</form>
<p></p>\n";
}

function SaveDump()
{
$chyba=0; // inic. chyb. prom.
$novy_radek="\r\n";

if (!isset($GLOBALS["prtyp"])):
  echo "<p align=\"center\" class=\"txt\">Error D1: ".RS_DBS_SD_ERR_TYP_ZALOHY."</p>\n";
  $chyba=1;
endif;
if (!isset($GLOBALS["prtabulky"])||count($GLOBALS["prtabulky"])<=0):
  echo "<p align=\"center\" class=\"txt\">Error D2: ".RS_DBS_SD_ERR_PRAZNY_VSTUP."</p>\n";
  $chyba=1;
endif;

// test na chybu
if ($chyba==0):
  // lze zacit zalohovaci proces
  if ($GLOBALS["zobrazhlavicku"]==0): Hlavicka(); endif; // vystup do souboru

  $pocettabulek=count($GLOBALS["prtabulky"]);
  if ($GLOBALS["zobrazhlavicku"]==1): echo "<p><pre>"; endif; // zobr. na monitoru
  echo "# Zaloha phpRS databaze".$novy_radek."# ze dne: ".Date("d.m.Y, H:i:s").$novy_radek.$novy_radek;
  for($pom=0;$pom<$pocettabulek;$pom++):
    // struktura
    if (($GLOBALS["prtyp"]==2)||($GLOBALS["prtyp"]==3)):
      echo "# --- Struktura tabulky ".$GLOBALS["prtabulky"][$pom]." ---".$novy_radek.$novy_radek;
      echo Struktura($GLOBALS["prtabulky"][$pom]).$novy_radek;
    endif;
    // konec - struktura
    // data
    if (($GLOBALS["prtyp"]==1)||($GLOBALS["prtyp"]==3)):
      echo "# --- Data z tabulky ".$GLOBALS["prtabulky"][$pom]." ---".$novy_radek.$novy_radek;
      $dotazpol=mysql_query("select * from ".$GLOBALS["prtabulky"][$pom],$GLOBALS["dbspojeni"]);
      $pocetpol=mysql_num_rows($dotazpol);
      $pocetslppol=mysql_num_fields($dotazpol);
      if ($GLOBALS["zobrazhlavicku"]==0):
        // vypis dat do souboru
        for($p1=0;$p1<$pocetpol;$p1++):
          $prdata=mysql_fetch_row($dotazpol);
          echo "insert into ".$GLOBALS["prtabulky"][$pom]." values(";
          for($p2=0;$p2<$pocetslppol;$p2++):
            if ($p2!=0): echo ","; endif;
            echo "'".addslashes($prdata[$p2])."'";
          endfor;
          echo ");".$novy_radek;
        endfor;
      else:
        // vypis dat na obrazovku
        for($p1=0;$p1<$pocetpol;$p1++):
          $prdata=mysql_fetch_row($dotazpol);
          echo "insert into ".$GLOBALS["prtabulky"][$pom]." values(";
          for($p2=0;$p2<$pocetslppol;$p2++):
            if ($p2!=0): echo ","; endif;
            echo "'".addslashes(OdstranHTML($prdata[$p2]))."'";
          endfor;
          echo ");".$novy_radek;
        endfor;
      endif;
      echo $novy_radek; // zakoncovaci mezera / radek
    endif;
    // konec - data
  endfor;
  if ($GLOBALS["zobrazhlavicku"]==1): echo "</pre></p>\n"; endif; // zobr. na monitoru
endif;

// navrat
if ($GLOBALS["zobrazhlavicku"]==1): // zobr. na monitoru
  echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowDump&amp;modul=dump\">".RS_DBS_SD_ZPET."</a></p>\n";
  echo "<p></p>\n";
endif;
}

function ImportDump()
{
/*
$_FILES['prsoubor']['name'] - originalni nazev souboru na klientskem pocitaci
$_FILES['prsoubor']['type'] - MIME typ souboru, pokud prohlizec tuto informaci poskytuje (napr. "image/gif")
$_FILES['prsoubor']['size'] - velikost uploadovaneho souboru v bytech
$_FILES['prsoubor']['tmp_name'] - docasny nazev souboru, pod nimz je uploadovany soubor ulozen na serveru
*/

if ($_FILES['prsoubor']['size']==0):
  // CHYBA: Pozor, importovaný soubor má nulovou délku!
  echo "<p align=\"center\" class=\"txt\">Error D3! ".RS_DBS_SD_ERR_CHYBI_SOUBOR."</p>\n";
else: // start zpracovani SQL skriptu
  // test na POST metodu pri zaslani souboru
  if (is_uploaded_file($_FILES['prsoubor']['tmp_name'])):

     // nacteni souboru
     $fd = fopen ($_FILES['prsoubor']['tmp_name'], "r");
     $sqlskript = fread ($fd,$_FILES['prsoubor']['size']);
     fclose ($fd);

     // specialni priprava SQL skriptu
     $sqlskript=trim($sqlskript); // odstraneni zbyt. znaku na zacatku a konci ret.
     $delkaskriptu=strlen($sqlskript);

     // inic.
     $otevrenyret=0;
     $prikaz="";
     // rozbor SQL skriptu
     for($pom=0;$pom<$delkaskriptu;$pom++):
        $znak=$sqlskript[$pom];
        if (($otevrenyret==0)&&($znak==";")):
          // ukoncovaci strednik se nepridava k prikazu.
          $poleprikazu[]=$prikaz; // ulozeni prikazu
          $prikaz=""; // vynulovani ret.
        else:
          if (($znak=="'")||($znak=='"')||($znak=="´")||($znak=="`")):
            if ($sqlskript[$pom-1]!="\\"): // test na predchazejici znak
              if ($otevrenyret==0): $otevrenyret=1; else: $otevrenyret=0; endif; // otevreni/zavreni ret.
            endif;
          endif;
          $prikaz.=$znak; // pridani znaku k ret.
        endif;
     endfor;
     // inic.
     $pocetprikazu=count($poleprikazu);
     $okprikazy=0;
     $errprikazy=0;
     // provedeni prikazu
     for ($pom=0;$pom<$pocetprikazu;$pom++):
       @$error=mysql_query($poleprikazu[$pom],$GLOBALS["dbspojeni"]);
       if ($error==0):
        echo "<p align=\"center\" class=\"txt\">".mysql_errno().": ".mysql_error()."</p>\n";
        $errprikazy++;
       else:
        $okprikazy++;
       endif;
     endfor;
     // vysledkova tabulka
     echo "<p></p>\n";
     echo "<table border=\"0\" align=\"center\">\n";
     echo "<tr class=\"txt\"><td>".RS_DBS_SD_STAV_POCET_ZNAKU.":</td><td align=\"right\">".$delkaskriptu."</td></tr>\n";
     echo "<tr class=\"txt\"><td>".RS_DBS_SD_STAV_POCET_SQL.":</td><td align=\"right\">".$pocetprikazu."</td></tr>\n";
     echo "<tr class=\"txt\"><td>".RS_DBS_SD_STAV_POCET_OK_SQL.":</td><td align=\"right\">".$okprikazy."</td></tr>\n";
     echo "<tr class=\"txt\"><td>".RS_DBS_SD_STAV_POCET_CHYB_SQL.":</td><td align=\"right\">".$errprikazy."</td></tr>\n";
     echo "</table>\n";
  else:
    echo "<p align=\"center\" class=\"txt\">Error D4! ".RS_DBS_SD_ERR_NEKOREKTNI_VSTUP."</p>\n";
  endif; // konec POST testu
endif; // konec zpracovani SQL skriptu

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowDump&amp;modul=dump\">".RS_DBS_SD_ZPET."</a></p>\n<p></p>\n";
}
?>
