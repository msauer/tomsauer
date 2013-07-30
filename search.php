<?
######################################################################
# phpRS Search 1.6.2
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_user, rs_topic, rs_clanky

/* mozne vstupni promenne:

  $rstext ... prenasi hledany text, povinny vstup, retezec: "all-phpRS-all" umozni kompletni vypis
  $rstema ... omezuje vypis pouze na zadanou rubriku/tema -> vstupem je cislo tematu, default hodnota = nic
  $rsautor ... omezuje vypis pouze na zvoleneho autora -> vstupem je cislo autora, default hodnota = nic
  $rsod + $rsdo ... umoznuje casove omezeni, $rsdo neni povinny, jelikoz search.php jej umi doplnit
  $rskde (titulek,uvod,text,t_slova) ... specifikuje, kde se dany $rstext vyhledava, default nastaveni ukazuje na "text"
               - platne hodnoty: tit, uvd, txt, tsl, vse
  $rskolik + $rskolikata ... umoznuje definovat rozsah vypisu: $rskolik (mnozstvi polozek) a $rskolikata prenasi informaci o pozici
  $rsvztah ... umoznuje definovat vztah mezi vetsim poctem zadanych slov k vyhledavani, default hodnota = OR
               - platne hodnoty: AND, OR
  $rsvelikost ... definuje zpusob vypisu: bud jednoradkovy vypis nebo vypis doplneni o uvodni texty u jednotlivych nalezenych polozek
               - platne hodnoty: jr, uvod
*/

/*
Header("Pragma: no-cache");
Header("Cache-Control: no-cache");
Header("Expires: ".GMDate("D, d M Y H:i:s",Date("U")+3600)." GMT");
*/

define('IN_CODE',true); // inic. ochranne konstanty

include_once("config.php");
include_once("specfce.php");
include_once("myweb.php");
include_once("sl.php");
include_once("trlayout.php");
include_once($adrlayoutu);

function Prohledej($jak='+', $co='', $naco='')
{
if ($jak=='-'):
  $jaktxt='NOT LIKE';
else:
  $jaktxt='LIKE';
endif;

$naco=mysql_escape_string($naco); // bezpecnostni korekce

switch ($co):
  case 'tit': $str="titulek LIKE ('%".$naco."%')"; break;
  case 'uvd': $str="uvod LIKE ('%".$naco."%')"; break;
  case 'txt': $str="text LIKE ('%".$naco."%')"; break;
  case 'tsl': $str="t_slova LIKE ('%".$naco."%')"; break;
  case 'vse': $str="(titulek LIKE ('%".$naco."%') OR uvod LIKE ('%".$naco."%') OR text LIKE ('%".$naco."%'))"; break;
  default: $str='';
endswitch;

return $str; // vraceni prikazu
}

function ZpracujHleStr($vstup='')
{
if (empty($vstup)):
  // vraceni prazdneho vystupu
  return $vysledek[0]='';
else:
  // dekompilace vyhledavaneho retezce - vysledkem je pole hledanych slov a retezcu $slova[]
  $slova=array();
  $p_txt=str_replace("'","\'",$vstup); // zpracovani apostrofu
  $p_txt=str_replace('"',' " ',$p_txt); // dekompilace textu na fraze a slova
  $p_pompole=explode(' ',$p_txt);
  $p_pocet_pompole=count($p_pompole); // pocet prvku v pompole
  $p_uvozovka=0; // 0 = false stav, 1 = true stav
  $p_str_uvozovka='';
  // zpracovani vyhled. retezce
  for ($pom=0;$pom<$p_pocet_pompole;$pom++):
    if (($p_uvozovka==0)&&($p_pompole[$pom]!='"')&&($p_pompole[$pom]!='')): // zapis do pole hled. slov
      $slova[]=$p_pompole[$pom];
    else:
      if (($p_uvozovka==1)&&($p_pompole[$pom]!='"')): // zapis v ramci uvozovek
        $p_str_uvozovka.=' '.$p_pompole[$pom];
      endif;
      if ($p_pompole[$pom]=='"'): // inicializace uvozovek
         if ($p_uvozovka==0): // prepinani mezi uvozovkami
           $p_uvozovka=1; // start vnoreneho retezce
         else:
           if ($p_str_uvozovka!=''): // test na vyprazdneni pom. retezce do pole hled. slov
             $slova[]=trim($p_str_uvozovka);
             $p_str_uvozovka=''; // vynulovani pomoc. promenne
           endif;
           $p_uvozovka=0; // konec vnoreneho retezce
         endif;
      endif;
    endif;
  endfor;
  // test na zbytkovy retezec v $p_str_uvozovka
  if (trim($p_str_uvozovka)!=''): // pom. prom. obsahuje nejaky zbytkovy retezec
    $p_pompole=explode(' ',trim($p_str_uvozovka));
    $p_pocet_pompole=count($p_pompole); // pocet prvku v pompole
    for ($pom=0;$pom<$p_pocet_pompole;$pom++):
      if ($p_pompole[$pom]!=' '): $slova[]=$p_pompole[$pom]; endif;
    endfor;
  endif;
  // vraceni vysledkoveho pole
  return $slova;
endif;
}

// vyhledavani
function Vyhledavani()
{
// nacteni seznamu uzivatelu(autoru) do pole "autori"
$dotazautori=mysql_query("select idu,jmeno,email from ".$GLOBALS["rspredpona"]."user order by idu",$GLOBALS["dbspojeni"]);
$pocetautori=mysql_num_rows($dotazautori);
for ($pom=0;$pom<$pocetautori;$pom++):
  $pole_data_aut=mysql_fetch_assoc($dotazautori);
  $autori[$pole_data_aut["idu"]][0]=$pole_data_aut["jmeno"];
  $autori[$pole_data_aut["idu"]][1]="mailto:".$pole_data_aut["email"];
endfor;

// nacteni seznamu temat do pole "rubriky"
$dotazrubr=mysql_query("select idt,nazev from ".$GLOBALS["rspredpona"]."topic order by idt",$GLOBALS["dbspojeni"]);
$pocetrubr=mysql_num_rows($dotazrubr);
for ($pom=0;$pom<$pocetrubr;$pom++):
  $rubriky[mysql_Result($dotazrubr,$pom,"idt")]=mysql_Result($dotazrubr,$pom,"nazev");
endfor;

// ************* Tvorba dotazu *************
// mozne vstupni promenne: $rstext, $rstema, $rsautor, $rsod + $rsdo, $rskde (titulek,uvod,text,t_slova), $rskolik + $rskolikata, $rsvztah

$GLOBALS["rstext"]=stripslashes(trim($GLOBALS["rstext"])); // priprava hledaneho retezce - odstraneni zbytecnych mezer,tabelatoru,atd. + lomitka u spec. znaku

if ($GLOBALS["rstext"]==''):
 // prazdna promenna $rstext
 $mozneobratky=0;
 $rotace=0;
else:
 // dekompilace vyhledavaneho retezce - vysledkem je pole hledanych slov a retezcu $slova[]
 $slova=ZpracujHleStr($GLOBALS["rstext"]);

 // start sestavovani prikazu
 $obsahpodminky="where";

 // zpracovani pole hledanych slov; kdyz je text "all-phpRS-all" tak se pozaduje uplny vypis
 if ($slova[0]!='all-phpRS-all'):
   $GLOBALS["rsvztah"]=mysql_escape_string($GLOBALS["rsvztah"]);
   $pocet_slova=count($slova);
   // vyhledavani omezene na nektera slova
   $obsahpodminky.=" (";
   for ($pom=0;$pom<$pocet_slova;$pom++):
     if ($pom>0): // vztah se neuvadi u prvniho prubehu
       $obsahpodminky.=" ".$GLOBALS["rsvztah"]." ";
     endif;
     if (SubStr($slova[$pom],0,1)=="-"):
       $obsahpodminky.=Prohledej("-",$GLOBALS["rskde"],SubStr($slova[$pom],1,(StrLen($slova[$pom])-1)));
     else:
       $obsahpodminky.=Prohledej("+",$GLOBALS["rskde"],$slova[$pom]);
     endif;
   endfor;
   $obsahpodminky.=")";
 else:
   $obsahpodminky.=" idc!=0";
 endif;

 // omezeni tematem - $GLOBALS["stromhlmenu"] ... ukazuje miru vnoreni
 if (isset($GLOBALS["rstema"])&&($GLOBALS["rstema"]!="nic")):
   // inic. $pomseznamu
   $pomseznam[0][0]=mysql_escape_string($GLOBALS["rstema"]);
   $pomseznam[0][1]=1;  // umele zapnute rodicovstvi z inic. dotazu
   $poc_pomseznam=1; // celkovy pocet polozek v $pomseznam
   $akt_poz_pomseznam=0; // akt. pozice v $pomseznam
   $str_temata="";
   $spojka_temata="";
   // sestaveni stromu temat
   while($poc_pomseznam>$akt_poz_pomseznam):
     // zapis temata do vysledku
     $str_temata.=$spojka_temata.$pomseznam[$akt_poz_pomseznam][0];
     $spojka_temata=",";
     // test na rodicovstvi
     if ($pomseznam[$akt_poz_pomseznam][1]==1):
       $dotazkapit=mysql_query("select idt,rodic from ".$GLOBALS["rspredpona"]."topic where id_predka='".$pomseznam[$akt_poz_pomseznam][0]."'",$GLOBALS["dbspojeni"]);
       $pocetkapit=mysql_num_rows($dotazkapit);
       // zapis do $pomseznam
       for ($pom=0;$pom<$pocetkapit;$pom++):
         $pomseznam[$poc_pomseznam][0]=mysql_Result($dotazkapit,$pom,"idt");
         $pomseznam[$poc_pomseznam][1]=mysql_Result($dotazkapit,$pom,"rodic");
         $poc_pomseznam++;
       endfor;
     endif;
     // posunuti na dalsi pozici v seznamu
     $akt_poz_pomseznam++;
   endwhile;
   // finalni podminka
   $obsahpodminky.=" AND tema IN (".$str_temata.")";
 endif;

 // omezeni autorem
 if (isset($GLOBALS["rsautor"])&&($GLOBALS["rsautor"]!="nic")):
   $obsahpodminky.=" AND autor='".mysql_escape_string($GLOBALS["rsautor"])."'";
 endif;

 // omezeni casem
 if (isset($GLOBALS["rsod"])&&($GLOBALS["rsod"]!="nic")):
   if (!isset($GLOBALS["rsdo"])): $GLOBALS["rsdo"]=Date("Y-m-d"); endif;
   $obsahpodminky.=" AND datum>='".mysql_escape_string($GLOBALS["rsod"])."' AND datum<='".mysql_escape_string($GLOBALS["rsdo"])."'";
 endif;

 // finalni vyhodnoceni podminky
 if ($obsahpodminky!="where"):
   $obsahpodminky.=" AND";
 endif;

 // ziskani auktualniho podminkoveho data
 $dnesaktdatum=Date("Y-m-d H:i:s");

 // vypocet limitu
 $dotazpocet=mysql_query("SELECT count(idc) as pocet FROM ".$GLOBALS["rspredpona"]."clanky ".$obsahpodminky." visible=1 AND datum<='".$dnesaktdatum."'",$GLOBALS["dbspojeni"]);
 if (mysql_num_rows($dotazpocet)==1):
   $celkemnalezeno=mysql_Result($dotazpocet,0,"pocet");
 else:
   $celkemnalezeno=0;
 endif;
 $mozneobratky=ceil($celkemnalezeno/$GLOBALS["rskolik"]);
 if ($GLOBALS["rskolikata"]==1): $rspocatecni=0; else: $rspocatecni=$GLOBALS["rskolik"]*($GLOBALS["rskolikata"]-1); endif;

 // sestaveni kompletniho dotazu
 if ($GLOBALS['rsvelikost']=='sab'):
   $dotaz="SELECT idc,link,titulek,uvod,text,tema,date_format(datum,'%d. %m. %Y') as vyslden,autor,kom,visit,visible,zdroj,skupina_cl,znacky,typ_clanku,sablona ";
   $dotaz.="FROM ".$GLOBALS["rspredpona"]."clanky ".$obsahpodminky." visible=1 AND datum<='".$dnesaktdatum."' ORDER BY datum desc LIMIT ".$rspocatecni.",".$GLOBALS["rskolik"];
 else:
   $dotaz="SELECT link,titulek,uvod,date_format(datum,'%d.%m.%Y') as vyslden,tema,autor ";
   $dotaz.="FROM ".$GLOBALS["rspredpona"]."clanky ".$obsahpodminky." visible=1 AND datum<='".$dnesaktdatum."' ORDER BY datum desc LIMIT ".$rspocatecni.",".$GLOBALS["rskolik"];
 endif;
 $vvysledek=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
 $rotace=mysql_num_rows($vvysledek);
endif;

if (!is_int($rotace)):
  $rotace=round($rotace);
endif;

// ************* Tvorba vypisu *************
if ($rotace==0): // prazdne vyhl.
  echo "<p align=\"center\" class=\"z\"><strong>".RS_VY_NULL."</strong></p>\n";
else: // exiteje vysledek
  echo "<p align=\"center\" class=\"z\"><strong>".RS_VY_VYSLEDEK_1." ".$rotace." ".RS_VY_VYSLEDEK_2." ".$celkemnalezeno." ".RS_VY_VYSLEDEK_3."</strong></p>\n";
endif;
// sestaveni navigacniho pasu
$navigace='';
if ($mozneobratky>1):
  $navigace.='<p align="center" class="z">|';
  for ($pom=0;$pom<$mozneobratky;$pom++):
    $vysl_strana=$pom+1;
    if ($vysl_strana==$GLOBALS["rskolikata"]): // omezeni akt. vypisove stranky
      $navigace.=($pom*$GLOBALS["rskolik"]).'-'.min(($vysl_strana*$GLOBALS["rskolik"]),$celkemnalezeno).'|';
    else:
      $navigace.='<a href="search.php?rstext='.$GLOBALS["rstext"].'&amp;rsautor='.$GLOBALS["rsautor"].'&amp;rstema='.$GLOBALS["rstema"].'&amp;rskde='.$GLOBALS["rskde"].'&amp;rsvelikost='.$GLOBALS["rsvelikost"].'&amp;rskolik='.$GLOBALS["rskolik"].'&amp;rskolikata='.$vysl_strana.$GLOBALS["prmenulink"].'">'.($pom*$GLOBALS["rskolik"]).'-'.min(($vysl_strana*$GLOBALS["rskolik"]),$celkemnalezeno).'</a>|';
    endif;
  endfor;
  $navigace.="</p>\n";
endif;
// 1. navigacni lista
echo $navigace;
// test na zpusob vypis vysledku vyhledavani
switch ($GLOBALS['rsvelikost']):
  case 'sab':
  // *** vypis pres sablonu ***
  include_once("trclanek.php"); // nacteni tridy clanky

  $GLOBALS["clanek"] = new CClanek();
  $GLOBALS["clanek"]->NactiZdrojCla($vvysledek);

  for ($pom=0;$pom<$GLOBALS["clanek"]->Ukaz("pocetclanku");$pom++):
    // urceni pozadovane varianty sablony
    if ($GLOBALS["clanek"]->Ukaz("typ_clanku")==2): // 1 - standardni, 2 - kratky
      $rs_typ_clanku="kratky"; // urceni pozadovane varianty sablony
    else:
      $rs_typ_clanku="nahled"; // urceni pozadovane varianty sablony
    endif;
    // volani sablony
    if ($GLOBALS["clanek"]->Ukaz("sablona")==""):
      // chybova hlaska: Chyba při zobrazování článku číslo xxxx! Systém nemůže nalézt odpovídající šablonu!
      echo "<p align=\"center\" class=\"z\">".RS_IN_ERR1_1." ".$GLOBALS["clanek"]->Ukaz("link")."! ".RS_IN_ERR1_2."<p>\n";
    else:
      include($GLOBALS["clanek"]->Ukaz("sablona")); // vlozeni sablony; pozor, musi byt povoleno vice-nasobne vlozeni sablony
    endif;
    $GLOBALS["clanek"]->DalsiRadek(); // prechod na dalsi radek
  endfor;
  // *** konec: vypis pres sablonu ***
  break;
  case 'uvod':
  // *** vypis s uvodnim textem ***
  for($pro=0;$pro<$rotace;$pro++):
    $pole_data=mysql_fetch_assoc($vvysledek);
    echo "<div class=\"z\"><strong><a href=\"view.php?cisloclanku=".$pole_data["link"]."\">".$pole_data["titulek"]."</a></strong><br />\n";
    echo "(<i>";
    // kompilace autora
    if (isset($autori[$pole_data["autor"]][0])):
      echo "<a href=\"".$autori[$pole_data["autor"]][1]."\">".$autori[$pole_data["autor"]][0]."</a>, ";
    else:
      echo "<a href=\"".$GLOBALS["redakceadr"]."\">".RS_VY_REDAKCE."</a>, ";
    endif;
    // kompilace tematu
    if (isset($rubriky[$pole_data["tema"]])):
      echo $rubriky[$pole_data["tema"]].", ";
    endif;
    echo $pole_data["vyslden"]."</i>)<br />\n";
    echo $pole_data["uvod"]."</div><br />\n";
  endfor;
  // *** konec: vypis s uvodnim textem ***
  break;
  case 'jr':
  // *** jednoradkovy vypis ***
  echo "<table cellpadding=\"5\" border=\"0\" class=\"z\" align=\"center\">\n";
  if ($rotace>0): // prazdne vyhl.
    echo "<tr class=\"z\"><td align=\"left\"><b>".RS_VY_NAZEV_CLA."</b></td><td align=\"center\"><b>".RS_VY_DATUM_VYD."</b></td><td align=\"center\"><b>".RS_VY_AUTOR."</b></td><td align=\"left\"><b>".RS_VY_TEMA."</b></td></tr>\n";
  endif;
  for($pro=0;$pro<$rotace;$pro++):
    $pole_data=mysql_fetch_assoc($vvysledek);
    echo "<tr class=\"z\"><td align=\"left\"><a href=\"view.php?cisloclanku=".$pole_data["link"]."\">".$pole_data["titulek"]."</a></td>\n";
    echo "<td align=\"center\">".$pole_data["vyslden"]."</td>\n";
    // kompilace autora
    if (isset($autori[$pole_data["autor"]][0])):
      echo "<td align=\"center\"><a href=\"".$autori[$pole_data["autor"]][1]."\">".$autori[$pole_data["autor"]][0]."</a></td>\n";
    else:
      echo "<td align=\"center\"><a href=\"".$GLOBALS["redakceadr"]."\">".RS_VY_REDAKCE."</a></td>\n";
    endif;
    // kompilace tematu
    if (isset($rubriky[$pole_data["tema"]])):
      echo "<td align=\"left\">".$rubriky[$pole_data["tema"]]."</td>";
    else:
      echo "<td align=\"left\">&nbsp;</td>";
    endif;
    echo "</tr>\n";
  endfor;
  echo "</table>\n";
  // *** konec: jednoradkovy vypis ***
  break;
endswitch;
// 2. navigacni lista
echo $navigace;
echo "<p></p>\n";
// ************* konec: Tvorba vypisu *************
}

function VyhlSeznamTemat($pocatecnihodnota = 0)
{
// generuje a tridi pole hierarchicky na sobe zavislych rubrik; vystup obsahuje uplnou cestu k jednotlivym rubrikam
$dotazsez=mysql_query("select idt,nazev,id_predka from ".$GLOBALS["rspredpona"]."topic order by level,nazev",$GLOBALS["dbspojeni"]);
$pocetsez=mysql_num_rows($dotazsez);

for ($pom=0;$pom<$pocetsez;$pom++): // nacteni pole informaci
    $pole_data=mysql_fetch_assoc($dotazsez);
    $vstdata[$pom][0]=$pole_data['idt'];       // id
    $vstdata[$pom][1]=$pole_data['nazev'];     // nazev polozky
    $vstdata[$pom][2]=$pole_data['id_predka']; // id rodice
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

function VyFormular()
{
echo "<p class=\"nadpis\">".RS_VY_NADPIS."</p>
<form action=\"search.php\" method=\"post\">
<table border=\"0\" align=\"center\">
<tr class=\"z\"><td>".RS_VY_HLE_TEXT."</td><td><input type=\"text\" name=\"rstext\" size=\"40\" class=\"textpole\" /></td></tr>
<tr class=\"z\"><td>".RS_VY_HLE_AUTOR."</td><td><select name=\"rsautor\" size=\"1\">
<option value=\"nic\">".RS_VY_BEZ_OMEZENI."</option>";
$dotazusr=mysql_query("select idu,jmeno from ".$GLOBALS["rspredpona"]."user order by idu",$GLOBALS["dbspojeni"]);
$pocetusr=mysql_num_rows($dotazusr);
for ($pom=0;$pom<$pocetusr;$pom++):
  echo "<option value=\"".mysql_Result($dotazusr,$pom,"idu")."\">".mysql_Result($dotazusr,$pom,"jmeno")."</option>";
endfor;
echo "</select></td></tr>
<tr class=\"z\"><td>".RS_VY_HLE_TEMA."</td><td><select name=\"rstema\" size=\"1\">
<option value=\"nic\">".RS_VY_BEZ_OMEZENI."</option>";
$poletopic=VyhlSeznamTemat();
if (is_array($poletopic)): // je kdyz existuje vysledkove pole
  $pocettopic=count($poletopic);
  for ($pom=0;$pom<$pocettopic;$pom++):
    echo "<option value=\"".$poletopic[$pom][0]."\">".$poletopic[$pom][1]."</option>\n";
  endfor;
endif;
echo "</select></td></tr>
<tr class=\"z\"><td>".RS_VY_HLE_OMEZIT_NA."</td><td><select name=\"rskde\" size=\"1\"><option value=\"vse\">".RS_VY_CELY_CLA."</option><option value=\"txt\">".RS_VY_HLAVNI_CAST."</option><option value=\"tit\">".RS_VY_TITULEK."</option><option value=\"uvd\">".RS_VY_UVOD."</option><option value=\"tsl\">".RS_VY_DB_KLICU."</option></select></td></tr>
<tr class=\"z\"><td>".RS_VY_VZTAH."&nbsp; </td><td><select name=\"rsvztah\" size=\"1\"><option value=\"OR\">".RS_VY_VZT_NEBO."</option><option value=\"AND\">".RS_VY_VZT_A."</option></select></td></tr>
</table>
<p align=\"center\"><input type=\"submit\" value=\" ".RS_VY_TL_HLEDAT." \" class=\"tl\" /></p>
</form>
<p></p>\n";
}

// specifikace neexistujicich promennych - jen v pripade, ze nejsou definovany vstupem
if (!isset($GLOBALS["rsvelikost"])):
  $GLOBALS["rsvelikost"]="jr";
endif;

if (!isset($GLOBALS["rskde"])):
  $GLOBALS["rskde"]="vse";
endif;

if (!isset($GLOBALS["rskolikata"])):
  if ($GLOBALS["rsvelikost"]=="sab"): $GLOBALS["rskolik"]=15; else: $GLOBALS["rskolik"]=50; endif;
  $GLOBALS["rskolikata"]=1;
endif;

if (!isset($GLOBALS["rsautor"])):
  $GLOBALS["rsautor"]="nic";
endif;

if (!isset($GLOBALS["rstema"])):
  $GLOBALS["rstema"]="nic";
endif;

if (!isset($GLOBALS["rsvztah"])):
  $GLOBALS["rsvztah"]="OR";
endif;

if (isset($GLOBALS["stromhlmenu"])):
  $GLOBALS["prmenulink"]="&amp;stromhlmenu=".$GLOBALS["stromhlmenu"];
else:
  $GLOBALS["prmenulink"]="";
endif;

// Tvorba stranky
$vzhledwebu->Generuj();
if ($GLOBALS["rsvelikost"]!='sab'):
  ObrTabulka();  // Vlozeni layout prvku krome 'sab' modu
endif;

// detekce existence $rstext
if (isset($GLOBALS["rstext"])):
  Vyhledavani();
else:
  VyFormular();
endif;

if ($GLOBALS["rsvelikost"]!='sab'):
  KonecObrTabulka();  // Vlozeni layout prvku krome 'sab' modu
endif;
$vzhledwebu->Generuj();
?>