
<div class="gal_omezeni">
 Obrázek může mít nejvýše <*vyrazne*><*velikost_obrazek*>kB<*/vyrazne*> a
 maximální velikost galerie je <*vyrazne*><*velikost_galerie*> MB<*/vyrazne*>.<br>
 <*vyrazne*>Redakce si vyhrazuje právo<*/vyrazne*> v případě, že obrázek odporuje slušným mravům, nebo v případě porušení zákonů České Republiky
 <*vyrazne*>obrázek bez upozornění smazat<*/vyrazne*>.

<hr class="gal_cara">

<form style="display: inline;" action="gallery.php?akce=obrazek_novy_manual" method="post">
Chci <input type="submit" value="přidat" class="tl"> přesně <input type="text" value="<*number*>" name="number" size="1" class="textpole"> obrázků.
</form>
<hr class="gal_cara">

</div>

<form enctype="multipart/form-data" action="gallery.php?akce=obrazek_novy_manual" method="post">
<input type="hidden" name="do_it" value="1">
<*vyrazne*><*chyba*><*/vyrazne*>


<div class="gal_text">
 Nejprve <span class="gal_tucne">vyberte galerii</span>, do které chcete obrázky přidat:<br />
  <div class="gal_formular">
   <*VypisGalerie*>
  </div><br />
 <hr class="gal_cara">


<*pridej_obrazky*> 
 
 
 
   <br />Stiskněte tlačítko a počkejte :-)<br />
  <div class="gal_formular">
<input type="submit" class="tl" value="Přidej obrázky">
  </div>
</div>


</form>
