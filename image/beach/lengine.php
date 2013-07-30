<?

######################################################################
# phpRS Layout Engine 2.3.0 - verze: "Beach"
######################################################################

// Copyright (c) 2002-2004 by Jiri Lukas (jirilukas@supersvet.cz) & layout vytvoril: Lukas Soucek (soucek.l@email.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// promenna obsahujici nazev a verzi layoutu phpRS
$layoutversion="Layout Engine: Beach verze 2.3.0 (verze 1.1.1)";
// promenna obsahujici pripojeni na zakladni CSS soubor tohoto layoutu
$layoutcss="<link rel=\"stylesheet\" href=\"image/beach/beach.css\" type=\"text/css\">";

// ----------- [priprava na generovani stranky] -----------

if (!isset($rs_main_sablona)): $rs_main_sablona=""; endif;

$vzhledwebu = new CLayout(); // inic. vzhledove tridy

switch ($rs_main_sablona):
  case "base": // zakladni sablona
    $vzhledwebu->NactiFileSablonu("image/beach/fs_base.sab");
    $vzhledwebu->UlozPro("title",$wwwname);
    $vzhledwebu->UlozPro("datum",Date("d. m. Y"));
    $vzhledwebu->UlozPro("banner1",Banners_str(1));
    $vzhledwebu->UlozPro("banner2",Banners_str(2));
    break;
  case "download": // download sablona
    $vzhledwebu->NactiFileSablonu("image/beach/fs_download.sab");
    $vzhledwebu->UlozPro("title",$wwwname);
    $vzhledwebu->UlozPro("datum",Date("d. m. Y"));
    $vzhledwebu->UlozPro("banner1",Banners_str(1));
    $vzhledwebu->UlozPro("banner2",Banners_str(2));
    break;
  default: // defaultni sablona - je shodna s jednou z vyse uvedenych sablon
    $vzhledwebu->NactiFileSablonu("image/beach/fs_base.sab");
    $vzhledwebu->UlozPro("title",$wwwname);
    $vzhledwebu->UlozPro("datum",Date("d. m. Y"));
    break;
endswitch;

$vzhledwebu->Inic();

// ------- [konec - priprava na generovani stranky] -------

function Blok1($bnadpis,$bdata)
{
echo "<!-- Blok -->
<table class=\"transparent\" cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\" bgColor=\"#474747\" border=\"1\" bordercolor=\"#474747\" align=\"center\"><tr>
<td class=\"biltucne\">&nbsp; ".$bnadpis."</td>
</tr></table>
<table class=\"transparent\"cellSpacing=\"0\" cellPadding=\"3\" width=\"100%\" bgColor=\"#e3e3e3\" border=\"1\" bordercolor=\"#474747\"><tr><td class=\"z\">\n";
echo $bdata; // data
echo "</td></tr></table>
<br />\n";
}

function Blok2($bnadpis,$bdata)
{
echo "<!-- Blok -->
<table class=\"transparent\" cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\" bgColor=\"#474747\" border=\"1\" bordercolor=\"#474747\" align=\"center\"><tr>
<td class=\"biltucne\">&nbsp; ".$bnadpis."</td>
</tr></table>
<table class=\"transparent\" cellSpacing=\"0\" cellPadding=\"3\" width=\"100%\" bgColor=\"#e3e3e3\" border=\"1\" bordercolor=\"#474747\"><tr><td class=\"z\">\n";
echo $bdata; // data
echo "</td></tr></table>
<br />\n";
}

function Blok3($bnadpis,$bdata)
{
echo "<!-- Blok -->
<table cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\" bgColor=\"#444444\" border=\"1\" bordercolor=\"#035D8A\" align=\"center\"><tr>
<td class=\"biltucne\">&nbsp; ".$bnadpis."</td>
</tr></table>
<table cellSpacing=\"0\" cellPadding=\"3\" width=\"100%\" bgColor=\"#E6EEFF\" border=\"1\" bordercolor=\"#035D8A\"><tr><td class=\"z\">\n";
echo $bdata; // data
echo "</td></tr></table>
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