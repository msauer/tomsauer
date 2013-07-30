<form action="gallery.php" method="post">
<*chyba*>

<div class="gal_text">
Titulek obrázku<br />
  <div class="gal_formular">
   <input class="textpole" type="text" size="40" name="media_caption" value="<*media_caption*>">
  </div>
 <br />Popis obrázku<br />
  <div class="gal_formular">
   <textarea class="textbox" rows="5" cols="40" name="media_description" ><*media_description*></textarea>
  </div>
 <br />Kategorie<br />
  <div class="gal_formular">
   <*kategorie*>
  </div>  
 <br />Náhled obrázku<br />
  <div class="gal_formular">
   <div class="gal_obr"><img width="<*media_thumbnail_width*>" height="<*media_thumbnail_height*>" src="<*media_thumbnail*>" alt="<*media_description*>" title="<*media_description*>"></div>
  </div> 

  <hr class="gal_cara">
  <br />Stiskněte tlačítko a počkejte :-)<br />
  <div class="gal_formular">
<input type="hidden" name="do_it" value="1">
<input type="hidden" name="akce" value="obrazek_uprav">
<input type="hidden" name="media_id" value="<*media_id*>">
<input type="submit" class="tl" value="Uprav obrázek">
  </div>
</div>


</form>

