<div class="nadpis">
 <*galerie_ukaz_title*>
</div>

<div class="gal_ukaz_popis">
 <*galerie_ukaz_description*>
</div>

<div class="gal_ukaz_vlastnik">
 <*galerie_ukaz_user*>
</div>

<form method="get" action="gallery.php" class="gal_ukaz_form">
 <input type="hidden" class="tl" name="akce" value="galerie_ukaz">
 <input type="hidden" class="tl" name="galerie_id" value="<*galerie_ukaz_id*>">
 <input type="submit" class="tl" value="Zobrazit">
 <input value="<*gal_ukaz_pocet*>" type="text" size="2" name="gal_ukaz_pocet" class="gal_input"> obrázků podle
 <select name="gal_ukaz_order" size="1" class="gal_input">
   <option value="date_up" <*date_up*>>datumu přidání - nejnovější</option>
   <option value="date_down" <*date_down*>>datumu přidání - nejstarší</option>
   <option value="view_up" <*view_up*>>počtu zobrazení - nejvíce</option>
   <option value="view_down" <*view_down*>>počtu zobrazení - nejméně</option>
   <option value="hodn_up" <*hodn_up*>>hodnocení - nejlepší</option>
   <option value="hodn_down" <*hodn_down*>>hodnocení - nejhorší</option>
   </select>
</form>   
 <*ukaz_obrazky*>  
<*strankovani*>   



