<?
#####################################################################
# phpRS Admin dictionary (Admin slovnik) - modul: "config" - version 1.0.0
#####################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.
// preklad: Michal [zvalo] Zvalený (michal@zvaleny.sk); http://www.zvaleny.sk

// rozcestnik
define('RS_CFG_ROZ_SPRAVA_CFG','Konfigurácia systému');
// hlavni fce - konfigurace
define('RS_CFG_KO_NADPIS_KONFIGURACE','Konfigurácia základného nastavenia phpRS systému');
define('RS_CFG_KO_NADPIS_SABLONY','Správa globálnych a článkových šablón');
define('RS_CFG_KO_NADPIS_PLUGINY','Správa plug-inov');
define('RS_CFG_KO_NADPIS_MODULY','Správa modulov');
define('RS_CFG_KO_ZPET','Späť na hlavnú stránku sekcie');
define('RS_CFG_KO_PROMENNA','Premenná');
define('RS_CFG_KO_HODNOTA','Hodnota');
define('RS_CFG_KO_KOLIK','koľko');
define('RS_CFG_KO_VOLBA_GLOB_SAB','Globálna šablóna');
define('RS_CFG_KO_VOLBA_POCET_CLA','Počet článkov zobrazených<br />na hlavnej stránke');
define('RS_CFG_KO_VOLBA_ANKETA','Aktívna anketa');
define('RS_CFG_KO_VOLBA_PLATNOST_CLA','Strážiť platnosť článkov<br />na hlavnej stránke');
define('RS_CFG_KO_VOLBA_NOVINKY','Zobraziť novinky');
define('RS_CFG_KO_VOLBA_STRANKOVANI','Povoliť stránkovanie<br />na hlavnej stránke');
define('RS_CFG_KO_TL_ULOZ_NASTAV','Ulož nastavenia');
define('RS_CFG_KO_OK_EDIT_CFG','Akcia prebehla úspešne! Konfigurácia bola aktualizovaná.');
// hlavni fce - sprava plug-inu
define('RS_CFG_SP_ZPET_PLUGINY','Späť na prehľad plug-inov');
define('RS_CFG_SP_NAZEV','Názov plug-inu');
define('RS_CFG_SP_PRAVA','Prístupové<br />práva');
define('RS_CFG_SP_MENU','Aktívne menu');
define('RS_CFG_SP_SYS_BLOK','Aktívny systémový blok');
define('RS_CFG_SP_AKCE','Akcia');
define('RS_CFG_SP_ZADNY_PLUGIN','V súčasnej chvíli nie je pripojený žiadny plug-in!');
define('RS_CFG_SP_PRAVA_NASTAVENI','podľa nastavení');
define('RS_CFG_SP_PRAVA_VSICHNI','všetci užívatelia');
define('RS_CFG_SP_PRAVA_ADMIN','len admin');
define('RS_CFG_SP_SMAZ','Zmaž');
define('RS_CFG_SP_CESTA','Cesta k novému plug-inu');
define('RS_CFG_SP_CESTA_INFO','... Cestu zadajte ako relatívnu adresu (napr.: plugin/nejkomentare/plugin.php).');
define('RS_CFG_SP_OK_ADD_PLUGIN','Akcia prebehla úspešne! Do systému bol pridaný nový plug-in.');
define('RS_CFG_SP_OK_DEL_PLUGIN','Akcia prebehla úspešne! Požadovaný plug-in bol odstránený.');
define('RS_CFG_SP_ERR_REGISTR_TAB','Pridanie plug-inu do registračnej tabuľky sa nepodarilo.');
define('RS_CFG_SP_ERR_TAB_PRISTUP_PRAV','Pridanie plug-inu do tabuľky s prístupovými právami sa nepodarilo.');
define('RS_CFG_SP_ERR_IMPORT','Pri pokuse o import zadaného plug-inu došlo k chybe. Overte si správnosť zadanej cesty a kompatibilitu plug-inu s vašou verziou phpRS!');
define('RS_CFG_SP_WAR_CHYBA_INTEGRITY','Varovanie: Bola zistená chyba integrity! Systém už obsahuje plug-in (modul) so zhodným indentifikačným označením.');
// hlavni fce - sprava sablon
define('RS_CFG_SS_ZPET_SABLONY','Späť na Správu globálnych a článkových šablón');
define('RS_CFG_SS_NADPIS_GLOBAL_SAB','Prehľad globálnych šablón');
define('RS_CFG_SS_NADPIS_CAL_SAB','Prehľad článkových šablón');
define('RS_CFG_SS_NADPIS_NOVE_SAB','Vyhľadávanie nových šablón');
define('RS_CFG_SS_NADPIS_NALEZENE_SAB','Prehľad nájdených šablón');
define('RS_CFG_SS_NADPIS_NAZEV_CLA_SAB','Názov článkovej šablóny');
define('RS_CFG_SS_NAZEV_SAB','Názov šablóny');
define('RS_CFG_SS_TYP_SAB','Typ šablóny');
define('RS_CFG_SS_UMISTENI_SAB','Umiestnenie šablóny');
define('RS_CFG_SS_CESTA_SAB','Cesta k šablóne');
define('RS_CFG_SS_PRACE_SE_SAB','Práca so šablónou');
define('RS_CFG_SS_CESTA_INSTAL_SB','Cesta k inštalačnému súboru');
define('RS_CFG_SS_INSTALOVAT','inštalovať');
define('RS_CFG_SS_AKCE','Akcia');
define('RS_CFG_SS_SMAZ','Zmaž');
define('RS_CFG_SS_PRIRADIT_SAB','Priradiť šablónu');
define('RS_CFG_SS_ZADNA_GLOB_SAB','Aktuálne systém neobsahuje žiadnu globálnu šablónu!<br />Tento stav znemožňuje funkčnosť čitateľského modulu.');
define('RS_CFG_SS_ZADNA_CLA_SAB','Aktuálne systém neobsahuje žiadnu článkovú šablónu!<br />Tento stav znemožňuje funkčnosť čitateľského rozhrania.');
define('RS_CFG_SS_ZADNA_SABLONA','Systém nenašiel žiadnu šablónu!');
define('RS_CFG_SS_ZADNA_RUBRIKA','Zatiaľ nebola nadefinovaná žiadna rubrika!');
define('RS_CFG_SS_CESTA_SAB_ADR','Cesta k prehľadávanému adresáru:');
define('RS_CFG_SS_CESTA_SAB_ADR_INFO','Cestu zadajte ako relatívnu adresu (napr.: image).<br />Zadaný adresár (vrátane všetkých podadresárov) bude testovaný na prítomnosť globálnych a článkových šablón.');
define('RS_CFG_SS_VYBRANA_CLA_SAB','Vybranú článkovú šablónu chcem');
define('RS_CFG_SS_PRIRADIT_VSEM','Priradiť všetkým článkom.');
define('RS_CFG_SS_PRIRADIT_PODMINCE','Priradiť len článkom, ktoré odpovedajú nasledujúcej podmienke:');
define('RS_CFG_SS_VZTAH_INFO','(Vzťah medzi jednotlivými podmienkami má logickú hodnotu ALEBO)');
define('RS_CFG_SS_PODMINKA_TEMA','Téma / témy:');
define('RS_CFG_SS_PODMINKA_AUTOR','Autor / autori:');
define('RS_CFG_SS_PODMINA_CLA_SAB','Článková / článkové šab.:');
define('RS_CFG_SS_TL_HLEDAT','Hľadať');
define('RS_CFG_SS_TL_NAINSTALUJ','Nainštaluj všetky označené šablóny');
define('RS_CFG_SS_TL_NASTAVIT','Nastaviť');
define('RS_CFG_SS_OK_ADD_SAB','Inštalácia globálnych a článkových šablón prebehla v poriadku!');
define('RS_CFG_SS_OK_DEL_GLOB_SAB','Akcia prebehla úspešne! Vybraná globálna šablóna bola odstránená.');
define('RS_CFG_SS_OK_DEL_CLA_SAB','Akcia prebehla úspešne! Vybraná článková šablóna bola odstránená.');
define('RS_CFG_SS_OK_NASTAV_CLA_SAB','Vybraná článková šablóna bola priradená všetkým odpovedajúcim článkom.');
define('RS_CFG_SS_ERR_AKTIVNI_CLA_SAB','Pozor chyba! Požadovanú akciu nemožno vykonať, pretože vybraná článková šablóna je priradená k jednému alebo viac článkom.');
define('RS_CFG_SS_ERR_NEEXISTUJE_ADR','Pozor chyba! Systém nemôže nájsť vami zadaný adresár.');
define('RS_CFG_SS_ERR_CHYBI_INSTAL_SB','Chyba! Systém nemôže nájsť inštalačný súbor!');
define('RS_CFG_SS_ERR_GLOB_SAB_CHYBI_ATR','Chyba! Inštalovaná globálna šablóna neobsahuje všetky potrebné parametre!');
define('RS_CFG_SS_ERR_GLOB_SAB_SHODA_SAB_1','Upozornenie! Globálnu šablónu');
define('RS_CFG_SS_ERR_GLOB_SAB_SHODA_SAB_2','nemožno nainštalovať, pretože sa v systéme nachádza úplne zhodná šablóna!');
define('RS_CFG_SS_ERR_GLOB_SAB_NEOCEK_CHYBA_1','Chyba! V priebehu inštalácie globálnej šablóny');
define('RS_CFG_SS_ERR_GLOB_SAB_NEOCEK_CHYBA_2','došlo k neočakávanej chybe!');
define('RS_CFG_SS_ERR_CLA_SAB_CHYBI_ATR','Chyba! Inštalovaná článková šablóna neobsahuje všetky potrebné parametre!');
define('RS_CFG_SS_ERR_CLA_SAB_SHODA_SAB_1','Upozornenie! Článkovú šablónu');
define('RS_CFG_SS_ERR_CLA_SAB_SHODA_SAB_2','nemožno nainštalovať, pretože sa v systéme nachádza úplne zhodná šablóna!');
define('RS_CFG_SS_ERR_CLA_SAB_NEOCEK_CHYBA_1','Chyba! V priebehu inštalácie článkovej šablóny');
define('RS_CFG_SS_ERR_CLA_SAB_NEOCEK_CHYBA_2','došlo k neočakávanej chybe!');
// hlavni fce - sprava modulu
define('RS_CFG_SM_ZPET_MODULY','Späť na Správu modulov');
define('RS_CFG_SM_NAZEV_MODULU','Názov modulu');
define('RS_CFG_SM_NAZEV_MENU','Názov menu');
define('RS_CFG_SM_PRAVA','Prístupové<br />práva');
define('RS_CFG_SM_TYP','Typ modulu');
define('RS_CFG_SM_STAV','Stav modulu');
define('RS_CFG_SM_PORADI','Poradie');
define('RS_CFG_SM_AKCE','Akcia');
define('RS_CFG_SM_JEN_ADMIN','len admin');
define('RS_CFG_SM_VSICHNI','všetci užívatelia');
define('RS_CFG_SM_DLE_NASTAV','podľa nastavenia');
define('RS_CFG_SM_ZAKLADNI_MODUL','základný modul');
define('RS_CFG_SM_NASTAV_MODUL','nastaviteľný modul');
define('RS_CFG_SM_AKTIVOVAT','Aktivovať');
define('RS_CFG_SM_BLOKOVAT','Blokovať');
define('RS_CFG_SM_UPRAVIT','Edituj');
define('RS_CFG_SM_FORM_TITULEK','Titulok v menu');
define('RS_CFG_SM_FORM_RGB','RGB farba pozadia');
define('RS_CFG_SM_FORM_RGB_INFO','prázdné pole = defaultná farba');
define('RS_CFG_SM_TL_ULOZ_NASTAV','Uložiť zmeny nastavenia');
define('RS_CFG_SM_OK_STAV_MODULU','Akcia prebehla úspešne! Nastavenie modulu bolo aktualizované.');
define('RS_CFG_SM_OK_PORADI_MODULU','Aktualizácia nastavení modulov prebehla úspešne.');
define('RS_CFG_SM_OK_EDIT_MODULU','Akcia prebehla úspešne! Nastavenie modulu bolo aktualizované.');
define('RS_CFG_SM_ERR_CHYBI_ATR','Systém nemá k dispozícii všetky potrebné premenné!');
?>
