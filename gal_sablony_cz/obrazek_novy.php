<form enctype="multipart/form-data" action="gallery.php" method="post">
<center><*vyrazne*><*chyba*><*/vyrazne*></center>
<div class="gal_omezeni">
 Obrázek může mít nejvýše <span class="gal_vyrazne"><*velikost_obrazek*> kB</span> a
 maximální velikost galerie je <span class="gal_vyrazne"><*velikost_galerie*> MB</span>.<br>
 <span class="gal_vyrazne">Redakce si vyhrazuje právo</span> v případě, že obrázek odporuje slušným mravům, nebo v případě porušení zákonů České Republiky
 <span class="gal_vyrazne">obrázek bez upozornění smazat</span>.
</div>

<hr class="gal_cara">
<div class="gal_text">
 <span class="gal_tucne">Vyberte galerii</span>:<br />
  <div class="gal_formular">
   <*VypisGalerie*>
  </div>
 <br /><span class="gal_tucne">Cesta k obrázku:</span><br />
  <div class="gal_formular">
   <input class="textpole" type="file" size="40" name="obrazek_url">
  </div>
 <br /><span class="gal_tucne">Titulek obrázku:</span><br />
  <div class="gal_formular">
   <input class="textpole" type="text" size="40" name="obrazek_titulek" value="<*obrazek_titulek*>">
  </div>
 <br /><span class="gal_tucne">Popis obrázku:</span><br />
  <div class="gal_formular">
   <textarea class="textbox" rows="5" cols="40" name="obrazek_popis" ><*obrazek_popis*></textarea>
  </div>
 <br /><span class="gal_tucne">Kategorie:</span><br />
  <div class="gal_formular">
   <*VypisKategorie*>
  </div>
  <br /><span class="gal_tucne">Stiskněte pouze jednou a počkejte</span><br />
  <div class="gal_formular">
<input type="hidden" name="do_it" value="1"> 
<input type="hidden" name="akce" value="obrazek_novy"> 
<input type="submit" class="tl" value="Přidej obrázek">
  </div>
</div>
</form>