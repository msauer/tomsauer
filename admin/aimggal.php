<?

######################################################################
# phpRS Administration Engine - ImageGallery section 1.2.2
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_imggal_obr, rs_imggal_sekce, rs_user

/*
  Tento soubor zajistuje spravu interni galerie.
*/

if ($Uzivatel->StavSession!=1):
  echo "<html><body><div align=\"center\">Tento soubor neni urcen k vnejsimu spousteni!</div></body></html>";
  exit;
endif;

// ---[rozcestnik]------------------------------------------------------------------
switch($GLOBALS['akce']):
     // galerie obrazku
     case "ImgGal": AdminMenu();
          echo "<h2 align=\"center\">".RS_IGA_ROZ_SHOW_GAL."</h2><p align=\"center\">";
          ZaklPrehledIG();
          break;
     case "AddImgGal": AdminMenu();
          echo "<h2 align=\"center\">".RS_IGA_ROZ_ADD_GAL."</h2><p align=\"center\">";
          FormPriIG();
          break;
     case "AcAddImgGal": AdminMenu();
          echo "<h2 align=\"center\">".RS_IGA_ROZ_ADD_GAL."</h2><p align=\"center\">";
          PridejIG();
          break;
     case "EditImgGal": AdminMenu();
          echo "<h2 align=\"center\">".RS_IGA_ROZ_EDIT_GAL."</h2><p align=\"center\">";
          FormUprIG();
          break;
     case "AcEditImgGal": AdminMenu();
          echo "<h2 align=\"center\">".RS_IGA_ROZ_EDIT_GAL."</h2><p align=\"center\">";
          UpravIG();
          break;
     case "DeleteImgGal": AdminMenu();
          echo "<h2 align=\"center\">".RS_IGA_ROZ_DEL_GAL."</h2><p align=\"center\">";
          SmazIG();
          break;
     case "OpenImgGal": AdminMenu();
          echo "<h2 align=\"center\">".RS_IGA_ROZ_OPEN_GAL."</h2><p align=\"center\">";
          ShowPicIG();
          break;
     case "AddObrImgGal": AdminMenu();
          echo "<h2 align=\"center\">".RS_IGA_ROZ_ADD_OBR."</h2><p align=\"center\">";
          FormPriOBRIG();
          break;
     case "AcAddObrImgGal": AdminMenu();
          echo "<h2 align=\"center\">".RS_IGA_ROZ_ADD_OBR."</h2><p align=\"center\">";
          PridejOBRIG();
          break;
     case "DeleteObrImgGal": AdminMenu();
          echo "<h2 align=\"center\">".RS_IGA_ROZ_DEL_OBR."</h2><p align=\"center\">";
          SmazOBRIG();
          break;
     case "EditObrImgGal": AdminMenu();
          echo "<h2 align=\"center\">".RS_IGA_ROZ_EDIT_OBR."</h2><p align=\"center\">";
          FormUprOBRIG();
          break;
     case "AcEditObrImgGal": AdminMenu();
          echo "<h2 align=\"center\">".RS_IGA_ROZ_EDIT_OBR."</h2><p align=\"center\">";
          UpravOBRIG();
          break;
endswitch;
echo "</p>\n"; // zakonceni P tagu

// --[galerie]----------------------------------------------------------------------

// zakladni rozcestnik
function ZaklPrehledIG()
{
$autori=new SezAutori();

// link - pridat gal.
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=AddImgGal&amp;modul=intergal\">".RS_IGA_SG_PRIDAT_GAL."</a></p>\n";

$dotazgal=mysql_query("select ids,vlastnik,nazev,prava from ".$GLOBALS["rspredpona"]."imggal_sekce order by nazev",$GLOBALS["dbspojeni"]);
if ($dotazgal==0):
  $pocetgal=0; // neexistuje databazova tabulka
else:
  $pocetgal=mysql_num_rows($dotazgal); // pocet zaznamu v tabulce
endif;

// vypis
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\"><input type=\"hidden\" name=\"akce\" value=\"DeleteImgGal\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">
<tr class=\"txt\" bgcolor=\"#E6E6E6\"><td align=\"center\"><b>".RS_IGA_SG_NAZEV_GAL."</b></td>
<td align=\"center\"><b>".RS_IGA_SG_VLASTNIK."</b></td>
<td align=\"center\"><b>".RS_IGA_SG_AKCE."</b></td>
<td align=\"center\"><b>".RS_IGA_SG_SMAZ."</b></td></tr>\n";
if ($pocetgal==0):
  // zadna galerie
  echo "<tr class=\"txt\"><td colspan=\"4\" align=\"center\"><b>".RS_IGA_SG_ZADNA_GAL."</b></td></tr>\n";
else:
  for ($pom=0;$pom<$pocetgal;$pom++):
    $pole_data=mysql_fetch_assoc($dotazgal);
    if ((RSAUT_IDUSER==$pole_data["vlastnik"])||(RSAUT_PRAVA==2)):
      // je vlastnik nebo admin mohou vse
      echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
      echo "<td align=\"left\"><img src=\"image/adr_gal.gif\" width=\"24\" height=\"30\" align=\"absmiddle\" alt=\"".$pole_data["nazev"]."\" /> ".$pole_data["nazev"]."</td>\n";
      echo "<td align=\"left\">".$autori->UkazUser($pole_data["vlastnik"])."</td>\n";
      echo "<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=EditImgGal&amp;modul=intergal&amp;prids=".$pole_data["ids"]."\">".RS_IGA_SG_UPRAVIT."</a> / ";
      echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=OpenImgGal&amp;modul=intergal&amp;prids=".$pole_data["ids"]."\">".RS_IGA_SG_OTEVRIT."</a></td>\n";
      echo "<td align=\"center\"><input type=\"checkbox\" name=\"prpoleids[]\" value=\"".$pole_data["ids"]."\" /></td></tr>\n";
    else:
      // rozklad pristupovych prav
      $prpoleprav=array();
      $prpoleprav=explode(":",$pole_data["prava"]); // 0 - cteni, 1 - zapis, 2 - mazani
      // vypis galerie
      echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
      echo "<td align=\"left\"><img src=\"image/adr_gal.gif\" width=\"24\" height=\"30\" align=\"absmiddle\" alt=\"galerie\" /> ".$pole_data["nazev"]."</td>\n";
      echo "<td align=\"left\">".$autori->UkazUser($pole_data["vlastnik"])."</td>\n";
      echo "<td align=\"center\">".RS_IGA_SG_UPRAVIT." / ";
      // test ma moznost prohlizeni
      if ($prpoleprav[0]==1):
        echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=OpenImgGal&amp;modul=intergal&amp;prids=".$pole_data["ids"]."\">".RS_IGA_SG_OTEVRIT."</a></td>\n";
      else:
        echo RS_IGA_SG_OTEVRIT."</td>\n";
      endif;
      echo "<td align=\"center\">&nbsp;</td></tr>\n";
    endif;
  endfor;
  echo "<tr class=\"txt\"><td align=\"right\" colspan=\"4\"><input type=\"submit\" value=\" ".RS_IGA_SG_SMAZ_OZNAC." \" class=\"tl\" /></td></tr>\n";
endif;
echo "</table>\n";
echo "<input type=\"hidden\" name=\"akce\" value=\"DeleteImgGal\" /><input type=\"hidden\" name=\"modul\" value=\"intergal\" />\n";
echo "</form>\n";

// upozorneni
echo "<p align=\"center\" class=\"txt\">".RS_IGA_SG_UPOZORNENI."</p>
<p></p>\n";
}

function FormPriIG()
{
// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ImgGal&amp;modul=intergal\">".RS_IGA_SG_ZPET."</a></p>\n";
// formular
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy\" bgcolor=\"#E6E6E6\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_IGA_SG_FORM_NAZEV_GAL."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prnazev\" size=\"54\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td colspan=\"2\" align=\"left\"><b>".RS_IGA_SG_FORM_POPIS."</b><br />
<textarea name=\"prpopis\" rows=\"4\" cols=\"75\" class=\"textbox\"></textarea></td></tr>
<tr class=\"txt\"><td colspan=\"2\" align=\"center\"><b>".RS_IGA_SG_FORM_PRAVA."</b></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_IGA_SG_FORM_PRAVA_PROHLIZENI."</b></td>
<td align=\"left\"><input type=\"radio\" name=\"prcteni\" value=\"1\" checked />".RS_TL_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"prcteni\" value=\"0\" />".RS_TL_NE."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_IGA_SG_FORM_PRAVA_ZAPIS."</b></td>
<td align=\"left\"><input type=\"radio\" name=\"przapis\" value=\"1\" checked />".RS_TL_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"przapis\" value=\"0\" />".RS_TL_NE."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_IGA_SG_FORM_PRAVA_MAZANI."</b></td>
<td align=\"left\"><input type=\"radio\" name=\"prmazani\" value=\"1\" />".RS_TL_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"prmazani\" value=\"0\" checked />".RS_TL_NE."</td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcAddImgGal\" /><input type=\"hidden\" name=\"modul\" value=\"intergal\" />
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_PRIDAT." \" class=\"tl\" /> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\" /></p>
</form>\n";
}

function PridejIG()
{
// bezpecnostni korekce
$GLOBALS["prnazev"]=KorekceNadpisu($GLOBALS["prnazev"]); // korekce nadpisu
$GLOBALS["prnazev"]=mysql_escape_string($GLOBALS["prnazev"]);
$GLOBALS["prpopis"]=mysql_escape_string($GLOBALS["prpopis"]);
$GLOBALS["prcteni"]=mysql_escape_string($GLOBALS["prcteni"]);
$GLOBALS["przapis"]=mysql_escape_string($GLOBALS["przapis"]);
$GLOBALS["prmazani"]=mysql_escape_string($GLOBALS["prmazani"]);

// sestaveni prav - tri cislice za sebou oddelene dvojteckou, 0 - false, 1 - true
if (!isset($GLOBALS["prcteni"])): $GLOBALS["prcteni"]=0; endif;
if (!isset($GLOBALS["przapis"])): $GLOBALS["przapis"]=0; endif;
if (!isset($GLOBALS["prmazani"])): $GLOBALS["prmazani"]=0; endif;
$nast_prava=$GLOBALS["prcteni"].":".$GLOBALS["przapis"].":".$GLOBALS["prmazani"];

// pridani galerie
$dotaz="insert into ".$GLOBALS["rspredpona"]."imggal_sekce values(null,'".RSAUT_IDUSER."','".$GLOBALS["prnazev"]."','".$GLOBALS["prpopis"]."','".$nast_prava."')";
@$error=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
if (!$error):
  echo "<p align=\"center\" class=\"txt\">Error G1: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_IGA_SG_OK_ADD_GAL."</p>\n"; // vse OK
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ImgGal&amp;modul=intergal\">".RS_IGA_SG_ZPET."</a></p>\n";
}

function FormUprIG()
{
// bezpecnostni kontrola
$GLOBALS["prids"]=addslashes($GLOBALS["prids"]);

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ImgGal&amp;modul=intergal\">".RS_IGA_SG_ZPET."</a></p>\n";

$dotazgal=mysql_query("select ids,vlastnik,nazev,popis,prava from ".$GLOBALS["rspredpona"]."imggal_sekce where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
$pole_data=mysql_fetch_assoc($dotazgal);

// rozklad pristupovych prav
$prpoleprav=explode(":",$pole_data["prava"]); // 0 - cteni, 1 - zapis, 2 - mazani

// formular
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy\" bgcolor=\"#E6E6E6\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_IGA_SG_FORM_NAZEV_GAL."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prnazev\" size=\"54\" class=\"textpole\" value=\"".$pole_data["nazev"]."\" /></td></tr>
<tr class=\"txt\"><td colspan=\"2\" align=\"left\"><b>".RS_IGA_SG_FORM_POPIS."</b><br />
<textarea name=\"prpopis\" rows=\"4\" cols=\"75\" class=\"textbox\">".KorekceHTML($pole_data["popis"])."</textarea></td></tr>
<tr class=\"txt\"><td colspan=\"2\" align=\"center\"><b>".RS_IGA_SG_FORM_PRAVA."</b></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_IGA_SG_FORM_PRAVA_PROHLIZENI."</b></td>\n";
if ($prpoleprav[0]==1):
  echo "<td align=\"left\"><input type=\"radio\" name=\"prcteni\" value=\"1\" checked />".RS_TL_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"prcteni\" value=\"0\" />".RS_TL_NE."</td></tr>";
else:
  echo "<td align=\"left\"><input type=\"radio\" name=\"prcteni\" value=\"1\" />".RS_TL_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"prcteni\" value=\"0\" checked />".RS_TL_NE."</td></tr>";
endif;
echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_IGA_SG_FORM_PRAVA_ZAPIS."</b></td>\n";
if ($prpoleprav[1]==1):
  echo "<td align=\"left\"><input type=\"radio\" name=\"przapis\" value=\"1\" checked />".RS_TL_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"przapis\" value=\"0\" />".RS_TL_NE."</td></tr>";
else:
  echo "<td align=\"left\"><input type=\"radio\" name=\"przapis\" value=\"1\" />".RS_TL_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"przapis\" value=\"0\" checked />".RS_TL_NE."</td></tr>";
endif;
echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_IGA_SG_FORM_PRAVA_MAZANI."</b></td>\n";
if ($prpoleprav[2]==1):
  echo "<td align=\"left\"><input type=\"radio\" name=\"prmazani\" value=\"1\" checked />".RS_TL_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"prmazani\" value=\"0\" />".RS_TL_NE."</td></tr>";
else:
  echo "<td align=\"left\"><input type=\"radio\" name=\"prmazani\" value=\"1\" />".RS_TL_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"prmazani\" value=\"0\" checked />".RS_TL_NE."</td></tr>";
endif;
echo "</table>
<input type=\"hidden\" name=\"akce\" value=\"AcEditImgGal\" /><input type=\"hidden\" name=\"modul\" value=\"intergal\" />
<input type=\"hidden\" name=\"prvlastnik\" value=\"".$pole_data["vlastnik"]."\" /><input type=\"hidden\" name=\"prids\" value=\"".$pole_data["ids"]."\" />
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\" /> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\" /></p>
</form>\n";
}

function UpravIG()
{
// bezpecnostni kontrola
$GLOBALS["prids"]=mysql_escape_string($GLOBALS["prids"]);
$GLOBALS["prvlastnik"]=mysql_escape_string($GLOBALS["prvlastnik"]);
$GLOBALS["prnazev"]=KorekceNadpisu($GLOBALS["prnazev"]); // korekce nadpisu
$GLOBALS["prnazev"]=mysql_escape_string($GLOBALS["prnazev"]);
$GLOBALS["prpopis"]=mysql_escape_string($GLOBALS["prpopis"]);
$GLOBALS["prcteni"]=mysql_escape_string($GLOBALS["prcteni"]);
$GLOBALS["przapis"]=mysql_escape_string($GLOBALS["przapis"]);
$GLOBALS["prmazani"]=mysql_escape_string($GLOBALS["prmazani"]);

// sestaveni prav - tri cislice za sebou oddelene dvojteckou, 0 - false, 1 - true
if (!isset($GLOBALS["prcteni"])): $GLOBALS["prcteni"]=0; endif;
if (!isset($GLOBALS["przapis"])): $GLOBALS["przapis"]=0; endif;
if (!isset($GLOBALS["prmazani"])): $GLOBALS["prmazani"]=0; endif;
$nast_prava=$GLOBALS["prcteni"].":".$GLOBALS["przapis"].":".$GLOBALS["prmazani"];

// uprava hodnot
if ((RSAUT_IDUSER==$GLOBALS["prvlastnik"])||(RSAUT_PRAVA==2)):
  $dotaz="update ".$GLOBALS["rspredpona"]."imggal_sekce set nazev='".$GLOBALS["prnazev"]."',popis='".$GLOBALS["prpopis"]."',prava='".$nast_prava."' where ids='".$GLOBALS["prids"]."'";
  @$error=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
  if (!$error):
    echo "<p align=\"center\" class=\"txt\">Error G2: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_IGA_SG_OK_EDIT_GAL."</p>\n"; // vse OK
  endif;
endif;
// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ImgGal&amp;modul=intergal\">".RS_IGA_SG_ZPET."</a></p>\n";
}

function SmazIG()
{
// pocet polozek
if (isset($GLOBALS["prpoleids"])):
  $pocetgal=count($GLOBALS["prpoleids"]);
else:
  $pocetgal=0;
  echo RS_IGA_SG_ERR_DEL_POCET_NULA; // prazdny vyber
endif;

// vymazani galerie
for ($pom=0;$pom<$pocetgal;$pom++):
  $akt_id_galerie=mysql_escape_string($GLOBALS["prpoleids"][$pom]);
  // zjisteni jmena gal.
  $dotazjmeno=mysql_query("select nazev from ".$GLOBALS["rspredpona"]."imggal_sekce where ids='".$akt_id_galerie."'",$GLOBALS["dbspojeni"]);
  $jmeno_gal=mysql_result($dotazjmeno,0,"nazev");
  // overeni poctu obr. v gal.
  $dotazobrpoc=mysql_query("select ido from ".$GLOBALS["rspredpona"]."imggal_obr where sekce='".$akt_id_galerie."'",$GLOBALS["dbspojeni"]);
  if (mysql_num_rows($dotazobrpoc)>0):
    // chyba - galerie neni prazdna
    echo "<p align=\"center\" class=\"txt\">".RS_IGA_SG_ERR_NENI_PRAZDNA_C1." \"".$jmeno_gal."\" ".RS_IGA_SG_ERR_NENI_PRAZDNA_C2."</p>\n";
  else:
    // OK - prazdna gal.; lze vymazat
    @$error=mysql_query("delete from ".$GLOBALS["rspredpona"]."imggal_sekce where ids='".$akt_id_galerie."'",$GLOBALS["dbspojeni"]);
    if (!$error):
      echo "<p align=\"center\" class=\"txt\">Error G3: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
    else:
      echo "<p align=\"center\" class=\"txt\">".RS_IGA_SG_OK_DEL_GAL_C1." \"".$jmeno_gal."\" ".RS_IGA_SG_OK_DEL_GAL_C2."</p>\n"; // vse OK
    endif;
  endif;
endfor;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ImgGal&amp;modul=intergal\">".RS_IGA_SG_ZPET."</a></p>\n";
}

// --[obrazky]----------------------------------------------------------------------

function ShowPicIG()
{
// bezpecnostni kontrola
$GLOBALS["prids"]=mysql_escape_string($GLOBALS["prids"]);
// inic. str.
if (!isset($GLOBALS["prstrana"])): $GLOBALS["prstrana"]=1; endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ImgGal&amp;modul=intergal\">".RS_IGA_SG_ZPET."</a></p>\n";

$dotazgal=mysql_query("select vlastnik,nazev,popis,prava from ".$GLOBALS["rspredpona"]."imggal_sekce where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
list($prvlastnik,$prnazev,$prpopis,$prprava)=mysql_fetch_row($dotazgal);

// rozklad pristupovych prav
if ((RSAUT_IDUSER==$prvlastnik)||(RSAUT_PRAVA==2)): // pro vlastnika a admina vsechna true
  $prpoleprav[0]=1; // cteni
  $prpoleprav[1]=1; // zapis
  $prpoleprav[2]=1; // mazani
else:
  $prpoleprav=explode(":",$prprava); // 0 - cteni, 1 - zapis, 2 - mazani
endif;

// zobrazeni
echo "<div align=\"center\">
<div align=\"center\" style=\"background-color: #E6E6E6; border: 2px solid #8C8C8C; width: 400px;\">
<span class=\"txt\"><big><strong>".$prnazev."</strong></big><br />".$prpopis."</span>
</div>
</div>\n";

// link - pridat obr.
if ($prpoleprav[1]==1):
  echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=AddObrImgGal&amp;modul=intergal&amp;prids=".$GLOBALS["prids"]."\">".RS_IGA_PO_PRIDAT_OBR."</a></p>\n";
endif;

// sestaveni limitu + limitniho pasu
$dotazcelkobr=mysql_query("select count(ido) as pocet from ".$GLOBALS["rspredpona"]."imggal_obr where sekce='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
$pocetcelkobr=mysql_Result($dotazcelkobr,0,"pocet");

$pocetobrnastr=20; // pocet obrazku na 1 str.
$mozneobratky=ceil($pocetcelkobr/$pocetobrnastr);
if ($GLOBALS["prstrana"]==1): $startpozice=0; else: $startpozice=$pocetobrnastr*($GLOBALS["prstrana"]-1); endif; // vypocit limitu

if ($mozneobratky>1):
  echo "<p align=\"center\" class=\"txt\"> | ";
  for ($pom=0;$pom<$mozneobratky;$pom++):
    if (($pom+1)==$GLOBALS["prstrana"]): // omezeni akt. vypisove stranky
     echo ($pom*$pocetobrnastr)."-".(($pom+1)*$pocetobrnastr)." | ";
    else:
     echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=OpenImgGal&amp;modul=intergal&amp;prids=".$GLOBALS["prids"]."&amp;prstrana=".($pom+1)."\">".($pom*$pocetobrnastr)."-".(($pom+1)*$pocetobrnastr)."</a> | ";
    endif;
  endfor;
  echo "</p>\n";
endif;
// konec sestaveni limitu + limitniho pasu

// vzneseni dotazu
$dotazobr=mysql_query("select ido,vlastnik,nazev,obr_poloha,obr_width,obr_height,obr_vel,nahl_poloha,nahl_width,nahl_height from ".$GLOBALS["rspredpona"]."imggal_obr where sekce='".$GLOBALS["prids"]."' order by ido limit ".$startpozice.",".$pocetobrnastr,$GLOBALS["dbspojeni"]);
$pocetobr=mysql_num_rows($dotazobr);

// zobrazeni obrazku
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">\n";
if ($pocetobr==0):
  // zadny obrazek
  echo "<tr class=\"txt\"><td colspan=\"5\" align=\"center\"><b>".RS_IGA_PO_ZADNY_OBR."</b></td></tr>\n";
else:
  $pocetprubehu=0;
  if (RSAUT_IDUSER==$prvlastnik||RSAUT_PRAVA==2): // je vlastnik nebo admin, muze vse
     for ($pom=0;$pom<$pocetobr;$pom++):
       $pole_data=mysql_fetch_assoc($dotazobr);
       if ($pocetprubehu==0):
         echo "<tr class=\"txt\">\n";
       endif;
       echo "<td align=\"center\" width=\"150\"><big><b>".RS_IGA_PO_ID." ".$pole_data["ido"]."</b></big><br />";
       // test na existenci nahledu
       if ($pole_data["nahl_poloha"]=='none'):
         echo "<br /><br />".RS_IGA_PO_NENI_NAHLED."<br /><br /><br />\n";
       else:
         echo "<img src=\"".$pole_data["nahl_poloha"]."\" width=\"".$pole_data["nahl_width"]."\" height=\"".$pole_data["nahl_height"]."\" align=\"absmiddle\" alt=\"".$pole_data["nazev"]."\" /><br />\n";
       endif;
       echo "<b>".$pole_data["nazev"]."</b> (<a href=\"".$pole_data["obr_poloha"]."\" target=\"_blank\">".RS_IGA_PO_ORIGINAL."</a>)<br />";
       echo RS_IGA_PO_SIRKA_VYSKA." ".$pole_data["obr_width"]."x".$pole_data["obr_height"]."<br />";
       echo RS_IGA_PO_VELIKOST." ".round($pole_data["obr_vel"]/1024)." kB<br />";
       echo "<input type=\"checkbox\" name=\"prpoleid[]\" value=\"".$pole_data["ido"]."\" /> ".RS_IGA_PO_SMAZ." / <a href=\"".RS_VYKONNYSOUBOR."?akce=EditObrImgGal&amp;modul=intergal&amp;prids=".$GLOBALS["prids"]."&amp;prido=".$pole_data["ido"]."\">".RS_IGA_PO_UPRAVIT."</a></td>\n";
       if ($pocetprubehu==4):
         echo "</tr>\n";
         $pocetprubehu=0;
       else:
         $pocetprubehu++;
       endif;
     endfor;
  else:
     for ($pom=0;$pom<$pocetobr;$pom++):
       $pole_data=mysql_fetch_assoc($dotazobr);
       if ($pocetprubehu==0):
         echo "<tr class=\"txt\">\n";
       endif;
       echo "<td align=\"center\" width=\"150\"><big><b>".RS_IGA_PO_ID." ".$pole_data["ido"]."</b></big><br />";
       // test na existenci nahledu
       if ($pole_data["nahl_poloha"]=='none'):
         echo "<br /><br />".RS_IGA_PO_NENI_NAHLED."<br /><br /><br />\n";
       else:
         echo "<img src=\"".$pole_data["nahl_poloha"]."\" width=\"".$pole_data["nahl_width"]."\" height=\"".$pole_data["nahl_height"]."\" align=\"absmiddle\" alt=\"".$pole_data["nazev"]."\" /><br />\n";
       endif;
       echo "<b>".$pole_data["nazev"]."</b> (<a href=\"".$pole_data["obr_poloha"]."\" target=\"_blank\">".RS_IGA_PO_ORIGINAL."</a>)<br />";
       echo RS_IGA_PO_SIRKA_VYSKA." ".$pole_data["obr_width"]."x".$pole_data["obr_height"]."<br />";
       echo RS_IGA_PO_VELIKOST." ".round($pole_data["obr_vel"]/1024)." kB<br />";
       // test ma moznost mazani
       if ($prpoleprav[2]==1):
         echo "<br /><input type=\"checkbox\" name=\"prpoleid[]\" value=\"".$pole_data["ido"]."\" /> Smazat</td>\n";
       else:
         echo "</td>\n";
       endif;
       // konec testu na moz. mazani
       if ($pocetprubehu==4):
         echo "</tr>\n";
         $pocetprubehu=0;
       else:
         $pocetprubehu++;
       endif;
     endfor;
  endif;
  // dokonceni tabulky
  switch ($pocetprubehu):
    case 0: break;
    case 1: echo "<td width=\"150\">&nbsp;</td><td width=\"150\">&nbsp;</td><td width=\"150\">&nbsp;</td><td width=\"150\">&nbsp;</td></tr>"; break;
    case 2: echo "<td width=\"150\">&nbsp;</td><td width=\"150\">&nbsp;</td><td width=\"150\">&nbsp;</td></tr>"; break;
    case 3: echo "<td width=\"150\">&nbsp;</td><td width=\"150\">&nbsp;</td></tr>"; break;
    case 4: echo "<td width=\"150\">&nbsp;</td></tr>"; break;
  endswitch;
  if (RSAUT_IDUSER==$prvlastnik||RSAUT_PRAVA==2||$prpoleprav[2]==1):
    echo "<tr class=\"txt\"><td align=\"right\" colspan=\"5\"><input type=\"submit\" value=\" ".RS_IGA_PO_SMAZ_OZNAC." \" class=\"tl\" /></td></tr>\n";
  endif;
endif;
echo "</table>
<input type=\"hidden\" name=\"akce\" value=\"DeleteObrImgGal\"><input type=\"hidden\" name=\"modul\" value=\"intergal\">
<input type=\"hidden\" name=\"prids\" value=\"".$GLOBALS["prids"]."\">
</form>
<p></p>\n";

// informace k aplikaci obrazku - tzv. phprs znacka
echo "<p align=\"center\" class=\"txt\">".RS_IGA_PO_INFO_PHPRS_ZNACKY."</p>\n<p></p>\n";
}

function FormPriOBRIG()
{
// bezpecnostni kontrola
$GLOBALS["prids"]=mysql_escape_string($GLOBALS["prids"]);

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=OpenImgGal&amp;modul=intergal&amp;prids=".$GLOBALS["prids"]."\">".RS_IGA_PO_ZPET_PRED."</a></p>\n";

echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\" enctype=\"multipart/form-data\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy\" bgcolor=\"#E6E6E6\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_IGA_PO_FORM_NAZEV_OBR."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prnazev\" size=\"57\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td colspan=\"2\" align=\"left\"><b>".RS_IGA_PO_FORM_POPIS."</b><br />
<textarea name=\"prpopis\" rows=\"3\" cols=\"75\" class=\"textbox\"></textarea></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_IGA_PO_FORM_OBRAZEK."</b></td>
<td align=\"left\"><input type=\"file\" name=\"prsoubor\" accept=\"image/gif,image/jpeg,image/png\" size=\"30\" class=\"textpole\"></td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcAddObrImgGal\" /><input type=\"hidden\" name=\"modul\" value=\"intergal\">
<input type=\"hidden\" name=\"prids\" value=\"".$GLOBALS["prids"]."\" />
<p align=\"center\"><input type=\"submit\" value=\" ".RS_IGA_PO_TL_UPLOAD." \" class=\"tl\" /></p>
</form>\n";

// upozorneni
echo "<p align=\"center\" class=\"txt\">".RS_IGA_PO_INFO_GENER_NAHLEDU."</p>\n<p></p>\n";
}

function PridejOBRIG()
{
// bezpecnostni kontrola
$GLOBALS["prids"]=mysql_escape_string($GLOBALS["prids"]);

// test na pritomnost dulezitych prom.
if (!isset($GLOBALS['rsconfig']['img_nahled_sirka'])): $GLOBALS['rsconfig']['img_nahled_sirka']=120; endif; // sirka nahledu
if (!isset($GLOBALS['rsconfig']['img_nahled_vyska'])): $GLOBALS['rsconfig']['img_nahled_vyska']=96; endif; // vyska nahledu

$prsoubor=$_FILES['prsoubor']['tmp_name']; // docasny nazev souboru
$ulozinf_vel=$_FILES['prsoubor']['size']; // velkost souboru

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=OpenImgGal&amp;modul=intergal&amp;prids=".$GLOBALS["prids"]."\">".RS_IGA_PO_ZPET_OBR."</a></p>\n";

// test na uskutecneni uploadu
if ($prsoubor=="none"||$prsoubor==""):
  echo "<p align=\"center\" class=\"txt\">Error G4: ".RS_IGA_PO_ERR_NELZE_UPLOADOVAT."</p>\n";
  exit();
endif;
// test na nenulovou vel.
if ($ulozinf_vel==0):
  echo "<p align=\"center\" class=\"txt\">Error G5: ".RS_IGA_PO_ERR_NULOVA_DELKA."</p>\n";
  exit();
endif;

// dekompilace jmena souboru souboru - ziskani pripony
$prjmenosouboru=explode(".",$_FILES['prsoubor']['name']); // 0 - jmeno, 1 - pripona
$pocet_casti=count($prjmenosouboru);
if ($pocet_casti>0):
  $prpriponasouboru=strtolower($prjmenosouboru[$pocet_casti-1]);
else:
  $prpriponasouboru="";
endif;

// ziskani informaci o souboru
$probrparametry=GetImageSize($prsoubor);
if (!is_array($probrparametry)):
  // nemohu najit potrebne informace o souboru
  echo "<p align=\"center\" class=\"txt\">".RS_IGA_PO_ERR_SECURE_ERR_NEJSOU_DATA."</p>\n";
  exit();
endif;
$ulozinf_sirka=$probrparametry[0]; // sirka
$ulozinf_vyska=$probrparametry[1]; // vyska
$prtypuplsoub=$probrparametry[2]; // typ upload. souboru

// typy obr.: 1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF, 5 = PSD, 6 = BMP, 7 = TIFF(intel byte order), 8 = TIFF(motorola byte order),
// 9 = JPC, 10 = JP2, 11 = JPX, 12 = JB2, 13 = SWC, 14 = IFF.

// bezpecnostni kontrola na format obrazku
//if ($prpriponasouboru!="jpg"&&$prpriponasouboru!="jpeg"&&$prpriponasouboru!="gif"&&$prpriponasouboru!="png"&&$prpriponasouboru!="bmp"):
if ($prtypuplsoub!=1&&$prtypuplsoub!=2&&$prtypuplsoub!=3&&$prtypuplsoub!=6):
  echo "<p align=\"center\" class=\"txt\">".RS_IGA_PO_ERR_SECURE_ERR_FORMAT."</p>\n";
  exit();
endif;

// spec. predpona
$prpredjmeno=Date("YmdHi");
// sestaveni noveho jmena souboru
$ulozinf_novsoubor=$GLOBALS['rsconfig']['img_adresar'].$prpredjmeno."_".$prjmenosouboru[0].".".$prpriponasouboru;
// sestaveni noveho jmeno nahledu
$nahledjmeno=$GLOBALS['rsconfig']['img_adresar']."n".$prpredjmeno."_".$prjmenosouboru[0].".".$prpriponasouboru;

if (is_uploaded_file($prsoubor)):
  if (move_uploaded_file($prsoubor,$ulozinf_novsoubor)):
    echo "<p align=\"center\" class=\"txt\">".RS_IGA_PO_OK_UPLOAD_OBR."</p>\n"; // vse OK
    chmod ($ulozinf_novsoubor,0744); // nastaveni pristupovych prav
  else:
    // chyba pri ukladani souboru
    echo "<p align=\"center\" class=\"txt\">".RS_IGA_PO_ERR_CHYBA_PRI_ULOZENI."</p>\n";
    exit();
  endif;
else:
  // soubor nebyl uploadovan bezpecnou cestou
  echo "<p align=\"center\" class=\"txt\">".RS_IGA_PO_ERR_SECURE_ERR_BEZPECNOST."</p>\n";
  exit();
endif;

$generovatnahl=1; // generovat nahledu, default true

if (($GLOBALS['rsconfig']['img_nahled_sirka']>$ulozinf_sirka)&&($GLOBALS['rsconfig']['img_nahled_vyska']>$ulozinf_vyska)):
  $generovatnahl=0; // zruseni generovani nahledu
endif;

if (($prtypuplsoub!=2)&&($prtypuplsoub!=3)):
  $generovatnahl=0; // zruseni generovani nahledu
endif;

// generovani nahledu
if ($generovatnahl==1):
  // priprava obr.
  if($ulozinf_sirka>$ulozinf_vyska): $prpodilobr=$ulozinf_sirka/$ulozinf_vyska; endif;
  if($ulozinf_sirka<$ulozinf_vyska): $prpodilobr=$ulozinf_vyska/$ulozinf_sirka; endif;
  if($ulozinf_sirka==$ulozinf_vyska): $prpodilobr=1; endif;
  // doupraveni rozmeru nahledu pohled sirky, vysky real. obr.
  if($ulozinf_sirka>$ulozinf_vyska):
    $nahledsirka=$GLOBALS['rsconfig']['img_nahled_sirka'];
    $nahledvyska=round($GLOBALS['rsconfig']['img_nahled_sirka']/$prpodilobr);
  endif;
  if($ulozinf_sirka<$ulozinf_vyska):
    $nahledsirka=round($GLOBALS['rsconfig']['img_nahled_vyska']/$prpodilobr);
    $nahledvyska=$GLOBALS['rsconfig']['img_nahled_vyska'];
  endif;
  if($ulozinf_sirka==$ulozinf_vyska):
    $nahledsirka=$GLOBALS['rsconfig']['img_nahled_sirka'];
    $nahledvyska=$GLOBALS['rsconfig']['img_nahled_vyska'];
  endif;
  // tvorba nahledu
  switch ($prtypuplsoub):
    case 2: $probrzbroj=ImageCreateFromJPEG($ulozinf_novsoubor); break;
    case 3: $probrzbroj=ImageCreateFromPNG($ulozinf_novsoubor); break;
  endswitch;
  // pro GD 1.x
  /*
  $probrcil=ImageCreate($nahledsirka,$nahledvyska);
  imagecopyresized($probrcil,$probrzbroj,0,0,0,0,$nahledsirka,$nahledvyska,$ulozinf_sirka,$ulozinf_vyska);
  */
  // konec pro GD 1.x
  // pro GD 2.x
  $probrcil=imagecreatetruecolor($nahledsirka,$nahledvyska);
  //ImageCopyResized($probrcil,$probrzbroj,0,0,0,0,$nahledsirka,$nahledvyska,$ulozinf_sirka,$ulozinf_vyska);
  imagecopyresampled($probrcil,$probrzbroj,0,0,0,0,$nahledsirka,$nahledvyska,$ulozinf_sirka,$ulozinf_vyska); // lepsi vysledky nez: ImageCopyResized
  // konec pro GD 2.x
  switch ($prtypuplsoub):
    case 2: ImageJPEG($probrcil,$nahledjmeno,90); break;
    case 3: ImagePNG($probrcil,$nahledjmeno); break;
  endswitch;
  ImageDestroy($probrzbroj);
  ImageDestroy($probrcil);
else:
  // nelze generovat nahled
  echo "<p align=\"center\" class=\"txt\">".RS_IGA_PO_ERR_NELZE_GENER_NAHLED."</p>\n";
endif;

// ulozeni obrazku do DB
if ($generovatnahl==1):
  // s nahledem
  @$error=mysql_query("insert into ".$GLOBALS["rspredpona"]."imggal_obr values(null,'".RSAUT_IDUSER."','".$GLOBALS["prids"]."','".$GLOBALS["prnazev"]."','".$GLOBALS["prpopis"]."','','".$ulozinf_novsoubor."','".$ulozinf_sirka."','".$ulozinf_vyska."','".$ulozinf_vel."','".$nahledjmeno."','".$nahledsirka."','".$nahledvyska."')",$GLOBALS["dbspojeni"]);
  if (!$error):
    echo "<p align=\"center\" class=\"txt\">Error G6: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_IGA_PO_OK_ADD_OBR."</p>\n"; // vse OK
  endif;
else:
  // bez nahledu
  @$error=mysql_query("insert into ".$GLOBALS["rspredpona"]."imggal_obr values(null,'".RSAUT_IDUSER."','".$GLOBALS["prids"]."','".$GLOBALS["prnazev"]."','".$GLOBALS["prpopis"]."','','".$ulozinf_novsoubor."','".$ulozinf_sirka."','".$ulozinf_vyska."','".$ulozinf_vel."','none','0','0')",$GLOBALS["dbspojeni"]);
  if (!$error):
    echo "<p align=\"center\" class=\"txt\">Error G7: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_IGA_PO_OK_ADD_OBR."</p>\n"; // vse OK
  endif;
endif;
}

function SmazOBRIG()
{
// bezpecnostni kontrola
$GLOBALS["prids"]=mysql_escape_string($GLOBALS["prids"]);

// nacteni gal.
$dotazgal=mysql_query("select vlastnik,prava from ".$GLOBALS["rspredpona"]."imggal_sekce where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
list($prvlastnik,$prprava)=mysql_fetch_row($dotazgal);

// rozklad pristupovych prav
if ((RSAUT_IDUSER==$prvlastnik)||(RSAUT_PRAVA==2)): // pro vlastnika a admina vsechna true
  $prpoleprav[0]=1; // cteni
  $prpoleprav[1]=1; // zapis
  $prpoleprav[2]=1; // mazani
else:
  $prpoleprav=explode(":",$prprava); // 0 - cteni, 1 - zapis, 2 - mazani
endif;

if (isset($GLOBALS["prpoleid"])):
  $pocetobr=count($GLOBALS["prpoleid"]);
else:
  $pocetobr=0; // prazdny vyber
endif;

if ($prpoleprav[2]==1): // uzivatel ma povoleni mazat
  for ($pom=0;$pom<$pocetobr;$pom++):
    $akt_id_obrazek=mysql_escape_string($GLOBALS["prpoleid"][$pom]); // id obrazku
    $dotazobr=mysql_query("select obr_poloha,nahl_poloha  from ".$GLOBALS["rspredpona"]."imggal_obr where ido='".$akt_id_obrazek."' and sekce='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
    if (mysql_num_rows($dotazobr)==1):
      $pole_poloha=mysql_fetch_assoc($dotazobr);
      if (unlink($pole_poloha["obr_poloha"])==0):
        echo "<p align=\"center\" class=\"txt\">".RS_IGA_PO_ERR_CHYBA_DEL_ORIG_OBR."</p>\n"; // chyba pri mazani orig.
      endif;
      if (unlink($pole_poloha["nahl_poloha"])==0):
        echo "<p align=\"center\" class=\"txt\">".RS_IGA_PO_ERR_CHYBA_DEL_NAHLED_OBR."</p>\n"; // chyba pri mazani nahledu
      endif;
      @$error=mysql_query("delete from ".$GLOBALS["rspredpona"]."imggal_obr where ido='".addslashes($GLOBALS["prpoleid"][$pom])."' and sekce='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
      if (!$error):
        echo "<p align=\"center\" class=\"txt\">Error G8: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
      else:
        echo "<p align=\"center\" class=\"txt\">".RS_IGA_PO_OK_DEL_OBR_C1." ".$GLOBALS["prpoleid"][$pom]." ".RS_IGA_PO_OK_DEL_OBR_C2."</p>\n"; // vse OK
      endif;
    else:
      echo "<p align=\"center\" class=\"txt\">Error G9: ".RS_IGA_PO_ERR_NEMOHU_NAJIT."</p>\n"; // nelze najit obrazek
    endif;
  endfor;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ImgGal&amp;modul=intergal\">".RS_IGA_SG_ZPET."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=OpenImgGal&amp;modul=intergal&amp;prids=".$GLOBALS["prids"]."\">".RS_IGA_PO_ZPET_OBR."</a></p>\n";
}

function FormUprOBRIG()
{
// bezpecnostni kontrola
$GLOBALS["prids"]=mysql_escape_string($GLOBALS["prids"]);
$GLOBALS["prido"]=mysql_escape_string($GLOBALS["prido"]);

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=OpenImgGal&amp;modul=intergal&amp;prids=".$GLOBALS["prids"]."\">".RS_IGA_PO_ZPET_OBR."</a></p>\n";

// dotaz na upravu obr.
$dotazobr=mysql_query("select ido,vlastnik,nazev,popis,obr_poloha,obr_width,obr_height,obr_vel,nahl_poloha,nahl_width,nahl_height from ".$GLOBALS["rspredpona"]."imggal_obr where ido='".$GLOBALS["prido"]."' and sekce='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
$pocetobr=mysql_num_rows($dotazobr);

if ($pocetobr==1):
  // nacteni dat
  $pole_data=mysql_fetch_assoc($dotazobr);
  // vypis
  echo "<p align=\"center\" class=\"txt\"><big><b>".RS_IGA_PO_ID." ".$pole_data["ido"]."</b></big></p>\n";
  echo "<p align=\"center\"><img src=\"".$pole_data["obr_poloha"]."\" width=\"".$pole_data["obr_width"]."\" height=\"".$pole_data["obr_height"]."\" align=\"absmiddle\" alt=\"".$pole_data["nazev"]."\" /></p>\n";
  echo "<p align=\"center\" class=\"txt\"><b>".$pole_data["nazev"]."</b><br />\n";
  echo RS_IGA_PO_SIRKA_VYSKA." ".$pole_data["obr_width"]."x".$pole_data["obr_height"].", ".RS_IGA_PO_VELIKOST." ".round($pole_data["obr_vel"]/1024)." kB</p>\n";
  echo "<p align=\"center\" class=\"txt\">".RS_IGA_PO_ADR_ORIG_OBR." ".$pole_data["obr_poloha"]."<br />\n";
  if ($pole_data["nahl_poloha"]=="none"):
    echo RS_IGA_PO_ADR_NAHLED_OBR." ".RS_IGA_PO_NENI_NAHLED."</p>\n"; // nahled neexistuje
  else:
    echo RS_IGA_PO_ADR_NAHLED_OBR." ".$pole_data["nahl_poloha"]."</p>\n"; // nahled existuje
  endif;
  // formular pro upravu popisu
  echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy\" bgcolor=\"#E6E6E6\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_IGA_PO_FORM_NAZEV_OBR."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prnazev\" size=\"57\" value=\"".$pole_data["nazev"]."\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td colspan=\"2\" align=\"left\"><b>".RS_IGA_PO_FORM_POPIS."</b><br />
<textarea name=\"prpopis\" rows=\"3\" cols=\"75\" class=\"textbox\">".KorekceHTML($pole_data["popis"])."</textarea></td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcEditObrImgGal\" /><input type=\"hidden\" name=\"modul\" value=\"intergal\" />
<input type=\"hidden\" name=\"prids\" value=\"".$GLOBALS["prids"]."\" /><input type=\"hidden\" name=\"prido\" value=\"".$GLOBALS["prido"]."\" />
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\" /></p>
</form>
<p></p>\n";
endif;
}

function UpravOBRIG()
{
// bezpecnostni kontrola
$GLOBALS["prids"]=mysql_escape_string($GLOBALS["prids"]);
$GLOBALS["prido"]=mysql_escape_string($GLOBALS["prido"]);
$GLOBALS["prnazev"]=KorekceNadpisu($GLOBALS["prnazev"]); // korekce nadpisu
$GLOBALS["prnazev"]=mysql_escape_string($GLOBALS["prnazev"]);
$GLOBALS["prpopis"]=mysql_escape_string($GLOBALS["prpopis"]);

$dotazgal=mysql_query("select vlastnik,prava from ".$GLOBALS["rspredpona"]."imggal_sekce where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
list($prvlastnik,$prprava)=mysql_fetch_row($dotazgal);

// rozklad pristupovych prav
if ((RSAUT_IDUSER==$prvlastnik)||(RSAUT_PRAVA==2)): // pro vlastnika a admina vsechna true
  $prpoleprav[0]=1; // cteni
  $prpoleprav[1]=1; // zapis
  $prpoleprav[2]=1; // mazani
else:
  $prpoleprav=explode(":",$prprava); // 0 - cteni, 1 - zapis, 2 - mazani
endif;

// uprava hodnot
if ((RSAUT_IDUSER==$prvlastnik)||(RSAUT_PRAVA==2)):
  $dotaz="update ".$GLOBALS["rspredpona"]."imggal_obr set nazev='".$GLOBALS["prnazev"]."',popis='".$GLOBALS["prpopis"]."' where ido='".$GLOBALS["prido"]."' and sekce='".$GLOBALS["prids"]."'";
  @$error=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
  if (!$error):
    echo "<p align=\"center\" class=\"txt\">Error G10: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_IGA_PO_OK_EDIT_OBR."</p>\n"; // vse OK
  endif;
endif;

// navrat
FormUprOBRIG();
}
?>
