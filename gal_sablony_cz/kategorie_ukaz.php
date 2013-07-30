<div class="nadpis">
<*kategorie_ukaz_title*>
</div>

<div class="gal_popis">
<*kategorie_ukaz_popis*>
</div>

<form method="get" action="gallery.php" class="gal_ukaz_form">
 <input type="hidden" class="tl" name="akce" value="kategorie_ukaz">
 <input type="hidden" class="tl" name="kat_id" value="<*kat_id*>">
 <input type="submit" class="tl" value="Zobrazit">
 <input value="<*kat_ukaz_pocet*>" type="text" size="2" name="kat_ukaz_pocet" class="gal_input"> obrázků podle
 <select name="kat_ukaz_order" size="1" class="gal_input">
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