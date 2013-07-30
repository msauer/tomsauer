<?

######################################################################
# phpRS Layout Engine 2.6.0 - verze: "FreeStyle"
######################################################################

// Copyright (c) 2002-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// nazev a verze phpRS layoutu
$layoutversion='Layout Engine: FreeStyle verze 2.6.0';
// HTML META tag LINK pro pripojeni zakladniho CSS souboru
$layoutcss='<link rel="stylesheet" href="image/freestyle/freestyle.css" type="text/css">';
// kodovani phpRS layoutu
$layoutkodovani='utf-8';

// ----------- [priprava na generovani stranky] -----------

if (!isset($rs_main_sablona)): $rs_main_sablona=""; endif;

$vzhledwebu = new CLayout(); // inic. vzhledove tridy

switch ($rs_main_sablona):
  case 'base': // zakladni sablona
    $vzhledwebu->NactiFileSablonu('image/freestyle/fs_base.sab');
    $vzhledwebu->UlozPro('title',$wwwname);
    $vzhledwebu->UlozPro('datum',Date("d. m. Y"));
    $vzhledwebu->UlozPro('banner1',Banners_str(1));
    $vzhledwebu->UlozPro('banner2',Banners_str(2));
    break;
  case 'download': // download sablona
    $vzhledwebu->NactiFileSablonu('image/freestyle/fs_download.sab');
    $vzhledwebu->UlozPro('title',$wwwname);
    $vzhledwebu->UlozPro('datum',Date("d. m. Y"));
    $vzhledwebu->UlozPro('banner1',Banners_str(1));
    $vzhledwebu->UlozPro('banner2',Banners_str(2));
    break;
  default: // defaultni sablona - je shodna s jednou z vyse uvedenych sablon
    $vzhledwebu->NactiFileSablonu('image/freestyle/fs_base.sab');
    $vzhledwebu->UlozPro('title',$wwwname);
    $vzhledwebu->UlozPro('datum',Date("d. m. Y"));
    break;
endswitch;

$vzhledwebu->Inic();

// ------- [konec - priprava na generovani stranky] -------

function Blok1($bnadpis = '',$bdata = '')
{
echo "<!-- Blok -->
<table cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\" bgColor=\"#003098\" border=\"0\" align=\"center\"><tr>
<td class=\"biltucne\">&nbsp; ".$bnadpis."</td><td width=\"6\"><img src=\"image/freestyle/konectit.gif\" height=\"16\" width=\"6\" border=\"0\" alt=\"kulatý roh\" /></td>
</tr></table>
<table cellSpacing=\"0\" cellPadding=\"3\" width=\"100%\" bgColor=\"#FFFFFF\" border=\"0\"><tr><td class=\"z\">\n";
echo $bdata; // data
echo "</td></tr></table>
<br />\n";
}

function Blok2($bnadpis = '',$bdata = '')
{
echo "<!-- Blok -->
<table cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\" bgColor=\"#003098\" border=\"0\" align=\"center\"><tr>
<td class=\"biltucne\">&nbsp; ".$bnadpis."</td><td width=\"6\"><img src=\"image/freestyle/konectit.gif\" height=\"16\" width=\"6\" border=\"0\" alt=\"kulatý roh\" /></td>
</tr></table>
<table cellSpacing=\"0\" cellPadding=\"3\" width=\"100%\" bgColor=\"#FFFFFF\" border=\"0\"><tr><td class=\"z\">\n";
echo $bdata; // data
echo "</td></tr></table>
<br />\n";
}

function Blok3($bnadpis = '',$bdata = '')
{
echo "<!-- Blok -->
<table cellSpacing=\"0\" cellPadding=\"3\" width=\"100%\" bgColor=\"#FFFFFF\" border=\"0\"><tr><td class=\"z\">\n";
echo $bdata; // data
echo "</td></tr></table>
<br />\n";
}

function Blok4($bnadpis = '',$bdata = '')
{
echo "<!-- Blok -->
<table cellSpacing=\"0\" cellPadding=\"3\" width=\"100%\" bgColor=\"#FFFFFF\" border=\"0\"><tr><td class=\"z\">\n";
echo $bdata; // data
echo "</td></tr></table>
<br />\n";
}

function Blok5($bnadpis = '',$bdata = '')
{
echo "<!-- Blok -->
<table cellSpacing=\"0\" cellPadding=\"3\" width=\"100%\" bgColor=\"#FFFFFF\" border=\"0\"><tr><td class=\"z\">\n";
echo $bdata; // data
echo "</td></tr></table>
<br />\n";
}

function ObrTabulka()
{
echo "<div class=\"modryram\"><div class=\"z\">\n";
}

function KonecObrTabulka()
{
echo "</div></div>
<p></p>\n";
}
?>
