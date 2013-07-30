<?

######################################################################
# phpRS Administration Engine - Config's section 1.5.6
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_config, rs_ankety, rs_plugin

/*
Seznam promennych v tabulce: rs_config
- aktivni_anketa (hodnota: cislo nebo false)
- pocet_clanku (hodnota: cislo)
- posledni_zmena (hodnota: specialni retezec znaku)
- hlidat_platnost (hodnota: cislo)
- pocet_novinek (hodnta: cislo)
- global_sab (hodnota: cislo)
- povolit_str (hodnota: cislo)
*/

if ($Uzivatel->StavSession!=1):
  echo "<html><body><div align=\"center\">Tento soubor neni urcen k vnejsimu spousteni!</div></body></html>";
  exit;
endif;

// ---[rozcestnik]------------------------------------------------------------------
switch($GLOBALS['akce']):
     // konfigurace - zakladni nastaveni
     case "ShowConfig": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2><p align=\"center\">";
          ShowConfig();
          break;
     case "SaveConfig": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2><p align=\"center\">";
          SaveConfig();
          break;
     // konfigurace - plug-iny
     case "ShowPlugin": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2><p align=\"center\">";
          ShowPlugin();
          break;
     case "SavePlugin": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2><p align=\"center\">";
          SavePlugin();
          break;
     case "DelPlugin": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2><p align=\"center\">";
          DelPlugin();
          break;
     // konfigurace - globalni a clankove sablony
     case "SprSab": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2><p align=\"center\">";
          SpravaSab();
          break;
     case "DelGlobSab": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2><p align=\"center\">";
          SmazGlobalSab();
          break;
     case "DelClaSab": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2><p align=\"center\">";
          SmazClaSab();
          break;
     case "AddSab": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2><p align=\"center\">";
          FormPrSab();
          break;
     case "AcAddSab": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2><p align=\"center\">";
          PridatSab();
          break;
     case "NastClaSab": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2><p align=\"center\">";
          NastClaSab();
          break;
     case "AcNastClaSab": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2><p align=\"center\">";
          AcNastClaSab();
          break;
     // konfigurace - moduly
     case "ShowModul": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2><p align=\"center\">";
          ShowModul();
          break;
     case "StavModul": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2><p align=\"center\">";
          StavModul();
          break;
     case "KonfigModul": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2><p align=\"center\">";
          KonfigModul();
          break;
     case "EditModul": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2><p align=\"center\">";
          EditModul();
          break;
     case "AcEditModul": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2><p align=\"center\">";
          AcEditModul();
          break;
endswitch;
echo "</p>\n"; // zakonceni P tagu

// ---[pomocne fce]-----------------------------------------------------------------

// nacteni konfiguracni hodnoty z konfig. tab
function NactiKonfigHod($promenna = '', $typ = '')
{
$promenna=addslashes($promenna);

switch ($typ):
  case 'varchar': $dotaz="select idc,hodnota from ".$GLOBALS["rspredpona"]."config where promenna='".$promenna."'"; break;
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

function KonfOptGlobSab($hledam = '')
{
$vysl='';

$dotazpom=mysql_query("select ids,nazev_sab,typ_sab from ".$GLOBALS["rspredpona"]."global_sab order by nazev_sab",$GLOBALS["dbspojeni"]);
$pocetpom=mysql_num_rows($dotazpom);
$nalezeno=0; // inic. stavu

while ($pole_data = mysql_fetch_assoc($dotazpom)):
  $vysl.="<option value=\"".$pole_data["ids"]."\"";
  if ($pole_data["ids"]==$hledam): $vysl.=" selected"; $nalezeno=1; endif;
  $vysl.=">".$pole_data["nazev_sab"];
  if ($pole_data["typ_sab"]!=''): $vysl.=" (".$pole_data["typ_sab"].")"; endif;
  $vysl.="</option>\n";
endwhile;

// test na akt. nastaveni glob. sablony
if ($nalezeno==0): $vysl.="<option value=\"".$hledam."\" selected>-- není přiřazena žádná globální šablona --</option>\n"; endif;

return $vysl;
}

function KonfOptAnkety($hledam = '')
{
$vysl='';

$dotazpom=mysql_query("select ida,titulek from ".$GLOBALS["rspredpona"]."ankety order by titulek",$GLOBALS["dbspojeni"]);
$pocetpom=mysql_num_rows($dotazpom);

// inic. vypnuti ankety
if ($hledam=='false'):
  $vysl.="<option value=\"false\" selected>-- žádná anketa není aktivní --</option>\n";
else:
  $vysl.="<option value=\"false\">-- žádná anketa není aktivní --</option>\n";
endif;

while ($pole_data = mysql_fetch_assoc($dotazpom)):
  $vysl.="<option value=\"".$pole_data["ida"]."\"";
  if ($pole_data["ida"]==$hledam): $vysl.=" selected"; endif;
  $vysl.=">".$pole_data["titulek"]."</option>\n";
endwhile;

return $vysl;
}

function OptClankoveSab($hledam = 0)
{
$vysl='';

$dotazpom=mysql_query("select ids,nazev_cla_sab from ".$GLOBALS["rspredpona"]."cla_sab order by nazev_cla_sab",$GLOBALS["dbspojeni"]);
$pocetpom=mysql_num_rows($dotazpom);

while ($pole_data = mysql_fetch_assoc($dotazpom)):
  $vysl.='<option value="'.$pole_data["ids"].'"';
  if ($pole_data["ids"]==$hledam): $vysl.=' selected'; endif;
  $vysl.='>'.$pole_data["nazev_cla_sab"]."</option>\n";
endwhile;

return $vysl;
}

// ---[hlavni fce - zakladni konf.]-------------------------------------------------

function ShowConfig()
{
// nadpis konfigurace zakladniho nastaveni phpRS systemu
echo "<p align=\"center\" class=\"txt\"><strong><big>".RS_CFG_KO_NADPIS_KONFIGURACE."</big></strong></p>\n";

// linky na spravu subcasti
echo "<p align=\"center\" class=\"txt\"><b><a href=\"admin.php?akce=SprSab&amp;modul=config\">".RS_CFG_KO_NADPIS_SABLONY."</a></b> -
<b><a href=\"admin.php?akce=ShowPlugin&amp;modul=config\">".RS_CFG_KO_NADPIS_PLUGINY."</a></b> -
<b><a href=\"admin.php?akce=ShowModul&amp;modul=config\">".RS_CFG_KO_NADPIS_MODULY."</a></b></p>\n";

// start konfig. tab
echo "<form action=\"admin.php\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_CFG_KO_PROMENNA."</b></td><td align=\"left\"><b>".RS_CFG_KO_HODNOTA."</b></td></tr>\n";

$akt_cis_prom=0; // inic. pocitadla promennych

// zobrazeni promenne global_sab
$konf_globalsab=NactiKonfigHod('global_sab','varchar');
echo "<tr class=\"txt\"><td align=\"left\" width=\"250\"><b>".RS_CFG_KO_VOLBA_GLOB_SAB."</b></td>
<td align=\"left\">
<select name=\"prhodnota[".$akt_cis_prom."]\" size=\"1\">".KonfOptGlobSab($konf_globalsab[1])."</select>
<input type=\"hidden\" name=\"pridc[".$akt_cis_prom."]\" value=\"".$konf_globalsab[0]."\" />
<input type=\"hidden\" name=\"prtyp[".$akt_cis_prom."]\" value=\"varchar\" />
<input type=\"hidden\" name=\"prprepinac[".$akt_cis_prom."]\" value=\"1\" />
<input type=\"hidden\" name=\"prtestint[".$akt_cis_prom."]\" value=\"0\" />
</td></tr>\n";
$akt_cis_prom++;

// zobrazeni promenne pocet_clanku
$konf_pocetcl=NactiKonfigHod('pocet_clanku','varchar');
echo "<tr class=\"txt\"><td align=\"left\" width=\"250\"><b>".RS_CFG_KO_VOLBA_POCET_CLA."</b></td>
<td align=\"left\">
<input type=\"text\" name=\"prhodnota[".$akt_cis_prom."]\" value=\"".$konf_pocetcl[1]."\" size=\"3\" class=\"textpole\" />
<input type=\"hidden\" name=\"pridc[".$akt_cis_prom."]\" value=\"".$konf_pocetcl[0]."\" />
<input type=\"hidden\" name=\"prtyp[".$akt_cis_prom."]\" value=\"varchar\" />
<input type=\"hidden\" name=\"prprepinac[".$akt_cis_prom."]\" value=\"1\" />
<input type=\"hidden\" name=\"prtestint[".$akt_cis_prom."]\" value=\"1\" />
</td></tr>\n";
$akt_cis_prom++;

// zobrazeni promenne aktivni_anketa
$konf_aktanket=NactiKonfigHod('aktivni_anketa','varchar');
echo "<tr class=\"txt\"><td align=\"left\" width=\"250\"><b>".RS_CFG_KO_VOLBA_ANKETA."</b></td>
<td align=\"left\">
<select name=\"prhodnota[".$akt_cis_prom."]\" size=\"1\">".KonfOptAnkety($konf_aktanket[1])."</select>
<input type=\"hidden\" name=\"pridc[".$akt_cis_prom."]\" value=\"".$konf_aktanket[0]."\" />
<input type=\"hidden\" name=\"prtyp[".$akt_cis_prom."]\" value=\"varchar\" />
<input type=\"hidden\" name=\"prprepinac[".$akt_cis_prom."]\" value=\"1\" />
<input type=\"hidden\" name=\"prtestint[".$akt_cis_prom."]\" value=\"0\" />
</td></tr>\n";
$akt_cis_prom++;

// zobrazeni promenne hlidat_platnost
$konf_platnost=NactiKonfigHod('hlidat_platnost','varchar');
echo "<tr class=\"txt\"><td align=\"left\" width=\"250\"><b>".RS_CFG_KO_VOLBA_PLATNOST_CLA."</b></td>
<td align=\"left\">
<select name=\"prhodnota[".$akt_cis_prom."]\" size=\"1\">";
if ($konf_platnost[1]==1):
  echo "<option value=\"1\" selected>".RS_TL_ANO."</option><option value=\"0\">".RS_TL_NE."</option>\n";
else:
  echo "<option value=\"1\">".RS_TL_ANO."</option><option value=\"0\" selected>".RS_TL_NE."</option>\n";
endif;
echo "</select>
<input type=\"hidden\" name=\"pridc[".$akt_cis_prom."]\" value=\"".$konf_platnost[0]."\" />
<input type=\"hidden\" name=\"prtyp[".$akt_cis_prom."]\" value=\"varchar\" />
<input type=\"hidden\" name=\"prprepinac[".$akt_cis_prom."]\" value=\"1\" />
<input type=\"hidden\" name=\"prtestint[".$akt_cis_prom."]\" value=\"0\" />
</td></tr>\n";
$akt_cis_prom++;

// zobrazeni promenne pocet_novinek
$konf_pocetnov=NactiKonfigHod('pocet_novinek','varchar');
echo "<tr class=\"txt\"><td align=\"left\" width=\"250\"><b>".RS_CFG_KO_VOLBA_NOVINKY."</b></td>
<td align=\"left\">
<select name=\"prprepinac[".$akt_cis_prom."]\" size=\"1\">";
if ($konf_pocetnov[1]==0):
  echo "<option value=\"1\">".RS_TL_ANO."</option><option value=\"0\" selected>".RS_TL_NE."</option></select> ".RS_CFG_KO_KOLIK." <input type=\"text\" name=\"prhodnota[".$akt_cis_prom."]\" value=\"\" size=\"3\" class=\"textpole\" />\n";
else:
  echo "<option value=\"1\" selected>".RS_TL_ANO."</option><option value=\"0\">".RS_TL_NE."</option></select> ".RS_CFG_KO_KOLIK." <input type=\"text\" name=\"prhodnota[".$akt_cis_prom."]\" value=\"".$konf_pocetnov[1]."\" size=\"3\" class=\"textpole\" />\n";
endif;
echo "<input type=\"hidden\" name=\"pridc[".$akt_cis_prom."]\" value=\"".$konf_pocetnov[0]."\" />
<input type=\"hidden\" name=\"prtyp[".$akt_cis_prom."]\" value=\"varchar\" />
<input type=\"hidden\" name=\"prtestint[".$akt_cis_prom."]\" value=\"1\" />
</td></tr>\n";
$akt_cis_prom++;

// zobrazeni promenne povolit_str
$konf_povolitstr=NactiKonfigHod('povolit_str','varchar');
echo "<tr class=\"txt\"><td align=\"left\" width=\"250\"><b>".RS_CFG_KO_VOLBA_STRANKOVANI."</b></td>
<td align=\"left\">
<select name=\"prhodnota[".$akt_cis_prom."]\" size=\"1\">";
if ($konf_povolitstr[1]==1):
  echo "<option value=\"1\" selected>".RS_TL_ANO."</option><option value=\"0\">".RS_TL_NE."</option>\n";
else:
  echo "<option value=\"1\">".RS_TL_ANO."</option><option value=\"0\" selected>".RS_TL_NE."</option>\n";
endif;
echo "</select>
<input type=\"hidden\" name=\"pridc[".$akt_cis_prom."]\" value=\"".$konf_povolitstr[0]."\" />
<input type=\"hidden\" name=\"prtyp[".$akt_cis_prom."]\" value=\"varchar\" />
<input type=\"hidden\" name=\"prprepinac[".$akt_cis_prom."]\" value=\"1\" />
<input type=\"hidden\" name=\"prtestint[".$akt_cis_prom."]\" value=\"0\" />
</td></tr>\n";
$akt_cis_prom++;

// konec konfig. tab
echo "<tr class=\"txt\"><td align=\"center\" colspan=\"2\"><input type=\"submit\" value=\" ".RS_CFG_KO_TL_ULOZ_NASTAV." \" class=\"tl\" /></td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"SaveConfig\" /><input type=\"hidden\" name=\"modul\" value=\"config\" />
</form>
<p></p>\n";
}

function SaveConfig()
{
$chyba=0; // inic. chyby

if (!isset($GLOBALS['prhodnota'])||!isset($GLOBALS['pridc'])||!isset($GLOBALS['prtyp'])||!isset($GLOBALS['prprepinac'])||!isset($GLOBALS['prtestint'])):
  $chyba=1; // chybi jedna z potrebnych promennych
endif;
$pocet_radku=count($GLOBALS["pridc"]);
if ($pocet_radku<1):
  $chyba=1; // chybi promenne
endif;

if ($chyba==0):
  // inic. celk. chyby
  $chyba_vse=0;
  // ulozeni jednotlivych konf. prom.
  for ($pom=0;$pom<$pocet_radku;$pom++):
    // test na integer hodnotu
    if ($GLOBALS["prtestint"][$pom]==1):
      if (!ereg("^[0-9]*$",$GLOBALS["prhodnota"][$pom])): $GLOBALS["prhodnota"][$pom]=0; endif;
    endif;
    // test na stav prepinace
    if ($GLOBALS["prprepinac"][$pom]==0):
      $GLOBALS["prhodnota"][$pom]=0;
    endif;
    // bezpec. kontrola
    $GLOBALS["prhodnota"][$pom]=mysql_escape_string($GLOBALS["prhodnota"][$pom]);
    $GLOBALS["pridc"][$pom]=mysql_escape_string($GLOBALS["pridc"][$pom]);
    // typ konf. prom. + sestaveni dotazu
    switch ($GLOBALS['prtyp'][$pom]):
      case 'varchar': $dotaz="update ".$GLOBALS["rspredpona"]."config set hodnota='".$GLOBALS["prhodnota"][$pom]."' where idc='".$GLOBALS["pridc"][$pom]."'"; break;
      default: $dotaz='';
    endswitch;
    @$error=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
    if (!$error):
      echo "<p align=\"center\" class=\"txt\">Error C1: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
    endif;
  endfor;
  // test na celk. vysledek
  if ($chyba_vse==0):
    echo "<p align=\"center\" class=\"txt\">".RS_CFG_KO_OK_EDIT_CFG."</p>\n"; // vse OK
  endif;
endif;

// navrat
ShowConfig();
}

// ---[hlavni fce - plug-iny]-------------------------------------------------------

function ShowPlugin()
{
// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowConfig&amp;modul=config\">".RS_CFG_KO_ZPET."</a></p>\n";

// nadpis sprava plug-inu
echo "<p align=\"center\" class=\"txt\"><strong><big>".RS_CFG_KO_NADPIS_PLUGINY."</big></strong></p>\n";

// dotaz plug-iny
$dotazplug=mysql_query("select idp,nazev,pristup,menu,sys_blok from ".$GLOBALS["rspredpona"]."plugin order by nazev",$GLOBALS["dbspojeni"]);
$pocetplug=mysql_num_rows($dotazplug);
// vypis polozek
echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">
<tr class=\"txt\" bgcolor=\"#E6E6E6\">
<td align=\"center\"><b>".RS_CFG_SP_NAZEV."</b></td>
<td align=\"center\"><b>".RS_CFG_SP_PRAVA."</b></td>
<td align=\"center\"><b>".RS_CFG_SP_MENU."</b></td>
<td align=\"center\"><b>".RS_CFG_SP_SYS_BLOK."</b></td>
<td align=\"center\"><b>".RS_CFG_SP_AKCE."</b></td>
</tr>\n";
if ($pocetplug==0):
  // zadne plug-iny
  echo "<tr class=\"txt\"><td align=\"center\" colspan=\"5\">".RS_CFG_SP_ZADNY_PLUGIN."</td></tr>\n";
else:
  for ($pom=0;$pom<$pocetplug;$pom++):
    $akt_pole_plug=mysql_fetch_assoc($dotazplug);
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    echo "<td>".$akt_pole_plug["nazev"]."</td>";
    // pristupova prava: 1 = dle nastaveni v administraci; 2 = uplne vsichni; 3 = pouze admin
    switch ($akt_pole_plug["pristup"]):
      case 1: echo "<td align=\"center\">".RS_CFG_SP_PRAVA_NASTAVENI."</td>"; break;
      case 2: echo "<td align=\"center\">".RS_CFG_SP_PRAVA_VSICHNI."</td>"; break;
      case 3: echo "<td align=\"center\">".RS_CFG_SP_PRAVA_ADMIN."</td>"; break;
    endswitch;
    if ($akt_pole_plug["menu"]): echo "<td align=\"center\">".RS_TL_ANO."</td>"; else: echo "<td align=\"center\">".RS_TL_NE."</td>"; endif;
    if ($akt_pole_plug["sys_blok"]): echo "<td align=\"center\">".RS_TL_ANO."</td>"; else: echo "<td align=\"center\">".RS_TL_NE."</td>"; endif;
    echo "<td align=\"center\"><a href=\"admin.php?akce=DelPlugin&amp;modul=config&amp;pridp=".$akt_pole_plug["idp"]."\">".RS_CFG_SP_SMAZ."</a></td></tr>\n";
  endfor;
endif;
echo "</table>\n";

// pridani noveho plug-inu
echo "<form action=\"admin.php\" method=\"post\">
<input type=\"hidden\" name=\"akce\" value=\"SavePlugin\" /><input type=\"hidden\" name=\"modul\" value=\"config\" />
<p class=\"txt\" align=\"center\"><b>".RS_CFG_SP_CESTA." <sup>*)</sup></b> <input type=\"text\" name=\"prcesta\" size=\"30\" class=\"textpole\" />
<input type=\"submit\" value=\" ".RS_TL_PRIDAT." \" class=\"tl\" /></p>
</form>\n";
// napoveda ke zpusobu zadani cesty
echo "<p class=\"txt\" align=\"center\"><sup>*)</sup> ".RS_CFG_SP_CESTA_INFO."</p>\n";
}

function SavePlugin()
{
$chyba=0; // inic. chyby
$chyba_integr=0; // inic. chyba integrity

if ($GLOBALS["prcesta"]!=""): // odstraneni prazdneho pole
  if (file_exists($GLOBALS["prcesta"])): // kontrola existence konf. souboru
    include($GLOBALS["prcesta"]); // soubor existuje
    // kontrola existence zakl. promennych
    if (!isset($plugin_nazev)): $chyba=1; endif;
    if (!isset($pi_pristup)): $chyba=1; endif;
    if (!isset($pi_pristup)): $chyba=1; endif;
    if (!isset($pi_sys_blok)): $chyba=1; endif;
    if (!isset($pi_indent_modulu)): $chyba=1; endif;
  else: // soubor neexistuje
    $chyba=1;
  endif;
else:
  $chyba=1;
endif;

if ($chyba==0):
  // bezpecnostni korekce
  $plugin_nazev=mysql_escape_string($plugin_nazev);
  $pi_pristup=mysql_escape_string($pi_pristup);
  $pi_menu=mysql_escape_string($pi_menu);
  $pi_nazev_menu=mysql_escape_string($pi_nazev_menu);
  $pi_indent_modulu=mysql_escape_string($pi_indent_modulu);
  $pi_inclakce_menu=mysql_escape_string($pi_inclakce_menu);
  $pi_link_menu=mysql_escape_string($pi_link_menu);
  $pi_sys_blok=mysql_escape_string($pi_sys_blok);
  $pi_nazev_blok=mysql_escape_string($pi_nazev_blok);
  $pi_zkratka_blok=mysql_escape_string($pi_zkratka_blok);
  $pi_inclsb_blok=mysql_escape_string($pi_inclsb_blok);
  $pi_funkce_blok=mysql_escape_string($pi_funkce_blok);
  // test integrity - indet. modulu
  $dotaztestmodul=mysql_query("select idm from ".$GLOBALS["rspredpona"]."moduly_prava where ident_modulu='".$pi_indent_modulu."'",$GLOBALS["dbspojeni"]);
  if (mysql_num_rows($dotaztestmodul)>0):
    $chyba_integr=1;
  endif;
  // test integrity - zkratka bloku, testuje se pouze v pripade aktivniho systemoveho bloku
  if ($pi_sys_blok==1):
    $dotaztestplugin=mysql_query("select idp from ".$GLOBALS["rspredpona"]."plugin where zkratka_blok='".$pi_zkratka_blok."'",$GLOBALS["dbspojeni"]);
    if (mysql_num_rows($dotaztestplugin)>0):
      $chyba_integr=1;
    endif;
  endif;
  // test na chybu integrity
  if ($chyba_integr==0):
    // ulozeni plug-inu
    @$error=mysql_query("insert into ".$GLOBALS["rspredpona"]."plugin values (null,'".$plugin_nazev."','".$pi_pristup."','".$pi_menu."','".$pi_nazev_menu."','".$pi_inclakce_menu."','".$pi_link_menu."','".$pi_sys_blok."','".$pi_nazev_blok."','".$pi_zkratka_blok."','".$pi_inclsb_blok."','".$pi_funkce_blok."')",$GLOBALS["dbspojeni"]);
    if (!$error):
      // chyba pri vlozeni do registracni tabulky
      echo "<p align=\"center\" class=\"txt\">Error C3: ".RS_DB_ERR_SQL_DOTAZ." ".RS_CFG_SP_ERR_REGISTR_TAB."</p>\n";
    else:
      $idpluginu=mysql_insert_id();
      echo "<p align=\"center\" class=\"txt\">".RS_CFG_SP_OK_ADD_PLUGIN."</p>\n"; // vse OK
      // pridani plug-inu do tabulky s pristupovymi pravy
      if ($pi_menu=='1'):
        $akt_barva_txt='';
        $akt_barva_bg='';
        // pristupova prava: 1 = dle nastaveni v administraci; 2 = uplne vsichni; 3 = pouze admin
        switch ($pi_pristup):
          case 1: $akt_all_prava_users=0; $akt_jen_admin_modul=0; break;
          case 2: $akt_all_prava_users=1; $akt_jen_admin_modul=0; break;
          case 3: $akt_all_prava_users=0; $akt_jen_admin_modul=1; break;
          default: $akt_all_prava_users=0; $akt_jen_admin_modul=0; break;
        endswitch;
        // sestaveni dotazu
        $dotaz="insert into ".$GLOBALS["rspredpona"]."moduly_prava values ";
        $dotaz.="(null,'Modul ".$plugin_nazev."','".$pi_indent_modulu."','','".$akt_all_prava_users."','".$pi_nazev_menu."','".$pi_inclakce_menu."','".$pi_link_menu."',";
        $dotaz.="'30','0','".$akt_jen_admin_modul."','0','".$akt_barva_txt."','".$akt_barva_bg."','1','".$idpluginu."')";
        @$error=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
        if (!$error):
          // chyba pri vlozeni do tabulky s pristupovymi pravy
          echo "<p align=\"center\" class=\"txt\">Error C4: ".RS_DB_ERR_SQL_DOTAZ." ".RS_CFG_SP_ERR_TAB_PRISTUP_PRAV."</p>\n";
        endif;
      endif;
    endif;
  else:
    // system jiz obsahuje shodny plug-iny (plug-in se shodnou identifikaci)
    echo "<p align=\"center\" class=\"txt\">".RS_CFG_SP_WAR_CHYBA_INTEGRITY."</p>\n";
  endif;
else:
  // chyba pri importu
  echo "<p align=\"center\" class=\"txt\">Error C5: ".RS_CFG_SP_ERR_IMPORT."</p>\n";
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowPlugin&amp;modul=config\">".RS_CFG_SP_ZPET_PLUGINY."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowConfig&amp;modul=config\">".RS_CFG_KO_ZPET."</a></p>\n";
}

function DelPlugin()
{
$GLOBALS["pridp"]=addslashes($GLOBALS["pridp"]);

// odstraneni plug-inu z registracni tabulky
@$error=mysql_query("delete from ".$GLOBALS["rspredpona"]."plugin where idp='".$GLOBALS["pridp"]."'",$GLOBALS["dbspojeni"]);
if (!$error):
  echo "<p align=\"center\" class=\"txt\">Error C6: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_CFG_SP_OK_DEL_PLUGIN."</p>\n"; // vse OK
  // odstraneni zaznamu z tabulky s pristovymi pravy
  @$error=mysql_query("delete from ".$GLOBALS["rspredpona"]."moduly_prava where fk_id_plugin='".$GLOBALS["pridp"]."' and plugin='1'",$GLOBALS["dbspojeni"]);
  if (!$error):
    echo "<p align=\"center\" class=\"txt\">Error C7: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
  endif;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowPlugin&amp;modul=config\">".RS_CFG_SP_ZPET_PLUGINY."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowConfig&amp;modul=config\">".RS_CFG_KO_ZPET."</a></p>\n";
}

// ---[hlavni fce - sablony]--------------------------------------------------------

function SpravaSab()
{
// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowConfig&amp;modul=config\">".RS_CFG_KO_ZPET."</a></p>\n";

// nadpis global. sab.
echo "<p align=\"center\" class=\"txt\"><strong><big>".RS_CFG_SS_NADPIS_GLOBAL_SAB."</big></strong></p>\n";

$dotazsab=mysql_query("select ids,nazev_sab,typ_sab,adr_sab from ".$GLOBALS["rspredpona"]."global_sab order by nazev_sab",$GLOBALS["dbspojeni"]);
$pocetsab=mysql_num_rows($dotazsab);
// vypis
echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">
<tr class=\"txt\" bgcolor=\"#E6E6E6\">
<td align=\"center\"><b>".RS_CFG_SS_NAZEV_SAB."</b></td>
<td align=\"center\"><b>".RS_CFG_SS_TYP_SAB."</b></td>
<td align=\"center\"><b>".RS_CFG_SS_UMISTENI_SAB."</b></td>
<td align=\"center\"><b>".RS_CFG_SS_AKCE."</b></td>
</tr>\n";
if ($pocetsab==0):
  // zadna globalni sablona
  echo "<tr class=\"txt\"><td align=\"center\" colspan=\"4\">".RS_CFG_SS_ZADNA_GLOB_SAB."</td></tr>\n";
else:
  for ($pom=0;$pom<$pocetsab;$pom++):
    $akt_pole_data=mysql_fetch_assoc($dotazsab);
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    echo "<td>".$akt_pole_data["nazev_sab"]."</td>";
    echo "<td>".TestNaNic($akt_pole_data["typ_sab"])."</td>";
    echo "<td>".$akt_pole_data["adr_sab"]."</td>";
    echo "<td align=\"center\"><a href=\"admin.php?akce=DelGlobSab&amp;modul=config&amp;prids=".$akt_pole_data["ids"]."\">".RS_CFG_SS_SMAZ."</a></td></tr>\n";
  endfor;
endif;
echo "</table>\n";

// nadpis cla. sab.
echo "<p align=\"center\" class=\"txt\"><strong><big>".RS_CFG_SS_NADPIS_CAL_SAB."</big></strong></p>\n";

$dotazsab=mysql_query("select ids,nazev_cla_sab,soubor_cla_sab from ".$GLOBALS["rspredpona"]."cla_sab order by nazev_cla_sab",$GLOBALS["dbspojeni"]);
$pocetsab=mysql_num_rows($dotazsab);
// vypis
echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">
<tr class=\"txt\" bgcolor=\"#E6E6E6\">
<td align=\"center\"><b>".RS_CFG_SS_NAZEV_SAB."</b></td>
<td align=\"center\"><b>".RS_CFG_SS_CESTA_SAB."</b></td>
<td align=\"center\"><b>".RS_CFG_SS_PRACE_SE_SAB."</b></td>
<td align=\"center\"><b>".RS_CFG_SS_AKCE."</b></td>
</tr>\n";
if ($pocetsab==0):
  // zadan clankova sablona
  echo "<tr class=\"txt\"><td align=\"center\" colspan=\"4\">".RS_CFG_SS_ZADNA_CLA_SAB."</td></tr>\n";
else:
  for ($pom=0;$pom<$pocetsab;$pom++):
    $akt_pole_data=mysql_fetch_assoc($dotazsab);
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    echo "<td>".$akt_pole_data["nazev_cla_sab"]."</td>";
    echo "<td>".$akt_pole_data["soubor_cla_sab"]."</td>";
    echo "<td align=\"center\"><a href=\"admin.php?akce=NastClaSab&amp;modul=config&amp;prids=".$akt_pole_data["ids"]."\">".RS_CFG_SS_PRIRADIT_SAB."</a></td>";
    echo "<td align=\"center\"><a href=\"admin.php?akce=DelClaSab&amp;modul=config&amp;prids=".$akt_pole_data["ids"]."\">".RS_CFG_SS_SMAZ."</a></td></tr>\n";
  endfor;
endif;
echo "</table>\n";

// nadpis vyhledavani novych sablon
echo "<p align=\"center\" class=\"txt\"><strong><big>".RS_CFG_SS_NADPIS_NOVE_SAB."</big></strong></p>\n";
// napoveda k zadani cesty
echo "<p class=\"txt\" align=\"center\">".RS_CFG_SS_CESTA_SAB_ADR_INFO."</p>\n";
// vyhledani nove sablony
echo "<form action=\"admin.php\" method=\"post\">
<input type=\"hidden\" name=\"akce\" value=\"AddSab\" /><input type=\"hidden\" name=\"modul\" value=\"config\" />
<p class=\"txt\" align=\"center\"><b>".RS_CFG_SS_CESTA_SAB_ADR."</b> <input type=\"text\" name=\"prcesta\" size=\"30\" class=\"textpole\" value=\"image\" />
<input type=\"submit\" value=\" ".RS_CFG_SS_TL_HLEDAT." \" class=\"tl\" /></p>
</form>\n";

echo "<p>&nbsp;</p>";
}

function SmazGlobalSab()
{
// bezpecnostni korekce
$GLOBALS["prids"]=mysql_escape_string($GLOBALS["prids"]);

// odstraneni globalni sablony
@$error=mysql_query("delete from ".$GLOBALS["rspredpona"]."global_sab where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
if (!$error):
  echo "<p align=\"center\" class=\"txt\">Error C8: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_OK_DEL_GLOB_SAB."</p>\n"; // vse OK
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=SprSab&amp;modul=config\">".RS_CFG_SS_ZPET_SABLONY."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowConfig&amp;modul=config\">".RS_CFG_KO_ZPET."</a></p>\n";
}

function SmazClaSab()
{
$chyba=0; // inic. chyby

// bezpecnostni korekce
$GLOBALS["prids"]=mysql_escape_string($GLOBALS["prids"]);

// test na aktivni stav cla. sablony (= je prirazena nejakemu clanku)
$dotazmn=mysql_query("select count(idc) as pocet from ".$GLOBALS["rspredpona"]."clanky where sablona='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
if (mysql_result($dotazmn,0,"pocet")>0):
  echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_ERR_AKTIVNI_CLA_SAB."</p>\n";
  $chyba=1;
endif;

// odstraneni globalni sablony
if ($chyba==0):
  @$error=mysql_query("delete from ".$GLOBALS["rspredpona"]."cla_sab where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
  if (!$error):
    echo "<p align=\"center\" class=\"txt\">Error C9: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_OK_DEL_CLA_SAB."</p>\n"; // vse OK
  endif;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=SprSab&amp;modul=config\">".RS_CFG_SS_ZPET_SABLONY."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowConfig&amp;modul=config\">".RS_CFG_KO_ZPET."</a></p>\n";
}

function FormPrSab()
{
$chyba=0; // inic. chyby

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowConfig&amp;modul=config\">".RS_CFG_KO_ZPET."</a> -
<a href=\"admin.php?akce=SprSab&amp;modul=config\">".RS_CFG_SS_ZPET_SABLONY."</a></p>\n";

// test na vstup
if (!is_dir($GLOBALS["prcesta"])):
  // system nemuze nalezt vami zadany adresar
  echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_ERR_NEEXISTUJE_ADR."</p>\n";
  $chyba=1;
endif;

if ($chyba==0):
  // inic. pole adresaru
  $pole_adr_pozice=0;
  $pole_adr_pocet=1;
  $pole_adr[0]=$GLOBALS["prcesta"];
  // inic. pole vysledku
  $pole_vysl_pocet=0;
  $pole_vysl=array();

  while ($pole_adr_pozice<$pole_adr_pocet):
    $adresar=dir($pole_adr[$pole_adr_pozice]);
    //echo "Cesta: ".$adresar->path."<br>\n"; // ** ladici radek **
    while($najdi=$adresar->read()):
      //echo $najdi."<br>\n"; // ** ladici radek **
      if ($najdi!="."&&$najdi!=".."):
        // soubory a adresare
        $cela_cesta=$pole_adr[$pole_adr_pozice]."/".$najdi;
        if (is_dir($cela_cesta)):
          // polozka je adresar
          $pole_adr[$pole_adr_pocet]=$cela_cesta;
          $pole_adr_pocet++;
        else:
          // polozka je soubor
          if ($najdi=="install.php"):
            $pole_vysl[$pole_vysl_pocet]=$cela_cesta;
            $pole_vysl_pocet++;
          endif;
        endif;
        // konec - soubory a adresare
      endif;
    endwhile;
    $adresar->close();

    $pole_adr_pozice++;
  endwhile;
endif;

// nadpis nalezene sab.
echo "<p align=\"center\" class=\"txt\"><strong><big>".RS_CFG_SS_NADPIS_NALEZENE_SAB."</big></strong></p>\n";

if ($pole_vysl_pocet>0):
  echo "<form action=\"admin.php\" method=\"post\">\n";
  echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">\n";
  echo "<tr class=\"txt\" bgcolor=\"#E6E6E6\"><td align=\"center\"><b>".RS_CFG_SS_CESTA_INSTAL_SB."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_CFG_SS_AKCE."</b></td></tr>\n";
  for ($pom=0;$pom<$pole_vysl_pocet;$pom++):
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">\n";
    echo "<td>".$pole_vysl[$pom]."<input type=\"hidden\" name=\"prsablona[".$pom."]\" value=\"".$pole_vysl[$pom]."\" /></td>\n";
    echo "<td align=\"center\"><input type=\"checkbox\" name=\"prstav[]\" value=\"".$pom."\" /> ".RS_CFG_SS_INSTALOVAT."</a></td></tr>\n";
  endfor;
  echo "<tr><td colspan=\"2\" align=\"right\"><input type=\"submit\" value=\" ".RS_CFG_SS_TL_NAINSTALUJ." \" class=\"tl\" /></td></tr>\n";
  echo "</table>\n";
  echo "<input type=\"hidden\" name=\"akce\" value=\"AcAddSab\" /><input type=\"hidden\" name=\"modul\" value=\"config\" />\n";
  echo "</form>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_ZADNA_SABLONA."</p>\n";
endif;
}

function PridatSab()
{
$chyba_inst=0; // inic. chyby

if (isset($GLOBALS["prstav"])):
  $pocet_sab=count($GLOBALS["prstav"]);
else:
  $pocet_sab=0;
endif;

if ($pocet_sab>0):
  for($pom=0;$pom<$pocet_sab;$pom++):
    $id_sablony=$GLOBALS["prstav"][$pom];
    if (isset($GLOBALS["prsablona"][$id_sablony])):
      if (file_exists($GLOBALS["prsablona"][$id_sablony])):
        // vlozeni instalacniho souboru
        include($GLOBALS["prsablona"][$id_sablony]);
        // instalace globalnich sablon
        if (isset($rs_gsab_nazev)):
          // --- instalace globalnich sablon ---
          $pocetsab=count($rs_gsab_nazev);
          $chyba_vse=0;

          for ($p1=0;$p1<$pocetsab;$p1++):
            $chyba_test=0;
            if ($rs_gsab_nazev[$p1]==''): $chyba_test=1; endif;
            if ($rs_gsab_ident[$p1]==''): $chyba_test=1; endif;
            if ($rs_gsab_soubor[$p1]==''): $chyba_test=1; endif;
            if ($rs_gsab_adresar[$p1]==''): $chyba_test=1; endif;
            // instalace sablony
            if ($chyba_test==0):
              // bezpecnostni korekce
              $rs_gsab_nazev[$p1]=mysql_escape_string($rs_gsab_nazev[$p1]);
              $rs_gsab_typ[$p1]=mysql_escape_string($rs_gsab_typ[$p1]);
              $rs_gsab_ident[$p1]=mysql_escape_string($rs_gsab_ident[$p1]);
              $rs_gsab_soubor[$p1]=mysql_escape_string($rs_gsab_soubor[$p1]);
              $rs_gsab_adresar[$p1]=mysql_escape_string($rs_gsab_adresar[$p1]);
              // test na duplicitu
              $dotaz="select count(ids) as pocet from ".$GLOBALS["rspredpona"]."global_sab ";
              $dotaz.="where nazev_sab='".$rs_gsab_nazev[$p1]."' and typ_sab='".$rs_gsab_typ[$p1]."' and ident_sab='".$rs_gsab_ident[$p1]."' and soubor_sab='".$rs_gsab_soubor[$p1]."' and adr_sab='".$rs_gsab_adresar[$p1]."'";
              $dotazshoda=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
              if (mysql_result($dotazshoda,0,"pocet")==0):
                // stejna sablona neexistuje
                $dotaz="insert into ".$GLOBALS["rspredpona"]."global_sab ";
                $dotaz.="values(null,'".$rs_gsab_nazev[$p1]."','".$rs_gsab_typ[$p1]."','".$rs_gsab_ident[$p1]."','".$rs_gsab_soubor[$p1]."','".$rs_gsab_adresar[$p1]."')";
                @$error=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
                if (!$error):
                  echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_ERR_GLOB_SAB_NEOCEK_CHYBA_1." ".$rs_gsab_nazev[$p1]." ".RS_CFG_SS_ERR_GLOB_SAB_NEOCEK_CHYBA_2."</p>\n";
                  $chyba_vse=1;
                endif;
              else:
                // existuje stejna sablona
                echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_ERR_GLOB_SAB_SHODA_SAB_1." ".$rs_gsab_nazev[$p1]." ".RS_CFG_SS_ERR_GLOB_SAB_SHODA_SAB_2."</p>\n";
                $chyba_vse=1;
              endif;
            else:
              // chybi nektery z potrebnych parametru
              echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_ERR_GLOB_SAB_CHYBI_ATR."</p>\n";
              $chyba_vse=1;
            endif;
          endfor;

          if ($chyba_vse==1):
            $chyba_inst=1;
          endif;
          // --- konec: instalace globalnich sablon ---
        endif;
        // instalace clankovych sablon
        if (isset($rs_csab_nazev)):
          // --- instalace clankovych sablon ---
          $pocetsab=count($rs_csab_nazev);
          $chyba_vse=0;

          for ($p1=0;$p1<$pocetsab;$p1++):
            $chyba_test=0;
            if ($rs_csab_nazev[$p1]==''): $chyba_test=1; endif;
            if ($rs_csab_soubor[$p1]==''): $chyba_test=1; endif;
            // instalace sablony
            if ($chyba_test==0):
              // bezpecnostni korekce
              $rs_csab_nazev[$p1]=mysql_escape_string($rs_csab_nazev[$p1]);
              $rs_csab_soubor[$p1]=mysql_escape_string($rs_csab_soubor[$p1]);
              // test na duplicitu
              $dotaz="select count(ids) as pocet from ".$GLOBALS["rspredpona"]."cla_sab ";
              $dotaz.="where nazev_cla_sab='".$rs_csab_nazev[$p1]."' and soubor_cla_sab='".$rs_csab_soubor[$p1]."'";
              $dotazshoda=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
              if (mysql_result($dotazshoda,0,"pocet")==0):
                // stejna sablona neexistuje
                $dotaz="insert into ".$GLOBALS["rspredpona"]."cla_sab ";
                $dotaz.="values(null,'".$rs_csab_nazev[$p1]."','".$rs_csab_soubor[$p1]."')";
                @$error=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
                if (!$error):
                  echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_ERR_CLA_SAB_NEOCEK_CHYBA_1." ".$rs_csab_nazev[$p1]." ".RS_CFG_SS_ERR_CLA_SAB_NEOCEK_CHYBA_2."</p>\n";
                  $chyba_vse=1;
                endif;
              else:
                // existuje stejna sablona
                echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_ERR_CLA_SAB_SHODA_SAB_1." ".$rs_csab_nazev[$p1]." ".RS_CFG_SS_ERR_CLA_SAB_SHODA_SAB_2."</p>\n";
                $chyba_vse=1;
              endif;
            else:
              // chybi nektery z potrebnych parametru
              echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_ERR_CLA_SAB_CHYBI_ATR."</p>\n";
              $chyba_vse=1;
            endif;
          endfor;

          if ($chyba_vse==1):
            $chyba_inst=1;
          endif;
          // --- konec: instalace clankovych sablon ---
        endif;
      else:
        // system nemuze nalezt instalacni soubor
        echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_ERR_CHYBI_INSTAL_SB."</p>\n";
      endif;
    endif;
  endfor;
endif;

if ($chyba_inst==0):
  echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_OK_ADD_SAB."</p>\n"; // vse OK
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=SprSab&amp;modul=config\">".RS_CFG_SS_ZPET_SABLONY."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowConfig&amp;modul=config\">".RS_CFG_KO_ZPET."</a></p>\n";
}

function NastClaSab()
{
$GLOBALS["prids"]=mysql_escape_string($GLOBALS["prids"]);

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowConfig&amp;modul=config\">".RS_CFG_KO_ZPET."</a> -
<a href=\"admin.php?akce=SprSab&amp;modul=config\">".RS_CFG_SS_ZPET_SABLONY."</a></p>\n";

$dotazsab=mysql_query("select ids,nazev_cla_sab,soubor_cla_sab from ".$GLOBALS["rspredpona"]."cla_sab where ids=".$GLOBALS["prids"],$GLOBALS["dbspojeni"]);
$pocetsab=mysql_num_rows($dotazsab);
if ($pocetsab==1):
  echo "<p align=\"center\" class=\"txt\"><strong><big>".RS_CFG_SS_NADPIS_NAZEV_CLA_SAB.":<br />\"".mysql_result($dotazsab,0,"nazev_cla_sab")."\"</big></strong></p>\n";
endif;

// formular pro nastaveni
echo "<form action=\"admin.php\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\">
<tr class=\"txt\"><td colspan=\"2\" align=\"center\"><b>".RS_CFG_SS_VYBRANA_CLA_SAB."</b></td></tr>
<tr class=\"txt\"><td colspan=\"2\" align=\"left\"><input type=\"radio\" name=\"przpusob\" value=\"vse\" /> <b>".RS_CFG_SS_PRIRADIT_VSEM."</b></td></tr>
<tr class=\"txt\"><td colspan=\"2\" align=\"left\"><input type=\"radio\" name=\"przpusob\" value=\"vol\" checked /> <b>".RS_CFG_SS_PRIRADIT_PODMINCE."</b><br />".RS_CFG_SS_VZTAH_INFO."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CFG_SS_PODMINKA_TEMA."</b></td>
<td align=\"left\">";
$poletopic=GenerujSeznamSCestou();
if (!is_array($poletopic)):
  echo RS_CFG_SS_ZADNA_RUBRIKA; // chyba; neexistuje rubrika
else:
  echo "<select name=\"prtema[]\" size=\"10\" multiple>";
  $pocettopic=count($poletopic);
  for ($pom=0;$pom<$pocettopic;$pom++):
    echo "<option value=\"".$poletopic[$pom][0]."\">".$poletopic[$pom][1]."</option>\n";
  endfor;
  echo "</select>";
endif;
echo "</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CFG_SS_PODMINKA_AUTOR."</b></td>
<td align=\"left\"><select name=\"prautor[]\" size=\"10\" multiple>".OptAutori()."</select></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CFG_SS_PODMINA_CLA_SAB."</b></td>
<td align=\"left\"><select name=\"prclasab[]\" size=\"10\" multiple>".OptClankoveSab()."</select></td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcNastClaSab\" /><input type=\"hidden\" name=\"modul\" value=\"config\" />
<input type=\"hidden\" name=\"prids\" value=\"".$GLOBALS["prids"]."\" />
<p align=\"center\"><input type=\"submit\" value=\" ".RS_CFG_SS_TL_NASTAVIT." \" class=\"tl\" /> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\" /></p>
</form>\n";
}

function AcNastClaSab()
{
$chyba=0;
$chyba_nast=0;

$GLOBALS["prids"]=mysql_escape_string($GLOBALS["prids"]);
$GLOBALS["przpusob"]=mysql_escape_string($GLOBALS["przpusob"]);

if (!isset($GLOBALS["przpusob"])): $chyba=1; endif;
if (!isset($GLOBALS["prids"])): $chyba=1; endif;

if ($chyba==0):
  switch($GLOBALS["przpusob"]):
    case "vse": // nastavit vsem cl.
      $dotaz="update ".$GLOBALS["rspredpona"]."clanky set sablona='".$GLOBALS["prids"]."'";
      @$error=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
      if (!$error):
        echo "<p align=\"center\" class=\"txt\">Error C10: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
        $chyba_nast=1;
      endif;
      break;
    case "vol": // nastavit jen vybranym cl.
      // temata
      if (isset($GLOBALS["prtema"])):
        $pocet_pol=count($GLOBALS["prtema"]);
        $prwhere="";
        for($pom=0;$pom<$pocet_pol;$pom++):
          if ($pom>0): $prwhere.=","; endif;
          $prwhere.=mysql_escape_string($GLOBALS["prtema"][$pom]);
        endfor;
        $dotaz="update ".$GLOBALS["rspredpona"]."clanky set sablona='".$GLOBALS["prids"]."' where tema in (".$prwhere.")";
        @$error=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
        if (!$error):
          echo "<p align=\"center\" class=\"txt\">Error C11: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
          $chyba_nast=1;
        endif;
      endif;
      // autori
      if (isset($GLOBALS["prautor"])):
        $pocet_pol=count($GLOBALS["prautor"]);
        $prwhere="";
        for($pom=0;$pom<$pocet_pol;$pom++):
          if ($pom>0): $prwhere.=","; endif;
          $prwhere.=mysql_escape_string($GLOBALS["prautor"][$pom]);
        endfor;
        $dotaz="update ".$GLOBALS["rspredpona"]."clanky set sablona='".$GLOBALS["prids"]."' where autor in (".$prwhere.")";
        @$error=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
        if (!$error):
          echo "<p align=\"center\" class=\"txt\">Error C12: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
          $chyba_nast=1;
        endif;
      endif;
      // clankove sablony
      if (isset($GLOBALS["prclasab"])):
        $pocet_pol=count($GLOBALS["prclasab"]);
        $prwhere="";
        for($pom=0;$pom<$pocet_pol;$pom++):
          if ($pom>0): $prwhere.=","; endif;
          $prwhere.=mysql_escape_string($GLOBALS["prclasab"][$pom]);
        endfor;
        $dotaz="update ".$GLOBALS["rspredpona"]."clanky set sablona='".$GLOBALS["prids"]."' where sablona in (".$prwhere.")";
        @$error=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
        if (!$error):
          echo "<p align=\"center\" class=\"txt\">Error C13: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
          $chyba_nast=1;
        endif;
      endif;
      break;
  endswitch;
endif;

// globalni vysledek
if ($chyba_nast==0):
  echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_OK_NASTAV_CLA_SAB."</p>\n"; // vse OK
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=SprSab&amp;modul=config\">".RS_CFG_SS_ZPET_SABLONY."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowConfig&amp;modul=config\">".RS_CFG_KO_ZPET."</a></p>\n";
}

// ---[hlavni fce - nastaveni modulu]-----------------------------------------------

function ShowModul()
{
// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowConfig&amp;modul=config\">".RS_CFG_KO_ZPET."</a></p>\n";

// nadpis sprava modulu
echo "<p align=\"center\" class=\"txt\"><strong><big>".RS_CFG_KO_NADPIS_MODULY."</big></strong></p>\n";

// sestaveni dotazu
$dotaz="select idm,nazev_modulu,all_prava_users,nazev_menu,poradi_menu,zakladni_modul,jen_admin_modul,blokovat_modul,plugin ";
$dotaz.="from ".$GLOBALS["rspredpona"]."moduly_prava order by poradi_menu";

$dotazmoduly=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetmooduly=mysql_num_rows($dotazmoduly);

if ($pocetmooduly>0):
  // vypis polozek
  echo "<form action=\"admin.php\" method=\"post\">\n";
  echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">\n";
  echo "<tr class=\"txt\" bgcolor=\"#E6E6E6\">\n";
  echo "<td align=\"center\"><b>".RS_CFG_SM_NAZEV_MODULU."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_CFG_SM_NAZEV_MENU."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_CFG_SM_PRAVA."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_CFG_SM_TYP."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_CFG_SM_STAV."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_CFG_SM_PORADI."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_CFG_SM_AKCE."</b></td>\n";
  echo "</tr>\n";
  for ($pom=0;$pom<$pocetmooduly;$pom++):
    $akt_pole_moduly=mysql_fetch_assoc($dotazmoduly);
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    echo "<td>".$akt_pole_moduly["nazev_modulu"]."</td>";
    echo "<td>".$akt_pole_moduly["nazev_menu"]."</td>";
    // pristupova prava
    if ($akt_pole_moduly["jen_admin_modul"]==1):
      echo "<td align=\"center\">".RS_CFG_SM_JEN_ADMIN."</td>"; // jen admin
    else:
      if ($akt_pole_moduly["all_prava_users"]==1):
        echo "<td align=\"center\">".RS_CFG_SM_VSICHNI."</td>"; // vsichni uzivatele
      else:
        echo "<td align=\"center\">".RS_CFG_SM_DLE_NASTAV."</td>"; // dle nastaveni
      endif;
    endif;
    // konec - pristupova prava
    // typ modulu
    if ($akt_pole_moduly["zakladni_modul"]==1):
      echo "<td align=\"center\">".RS_CFG_SM_ZAKLADNI_MODUL."</td>"; // zakladni modul - nelze blokovat
    else:
      echo "<td align=\"center\">".RS_CFG_SM_NASTAV_MODUL."</td>"; // nastavitelny modul
    endif;
    // konec - typ modulu
    // stav modulu
    if ($akt_pole_moduly["zakladni_modul"]==1):
      echo "<td>&nbsp;</td>";
    else:
      if ($akt_pole_moduly["blokovat_modul"]==1):
        echo "<td align=\"center\"><a href=\"admin.php?akce=StavModul&amp;modul=config&amp;pridm=".$akt_pole_moduly["idm"]."&amp;prstav=0\">".RS_CFG_SM_AKTIVOVAT."</a></td>"; // aktivovat
      else:
        echo "<td align=\"center\"><a href=\"admin.php?akce=StavModul&amp;modul=config&amp;pridm=".$akt_pole_moduly["idm"]."&amp;prstav=1\">".RS_CFG_SM_BLOKOVAT."</a></td>"; // blokovat
      endif;
    endif;
    // konec - stav modulu
    echo "<td><input type=\"text\" name=\"prporadi[".$pom."]\" size=\"4\" value=\"".$akt_pole_moduly["poradi_menu"]."\" class=\"textpole\" /><input type=\"hidden\" name=\"prmodul_id[".$pom."]\" value=\"".$akt_pole_moduly["idm"]."\" /></td>";
    echo "<td align=\"center\"><a href=\"admin.php?akce=EditModul&amp;modul=config&amp;pridm=".$akt_pole_moduly["idm"]."\">".RS_CFG_SM_UPRAVIT."</a></td>\n";
    echo "</tr>\n";
  endfor;
  echo "<tr><td align=\"right\" colspan=\"7\"><input type=\"submit\" value=\" ".RS_CFG_SM_TL_ULOZ_NASTAV." \" class=\"tl\" /></td></tr>\n";
  echo "</table>\n";
  echo "<input type=\"hidden\" name=\"akce\" value=\"KonfigModul\" /><input type=\"hidden\" name=\"modul\" value=\"config\" />\n";
  echo "</form>\n";
endif;
echo "<p></p>\n";
}

function StavModul()
{
$GLOBALS['pridm']=mysql_escape_string($GLOBALS['pridm']);
$GLOBALS['prstav']=mysql_escape_string($GLOBALS['prstav']);

// update tabulky je omezen pouze na nastavitelne moduly (zakladni_modul=0)
@$error=mysql_query("update ".$GLOBALS["rspredpona"]."moduly_prava set blokovat_modul='".$GLOBALS['prstav']."' where idm='".$GLOBALS['pridm']."' and zakladni_modul=0",$GLOBALS["dbspojeni"]);
if (!$error):
  echo "<p align=\"center\" class=\"txt\">Error C14: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_CFG_SM_OK_STAV_MODULU."</p>\n"; // vse OK
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowModul&amp;modul=config\">".RS_CFG_SM_ZPET_MODULY."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowConfig&amp;modul=config\">".RS_CFG_KO_ZPET."</a></p>\n";
}

function KonfigModul()
{
$chyba_all=0; // inic. chyby - 0 = zadna chyba

// test na pritomnost vsech potrebnych promennych
if (!isset($GLOBALS['prporadi'])||!isset($GLOBALS['prmodul_id'])):
  echo "<p align=\"center\" class=\"txt\">".RS_CFG_SM_ERR_CHYBI_ATR."</p>\n";
else:
  $pocet_polozek=count($GLOBALS['prmodul_id']);
  for ($pom=0;$pom<$pocet_polozek;$pom++):
    // inic.
    $akt_id_modul=mysql_escape_string($GLOBALS['prmodul_id'][$pom]);
    $akt_hodnota=mysql_escape_string($GLOBALS['prporadi'][$pom]);
    // nastaveni
    @$error=mysql_query("update ".$GLOBALS["rspredpona"]."moduly_prava set poradi_menu='".$akt_hodnota."' where idm='".$akt_id_modul."'",$GLOBALS["dbspojeni"]);
    if (!$error):
      echo "<p align=\"center\" class=\"txt\">Error C15: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
      $chyba_all=1; // chyba
    endif;
  endfor;
endif;

// globalni stav
if ($chyba_all==0):
  echo "<p align=\"center\" class=\"txt\">".RS_CFG_SM_OK_PORADI_MODULU."</p>\n"; // vse OK
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowModul&amp;modul=config\">".RS_CFG_SM_ZPET_MODULY."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowConfig&amp;modul=config\">".RS_CFG_KO_ZPET."</a></p>\n";
}

function EditModul()
{
// bezpecnostni korekce
$GLOBALS['pridm']=mysql_escape_string($GLOBALS['pridm']);

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowModul&amp;modul=config\">".RS_CFG_SM_ZPET_MODULY."</a></p>\n";

// formular pro upravu nastaveni modulu
$dotazmodul=mysql_query("select * from ".$GLOBALS["rspredpona"]."moduly_prava where idm='".$GLOBALS['pridm']."'",$GLOBALS["dbspojeni"]);
$pole_data=mysql_fetch_assoc($dotazmodul);

echo "<form action=\"admin.php\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy\" bgcolor=\"#E6E6E6\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_CFG_SM_FORM_TITULEK."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prtitulek\" size=\"50\" value=\"".$pole_data['nazev_menu']."\" class=\"textpole\" /></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CFG_SM_FORM_RGB."</b></td>
<td align=\"left\">#<input type=\"text\" name=\"prbarvabg\" size=\"8\" value=\"".$pole_data['barva_bg']."\" class=\"textpole\" /> ".RS_CFG_SM_FORM_RGB_INFO."</td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcEditModul\" /><input type=\"hidden\" name=\"modul\" value=\"config\" />
<input type=\"hidden\" name=\"pridm\" value=\"".$pole_data['idm']."\" />
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\" /> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\" /></p>
</form>\n";
}

function AcEditModul()
{
// bezpecnostni korekce
$GLOBALS['pridm']=mysql_escape_string($GLOBALS['pridm']);
$GLOBALS['prtitulek']=mysql_escape_string($GLOBALS['prtitulek']);
$GLOBALS['prbarvabg']=mysql_escape_string($GLOBALS['prbarvabg']);

// aktualizace nastaveni modulu
$dotaz="update ".$GLOBALS["rspredpona"]."moduly_prava set nazev_menu='".$GLOBALS['prtitulek']."',barva_bg='".$GLOBALS['prbarvabg']."' where idm='".$GLOBALS['pridm']."'";
@$error=mysql_query($dotaz,$GLOBALS["dbspojeni"]);
if (!$error):
  echo "<p align=\"center\" class=\"txt\">Error C16: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_CFG_SM_OK_EDIT_MODULU."</p>\n"; // vse OK
endif;

// navrat
ShowModul();
}
?>
