
<center><*vyrazne*><*chyba*><*/vyrazne*></center>
<div class="gal_omezeni">

 Obrázek může mít nejvýše <*vyrazne*><*velikost_obrazek*> kB<*/vyrazne*> a
 maximální velikost galerie je <*vyrazne*><*velikost_galerie*> MB<*/vyrazne*>.<br>
 <*vyrazne*>Redakce si vyhrazuje právo<*/vyrazne*> v případě, že obrázek odporuje slušným mravům, nebo v případě porušení zákonů České Republiky
 <*vyrazne*>obrázek bez upozornění smazat<*/vyrazne*>.


<hr class="gal_cara">

<form action="gallery.php?akce=obrazek_novy_auto" method="post">
Chci <input type="submit" value="přidat" class="tl"> přesně <input type="text" value="<*number*>" name="number" size="1" class="textpole"> obrázků.
</form>
<hr class="gal_cara"> 

<form enctype="multipart/form-data" action="gallery.php" method="post">
<input type="hidden" name="akce" value="obrazek_novy_auto">
<input type="hidden" name="number" value="<*number*>">
<input type="hidden" name="do_it" value="1">

<div class="gal_text">
 <span class="gal_tucne">Vyberte galerii:</span><br />
  <div class="gal_formular">
   <*VypisGalerie*>
  </div><br />

  <span class="gal_tucne">Titulek obrázků:</span><br />
  <div class="gal_formular">  
    <input class="textpole" type="text" size="40" name="obrazek_titulek" value="<*obrazek_titulek*>">
  </div><br />
  
  <span class="gal_tucne">Popis obrázků:</span><br />
  <div class="gal_formular">  
   <textarea class="textbox" rows="2" cols="40" name="obrazek_popis"><*obrazek_popis*></textarea>
  </div><br />
  
  <span class="gal_tucne">Kategorie:</span><br />
  <div class="gal_formular">  
    <*VypisKategorie*>
  </div><br/>

  <span class="gal_tucne" style="cursor: help;" title="Pokud nevíte co to znamená, nechte přednastavenou hodnotu!">Kde začít číslování:</span><br />
  <div class="gal_formular">  
    <input class="textpole" type="text" size="1" name="obrazek_cislovani" value="<*obrazek_cislovani*>">
  </div>
    
 <hr class="gal_cara">

<*pridej_obrazky*>

 
 
 
   <br /><span class="gal_tucne">Stiskněte pouze jednou a počkejte</span><br />
  <div class="gal_formular">
<input type="submit" class="tl" value="Přidej obrázky">
  </div>
</div>


</form>
