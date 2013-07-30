<?

######################################################################
# phpRS RSS 1.0.8 (RSS version 2.00)
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_topic, rs_clanky

/*
Tento soubor zajistuje generovani RSS souboru pro moznost vzajemne vymeny clanku mezi informacnimi servery.
Generuje RSS verze 2.0.

Promenna $mnozstvi definuje pocet clanku v RSS souboru. Default hodnota = 5
*/

define('IN_CODE',true); // inic. ochranne konstanty

include_once("config.php");

function Hlavicka($data)
{
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>
<rss version=\"2.0\">
\t<channel>
\t\t<title>".$GLOBALS["wwwname"]."</title>
\t\t<link>".$GLOBALS["baseadr"]."</link>
\t\t<description>Super Svet - casopis o IT, PHP, MySQL a phpRS informacnim systemu.</description>
\t\t<language>cs</language>
\t\t<lastBuildDate>".GMDate("D, d M Y H:i:s")." GMT</lastBuildDate>\r\n
\t\t<webMaster>phprs@supersvet.cz (phpRS system)</webMaster>
\t\t<managingEditor>phprs@supersvet.cz (phpRS system)</managingEditor>
\t\t<copyright>".Date("Y")." ".$GLOBALS["wwwname"].". All rights reserved.</copyright>";  // GMDate() - GMT
echo $data;
echo "\t</channel>
</rss>";
}

$dnesnidatum=Date("Y-m-d H:i:s");  // dnesni datum ve formatu datetime
if (!isset($GLOBALS["mnozstvi"])): $GLOBALS["mnozstvi"]=5; else: $GLOBALS["mnozstvi"]=addslashes($GLOBALS["mnozstvi"]); endif;  // test na pritomnost promenne mnozstvi

// generovani RSS souboru - obsahuje nejakt. clanku
$dotazcl=mysql_query("select c.link,c.titulek,c.uvod,date_format(datum,'%a, %d %b %Y %H:%i:%S ') as datum,t.nazev from ".$GLOBALS["rspredpona"]."clanky as c,".$GLOBALS["rspredpona"]."topic as t where c.tema=t.idt and c.visible='1' and datum<'".$dnesnidatum."' order by c.datum desc limit 0,".$GLOBALS["mnozstvi"],$GLOBALS["dbspojeni"]);
$pocetcl=mysql_num_rows($dotazcl);
$prdata="";
//$datumek=;
if ($pocetcl>0): // existuji nejake clanky
  // z nazev a uvodu clanku jsou odstraneny vsechy HTML tagy
  for($pom=0;$pom<$pocetcl;$pom++):
    $pole_data=mysql_fetch_assoc($dotazcl);
    $prdata .="\t\t<item>\r\n";
    $prdata .="\t\t\t<title>".htmlspecialchars(strip_tags($pole_data["titulek"]))."</title>\r\n";  // nazev. cl
    $prdata .="\t\t\t<link>".$baseadr."view.php?cisloclanku=".$pole_data["link"]."</link>\r\n"; // link cl.
    $prdata .="\t\t\t<pubDate>".$pole_data["datum"]."GMT</pubDate>\r\n"; // datum vydani
    $prdata .="\t\t\t<description>".htmlspecialchars(strip_tags($pole_data["uvod"]))."</description>\r\n"; // uvodni cast
    $prdata .="\t\t\t<category>".$pole_data["nazev"]."</category>\r\n"; // kategorie
    $prdata .="\t\t</item>\r\n";
  endfor;
endif;

header("Content-Type: text/xml");
Hlavicka($prdata);
?>
