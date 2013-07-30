<?
#####################################################################
# phpRS Admin dictionary (Admin slovnik) - modul: "config" - version 1.0.0
#####################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// rozcestnik
define('RS_CFG_ROZ_SPRAVA_CFG','Konfigurace systému');
// hlavni fce - konfigurace
define('RS_CFG_KO_NADPIS_KONFIGURACE','Konfigurace základního nastavení phpRS systému');
define('RS_CFG_KO_NADPIS_SABLONY','Správa globálních a článkových šablon');
define('RS_CFG_KO_NADPIS_PLUGINY','Správa plug-inů');
define('RS_CFG_KO_NADPIS_MODULY','Správa modulů');
define('RS_CFG_KO_ZPET','Zpět na hlavní stránku sekce');
define('RS_CFG_KO_PROMENNA','Proměnná');
define('RS_CFG_KO_HODNOTA','Hodnota');
define('RS_CFG_KO_KOLIK','kolik');
define('RS_CFG_KO_VOLBA_GLOB_SAB','Globální šablona');
define('RS_CFG_KO_VOLBA_POCET_CLA','Počet článků zobrazených<br />na hlavní stránce');
define('RS_CFG_KO_VOLBA_ANKETA','Aktivní anketa');
define('RS_CFG_KO_VOLBA_PLATNOST_CLA','Hlídat platnost článků<br />na hlavní stránce');
define('RS_CFG_KO_VOLBA_NOVINKY','Zobrazit novinky');
define('RS_CFG_KO_VOLBA_STRANKOVANI','Povolit stránkování<br />na hlavní stránce');
define('RS_CFG_KO_TL_ULOZ_NASTAV','Ulož nastavení');
define('RS_CFG_KO_OK_EDIT_CFG','Akce proběhla úspěšně! Konfigurace byla aktualizována.');
// hlavni fce - sprava plug-inu
define('RS_CFG_SP_ZPET_PLUGINY','Zpět na přehled plug-inů');
define('RS_CFG_SP_NAZEV','Název plug-inu');
define('RS_CFG_SP_PRAVA','Přístupová<br />práva');
define('RS_CFG_SP_MENU','Aktivní menu');
define('RS_CFG_SP_SYS_BLOK','Aktivní systémový blok');
define('RS_CFG_SP_AKCE','Akce');
define('RS_CFG_SP_ZADNY_PLUGIN','V současné chvíli není připojen žádný plug-in!');
define('RS_CFG_SP_PRAVA_NASTAVENI','dle nastavení');
define('RS_CFG_SP_PRAVA_VSICHNI','všichni uživatelé');
define('RS_CFG_SP_PRAVA_ADMIN','pouze admin');
define('RS_CFG_SP_SMAZ','Smaž');
define('RS_CFG_SP_CESTA','Cesta k novému plug-inu');
define('RS_CFG_SP_CESTA_INFO','... Cestu zadejte jako relativní adresu (např.: plugin/nejkomentare/plugin.php).');
define('RS_CFG_SP_OK_ADD_PLUGIN','Akce proběhla úspěšně! Do systému byl přidán nový plug-in.');
define('RS_CFG_SP_OK_DEL_PLUGIN','Akce proběhla úspěšně! Požadovaný plug-in byl odstraněn.');
define('RS_CFG_SP_ERR_REGISTR_TAB','Přidání plug-inu do registrační tabulky se nepodařilo.');
define('RS_CFG_SP_ERR_TAB_PRISTUP_PRAV','Přidání plug-inu do tabulky s přístupovými právy se nepodařilo.');
define('RS_CFG_SP_ERR_IMPORT','Při pokusu o import zadaného plug-inu došlo k chybě. Ověřte si správnost zadané cesty a kompatibilitu plug-inu s vaší verzí phpRS!');
define('RS_CFG_SP_WAR_CHYBA_INTEGRITY','Warning: Byla zjištěna chyba integrity! Systém již obsahuje plug-in (modul) se shodným indentifikačním označením.');
// hlavni fce - sprava sablon
define('RS_CFG_SS_ZPET_SABLONY','Zpět na Správu globálních a článkových šablon');
define('RS_CFG_SS_NADPIS_GLOBAL_SAB','Přehled globálních šablon');
define('RS_CFG_SS_NADPIS_CAL_SAB','Přehled článkových šablon');
define('RS_CFG_SS_NADPIS_NOVE_SAB','Vyhledávání nových šablon');
define('RS_CFG_SS_NADPIS_NALEZENE_SAB','Přehled nalezených šablon');
define('RS_CFG_SS_NADPIS_NAZEV_CLA_SAB','Název článkové šablony');
define('RS_CFG_SS_NAZEV_SAB','Název šablony');
define('RS_CFG_SS_TYP_SAB','Typ šablony');
define('RS_CFG_SS_UMISTENI_SAB','Umístění šablony');
define('RS_CFG_SS_CESTA_SAB','Cesta k šabloně');
define('RS_CFG_SS_PRACE_SE_SAB','Práce se šablonou');
define('RS_CFG_SS_CESTA_INSTAL_SB','Cesta k instalačímu soboru');
define('RS_CFG_SS_INSTALOVAT','instalovat');
define('RS_CFG_SS_AKCE','Akce');
define('RS_CFG_SS_SMAZ','Smaž');
define('RS_CFG_SS_PRIRADIT_SAB','Přiřadit šablonu');
define('RS_CFG_SS_ZADNA_GLOB_SAB','Aktuálně systém neobsahuje žádnou globální šablonu!<br />Tento stav znemožňuje funkčnost čtenářského modulu.');
define('RS_CFG_SS_ZADNA_CLA_SAB','Aktuálně systém neobsahuje žádnou článkovou šablonu!<br />Tento stav znemožňuje funkčnost čtenářského rozhraní.');
define('RS_CFG_SS_ZADNA_SABLONA','Systém nenašel žádnou šablonu!');
define('RS_CFG_SS_ZADNA_RUBRIKA','Zatím nebyla nadefinován žádná rubrika!');
define('RS_CFG_SS_CESTA_SAB_ADR','Cesta k prohledávanému adresáři:');
define('RS_CFG_SS_CESTA_SAB_ADR_INFO','Cestu zadejte jako relativní adresu (např.: image).<br />Zadaný adresář (včetně všech podadresářů) bude testován na přítomnost globálních a článkových šablon.');
define('RS_CFG_SS_VYBRANA_CLA_SAB','Vybranou článkovou šablonu chci');
define('RS_CFG_SS_PRIRADIT_VSEM','Přiřadit všem článkům.');
define('RS_CFG_SS_PRIRADIT_PODMINCE','Přiřadit pouze článkům, které odpovídají následující podmínce:');
define('RS_CFG_SS_VZTAH_INFO','(Vztah mezi jednotlivými podmínkami má logickou hodnotu NEBO)');
define('RS_CFG_SS_PODMINKA_TEMA','Téma / témata:');
define('RS_CFG_SS_PODMINKA_AUTOR','Autor / autoři:');
define('RS_CFG_SS_PODMINA_CLA_SAB','Článková / článkové šab.:');
define('RS_CFG_SS_TL_HLEDAT','Hledat');
define('RS_CFG_SS_TL_NAINSTALUJ','Nainstaluj všechny označené šablony');
define('RS_CFG_SS_TL_NASTAVIT','Nastavit');
define('RS_CFG_SS_OK_ADD_SAB','Instalace globálních a článkových šablon proběhla v pořádku!');
define('RS_CFG_SS_OK_DEL_GLOB_SAB','Akce proběhla úspěšně! Vybraná globální šablona byla odstraněna.');
define('RS_CFG_SS_OK_DEL_CLA_SAB','Akce proběhla úspěšně! Vybraná článková šablona byla odstraněna.');
define('RS_CFG_SS_OK_NASTAV_CLA_SAB','Vybraná článková šablona byla přiřazena všem odpovídajícím článkům.');
define('RS_CFG_SS_ERR_AKTIVNI_CLA_SAB','Pozor chyba! Požadovanou akci nelze provést, jelikož vybraná článková šablona je přiřazena k jednomu nebo více článkům.');
define('RS_CFG_SS_ERR_NEEXISTUJE_ADR','Pozor chyba! Systém nemůže nalézt vámi zadaný adresář.');
define('RS_CFG_SS_ERR_CHYBI_INSTAL_SB','Chyba! Systém nemůže nalézt instalační soubor!');
define('RS_CFG_SS_ERR_GLOB_SAB_CHYBI_ATR','Chyba! Instalovaná globální šablona neobsahuje veškeré potřebné parametry!');
define('RS_CFG_SS_ERR_GLOB_SAB_SHODA_SAB_1','Upozornění! Globální šablonu');
define('RS_CFG_SS_ERR_GLOB_SAB_SHODA_SAB_2','nelze nainstalovat, jelikož se v systému nalézá úplně shodná šablona!');
define('RS_CFG_SS_ERR_GLOB_SAB_NEOCEK_CHYBA_1','Chyba! V průběhu instalace globální šablony');
define('RS_CFG_SS_ERR_GLOB_SAB_NEOCEK_CHYBA_2','došlo k neočekávané chybě!');
define('RS_CFG_SS_ERR_CLA_SAB_CHYBI_ATR','Chyba! Instalovaná článková šablona neobsahuje veškeré potřebné parametry!');
define('RS_CFG_SS_ERR_CLA_SAB_SHODA_SAB_1','Upozornění! Článkovou šablonu');
define('RS_CFG_SS_ERR_CLA_SAB_SHODA_SAB_2','nelze nainstalovat, jelikož se v systému nalézá úplně shodná šablona!');
define('RS_CFG_SS_ERR_CLA_SAB_NEOCEK_CHYBA_1','Chyba! V průběhu instalace článkové šablony');
define('RS_CFG_SS_ERR_CLA_SAB_NEOCEK_CHYBA_2','došlo k neočekávané chybě!');
// hlavni fce - sprava modulu
define('RS_CFG_SM_ZPET_MODULY','Zpět na Správu modulů');
define('RS_CFG_SM_NAZEV_MODULU','Název modulu');
define('RS_CFG_SM_NAZEV_MENU','Název menu');
define('RS_CFG_SM_PRAVA','Přístupová<br />práva');
define('RS_CFG_SM_TYP','Typ modulu');
define('RS_CFG_SM_STAV','Stav modulu');
define('RS_CFG_SM_PORADI','Pořadí');
define('RS_CFG_SM_AKCE','Akce');
define('RS_CFG_SM_JEN_ADMIN','jen admin');
define('RS_CFG_SM_VSICHNI','všichni uživatelé');
define('RS_CFG_SM_DLE_NASTAV','dle nastavení');
define('RS_CFG_SM_ZAKLADNI_MODUL','základní modul');
define('RS_CFG_SM_NASTAV_MODUL','nastavitelný modul');
define('RS_CFG_SM_AKTIVOVAT','Aktivovat');
define('RS_CFG_SM_BLOKOVAT','Blokovat');
define('RS_CFG_SM_UPRAVIT','Edituj');
define('RS_CFG_SM_FORM_TITULEK','Titulek v menu');
define('RS_CFG_SM_FORM_RGB','RGB barva pozadí');
define('RS_CFG_SM_FORM_RGB_INFO','prázdné pole = defaultní barva');
define('RS_CFG_SM_TL_ULOZ_NASTAV','Uložit změny nastavení');
define('RS_CFG_SM_OK_STAV_MODULU','Akce proběhla úspěšně! Nastavení modulu bylo aktualizováno.');
define('RS_CFG_SM_OK_PORADI_MODULU','Aktualizace nastavení modulů proběhla úspěšně.');
define('RS_CFG_SM_OK_EDIT_MODULU','Akce proběhla úspěšně! Nastavení modulu bylo aktualizováno.');
define('RS_CFG_SM_ERR_CHYBI_ATR','Systém nemá k dispozici veškeré potřebné proměnné!');
?>
