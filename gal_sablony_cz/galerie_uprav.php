<form action="gallery.php?akce=galerie_uprav" method="post">

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
   <input type="radio" name="galerie_obrazek_ukaz" value="3" <*3*>>Náhodný<br />
   <input type="radio" name="galerie_obrazek_ukaz" value="1" <*1*>>Nejlépe hodnocený<br />
   <input type="radio" name="galerie_obrazek_ukaz" value="2" <*2*>>Nejzobrazovanější
  </div>  
 <br />Mohou do galerie přidávat ostatní uživatelé?<br />
  <div class="gal_formular">
   <input type="radio" name="galerie_verejna" value="1" <*verejna1*>>Ano<br />
   <input type="radio" name="galerie_verejna" value="0" <*verejna0*>>Ne<br />
  </div>    

<br />Založení galerie<br />
  <div class="gal_formular">
   <input class="textpole" type="text" size="22" disabled value="<*galerie_zalozeni*>">
  </div>
  
<br />Poslední přidání obrázku<br />
  <div class="gal_formular">
   <input class="textpole" type="text" size="22" disabled value="<*galerie_uprava*>">
  </div>    
  
  <hr class="gal_cara">
  <br />Stiskněte tlačítko a počkejte :-)<br />
  <div class="gal_formular">
<input type="hidden" name="do_it" value="1">
<input type="hidden" name="galerie_id" value="<*galerie_id*>">
<input type="submit" class="tl" value="Uprav galerii">
  </div>
</div>


</form>