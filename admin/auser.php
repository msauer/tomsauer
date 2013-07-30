<?

######################################################################
# phpRS Administration Engine - User's section 1.4.3
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_user, rs_clanky, rs_user_prava

/*
  Tento soubor zajistuje spravu uzivatelu a delegovani pristupovych prav.
*/

if ($Uzivatel->StavSession!=1):
  echo "<html><body><div align=\"center\">Tento soubor neni urcen k vnejsimu spousteni!</div></body></html>";
  exit();
endif;

// ---[rozcestnik]------------------------------------------------------------------
switch($GLOBALS['akce']):
     // uzivatele
     case "ShowUser": AdminMenu();
          echo "<h2 align=\"center\">".RS_USR_ROZ_SPRAVA_USER."</h2><p align=\"center\">\n";
          ShowUser();
          break;
     case "AddUser": AdminMenu();
          echo "<h2 align=\"center\">".RS_USR_ROZ_ADD_USER."</h2><p align=\"center\">\n";
          AddUser();
          break;
     case "AcAddUser": AdminMenu();
          echo "<h2 align=\"center\">".RS_USR_ROZ_ADD_USER."</h2><p align=\"center\">\n";
          AcAddUser();
          break;
     case "DelUser": AdminMenu();
          echo "<h2 align=\"center\">".RS_USR_ROZ_DEL_USER."</h2><p align=\"center\">\n";
          DelUser();
          break;
     case "EditUser": AdminMenu();
          echo "<h2 align=\"center\">".RS_USR_ROZ_EDIT_USER."</h2><p align=\"center\">\n";
          EditUser();
          break;
     case "AcEditUser": AdminMenu();
          echo "<h2 align=\"center\">".RS_USR_ROZ_EDIT_USER."</h2><p align=\"center\">\n";
          AcEditUser();
          break;
     case "PravaUser": AdminMenu();
          echo "<h2 align=\"center\">".RS_USR_ROZ_KONFIG_USER."</h2><p align=\"center\">\n";
          PravaUser();
          break;
     case "AcPravaUser": AdminMenu();
          echo "<h2 align=\"center\">".RS_USR_ROZ_KONFIG_USER."</h2><p align=\"center\">\n";
          AcPravaUser();
          break;
     case "VazbaUser": AdminMenu();
          echo "<h2 align=\"center\">".RS_USR_ROZ_KONFIG_USER."</h2><p align=\"center\">\n";
          VazbaUser();
          break;
     case "AcVazbaUser": AdminMenu();
          echo "<h2 align=\"center\">".RS_USR_ROZ_KONFIG_USER."</h2><p align=\"center\">\n";
          AcVazbaUser();
          break;
     case "DelVazbaUser": AdminMenu();
          echo "<h2 align=\"center\">".RS_USR_ROZ_KONFIG_USER."</h2><p align=\"center\">\n";
          DelVazbaUser();
          break;
endswitch;
echo "</p>\n"; // zakonceni P tagu

// ---[pomocne fce]-----------------------------------------------------------------

/*
  UpravPrava($idsekce = 0, $iduzivatel = 0, $stav = 0)
  NastavAllPrava($iduzivatel = 0, $stav = 0)
  OvereniCizichPrav($seznam_prav = '', $iduzivatele = 0)
  OptOmzSezUziv($typ = 0, $hledam = 0)
*/

function UpravPrava($idsekce = 0, $iduzivatel = 0, $stav = 0)
{
// stav: 1 - pridat prava, 0 - odebrat prava

$dotazsek=mysql_query("select fks_prava_users from ".$GLOBALS["rspredpona"]."moduly_prava where idm='".$idsekce."'",$GLOBALS["dbspojeni"]);
$pocetsek=mysql_num_rows($dotazsek);

if ($pocetsek>0):
  // sekce existuje
  $nalezenaprava=mysql_result($dotazsek,0,"fks_prava_users");
  $uzivatele=explode(":",$nalezenaprava); // prevod seznamu povolenych uzivatelu do pole
  $pocetuzivatelu=count($uzivatele);

  $novyseznam=""; // inic. novy seznam
  $spojka=""; // inic. spojovaciho retezce

  for ($pom=0;$pom<$pocetuzivatelu;$pom++):
    // vytvoreni noveho seznamu, ktery nebude obsahovat $iduzivatel
    if ($iduzivatel!=$uzivatele[$pom]&&$uzivatele[$pom]!=""):
      $novyseznam.=$spojka.$uzivatele[$pom];
      $spojka=":";
    endif;
  endfor;

  if ($stav==1): // pridani pristupoveho prava pro $iduzivatel
    $novyseznam.=$spojka.$iduzivatel;
  endif;

  @$error=mysql_query("update ".$GLOBALS["rspredpona"]."moduly_prava set fks_prava_users='".$novyseznam."' where idm='".$idsekce."'",$GLOBALS["dbspojeni"]);
  return $error; // 0 - chyba, 1 - vse OK
else:
  return 0; // sekce nenalezena
endif;
}

function NastavAllPrava($iduzivatel = 0, $stav = 0)
{
// stav: 1 - pridat prava, 0 - odebrat prava

// z nastaveni jsou vyrazeny: globalni moduly
$dotazprav=mysql_query("select idm from ".$GLOBALS["rspredpona"]."moduly_prava where all_prava_users=0",$GLOBALS["dbspojeni"]);
$pocetprav=mysql_num_rows($dotazprav);

for ($pom=0;$pom<$pocetprav;$pom++):
  UpravPrava(mysql_result($dotazprav,$pom,"idm"),$iduzivatel,$stav); // nastaveni pristupoveho prava ke konkretni sekci
endfor;
}

function OvereniCizichPrav($seznam_prav = '', $iduzivatele = 0)
{
if (empty($seznam_prav)||$iduzivatele==0):
  // prazdny vstup
  return 0;
else:
  $uzivatele=explode(":",$seznam_prav); // prevod seznamu povolenych uzivatelu do pole
  $pocetuzivatelu=count($uzivatele);

  $aktivni=0; // defaultne pristup zamitnut

  for ($pom=0;$pom<$pocetuzivatelu;$pom++):
    if ($uzivatele[$pom]==$iduzivatele): $aktivni=1; break; endif; // pristupove pravo potvrzeno
  endfor;

  return $aktivni;
endif;
}

function OptOmzSezUziv($typ = 0, $hledam = 0)
{
$vysl="";

$dotazusr=mysql_query("select idu,user,jmeno from ".$GLOBALS["rspredpona"]."user where admin='".$typ."' order by user",$GLOBALS["dbspojeni"]);
$pocetusr=mysql_num_rows($dotazusr);

if ($pocetusr==0):
  // CHYBA: Neexistuje žádný odpovídající uživatel!
  $vysl.="<option value=\"0\">".RS_USR_POM_ZADNY_USER."</option>\n";
else:
  for ($pom=0;$pom<$pocetusr;$pom++):
    $pole_data=mysql_fetch_assoc($dotazusr);
    $vysl.="<option value=\"".$pole_data["idu"]."\"";
    if ($hledam==$pole_data["idu"]): $vysl.=" selected"; endif;
    $vysl.=">".$pole_data["user"]." / ".$pole_data["jmeno"]."</option>\n";
  endfor;
endif;

return $vysl;
}

function OptJazyky($hledam = '')
{
$vysl='';

$adr = dir("lang");
while($polozka=$adr->read()):
  if ($polozka!='.'&&$polozka!='..'):
    if (is_dir($adr->path.'/'.$polozka)):
      $vysl.="<option value=\"".$polozka."\"";
      if ($polozka==$hledam): $vysl.=" selected"; endif;
      $vysl.=">".$polozka."</option>";
    endif;
  endif;
endwhile;
$adr->close();

return $vysl;
}

// ---[hlavni fce]------------------------------------------------------------------

/*
  ShowUser()
  AddUser()
  AcAddUser()
  DelUser()
  EditUser()
  AcEditUser()
*/

function ShowUser()
{
// link
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=AddUser&amp;modul=users\">".RS_USR_SU_PRIDAT_USER."</a></p>\n";
// vypis uzivatelu
$dotazusr=mysql_query("select idu,user,jmeno,admin,blokovat from ".$GLOBALS["rspredpona"]."user order by user",$GLOBALS["dbspojeni"]);
$pocetusr=mysql_num_rows($dotazusr);
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">
<tr class=\"txt\" bgcolor=\"#E6E6E6\">
<td align=\"center\"><b>".RS_USR_SU_UZIVATEL."</b></td>
<td align=\"center\"><b>".RS_USR_SU_JMENO."</b></td>
<td align=\"center\"><b>".RS_USR_SU_TYP."</b></td>
<td align=\"center\"><b>".RS_USR_SU_STAV."</b></td>
<td align=\"center\"><b>".RS_USR_SU_AKCE."</b></td>
<td align=\"center\"><b>".RS_USR_SU_SMAZ."</b></td></tr>\n";
if ($pocetusr==0):
  echo "<tr class=\"txt\"><td colspan=\"5\" align=\"center\">".RS_USR_SU_ZADNY_USER."</td></tr>\n";
else:
  for ($pom=0;$pom<$pocetusr;$pom++):
    $pole_data=mysql_fetch_assoc($dotazusr);
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    echo "<td align=\"left\">".$pole_data["user"]."</td>\n";
    echo "<td align=\"left\">".TestNaNic($pole_data["jmeno"])."</td>\n";
    echo "<td align=\"center\">";
    switch ($pole_data["admin"]):
      case 0: echo RS_USR_SU_AUTOR; break; // autor
      case 1: echo RS_USR_SU_REDAKTOR; break; // redaktor
      case 2: echo RS_USR_SU_ADMIN; break; // admin
    endswitch;
    echo "</td>\n";
    if ($pole_data["blokovat"]==1):
      echo "<td align=\"center\">".RS_USR_SU_BLOKOVAT."</td>\n"; // blokovano
    else:
      echo "<td align=\"center\">".RS_USR_SU_AKTIVNI."</td>\n"; // aktivni stav
    endif;
    echo "<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=EditUser&amp;modul=users&amp;pridu=".$pole_data["idu"]."\">".RS_USR_SU_UPRAVIT."</a> / ";
    // nastaveni prav
    if ($pole_data["admin"]==2): // admin
      echo RS_USR_SU_NAST_PRAVA." / ";
    else:
      echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=PravaUser&amp;modul=users&amp;pridu=".$pole_data["idu"]."\">".RS_USR_SU_NAST_PRAVA."</a> / ";
    endif;
    // nastaveni vazby - redaktor - autro
    if ($pole_data["admin"]==1): // redaktor
      echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=VazbaUser&amp;modul=users&amp;pridu=".$pole_data["idu"]."\">".RS_USR_SU_NAST_VAZBY."</a>";
    else:
      echo RS_USR_SU_NAST_VAZBY;
    endif;
    echo "</td>\n";
    echo "<td align=\"center\"><input type=\"checkbox\" name=\"poleid[]\" value=\"".$pole_data["idu"]."\" /></td></tr>";
  endfor;
  echo "<tr class=\"txt\"><td colspan=\"6\" align=\"right\"><input type=\"submit\" value=\" ".RS_USR_SU_SMAZ_OZNAC." \" class=\"tl\" /></td></tr>\n";
endif;
echo "</table>
<input type=\"hidden\" name=\"akce\" value=\"DelUser\" /><input type=\"hidden\" name=\"modul\" value=\"users\" />
</form>
<p></p>\n";
}

function AddUser()
{
// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowUser&amp;modul=users\">".RS_USR_SU_ZPET."</a></p>\n";
// pridavaci formular
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy\" bgcolor=\"#E6E6E6\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_USR_SU_FORM_USER."</b></td>
<td align=\"left\"><input type=\"text\" name=\"pruser\" size=\"40\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_USR_SU_FORM_HESLO."</b></td>
<td align=\"left\"><input type=\"password\" name=\"prheslo\" size=\"40\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_USR_SU_FORM_HESLO_POTV."</b></td>
<td align=\"left\"><input type=\"password\" name=\"prheslo2\" size=\"40\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_USR_SU_FORM_JMENO."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prjmeno\" size=\"40\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_USR_SU_FORM_EMAIL."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prmail\" size=\"40\" value=\"@\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_USR_SU_FORM_URL."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prurl\" size=\"40\" value=\"http://\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_USR_SU_FORM_IM."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prim\" size=\"40\" value=\"\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_USR_SU_FORM_BLOKOVAT."</b></td>
<td align=\"left\"><input type=\"radio\" name=\"prblokace\" value=\"1\" /> ".RS_TL_ANO." <input type=\"radio\" name=\"prblokace\" value=\"0\" checked /> ".RS_TL_NE."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_USR_SU_FORM_TYP."</b></td>
<td align=\"left\"><input type=\"radio\" name=\"prprava\" value=\"0\" checked />".RS_USR_SU_AUTOR." &nbsp;&nbsp; <input type=\"radio\" name=\"prprava\" value=\"1\" />".RS_USR_SU_REDAKTOR." &nbsp;&nbsp; <input type=\"radio\" name=\"prprava\" value=\"2\" />".RS_USR_SU_ADMIN."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_USR_SU_JAZYK."</b></td>
<td align=\"left\"><select name=\"prjazyk\" size=\"1\">".OptJazyky('cz')."</select></td></tr>
</table>
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_PRIDAT." \" class=\"tl\" /> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\" /></p>
<input type=\"hidden\" name=\"akce\" value=\"AcAddUser\" /><input type=\"hidden\" name=\"modul\" value=\"users\" />
</form>
<p></p>\n";
}

function AcAddUser()
{
// kontrola vstupu
$GLOBALS["pruser"]=mysql_escape_string($GLOBALS["pruser"]);
$GLOBALS["prjmeno"]=mysql_escape_string($GLOBALS["prjmeno"]);
$GLOBALS["prmail"]=mysql_escape_string($GLOBALS["prmail"]);
$GLOBALS["prurl"]=mysql_escape_string($GLOBALS["prurl"]);
$GLOBALS["prim"]=mysql_escape_string($GLOBALS["prim"]);
$GLOBALS["prblokace"]=mysql_escape_string($GLOBALS["prblokace"]);
$GLOBALS["prprava"]=mysql_escape_string($GLOBALS["prprava"]);
$GLOBALS["prjazyk"]=mysql_escape_string($GLOBALS["prjazyk"]);

$chyba=0; // detekce chyby

if (StrLen($GLOBALS["pruser"])>10): // test na velikost - prilis dlouhe
  echo "<p align=\"center\" class=\"txt\">Error U1: ".RS_USR_SU_ERR_DLOUHE_USERNAME."</p>\n";
  $chyba=1;
endif;
if (StrLen($GLOBALS["pruser"])<=1): // test na velikost - prilis kratke
  echo "<p align=\"center\" class=\"txt\">Error U2: ".RS_USR_SU_ERR_KRATKE_USERNAME."</p>\n";
  $chyba=1;
endif;
$dotazusr=mysql_query("select count(idu) as pocet from ".$GLOBALS["rspredpona"]."user where user='".addslashes($GLOBALS["pruser"])."'",$GLOBALS["dbspojeni"]);
if (mysql_result($dotazusr,0,"pocet")>0): // test na duplicitu
  echo "<p align=\"center\" class=\"txt\">Error U3: ".RS_USR_SU_ERR_DUPLICITNI_USERNAME."</p>\n";
  $chyba=1;
endif;
if (StrLen($GLOBALS["prheslo"])>10): // test na velikost - prilis dlouhe
  echo "<p align=\"center\" class=\"txt\">Error U4: ".RS_USR_SU_ERR_DLOUHE_HESLO."</p>\n";
  $chyba=1;
endif;
if ($GLOBALS["prheslo"]!=$GLOBALS["prheslo2"]): // test na shodu HESLA s kontrolnim zadanim
  echo "<p align=\"center\" class=\"txt\">Error U5: ".RS_USR_SU_ERR_RUZNA_HESLO."</p>\n";
  $chyba=1;
endif;

// pridani noveho uzivatele
if ($chyba==0):
  $GLOBALS["prheslo"]=MD5($GLOBALS["prheslo"]); // zpracovani hesla

  if ($GLOBALS["prprava"]==2): // admin
    $nast_pravo_vydavat=1;
  else: // ostatni
    $nast_pravo_vydavat=0;
  endif;

  $nast_pocet_chyb=0;
  $nast_pom_str='';

  $dotaz="insert into ".$GLOBALS["rspredpona"]."user ";
  $dotaz.="values(null,'".$GLOBALS["pruser"]."','".$GLOBALS["prheslo"]."','".$GLOBALS["prjmeno"]."','".$GLOBALS["prmail"]."','".$GLOBALS["prurl"]."',";
  $dotaz.="'".$GLOBALS["prim"]."','".$GLOBALS["prprava"]."','".$nast_pravo_vydavat."','".$GLOBALS["prblokace"]."','".$nast_pocet_chyb."','".$GLOBALS["prjazyk"]."',";
  $dotaz.="'".$nast_pom_str."')";

  @$error=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
  if (!$error):
    echo "<p align=\"center\" class=\"txt\">Error U6: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_USR_SU_OK_ADD_USER."</p>\n";
    // zjisteni id noveho uzivatele
    $dotazusr=mysql_query("select idu from ".$GLOBALS["rspredpona"]."user where user='".$GLOBALS["pruser"]."' and password='".$GLOBALS["prheslo"]."'",$GLOBALS["dbspojeni"]);
    $iduzivatele=mysql_result($dotazusr,0,"idu");
    // prednastaveni prav u jednotlivych modulu s ohledem na zvoleny typ uzivatele
    if ($GLOBALS["prprava"]==2): // admin
      NastavAllPrava($iduzivatele,1);
    else: // autor, redaktor
      NastavAllPrava($iduzivatele,0);
    endif;
  endif;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowUser&amp;modul=users\">".RS_USR_SU_ZPET."</a></p>\n";
}

function DelUser()
{
if (isset($GLOBALS["poleid"])):
  $pocetpol=count($GLOBALS["poleid"]);
else:
  $pocetpol=0;
endif;

for ($pom=0;$pom<$pocetpol;$pom++):
  // dotaz na existenci clanku spojeneho s uzivatelem
  $id_user=mysql_escape_string($GLOBALS["poleid"][$pom]);

  // test na pocet clanku vydanych odmazavanym autorem
  $dotazcla=mysql_query("select count(idc) as pocet from ".$GLOBALS["rspredpona"]."clanky where autor='".$id_user."'",$GLOBALS["dbspojeni"]);
  if ($dotazcla!=0):
    $pocetcla=mysql_result($dotazcla,0,"pocet");
  else:
    $pocetcla=0;
  endif;

  // vymazani uzivatele
  if ($pocetcla==0):
    @$error=mysql_query("delete from ".$GLOBALS["rspredpona"]."user where idu='".$id_user."'",$GLOBALS["dbspojeni"]);
    if (!$error):
      echo "<p align=\"center\" class=\"txt\">Error U7: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
    else:
      echo "<p align=\"center\" class=\"txt\">".RS_USR_SU_OK_DEL_USER."</p>\n";
    endif;
  else:
    // CHYBA: Akci nelze provést, jelikož vybraný uživatel je spojen s jedním nebo více články.
    echo "<p align=\"center\" class=\"txt\">".RS_USR_SU_ERR_USER_ZAVAZKY."</p>\n";
  endif;
endfor;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowUser&amp;modul=users\">".RS_USR_SU_ZPET."</a></p>\n";
}

function EditUser()
{
$GLOBALS["pridu"]=addslashes($GLOBALS["pridu"]);

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowUser&amp;modul=users\">".RS_USR_SU_ZPET."</a></p>\n";

$dotazusr=mysql_query("select * from ".$GLOBALS["rspredpona"]."user where idu='".$GLOBALS["pridu"]."'",$GLOBALS["dbspojeni"]);
$pole_data=mysql_fetch_assoc($dotazusr);

// editacni formular
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy\" bgcolor=\"#E6E6E6\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_USR_SU_FORM_USER."</b></td>
<td align=\"left\"><input type=\"text\" name=\"pruser\" value=\"".$pole_data['user']."\" size=\"40\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_USR_SU_FORM_JMENO."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prjmeno\" value=\"".$pole_data['jmeno']."\" size=\"40\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_USR_SU_FORM_EMAIL."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prmail\" value=\"".$pole_data['email']."\" size=\"40\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_USR_SU_FORM_URL."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prurl\" value=\"".$pole_data['url']."\" size=\"40\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_USR_SU_FORM_IM."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prim\" size=\"40\" value=\"".$pole_data['im_ident']."\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_USR_SU_FORM_BLOKOVAT."</b></td>
<td align=\"left\">";
if ($pole_data['blokovat']==1):
  echo "<input type=\"radio\" name=\"prblokace\" value=\"1\" checked /> ".RS_TL_ANO." <input type=\"radio\" name=\"prblokace\" value=\"0\" /> ".RS_TL_NE;
else:
  echo "<input type=\"radio\" name=\"prblokace\" value=\"1\" /> ".RS_TL_ANO." <input type=\"radio\" name=\"prblokace\" value=\"0\" checked /> ".RS_TL_NE;
endif;
echo "</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_USR_SU_FORM_TYP."</b></td>
<td align=\"left\">";
switch ($pole_data['admin']):
  case 0: // autor
    echo "<input type=\"radio\" name=\"prprava\" value=\"0\" checked />".RS_USR_SU_AUTOR." &nbsp;&nbsp; <input type=\"radio\" name=\"prprava\" value=\"1\" />".RS_USR_SU_REDAKTOR." &nbsp;&nbsp; <input type=\"radio\" name=\"prprava\" value=\"2\" />".RS_USR_SU_ADMIN;
    break;
  case 1: // redaktor
    echo "<input type=\"radio\" name=\"prprava\" value=\"0\" />".RS_USR_SU_AUTOR." &nbsp;&nbsp; <input type=\"radio\" name=\"prprava\" value=\"1\" checked />".RS_USR_SU_REDAKTOR." &nbsp;&nbsp; <input type=\"radio\" name=\"prprava\" value=\"2\" />".RS_USR_SU_ADMIN;
    break;
  case 2: // admin
    echo "<input type=\"radio\" name=\"prprava\" value=\"0\" />".RS_USR_SU_AUTOR." &nbsp;&nbsp; <input type=\"radio\" name=\"prprava\" value=\"1\" />".RS_USR_SU_REDAKTOR." &nbsp;&nbsp; <input type=\"radio\" name=\"prprava\" value=\"2\" checked />".RS_USR_SU_ADMIN;
    break;
endswitch;
echo "</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_USR_SU_JAZYK."</b></td>
<td align=\"left\"><select name=\"prjazyk\" size=\"1\">".OptJazyky($pole_data['jazyk_prostredi'])."</select></td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcUserEdit\" /><input type=\"hidden\" name=\"pridu\" value=\"".$GLOBALS["pridu"]."\" />
<p></p>

<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy\" bgcolor=\"#E6E6E6\">
<tr class=\"txt\"><td align=\"center\" colspan=\"2\"><b>".RS_USR_SU_FORM_INFO_HESLO."</b></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_USR_SU_FORM_NEW_HESLO."</b></td>
<td align=\"left\"><input type=\"password\" name=\"prheslo\" value=\"\" size=\"40\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_USR_SU_FORM_NEW_HESLO_POTV."</b></td>
<td align=\"left\"><input type=\"password\" name=\"prheslo2\" value=\"\" size=\"40\" class=\"textpole\"></td></tr>
</table>

<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\" /> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\" /></p>
<input type=\"hidden\" name=\"akce\" value=\"AcEditUser\" /><input type=\"hidden\" name=\"modul\" value=\"users\" />
<input type=\"hidden\" name=\"pridu\" value=\"".$pole_data['idu']."\" /><input type=\"hidden\" name=\"proldprava\" value=\"".$pole_data['admin']."\" />
</form>
<p></p>\n";
}

function AcEditUser()
{
// kontrola vstupu
$GLOBALS['pridu']=mysql_escape_string($GLOBALS['pridu']);
$GLOBALS['pruser']=mysql_escape_string($GLOBALS['pruser']);
$GLOBALS['prjmeno']=mysql_escape_string($GLOBALS['prjmeno']);
$GLOBALS['prmail']=mysql_escape_string($GLOBALS['prmail']);
$GLOBALS['prurl']=mysql_escape_string($GLOBALS['prurl']);
$GLOBALS['prim']=mysql_escape_string($GLOBALS['prim']);
$GLOBALS['prblokace']=mysql_escape_string($GLOBALS['prblokace']);
$GLOBALS['prprava']=mysql_escape_string($GLOBALS['prprava']);
$GLOBALS['proldprava']=mysql_escape_string($GLOBALS['proldprava']);
$GLOBALS["prjazyk"]=mysql_escape_string($GLOBALS["prjazyk"]);

$chyba=0; // inic. detekce chyby

if (StrLen($GLOBALS["pruser"])>10): // test na velikost - prilis dlouhe
  echo "<p align=\"center\" class=\"txt\">Error U8: ".RS_USR_SU_ERR_DLOUHE_USERNAME."</p>\n";
  $chyba=1;
endif;
if (StrLen($GLOBALS["pruser"])<=1): // test na velikost - prilis kratke
  echo "<p align=\"center\" class=\"txt\">Error U9: ".RS_USR_SU_ERR_KRATKE_USERNAME."</p>\n";
  $chyba=1;
endif;
$dotazusr=mysql_query("select count(idu) as pocet from ".$GLOBALS["rspredpona"]."user where user='".$GLOBALS["pruser"]."' and idu!='".addslashes($GLOBALS["pridu"])."'",$GLOBALS["dbspojeni"]);
if (mysql_result($dotazusr,0,"pocet")>0): // test na duplicitu
  echo "<p align=\"center\" class=\"txt\">Error U10: ".RS_USR_SU_ERR_DUPLICITNI_USERNAME."</p>\n";
  $chyba=1;
endif;
if (StrLen($GLOBALS["prheslo"])>10): // test na velikost - prilis dlouhe
  echo "<p align=\"center\" class=\"txt\">Error U11: ".RS_USR_SU_ERR_DLOUHE_HESLO."</p>\n";
  $chyba=1;
endif;
if (!empty($GLOBALS["prheslo"])):
  if ($GLOBALS["prheslo"]!=$GLOBALS["prheslo2"]): // test na shodu HESLA s kontrolnim zadanim
    echo "<p align=\"center\" class=\"txt\">Error U12: ".RS_USR_SU_ERR_RUZNA_HESLO."</p>\n";
    $chyba=1;
  endif;
endif;

// uprava uzivatele
if ($chyba==0):
  if ($GLOBALS["prheslo"]==''):
    $dotaz="update ".$GLOBALS["rspredpona"]."user set ";
    $dotaz.="user='".$GLOBALS["pruser"]."', jmeno='".$GLOBALS["prjmeno"]."', email='".$GLOBALS["prmail"]."', url='".$GLOBALS["prurl"]."',";
    $dotaz.="im_ident='".$GLOBALS["prim"]."', admin='".$GLOBALS["prprava"]."', blokovat='".$GLOBALS["prblokace"]."', jazyk_prostredi='".$GLOBALS["prjazyk"]."' ";
    $dotaz.="where idu='".$GLOBALS["pridu"]."'";
  else:
    $GLOBALS["prheslo"]=MD5($GLOBALS["prheslo"]); // vypicet hash hesla
    $dotaz="update ".$GLOBALS["rspredpona"]."user set ";
    $dotaz.="user='".$GLOBALS["pruser"]."', password='".$GLOBALS["prheslo"]."', jmeno='".$GLOBALS["prjmeno"]."', email='".$GLOBALS["prmail"]."', url='".$GLOBALS["prurl"]."',";
    $dotaz.="im_ident='".$GLOBALS["prim"]."', admin='".$GLOBALS["prprava"]."', blokovat='".$GLOBALS["prblokace"]."', jazyk_prostredi='".$GLOBALS["prjazyk"]."' ";
    $dotaz.=" where idu='".$GLOBALS["pridu"]."'";
    echo "<p align=\"center\" class=\"txt\">Uživatel \"".$GLOBALS["pruser"]."\" má nové heslo!</p>\n";
  endif;
  // uprava uzivatele
  @$error=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
  // test na chybu pri ukladani
  if (!$error):
    echo "<p align=\"center\" class=\"txt\">Error U13: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_USR_SU_OK_EDIT_USER."</p>\n";
  endif;
  // prednastaveni prav u jednotlivych modulu s ohledem na zvoleny typ uzivatele
  if ($GLOBALS["prprava"]==2&&$GLOBALS["proldprava"]!=$GLOBALS["prprava"]): // novy admin
    NastavAllPrava($GLOBALS["pridu"],1);
  endif;
  if ($GLOBALS["prprava"]!=2&&$GLOBALS["proldprava"]!=$GLOBALS["prprava"]): // novy autor nebo redaktor
    NastavAllPrava($GLOBALS["pridu"],0);
  endif;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowUser&amp;modul=users\">".RS_USR_SU_ZPET."</a></p>\n";
}

// ---[hlavni fce]------------------------------------------------------------------

/*
  PravaUser()
  AcPravaUser()
*/

function PravaUser()
{
// bezpecnostni korekce
$GLOBALS["pridu"]=mysql_escape_string($GLOBALS["pridu"]);

// link
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowUser&amp;modul=users\">".RS_USR_SU_ZPET."</a></p>\n";

// identifikace uziv.
$dotazusr=mysql_query("select admin,jmeno,pravo_vydavat from ".$GLOBALS["rspredpona"]."user where idu='".$GLOBALS["pridu"]."'",$GLOBALS["dbspojeni"]);
$pole_data=mysql_fetch_assoc($dotazusr);

// info o akt. uzivateli
echo "<p align=\"center\" class=\"txt\"><big><b>".RS_USR_NP_AKTUAL_USER.": \"".$pole_data["jmeno"]." - ";
switch ($pole_data["admin"]):
  case 0: echo RS_USR_SU_AUTOR; break; // autor
  case 1: echo RS_USR_SU_REDAKTOR; break; // redaktor
  case 2: echo RS_USR_SU_ADMIN; break; // admin
endswitch;
echo "\"</b></big></p>\n";

echo "<hr width=\"70%\" />\n";

echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">
<tr class=\"txt\" bgcolor=\"#E6E6E6\">
<td align=\"center\"><b>".RS_USR_NP_USER_PR."</b></td>
<td align=\"center\" colspan=\"2\"><b>".RS_USR_NP_NASTAV_PR."</b></td></tr>
<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">
<td align=\"left\">".RS_USR_NP_PR_VYDAVAT."</td>\n";
if ($pole_data["pravo_vydavat"]==1):
  echo "<td align=\"center\"><input type=\"radio\" name=\"prpravovydavat\" value=\"1\" checked /> <b>".RS_USR_NP_PR_POVOLENO."</b></td>\n";
  echo "<td align=\"center\"><input type=\"radio\" name=\"prpravovydavat\" value=\"0\" /> ".RS_USR_NP_PR_ZAKAZANO."</td>\n";
else:
  echo "<td align=\"center\"><input type=\"radio\" name=\"prpravovydavat\" value=\"1\" /> ".RS_USR_NP_PR_POVOLENO."</td>\n";
  echo "<td align=\"center\"><input type=\"radio\" name=\"prpravovydavat\" value=\"0\" checked /> <b>".RS_USR_NP_PR_ZAKAZANO."</b></td>\n";
endif;
echo "</tr>
</table>\n";

echo "<p></p><hr width=\"70%\" /><p></p>\n";

// z nastaveni jsou vyrazeny: globalni moduly, moduly urcene pouze pro adminy a blokovane moduly
$dotaz="select idm,nazev_modulu,fks_prava_users from ".$GLOBALS["rspredpona"]."moduly_prava where ";
$dotaz.="all_prava_users=0 and jen_admin_modul=0 and blokovat_modul=0";

$dotazprava=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetprava=mysql_num_rows($dotazprava);

echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">
<tr class=\"txt\" bgcolor=\"#E6E6E6\">
<td align=\"center\"><b>".RS_USR_NP_NAZEV_MODUL."</b></td>
<td align=\"center\" colspan=\"2\"><b>".RS_USR_NP_PRISTUP_MODUL."</b></td></tr>\n";
if ($pocetprava==0):
  // CHYBA: Nebyl nalezena žádný modul!
  echo "<tr class=\"txt\"><td colspan=\"3\" align=\"center\"><b>".RS_USR_NP_NIC_MODUL."</b></td></tr>\n";
else:
  for ($pom=0;$pom<$pocetprava;$pom++):
    $pole_data=mysql_fetch_assoc($dotazprava);
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">\n";
    echo "<td align=\"left\">".$pole_data["nazev_modulu"]."<input type=\"hidden\" name=\"prpravosekce[".$pom."]\" value=\"".$pole_data["idm"]."\" /></td>\n";
    if (OvereniCizichPrav($pole_data["fks_prava_users"],$GLOBALS["pridu"])==1):
      echo "<td align=\"center\"><input type=\"radio\" name=\"prpravonast[".$pom."]\" value=\"1\" checked /> <b>".RS_USR_NP_M_POVOLEN."</b></td>\n";
      echo "<td align=\"center\"><input type=\"radio\" name=\"prpravonast[".$pom."]\" value=\"0\" /> ".RS_USR_NP_M_ZAKAZAN."</td>\n";
    else:
      echo "<td align=\"center\"><input type=\"radio\" name=\"prpravonast[".$pom."]\" value=\"1\" /> ".RS_USR_NP_M_POVOLEN."</td>\n";
      echo "<td align=\"center\"><input type=\"radio\" name=\"prpravonast[".$pom."]\" value=\"0\" checked /> <b>".RS_USR_NP_M_ZAKAZAN."</b></td>\n";
    endif;
    echo "</tr>\n";
  endfor;
endif;
echo "</table>
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\" /> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\" /></p>
<input type=\"hidden\" name=\"akce\" value=\"AcPravaUser\" /><input type=\"hidden\" name=\"modul\" value=\"users\" />
<input type=\"hidden\" name=\"pridu\" value=\"".$GLOBALS["pridu"]."\" />
</form>
</p></p>\n";
}

function AcPravaUser()
{
// bezpecnostni korekce
$GLOBALS["pridu"]=mysql_escape_string($GLOBALS["pridu"]);
$GLOBALS["prpravovydavat"]=mysql_escape_string($GLOBALS["prpravovydavat"]);

// test na pristomnost definice pristup. prav
if (!isset($GLOBALS["prpravosekce"])||!isset($GLOBALS["prpravonast"])):
  echo "<p align=\"center\" class=\"txt\">Error U14: ".RS_USR_NP_ERR_NASTAV_PR."</p>\n";
  $chyba=1;
endif;

// nastaveni prav u jednotlivych modulu
$pocetprav=count($GLOBALS["prpravosekce"]);
for ($pom=0;$pom<$pocetprav;$pom++):
  UpravPrava($GLOBALS["prpravosekce"][$pom],$GLOBALS["pridu"],$GLOBALS["prpravonast"][$pom]);
endfor;

// nastaveni vydavatelskeho prava
mysql_query("update ".$GLOBALS["rspredpona"]."user set pravo_vydavat='".$GLOBALS["prpravovydavat"]."' where idu='".$GLOBALS["pridu"]."'",$GLOBALS["dbspojeni"]);

// navrat
PravaUser();
}

// ---[hlavni fce]------------------------------------------------------------------

/*
  VazbaUser()
  AcVazbaUser()
  DelVazbaUser()
*/

function VazbaUser()
{
// bezpecnostni korekce
$GLOBALS["pridu"]=mysql_escape_string($GLOBALS["pridu"]);

// link
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowUser&amp;modul=users\">".RS_USR_SU_ZPET."</a></p>\n";

// identifikace uziv.
$dotazusr=mysql_query("select admin,jmeno from ".$GLOBALS["rspredpona"]."user where idu='".$GLOBALS["pridu"]."'",$GLOBALS["dbspojeni"]);
$pole_data=mysql_fetch_assoc($dotazusr);

// info o akt. uzivateli
echo "<p align=\"center\" class=\"txt\"><big><b>".RS_USR_NP_AKTUAL_USER.": \"".$pole_data["jmeno"]." - ";
switch ($pole_data["admin"]):
  case 0: echo RS_USR_SU_AUTOR; break; // autor
  case 1: echo RS_USR_SU_REDAKTOR; break; // redaktor
  case 2: echo RS_USR_SU_ADMIN; break; // admin
endswitch;
echo "\"</b></big></p>\n";

echo "<hr width=\"70%\" />\n";

$dotaz="select v.idv,u.idu,u.user,u.jmeno from ".$GLOBALS["rspredpona"]."user as u,".$GLOBALS["rspredpona"]."vazby_prava as v ";
$dotaz.="where v.fk_id_nadrizeny=".$GLOBALS["pridu"]." and v.fk_id_podrizeny=u.idu and u.admin=0";
$dotazvazba=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetvazba=mysql_num_rows($dotazvazba);

echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">
<tr class=\"txt\" bgcolor=\"#E6E6E6\">
<td align=\"center\"><b>".RS_USR_NV_PODRIZENI_USER."</b></td>
<td align=\"center\"><b>".RS_USR_NV_SMAZ."</b></td></tr>\n";
if ($pocetvazba==0):
  // CHYBA: Nebyl nalezen žádný podřízený uživatel!
  echo "<tr class=\"txt\"><td colspan=\"2\" align=\"center\"><b>".RS_USR_NV_NIC_PODRIZENI."</b></td></tr>\n";
else:
  for ($pom=0;$pom<$pocetvazba;$pom++):
    $pole_vazba=mysql_fetch_assoc($dotazvazba);
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    echo "<td align=\"left\">".$pole_vazba["user"]." / ".$pole_vazba["jmeno"]."</td>\n";
    echo "<td align=\"center\"><input type=\"checkbox\" name=\"poleid[]\" value=\"".$pole_vazba["idv"]."\" /></td></tr>";
  endfor;
  echo "<tr class=\"txt\"><td colspan=\"2\" align=\"right\"><input type=\"submit\" value=\" ".RS_USR_NV_SMAZ_OZNAC." \" class=\"tl\" /></td></tr>\n";
endif;
echo "</table>
<input type=\"hidden\" name=\"akce\" value=\"DelVazbaUser\" /><input type=\"hidden\" name=\"modul\" value=\"users\" />
<input type=\"hidden\" name=\"pridu\" value=\"".$GLOBALS["pridu"]."\" />
</form>
<p></p>\n";

echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<div align=\"center\">
<select name=\"pruziv\" size=\"0\">".OptOmzSezUziv(0,0)."</select> &nbsp; <input type=\"submit\" value=\" ".RS_USR_NV_ADD_PODRIZENI." \" class=\"tl\" />
<input type=\"hidden\" name=\"akce\" value=\"AcVazbaUser\" /><input type=\"hidden\" name=\"modul\" value=\"users\" />
<input type=\"hidden\" name=\"pridu\" value=\"".$GLOBALS["pridu"]."\" />
</div>
</form>
<p></p>\n";
}

function AcVazbaUser()
{
// bezpecnostni korekce
$GLOBALS["pridu"]=mysql_escape_string($GLOBALS["pridu"]);
$GLOBALS["pruziv"]=mysql_escape_string($GLOBALS["pruziv"]);

// test na existenci stejne vazby
$dotazkontr=mysql_query("select idv from ".$GLOBALS["rspredpona"]."vazby_prava where fk_id_nadrizeny='".$GLOBALS["pridu"]."' and fk_id_podrizeny='".$GLOBALS["pruziv"]."'",$GLOBALS["dbspojeni"]);
if (mysql_num_rows($dotazkontr)==0):
  // vlozeni vazby
  @$error=mysql_query("insert into ".$GLOBALS["rspredpona"]."vazby_prava values(null,'".$GLOBALS["pridu"]."','".$GLOBALS["pruziv"]."')",$GLOBALS["dbspojeni"]);
  if (!$error):
    echo "<p align=\"center\" class=\"txt\">Error U15: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_USR_NV_OK_ADD_PODRIZENI."</p>\n";
  endif;
endif;

// navrat
VazbaUser();
}

function DelVazbaUser()
{
// bezpecnostni korekce
$GLOBALS["pridu"]=mysql_escape_string($GLOBALS["pridu"]);

if (isset($GLOBALS["poleid"])):
  $pocetpol=count($GLOBALS["poleid"]);
else:
  $pocetpol=0;
endif;

for ($pom=0;$pom<$pocetpol;$pom++):
  $id_vazba=addslashes($GLOBALS["poleid"][$pom]);
  @$error=mysql_query("delete from ".$GLOBALS["rspredpona"]."vazby_prava where idv='".$id_vazba."' and fk_id_nadrizeny='".$GLOBALS["pridu"]."'",$GLOBALS["dbspojeni"]);
  if (!$error):
    echo "<p align=\"center\" class=\"txt\">Error U16: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_USR_NV_OK_DEL_PODRIZENI."</p>\n";
  endif;
endfor;

// navrat
VazbaUser();
}
?>
