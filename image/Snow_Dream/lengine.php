<?

######################################################################
# phpRS Layout Engine 2.3.0 - verze: "FreeStyle"
######################################################################

// Copyright (c) 2002-2004 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.
// Layout Snow_Dream uprava Jan Kulík, 3/2004

// promenna obsahujici nazev a verzi layoutu phpRS
$layoutversion="Layout Engine: FreeStyle verze 2.3.0";
// promenna obsahujici pripojeni na zakladni CSS soubor tohoto layoutu
$layoutcss="<link rel=\"stylesheet\" href=\"image/freestyle/freestyle.css\" type=\"text/css\">";

// ----------- [priprava na generovani stranky] -----------

if (!isset($rs_main_sablona)): $rs_main_sablona=""; endif;

$vzhledwebu = new CLayout(); // inic. vzhledove tridy

switch ($rs_main_sablona):
  case "base": // zakladni sablona
    $vzhledwebu->NactiFileSablonu("image/freestyle/fs_base.sab");
    $vzhledwebu->UlozPro("title","Testování Redsysu...");
    $vzhledwebu->UlozPro("datum",Date("d. m. Y"));
    $vzhledwebu->UlozPro("banner1",Banners_str(1));
    $vzhledwebu->UlozPro("banner2",Banners_str(2));
    break;
  case "download": // download sablona
    $vzhledwebu->NactiFileSablonu("image/freestyle/fs_download.sab");
    $vzhledwebu->UlozPro("title","Super Svìt");
    $vzhledwebu->UlozPro("datum",Date("d. m. Y"));
    $vzhledwebu->UlozPro("banner1",Banners_str(1));
    $vzhledwebu->UlozPro("banner2",Banners_str(2));
    break;
  default: // defaultni sablona - je shodna s jednou z vyse uvedenych sablon
    $vzhledwebu->NactiFileSablonu("image/freestyle/fs_base.sab");
    $vzhledwebu->UlozPro("title","Super Svìt");
    $vzhledwebu->UlozPro("datum",Date("d. m. Y"));
    break;
endswitch;

$vzhledwebu->Inic();

// ------- [konec - priprava na generovani stranky] -------

function Blok1($bnadpis,$bdata)
{
echo "<!-- Blok -->
<table cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\" background=image/freestyle/tableheader.jpg border=0 align=center<tr>
<td class=\"biltucne\">&nbsp; ".$bnadpis."</td><td width=\"6\"></td>
</tr></table>
<table cellSpacing=\"0\" cellPadding=\"3\" width=\"100%\" bgColor=\"#F9F9F9\" border=\"0\"><tr><td class=\"z1\">\n";
echo $bdata; // data
echo "</td></tr></table>
\n";
}

function Blok2($bnadpis,$bdata)
{
echo "<!-- Blok -->
<table cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\" background=image/freestyle/tableheader1.jpg border=0 align=center<tr>
<td class=\"biltucne\">&nbsp; ".$bnadpis."</td><td width=\"6\"></td>
</tr></table>
<table cellSpacing=\"0\" cellPadding=\"3\" width=\"100%\" bgColor=\"#F9F9F9\" border=\"0\"><tr><td class=\"z1\">\n";
echo $bdata; // data
echo "</td></tr></table>
\n";
}

function Blok3($bnadpis,$bdata)
{
echo "<!-- Blok -->
<table cellSpacing=\"0\" cellPadding=\"3\" width=\"100%\" bgColor=\"#F9F9F9\" borderColor=\"#B9B9B9\" border=\"0\"><tr><td class=\"z1\">\n";
echo $bdata; // data
echo "</td></tr></table>
\n";
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