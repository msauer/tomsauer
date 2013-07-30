<?

######################################################################
# phpRS Specialni Funkce 1.5.1
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_klik_rekl, rs_config, rs_ankety, rs_odpovedi, rs_news, rs_topic, rs_kontrola_ip

if (!defined('IN_CODE')): die('Nepovoleny pristup! / Hacking attempt!'); endif;

// ------------------------------------------------- nacteni nastaveni z DB ------------------------------------------
$dotazconfig=mysql_query("select promenna,hodnota from ".$GLOBALS["rspredpona"]."config",$GLOBALS["dbspojeni"]);
if ($dotazconfig!=0):
 while ($pole_data = mysql_fetch_assoc($dotazconfig)):
   $GLOBALS['rsconfig']['rs_nastaveni'][$pole_data['promenna']]=$pole_data['hodnota'];
 endwhile;
endif;

// ------------------------------------------------- systemove bloky -------------------------------------------------

// systemovy blok: ankety
function Anketa()
{
// zjisteni aktivni ankety
$zjistanketa=NactiConfigProm('aktivni_anketa',0);
// podminka zobrazeni -> nalezeni aktivni ankety; false = anketa neexistuje
if ($zjistanketa!='false'):
  $dotazotazka=mysql_query("select otazka from ".$GLOBALS["rspredpona"]."ankety where ida='".$zjistanketa."'",$GLOBALS["dbspojeni"]);
  $ankotazka=mysql_result($dotazotazka,0,"otazka"); // anketni otazka
  $dotazcelkem=mysql_query("select sum(pocitadlo) as celkem from ".$GLOBALS["rspredpona"]."odpovedi where anketa='".$zjistanketa."'",$GLOBALS["dbspojeni"]);
  $celkemhlasu=mysql_result($dotazcelkem,0,"celkem"); // celkem hlasu

  if ($celkemhlasu==0): $jednoproc=0; else: $jednoproc=100/$celkemhlasu; endif; // zjisteni poctu dilku na jeden hlas

  // nacteni prehledu moznych odpovedi
  $dotazodp=mysql_query("select ido,odpoved,pocitadlo from ".$GLOBALS["rspredpona"]."odpovedi where anketa='".$zjistanketa."' order by ido",$GLOBALS["dbspojeni"]);
  $pocetodp=mysql_num_rows($dotazodp);

  $barva_prouzku=1; // barva procentualniho prouzku u odpovedi

  $txt_anketa="<span class=\"anketasysz\">".$ankotazka."</span><br /><br />\n";
  $txt_anketa.="<span class=\"anketasysodp\">\n";
  while($akt_pole_data = mysql_fetch_assoc($dotazodp)):
    $velikost=ceil($jednoproc*$akt_pole_data["pocitadlo"]);
    $txt_anketa.="<a href=\"ankety.php?akce=hlasuj&amp;hlas=".$akt_pole_data["ido"]."&amp;cil=".$GLOBALS['rsconfig']['anketa_cil_str']."&amp;anketa=".$zjistanketa."\">".$akt_pole_data["odpoved"]."</a><br />\n";
    $txt_anketa.="<img src=\"pictures.php?rvel=".$velikost."&amp;barva=".$barva_prouzku."\" height=\"8\" width=\"".$velikost."\" alt=\"".$akt_pole_data["pocitadlo"]."\" /> (".$akt_pole_data["pocitadlo"]." ".RS_SP_POCET_HLA.")<br />\n";
    $barva_prouzku++;
  endwhile;
  $txt_anketa.="</span>\n";
  $txt_anketa.="<p align=\"center\" class=\"anketasysz\">".RS_SP_CELKEM_HLA.": ".$celkemhlasu."</p>\n";

  // zobrazeni menu
  switch ($GLOBALS["vzhledwebu"]->AktBlokTyp()):
    case 1: Blok1($GLOBALS["vzhledwebu"]->AktBlokNazev(),$txt_anketa); break;
    case 2: Blok2($GLOBALS["vzhledwebu"]->AktBlokNazev(),$txt_anketa); break;
    case 3: Blok3($GLOBALS["vzhledwebu"]->AktBlokNazev(),$txt_anketa); break;
    case 4: Blok4($GLOBALS["vzhledwebu"]->AktBlokNazev(),$txt_anketa); break;
    case 5: Blok5($GLOBALS["vzhledwebu"]->AktBlokNazev(),$txt_anketa); break;
    default: Blok1($GLOBALS["vzhledwebu"]->AktBlokNazev(),$txt_anketa); break;
  endswitch;
endif;
}

// systemovy blok: novinky
function HotNews()
{
// zjisteni pozadovane poctu hot news urcenych k zobrazeni; 0 = zadne
$pocetzprav=NactiConfigProm('pocet_novinek',0);
// podminka zobrazeni -> kladne mnozstvi "hot news"
if ($pocetzprav>0):
  $dotaznews=mysql_query("select titulek,informace,datum,typ_nov from ".$GLOBALS["rspredpona"]."news order by datum desc limit 0,".$pocetzprav,$GLOBALS["dbspojeni"]);
  $pocetnews=mysql_num_rows($dotaznews);
  if ($pocetnews==0):
    $txt_novinky='<div class="novtext">Databáze neobsahuje žádnou novinku.</div>'."\n";
  else:
    // inic.
    $txt_novinky=''; // vysledny retezec
    $prvni=1; // test na prvni prubeh
    // vypis
    while($pole_data = mysql_fetch_assoc($dotaznews)):
      if ($prvni==1): $prvni=0; else: $txt_novinky.="<br />\n"; endif;
      $txt_novinky.='<span class="novdatum">'.MyDatetimeToDate($pole_data['datum']).':</span> ';
      // typ_nov: 0 = bezna, 1 = zvyraznena
      if ($pole_data['typ_nov']==0):
        $txt_novinky.='<span class="novtit">'.$pole_data['titulek'].'</span>';
      else:
        $txt_novinky.='<span class="novtitduraz">'.$pole_data['titulek'].'</span>';
      endif;
      $txt_novinky.='<div class="novtext">'.$pole_data['informace'].'</div>'."\n";
    endwhile;
  endif;

  // zobrazeni menu
  switch ($GLOBALS["vzhledwebu"]->AktBlokTyp()):
    case 1: Blok1($GLOBALS["vzhledwebu"]->AktBlokNazev(),$txt_novinky); break;
    case 2: Blok2($GLOBALS["vzhledwebu"]->AktBlokNazev(),$txt_novinky); break;
    case 3: Blok3($GLOBALS["vzhledwebu"]->AktBlokNazev(),$txt_novinky); break;
    case 4: Blok4($GLOBALS["vzhledwebu"]->AktBlokNazev(),$txt_novinky); break;
    case 5: Blok5($GLOBALS["vzhledwebu"]->AktBlokNazev(),$txt_novinky); break;
    default: Blok1($GLOBALS["vzhledwebu"]->AktBlokNazev(),$txt_novinky); break;
  endswitch;
endif;
}

// systemovy blok: seznam rubrik (navigacniho menu)
function GenHlavMenu() // vstup do fce: $stromhlmenu
{
// dekompilace stromu vnoreni
if (isset($GLOBALS["stromhlmenu"])): $przobrazid=explode(":",$GLOBALS["stromhlmenu"]); $pocetprvku=count($przobrazid); else: $pocetprvku=0; endif;

$podminka='';
for($pom=0;$pom<$pocetprvku;$pom++):
  if($pom>0): $podminka.=','; endif;
  $podminka.=$przobrazid[$pom];
endfor;
if ($podminka!=''): $podminka=' where id_predka in ('.mysql_escape_string($podminka).') or level=0'; else: $podminka=' where level=0'; endif; // kompletni sestaveni podminky

$dotazmenu=mysql_query("select idt,nazev,level,rodic,id_predka from ".$GLOBALS["rspredpona"]."topic".$podminka." order by level,nazev",$GLOBALS["dbspojeni"]);
$pocetmenu=mysql_num_rows($dotazmenu);

$poc_sezzakl=0;
$poc_sezprvni=0;
$poc_sezdruha=0;
$poc_seztreti=0;

// nacteni dat do seznamu
for ($pom=0;$pom<$pocetmenu;$pom++):
  $pole_data=mysql_fetch_assoc($dotazmenu);
  switch ($pole_data['level']):
  case 0:
    // 0 - id, 1 - prep. rodic, 2 - nazev polozky
    $sezzakl[$poc_sezzakl][0]=$pole_data['idt'];
    $sezzakl[$poc_sezzakl][1]=$pole_data['rodic'];
    $sezzakl[$poc_sezzakl][2]=$pole_data['nazev'];
    $poc_sezzakl++;
    break;
  case 1:
    // 0 - id, 1 - prep. rodic, 2 - nazev pol., 4 - id predka
    $sezprvni[$poc_sezprvni][0]=$pole_data['idt'];
    $sezprvni[$poc_sezprvni][1]=$pole_data['rodic'];
    $sezprvni[$poc_sezprvni][2]=$pole_data['nazev'];
    $sezprvni[$poc_sezprvni][4]=$pole_data['id_predka'];
    $poc_sezprvni++;
    break;
  case 2:
    // 0 - id, 1 - prep. rodic, 2 - nazev pol., 4 - id predka
    $sezdruha[$poc_sezdruha][0]=$pole_data['idt'];
    $sezdruha[$poc_sezdruha][1]=$pole_data['rodic'];
    $sezdruha[$poc_sezdruha][2]=$pole_data['nazev'];
    $sezdruha[$poc_sezdruha][4]=$pole_data['id_predka'];
    $poc_sezdruha++;
    break;
  case 3:
    // 0 - id, 1 - prep. rodic, 2 - nazev pol., 4 - id predka
    $seztreti[$poc_seztreti][0]=$pole_data['idt'];
    $seztreti[$poc_seztreti][1]=$pole_data['rodic'];
    $seztreti[$poc_seztreti][2]=$pole_data['nazev'];
    $seztreti[$poc_seztreti][4]=$pole_data['id_predka'];
    $poc_seztreti++;
    break;
 endswitch;
endfor;

$plus="<img src=\"".$GLOBALS["adrobrlayoutu"]."plus.gif\" width=\"11\" height=\"11\" alt=\"plus\" />&nbsp;";
$minus="<img src=\"".$GLOBALS["adrobrlayoutu"]."minus.gif\" width=\"11\" height=\"11\" alt=\"mínus\" />&nbsp;";
$prmenu="";

// sestaveni stromu
$prmenu="<table cellspacing=\"0\" cellpadding=\"1\">\n";
for ($pom=0;$pom<$poc_sezzakl;$pom++):
  // zakl. urov.
  if ($sezzakl[$pom][1]==0): $prmenu.="<tr class=\"z\"><td>".$minus."</td><td colspan=\"4\"><a href=\"search.php?rsvelikost=sab&amp;rstext=all-phpRS-all&amp;rstema=".$sezzakl[$pom][0]."&amp;stromhlmenu=".$sezzakl[$pom][0]."\">".$sezzakl[$pom][2]."</a></td></tr>\n";
  else: $prmenu.="<tr class=\"z\"><td>".$plus."</td><td colspan=\"4\"><a href=\"search.php?rsvelikost=sab&amp;rstext=all-phpRS-all&amp;rstema=".$sezzakl[$pom][0]."&amp;stromhlmenu=".$sezzakl[$pom][0]."\">".$sezzakl[$pom][2]."</a></td></tr>\n";
    // prvni urov.
    for ($pom1=0;$pom1<$poc_sezprvni;$pom1++):
     if ($sezprvni[$pom1][4]==$sezzakl[$pom][0]):
       if ($sezprvni[$pom1][1]==0): $prmenu.="<tr class=\"z\"><td></td><td>".$minus."</td><td colspan=\"3\"><a href=\"search.php?rsvelikost=sab&amp;rstext=all-phpRS-all&amp;rstema=".$sezprvni[$pom1][0]."&amp;stromhlmenu=".$sezzakl[$pom][0].":".$sezprvni[$pom1][0]."\">".$sezprvni[$pom1][2]."</a></td></tr>\n";
       else: $prmenu.="<tr class=\"z\"><td></td><td>".$plus."</td><td colspan=\"3\"><a href=\"search.php?rsvelikost=sab&amp;rstext=all-phpRS-all&amp;rstema=".$sezprvni[$pom1][0]."&amp;stromhlmenu=".$sezzakl[$pom][0].":".$sezprvni[$pom1][0]."\">".$sezprvni[$pom1][2]."</a></td></tr>\n";
         // druha urov.
         for ($pom2=0;$pom2<$poc_sezdruha;$pom2++):
          if ($sezdruha[$pom2][4]==$sezprvni[$pom1][0]):
            if ($sezdruha[$pom2][1]==0): $prmenu.="<tr class=\"z\"><td colspan=\"2\"></td><td>".$minus."</td><td colspan=\"2\"><a href=\"search.php?rsvelikost=sab&amp;rstext=all-phpRS-all&rstema=".$sezdruha[$pom2][0]."&amp;stromhlmenu=".$sezzakl[$pom][0].":".$sezprvni[$pom1][0].":".$sezdruha[$pom2][0]."\">".$sezdruha[$pom2][2]."</a></td></tr>\n";
            else: $prmenu.="<tr class=\"z\"><td colspan=\"2\"></td><td>".$plus."</td><td colspan=\"2\"><a href=\"search.php?rsvelikost=sab&amp;rstext=all-phpRS-all&rstema=".$sezdruha[$pom2][0]."&amp;stromhlmenu=".$sezzakl[$pom][0].":".$sezprvni[$pom1][0].":".$sezdruha[$pom2][0]."\">".$sezdruha[$pom2][2]."</a></td></tr>\n";
              // treti urov.
              for ($pom3=0;$pom3<$poc_seztreti;$pom3++):
               if ($seztreti[$pom3][4]==$sezdruha[$pom2][0]):
                 if ($seztreti[$pom3][1]==0): $prmenu.="<tr class=\"z\"><td colspan=\"3\"></td><td>".$minus."</td><td><a href=\"search.php?rsvelikost=sab&amp;rstext=all-phpRS-all&amp;rstema=".$seztreti[$pom3][0]."&amp;stromhlmenu=".$sezzakl[$pom][0].":".$sezprvni[$pom1][0].":".$sezdruha[$pom2][0].":".$seztreti[$pom3][0]."\">".$seztreti[$pom3][2]."</a></td></tr>\n";
                 else: $prmenu.="<tr class=\"z\"><td colspan=\"3\"></td><td>".$plus."</td><td><a href=\"search.php?rsvelikost=sab&amp;rstext=all-phpRS-all&amp;rstema=".$seztreti[$pom3][0]."&amp;stromhlmenu=".$sezzakl[$pom][0].":".$sezprvni[$pom1][0].":".$sezdruha[$pom2][0].":".$seztreti[$pom3][0]."\">".$seztreti[$pom3][2]."</a></td></tr>\n"; endif;
               endif;
              endfor;
              // konec. treti
            endif;
          endif;
         endfor;
         // konec. druhe
       endif;
     endif;
    endfor;
    // konec. prvni
  endif;
endfor;
$prmenu.="</table>\n";

// zobrazeni menu
switch ($GLOBALS["vzhledwebu"]->AktBlokTyp()):
  case 1: Blok1($GLOBALS["vzhledwebu"]->AktBlokNazev(),$prmenu); break;
  case 2: Blok2($GLOBALS["vzhledwebu"]->AktBlokNazev(),$prmenu); break;
  case 3: Blok3($GLOBALS["vzhledwebu"]->AktBlokNazev(),$prmenu); break;
  case 4: Blok4($GLOBALS["vzhledwebu"]->AktBlokNazev(),$prmenu); break;
  case 5: Blok5($GLOBALS["vzhledwebu"]->AktBlokNazev(),$prmenu); break;
  default: Blok1($GLOBALS["vzhledwebu"]->AktBlokNazev(),$prmenu); break;
endswitch;
}

// systemovy blok: kalendar
function Kalendar()
{
// vstup do fce: $kalendarmes, $kalendarrok
// inic. vstupnych promennych
if(isset($GLOBALS["kalendarmes"])): $mesic=mysql_escape_string($GLOBALS["kalendarmes"]); else: $mesic=date("m"); endif;
if(isset($GLOBALS["kalendarrok"])): $rok=mysql_escape_string($GLOBALS["kalendarrok"]); else: $rok=date("Y"); endif;
$dnesnidatum=date("Y-m-d");

$dotaz="select date_format(datum,'%Y-%m-%d') as vyslden from ".$GLOBALS["rspredpona"]."clanky where datum>='".date("Y-m-d",mktime(0,0,1,$mesic,1,$rok))."' and datum<'".date("Y-m-d",mktime(0,0,1,($mesic+1),1,$rok))."' and datum<='".$dnesnidatum."' and visible='1' group by vyslden";
$dotazcla=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetcla=mysql_num_rows($dotazcla);

// naplneni pomocneho clankoveho pole
$akt_pole_clanku=array();
for ($pom=0;$pom<$pocetcla;$pom++):
  $akt_pole_clanku[]=mysql_result($dotazcla,$pom,"vyslden");
endfor;

// sestaveni celkoveho stavoveho vysledkoveho pole
$darum=array();
for($pom=1;$pom<32;$pom++):
  if (checkdate($mesic,$pom,$rok)):
    // stavy v poly "$datum": 0 = zadny clanek, 1 = vydan alespon jeden clanek, 2 = dnesni datum
    $datum[$pom]=0; // defaultne = zadny clanek
    $porovnani_date=date("Y-m-d",mktime(0,0,1,$mesic,$pom,$rok)); // datum urcene k porovnani s datumy v DB
    if (in_array($porovnani_date,$akt_pole_clanku)):
      $datum[$pom]=1; // vydan alespon jeden clanek
    endif;
    if ($porovnani_date==$dnesnidatum): $datum[$pom]=2; endif; // test na dnesni datum
  endif;
endfor;
$pocetdnuvmes=count($datum);

$cislodne=date("w",mktime(0,0,1,$mesic,1,$rok)); // 0 - nedele, ..., 6 - sobota
$pristimes=date("m",mktime(0,0,1,($mesic+1),1,$rok));
$pristirok=date("Y",mktime(0,0,1,($mesic+1),1,$rok));
$pristiod=$pristirok."-".$pristimes."-01 00:00:00";
$pristido=date("Y-m-d H:i:s",mktime(23,59,59,$mesic+2,1,$rok)-86400);
$predeslymes=date("m",mktime(0,0,1,($mesic-1),1,$rok));
$predeslyrok=date("Y",mktime(0,0,1,($mesic-1),1,$rok));
$predeslyod=$predeslyrok."-".$predeslymes."-01 00:00:00";
$predeslydo=date("Y-m-d H:i:s",(mktime(23,59,59,$mesic,1,$rok)-86400));

// hlavicka tabulky dnu
$prmenu="<table border=\"1\" align=\"center\" cellspacing=\"0\" cellpadding=\"1\">
<tr class=\"kaltext\"><td colspan=\"7\" align=\"center\"><b>
<a href=\"search.php?kalendarmes=$predeslymes&amp;kalendarrok=$predeslyrok&amp;rsod=$predeslyod&amp;rsdo=$predeslydo&amp;rstext=all-phpRS-all\">&lt;&lt;</a>&nbsp;
<a href=\"search.php?kalendarmes=$mesic&amp;kalendarrok=$rok&amp;rsod=$rok-$mesic-01 00:00:00&amp;rsdo=$rok-$mesic-$pocetdnuvmes 23:59:59&amp;rstext=all-phpRS-all\">".TextMesic($mesic)."</a>
&nbsp;<a href=\"search.php?kalendarmes=$pristimes&amp;kalendarrok=$pristirok&amp;rsod=$pristiod&amp;rsdo=$pristido&amp;rstext=all-phpRS-all\">&gt;&gt;</a>
</b></td></tr>
<tr class=\"kaltext\"><td>".RS_SP_KAL_PO."</td><td>".RS_SP_KAL_UT."</td><td>".RS_SP_KAL_ST."</td><td>".RS_SP_KAL_CT."</td><td>".RS_SP_KAL_PA."</td><td>".RS_SP_KAL_SO."</td><td>".RS_SP_KAL_NE."</td></tr>\n";

// priprava zobrazeni prvniho dne v mesici
switch($cislodne):
  case 0: $prmenu.="<tr class=\"kaltext\"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>\n"; break;
  case 1: break;
  case 2: $prmenu.="<tr class=\"kaltext\"><td>&nbsp;</td>"; break;
  case 3: $prmenu.="<tr class=\"kaltext\"><td>&nbsp;</td><td>&nbsp;</td>"; break;
  case 4: $prmenu.="<tr class=\"kaltext\"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>"; break;
  case 5: $prmenu.="<tr class=\"kaltext\"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>"; break;
  case 6: $prmenu.="<tr class=\"kaltext\"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>"; break;
endswitch;

// vypis vsech dnu do tabulky
for($pom=1;$pom<=$pocetdnuvmes;$pom++):
  if ($cislodne==1): $prmenu.="<tr class=\"kaltext\">"; endif;
  // vzhled
  switch ($datum[$pom]):
    case 0: $prmenu.="<td align=\"center\">".$pom."</td>\n"; break;
    case 1: $prmenu.="<td align=\"center\" class=\"kalclanek\"><a href=\"search.php?kalendarmes=".$mesic."&amp;kalendarrok=".$rok."&amp;rsod=".$rok."-".$mesic."-".$pom." 00:00:01&amp;rsdo=".$rok."-".$mesic."-".$pom." 23:59:59&amp;rstext=all-phpRS-all\">".$pom."</a></td>\n"; break;
    case 2: $prmenu.="<td align=\"center\" class=\"kaldnesni\"><a href=\"index.php\">".$pom."</a></td>\n"; break;
    default: $prmenu.="<td align=\"center\">".$pom."</td>\n"; break;
  endswitch;
  // test na typ dne
  if ($cislodne==0): $prmenu.="</tr>\n"; endif;
  if ($cislodne==6): $cislodne=0; else: $cislodne=($cislodne+1); endif;
endfor;

// dokonceni tabulky dnu
switch($cislodne):
  case 0: $prmenu.="<td>&nbsp;</td></tr>\n"; break;
  case 1: break;
  case 2: $prmenu.="<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>\n"; break;
  case 3: $prmenu.="<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>\n"; break;
  case 4: $prmenu.="<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>\n"; break;
  case 5: $prmenu.="<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>\n"; break;
  case 6: $prmenu.="<td>&nbsp;</td><td>&nbsp;</td></tr>\n"; break;
endswitch;
$prmenu.="</table>\n";

// zobrazeni menu
switch ($GLOBALS["vzhledwebu"]->AktBlokTyp()):
  case 1: Blok1($GLOBALS["vzhledwebu"]->AktBlokNazev(),$prmenu); break;
  case 2: Blok2($GLOBALS["vzhledwebu"]->AktBlokNazev(),$prmenu); break;
  case 3: Blok3($GLOBALS["vzhledwebu"]->AktBlokNazev(),$prmenu); break;
  case 4: Blok4($GLOBALS["vzhledwebu"]->AktBlokNazev(),$prmenu); break;
  case 5: Blok5($GLOBALS["vzhledwebu"]->AktBlokNazev(),$prmenu); break;
  default: Blok1($GLOBALS["vzhledwebu"]->AktBlokNazev(),$prmenu); break;
endswitch;
}

// ------------------------------------------------- pomocne funkce -------------------------------------------------

function Banners($poloha = 0)
{
// od verze phpRS 1.4.0 je reklamni system zcela automaticky a nastavuje se z administracniho modulu
// pozice: 1 - horni, 2 - dolni
switch($poloha):
  // horni pozice
  case 1: $dotazkod=mysql_query("select kod from ".$GLOBALS["rspredpona"]."klik_rekl where pozice='1'",$GLOBALS["dbspojeni"]);
          $prkod=mysql_Result($dotazkod,0,"kod"); break;
  // dolni pozice
  case 2: $dotazkod=mysql_query("select kod from ".$GLOBALS["rspredpona"]."klik_rekl where pozice='2'",$GLOBALS["dbspojeni"]);
          $prkod=mysql_Result($dotazkod,0,"kod"); break;
  // nulovy vstup
  default: $prkod=""; break;
endswitch;
echo "\n<!-- Misto pro banner -->\n";
// rozhodnuti o zpusobu zobrazeni rekl. kodu
$prklic=substr($prkod,0,8);
// test na kampan
if ($prklic=="<kampan>"):
  // zjistena kampan
  $prdelka=strlen($prkod)-17;
  $prkampan=substr($prkod,8,$prdelka); // ziskani id kampane
  $dotazkam=mysql_query("select count(idb) as pocet from ".$GLOBALS["rspredpona"]."klik_ban where id_kampan='".$prkampan."'",$GLOBALS["dbspojeni"]);
  $prpocban=mysql_Result($dotazkam,0,"pocet"); // pocet banneru v kampani
  if ($prpocban!=0): // kampan obsahuje bannery
    $prktery=(rand(1,$prpocban)-1); // nutne odecist 1, protoze se poradi pocita od nuly
    $dotazban=mysql_query("select idb,text,banner,width,height,druh from ".$GLOBALS["rspredpona"]."klik_ban where id_kampan='".$prkampan."' order by idb limit ".$prktery.",1",$GLOBALS["dbspojeni"]);
    list($prbidb,$prbtext,$prbbanner,$prbwidth,$prbheight,$prbdruh)=mysql_fetch_row($dotazban);
    // vypis reklamy
    echo "<div align=\"center\">";
    switch($prbdruh):
      case 0: // forma: banner
        echo "<span class=\"bannerpod\"><a href=\"direct.php?kam=".$prbidb."\" target=\"_blank\"><img src=\"".$prbbanner."\" alt=\"".$prbtext."\" width=\"".$prbwidth."\" height=\"".$prbheight."\" />";
        if ($prbtext!=""): echo "<br />".$prbtext; endif; // kdyz existuje doplnkovy text
        echo "</a></span>";
        break;
      case 1: // forma: text
        echo "<span class=\"banner\"><a href=\"direct.php?kam=".$prbidb."\" title=\"".$prbtext."\" target=\"_blank\">".$prbbanner."</a></span>";
        break;
      case 2: // forma: reklamni kod
        echo $prbbanner;
        break;
    endswitch;
    echo "</div>";
  endif; // konec $prpocban
else:
  // neni kampan, cisty reklamni kod
  echo "<div align=\"center\">".$prkod."</div>";
endif;
echo "\n<!-- Konec Misto pro banner -->\n";
}

function Banners_str($poloha = 0)
{
// od verze phpRS 1.4.0 je reklamni system zcela automaticky a nastavuje se z administracniho modulu
// pozice: 1 - horni, 2 - dolni
switch($poloha):
  // horni pozice
  case 1: $dotazkod=mysql_query("select kod from ".$GLOBALS["rspredpona"]."klik_rekl where pozice='1'",$GLOBALS["dbspojeni"]);
          $prkod=mysql_Result($dotazkod,0,"kod"); break;
  // dolni pozice
  case 2: $dotazkod=mysql_query("select kod from ".$GLOBALS["rspredpona"]."klik_rekl where pozice='2'",$GLOBALS["dbspojeni"]);
          $prkod=mysql_Result($dotazkod,0,"kod"); break;
  // nulovy vstup
  default: $prkod=""; break;
endswitch;
$vysledek="";
$vysledek.="\n<!-- Misto pro banner -->\n";
// rozhodnuti o zpusobu zobrazeni rekl. kodu
$prklic=substr($prkod,0,8);
// test na kampan
if ($prklic=="<kampan>"):
  // zjistena kampan
  $prdelka=strlen($prkod)-17;
  $prkampan=substr($prkod,8,$prdelka); // ziskani id kampane
  $dotazkam=mysql_query("select count(idb) as pocet from ".$GLOBALS["rspredpona"]."klik_ban where id_kampan='".$prkampan."'",$GLOBALS["dbspojeni"]);
  $prpocban=mysql_Result($dotazkam,0,"pocet"); // pocet banneru v kampani
  if ($prpocban!=0): // kampan obsahuje bannery
    $prktery=(rand(1,$prpocban)-1); // nutne odecist 1, protoze se poradi pocita od nuly
    $dotazban=mysql_query("select idb,text,banner,width,height,druh from ".$GLOBALS["rspredpona"]."klik_ban where id_kampan='".$prkampan."' order by idb limit ".$prktery.",1",$GLOBALS["dbspojeni"]);
    list($prbidb,$prbtext,$prbbanner,$prbwidth,$prbheight,$prbdruh)=mysql_fetch_row($dotazban);
    // vypis reklamy
    $vysledek.="<div align=\"center\">";
    switch($prbdruh):
      case 0: // forma: banner
        $vysledek.="<span class=\"bannerpod\"><a href=\"direct.php?kam=".$prbidb."\" target=\"_blank\"><img src=\"".$prbbanner."\" alt=\"".$prbtext."\" width=\"".$prbwidth."\" height=\"".$prbheight."\" />";
        if ($prbtext!=""): $vysledek.="<br />".$prbtext; endif; // kdyz existuje doplnkovy text
        $vysledek.="</a></span>";
        break;
      case 1: // forma: text
        $vysledek.="<span class=\"banner\"><a href=\"direct.php?kam=".$prbidb."\" title=\"".$prbtext."\" target=\"_blank\">".$prbbanner."</a></span>";
        break;
      case 2: // forma: reklamni kod
        $vysledek.=$prbbanner;
        break;
    endswitch;
    $vysledek.="</div>";
  endif; // konec $prpocban
else:
  // neni kampan, cisty reklamni kod
  $vysledek.="<div align=\"center\">".$prkod."</div>";
endif;
$vysledek.="\n<!-- Konec Misto pro banner -->\n";

return $vysledek;
}

function SouvisejiciCl($id_clanek = 0)
{
$id_clanek=mysql_escape_string($id_clanek); // vstupni korekce

$dotazskup=mysql_query("select skupina_cl from ".$GLOBALS["rspredpona"]."clanky where link='".$id_clanek."'",$GLOBALS["dbspojeni"]);

if (mysql_num_rows($dotazskup)>0): // clanek existuje
  $id_skupiny=mysql_result($dotazskup,0,"skupina_cl"); // identifikacni cislo skupiny
  if ($id_skupiny>0): // 0 = bez zarazeni
    $dotazclanky=mysql_query("select link,titulek,datum from ".$GLOBALS["rspredpona"]."clanky where skupina_cl='".$id_skupiny."' and link!='".$id_clanek."' and visible='1' and datum<='".Date("Y-m-d H:i:s")."' order by datum desc",$GLOBALS["dbspojeni"]);
    $pocetclanky=mysql_num_rows($dotazclanky);
    if ($pocetclanky>0):
      echo "<div class=\"z\">\n";
      echo "<strong>".RS_SP_SOUVIS_CLA.":</strong><br />\n";
      while ($pole_data = mysql_fetch_assoc($dotazclanky)):
        echo "<a href=\"view.php?cisloclanku=".$pole_data["link"]."\" target=\"_blank\">".$pole_data["titulek"]."</a> (".MyDatetimeToDate($pole_data["datum"]).")<br />\n";
      endwhile;
      echo "</div><br />\n";
    endif;
  endif;
endif;
}

function HodnoceniCl($id_clanek = 0)
{
$id_clanek=mysql_escape_string($id_clanek); // vstupni korekce

$dotazhod=mysql_query("select hodnoceni,mn_hodnoceni from ".$GLOBALS["rspredpona"]."clanky where link='".$id_clanek."'",$GLOBALS["dbspojeni"]);

if (mysql_num_rows($dotazhod)>0): // clanek existuje
  $hodnoceni=mysql_Result($dotazhod,0,"hodnoceni"); // hodnoceni
  $mnozstvi=mysql_Result($dotazhod,0,"mn_hodnoceni"); // mnozstvi hodnot

  echo "<div align=\"right\"><div class=\"hodnoceni\"><form action=\"view.php\" method=\"post\" style=\"margin: 0px;\">\n";
  echo "<input type=\"hidden\" name=\"cisloclanku\" value=\"".$id_clanek."\" />\n";
  if ($mnozstvi>0):
    echo "[".RS_SP_AKT_ZNAMKA.": ".number_format(($hodnoceni/$mnozstvi),2,',','')." / ".RS_SP_POCET_ZNAMEK.": ".$mnozstvi."] ";
  else:
    echo "[".RS_SP_AKT_ZNAMKA.": 0 / ".RS_SP_POCET_ZNAMEK.": 0] ";
  endif;
  echo "<input type=\"radio\" name=\"hlasovani\" value=\"1\" />1 ";
  echo "<input type=\"radio\" name=\"hlasovani\" value=\"2\" />2 ";
  echo "<input type=\"radio\" name=\"hlasovani\" value=\"3\" />3 ";
  echo "<input type=\"radio\" name=\"hlasovani\" value=\"4\" />4 ";
  echo "<input type=\"radio\" name=\"hlasovani\" value=\"5\" />5 ";
  echo "<input type=\"submit\" value=\" ".RS_SP_TL_ZNAMKA." \" class=\"tl\" />\n";
  echo "</form></div></div><br />\n";
endif;
}

function TextMesic($cismes = 0)
{
switch($cismes):
  case 1: $txt=RS_SP_KAL_M01; break;
  case 2: $txt=RS_SP_KAL_M02; break;
  case 3: $txt=RS_SP_KAL_M03; break;
  case 4: $txt=RS_SP_KAL_M04; break;
  case 5: $txt=RS_SP_KAL_M05; break;
  case 6: $txt=RS_SP_KAL_M06; break;
  case 7: $txt=RS_SP_KAL_M07; break;
  case 8: $txt=RS_SP_KAL_M08; break;
  case 9: $txt=RS_SP_KAL_M09; break;
  case 10: $txt=RS_SP_KAL_M10; break;
  case 11: $txt=RS_SP_KAL_M11; break;
  case 12: $txt=RS_SP_KAL_M12; break;
  default: $txt='';
endswitch;

return $txt;
}

// generator pevne mezery
function Me($vel = 1)
{
$mez="";
for ($pom=0;$pom<$vel;$pom++):
  $mez.="&nbsp;";
endfor;
return $mez;
}

// preved MySQL datetime typ do formy bezneho teckou oddeleneho datumu
function MyDatetimeToDate($mysql_datum)
{
$rozlozenedatum=explode(" ",trim($mysql_datum)); // [0] - datum, [1] - cas
$vysledek=explode("-",$rozlozenedatum[0]);
return $vysledek[2].".".$vysledek[1].".".$vysledek[0]; // dd.mm.rrrr
}

// prevede MySQL datetime typ na unixovy cas (cislo)
function MyDatetimeToInt($mysql_datum)
{
list($datum,$cas)=explode(" ",$mysql_datum);
list($rok,$mes,$dat)=explode("-",$datum);
list($hod,$min,$sek)=explode(":",$cas);
return date("U",mktime($hod,$min,$sek,$mes,$dat,$rok)); // int
}

// preved MySQL datetime do standarni zobrazovaci formy
function MyDatetimeToStd($mysql_datum)
{
list($datum,$cas)=Explode (" ",$mysql_datum);
list($rok,$mes,$den)=Explode ("-",$datum);
return $den.".".$mes.".".$rok." ".$cas; // dd.mm.rrrr hh:mm:ss
}

// test na opakujici se IP adresu
function TestNaOpakujiciIP($akt_typ_kontr_str = '', $akt_delka_omezeni = 0, $akt_max_pocet_opak = 0)
{
$vysledek=1; // default true; 1 = opakuje se, 0 = neopakuje se

$akt_ip_adresa=$_SERVER["REMOTE_ADDR"]; // ip adresa ctenare
$akt_cas=Date("Y-m-d H:i:s");

// inic. tabulky - ocisteni od starych dat
@mysql_query("delete from ".$GLOBALS["rspredpona"]."kontrola_ip where cas<'".$akt_cas."'",$GLOBALS["dbspojeni"]);
// testovani dotaz
$dotazip=mysql_query("select idk,pocet from ".$GLOBALS["rspredpona"]."kontrola_ip where cas>='".$akt_cas."' and ip_adresa='".$akt_ip_adresa."' and typ='".$akt_typ_kontr_str."'",$GLOBALS["dbspojeni"]);
if ($dotazip!=0):
  if (mysql_num_rows($dotazip)==0):
    // neexistuje zaznam - nutno vytvorit
    $akt_ttl_cas=Date("Y-m-d H:i:s",time()+$akt_delka_omezeni); // stanoveni casove platnosti omezeni
    @mysql_query("insert into ".$GLOBALS["rspredpona"]."kontrola_ip values (null,'".$akt_ip_adresa."','".$akt_ttl_cas."',1,'".$akt_typ_kontr_str."')",$GLOBALS["dbspojeni"]);
    $vysledek=0; // false; 0 = neopakuje se
  else:
    // nacteni ziskanych dat
    $akt_pole_data=mysql_fetch_assoc($dotazip);
    // test na pocet opakovani
    if ($akt_pole_data['pocet']<$akt_max_pocet_opak):
      $vysledek=0; // false; 0 = neopakuje se
      @mysql_query("update ".$GLOBALS["rspredpona"]."kontrola_ip set pocet=pocet+1 where idk=".$akt_pole_data['idk']." and typ='".$akt_typ_kontr_str."'",$GLOBALS["dbspojeni"]);
    endif;
  endif;
endif;

return $vysledek;
}

// nacteni hodnoty konfiguracni promenne
function NactiConfigProm($promenna = '', $chyba = 0)
{
if (isset($GLOBALS['rsconfig']['rs_nastaveni'][$promenna])):
  return $GLOBALS['rsconfig']['rs_nastaveni'][$promenna]; // hledana promenna existuje
else:
  return $chyba; // chyba
endif;
}
?>
