<?php

######################################################################
# phpRS authorization 1.3.2 for phpRS Gallery 0.99.500
######################################################################

// Copyright (c) 2001-2004 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_guard, rs_user, rs_vazby_prava

/*
   Typy uzivatelu:
   0 - autor
   1 - redaktor
   2 - admin
*/

$GLOBALS["jmenoovercookies"]='adminco'.$GLOBALS["rspredpona"]; // jmeno overovaci cookies

class RSAutor
{
var $StavSession,$IdUser,$IdSession,$UserType,$UserVydavatel;
var $SessionUser,$UserName,$Jmeno;
var $pole_podrizeni,$stav_podrizeni;
var $chyba;

 /*
   RSAutor()
   PrvniPrihlaseni($vstup_user = '',$vstup_pass = '') ... vystup: 0/1
   OvereniPrihlaseni($vstup_cookies = '')             ... vystup: 0/1
   Odhlaseni()
   OvereniTypuBool($vstup_uroven = 0)                 ... vystup: 0/1
   OvereniTypu($vstup_uroven = 0)
   OvereniPravBool($ident_modulu = '')                ... vystup: 0/1
   OvereniPrav($ident_modulu = '')
   MuzeVydavat()                                      ... vystup: 0/1
   JeAdmin()                                          ... vystup: 0/1
   SeznamDostupUser()                                 ... vystup: seznam pristupnych id user
   JePodrizeny()                                      ... vystup: 0/1
   ZjistiPath()
   Ukaz($co = "")                                     ... vystup: informace o uzivateli
   ZobrazChybu($idchyba = 0)                          ... vystup: chybove hlaseni
  */

 function RSAutor()
 {
 // stav autorizace
 $this->StavSession=0;
 // session
 $this->IdSession=0;
 $this->SessionUser='';
 // informace o uzivateli
 $this->IdUser=0;
 $this->UserName='';
 $this->Jmeno='';
 $this->UserType=0;
 $this->UserVydavatel=0;
 // inic. pole porzizenych
 $this->pole_podrizeni=array();
 $this->stav_podrizeni=0;
 // stav chyby
 $this->chyba=0;
 }

 function PrvniPrihlaseni($vstup_user = '',$vstup_pass = '')
 {
 if (empty($vstup_user)):
   $this->chyba=1;
   return 0; // prazdny user
 endif;

 if (empty($vstup_pass)):
   $this->chyba=2;
   return 0; // prazdne pass
 endif;

 // vstupni korekce
 $vstup_user=addslashes($vstup_user);
 $vstup_pass=addslashes($vstup_pass);

 $dotazuser=mysql_query("select idu,user,password,jmeno,admin from ".$GLOBALS["rspredpona"]."user where user='".$vstup_user."' and admin>=0",$GLOBALS["dbspojeni"]);
 if ($dotazuser!=0):
   $pocetuser=mysql_num_rows($dotazuser);
 else:
   $pocetuser=0;
 endif;
 // kontrola vysledku dotazu na user
 if ($pocetuser==0):
   $this->chyba=3;
   return 0;
 else:
   // inic. vysledkoveho pole
   $akt_pom_login_pole=array();
   $akt_pom_login_pole=mysql_fetch_assoc($dotazuser);
   // vse OK - uzivatel existuje
   if (MD5($vstup_pass)!=$akt_pom_login_pole['password']): // porovnani vstupu s heslem
     $this->chyba=4;
     return 0;
   else:
     // vse OK
     $this->IdUser=$akt_pom_login_pole['idu']; // cislo uzivatele
     $this->UserType=$akt_pom_login_pole['admin']; // typ uzivatele
     $this->UserName=$akt_pom_login_pole['user']; // username uzivatele
     $this->Jmeno=$akt_pom_login_pole['jmeno']; // jmeno uzivatele
     $this->SessionUser=MD5(Date("szY")."g".$vstup_user.$vstup_pass); // generovani session
     $aktualnicas=Date("Y-m-d H:i:s");

     // ulozeni session
     mysql_query("insert into ".$GLOBALS["rspredpona"]."guard(idg,password,kdo,cas) values(null,'".$this->SessionUser."','".$this->IdUser."','".$aktualnicas."')",$GLOBALS["dbspojeni"]);

     // dotaz na session
     $dotazsess=mysql_query("select idg from ".$GLOBALS["rspredpona"]."guard where password='".$this->SessionUser."' and kdo='".$this->IdUser."' and cas='".$aktualnicas."'",$GLOBALS["dbspojeni"]);
     $this->IdSession=mysql_result($dotazsess,0,"idg"); // id session

     // vlozeni do cookies a odeslani
     $adminco=base64_encode("phpRS:".$this->IdSession.":".$this->SessionUser.":".$this->IdUser);
     // test na nastaveni citlivosti cookies
     if ($GLOBALS["cookiessdomenou"]==1):
       // cookies - jmeno_cookies , obsah , platnost , path , domena
       setcookie($GLOBALS["jmenoovercookies"],$adminco,time()+$GLOBALS['rsconfig']['platnost_auth'],$this->ZjistiPath(),$_SERVER["HTTP_HOST"]);
     else:
       // cookies - jmeno_cookies , obsah , platnost
       setcookie($GLOBALS["jmenoovercookies"],$adminco,time()+$GLOBALS['rsconfig']['platnost_auth']);
     endif;

     $this->StavSession=1; // aktivni stav prihlaseni
     return 1;
   endif;
 endif;
 }

 function OvereniPrihlaseni($vstup_cookies = '')
 {
 if (empty($vstup_cookies)):
   $this->chyba=5;
   return 0; // chyba; neexistuje session
 endif;

 $dekoduj_vstup=base64_decode($vstup_cookies);
 $dekoduj_vstup=explode(":",$dekoduj_vstup);

 // dekompilace + vstupni korekce
 $this->IdSession=addslashes($dekoduj_vstup[1]);   // id session
 $this->SessionUser=addslashes($dekoduj_vstup[2]); // session
 $this->IdUser=addslashes($dekoduj_vstup[3]);      // cislo uzivatele

 // kontrola - porovnani exist. session s cookies
 $dotazkontrsess=mysql_query("select password from ".$GLOBALS["rspredpona"]."guard where idg='".$this->IdSession."' and password='".$this->SessionUser."' and kdo='".$this->IdUser."'",$GLOBALS["dbspojeni"]);
 if ($dotazkontrsess!=0):
   $pocetkontrsess=mysql_num_rows($dotazkontrsess);
 else:
   $pocetkontrsess=0;
 endif;

 if ($pocetkontrsess==0):
   // chyba; neplatna session
   $this->chyba=6;
   return 0;
 else:
   // vse OK
   $dotazprava=mysql_query("select user,jmeno,admin from ".$GLOBALS["rspredpona"]."user where idu='".$this->IdUser."'",$GLOBALS["dbspojeni"]);

   // inic. vysledkoveho pole
   $akt_pom_login_pole=array();
   $akt_pom_login_pole=mysql_fetch_assoc($dotazprava);

   $this->UserName=$akt_pom_login_pole['user']; // username uzivatele
   $this->Jmeno=$akt_pom_login_pole['jmeno']; // jmeno uzivatele
   $this->UserType=$akt_pom_login_pole['admin']; // typ uzivatele

   define("RSAUT_PRAVA",$this->UserType);
   define("RSAUT_IDUSER",$this->IdUser);
   define("RSAUT_VYDAVATEL",$this->UserVydavatel);

   $this->StavSession=1; // aktivni stav prihlaseni
   return 1;
 endif;
 }

 function Odhlaseni()
 {
 $this->StavSession=0;
 // odmazani session
 mysql_query("delete from ".$GLOBALS["rspredpona"]."guard where idg='".$this->IdSession."'",$GLOBALS["dbspojeni"]);
 // vytvoreni mazaciho cookies a odeslani
 /*
 $adminco=base64_encode("phpRS:::");
 setcookie($GLOBALS["jmenoovercookies"],$adminco,time()-3600); // jmeno_cookies , obsah , platnost
 */
 }

 function OvereniTypuBool($vstup_uroven = 0)
 {
 // uzivatele: 0 = autor, 1 = redaktor, 2 = admin
 if ($vstup_uroven>$this->UserType): // kdyz je pozadovana uroven vetsi, nez aktualni, tak uzivatel nesplnuje podminku
   return 0;
 else:
   return 1;
 endif;
 }

 function OvereniTypu($vstup_uroven = 0)
 {
 // uzivatele: 0 = autor, 1 = redaktor, 2 = admin
 if ($vstup_uroven>$this->UserType): // kdyz je pozadovana uroven vetsi, nez aktualni, tak uzivatel nesplnuje podminku
   echo "<p align=\"center\">Nemáte potřebná práva pro vstup do této sekce!</p></body></html>";
   exit;
 endif;
 }

 function OvereniPravBool($ident_modulu = '')
 {
 // test na platnost vstupu a aktivni session
 if (empty($ident_modulu)||$this->StavSession==0):
   // prazdny vstup
   return 0; // NE
 else:
   // test na typ uzivatele: 2 = admin
   if ($this->UserType==2):
     return 1; // OK
   else:
     $ident_modulu=addslashes($ident_modulu);
     // vyhledani prislusne sekce
     $dotazsek=mysql_query("select fks_prava_users,all_prava_users from ".$GLOBALS["rspredpona"]."moduly_prava where ident_modulu='".$ident_modulu."'",$GLOBALS["dbspojeni"]);
     $pocetsek=mysql_num_rows($dotazsek);
     if ($pocetsek>0):
       // zaznam existuje
       $akt_pole_nalez_prava=mysql_fetch_assoc($dotazsek); // nacteni ziskanych dat
       // test na globalni pristupnost modulu
       if ($akt_pole_nalez_prava['all_prava_users']==1):
         return 1; // OK
       else:
         $uzivatele=explode(":",$akt_pole_nalez_prava['fks_prava_users']); // prevod seznamu povolenych uzivatelu do pole
         $pocetuzivatelu=count($uzivatele);

         $aktivni=0; // defaultne pristup zamitnut

         for ($pom=0;$pom<$pocetuzivatelu;$pom++):
           if ($uzivatele[$pom]==$this->IdUser): $aktivni=1; break; endif; // pristupove pravo potvrzeno
         endfor;

         return $aktivni; // vysledek po vyhledavni shody
       endif;
     else:
       // zaznam neexistuje
       return 0; // NE
     endif;
   endif;
 endif;
 }

 function OvereniPrav($ident_modulu = '')
 {
 // test na platnost vstupu a aktivni session
 if (empty($ident_modulu)||$this->StavSession==0):
   // prazdny vstup
   echo "<p align=\"center\">Nelze ověřit přístupová práva!</p></body></html>"; // NE
   exit();
 else:
   // test na typ uzivatele: 0 = autor, 1 = redaktor, 2 = admin
   if ($this->UserType==0||$this->UserType==1):
     $ident_modulu=addslashes($ident_modulu);
     // vyhledani prislusne sekce
     $dotazsek=mysql_query("select fks_prava_users,all_prava_users from ".$GLOBALS["rspredpona"]."moduly_prava where ident_modulu='".$ident_modulu."'",$GLOBALS["dbspojeni"]);
     $pocetsek=mysql_num_rows($dotazsek);
     if ($pocetsek>0):
       // zaznam existuje
       $akt_pole_nalez_prava=mysql_fetch_assoc($dotazsek); // nacteni ziskanych dat
       // test na globalni pristupnost modulu: 0 = musi byt testovani kazdy uzivatel zvlast, 1 = globalni pristupny
       if ($akt_pole_nalez_prava['all_prava_users']==0):
         $uzivatele=explode(":",$akt_pole_nalez_prava['fks_prava_users']); // prevod seznamu povolenych uzivatelu do pole
         $pocetuzivatelu=count($uzivatele);

         $aktivni=0; // defaultne pristup zamitnut

         for ($pom=0;$pom<$pocetuzivatelu;$pom++):
           if ($uzivatele[$pom]==$this->IdUser): $aktivni=1; break; endif; // pristupove pravo potvrzeno
         endfor;

         // redakce na vysledek vyhledavni shody
         if ($aktivni==0):
           echo "<p align=\"center\">Nemáte potřebná práva pro vstup do této sekce!</p></body></html>";
           exit;
         endif;
       endif;
     else:
       // zaznam neexistuje
       echo "<p align=\"center\">Nelze ověřit přístupová práva!</p></body></html>"; // NE
       exit();
     endif;
   endif;
 endif;
 }

 function MuzeVydavat()
 {
 // test na aktivni stav prihlaseni
 if ($this->StavSession==1):
   if ($this->UserType==2): // admin
     return 1; // admin vzdy muze
   else:
     return $this->UserVydavatel; // aktualni nastaveni
   endif;
 else:
   return 0; // neexistuje session
 endif;
 }

 function JeAdmin()
 {
 // test na aktivni stav prihlaseni (1 = true) a typ uzivatele (2 = admin)
 if ($this->StavSession==1&&$this->UserType==2):
   return 1; // vysledek true
 else:
   return 0; // vysledek false
 endif;
 }

 function SeznamDostupUser()
 {
 $seznam='';

 if ($this->StavSession==1):
   $seznam.=$this->IdUser; // seznam obsahuje i ID nadrizeneho uzivatele
   $dotazpod=mysql_query("select fk_id_podrizeny from ".$GLOBALS["rspredpona"]."vazby_prava where fk_id_nadrizeny='".$this->IdUser."'",$GLOBALS["dbspojeni"]);
   $pocetpod=mysql_num_rows($dotazpod);
   for ($pom=0;$pom<$pocetpod;$pom++):
     $seznam.=','.mysql_Result($dotazpod,$pom,"fk_id_podrizeny");;
   endfor;
 else:
   $seznam='0'; // nic
 endif;

 return $seznam;
 }

 function JePodrizeny($id_test = 0)
 {
 if ($this->stav_podrizeni==0):
   // nutno nacist data
   if ($this->StavSession==1):
     $this->pole_podrizeni[]=$this->IdUser; // pole obsahuje i ID samotneho nadrizeneho uzivatele
     $dotazpod=mysql_query("select fk_id_podrizeny from ".$GLOBALS["rspredpona"]."vazby_prava where fk_id_nadrizeny='".$this->IdUser."'",$GLOBALS["dbspojeni"]);
     $pocetpod=mysql_num_rows($dotazpod);
     for ($pom=0;$pom<$pocetpod;$pom++):
       $this->pole_podrizeni[]=mysql_Result($dotazpod,$pom,"fk_id_podrizeny");;
     endfor;
     $this->stav_podrizeni=1;
   endif;
 endif;

 // test na podrizenost id user
 if (in_array($id_test,$this->pole_podrizeni)):
   return 1; // je podrizeny
 else:
   return 0; // neni podrizeny
 endif;
 }

 function ZjistiPath()
 {
 $skript=$_SERVER["PHP_SELF"];
 $casti=explode("/",$skript); // vzdy min. rozlozen na dve casti
 $pocetcasti=count($casti);

 if ($pocetcasti>2):
   $pocetcasti--; // musime odecist posledni, ktera obsahuje samotny skript
   $cesta="/";
   for ($pom=1;$pom<$pocetcasti;$pom++): // zaciname od 1, protoze 0 cast obsahuje prazdne pole, ktere vznikne separaci koren. adr.
     $cesta.=$casti[$pom]."/";
   endfor;
 else:
   // skript je v korenovem adresari nebo ma prazdnou cestu
   $cesta="/";
 endif;

 return $cesta;
 }

 function Ukaz($co = "")
 {
 if ($this->StavSession==1):
   // prihlaseni uzivatele je platne
   switch($co):
     case "id": return $this->IdUser; break;
     case "username": return $this->UserName; break;
     case "jmeno": return $this->Jmeno; break;
     case "idtyp": return $this->UserType; break;
     default: return "";
   endswitch;
 else:
   // chyba; uzivatel nema platnou autorizaci
   return "";
 endif;
 }

 function ZobrazChybu($idchyba = 0)
 {
 switch ($idchyba):
   case 0: $popis="Neznama chyba!"; break;
   case 1: $popis="Nezadali jste zadneho uzivatele."; break;
   case 2: $popis="Nezadali jste zadne heslo."; break;
   case 3: $popis="Neznamy uzivatel nebo uzivatel s nedostatecnymi pristupovymi pravy."; break;
   case 4: $popis="Spatne heslo."; break;
   case 5: $popis="Prihlasovaci session neexistuje."; break;
   case 6: $popis="Neplatna prihlasovaci session."; break;
   default: $popis="Neznama chyba ".$idchyba."."; break;
 endswitch;

 return $popis;
 }
}
$GalUzivatel = new  RSAutor();
$GalUzivatel->OvereniPrihlaseni($_COOKIE[$GLOBALS["jmenoovercookies"]])
?>