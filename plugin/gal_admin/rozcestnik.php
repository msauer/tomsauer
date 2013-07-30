<?php
######################################################################
# phpRS Gallery 0.99.500
######################################################################
// phpRS Copyright (c) 2001-2004 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// Gallery Copyright (c) 2004 by Michal Safus (michalsafus@gmail.com)
// http://www.phprs.cz/magazin/gallery
// This program is free software. - Toto je bezplatny a svobodny software.


switch ($GLOBALS["akce"]):
     case "kat_prehled": AdminMenu();
          echo "<p><h2 align=\"center\">Přehled všech kategorií</h2><center><a href=\"admin.php?modul=gallery&amp;akce=konf_prehled\">Konfigurace Galerie</a> - <a href=\"admin.php?modul=gallery&amp;akce=gal_prehled\">Správa Galerií</a> - <a href=\"admin.php?modul=gallery&amp;akce=Gal_Comment\">Správa Komentářů</a> - <a href=\"admin.php?modul=gallery&amp;akce=kat_prehled\">Správa Kategroií</a></center></p><p align=\"center\">";
          include("plugin/gal_admin/agal.php");
          KategoriePrehled();
          break;
     case "kat_smaz": AdminMenu();
          echo "<p><h2 align=\"center\">Smazání kategorie</h2><center><a href=\"admin.php?modul=gallery&amp;akce=konf_prehled\">Konfigurace Galerie</a> - <a href=\"admin.php?modul=gallery&amp;akce=gal_prehled\">Správa Galerií</a> - <a href=\"admin.php?modul=gallery&amp;akce=Gal_Comment\">Správa Komentářů</a> - <a href=\"admin.php?modul=gallery&amp;akce=kat_prehled\">Správa Kategroií</a></center></p><p align=\"center\">";
          include("plugin/gal_admin/agal.php");
          KategorieSmaz();
          break;
     case "kat_uprav": AdminMenu();
          echo "<p><h2 align=\"center\">Upravení kategorie</h2><center><a href=\"admin.php?modul=gallery&amp;akce=konf_prehled\">Konfigurace Galerie</a> - <a href=\"admin.php?modul=gallery&amp;akce=gal_prehled\">Správa Galerií</a> - <a href=\"admin.php?modul=gallery&amp;akce=Gal_Comment\">Správa Komentářů</a> - <a href=\"admin.php?modul=gallery&amp;akce=kat_prehled\">Správa Kategroií</a></center></p><p align=\"center\">";
          include("plugin/gal_admin/agal.php");
          KategorieUprav();
          break; 
     case "kat_pridej": AdminMenu();
          echo "<p><h2 align=\"center\">Přidání kategorie</h2><center><a href=\"admin.php?modul=gallery&amp;akce=konf_prehled\">Konfigurace Galerie</a> - <a href=\"admin.php?modul=gallery&amp;akce=gal_prehled\">Správa Galerií</a> - <a href=\"admin.php?modul=gallery&amp;akce=Gal_Comment\">Správa Komentářů</a> - <a href=\"admin.php?modul=gallery&amp;akce=kat_prehled\">Správa Kategroií</a></center></p><p align=\"center\">";
          include("plugin/gal_admin/agal.php");
          KategoriePridej();
          break;                           


     case "gal_prehled": AdminMenu();
          echo "<p><h2 align=\"center\">Přehled všech galerií</h2><center><a href=\"admin.php?modul=gallery&amp;akce=konf_prehled\">Konfigurace Galerie</a> - <a href=\"admin.php?modul=gallery&amp;akce=gal_prehled\">Správa Galerií</a> - <a href=\"admin.php?modul=gallery&amp;akce=Gal_Comment\">Správa Komentářů</a> - <a href=\"admin.php?modul=gallery&amp;akce=kat_prehled\">Správa Kategroií</a></center></p><p align=\"center\">";
          include("plugin/gal_admin/agal.php");
          GaleriePrehled();
          break;
     case "gal_ukaz": AdminMenu();
          echo "<p><h2 align=\"center\">Zobrazení galerie</h2><center><a href=\"admin.php?modul=gallery&amp;akce=konf_prehled\">Konfigurace Galerie</a> - <a href=\"admin.php?modul=gallery&amp;akce=gal_prehled\">Správa Galerií</a> - <a href=\"admin.php?modul=gallery&amp;akce=Gal_Comment\">Správa Komentářů</a> - <a href=\"admin.php?modul=gallery&amp;akce=kat_prehled\">Správa Kategroií</a></center></p><p align=\"center\">";
          include("plugin/gal_admin/agal.php");
          GalerieUkaz();
          break;
     case "gal_uprav": AdminMenu();
          echo "<p><h2 align=\"center\">Upravení galerie</h2><center><a href=\"admin.php?modul=gallery&amp;akce=konf_prehled\">Konfigurace Galerie</a> - <a href=\"admin.php?modul=gallery&amp;akce=gal_prehled\">Správa Galerií</a> - <a href=\"admin.php?modul=gallery&amp;akce=Gal_Comment\">Správa Komentářů</a> - <a href=\"admin.php?modul=gallery&amp;akce=kat_prehled\">Správa Kategroií</a></center></p><p align=\"center\">";
          include("plugin/gal_admin/agal.php");
          GalerieUprav();
          break;
     case "gal_obnov": AdminMenu();
          echo "<p><h2 align=\"center\">Obnovení galerie</h2><center><a href=\"admin.php?modul=gallery&amp;akce=konf_prehled\">Konfigurace Galerie</a> - <a href=\"admin.php?modul=gallery&amp;akce=gal_prehled\">Správa Galerií</a> - <a href=\"admin.php?modul=gallery&amp;akce=Gal_Comment\">Správa Komentářů</a> - <a href=\"admin.php?modul=gallery&amp;akce=kat_prehled\">Správa Kategroií</a></center></p><p align=\"center\">";
          include("plugin/gal_admin/agal.php");
          GalerieObnov();
          break;          
     case "gal_smaz": AdminMenu();
          echo "<p><h2 align=\"center\">Smazání galerie</h2><center><a href=\"admin.php?modul=gallery&amp;akce=konf_prehled\">Konfigurace Galerie</a> - <a href=\"admin.php?modul=gallery&amp;akce=gal_prehled\">Správa Galerií</a> - <a href=\"admin.php?modul=gallery&amp;akce=Gal_Comment\">Správa Komentářů</a> - <a href=\"admin.php?modul=gallery&amp;akce=kat_prehled\">Správa Kategroií</a></center></p><p align=\"center\">";
          include("plugin/gal_admin/agal.php");
          GalerieSmaz();
          break;

     case "obr_uprav": AdminMenu();
          echo "<p><h2 align=\"center\">Upravení obrázku</h2><center><a href=\"admin.php?modul=gallery&amp;akce=konf_prehled\">Konfigurace Galerie</a> - <a href=\"admin.php?modul=gallery&amp;akce=gal_prehled\">Správa Galerií</a> - <a href=\"admin.php?modul=gallery&amp;akce=Gal_Comment\">Správa Komentářů</a> - <a href=\"admin.php?modul=gallery&amp;akce=kat_prehled\">Správa Kategroií</a></center></p><p align=\"center\">";
          include("plugin/gal_admin/agal.php");
          ObrazekUprav();
          break;

     case "obr_smaz": AdminMenu();
          echo "<p><h2 align=\"center\">Upravení obrázku</h2><center><a href=\"admin.php?modul=gallery&amp;akce=konf_prehled\">Konfigurace Galerie</a> - <a href=\"admin.php?modul=gallery&amp;akce=gal_prehled\">Správa Galerií</a> - <a href=\"admin.php?modul=gallery&amp;akce=Gal_Comment\">Správa Komentářů</a> - <a href=\"admin.php?modul=gallery&amp;akce=kat_prehled\">Správa Kategroií</a></center></p><p align=\"center\">";
          include("plugin/gal_admin/agal.php");
          ObrazekSmaz($GLOBALS["media_id"]);
          break;
          
               // komentare
     case "Gal_Comment": AdminMenu();
          echo "<h2 align=\"center\">Komentáře obrázků</h2><center><a href=\"admin.php?modul=gallery&amp;akce=konf_prehled\">Konfigurace Galerie</a> - <a href=\"admin.php?modul=gallery&amp;akce=gal_prehled\">Správa Galerií</a> - <a href=\"admin.php?modul=gallery&amp;akce=Gal_Comment\">Správa Komentářů</a> - <a href=\"admin.php?modul=gallery&amp;akce=kat_prehled\">Správa Kategroií</a></center></p><p align=\"center\">";
          include("plugin/gal_admin/agal.php");
          VypisKom();
          break;
     case "Gal_ShowComment": AdminMenu();
          echo "<h2 align=\"center\">Komentáře obrázků</h2><center><a href=\"admin.php?modul=gallery&amp;akce=konf_prehled\">Konfigurace Galerie</a> - <a href=\"admin.php?modul=gallery&amp;akce=gal_prehled\">Správa Galerií</a> - <a href=\"admin.php?modul=gallery&amp;akce=Gal_Comment\">Správa Komentářů</a> - <a href=\"admin.php?modul=gallery&amp;akce=kat_prehled\">Správa Kategroií</a></center></p><p align=\"center\">";
          include("plugin/gal_admin/agal.php");
          UpravKom();
          break;
     case "Gal_DeleteComment": AdminMenu();
          echo "<h2 align=\"center\">Vymaž obsah komentáře</h2><center><a href=\"admin.php?modul=gallery&amp;akce=konf_prehled\">Konfigurace Galerie</a> - <a href=\"admin.php?modul=gallery&amp;akce=gal_prehled\">Správa Galerií</a> - <a href=\"admin.php?modul=gallery&amp;akce=Gal_Comment\">Správa Komentářů</a> - <a href=\"admin.php?modul=gallery&amp;akce=kat_prehled\">Správa Kategroií</a></center></p><p align=\"center\">";
          include("plugin/gal_admin/agal.php");
          VymazKom();
          break;
     case "Gal_DelAllComment": AdminMenu();
          echo "<h2 align=\"center\">Vymaž celý komentář</h2><center><a href=\"admin.php?modul=gallery&amp;akce=konf_prehled\">Konfigurace Galerie</a> - <a href=\"admin.php?modul=gallery&amp;akce=gal_prehled\">Správa Galerií</a> - <a href=\"admin.php?modul=gallery&amp;akce=Gal_Comment\">Správa Komentářů</a> - <a href=\"admin.php?modul=gallery&amp;akce=kat_prehled\">Správa Kategroií</a></center></p><p align=\"center\">";
          include("plugin/gal_admin/agal.php");
          VymazCelyKom();
          break;

     case "konf_prehled": AdminMenu();
          echo "<h2 align=\"center\">Konfigurace galerie</h2><center><a href=\"admin.php?modul=gallery&amp;akce=konf_prehled\">Konfigurace Galerie</a> - <a href=\"admin.php?modul=gallery&amp;akce=gal_prehled\">Správa Galerií</a> - <a href=\"admin.php?modul=gallery&amp;akce=Gal_Comment\">Správa Komentářů</a> - <a href=\"admin.php?modul=gallery&amp;akce=kat_prehled\">Správa Kategroií</a></center></p><p align=\"center\">";
          include("plugin/gal_admin/agal.php");
          KonfiguracePrehled();
          break;
     case "konf_uloz": AdminMenu();
          echo "<h2 align=\"center\">Konfigurace galerie</h2><center><a href=\"admin.php?modul=gallery&amp;akce=konf_prehled\">Konfigurace Galerie</a> - <a href=\"admin.php?modul=gallery&amp;akce=gal_prehled\">Správa Galerií</a> - <a href=\"admin.php?modul=gallery&amp;akce=Gal_Comment\">Správa Komentářů</a> - <a href=\"admin.php?modul=gallery&amp;akce=kat_prehled\">Správa Kategroií</a></center></p><p align=\"center\">";
          include("plugin/gal_admin/agal.php");
          KonfiguraceUloz();
          break;
     case "konf_gd": AdminMenu();
          echo "<h2 align=\"center\">Konfigurace galerie</h2><center><a href=\"admin.php?modul=gallery&amp;akce=konf_prehled\">Konfigurace Galerie</a> - <a href=\"admin.php?modul=gallery&amp;akce=gal_prehled\">Správa Galerií</a> - <a href=\"admin.php?modul=gallery&amp;akce=Gal_Comment\">Správa Komentářů</a> - <a href=\"admin.php?modul=gallery&amp;akce=kat_prehled\">Správa Kategroií</a></center></p><p align=\"center\">";
          include("plugin/gal_admin/agal.php");
          KonfiguraceGD();
          break;          
endswitch;
?> 