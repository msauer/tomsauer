<*chyba*>
<div class="gal_omezeni">
 Obrázek může mít nejvýše <*vyrazne*><*velikost_obrazek*> kB<*/vyrazne*> a
 maximální velikost galerie je <*vyrazne*><*velikost_galerie*> MB<*/vyrazne*>.<br>
 <*vyrazne*>Redakce si vyhrazuje právo<*/vyrazne*> v případě, že obrázek odporuje slušným mravům, nebo v případě porušení zákonů České Republiky
 <*vyrazne*>obrázek bez upozornění smazat<*/vyrazne*>.

 <hr class="gal_cara">
 Jak funguje FTP přidávání?<br />
 Do jednoho adresáře na webu (např) "upload" nahrajete obrázky, které odpovídají velikosti a chete je přidat do jedné
 galerie. Poté určíte cestu k tomuto adresáři a obrázky se Vám automaticky přidají do galerie a smažou se z daného adresáře.
</div>

<hr class="gal_cara">
<*vyrazne*><*chyba*><*/vyrazne*><br />


<form enctype="multipart/form-data" action="gallery.php" method="post">
<input type="hidden" name="akce" value="obrazek_novy_ftp">
<input type="hidden" name="do_it" value="1">

<div class="gal_text">
 Nejprve <span class="gal_tucne">vyberte galerii</span>, do které chcete obrázky přidat:<br />
  <div class="gal_formular">
   <*VypisGalerie*>
  </div><br />

  Adresář, ve kterém jsou obrázky na webu (relativně od index.php, např pouze "upload")<br />
  <div class="gal_formular">  
    <input class="textpole" type="text" size="40" name="obrazek_adresar" value="<*obrazek_adresar*>">
  </div><br />

  Titulek obrázků<br />
  <div class="gal_formular">  
    <input class="textpole" type="text" size="40" name="obrazek_titulek" value="<*obrazek_titulek*>">
  </div><br />  
  
  Popis obrázku<br />
  <div class="gal_formular">  
   <textarea class="textbox" rows="2" cols="40" name="obrazek_popis"><*obrazek_popis*></textarea>
  </div><br /> 
  
  Kategorie<br />
  <div class="gal_formular">  
    <*VypisKategorie*>
  </div><br />

  Smazat po uploadu obrázky?<br />
  <div class="gal_formular">  
   <input type="radio" name="obrazek_smazat" value="1" checked> Ano<br />
   <input type="radio" name="obrazek_smazat" value="0"> Ne
  </div>    
  
    
 <hr class="gal_cara">

<*pridej_obrazky*>

 
 
 
   <br />Stiskněte tlačítko a počkejte :-)<br />
  <div class="gal_formular">
<input type="submit" class="tl" value="Přidej obrázky">
  </div>
</div>


</form>
