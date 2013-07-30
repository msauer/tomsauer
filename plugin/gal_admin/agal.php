<?php
######################################################################
# phpRS Gallery 0.99.500
######################################################################
// phpRS Copyright (c) 2001-2004 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// Gallery Copyright (c) 2004 by Michal Safus (michalsafus@gmail.com)
// http://www.phprs.cz/magazin/gallery
// This program is free software. - Toto je bezplatny a svobodny software.

function VypisKategorie() {
 $kategorie=mysql_query("SELECT idk,kat_nazev FROM ".$GLOBALS["rspredpona"]."gallery_kategorie",$GLOBALS["dbspojeni"]);
 while($kategorie_po=mysql_fetch_array($kategorie)):
  $GLOBALS["kategorie_pole"][]=$kategorie_po["kat_nazev"];
  $GLOBALS["kategorie_pole_idk"][]=$kategorie_po["idk"];
 endwhile;

 $GLOBALS["pocetkat"]=count($GLOBALS["kategorie_pole"]);
 $vypis.="<select class=\"gal_input\" name=\"kategorie_id\" size=\"1\"> ";
  for ($pom=0;$pom<$GLOBALS["pocetkat"];$pom++):
   $pocitadlo=$pom+1;
   if($pocitadlo==$GLOBALS["cislo_kategorie"]): $selected=" selected";
   else: $selected="";
   endif;
   $vypis.="<option value=\"".$GLOBALS["kategorie_pole_idk"][$pom]."\"".$selected.">".$GLOBALS["kategorie_pole"][$pom]."</option>";
  endfor;
 $vypis.="</select>";
 return $vypis;
}

function GaleriePrehled() {
 $nesmazane=mysql_query("select * from ".$GLOBALS["rspredpona"]."gallery where gallery_delete='0' order by gallery_id",$GLOBALS["dbspojeni"]);
 $nesmazane_pocet=mysql_num_rows($nesmazane);
 $smazane=mysql_query("select * from ".$GLOBALS["rspredpona"]."gallery where gallery_delete='1' order by gallery_id",$GLOBALS["dbspojeni"]);
 $smazane_pocet=mysql_num_rows($smazane);
 echo RS_GAL_NADPIS_PREHLED_NESMAZANE."<br />";
  echo "<table width=\"775\" cellspacing=\"0\" cellpadding=\"2\" border=\"1\" align=\"center\" class=\"ramsedy\">
         <tr bgcolor=\"#E6E6E6\" class=\"txt\">
          <th>".RS_GAL_NAZEV."
          </th>
          <th>".RS_GAL_VLASTNIK."
          </th>
          <th colspan=\"2\">".RS_GAL_UPRAVY."
          </th>
         </tr>";
 for($pom=0;$pom<$nesmazane_pocet;$pom++):
  $admin=mysql_result($nesmazane,$pom,gallery_admin);
  $vlastnik_id=mysql_result($nesmazane,$pom,gallery_user_id);
  switch($admin):
   case "0": $vlastnik=mysql_query("select prezdivka,jmeno from ".$GLOBALS["rspredpona"]."ctenari where idc='".$vlastnik_id."'",$GLOBALS["dbspojeni"]); break;
   case "1": $vlastnik=mysql_query("select user as prezdivka,jmeno from ".$GLOBALS["rspredpona"]."user where idu='".$vlastnik_id."'",$GLOBALS["dbspojeni"]); break;
  endswitch;
  $vlastnik=mysql_fetch_array($vlastnik);
  echo "<tr class=\"txt\"><td><a href=\"".RS_VYKONNYSOUBOR."?modul=gallery&amp;akce=gal_ukaz&amp;gal_id=".mysql_result($nesmazane,$pom,gallery_id)."\">".mysql_result($nesmazane,$pom,gallery_title)."</a></td><td width=\"20%\" title=\"".$vlastnik["jmeno"]."\">".$vlastnik["prezdivka"]."</td><td width=\"10%\"><a href=\"".RS_VYKONNYSOUBOR."?modul=gallery&amp;akce=gal_uprav&amp;gal_id=".mysql_result($nesmazane,$pom,gallery_id)."\">".RS_GAL_UPRAV."</a></td><td width=\"10%\"><a href=\"".RS_VYKONNYSOUBOR."?modul=gallery&amp;akce=gal_smaz&amp;gal_id=".mysql_result($nesmazane,$pom,gallery_id)."\">".RS_GAL_SMAZ."</a></td></tr>";
 endfor;
 echo " <tr class=\"txt\"><th bgcolor=\"#E6E6E6\" colspan=\"4\">".RS_GAL_PREHLED_SMAZANI_NENAPORAD."</th></tr>";
 echo "</table><br /><br />";

 echo RS_GAL_NADPIS_PREHLED_SMAZANE;
 echo "<table width=\"775\" cellspacing=\"0\" cellpadding=\"2\" border=\"1\" align=\"center\" class=\"ramsedy\">
         <tr bgcolor=\"#E6E6E6\" class=\"txt\">
          <th>".RS_GAL_NAZEV."
          </th>
          <th>".RS_GAL_VLASTNIK."
          </th>
          <th colspan=\"2\">".RS_GAL_UPRAVY."
          </th>
         </tr>";
 for($pom=0;$pom<$smazane_pocet;$pom++):
  $admin=mysql_result($smazane,$pom,gallery_admin);
  $vlastnik_id=mysql_result($smazane,$pom,gallery_user_id);
  switch($admin):
   case "0": $podminka="ctenari where idc='".$vlastnik_id."'"; break;
   case "1": $podminka="user where idu='".$vlastnik_id."'"; break;
  endswitch;
  $vlastnik=mysql_query("select jmeno from ".$GLOBALS["rspredpona"].$podminka,$GLOBALS["dbspojeni"]);
  $vlastnik=mysql_fetch_array($vlastnik);
  echo "<tr class=\"txt\"><td><a href=\"".RS_VYKONNYSOUBOR."?modul=gallery&amp;akce=gal_ukaz&amp;gal_id=".mysql_result($smazane,$pom,gallery_id)."\">".mysql_result($smazane,$pom,gallery_title)."</a></td><td width=\"20%\">".$vlastnik["jmeno"]."</td><td width=\"10%\"><a href=\"".RS_VYKONNYSOUBOR."?modul=gallery&amp;akce=gal_obnov&amp;gal_id=".mysql_result($smazane,$pom,gallery_id)."\">".RS_GAL_OBNOV."</a></td><td width=\"10%\"><a href=\"".RS_VYKONNYSOUBOR."?modul=gallery&amp;akce=gal_smaz&amp;gal_id=".mysql_result($smazane,$pom,gallery_id)."\">".RS_GAL_SMAZ."</a></td></tr>";
 endfor;
 echo " <tr class=\"txt\"><th bgcolor=\"#E6E6E6\" colspan=\"4\">".RS_GAL_PREHLED_SMAZANI_NAPORAD."</th></tr>";
 echo "</table>";
}


function GalerieUprav() {
 if($GLOBALS["gal_id"]!=""):
  $udaje=mysql_query("select * from ".$GLOBALS["rspredpona"]."gallery where gallery_id='".$GLOBALS["gal_id"]."'",$GLOBALS["dbspojeni"]);
  $radky=mysql_num_rows($udaje);
  if($radky):
   if($GLOBALS["do_it"]):
    $gallery_title=addslashes($GLOBALS["uprav_nazev"]);
    $gallery_description=addslashes($GLOBALS["uprav_popis"]);
    $gallery_uprava=addslashes($GLOBALS["uprav_uprava"]);
    $gallery_zalozeni=addslashes($GLOBALS["uprav_zalozeni"]);
    $gallery_zobrazit=addslashes($GLOBALS["uprav_obrazek"]);
    $gallery_pomoc=explode(":",addslashes($GLOBALS["uprav_uzivatel"]));
    $gallery_user_id=$gallery_pomoc[0];
    $gallery_admin=$gallery_pomoc[1];
    $mysql=mysql_query("update ".$GLOBALS["rspredpona"]."gallery set gallery_user_id='".$gallery_user_id."',gallery_admin='".$gallery_admin."',gallery_title='".$gallery_title."', gallery_zobrazit='".$gallery_zobrazit."', gallery_zalozeni='".$gallery_zalozeni."', gallery_uprava='".$gallery_uprava."', gallery_description='".$gallery_description."' where gallery_id='".$GLOBALS["gal_id"]."'",$GLOBALS["dbspojeni"]);

    if($_POST["uprav_kategorie_upravit"]):
    $uprav_obrazky=mysql_query("UPDATE ".$GLOBALS["rspredpona"]."media SET media_category='".$GLOBALS["kategorie_id"]."' WHERE media_gallery_id='".$GLOBALS["gal_id"]."'",$GLOBALS["dbspojeni"]);
    if($uprav_obrazky): echo "Obrázky zařazeny do jiné kategorie.<br />";
    else: echo "Obrázky se nepodařilo přeřadit.<br />";
    endif;
    endif;

    if($mysql): echo RS_GAL_UPRAV_OK;
    else: echo RS_GAL_UPRAV_KO;
    endif;
   else:
  $admin=mysql_result($udaje,0,gallery_admin);
  $vlastnik_id=mysql_result($udaje,0,gallery_user_id);
  switch($admin):
   case "0": $podminka="ctenari where idc='".$vlastnik_id."'"; break;
   case "1": $podminka="user where idu='".$vlastnik_id."'"; break;
  endswitch;

  switch(mysql_result($udaje,0,gallery_zobrazit)):
   case "1": $hodn="selected"; break;
   case "2": $zobr="selected"; break;
   case "3": $naho="selected"; break;
  endswitch;

   echo "
   <form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
   <input type=\"hidden\" name=\"modul\" value=\"gallery\">
   <input type=\"hidden\" name=\"akce\" value=\"gal_uprav\">
   <input type=\"hidden\" name=\"do_it\" value=\"1\">
   <input type=\"hidden\" name=\"gal_id\" value=\"".$GLOBALS["gal_id"]."\">
   <table width=\"775\" cellspacing=\"0\" cellpadding=\"2\" border=\"1\" align=\"center\" class=\"ramsedy\" >
    <tr class=\"txt\" bgcolor=\"#E6E6E6\">
     <th width=\"50%\">".RS_GAL_NAZEV."
     </th>
     <th width=\"50%\">".RS_GAL_HODNOTA."
     </th>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_UPRAV_ID."
     </td>
     <td>".mysql_result($udaje,0,gallery_id)."
     </td>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_UPRAV_UZIVATEL."
     </td>
     <td>".VypisUzivatele((mysql_result($udaje,0,gallery_user_id)),(mysql_result($udaje,0,gallery_admin)))."
     </td>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_UPRAV_NAZEV."
     </td>
     <td><input type=\"text\" name=\"uprav_nazev\" value=\"".mysql_result($udaje,0,gallery_title)."\" size=\"40\" class=\"textpole\" />
     </td>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_UPRAV_POPIS."
     </td>
     <td><textarea class=\"textbox\" name=\"uprav_popis\" rows=\"7\" cols=\"40\">".mysql_result($udaje,0,gallery_description)."</textarea>
     </td>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_UPRAV_ZALOZENI."
     </td>
     <td><input type=\"text\" name=\"uprav_zalozeni\" value=\"".mysql_result($udaje,0,gallery_zalozeni)."\" size=\"22\" class=\"textpole\" /> RRRR-MM-DD HH:MM:SS
     </td>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_UPRAV_UPRAVA."
     </td>
     <td><input type=\"text\" name=\"uprav_uprava\" value=\"".mysql_result($udaje,0,gallery_uprava)."\" size=\"22\" class=\"textpole\" /> RRRR-MM-DD HH:MM:SS
     </td>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_UPRAV_OBRAZEK."
     </td>
     <td><select name=\"uprav_obrazek\">
          <option value=\"1\" $hodn>".RS_GAL_UPRAV_OBRAZEK_HODN."</option>
          <option value=\"2\" $zobr>".RS_GAL_UPRAV_OBRAZEK_ZOBR."</option>
          <option value=\"3\" $naho>".RS_GAL_UPRAV_OBRAZEK_NAHO."</option>
         </select>
     </td>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_UPRAV_KATEGORIE_UPRAVIT."
     </td>
     <td><input type=\"radio\" name=\"uprav_kategorie_upravit\" value=\"1\" /> Ano - <input type=\"radio\" name=\"uprav_kategorie_upravit\" value=\"0\" checked/> Ne
     </td>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_UPRAV_KATEGORIE_VYPIS."
     </td>
     <td>".VypisKategorie()."
     </td>
    </tr>
    <tr class=\"txt\">
     <td colspan=\"2\" align=\"center\">
      <input type=\"submit\" class=\"tl\" value=\"".RS_GAL_UPRAV_UPRAVIT."\">
     </td>
    </tr>
   </table>
  </form>";
   endif;
  else:
   echo RS_GAL_CHYBA_ID_RS_GAL_NEEX;
  endif;
 else:
  echo RS_GAL_CHYBA_ID_RS_GAL_NENI;
 endif;
}




function GalerieObnov() {
 if($GLOBALS["gal_id"]!=""):
  $udaje=mysql_query("select gallery_delete from ".$GLOBALS["rspredpona"]."gallery where gallery_id='".$GLOBALS["gal_id"]."'",$GLOBALS["dbspojeni"]);
  $radky=mysql_num_rows($udaje);
  if($radky):
   if(!mysql_result($udaje,0,gallery_delete)):
    echo RS_GAL_CHYBA_OBNOV_NEE;
   else:
    $mysql=mysql_query("update ".$GLOBALS["rspredpona"]."gallery set gallery_delete='0' where gallery_id='".$GLOBALS["gal_id"]."'");
    $mysql.=mysql_query("update ".$GLOBALS["rspredpona"]."media set media_smazano='0' where media_gallery_id='".$GLOBALS["gal_id"]."'");
    if($mysql):
     echo RS_GAL_OBNOV_OK;
    else:
     echo RS_GAL_OBNOV_KO;
    endif;
   endif;
  else:
   echo RS_GAL_CHYBA_ID_RS_GAL_NEEX;
  endif;
 else:
  echo RS_GAL_CHYBA_ID_RS_GAL_NENI;
 endif;

}

function GalerieSmaz() {
 if($GLOBALS["gal_id"]!=""):
  $udaje=mysql_query("select gallery_title,gallery_delete from ".$GLOBALS["rspredpona"]."gallery where gallery_id='".$GLOBALS["gal_id"]."'",$GLOBALS["dbspojeni"]);
  $radky=mysql_num_rows($udaje);
  if($radky):
   $smazano=mysql_result($udaje,0,gallery_delete);
   if($smazano):
    $obrazky=mysql_query("select * from ".$GLOBALS["rspredpona"]."media where media_gallery_id='".$GLOBALS["gal_id"]."'",$GLOBALS["dbspojeni"]);
    $pocet_obrazku=mysql_num_rows($obrazky);
    for($pom=0;$pom<$pocet_obrazku;$pom++):
     ObrazekSmaz(mysql_result($obrazky,$pom,media_id));
    endfor;
    $del2=mysql_query("delete from ".$GLOBALS["rspredpona"]."gallery where gallery_id='".$GLOBALS["gal_id"]."'",$GLOBALS["dbspojeni"]);
    if(del2):
     echo RS_GAL_SMAZ_OK;
    else:
     echo RS_GAL_SMAZ_KO;
    endif;

   else:
    $mysql=mysql_query("update ".$GLOBALS["rspredpona"]."gallery set gallery_delete='1' where gallery_id='".$GLOBALS["gal_id"]."'");
    $mysql.=mysql_query("update ".$GLOBALS["rspredpona"]."media set media_smazano='1' where media_gallery_id='".$GLOBALS["gal_id"]."'");
    if($mysql):
     echo RS_GAL_SMAZ_OK2;
    else:
     echo RS_GAL_SMAZ_KO;
    endif;
   endif;
  else:
   echo RS_GAL_CHYBA_ID_RS_GAL_NEEX;
  endif;
 else:
  echo RS_GAL_CHYBA_ID_RS_GAL_NENI;
 endif;
}


function GalerieUkaz() {
 if($GLOBALS["gal_id"]!=""):
  $udaje=mysql_query("select * from ".$GLOBALS["rspredpona"]."gallery where gallery_id='".$GLOBALS["gal_id"]."'",$GLOBALS["dbspojeni"]);
  $radky=mysql_num_rows($udaje);
  if($radky):
  $admin=mysql_result($udaje,0,gallery_admin);
  $vlastnik_id=mysql_result($udaje,0,gallery_user_id);
  switch($admin):
   case "0": $podminka="ctenari where idc='".$vlastnik_id."'"; break;
   case "1": $podminka="user where idu='".$vlastnik_id."'"; break;
  endswitch;
  $vlastnik=mysql_query("select jmeno from ".$GLOBALS["rspredpona"].$podminka,$GLOBALS["dbspojeni"]);
  $vlastnik=mysql_fetch_array($vlastnik);

  echo "
   <table width=\"775\" cellspacing=\"0\" cellpadding=\"2\" border=\"1\" align=\"center\" class=\"ramsedy\">
    <tr class=\"txt\" bgcolor=\"#E6E6E6\">
     <th width=\"50%\">".RS_GAL_NAZEV."
     </th>
     <th width=\"50%\">".RS_GAL_HODNOTA."
     </th>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_UPRAV_ID."
     </td>
     <td>".mysql_result($udaje,0,gallery_id)."
     </td>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_UPRAV_UZIVATEL."
     </td>
     <td>".$vlastnik[jmeno]."
     </td>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_UPRAV_NAZEV."
     </td>
     <td>".mysql_result($udaje,0,gallery_title)."
     </td>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_UPRAV_POPIS."
     </td>
     <td>".mysql_result($udaje,0,gallery_description)."
     </td>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_UPRAV_ZALOZENI."
     </td>
     <td>".mysql_result($udaje,0,gallery_zalozeni)."
     </td>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_UPRAV_UPRAVA."
     </td>
     <td>".mysql_result($udaje,0,gallery_uprava)."
     </td>
    </tr>
    <tr class=\"txt\">
     <th colspan=\"2\"><a href=\"".RS_VYKONNYSOUBOR."?modul=gallery&amp;akce=gal_uprav&amp;gal_id=".$GLOBALS["gal_id"]."\">".RS_GAL_UPRAV_UPRAV."</a>
     </th>
    </tr>
  </table><br /><br />";

echo "<table width=\"775\" cellspacing=\"0\" cellpadding=\"2\" border=\"1\" align=\"center\" class=\"ramsedy\">
       <tr bgcolor=\"#E6E6E6\" class=\"txt\"><th width=\"50%\">".RS_GAL_NAZEV." + ".RS_GAL_POPIS."</th><th width=\"30%\">".RS_GAL_NAHLED."</th><th width=\"20%\" colspan=\"2\">".RS_GAL_UPRAVY."</th></tr>";


  $obrazky=mysql_query("select * from ".$GLOBALS["rspredpona"]."media where media_gallery_id='".$GLOBALS["gal_id"]."'",$GLOBALS["dbspojeni"]);
  $obrazky_pocet=mysql_num_rows($obrazky);

  for($pom=0;$pom<$obrazky_pocet;$pom++):
   echo "<tr class=\"txt\"><td><i>".mysql_result($obrazky,$pom,media_caption)."</i><br />".mysql_result($obrazky,$pom,media_description)."</td>
         <td align=\"center\"><a href=\"".mysql_result($obrazky,$pom,media_file)."\"><img src=\"".mysql_result($obrazky,$pom,media_thumbnail)."\" width=\"".mysql_result($obrazky,$pom,media_thumbnail_width)."\" height=\"".mysql_result($obrazky,$pom,media_thumbnail_height)."\" alt=\"".mysql_result($obrazky,$pom,media_description)."\" title=\"".mysql_result($obrazky,$pom,media_description)."\" style=\"border: 2px solid #000000\"></a></td>
         <td><a href=\"".RS_VYKONNYSOUBOR."?modul=gallery&amp;akce=obr_uprav&amp;media_id=".mysql_result($obrazky,$pom,media_id)."\">".RS_GAL_UPRAV."</a></td><td><a href=\"".RS_VYKONNYSOUBOR."?modul=gallery&amp;akce=obr_smaz&amp;media_id=".mysql_result($obrazky,$pom,media_id)."\">".RS_GAL_SMAZ."</a></td></tr>
   ";
  endfor;

  else:
   echo RS_GAL_CHYBA_ID_RS_GAL_NEEX;
  endif;
 else:
  echo RS_GAL_CHYBA_ID_RS_GAL_NENI;
 endif;
}

function VypisUzivatele($id,$admin) {
 $dotaz_ctenari=mysql_query("select idc,prezdivka,jmeno from ".$GLOBALS["rspredpona"]."ctenari order by idc",$GLOBALS["dbspojeni"]);
 $dotaz_adminis=mysql_query("select idu,user,jmeno from ".$GLOBALS["rspredpona"]."user where admin='2' order by idu",$GLOBALS["dbspojeni"]);
 $dotaz_autorii=mysql_query("select idu,user,jmeno from ".$GLOBALS["rspredpona"]."user where admin='0' order by idu",$GLOBALS["dbspojeni"]);
 $dotaz_redakto=mysql_query("select idu,user,jmeno from ".$GLOBALS["rspredpona"]."user where admin='1' order by idu",$GLOBALS["dbspojeni"]);
 $ctenari=mysql_num_rows($dotaz_ctenari);
 $adminis=mysql_num_rows($dotaz_adminis);
 $autorii=mysql_num_rows($dotaz_autorii);
 $redakto=mysql_num_rows($dotaz_redakto);


 $vratit.="<select name=\"uprav_uzivatel\">";
 for($pom=0;$pom<$ctenari;$pom++):
  if((mysql_result($dotaz_ctenari,$pom,idc))==$id and $admin==0): $selected="selected"; else: $selected=""; endif;
  $vratit.="<option ".$selected." value=\"".mysql_result($dotaz_ctenari,$pom,idc).":0\">".mysql_result($dotaz_ctenari,$pom,prezdivka)." - ".mysql_result($dotaz_ctenari,$pom,jmeno)." (čtenář)</option>";
 endfor;
 for($pom=0;$pom<$adminis;$pom++):
  if((mysql_result($dotaz_adminis,$pom,idu))==$id and $admin==1): $selected="selected"; else: $selected=""; endif;
  $vratit.="<option ".$selected." value=\"".mysql_result($dotaz_adminis,$pom,idu).":1\">".mysql_result($dotaz_adminis,$pom,user)." - ".mysql_result($dotaz_adminis,$pom,jmeno)." (administrátor)</option>";
 endfor;
 for($pom=0;$pom<$redakto;$pom++):
  if((mysql_result($dotaz_redakto,$pom,idu))==$id and $admin==1): $selected="selected"; else: $selected=""; endif;
  $vratit.="<option ".$selected." value=\"".mysql_result($dotaz_redakto,$pom,idu).":1\">".mysql_result($dotaz_redakto,$pom,user)." - ".mysql_result($dotaz_redakto,$pom,jmeno)." (redaktor)</option>";
 endfor;
 for($pom=0;$pom<$autorii;$pom++):
  if((mysql_result($dotaz_autorii,$pom,idu))==$id and $admin==1): $selected="selected"; else: $selected=""; endif;
  $vratit.="<option ".$selected." value=\"".mysql_result($dotaz_autorii,$pom,idu).":1\">".mysql_result($dotaz_autorii,$pom,user)." - ".mysql_result($dotaz_autorii,$pom,jmeno)." (autor)</option>";
 endfor;
  $vratit.="</select>";
return $vratit;
}


function ObrazekUprav() {
  if($GLOBALS["media_id"]!=""):
  $udaje=mysql_query("select * from ".$GLOBALS["rspredpona"]."media where media_id='".$GLOBALS["media_id"]."'",$GLOBALS["dbspojeni"]);
  $radky=mysql_num_rows($udaje);
  if($radky):
   if($GLOBALS["do_it"]):
    $obrazek_caption=addslashes($GLOBALS["uprav_nazev"]);
    $obrazek_description=addslashes($GLOBALS["uprav_popis"]);
    $obrazek_galerie=addslashes($GLOBALS["uprav_galerie"]);
    $obrazek_kategorie=addslashes($GLOBALS["kategorie_id"]);
    $obrazek_velikost=addslashes($GLOBALS["uprav_velikost_obrazek"]);
    $obrazek_height=addslashes($GLOBALS["uprav_rozmer_obrazek_height"]);
    $obrazek_width=addslashes($GLOBALS["uprav_rozmer_obrazek_width"]);
    $nahled_height=addslashes($GLOBALS["uprav_rozmer_nahled_height"]);
    $nahled_width=addslashes($GLOBALS["uprav_rozmer_nahled_width"]);
    $mysql=mysql_query("update ".$GLOBALS["rspredpona"]."media set media_size='".$obrazek_velikost."',media_category='".$obrazek_kategorie."',media_height='".$obrazek_height."',media_width='".$obrazek_width."',media_thumbnail_height='".$nahled_height."', media_thumbnail_width='".$nahled_width."', media_gallery_id='".$obrazek_galerie."', media_caption='".$obrazek_caption."',media_description='".$obrazek_description."' where media_id='".$GLOBALS["media_id"]."' ",$GLOBALS["dbspojeni"]);
    if($mysql):
     echo RS_GAL_OBR_UPRAV_OK;
    else:
     echo RS_GAL_OBR_UPRAV_KO;
    endif;
   else:
   if ((mysql_result($udaje,0,media_hodnotilo))>0):
    $znamka=number_format(((mysql_result($udaje,0,media_znamka))/(mysql_result($udaje,0,media_hodnotilo))),2,'.','');
   else:
    $znamka="0";
   endif;

   $galerie_udaje=mysql_query("select gallery_user_id,gallery_admin,gallery_id,gallery_title from ".$GLOBALS["rspredpona"]."gallery where gallery_delete='0'",$GLOBALS["dbspojeni"]);
   $galerie_pocet=mysql_num_rows($galerie_udaje);
   $galerie="<select name=\"uprav_galerie\">";
    for($pom;$pom<$galerie_pocet;$pom++):
     $admin=mysql_result($galerie_udaje,$pom,gallery_admin);
     $vlastnik_id=mysql_result($galerie_udaje,$pom,gallery_user_id);
     switch($admin):
      case "0": $podminka="ctenari where idc='".$vlastnik_id."'"; break;
      case "1": $podminka="user where idu='".$vlastnik_id."'"; break;
     endswitch;
     $vlastnik=mysql_query("select jmeno from ".$GLOBALS["rspredpona"].$podminka,$GLOBALS["dbspojeni"]);
     $vlastnik=mysql_fetch_array($vlastnik);
     if((mysql_result($udaje,0,media_gallery_id))==mysql_result($galerie_udaje,$pom,gallery_id)): $selected="selected"; endif;
     $galerie.="<option $selected value=\"".mysql_result($galerie_udaje,$pom,gallery_id)."\">".mysql_result($galerie_udaje,$pom,gallery_title)." (".$vlastnik["jmeno"].")</option>";
     $selected="";
    endfor;
   $galerie.="</select>";
   $GLOBALS["cislo_kategorie"]=mysql_result($udaje,0,media_category);
   $kategorie=VypisKategorie();

   $rozmery_nahled=getimagesize(mysql_result($udaje,0,media_thumbnail)); // 0 - height; 1 - width
   $rozmery_obrazek=getimagesize(mysql_result($udaje,0,media_file)); // 0 - height; 1 - width
   $velikost_nahled=ceil((filesize(mysql_result($udaje,0,media_thumbnail)))/1024);
   $velikost_obrazek=ceil((filesize(mysql_result($udaje,0,media_file)))/1024);


  echo "
   <form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
   <input type=\"hidden\" name=\"modul\" value=\"gallery\">
   <input type=\"hidden\" name=\"akce\" value=\"obr_uprav\">
   <input type=\"hidden\" name=\"do_it\" value=\"1\">
   <input type=\"hidden\" name=\"media_id\" value=\"".$GLOBALS["media_id"]."\">
   <table width=\"775\" cellspacing=\"0\" cellpadding=\"2\" border=\"1\" align=\"center\" class=\"ramsedy\">
    <tr class=\"txt\" bgcolor=\"#E6E6E6\">
     <th width=\"50%\">".RS_GAL_NAZEV."
     </th>
     <th width=\"50%\">".RS_GAL_HODNOTA."
     </th>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_OBR_UPRAV_ID."
     </td>
     <td>".mysql_result($udaje,0,media_id)."
     </td>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_OBR_UPRAV_NAZEV."
     </td>
     <td><input type=\"text\" name=\"uprav_nazev\" value=\"".mysql_result($udaje,0,media_caption)."\" size=\"40\" class=\"textpole\" />
     </td>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_OBR_UPRAV_POPIS."
     </td>
     <td><textarea class=\"textbox\" name=\"uprav_popis\" rows=\"7\" cols=\"40\">".mysql_result($udaje,0,media_description)."</textarea>
     </td>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_OBR_UPRAV_NAHLED."
     </td>
     <td><img src=\"".mysql_result($udaje,0,media_thumbnail)."\" width=\"".mysql_result($udaje,0,media_thumbnail_width)."\" height=\"".mysql_result($udaje,0,media_thumbnail_height)."\" title=\"".mysql_result($udaje,0,media_description)."\" alt=\"".mysql_result($udaje,0,media_description)."\">
     </td>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_OBR_UPRAV_ROZMERY_OBRAZEK."
     </td>
     <td>
      <input type=\"text\" size=\"4\" name=\"uprav_rozmer_obrazek_width\" class=\"textpole\" value=\"".mysql_result($udaje,0,media_width)."\">px x <input type=\"text\" size=\"4\" name=\"uprav_rozmer_obrazek_height\" class=\"textpole\" value=\"".mysql_result($udaje,0,media_height)."\">px &nbsp; &nbsp; (".$rozmery_obrazek[0]."px x ".$rozmery_obrazek[1]."px)
     </td>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_OBR_UPRAV_ROZMERY_NAHLED."
     <td>
      <input type=\"text\" size=\"4\" name=\"uprav_rozmer_nahled_width\" class=\"textpole\" value=\"".mysql_result($udaje,0,media_thumbnail_width)."\">px x <input type=\"text\" size=\"4\" name=\"uprav_rozmer_nahled_height\" class=\"textpole\" value=\"".mysql_result($udaje,0,media_thumbnail_height)."\">px &nbsp; &nbsp; (".$rozmery_nahled[0]."px x ".$rozmery_nahled[1]."px)
     </td>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_OBR_UPRAV_VELIKOST."
     </td>
     <td><input type=\"text\" size=\"3\" name=\"uprav_velikost_obrazek\" class=\"textpole\" value=\"".mysql_result($udaje,0,media_size)."\"> kB &nbsp; &nbsp; (".$velikost_obrazek." kB)
     </td>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_OBR_UPRAV_VELIKOST_NAHLED."
     </td>
     <td>".$velikost_nahled." kB
     </td>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_OBR_UPRAV_ZOBRAZENI."
     </td>
     <td>".mysql_result($udaje,0,media_view)."
     </td>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_OBR_UPRAV_HODNOTILO."
     </td>
     <td>".mysql_result($udaje,0,media_hodnotilo)."
     </td>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_OBR_UPRAV_ZNAMKA."
     </td>
     <td>".$znamka."
     </td>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_OBR_UPRAV_GALERIE."
     </td>
     <td>".$galerie."
     </td>
    </tr>
    <tr class=\"txt\">
     <td>".RS_GAL_OBR_UPRAV_KATEGORIE."
     </td>
     <td>".$kategorie."
     </td>
    </tr>
    <tr class=\"txt\">
     <td colspan=\"2\" align=\"center\">
      <input type=\"submit\" class=\"tl\" value=\"".RS_GAL_OBR_UPRAV_UPRAVIT."\">
     </td>
    </tr>
   </table>
  </form>";
   endif;
  else:
   echo RS_GAL_OBR_CHYBA_ID_RS_GAL_OBR_NEEX;
  endif;
 else:
  echo RS_GAL_OBR_CHYBA_ID_RS_GAL_OBR_NENI;
 endif;

}

function ObrazekSmaz($id) {
  if($id!=""):
  $udaje=mysql_query("select * from ".$GLOBALS["rspredpona"]."media where media_id='".$id."'",$GLOBALS["dbspojeni"]);
  $radky=mysql_num_rows($udaje);
  if($radky):
   $file=mysql_result($udaje,0,media_file);
   $nahled=mysql_result($udaje,0,media_thumbnail);
   $del1=unlink($file);
   $del2=unlink($nahled);
   $del3=mysql_query("delete from ".$GLOBALS["rspredpona"]."media where media_id='".$id."'",$GLOBALS["dbspojeni"]);
   if($del1 and $del2 and $del3):
    echo RS_GAL_OBR_SMAZ_OK;
   else:
    echo RS_GAL_OBR_SMAZ_KO;
   endif;
  else:
   echo RS_GAL_OBR_CHYBA_ID_RS_GAL_OBR_NEEX;
  endif;
 else:
  echo RS_GAL_OBR_CHYBA_ID_RS_GAL_OBR_NENI;
 endif;
}





function VypisKom()
{
// kocet clanku s komentari
$dotazmno=mysql_query("select distinct obrazek from ".$GLOBALS["rspredpona"]."media_komentare",$GLOBALS["dbspojeni"]);
//$pocet=mysql_Result($dotazmno,0,"pocet");
$pocet=mysql_num_rows($dotazmno);

if (!isset($GLOBALS["aemin"])): // kdyz neni definovan interval
  if($pocet<20):
    $GLOBALS["aemin"]=0;
    $GLOBALS["aemax"]=$pocet;
  else:
    $GLOBALS["aemin"]=0;
    $GLOBALS["aemax"]=20;
  endif;
endif;

echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<p align=\"center\" class=\"txt\">
<input type=\"hidden\" name=\"modul\" value=\"gallery\">
<input type=\"hidden\" name=\"akce\" value=\"Gal_Comment\" />
<input type=\"submit\" value=\" Zobraz obrázky s komentáři \" class=\"tl\" />
od <input type=\"text\" name=\"aemin\" value=\"".$GLOBALS["aemin"]."\" size=\"4\" class=\"textpole\" />
do <input type=\"text\" name=\"aemax\" value=\"".$GLOBALS["aemax"]."\" size=\"4\" class=\"textpole\" />
(Celkový počet obrázků s komentáři: ".$pocet.")
<p></form>
<hr width=\"600\" />
<p></p>\n";

// informace
echo "<p align=\"center\" class=\"txt\">Obrázky jsou seřazeny dle data poslední aktualizace respektive podle data přidání posledního komentáře.</p>\n";


// vypocet omezeni
if ($GLOBALS["aemin"]==0): $upr_min=0; else: $upr_min=($GLOBALS["aemin"]-1); endif;
$kolik=$GLOBALS["aemax"]-$upr_min;

$dotaz="select c.media_id,c.media_caption,max(k.datum) as posledni,k.od from ".$GLOBALS["rspredpona"]."media as c, ".$GLOBALS["rspredpona"]."media_komentare as k ";
$dotaz.="where c.media_id = k.obrazek group by c.media_id ";
$dotaz.="order by posledni desc limit ".$upr_min.",".$kolik;

$dotazkom=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetkom=mysql_num_rows($dotazkom);

if ($pocetkom==0):
  echo "<p align=\"center\" class=\"txt\">Zadaný interval (od ".$GLOBALS["aemin"]." do ".$GLOBALS["aemax"].") je prázdný!</p>\n";
else:
  echo "<table cellspacing=\"0\" cellpadding=\"2\" border=\"1\" align=\"center\" class=\"ramsedy\">\n";
  echo "<tr class=\"txt\" bgcolor=\"#E6E6E6\">";
  echo "<td align=\"center\"><b>ID</b></td>";
  echo "<td align=\"center\" width=\"300\"><b>Titulek</b></td>";
  echo "<td align=\"center\"><b>Datum poslední aktualizace</b></td>";
  echo "<td align=\"center\"><b>Akce</b></td></tr>\n";
  for ($pom=0;$pom<$pocetkom;$pom++):
    $pole_data=mysql_fetch_assoc($dotazkom);
    echo "<tr class=\"txt\">\n";
    echo "<td align=\"center\">".$pole_data["media_id"]."</td>\n";
    echo "<td align=\"left\" width=\"300\">".$pole_data["media_caption"]."</td>\n";
    echo "<td align=\"center\">".MyDatetimeToDate($pole_data["posledni"])."</td>\n";
    echo "<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?modul=gallery&amp;akce=Gal_ShowComment&amp;prlink=".$pole_data["media_id"]."\">Zobrazit</a></td></tr>\n";
  endfor;
  echo "</table>\n";
endif;
echo "<p></p>\n";
}

function UpravKom()
{
$GLOBALS["prlink"]=addslashes($GLOBALS["prlink"]);

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?modul=gallery&amp;akce=Gal_Comment\">Zpět na hlavní stránku sekce</a></p>\n";

// nazev clanku
$dotaz="select media_caption from ".$GLOBALS["rspredpona"]."media where media_id='".$GLOBALS["prlink"]."'";
$dotazcla=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
if (mysql_num_rows($dotazcla)>0):
  echo "<p align=\"center\" class=\"txt\"><strong><big>".mysql_Result($dotazcla,0,"media_caption")."</big></strong></p>\n";
endif;

// vypis
$dotazkom=mysql_query("select * from ".$GLOBALS["rspredpona"]."media_komentare where obrazek='".$GLOBALS["prlink"]."' order by idk desc",$GLOBALS["dbspojeni"]);
$pocetkom=mysql_num_rows($dotazkom);
for ($pom=0;$pom<$pocetkom;$pom++):
  $pole_data=mysql_fetch_assoc($dotazkom);
  echo "<table cellspacing=\"0\" cellpadding=\"2\" border=\"1\" align=\"center\" width=\"600\" class=\"ramsedy\">\n";
  echo "<tr class=\"txt\"><td align=\"left\" width=\"180\"><b>Datum:</b></td><td align=\"left\" width=\"420\">".$pole_data["datum"]."</td></tr>\n";
  echo "<tr class=\"txt\"><td align=\"left\"><b>Komentář vložil:</b></td><td align=\"left\">".$pole_data["od"]."</td></tr>\n";
  echo "<tr class=\"txt\"><td align=\"left\"><b>E-mail:</b></td><td align=\"left\"><a href=\"mailto:".$pole_data["od_mail"]."\">".$pole_data["od_mail"]."</a></td></tr>\n";
  echo "<tr class=\"txt\"><td align=\"left\"><b>IP adresa:</b></td><td align=\"left\">".$pole_data["od_ip"]."</td></tr>\n";
  echo "<tr class=\"txt\"><td align=\"left\"><b>Titulek:</b></td><td align=\"left\">".$pole_data["titulek"]."</td></tr>\n";
  echo "<tr class=\"txt\"><td align=\"left\" colspan=\"2\"><hr /><b>Komentář:</b><br /><div class=\"smltxt\">".$pole_data["obsah"]."</div><hr /></td></tr>\n";
  echo "<tr class=\"txt\"><td align=\"center\" colspan=\"2\">";
  echo "<a href=\"".RS_VYKONNYSOUBOR."?modul=gallery&amp;akce=Gal_DeleteComment&amp;pridk=".$pole_data["idk"]."\">Vymaž obsah komentáře</a><sup>*</sup>";
  echo " &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ";
  echo "<a href=\"".RS_VYKONNYSOUBOR."?modul=gallery&amp;akce=Gal_DelAllComment&amp;pridk=".$pole_data["idk"]."\">Vymaž celý komentář</a>";
  echo "</td></tr>\n";
  echo "</table>\n";
  echo "<p></p>\n";
endfor;
echo "<p></p>
<table border=\"0\" align=\"center\" width=\"85%\"><tr><td align=\"center\" class=\"txt\">
<sup>*</sup> Tato akce vymaže obsah příslušného komentáře a nahradí jej následujícím textem: \"Text tohoto komentáře byl vymazán, jelikož
porušoval publikační pravidla našeho časopisu! Redakce\"
</td></tr></table>
<p></p>\n";
}

function VymazKom()
{
$chybatxt="Text tohoto komentáře byl vymazán, jelikož porušoval publikační pravidla našeho časopisu! Redakce";

@$error=mysql_query("update ".$GLOBALS["rspredpona"]."media_komentare set obsah='".$chybatxt."' where idk='".addslashes($GLOBALS["pridk"])."'",$GLOBALS["dbspojeni"]);
if (!$error):
  echo "<p align=\"center\" class=\"txt\">Error C1: Akce neproběhla úspěšně!</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">Akce proběhla úspěšně! Obsah komentáře byl aktualizován.</p>\n";
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?modul=gallery&amp;akce=Gal_Comment\">Zpět na hlavní stránku sekce</a></p>\n";
}

function VymazCelyKom()
{
$GLOBALS["pridk"]=addslashes($GLOBALS["pridk"]);

// zjisteni linku na clanek
$dotazkom=mysql_query("select clanek from ".$GLOBALS["rspredpona"]."media_komentare where idk='".$GLOBALS["pridk"]."'",$GLOBALS["dbspojeni"]);
if (mysql_num_rows($dotazkom)>0):
  $cla_link=mysql_Result($dotazkom,0,"clanek");
else:
  $cla_link="";
endif;

@$error=mysql_query("delete from ".$GLOBALS["rspredpona"]."media_komentare where idk='".$GLOBALS["pridk"]."'",$GLOBALS["dbspojeni"]);
if (!$error):
  echo "<p align=\"center\" class=\"txt\">Error C2: Akce neproběhla úspěšně!</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">Akce proběhla úspěšně! Komentář byl vymazán.</p>\n";
  mysql_query("update ".$GLOBALS["rspredpona"]."clanky set kom=kom-1 where link='".$cla_link."'",$GLOBALS["dbspojeni"]);
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?modul=gallery&amp;akce=Gal_Comment\">Zpět na hlavní stránku sekce</a></p>\n";
}

function KategoriePrehled() {
$vyber=mysql_query("SELECT idk,kat_nazev,kat_popis from ".$GLOBALS["rspredpona"]."gallery_kategorie order by idk",$GLOBALS["dbspojeni"]);
 echo "<table cellspacing=\"0\" cellpadding=\"2\" border=\"1\" align=\"center\" width=\"600\" class=\"ramsedy\">\n";
 echo "<tr bgcolor=\"#E6E6E6\" class=\"txt\"><th width=\"30\">ID</th><th width=\"150\">Název</th><th width=\"250\">Popis</th><th width=\"150\">Akce</th></tr>\n";
  while($kategorie=mysql_fetch_array($vyber)):
   echo "<tr class=\"txt\"><td align=\"left\">".$kategorie["idk"]."</td><td align=\"left\">".$kategorie["kat_nazev"]."</td><td align=\"left\">".$kategorie["kat_popis"]."</td><td align=\"left\"><a href=\"".RS_VYKONNYSOUBOR."?modul=gallery&amp;akce=kat_uprav&amp;kat_id=".$kategorie["idk"]."\">Edituj</a> - <a href=\"".RS_VYKONNYSOUBOR."?modul=gallery&amp;akce=kat_smaz&amp;kat_id=".$kategorie["idk"]."\">Smaž</a></td></tr>\n";
  endwhile;
echo "</table>";
echo "<p align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?modul=gallery&amp;akce=kat_pridej\">Přidat novou kategorii</p>";
}

function KategorieSmaz()  {
 if(!$_GET["do_it"]):
  $mysql=mysql_query("select kat_nazev from ".$GLOBALS["rspredpona"]."gallery_kategorie where idk=".$GLOBALS["kat_id"],$GLOBALS["dbspojeni"]);
  $kat=mysql_fetch_array($mysql);
  echo "Opravdu chcete smazat kategorii \"".$kat["kat_nazev"]."\"?";
  echo "<br /><a href=\"".RS_VYKONNYSOUBOR."?modul=gallery&amp;akce=kat_smaz&amp;kat_id=".$GLOBALS["kat_id"]."&amp;do_it=1\">Ano</a> - <a href=\"".RS_VYKONNYSOUBOR."?modul=gallery&amp;akce=kat_prehled\">Ne</a>";
 else:
  $mysql=mysql_query("delete from ".$GLOBALS["rspredpona"]."gallery_kategorie where idk=".$GLOBALS["kat_id"],$GLOBALS["dbspojeni"]);
  if($mysql):
   echo "Kategorie úspěšně smazána";
  else:
   echo "Kategorii se nepodařilo smazat";
  endif;
 endif;
}

function KategoriePridej() {
if(!$_POST["do_it"]):
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">";
 echo "<table cellspacing=\"0\" cellpadding=\"2\" border=\"1\" align=\"center\" width=\"500\" class=\"ramsedy\">\n";
 echo "<tr class=\"txt\"><td>Název</td><td align=\"center\"><input type=\"text\" size=\"50\" name=\"kat_nazev\" class=\"textpole\"></td></tr>";
 echo "<tr class=\"txt\"><td>Popis</td><td align=\"center\"><input type=\"text\" size=\"50\" name=\"kat_popis\" class=\"textpole\"></td></tr>";
 echo "<tr class=\"txt\"><td align=\"center\" colspan=\"2\"><input type=\"submit\" value=\" Přidat \" class=\"tl\"></td></tr>";
 echo "</table>";
 echo "<input type=\"hidden\" name=\"akce\" value=\"kat_pridej\">";
 echo "<input type=\"hidden\" name=\"modul\" value=\"gallery\">";
 echo "<input type=\"hidden\" name=\"do_it\" value=\"1\">";
echo "</form>" ;
else:

 $mysql=mysql_query("insert into ".$GLOBALS["rspredpona"]."gallery_kategorie values('','".$GLOBALS["kat_nazev"]."','".$GLOBALS["kat_popis"]."','','','','')",$GLOBALS["dbspojeni"]);
 if($mysql): echo "Kategorie založena";
 else: echo "Kategroii se nepodařilo založit";
 endif;
endif;
}

function KategorieUprav() {
if(!$_POST["do_it"]):
 $mysql=mysql_query("select idk,kat_nazev,kat_popis from ".$GLOBALS["rspredpona"]."gallery_kategorie where idk='".$GLOBALS["kat_id"]."'",$GLOBALS["dbspojeni"]);
 $kat=mysql_fetch_array($mysql);
 echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">";
 echo "<table cellspacing=\"0\" cellpadding=\"2\" border=\"1\" align=\"center\" width=\"500\" class=\"ramsedy\">\n";
 echo "<tr class=\"txt\"><td>Název</td><td align=\"center\"><input type=\"text\" size=\"50\" name=\"kat_nazev\" class=\"textpole\" value=\"".$kat["kat_nazev"]."\"></td></tr>";
 echo "<tr class=\"txt\"><td>Popis</td><td align=\"center\"><input type=\"text\" size=\"50\" name=\"kat_popis\" class=\"textpole\" value=\"".$kat["kat_popis"]."\"></td></tr>";
 echo "<tr class=\"txt\"><td align=\"center\" colspan=\"2\"><input type=\"submit\" value=\" Upravit \" class=\"tl\"></td></tr>";
 echo "</table>";
 echo "<input type=\"hidden\" name=\"akce\" value=\"kat_uprav\">";
 echo "<input type=\"hidden\" name=\"kat_id\" value=\"".$GLOBALS["kat_id"]."\">";
 echo "<input type=\"hidden\" name=\"modul\" value=\"gallery\">";
 echo "<input type=\"hidden\" name=\"do_it\" value=\"1\">";
 echo "</form>" ;
else:
 $mysql=mysql_query("update ".$GLOBALS["rspredpona"]."gallery_kategorie set kat_nazev='".$GLOBALS["kat_nazev"]."',kat_popis='".$GLOBALS["kat_popis"]."' where idk='".$GLOBALS["kat_id"]."'",$GLOBALS["dbspojeni"]);
 if($mysql): echo "Kategorie upravena";
 else: echo "Nepodařilo se kategorii upravit";
 endif;
endif;}



function NactiKonfigHod($promenna = '', $typ = '')
{
 $promenna=addslashes($promenna);
 switch ($typ):
   case 'varchar': $dotaz="select idk,hodnota from ".$GLOBALS["rspredpona"]."gallery_konfigurace where nazev='".$promenna."'"; break;
 endswitch;
 $dotazhod=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
 if ($dotazhod==0):
   // promenna neexistuje
   $vysledek[0]=0;
   $vysledek[1]='';
 else:
  if (mysql_num_rows($dotazhod)==1):
    // promenna nactena
    $vysledek=mysql_fetch_row($dotazhod);
  else:
    // promenna neexistuje
    $vysledek[0]=0;
    $vysledek[1]='';
  endif;
 endif;
 return $vysledek; // pole: 0 = id promenne, 1 = hodnota promenne
}



function KonfiguracePrehled() {
 include("version.php");
 $pocet_gal_admin=NactiKonfigHod("pocet_gal_admin","varchar");
 $pocet_gal_redaktor=NactiKonfigHod("pocet_gal_redaktor","varchar");
 $pocet_gal_autor=NactiKonfigHod("pocet_gal_autor","varchar");
 $pocet_gal_ctenar=NactiKonfigHod("pocet_gal_ctenar","varchar");
 $galerie_interni=NactiKonfigHod("galerie_interni","varchar");
  if($galerie_interni[1]): $ano_int="checked"; $ne_int=""; else: $ano_int=""; $ne_int="checked"; endif;
 $velikost_galerie=NactiKonfigHod("velikost_galerie","varchar");
 $velikost_obrazek=NactiKonfigHod("velikost_obrazek","varchar");
 $ftp_admin=NactiKonfigHod("ftp_admin","varchar");
 $ftp_autor=NactiKonfigHod("ftp_autor","varchar");
 $ftp_redaktor=NactiKonfigHod("ftp_redaktor","varchar");
 $ftp_ctenar=NactiKonfigHod("ftp_ctenar","varchar");
  if($ftp_admin[1]): $ano_ftp_admin="checked"; $ne_ftp_admin=""; else: $ano_ftp_admin=""; $ne_ftp_admin="checked"; endif;
  if($ftp_autor[1]): $ano_ftp_autor="checked"; $ne_ftp_autor=""; else: $ano_ftp_autor=""; $ne_ftp_autor="checked"; endif;
  if($ftp_ctenar[1]): $ano_ftp_ctenar="checked"; $ne_ftp_ctenar=""; else: $ano_ftp_ctenar=""; $ne_ftp_ctenar="checked"; endif;
  if($ftp_redaktor[1]): $ano_ftp_redaktor="checked"; $ne_ftp_redaktor="";  else: $ano_ftp_redaktor=""; $ne_ftp_redaktor="checked"; endif;
 $top_prehled=NactiKonfigHod("top_prehled","varchar");
 $hromadne_pridani=NactiKonfigHod("hromadne_pridani","varchar");
 $nahled_sirka=NactiKonfigHod("nahled_sirka","varchar");
 $nahled_vyska=NactiKonfigHod("nahled_vyska","varchar");
 $strankovani=NactiKonfigHod("strankovani","varchar");
 $sablony=NactiKonfigHod("sablony","varchar");
 $gallery_dir=NactiKonfigHod("gallery_dir","varchar");
 $pocet_znaku=NactiKonfigHod("pocet_znaku","varchar");
 $pocet_sloupcu=NactiKonfigHod("pocet_sloupcu","varchar");
 $phprs=NactiKonfigHod("phprs_verze","varchar");
      if($phprs[1]=="235"): $phpRS235="selected"; $phpRS250=""; $phpRS255=""; $phpRS265="";
  elseif($phprs[1]=="250"): $phpRS235=""; $phpRS250="selected"; $phpRS255=""; $phpRS265="";
  elseif($phprs[1]=="255"): $phpRS235=""; $phpRS250=""; $phpRS255="selected"; $phpRS265="";
  elseif($phprs[1]=="265"): $phpRS235=""; $phpRS250=""; $phpRS255=""; $phpRS265="selected";
  endif;
 $gd=NactiKonfigHod("gd_verze","varchar");
      if($gd[1]=="gd1"): $gd1="selected"; $gd2=""; $gd_auto="";
  elseif($gd[1]=="gd2"): $gd1=""; $gd2="selected"; $gd_auto="";
  elseif($gd[1]=="auto"): $gd1=""; $gd2=""; $gd_auto="selected";
  endif;

 echo "<form method=\"post\" action=\"".RS_VYKONNYSOUBOR."\">";
 echo "<table cellspacing=\"0\" cellpadding=\"2\" border=\"1\" align=\"center\" width=\"500\" class=\"ramsedy\">";
 echo "<tr class=\"txt\"><th>Nazev</th><th>Hodnota</th></tr>";
 echo "<tr class=\"txt\"><td>Počet galerií pro administrátora</td><td><input value=\"".$pocet_gal_admin[1]."\" type=\"text\" size=\"10\" class=\"textbox\" name=\"pocet_gal_admin\"></td></tr>";
 echo "<tr class=\"txt\"><td>Počet galerií pro redaktora</td><td><input value=\"".$pocet_gal_redaktor[1]."\" type=\"text\" size=\"10\" class=\"textbox\" name=\"pocet_gal_redaktor\"></td></tr>";
 echo "<tr class=\"txt\"><td>Počet galerií pro autora</td><td><input value=\"".$pocet_gal_autor[1]."\" type=\"text\" size=\"10\" class=\"textbox\" name=\"pocet_gal_autor\"></td></tr>";
 echo "<tr class=\"txt\"><td>Počet galerií pro čtenáře</td><td><input value=\"".$pocet_gal_ctenar[1]."\" type=\"text\" size=\"10\" class=\"textbox\" name=\"pocet_gal_ctenar\"></td></tr>";
 echo "<tr class=\"txt\"><td>Zobrazovat interní galerie</td><td><input ".$ano_int." type=\"radio\" name=\"galerie_interni\" value=\"1\">Ano - <input ".$ne_int." type=\"radio\" name=\"galerie_interni\" value=\"0\">Ne</td></tr>";
 echo "<tr class=\"txt\"><td>Maximální velikost nahrávaného obrázku</td><td><input value=\"".$velikost_obrazek[1]."\" name=\"velikost_obrazek\" type=\"text\" class=\"textbox\" size=\"10\"> v kB</td></tr>";
 echo "<tr class=\"txt\"><td>Maximální velikost galerie</td><td><input value=\"".$velikost_galerie[1]."\" type=\"text\" name=\"velikost_galerie\" class=\"textbox\" size=\"10\"> v MB</td></tr>";
 echo "<tr class=\"txt\"><td>Může admin nahrávat přes FTP?</td><td><input ".$ano_ftp_admin." type=\"radio\" name=\"ftp_admin\" value=\"1\">Ano - <input ".$ne_ftp_admin." type=\"radio\" name=\"ftp_admin\" value=\"0\">Ne</td></tr>";
 echo "<tr class=\"txt\"><td>Může redaktor nahrávat přes FTP?</td><td><input ".$ano_ftp_redaktor." type=\"radio\" name=\"ftp_redaktor\" value=\"1\">Ano - <input ".$ne_ftp_redaktor." type=\"radio\" name=\"ftp_redaktor\" value=\"0\">Ne</td></tr>";
 echo "<tr class=\"txt\"><td>Může autor nahrávat přes FTP?</td><td><input ".$ano_ftp_autor." type=\"radio\" name=\"ftp_autor\" value=\"1\">Ano - <input ".$ne_ftp_autor." type=\"radio\" name=\"ftp_autor\" value=\"0\">Ne</td></tr>";
 echo "<tr class=\"txt\"><td>Může čtenář nahrávat přes FTP?</td><td><input ".$ano_ftp_ctenar." type=\"radio\" name=\"ftp_ctenar\" value=\"1\">Ano - <input ".$ne_ftp_ctenar." type=\"radio\" name=\"ftp_ctenar\" value=\"0\">Ne</td></tr>";
 echo "<tr class=\"txt\"><td>Kolik obrázků zobrazovat v TOP přehledu?</td><td><input value=\"".$top_prehled[1]."\" type=\"text\" name=\"top_prehled\" class=\"textbox\" size=\"10\"></td></tr>";
 echo "<tr class=\"txt\"><td>Kolik obrázků lze maximálně hromadně přidat?</td><td><input value=\"".$hromadne_pridani[1]."\" type=\"text\" size=\"10\" class=\"textbox\" name=\"hromadne_pridani\"></td></tr>";
 echo "<tr class=\"txt\"><td>Maximální šířka náhledu</td><td><input value=\"".$nahled_sirka[1]."\" type=\"text\" size=\"10\" class=\"textbox\" name=\"nahled_sirka\"></td></tr>";
 echo "<tr class=\"txt\"><td>Maximální výška náhledu</td><td><input value=\"".$nahled_vyska[1]."\" type=\"text\" size=\"10\" class=\"textbox\" name=\"nahled_vyska\"></td></tr>";
 echo "<tr class=\"txt\"><td>Po kolika položkách stránkovat v galerii</td><td><input type=\"text\" value=\"".$strankovani[1]."\" class=\"textbox\" size=\"10\" name=\"strankovani\"></td></tr>";
 echo "<tr class=\"txt\"><td>V kolika sloupcích zobrazovat položky</td><td><input type=\"text\" value=\"".$pocet_sloupcu[1]."\" class=\"textbox\" size=\"10\" name=\"pocet_sloupcu\"></td></tr>";
 echo "<tr class=\"txt\"><td>Kolik znaků z popisu zobrazovat v přehledu galerií</td><td><input type=\"text\" value=\"".$pocet_znaku[1]."\" class=\"textbox\" size=\"10\" name=\"pocet_znaku\"></td></tr>";
 echo "<tr class=\"txt\"><td>Adresář se šablonami (musí končit lomítkem)</td><td><input type=\"text\" value=\"".$sablony[1]."\" class=\"textbox\" size=\"20\" name=\"sablony\"></td></tr>";
 echo "<tr class=\"txt\"><td>Adresář kam nahrávat obrázky (musí končit lomítkem)</td><td><input type=\"text\" value=\"".$gallery_dir[1]."\" class=\"textbox\" size=\"20\" name=\"gallery_dir\"></td></tr>";
 echo "<tr class=\"txt\"><td>phpRS (mělo by být: ".$phprsversion.")</td><td><select name=\"phprs_verze\"><!--<option ".$phpRS235." value=\"235\">phpRS 235</option>--><option ".$phpRS250." value=\"250\">phpRS 250</option><option ".$phpRS255." value=\"255\">phpRS 255</option><option ".$phpRS265." value=\"265\">phpRS 265</option></td></tr>";
 echo "<tr class=\"txt\"><td>Verze GD knihovny<br /><a href=\"".RS_VYKONNYSOUBOR."?modul=gallery&amp;akce=konf_gd\" target=\"blank\">Zkusit autodetekci</a></td><td><select name=\"gd_verze\"><option ".$gd1." value=\"gd1\">GD verze 1</option><option ".$gd2." value=\"gd2\">GD verze 2</option><option ".$gd_auto." value=\"auto\">Autodetekce</option></select></td></tr>";
 echo "<tr><td align=\"center\" colspan=\"2\"><input type=\"submit\" value=\" Uložit \" class=\"tl\"> <input type=\"reset\" value=\" Reset \" class=\"tl\"></td></tr>";
 echo "</table>";
 echo "<input type=\"hidden\" name=\"modul\" value=\"gallery\"><input type=\"hidden\" name=\"akce\" value=\"konf_uloz\">";
 echo "</form>";
}

function KonfiguraceUloz() {
 $mysql=mysql_query("update ".$GLOBALS["rspredpona"]."gallery_konfigurace set hodnota='".$_POST["pocet_gal_admin"]."' where nazev='pocet_gal_admin'",$GLOBALS["dbspojeni"]);
 $mysql.=mysql_query("update ".$GLOBALS["rspredpona"]."gallery_konfigurace set hodnota='".$_POST["pocet_gal_redaktor"]."' where nazev='pocet_gal_redaktor'",$GLOBALS["dbspojeni"]);
 $mysql.=mysql_query("update ".$GLOBALS["rspredpona"]."gallery_konfigurace set hodnota='".$_POST["pocet_gal_autor"]."' where nazev='pocet_gal_autor'",$GLOBALS["dbspojeni"]);
 $mysql.=mysql_query("update ".$GLOBALS["rspredpona"]."gallery_konfigurace set hodnota='".$_POST["pocet_gal_ctenar"]."' where nazev='pocet_gal_ctenar'",$GLOBALS["dbspojeni"]);
 $mysql.=mysql_query("update ".$GLOBALS["rspredpona"]."gallery_konfigurace set hodnota='".$_POST["nahled_sirka"]."' where nazev='nahled_sirka'",$GLOBALS["dbspojeni"]);
 $mysql.=mysql_query("update ".$GLOBALS["rspredpona"]."gallery_konfigurace set hodnota='".$_POST["nahled_vyska"]."' where nazev='nahled_vyska'",$GLOBALS["dbspojeni"]);
 $mysql.=mysql_query("update ".$GLOBALS["rspredpona"]."gallery_konfigurace set hodnota='".$_POST["galerie_interni"]."' where nazev='galerie_interni'",$GLOBALS["dbspojeni"]);
 $mysql.=mysql_query("update ".$GLOBALS["rspredpona"]."gallery_konfigurace set hodnota='".$_POST["ftp_admin"]."' where nazev='ftp_admin'",$GLOBALS["dbspojeni"]);
 $mysql.=mysql_query("update ".$GLOBALS["rspredpona"]."gallery_konfigurace set hodnota='".$_POST["ftp_redaktor"]."' where nazev='ftp_redaktor'",$GLOBALS["dbspojeni"]);
 $mysql.=mysql_query("update ".$GLOBALS["rspredpona"]."gallery_konfigurace set hodnota='".$_POST["ftp_autor"]."' where nazev='ftp_autor'",$GLOBALS["dbspojeni"]);
 $mysql.=mysql_query("update ".$GLOBALS["rspredpona"]."gallery_konfigurace set hodnota='".$_POST["ftp_ctenar"]."' where nazev='ftp_ctenar'",$GLOBALS["dbspojeni"]);
 $mysql.=mysql_query("update ".$GLOBALS["rspredpona"]."gallery_konfigurace set hodnota='".$_POST["top_prehled"]."' where nazev='top_prehled'",$GLOBALS["dbspojeni"]);
 $mysql.=mysql_query("update ".$GLOBALS["rspredpona"]."gallery_konfigurace set hodnota='".$_POST["hromadne_pridani"]."' where nazev='hromadne_pridani'",$GLOBALS["dbspojeni"]);
 $mysql.=mysql_query("update ".$GLOBALS["rspredpona"]."gallery_konfigurace set hodnota='".$_POST["velikost_galerie"]."' where nazev='velikost_galerie'",$GLOBALS["dbspojeni"]);
 $mysql.=mysql_query("update ".$GLOBALS["rspredpona"]."gallery_konfigurace set hodnota='".$_POST["velikost_obrazek"]."' where nazev='velikost_obrazek'",$GLOBALS["dbspojeni"]);
 $mysql.=mysql_query("update ".$GLOBALS["rspredpona"]."gallery_konfigurace set hodnota='".$_POST["strankovani"]."' where nazev='strankovani'",$GLOBALS["dbspojeni"]);
 $mysql.=mysql_query("update ".$GLOBALS["rspredpona"]."gallery_konfigurace set hodnota='".$_POST["sablony"]."' where nazev='sablony'",$GLOBALS["dbspojeni"]);
 $mysql.=mysql_query("update ".$GLOBALS["rspredpona"]."gallery_konfigurace set hodnota='".$_POST["gallery_dir"]."' where nazev='gallery_dir'",$GLOBALS["dbspojeni"]);
 $mysql.=mysql_query("update ".$GLOBALS["rspredpona"]."gallery_konfigurace set hodnota='".$_POST["phprs_verze"]."' where nazev='phprs_verze'",$GLOBALS["dbspojeni"]);
 $mysql.=mysql_query("update ".$GLOBALS["rspredpona"]."gallery_konfigurace set hodnota='".$_POST["gd_verze"]."' where nazev='gd_verze'",$GLOBALS["dbspojeni"]);
 $mysql.=mysql_query("update ".$GLOBALS["rspredpona"]."gallery_konfigurace set hodnota='".$_POST["pocet_znaku"]."' where nazev='pocet_znaku'",$GLOBALS["dbspojeni"]);
 $mysql.=mysql_query("update ".$GLOBALS["rspredpona"]."gallery_konfigurace set hodnota='".$_POST["pocet_sloupcu"]."' where nazev='pocet_sloupcu'",$GLOBALS["dbspojeni"]);
if($mysql):
  echo "Údaje byly aktualizovány.";
 else:
  echo "Úprava databáze se nepovedla, prosím zkuste to později.";
 endif;
}

function KonfiguraceGD() {
 if(function_exists("gd_info")):
  $verze=gd_info(); $mystring=$verze["GD Version"]; $findme = '(2.'; $pos = strpos($mystring, $findme);
  if (!$pos): echo "Nastavte Vaši GD knihovnu na <b>GD verze 1</b>."; else: echo "Nastavte Vaši GD knihovnu na <b>GD verze 2</b>"; endif;
 else:
  echo "Bohužel, Váš server nepodporuje funkci GD_Info a tak tato možnost není dostupná...";
 endif;



}
?>
