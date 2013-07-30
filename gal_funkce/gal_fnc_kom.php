<?php
######################################################################
# phpRS Gallery 0.99.500
######################################################################
// phpRS Copyright (c) 2001-2004 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// Gallery Copyright (c) 2004 by Michal Safus (michalsafus@gmail.com)
// http://www.phprs.cz/magazin/gallery
// This program is free software. - Toto je bezplatny a svobodny software.

function SetridKomentare($vstupnipole)
{
 /*
  predpoklada se, ze vstupni pole je jiz chnologociky setrizeno
  -> u komentaru lze tohoto stavu docilit i setrizenim podle id - lze totiz logicky predpokladat, ze nejnizsi id patri nejdrive vlozenemu komentari, atd.
  $vstupnipole[X][0] - id
                 [1] - id predka
                 [2] - pouzito 1/0, defaulne 0 (ne) */
 // inic. vstupniho pole
 $trizeni=0; // false
 if (is_array($vstupnipole)):
   $pocetprvku=count($vstupnipole); // pocet prvku ve vstupnim poli
   if ($pocetprvku>0):
     $trizeni=1; // true
   endif;
 endif;
 $polehist[0]=0; // historie prohledavani
 $polex=0; // akt. pozice v historii
 $vysledekcislo=0; // pocitadlo vyslednych radku ve vysledkovem poli
 // start trizeni
 while ($trizeni==1):
   $nasel=0;
   $aktpom=0;
   for ($pom=0;$pom<$pocetprvku;$pom++):
    if ($vstupnipole[$pom][2]==0): // kdyz nebyl akt. radek jeste pouzit
     if ($vstupnipole[$pom][1]==$polehist[$polex]): // kdyz nalezi hledanemu predku
      $aktpom=$pom;
      $nasel=1;
      break; // vyskoceni z for cyklu
     endif;
    endif;
   endfor;
  if ($nasel==1):
   // vysledek hledani je kladny
   $vysledek[$vysledekcislo][0]=$aktpom; // pridani do pole vysledku
   $vysledek[$vysledekcislo][1]=$polex; // od 0 vyse
   $vysledekcislo++; // prechod na dalsi radek ve vysledkovem poli
   $vstupnipole[$aktpom][2]=1; // nastaveni prepinace na pouzito
   $polex++; // prechod na vyssi uroven v historii
   $polehist[$polex]=$vstupnipole[$aktpom][0]; // nastaveni akt. hodnoty v historii
  else:
   // vysledek hledani je zaporny
   if ($polehist[$polex]==0):
    // vysledek hledani na zakladni urovni je prazdny -> neexistuje zadna dalsi vetev
    $trizeni=0;
   else:
    $polex--; // prechod na nizsi uroven v historii
   endif;
  endif;
 endwhile;
 /*
   $vysledek[X][0] - X ve vstupnim poly
               [1] - cislo urovne */
  return $vysledek;
}

function ZobrazKom()
{
 // bezpecnostni korekce
 $GLOBALS["media_id"]=addslashes($GLOBALS["media_id"]);
 $dotazkom=mysql_query("select idk,datum,od,titulek,reakce_na from ".$GLOBALS["rspredpona"]."media_komentare where obrazek='".$GLOBALS["media_id"]."' order by idk",$GLOBALS["dbspojeni"]);
 $pocetkom=mysql_num_rows($dotazkom);
 if ($pocetkom==0):
  $vypis.="<p class=\"komz\">".GAL_COMM_ZADNY_KOMENTAR."</p>\n";
 else:
  // prevod do pole
  for ($pom=0;$pom<$pocetkom;$pom++):
   // pole informaci
    $data[$pom][0]=mysql_Result($dotazkom,$pom,"idk");     // idk
    $data[$pom][1]=mysql_Result($dotazkom,$pom,"titulek"); // titulek
    $data[$pom][2]=mysql_Result($dotazkom,$pom,"od");      // autor
    $data[$pom][3]=mysql_Result($dotazkom,$pom,"datum");   // datum
   // pom. pole k setrideni
    $pomporadi[$pom][0]=mysql_Result($dotazkom,$pom,"idk");        // id komentare
    $pomporadi[$pom][1]=mysql_Result($dotazkom,$pom,"reakce_na");  // id predka komentare
    $pomporadi[$pom][2]=0;                                         // nastaveni stavu radku
  endfor;
  // setrideni
   $poradi=SetridKomentare($pomporadi);
  // zobraz
   $vypis.= "<p>";
   $vypis.= "<table cellpadding=\"3\" cellspacing=\"0\" width=\"100%\" align=\"center\">\n";
   for($pom=0;$pom<$pocetkom;$pom++):
     if ($pom/2==round($pom/2)):
     $barva="#BFBFBF";
     else:
     $barva="#D8D8D8";
     endif;
     // $poradi[$pom][0] -> obsahuje hodnotu poradi v poli $data
      $vypis.= "<tr bgcolor=\"".$barva."\" class=\"komz\"><td>";
      $vypis.= "</td><td>";
    // generator mezery
      for($xx=0;$xx<$poradi[$pom][1];$xx++):
       $vypis.= "&nbsp;&nbsp;&nbsp;";
      endfor;
     $vypis.= $data[$poradi[$pom][0]][1]."</td>";
     $vypis.= "<td>".$data[$poradi[$pom][0]][2]."</td><td align=\"right\">".MyDatetimeToStd($data[$poradi[$pom][0]][3])."</td></tr>\n";
   endfor;
  $vypis.= "<tr><td align=\"center\" colspan=\"4\">";
  $vypis.= "</td></tr>\n";
  $vypis.= "</table>\n";
  $vypis.= "</p>\n";
 endif;
  // paticka
   if ($pocetkom!=0):
    $vypis.= "<p align=\"center\" class=\"komlink\">
    <a class=\"gal_tlodkaz\" href=\"gallery.php?akce=comment_fullview&amp;media_id=".$GLOBALS["media_id"]."\">&nbsp;".GAL_COMM_ZOBRAZIT_VSE."&nbsp;</a>";
   endif;
 $vypis.=NovyFormKom();
 return $vypis;
}

function NovyFormKom()
{
 // bezpecnostni korekce
  $GLOBALS["media_id"]=addslashes($GLOBALS["media_id"]);
 // test na existenci reg. ctenare
  if ($GLOBALS["prmyctenar"]->ctenarstav==1):
   $prkdo=$GLOBALS["prmyctenar"]->Ukaz("jmeno");
   $prokdomail=$GLOBALS["prmyctenar"]->Ukaz("email");
  else:
   $prkdo="";
   $prokdomail="@";
  endif;
 // formular pro pridani noveho komentare
  $vypis.= "<form action=\"gallery.php\" method=\"post\">
   <input type=\"hidden\" name=\"akce\" value=\"comment_add\" />
   <input type=\"hidden\" name=\"media_id\" value=\"".$GLOBALS["media_id"]."\" />
   <input type=\"hidden\" name=\"cislokom\" value=\"0\" />
   <p><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
   <tr class=\"komz\"><td><b>".GAL_COMM_NICK.":</b>&nbsp;&nbsp;</td><td><input type=\"text\" size=\"35\" name=\"kod\" value=\"".$prkdo."\" class=\"textpole\" maxlength=\"15\" /></td></tr>
   <tr class=\"komz\"><td><b>".GAL_COMM_MAIL.":</b>&nbsp;&nbsp;</td><td><input type=\"text\" size=\"35\" name=\"kodmail\" value=\"".$prokdomail."\" class=\"textpole\" /></td></tr>
   <tr class=\"komz\"><td><b>".GAL_COMM_TITL.":</b>&nbsp;&nbsp;</td><td><input type=\"text\" size=\"35\" name=\"ktitulek\" class=\"textpole\" maxlength=\"15\" /></td></tr>
   </table></p>
   <p align=\"center\"><textarea name=\"kobsah\" cols=\"40\" rows=\"4\" wrap=\"yes\" class=\"textbox\">".GAL_COMM_MAXI."</textarea></p>
   <div style=\"font-size: 9px\"><strong>[b]</strong>".GAL_COMM_TUCNE."<strong>[/b]</strong> - <strong>[odkaz]</strong>".GAL_COMM_ODKAZ."<strong>[/odkaz]</strong> - <strong>[email]</strong>".GAL_COMM_EMAIL."<strong>[/email]</strong></div>
   <p align=\"center\"><input type=\"submit\" name=\"pridej\" value=\" ".GAL_COMM_ODESLAT." \" class=\"gal_tl\" />&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"reset\" value=\" ".GAL_COMM_RESET." \" class=\"gal_tl\" /></p>
   </form>";
   return $vypis;
}

function KorekceVstupu($txt = "")
{
 $hledam = array ("'&(?!#)'i","'<'i","'>'i","'\"'i");
 $nahrazuji = array ("&amp;","&lt;","&gt;","&quot;");
 $txt=preg_replace($hledam,$nahrazuji,$txt);
 return $txt;
}

function PrelozKomZnacky($txt = "")
{
 $txt=str_replace("[b]","<b>",$txt); // ochrana proti vlozeni JS
 $txt=str_replace("[/b]","</b>",$txt);
 $txt=ZpracujOdkaz("[odkaz]","[/odkaz]",$txt);
 $txt=ZpracujMail("[email]","[/email]",$txt);
 return $txt;
}

function ZpracujMail($start_tag = "", $konec_tag = "", $ret = "")
{
 $vel_start_tag=strlen($start_tag);
 $vel_konec_tag=strlen($konec_tag);
 $chyba=0;
  if ($vel_start_tag>0&&$vel_konec_tag>0&&$ret!=""):
   $nasel=0;  // inic.
   $poz_start_tag=strpos("n".$ret,$start_tag);
   if ($poz_start_tag>0):
    $nasel=1; // nast. true
    $poz_start_tag=$poz_start_tag-1;
   endif;
   while($nasel==1):
    $poz_konec_tag=strpos($ret,$konec_tag);
    if ($poz_start_tag<$poz_konec_tag):
      // vse OK
      $obsah=substr($ret,$poz_start_tag+$vel_start_tag,$poz_konec_tag-$poz_start_tag-$vel_start_tag); // retezec, odkud, delka
      $novy_obsah=str_replace("mailto:","",$obsah);
      $novy_obsah="<a href=\"mailto:".$novy_obsah."\">".$novy_obsah."</a>"; // novy link
      $ret=str_replace($start_tag.$obsah.$konec_tag,$novy_obsah,$ret); // nahrada puvodniho za novy
    else:
      // strukturalni chyba
      $chyba=1;
    break;
    endif;
    $nasel=0;  // nast. false
    $poz_start_tag=strpos($ret,$start_tag);
    if ($poz_start_tag>0):
      $nasel=1; // nast. true
    endif;
   endwhile;
   if ($chyba==1):
    $ret=str_replace($start_tag,"",$ret);
    $ret=str_replace($konec_tag,"",$ret);
   endif;
  endif;
 return $ret;
}

function ZpracujOdkaz($start_tag = "", $konec_tag = "", $ret = "")
{
 $vel_start_tag=strlen($start_tag);
 $vel_konec_tag=strlen($konec_tag);
 $chyba=0;
  if ($vel_start_tag>0&&$vel_konec_tag>0&&$ret!=""):
   $nasel=0;  // inic.
   $poz_start_tag=strpos("n".$ret,$start_tag);
  if ($poz_start_tag>0):
    $nasel=1; // nast. true
    $poz_start_tag=$poz_start_tag-1;
  endif;
  while($nasel==1):
    $poz_konec_tag=strpos($ret,$konec_tag);
    if ($poz_start_tag<$poz_konec_tag):
      // vse OK
      $obsah=substr($ret,$poz_start_tag+$vel_start_tag,$poz_konec_tag-$poz_start_tag-$vel_start_tag); // retezec, odkud, delka
      $novy_obsah=str_replace("http://","",$obsah);
      $novy_obsah="<a href=\"http://".$novy_obsah."\">".$novy_obsah."</a>"; // novy link
      $ret=str_replace($start_tag.$obsah.$konec_tag,$novy_obsah,$ret); // nahrada puvodniho za novy
    else:
      // strukturalni chyba
      $chyba=1;
      break;
    endif;
    $nasel=0;  // nast. false
    $poz_start_tag=strpos($ret,$start_tag);
    if ($poz_start_tag>0):
      $nasel=1; // nast. true
    endif;
  endwhile;
  if ($chyba==1):
    $ret=str_replace($start_tag,"",$ret);
    $ret=str_replace($konec_tag,"",$ret);
  endif;
 endif;
 return $ret;
}



function NovyPridejKom()
{
 // uprava vstupu
  $GLOBALS["media_id"]=KorekceVstupu($GLOBALS["media_id"]);
  $GLOBALS["cislokom"]=KorekceVstupu($GLOBALS["cislokom"]);
  $GLOBALS["kobsah"]=KorekceVstupu($GLOBALS["kobsah"]);
  $GLOBALS["kobsah"]=PrelozKomZnacky($GLOBALS["kobsah"]);
  $GLOBALS["kobsah"]=nl2br($GLOBALS["kobsah"]);
  $GLOBALS["kobsah"]=SubStr($GLOBALS["kobsah"], 0, 2000); //bereme pouze 2000 znaku
  $GLOBALS["kobsah"]=trim($GLOBALS["kobsah"]);
  $GLOBALS["kobsah"]=WordWrap($GLOBALS["kobsah"], 40, "\n", 1);
  $GLOBALS["ktitulek"]=SubStr(KorekceVstupu($GLOBALS["ktitulek"]), 0, 15); // bereme pouze 15 znaku
  $GLOBALS["kod"]=SubStr(KorekceVstupu($GLOBALS["kod"]), 0, 15); // bereme pouze 15 znaku
  $GLOBALS["kodmail"]=KorekceVstupu($GLOBALS["kodmail"]);
 // bezpecnostni korekce
  $GLOBALS["media_id"]=addslashes($GLOBALS["media_id"]);
  $GLOBALS["cislokom"]=addslashes($GLOBALS["cislokom"]);
  $GLOBALS["kobsah"]=addslashes($GLOBALS["kobsah"]);
  $GLOBALS["ktitulek"]=addslashes($GLOBALS["ktitulek"]);
  $GLOBALS["kod"]=addslashes($GLOBALS["kod"]);
  $GLOBALS["kodmail"]=addslashes($GLOBALS["kodmail"]);
  $ip_adresa=$_SERVER["REMOTE_ADDR"]; // ip adresa ctenare
  $aktdatum=Date("Y-m-d H:i:s");
  if ($GLOBALS["ktitulek"]==""): $GLOBALS["ktitulek"]=titulek;  endif;
  if ($GLOBALS["kobsah"]!="" AND $GLOBALS["kod"]!=""):
  // pridani komentare
  @$error=mysql_query("insert into ".$GLOBALS["rspredpona"]."media_komentare values (null,'".$aktdatum."','".$GLOBALS["kobsah"]."','".$GLOBALS["media_id"]."','".$GLOBALS["kod"]."','".$GLOBALS["kodmail"]."','".$ip_adresa."','".$GLOBALS["ktitulek"]."','".$GLOBALS["cislokom"]."')",$GLOBALS["dbspojeni"]);
  if (!$error):
    // chyba
    $vypis.= "<p align=\"center\"><b>".GAL_COMM_KO."</b></p><p align=\"center\" class=\"komlink\"><a class=\"gal_tl_odkaz\" href=\"javascript: history.go(-1);\"> ".GAL_COMM_ZPET." </a></p>\n";
  else:
    Header("Location: gallery.php?akce=obrazek_ukaz&media_id=".$GLOBALS["media_id"]."");
  endif;
  else:
  // chyba: prazdny komentar
  $vypis.= "<p align=\"center\">".GAL_COMM_UDAJE."</p><p align=\"center\" class=\"komlink\"><a class=\"gal_tl_odkaz\" href=\"javascript: history.go(-1);\"> ".GAL_COMM_ZPET." </a></p>\n";
  endif;
  return $vypis;
}


function ZobrazKoKom()
{
// bezpecnostni korekce
$GLOBALS["media_id"]=addslashes($GLOBALS["media_id"]);

$dotazkom=mysql_query("select idk,datum,obsah,od,od_mail,od_ip,titulek,reakce_na from ".$GLOBALS["rspredpona"]."media_komentare where obrazek='".$GLOBALS["media_id"]."' order by idk",$GLOBALS["dbspojeni"]);
$pocetkom=mysql_num_rows($dotazkom);

if ($pocetkom==0):
  $vypis.= "<p align=\"center\" class=\"komz\">".GAL_COMM_ZADNY_KOMENTAR."</p>\n";
else:
  // prevod do pole
  for ($pom=0;$pom<$pocetkom;$pom++):
    // pole informaci
    $data[$pom][0]=mysql_Result($dotazkom,$pom,"idk");     // idk
    $data[$pom][1]=mysql_Result($dotazkom,$pom,"titulek"); // titulek
    $data[$pom][2]=mysql_Result($dotazkom,$pom,"od");      // autor
    $data[$pom][3]=mysql_Result($dotazkom,$pom,"datum");   // datum
    $data[$pom][4]=mysql_Result($dotazkom,$pom,"obsah");   // obsah komentare
    $data[$pom][5]=mysql_Result($dotazkom,$pom,"od_mail"); // mail autora
    $data[$pom][6]=mysql_Result($dotazkom,$pom,"od_ip"); // mail autora
    // pom. pole k setrideni
    $pomporadi[$pom][0]=mysql_Result($dotazkom,$pom,"idk");        // id komentare
    $pomporadi[$pom][1]=mysql_Result($dotazkom,$pom,"reakce_na");  // id predka komentare
    $pomporadi[$pom][2]=0;                                         // nastaveni stavu radku
  endfor;
  // setrideni
  $poradi=SetridKomentare($pomporadi);
  // zjisteni maximalniho levelu
  $maxim_level=0;
  for($pom=0;$pom<$pocetkom;$pom++):
    if ($maxim_level<$poradi[$pom][1]):
      $maxim_level=$poradi[$pom][1];
    endif;
  endfor;
  // preddefinovani zakladniho levelu
  if ($maxim_level>0):
    $vzhled_zakl_level=" colspan=\"".($maxim_level+1)."\"";
  else:
    $vzhled_zakl_level="";
  endif;
  // zobrazeni komentaru
  $vypis.= "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" align=\"center\">\n";
  for($pom=0;$pom<$pocetkom;$pom++):
    $vypis.= "<tr class=\"komz\">\n";
    // $poradi[$pom][0] -> obsahuje hodnotu poradi v poli $data
    if ($poradi[$pom][1]==0):
      // zakladni level
      $vypis.= "<td align=\"left\"".$vzhled_zakl_level.">\n";
    else:
      // sublevel
      for ($p1=0;$p1<$poradi[$pom][1];$p1++):
        $vypis.= "<td align=\"left\">&nbsp;&nbsp;</td>\n";
      endfor;
      $vypis.= "<td align=\"left\" colspan=\"".($maxim_level+1-$poradi[$pom][1])."\">\n";
    endif;
    // hlavicka
    $vypis.= "<div class=\"komhlav\">";
    $vypis.= "<b>".GAL_COMM_ZE_DNE."</b> ".MyDatetimeToStd($data[$poradi[$pom][0]][3])." &nbsp;&nbsp;&nbsp;&nbsp;";
    $vypis.= "<a href=\"gallery.php?akce=comment_re&amp;media_id=".$GLOBALS["media_id"]."&amp;ck=".$data[$poradi[$pom][0]][0]."\">".GAL_COMM_REAGOVAT."</a><br />\n";
    $vypis.= "<b>".GAL_COMM_AUTOR."</b> ".$data[$poradi[$pom][0]][2];
    if ($data[$poradi[$pom][0]][5]!="" AND $data[$poradi[$pom][0]][5]!="@"):
    $vypis.= "<br><b>".GAL_COMM_MAIL.":</b> <a href=\"mailto:".$data[$poradi[$pom][0]][5]."\"> ".$data[$poradi[$pom][0]][5];
    $vypis.= "</a>";
    endif;
    if ($data[$poradi[$pom][0]][6]!="" AND $data[$poradi[$pom][0]][6]!="0.0.0.0"):
    $kdoip = gethostbyaddr("".$data[$poradi[$pom][0]][6]."");
    $vypis.= "<br><b>".GAL_COMM_IP.":</b> <span style=\"cursor: help;\" title=\"".$data[$poradi[$pom][0]][6]."\">".$kdoip."</span>";
    endif;
    $vypis.= "<br />\n";
    $vypis.= "<b>".GAL_COMM_TITL.":</b> ".VycistiKoment($data[$poradi[$pom][0]][1]);
    $vypis.= "</div>\n";
    // telo
    $vypis.= "<div class=\"komtext\">";
    $vypis.= VycistiKoment($data[$poradi[$pom][0]][4]);
    $vypis.= "</div><hr class=\"gal_cara\"><br />\n";
    $vypis.= "</td>\n";
    $vypis.= "</tr>\n";
  endfor;
  $vypis.= "</table>\n";
endif;
// paticka
$vypis.= "<p align=\"center\" class=\"komlink\">".NovyFormKom()."</p>\n";
return $vypis;
}

function NovyReFormKom()
{
// bezpecnostni korekce
$GLOBALS["media_id"]=addslashes($GLOBALS["media_id"]);
$GLOBALS["ck"]=addslashes($GLOBALS["ck"]);

// nacteni puvodniho komentare
$dotazkom=mysql_query("select datum,obsah,od,od_mail,titulek from ".$GLOBALS["rspredpona"]."media_komentare where idk='".$GLOBALS["ck"]."'",$GLOBALS["dbspojeni"]);
list($prdatum,$probsah,$prod,$prodmail,$prtitulek)=mysql_fetch_row($dotazkom);

// zobrazeni puvodni komentare
$vypis.= "<p><table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\" align=\"center\"><tr><td>\n";
$vypis.= "<div class=\"komhlav\">";
$vypis.= "<b>".GAL_COMM_ZE_DNE.":</b> ".MyDatetimeToStd($prdatum)."<br />\n";
$vypis.= "<b>".GAL_COMM_AUTOR.":</b> ".$prod;
if ($prodmail!=""):
  $vypis.= " (".$prodmail.")";
endif;
$vypis.= "<br />\n";
$vypis.= "<b>".GAL_COMM_TITL.":</b> ".$prtitulek;
$vypis.= "</div>\n";
$vypis.= "<div class=\"komtext\">\n";
$vypis.= $probsah;
$vypis.= "</div>\n";
$vypis.= "</td></tr></table></p>\n";

// test na existenci reg. ctenare
if ($GLOBALS["prmyctenar"]->ctenarstav==1):
  $prkdo=$GLOBALS["prmyctenar"]->Ukaz("jmeno");
  $prokdomail=$GLOBALS["prmyctenar"]->Ukaz("email");
else:
  $prkdo="";
  $prokdomail="@";
endif;

// formular na vlozeni odpovedi na predesly komentar
$vypis.= "<p align=\"center\" class=\"komz\"><big><b>".GAL_COMM_REAKCE."<br />\"".$prtitulek."\"</b></big></p>
<form action=\"gallery.php\" method=\"post\">
<input type=\"hidden\" name=\"akce\" value=\"comment_add\" />
<input type=\"hidden\" name=\"media_id\" value=\"".$GLOBALS["media_id"]."\" />
<input type=\"hidden\" name=\"cislokom\" value=\"".$GLOBALS["ck"]."\" />
<p align=\"center\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
<tr class=\"komz\"><td><b>".GAL_COMM_NICK."</b>&nbsp;&nbsp;</td><td><input type=\"text\" size=\"35\" name=\"kod\" value=\"".$prkdo."\" class=\"textpole\" /></td></tr>
<tr class=\"komz\"><td><b>".GAL_COMM_MAIL."</b>&nbsp;&nbsp;</td><td><input type=\"text\" size=\"35\" name=\"kodmail\" value=\"".$prokdomail."\" class=\"textpole\" /></td></tr>
<tr class=\"komz\"><td><b>".GAL_COMM_TITL."</b>&nbsp;&nbsp;</td><td><input type=\"text\" size=\"35\" name=\"ktitulek\" value=\"Re: ".$prtitulek."\" class=\"textpole\" /></td></tr>
</table></p>
<p align=\"center\"><textarea name=\"kobsah\" cols=\"40\" rows=\"4\" wrap=\"yes\" class=\"textbox\">".GAL_COMM_MAXI."</textarea></p>
<div style=\"font-size: 9px\"><strong>[b]</strong>".GAL_COMM_TUCNE."<strong>[/b]</strong> - <strong>[odkaz]</strong>".GAL_COMM_ODKAZ."<strong>[/odkaz]</strong> - <strong>[email]</strong>".GAL_COMM_EMAIL."<strong>[/email]</strong></div>
<p align=\"center\"><input type=\"submit\" name=\"pridej\" value=\" ".GAL_COMM_ODESLAT." \" class=\"gal_tl\" />&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"reset\" value=\" ".GAL_COMM_RESET." \" class=\"gal_tl\" /></p>
</form>
<p align=\"center\" class=\"komlink\"><a href=\"gallery.php?akce=comment_fullview&amp;media_id=".$GLOBALS["media_id"]."\">".GAL_COMM_ZOBRAZIT_VSE."</a></p>
<p></p>\n";
return $vypis;
}



function VycistiKoment($txt)
{
$txt=str_replace("<script","&lt;script",$txt); // ochrana proti vlozeni JS
$txt=str_replace("<body","&lt;body",$txt); // ochrana proti vlozeni body
$txt=str_replace("<applet","&lt;applet",$txt);
$txt=str_replace("<meta","&lt;meta",$txt); // ochrana proti pouziti Refresh
return $txt;
}
?>