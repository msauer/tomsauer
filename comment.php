<?
######################################################################
# phpRS Comment 1.6.0
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_bloky, rs_user, rs_clanky, rs_komentare

/*
  Pro fungovani funkce rozpoznavani registrovanych ctenaru musi byt aktivni trida CMyReader() nalezajici se v souboru myweb.php.
*/

define('IN_CODE',true); // inic. ochranne konstanty

include_once("config.php");
include_once("specfce.php");
include_once("myweb.php");
include_once("sl.php");
include_once("trlayout.php");
include_once($adrlayoutu);

include_once("admin/astdlib_comment.php"); // standardni knihovna komentarovych funkci

// --[pomocne fce]------------------------------------------------------------------------------------

// setrizeni komentaru
function SetridKomentare($vstupnipole)
{
/*
  predpoklada se, ze vstupni pole je jiz chnologociky setrizeno
  -> u komentaru lze tohoto stavu docilit i setrizenim podle id - lze totiz logicky predpokladat, ze nejnizsi id patri nejdrive vlozenemu komentari, atd.

  $vstupnipole[X][0] - id
                 [1] - id predka
                 [2] - pouzito 1/0, defaulne 0 (ne)
*/

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
              [1] - cislo urovne
*/
return $vysledek;
}

// --[zakladni fce]-----------------------------------------------------------------------------------

// zobrazeni vyberoveho prehledu vsech komentaru nalezicich k vybranemu clanku
function ZobrazKom()
{
// bezpecnostni korekce
$GLOBALS["cisloclanku"]=mysql_escape_string($GLOBALS["cisloclanku"]);

$dotaz="select idk,datum,od,titulek,reakce_na,registrovany,reg_prezdivka from ".$GLOBALS["rspredpona"]."komentare where clanek='".$GLOBALS["cisloclanku"]."' order by idk";
$dotazkom=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetkom=mysql_num_rows($dotazkom);

if ($pocetkom==0):
  // chyba - neni prirazen zadny komentar
  echo "<p align=\"center\" class=\"komz\">".RS_KO_NIC."</p>\n";
else:
  // prevod do pole
  for ($pom=0;$pom<$pocetkom;$pom++):
    // nacteni dat z DB
    $akt_pole_data=mysql_fetch_assoc($dotazkom);
    // pole informaci
    $data[$pom]['idk']=$akt_pole_data['idk'];     // idk
    $data[$pom]['titulek']=$akt_pole_data['titulek']; // titulek
    $data[$pom]['od']=$akt_pole_data['od'];       // autor
    $data[$pom]['datum']=$akt_pole_data['datum']; // datum
    $data[$pom]['registr']=$akt_pole_data['registrovany'];    // registrovany ctenar
    $data[$pom]['reg_jmeno']=$akt_pole_data['reg_prezdivka']; // prezdivka registr. ctenare
    // pom. pole k setrideni
    $pomporadi[$pom][0]=$akt_pole_data['idk'];        // id komentare
    $pomporadi[$pom][1]=$akt_pole_data['reakce_na'];  // id predka komentare
    $pomporadi[$pom][2]=0;                            // nastaveni stavu radku
  endfor;
  // setrideni
  $poradi=SetridKomentare($pomporadi);
  // zobraz
  echo "<form action=\"comment.php\" method=\"post\">\n";
  echo "<input type=\"hidden\" name=\"akce\" value=\"selectview\" /><input type=\"hidden\" name=\"cisloclanku\" value=\"".$GLOBALS["cisloclanku"]."\" />\n";
  echo "<table border=\"0\" cellpadding=\"2\" align=\"center\">\n";
  for($pom=0;$pom<$pocetkom;$pom++):
    $akt_komentar=$poradi[$pom][0]; // $poradi[$pom][0] -> obsahuje hodnotu poradi v poli $data
    // vypis radku
    echo "<tr class=\"komz\"><td><input type=\"checkbox\" name=\"kclanek[]\" value=\"".$data[$akt_komentar]['idk']."\" /></td>";
    echo "<td>";
    // generator mezery
    if ($poradi[$pom][1]>0):
      echo str_repeat('&nbsp;&nbsp;&nbsp;',$poradi[$pom][1]);
    endif;
    echo $data[$akt_komentar]['titulek']."</td>";
    // test na registraci ctenare
    if ($data[$akt_komentar]['registr']==1):
      echo "<td>[".$data[$akt_komentar]['reg_jmeno']."] - ".$data[$akt_komentar]['od']."</td>";
    else:
      echo "<td>".RS_KO_NEREG." - ".$data[$akt_komentar]['od']."</td>";
    endif;
    echo "<td align=\"right\">".MyDatetimeToStd($data[$akt_komentar]['datum'])."</td></tr>\n";
  endfor;
  echo "<tr><td align=\"center\" colspan=\"4\"><input type=\"submit\" value=\" ".RS_KO_ZOBRAZ_OZN." \" class=\"tl\" /></td></tr>\n";
  echo "</table>\n";
  echo "</form>\n";
endif;
// paticka
echo "<p align=\"center\" class=\"komlink\">
<a href=\"comment.php?akce=fullview&amp;cisloclanku=".$GLOBALS["cisloclanku"]."\">".RS_KO_ZOBRAZ_VSE."</a>
&nbsp;&nbsp;
<a href=\"comment.php?akce=new&amp;cisloclanku=".$GLOBALS["cisloclanku"]."\">".RS_KO_PRIDAT."</a>
</p>\n";
}

// zobrazeni vsech komentaru pridanych k vybranemu clanku
function ZobrazKoKom()
{
// bezpecnostni korekce
$GLOBALS["cisloclanku"]=mysql_escape_string($GLOBALS["cisloclanku"]);

$dotaz="select idk,datum,obsah,od,od_mail,titulek,reakce_na,registrovany,reg_prezdivka from ".$GLOBALS["rspredpona"]."komentare where clanek='".$GLOBALS["cisloclanku"]."' order by idk";
$dotazkom=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetkom=mysql_num_rows($dotazkom);

if ($pocetkom==0):
  // chyba - neni prirazen zadny komentar
  echo "<p align=\"center\" class=\"komz\">".RS_KO_NIC."</p>\n";
else:
  // pridavaci link
  echo "<p align=\"center\" class=\"komlink\"><a href=\"comment.php?akce=new&amp;cisloclanku=".$GLOBALS["cisloclanku"]."\">".RS_KO_PRIDAT."</a></p>\n";
  // prevod do pole
  for ($pom=0;$pom<$pocetkom;$pom++):
    // nacteni dat z DB
    $akt_pole_data=mysql_fetch_assoc($dotazkom);
    // pole informaci
    $data[$pom][0]=$akt_pole_data['idk'];     // idk
    $data[$pom][1]=$akt_pole_data['titulek']; // titulek
    $data[$pom][2]=$akt_pole_data['od'];      // autor
    $data[$pom][3]=$akt_pole_data['datum'];   // datum
    $data[$pom][4]=$akt_pole_data['obsah'];   // obsah komentare
    $data[$pom][5]=$akt_pole_data['od_mail']; // mail autora
    $data[$pom][6]=$akt_pole_data['registrovany'];  // registrovany ctenar
    $data[$pom][7]=$akt_pole_data['reg_prezdivka']; // prezdivka registr. ctenare
    // pom. pole k setrideni
    $pomporadi[$pom][0]=$akt_pole_data['idk'];        // id komentare
    $pomporadi[$pom][1]=$akt_pole_data['reakce_na'];  // id predka komentare
    $pomporadi[$pom][2]=0;                            // nastaveni stavu radku
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
  echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n";
  for($pom=0;$pom<$pocetkom;$pom++):
    $akt_komentar=$poradi[$pom][0]; // $poradi[$pom][0] -> obsahuje hodnotu poradi v poli $data
    // vypis radku
    echo "<tr class=\"komz\">\n";
    if ($poradi[$pom][1]==0):
      // zakladni level
      echo "<td align=\"left\"".$vzhled_zakl_level.">\n";
    else:
      // sublevel
      if ($poradi[$pom][1]>0):
        echo str_repeat('<td align="left">&nbsp;&nbsp;</td>',$poradi[$pom][1])."\n";
      endif;
      echo "<td align=\"left\" colspan=\"".($maxim_level+1-$poradi[$pom][1])."\">\n";
    endif;
    // hlavicka
    echo "<div class=\"komhlav\">";
    echo "<b>".RS_KO_ZPR_ZE_DNE.":</b> ".MyDatetimeToStd($data[$akt_komentar][3])." &nbsp;&nbsp;&nbsp;&nbsp;";
    echo "<a href=\"comment.php?akce=re&amp;cisloclanku=".$GLOBALS["cisloclanku"]."&amp;ck=".$data[$akt_komentar][0]."\">".RS_KO_ZPR_REG."</a><br />\n";
    echo "<b>".RS_KO_ZPR_AUT.":</b> ";
    if ($data[$akt_komentar][6]==1): // test na registraci ctenare
      echo "[".$data[$pom][7]."] - ".$data[$akt_komentar][2];
    else:
      echo RS_KO_NEREG." - ".$data[$akt_komentar][2];
    endif;
    if ($data[$akt_komentar][5]!=''): // test na existenci e-mailu
      echo " (".$data[$akt_komentar][5].")";
    endif;
    echo "<br />\n";
    echo "<b>".RS_KO_ZPR_TIT.":</b> ".VycistiKoment($data[$akt_komentar][1]);
    echo "</div>\n";
    // telo
    echo "<div class=\"komtext\">";
    echo VycistiKoment($data[$akt_komentar][4]);
    echo "</div><br />\n";
    echo "</td>\n";
    echo "</tr>\n";
  endfor;
  echo "</table>\n";
endif;
// paticka
echo "<p align=\"center\" class=\"komlink\"><a href=\"comment.php?akce=new&amp;cisloclanku=".$GLOBALS["cisloclanku"]."\">".RS_KO_PRIDAT."</a></p>\n";
}

// zobrazeni jen pozadovanych komentaru
function ZobrazVyKom()
{
// bezpecnostni korekce
$GLOBALS["cisloclanku"]=mysql_escape_string($GLOBALS["cisloclanku"]);

// zjisteni poctu vybranych komentaru + sestaveni omezujiciho pole
if (isset($GLOBALS["kclanek"])):
  $pocetvybr=count($GLOBALS["kclanek"]);
  // sestaveni omezujiciho pole
  for ($pom=0;$pom<$pocetvybr;$pom++):
    $pole_pov_id_kom[addslashes($GLOBALS["kclanek"][$pom])]=1;
  endfor;
else:
  $pocetvybr=0;
  echo "<p align=\"center\">".RS_KO_PRAZDNY_VYB."</p>\n";
endif;

$dotaz="select idk,datum,obsah,od,od_mail,titulek,reakce_na,registrovany,reg_prezdivka from ".$GLOBALS["rspredpona"]."komentare where clanek='".$GLOBALS["cisloclanku"]."' order by idk";
$dotazkom=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetkom=mysql_num_rows($dotazkom);

if ($pocetkom>0&&$pocetvybr>0):
  // pridavaci link
  echo "<p align=\"center\" class=\"komlink\"><a href=\"comment.php?akce=new&amp;cisloclanku=".$GLOBALS["cisloclanku"]."\">".RS_KO_PRIDAT."</a></p>\n";
  // prevod do pole
  for ($pom=0;$pom<$pocetkom;$pom++):
    // nacteni dat z DB
    $akt_pole_data=mysql_fetch_assoc($dotazkom);
    // pole informaci
    $data[$pom][0]=$akt_pole_data['idk'];     // idk
    $data[$pom][1]=$akt_pole_data['titulek']; // titulek
    $data[$pom][2]=$akt_pole_data['od'];      // autor
    $data[$pom][3]=$akt_pole_data['datum'];   // datum
    $data[$pom][4]=$akt_pole_data['obsah'];   // obsah komentare
    $data[$pom][5]=$akt_pole_data['od_mail']; // mail autora
    if (isset($pole_pov_id_kom[$akt_pole_data['idk']])):
      $data[$pom][6]=1; // zobrazit komentar - ANO
    else:
      $data[$pom][6]=0; // zobrazit komentar - NE
    endif;
    $data[$pom][7]=$akt_pole_data['registrovany'];  // registrovany ctenar
    $data[$pom][8]=$akt_pole_data['reg_prezdivka']; // prezdivka registr. ctenare
    // pom. pole k setrideni
    $pomporadi[$pom][0]=$akt_pole_data['idk'];        // id komentare
    $pomporadi[$pom][1]=$akt_pole_data['reakce_na'];  // id predka komentare
    $pomporadi[$pom][2]=0;                            // nastaveni stavu radku
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
  echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n";
  for($pom=0;$pom<$pocetkom;$pom++):
    $akt_komentar=$poradi[$pom][0]; // $poradi[$pom][0] -> obsahuje hodnotu poradi v poli $data
    // vypis radku
    if ($data[$akt_komentar][6]==1): // test na povoleni zobrazeni komentare
      echo "<tr class=\"komz\">\n";
      if ($poradi[$pom][1]==0):
        // zakladni level
        echo "<td align=\"left\"".$vzhled_zakl_level.">\n";
      else:
        // sublevel
        if ($poradi[$pom][1]>0):
          echo str_repeat('<td align="left">&nbsp;&nbsp;</td>',$poradi[$pom][1])."\n";
        endif;
        echo "<td align=\"left\" colspan=\"".($maxim_level+1-$poradi[$pom][1])."\">\n";
      endif;
      // hlavicka
      echo "<div class=\"komhlav\">";
      echo "<b>".RS_KO_ZPR_ZE_DNE.":</b> ".MyDatetimeToStd($data[$akt_komentar][3])." &nbsp;&nbsp;&nbsp;&nbsp;";
      echo "<a href=\"comment.php?akce=re&amp;cisloclanku=".$GLOBALS["cisloclanku"]."&amp;ck=".$data[$akt_komentar][0]."\">".RS_KO_ZPR_REG."</a><br />\n";
      echo "<b>".RS_KO_ZPR_AUT.":</b> ";
      if ($data[$akt_komentar][7]==1): // test na registraci ctenare
        echo "[".$data[$pom][8]."] - ".$data[$akt_komentar][2];
      else:
        echo RS_KO_NEREG." - ".$data[$akt_komentar][2];
      endif;
      if ($data[$akt_komentar][5]!=''): // test na existenci e-mailu
        echo " (".$data[$akt_komentar][5].")";
      endif;
      echo "<br />\n";
      echo "<b>".RS_KO_ZPR_TIT.":</b> ".VycistiKoment($data[$akt_komentar][1]);
      echo "</div>\n";
      // telo
      echo "<div class=\"komtext\">";
      echo VycistiKoment($data[$akt_komentar][4]);
      echo "</div><br />\n";
      echo "</td>\n";
      echo "</tr>\n";
    endif;
  endfor;
  echo "</table>\n";
endif;
// paticka
echo "<p align=\"center\" class=\"komlink\">
<a href=\"comment.php?akce=fullview&amp;cisloclanku=".$GLOBALS["cisloclanku"]."\">".RS_KO_ZOBRAZ_VSE."</a>
&nbsp;&nbsp;
<a href=\"comment.php?akce=new&amp;cisloclanku=".$GLOBALS["cisloclanku"]."\">".RS_KO_PRIDAT."</a>
</p>\n";
}

function NovyFormKom()
{
// bezpecnostni korekce
$GLOBALS["cisloclanku"]=mysql_escape_string($GLOBALS["cisloclanku"]);

// test na existenci reg. ctenare
if ($GLOBALS["prmyctenar"]->ctenarstav==1):
  $prctenar=$GLOBALS["prmyctenar"]->Ukaz("jmeno");
  $prctenar_mail=$GLOBALS["prmyctenar"]->Ukaz("email");
else:
  $prctenar='';
  $prctenar_mail='@';
endif;
// formular pro pridani noveho komentare
echo "<form action=\"comment.php\" method=\"post\">
<input type=\"hidden\" name=\"akce\" value=\"insert\" />
<input type=\"hidden\" name=\"cisloclanku\" value=\"".$GLOBALS["cisloclanku"]."\" />
<input type=\"hidden\" name=\"cislokom\" value=\"0\" />
<p><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
<tr class=\"komz\"><td><b>".RS_KO_ZPR_JME.":</b>&nbsp;</td><td><input type=\"text\" size=\"40\" name=\"kod\" value=\"".$prctenar."\" class=\"textpole\" /></td></tr>
<tr class=\"komz\"><td><b>".RS_KO_ZPR_EMAIL.":</b>&nbsp;</td><td><input type=\"text\" size=\"40\" name=\"kodmail\" value=\"".$prctenar_mail."\" class=\"textpole\" /></td></tr>
<tr class=\"komz\"><td><b>".RS_KO_ZPR_TIT.":</b>&nbsp;</td><td><input type=\"text\" size=\"40\" name=\"ktitulek\" class=\"textpole\" /></td></tr>
</table></p>
<p align=\"center\"><textarea name=\"kobsah\" cols=\"60\" rows=\"16\" wrap=\"yes\" class=\"textbox\"></textarea></p>
<p align=\"center\"><input type=\"submit\" name=\"pridej\" value=\" ".RS_ODESLAT." \" class=\"tl\" />&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"reset\" value=\" ".RS_RESET." \" class=\"tl\" /></p>
<p align=\"center\" class=\"komz\">".RS_KO_INFO."</p>
</form>
<p align=\"center\" class=\"komlink\"><a href=\"comment.php?akce=view&amp;cisloclanku=".$GLOBALS["cisloclanku"]."\">".RS_KO_ZOBRAZ_KOM."</a></p>
<p></p>\n";
}

function NovyReFormKom()
{
// bezpecnostni korekce
$GLOBALS["cisloclanku"]=mysql_escape_string($GLOBALS["cisloclanku"]);
$GLOBALS["ck"]=mysql_escape_string($GLOBALS["ck"]);

// nacteni puvodniho komentare
$dotazkom=mysql_query("select datum,obsah,od,od_mail,titulek from ".$GLOBALS["rspredpona"]."komentare where idk='".$GLOBALS["ck"]."'",$GLOBALS["dbspojeni"]);
list($prdatum,$probsah,$prod,$prodmail,$prtitulek)=mysql_fetch_row($dotazkom);

// zobrazeni puvodni komentare
echo "<p><table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\"><tr><td>\n";
echo "<div class=\"komhlav\">";
echo "<b>".RS_KO_ZPR_ZE_DNE.":</b> ".MyDatetimeToStd($prdatum)."<br />\n";
echo "<b>".RS_KO_ZPR_AUT.":</b> ".$prod;
if ($prodmail!=''):
  echo " (".$prodmail.")";
endif;
echo "<br />\n";
echo "<b>".RS_KO_ZPR_TIT.":</b> ".$prtitulek;
echo "</div>\n";
echo "<div class=\"komtext\">\n";
echo $probsah;
echo "</div>\n";
echo "</td></tr></table></p>\n";

// test na existenci reg. ctenare
if ($GLOBALS["prmyctenar"]->ctenarstav==1):
  $prctenar=$GLOBALS["prmyctenar"]->Ukaz("jmeno");
  $prctenar_mail=$GLOBALS["prmyctenar"]->Ukaz("email");
else:
  $prctenar='';
  $prctenar_mail='@';
endif;

// formular na vlozeni odpovedi na predesly komentar
echo "<p align=\"center\" class=\"komz\"><big><b>".RS_KO_RE_NA_KOM."<br />\"".$prtitulek."\"</b></big></p>
<form action=\"comment.php\" method=\"post\">
<input type=\"hidden\" name=\"akce\" value=\"insert\" />
<input type=\"hidden\" name=\"cisloclanku\" value=\"".$GLOBALS["cisloclanku"]."\" />
<input type=\"hidden\" name=\"cislokom\" value=\"".$GLOBALS["ck"]."\" />
<p align=\"center\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
<tr class=\"komz\"><td><b>".RS_KO_ZPR_JME.":</b>&nbsp;</td><td><input type=\"text\" size=\"40\" name=\"kod\" value=\"".$prctenar."\" class=\"textpole\" /></td></tr>
<tr class=\"komz\"><td><b>".RS_KO_ZPR_EMAIL.":</b>&nbsp;</td><td><input type=\"text\" size=\"40\" name=\"kodmail\" value=\"".$prctenar_mail."\" class=\"textpole\" /></td></tr>
<tr class=\"komz\"><td><b>".RS_KO_ZPR_TIT.":</b>&nbsp;</td><td><input type=\"text\" size=\"40\" name=\"ktitulek\" value=\"Re: ".$prtitulek."\" class=\"textpole\" /></td></tr>
</table></p>
<p align=\"center\"><textarea name=\"kobsah\" cols=\"60\" rows=\"16\" wrap=\"yes\" class=\"textbox\"></textarea></p>
<p align=\"center\"><input type=\"submit\" name=\"pridej\" value=\" ".RS_ODESLAT." \" class=\"tl\" />&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"reset\" value=\" ".RS_RESET." \" class=\"tl\" /></p>
<p align=\"center\" class=\"komz\">".RS_KO_INFO."</p>
</form>
<p align=\"center\" class=\"komlink\"><a href=\"comment.php?akce=view&amp;cisloclanku=".$GLOBALS["cisloclanku"]."\">".RS_KO_ZOBRAZ_KOM."</a></p>
<p></p>\n";
}

function NovyPridejKom()
{
// uprava vstupu
$GLOBALS["cisloclanku"]=KorekceVstupu($GLOBALS["cisloclanku"]);
$GLOBALS["cislokom"]=KorekceVstupu($GLOBALS["cislokom"]);
$GLOBALS["kobsah"]=KorekceVstupu($GLOBALS["kobsah"]);
$GLOBALS["kobsah"]=PrelozKomZnacky($GLOBALS["kobsah"]); // prelozeni kom. znacek
$GLOBALS["kobsah"]=KorekceVelikosti($GLOBALS["kobsah"]); // omezeni velikosti
$GLOBALS["kobsah"]=nl2br($GLOBALS["kobsah"]);
$GLOBALS["ktitulek"]=KorekceVstupu($GLOBALS["ktitulek"]);
$GLOBALS["ktitulek"]=KorekceVelikosti($GLOBALS["ktitulek"]); // omezeni velikosti
$GLOBALS["kod"]=KorekceVstupu($GLOBALS["kod"]);
$GLOBALS["kodmail"]=KorekceVstupu($GLOBALS["kodmail"]);
// bezpecnostni korekce
$GLOBALS["cisloclanku"]=mysql_escape_string($GLOBALS["cisloclanku"]);
$GLOBALS["cislokom"]=mysql_escape_string($GLOBALS["cislokom"]);
$GLOBALS["kobsah"]=mysql_escape_string($GLOBALS["kobsah"]);
$GLOBALS["ktitulek"]=mysql_escape_string($GLOBALS["ktitulek"]);
$GLOBALS["kod"]=mysql_escape_string($GLOBALS["kod"]);
$GLOBALS["kodmail"]=mysql_escape_string($GLOBALS["kodmail"]);

$ip_adresa=$_SERVER["REMOTE_ADDR"]; // ip adresa ctenare
$aktdatum=Date("Y-m-d H:i:s");

if ($GLOBALS["kobsah"]==''):
  // chyba - prazdny komentar
  echo "<p align=\"center\">".RS_KO_ERR2."</p>\n";
else:
  // test na existenci reg. ctenare
  if ($GLOBALS['prmyctenar']->ctenarstav==1):
    $nast_registrovany=1;
    $nast_reg_prezdivka=$GLOBALS['prmyctenar']->Ukaz('username');
    $nast_reg_id=$GLOBALS['prmyctenar']->Ukaz('id');
  else:
    $nast_registrovany=0;
    $nast_reg_prezdivka='';
    $nast_reg_id=0;
  endif;
  // sestaveni dotazu
  $dotaz="insert into ".$GLOBALS["rspredpona"]."komentare values ";
  $dotaz.="(null,'".$aktdatum."','".$GLOBALS["kobsah"]."','".$GLOBALS["cisloclanku"]."','".$GLOBALS["kod"]."','".$GLOBALS["kodmail"]."','".$ip_adresa."',";
  $dotaz.="'".$GLOBALS["ktitulek"]."','".$GLOBALS["cislokom"]."','".$nast_registrovany."','".$nast_reg_prezdivka."','".$nast_reg_id."')";
  // pridani komentare
  @$error=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
  if (!$error):
    // chyba - neuspesny sql prikaz
    echo "<p align=\"center\">".RS_KO_ERR1."</p>\n";
  else:
    // vse je OK; pripocitani 1 komentare k celkovemu poctu
    @mysql_query("update ".$GLOBALS["rspredpona"]."clanky set kom=(kom+1) where link='".$GLOBALS["cisloclanku"]."'",$GLOBALS["dbspojeni"]);
    echo "<p align=\"center\">".RS_KO_VLOZEN_OK."</p>\n";
  endif;
endif;
// paticka
echo "<p align=\"center\" class=\"komlink\"><a href=\"comment.php?akce=view&amp;cisloclanku=".$GLOBALS["cisloclanku"]."\">".RS_KO_ZOBRAZ_KOM."</a></p>\n";
echo "<p></p>\n";
}

function Chyba($idchyba = 0)
{
switch ($idchyba):
  case 0: echo "<p align=\"center\">".RS_KO_ERR5."</p>\n"; break; // defaultni chyba
  case 1: echo "<p align=\"center\">".RS_KO_ERR3."</p>\n"; break; // chybi cislo (link) clanku
  case 2: echo "<p align=\"center\">".RS_KO_ERR4."</p>\n"; break; // chybi clanek (vstupni link neukazuje na zadny clanek)
endswitch;
}

// overeni existence potrebnych promennych
if (!isset($GLOBALS["cisloclanku"])): $GLOBALS["akce"]="chyba1"; endif;
if (!isset($GLOBALS["akce"])): $GLOBALS["akce"]="view"; endif;

// bezpecnostni korekce
$GLOBALS["cisloclanku"]=mysql_escape_string($GLOBALS["cisloclanku"]);

// kontrola existence clanku
$dotazclanek=mysql_query("select titulek,datum,autor from ".$GLOBALS["rspredpona"]."clanky where link='".$GLOBALS["cisloclanku"]."'",$GLOBALS["dbspojeni"]);
if (mysql_num_rows($dotazclanek)==0):
  // clanek neexistuje
  $GLOBALS["akce"]="chyba2";
else:
  // ziskani dat o clanku
  list($clatitulek,$cladatum,$claautor)=mysql_fetch_row($dotazclanek);
  $cladatum=MyDatetimeToDate($cladatum); // vysledek dd.mm.rrrr
  // ziskani informaci o autorovi clanku
  $dotazautori=mysql_query("select idu,jmeno,email from ".$GLOBALS["rspredpona"]."user where idu=".$claautor,$GLOBALS["dbspojeni"]);
  if (mysql_num_rows($dotazautori)==1):
    $claautor_jm=mysql_Result($dotazautori,0,"jmeno");
    $claautor_mail='mailto:'.mysql_Result($dotazautori,0,"email");
  else:
    $claautor_jm='';
    $claautor_mail='';
  endif;
endif;

// Tvorba stranky
$vzhledwebu->Generuj();
ObrTabulka();  // Vlozeni layout prvku

if ($akce!="chyba1"||$akce!="chyba2"): // zobraz jen, kdyz je vse OK
  echo "<p align=\"center\" class=\"z\">\n";
  echo "<span class=\"nadpis\">".RS_KO_NADPIS."</span><br />".RS_KO_KE_CLA.": ".$clatitulek."<br />"; // Komentar - ke clanku - X
  echo "(".RS_KO_ZE_DNE." ".$cladatum.", ".RS_KO_AUTOR_CLA.": <a href=\"".$claautor_mail."\">".$claautor_jm."</a>)"; // ze dne - X - autor clanku - X
  echo "<p>\n";
endif;

// rozhodnuti o obsahu stranky
switch($akce):
  case "view": ZobrazKom(); break;
  case "fullview": ZobrazKoKom(); break;
  case "selectview": ZobrazVyKom(); break;
  case "new": NovyFormKom(); break;
  case "re": NovyReFormKom(); break;
  case "insert": NovyPridejKom(); break;
  case "chyba1": Chyba(1); break;
  case "chyba2": Chyba(2); break;
endswitch;

if ($GLOBALS["akce"]!="chyba1"||$GLOBALS["akce"]!="chyba2"): // zobraz jen, kdyz je vse OK
  echo "<p align=\"center\" class=\"komlink\"><a href=\"view.php?cisloclanku=".$GLOBALS["cisloclanku"]."\">".RS_KO_ZOBRAZ_CLA." ".$clatitulek."</a></p>\n";
endif;

// Dokonceni tvorby stranky
KonecObrTabulka();  // Vlozeni layout prvku
$vzhledwebu->Generuj();
?>