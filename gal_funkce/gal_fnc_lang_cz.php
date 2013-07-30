<?php
######################################################################
# phpRS Gallery 0.99.500
######################################################################
// phpRS Copyright (c) 2001-2004 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// Gallery Copyright (c) 2004 by Michal Safus (michalsafus@gmail.com)
// http://www.phprs.cz/magazin/gallery
// This program is free software. - Toto je bezplatny a svobodny software.

define("GAL_UKAZ_ZADNY_OBR","Galerie neobsahuje žádný obrázek!");

define("GAL_OBRAZEK_NOVY_PRIHLASENI","Obrázky mohou přidávat pouze přihlášení uživatelé.");

/* Jazykovy koutek */
define("GAL_OBRAZKY_MAXI_PRIDANI","Sorry, nejvyšší možný počet obrázků přidaných najednou je ");
define("GAL_OBRAZKY_OBRAZEK","Obrázek");
define("GAL_OBRAZKY_CESTA","Cesta k obrázku");
define("GAL_OBRAZKY_TITULEK","Titulek obrázku");
define("GAL_OBRAZKY_POPIS","Popis obrázku");
define("GAL_OBRAZKY_KATEGORIE","Kategorie");

define("GAL_NAV_PREHLED","Přehled galerií");
define("GAL_NAV_PREHLED_INTERNI","Přehled galerií z článků");
define("GAL_NAV_KATEGORIE","Přehled kategorií");
define("GAL_NAV_NOVA","Nová galerie");
define("GAL_NAV_PRIDANI","Přidání obrázků");
define("GAL_NAV_TOP","Nejlepší obrázky");
define("GAL_NAV_NOVY","Nový obrázek");

define("GAL_FTP_ADRESAR_NE","Zadaná cesta není cesta k adresáři");
define("GAL_FTP_NE","Nemůžete přidat obrázky pomocí FTP.");

define("GAL_ZALOZENO"," založeno, ");
define("GAL_ZBYVA"," zbývá");

define("GAL_VERZE_ORIGVERZE","");
define("GAL_VERZE_GALERIE","Verze Galerie");
define("GAL_VERZE_INFO","phpRS Gallery - system info");
define("GAL_VERZE_VERZE","Verze phpRS z ");
define("GAL_VERZE_NOVA","Existuje novější verze?");

define("GAL_COMM_ZADNY_KOMENTAR","K tomuto obrázku nebyl zatím přiřazen žádný komentář");
define("GAL_COMM_ZOBRAZIT_VSE","Zobrazit vše");
define("GAL_COMM_NICK","Nick (přezdívka)");
define("GAL_COMM_MAIL","E-mail");
define("GAL_COMM_TITL","Titulek");
define("GAL_COMM_MAXI","Maximálně 2000 znaků");
define("GAL_COMM_TUCNE","tučné");
define("GAL_COMM_ODKAZ","www.neco.cz");
define("GAL_COMM_EMAIL","nekdo@nekde.cz");
define("GAL_COMM_ODESLAT","Odeslat");
define("GAL_COMM_RESET","Reset");
define("GAL_COMM_KO","Nepodařilo se komentář přidat do databáze, zkuste to později");
define("GAL_COMM_OK","Komentář přidán");
define("GAL_COMM_ZPET","Zpět");
define("GAL_COMM_UDAJE","Nevyplnil(a) jste všechny povinné údaje");
define("GAL_COMM_REAKCE","Reakce na komentář");
define("GAL_COMM_REAGOVAT","Reagovat");
define("GAL_COMM_ZE_DNE","Komentář ze dne");
define("GAL_COMM_AUTOR","Autor");
define("GAL_COMM_IP","IP");

define("GAL_CHYBA_NOVA","Galerii mohou přidávat pouze registrovaní čtenáři, autoři či administrátoři. Prosím, přihlašte se přes Váš formulář.");
define("GAL_CHYBA_UDAJE","Vyplňte prosím všechny údaje.");
define("GAL_CHYBA_OBEJIT","Nepokoušejte se prosím obejít zabezpečení galerie. Děkujeme.");

define("GAL_CHYBA_DATABAZE","Nepodařilo se uložit požadavek do databáze. Zkuste to prosím zachvíli znovu.");
define("GAL_OK_DATABAZE","Požadavek byl úspěšně uložen do databáze.");

define("GAL_CHYBA_UPRAV_NE","Nemůžete editovat tuto galerii.");
define("GAL_CHYBA_UPRAV_EXIST","Zadaná galerie neexistuje.");

define("GAL_CHYBA_NOVY_SPATNE","Nejméně jeden z obrázků se nepodařilo přidat, prosím zkontrolujte přidané obrázky a případně přidejte zbývající. (pravděpodobně nesplňoval nějaký z omezujících údajů (velikost atd.))");
define("GAL_CHYBA_NOVY_NAHLED","Nepodařilo se vygenerovat náhled. Zkuste to prosím znovu.");
define("GAL_CHYBA_NOVY_KOPIE","Nepodařilo se obrázek zkopírovat na web.");
define("GAL_CHYBA_NOVY_VELIKOST","Obrázek je moc velký, vyberte prosím menší, nebo zmenšete ten současný.");
define("GAL_CHYBA_NOVY_DATABAZE","Nepodařilo se obrázek přidat.");
define("GAL_CHYBA_NOVY_PRIHLASENI","Nejste přihlášen(a). Nemůžete přidat obrázek.");
define("GAL_CHYBA_NOVY_ADRESAR","Adresář pro nahrání obrázků neexistuje. Prosím informujte Administrátora.");
define("GAL_CHYBA_NOVY_PRIPONA","Galerie nepodporuje přidání tohoto typu souboru.");
define("GAL_CHYBA_NOVY_UDAJE","Vyplňte prosím všechny údaje obrázku.");
define("GAL_CHYBA_NOVY_NE","Nemáte oprávnění k této volbě.");
define("GAL_OK_NOVY_PRIDANI","Obrázek byl úspěšně přidán.");


define("GAL_NADPIS_GAL_PREHLED","Přehled čtenářských galerií");
define("GAL_NADPIS_GAL_NOVA","Založení nové galerie");
define("GAL_NADPIS_GAL_UKAZ","Zobrazení vybrané galerie");
define("GAL_NADPIS_GAL_UPRAV","Upravení stávající galerie");
define("GAL_NADPIS_GAL_SMAZ","Smazání galerie");
define("GAL_NADPIS_OBR_UKAZ","Zobrazení obrázku");
define("GAL_NADPIS_OBR_UPRAV","Upravení obrázku");
define("GAL_NADPIS_OBR_SMAZ","Smazání obrázku");
define("GAL_NADPIS_OBR_NOVY","Přidání obrázku");
define("GAL_NADPIS_OBR_NOVY_ROZCEST","Přidání obrázku");
define("GAL_NADPIS_OBR_NOVY_AUTO","Přidání obrázku");
define("GAL_NADPIS_OBR_NOVY_MANUAL","Přidání obrázku");
define("GAL_NADPIS_OBR_NEJ","Nej... obrázky");
define("GAL_NADPIS_COM_ADD","Zobrazení obrázku");
define("GAL_NADPIS_COM_RE","Zobrazení obrázku");
define("GAL_NADPIS_COM_FULL","Zobrazení obrázku");

define("GAL_NADPIS_GAL_PREHLED_INT","Přehled galerií z článků");
define("GAL_NADPIS_GAL_UKAZ_INT","Zobrazení vybrané galerie");
define("GAL_NADPIS_OBR_UKAZ_INT","Zobrazení obrázku");

define("GAL_INTERNI_NE","Nemůžete prohlížet interní galerie");
define("GAL_INTERNI_NEEX","Žádná interní galerie neexistuje");

define("GAL_ADMIN","Admin");
define("GAL_CTENAR","Čtenář");
define("GAL_AUTOR","Autor");
define("GAL_AUTOR","Redaktor");

define("GAL_SMAZ_VYBRANE","Smaž vybrané");

define("GAL_CHYBA_GAL_NOVA_MAX","Nemůžete zakládat galerie. Vám přidělený limit v počtu galerií byl vyčerpán.");
define("GAL_CHYBA_GAL_NOVA_NE","Nejste přihlášen(a). Nemůžete přidávat galerie.");
define("GAL_CHYBA_GAL_NOVA_UDAJE","Vyplňte prosím všechny údaje.");

define("GAL_TOP_NEJZOBRAZOVANEJSI","Nejvíce prohlížené obrázky");
define("GAL_TOP_NEJNEZOBRAZOVANEJSI","Nejméně prohlížené obrázky");

define("GAL_ZNAMKA","Známka");
define("GAL_ZNAMKA_1","Dej jedničku");
define("GAL_ZNAMKA_2","Dej dvojku");
define("GAL_ZNAMKA_3","Dej trojku");
define("GAL_ZNAMKA_4","Dej čtyřku");
define("GAL_ZNAMKA_5","Dej pětku");

define("GAL_POCET_ZOBRAZENI_0","Obrázek ještě nikdo neviděl.");
define("GAL_POCET_ZOBRAZENI_1","Jste první, co si obrázek prohlíží.");
define("GAL_POCET_ZOBRAZENI_2","Obrázek již viděli dva lidé.");
define("GAL_POCET_ZOBRAZENI_3","Obrázek již viděli tři lidé.");
define("GAL_POCET_ZOBRAZENI_4","Obrázek již viděli čtyři lidé.");
define("GAL_POCET_ZOBRAZENI_5_1","Obrázek již vidělo ");
define("GAL_POCET_ZOBRAZENI_5_2"," lidí");
define("GAL_SOUVISEJICI_NE","Nepodařilo se najít související obrázky.");
define("GAL_SOUVISEJICI","Související obrázky");

define("GAL_VYPIS_ZADNA","Žádná nenalezena - ");
define("GAL_VYPIS_PRIDAT","přidat");

define("GAL_VYPIS_OBRAZKY_0","žádný obrázek");
define("GAL_VYPIS_OBRAZKY_1","1 obrázek");
define("GAL_VYPIS_OBRAZKY_2","2 obrázky");
define("GAL_VYPIS_OBRAZKY_3","3 obrázky");
define("GAL_VYPIS_OBRAZKY_4","4 obrázky");
define("GAL_VYPIS_OBRAZKY_5"," obrázků");

define("GAL_STRANKOVANI_PREDCHOZI","<");
define("GAL_STRANKOVANI_NASLEDUJICI",">");
define("GAL_STRANKOVANI_CELKEM","Celkem ");
define("GAL_STRANKOVANI_CELKEM2_GAL"," galerií");
define("GAL_STRANKOVANI_CELKEM2_OBR"," obrázků");
define("GAL_STRANKOVANI_ZOBRAZENO","zobrazeno ");

define("GAL_OBR_UPRAVA_NE_VLASTNIK","Nejste vlastníkem obrázku, nemůžete ho upravovat.");
define("GAL_OBR_UPRAVA_OK","Obrázek byl úspěšně aktualizován.");
define("GAL_OBR_UPRAVA_KO","Při úpravě obrázku došlo k chybě v databázi, zkuste to později.");
define("GAL_OBR_UPRAVA_UDAJE","Vyplňte prosím všechny údaje.");
define("GAL_OBR_UPRAVA_NE_NE","Tento obrázek prostě NEMŮŽETE upravit, chápete to?");
define("GAL_OBR_UPRAVA_NENI","Určený obrázek nebyl v databázi nalezen.");
define("GAL_OBR_UPRAVA_NEEX","Nebyl určen obrázek, který chcete upravit.");

define("GAL_OBR_UKAZ_NENI","Nebyl určen obrázek, který chcete zobrazit.");
define("GAL_OBR_UKAZ_NEEX","Zadaný obrázek neexistuje.");

define("GAL_OBR_SMAZ_OK","Obrázek číslo ");
define("GAL_OBR_SMAZ_OK2"," úspěšně smazán");
define("GAL_OBR_SMAZ_KO","Obrázek číslo ");
define("GAL_OBR_SMAZ_KO2"," se nepodařilo smazat");
define("GAL_OBR_SMAZ_NE","Nemůžete smazat tento obrázek");

define("GAL_SMAZ_OK","Jste si opravdu jisti že chcete galerii smazat včetně všech obrázků? Opravdu? FAKT?<br />");
define("GAL_SMAZ_OK_OK","Galerie byla úspěšně smazána. Finální smazání provede administrátor.<br /><br />Smazali jste si galerii a byl to omyl? Nezoufejte, napište administrátorovi těchto stránek.");
define("GAL_ANO","Ano");
define("GAL_NE","Ne");
define("GAL_SMAZ_NE","Nemůžete smazat galerii.");

define("GAL_NADPIS_KAT_PREHLED","Přehled podle kategorií");
define("GAL_NADPIS_KAT_UKAZ","Zobrazení kategorie");
define("GAL_CHYBA_KAT_NENI","Nezadali jste číslo kategorie, kterou chcete zobrazit.");
define("GAL_CHYBA_KAT_NEEX","Tato kategorie neexistuje.");

define("GAL_EDITUJ","Edituj");
define("GAL_SMAZ","Smaž");

define("GAL_PREHLED_ZADNA","Žádná galerie není vytvořena");
define("GAL_KAT_PREHLED_ZADNA","V žádné kategorii není obrázek.");
define("GAL_ADMINISTRACE","Administrace");

/* Konec jazykoveho koutku */
?>