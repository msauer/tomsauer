<?

######################################################################
# phpRS Layout Engine 2.3.0 - verze: "Bewell3"
######################################################################

// Copyright (c) 2002-2004 by Jiri Lukas (jirilukas@supersvet.cz), upravil: Tomáš Möller (tomas.moller@stribro.net)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// promenna obsahujici nazev a verzi layoutu phpRS
$layoutversion="Layout Engine: Bewell3 verze 2.3.0";
// promenna obsahujici pripojeni na zakladni CSS soubor tohoto layoutu
$layoutcss="<link rel=\"stylesheet\" href=\"image/bewell3/bewell3.css\" type=\"text/css\">";

// ----------- [priprava na generovani stranky] -----------

if (!isset($rs_main_sablona)): $rs_main_sablona=""; endif;

$vzhledwebu = new CLayout(); // inic. vzhledove tridy

switch ($rs_main_sablona):
  case "base": // zakladni sablona
    $vzhledwebu->NactiFileSablonu("image/bewell3/bw3_base.sab");
    $vzhledwebu->UlozPro("title","Titulek");
    $vzhledwebu->UlozPro("datum",Date("d. m. Y"));
    $vzhledwebu->UlozPro("banner1",Banners_str(1));
    $vzhledwebu->UlozPro("banner2",Banners_str(2));
    break;
  case "download": // download sablona
    $vzhledwebu->NactiFileSablonu("image/bewell3/bw3_download.sab");
    $vzhledwebu->UlozPro("title","Titulek");
    $vzhledwebu->UlozPro("datum",Date("d. m. Y"));
    $vzhledwebu->UlozPro("banner1",Banners_str(1));
    $vzhledwebu->UlozPro("banner2",Banners_str(2));
    break;
  default: // defaultni sablona - je shodna s jednou z vyse uvedenych sablon
    $vzhledwebu->NactiFileSablonu("image/bewell3/bw3_base.sab");
    $vzhledwebu->UlozPro("title","Titulek");
    $vzhledwebu->UlozPro("datum",Date("d. m. Y"));
    break;
endswitch;

$vzhledwebu->Inic();

// ------- [konec - priprava na generovani stranky] -------

function Blok1($bnadpis,$bdata)
{
echo "<!-- Blok -->
<div class=\"modryram\">
<table cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\" bgColor=\"#0061D7\" border=\"0\" align=\"center\"><tr>
<td class=\"biltucne\">&nbsp; ".$bnadpis."</td>
</tr></table>
<table cellSpacing=\"0\" cellPadding=\"3\" width=\"100%\" bgColor=\"#5EA6FF\" border=\"0\"><tr><td class=\"z\">\n";
echo $bdata; // data
echo "</td></tr></table></div>
<br />\n";
}

function Blok2($bnadpis,$bdata)
{
echo "<!-- Blok -->
<div class=\"modryram\">
<table cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\" bgColor=\"#0061D7\" border=\"0\" align=\"center\"><tr>
<td class=\"biltucne\">&nbsp; ".$bnadpis."</td>
</tr></table>
<table cellSpacing=\"0\" cellPadding=\"3\" width=\"100%\" bgColor=\"#5EA6FF\" border=\"0\"><tr><td class=\"z\">\n";
echo $bdata; // data
echo "</td></tr></table></div>
<br />\n";
}

function Blok3($bnadpis,$bdata)
{
echo "<!-- Blok -->
<div class=\"modryram\">
<table cellSpacing=\"0\" cellPadding=\"3\" width=\"100%\" bgColor=\"#5EA6FF\" border=\"0\"><tr><td class=\"z\">\n";
echo $bdata; // data
echo "</td></tr></table></div>
<br />\n";
}

function ObrTabulka()
{
echo "<div class=\"modryram\">
<table align=\"center\"><tr><td class=\"z\">\n";
}

function KonecObrTabulka()
{
echo "</td></tr></table>
</div>
<p></p>\n";
}
?>