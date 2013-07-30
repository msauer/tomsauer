<form action="gallery.php?akce=galerie_nova" method="post">

<div class="gal_omezeni">
 Můžete založit celkem <*vyrazne*><*galerie_pocet*><*/vyrazne*> galerií,
 každá galerie může mít maximálně <*vyrazne*><*galerie_velikost*> MB<*/vyrazne*>.
 <br />
 <*vyrazne*>Redakce si vyhrazuje právo<*/vyrazne*> v případě, že obsah galerie odporuje slušným mravům,
 nebo v případě porušení zákonů České Republiky <*vyrazne*>galerii bez upozornění smazat<*/vyrazne*>.
</div>

<hr class="gal_cara">

<*vyrazne*><*chyba*><*/vyrazne*>

<div class="gal_text">
Titulek galerie<br />
  <div class="gal_formular">
   <input class="textpole" type="text" size="40" name="galerie_titulek" value="<*galerie_titulek*>">
  </div>
 <br />Popis galerie<br />
  <div class="gal_formular">
   <textarea class="textbox" rows="5" cols="40" name="galerie_popis" ><*galerie_popis*></textarea>
  </div>
 <br />Jaký obrázek zobrazit v přehledu galerií?<br />
  <div class="gal_formular">
   <input type="radio" name="galerie_obrazek_jaky" value="3" checked>Náhodný<br />
   <input type="radio" name="galerie_obrazek_jaky" value="1">Nejlépe hodnocený<br />
   <input type="radio" name="galerie_obrazek_jaky" value="2">Nejzobrazovanější
  </div> 
 <br />Mohou do galerie přidávat obrázky i ostatní?<br />
  <div class="gal_formular">
   <input type="radio" name="galerie_verejna" checked value="0">Ne<br />
   <input type="radio" name="galerie_verejna" value="1">Ano
  </div>    
  <br />Stiskněte tlačítko a počkejte :-)<br />
  <div class="gal_formular">
<input type="hidden" name="do_it" value="1">  
<input type="submit" class="tl" value="Přidej galerii">
  </div>
</div>


</form>