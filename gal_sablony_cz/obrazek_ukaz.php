<html>
 <head>
  <title><*picture_show_media_caption*></title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
<style>
body {
        font-family: "verdana","arial";
        font-size: 12px;
        text-align: center;
        margin: 0;
}
.informace {
        font-family: "verdana","arial";
        font-size: 12px;
        text-align: center;
        margin: auto;
        width: 480px;
        border: 1px dashed #000000;
}
A img {
 border: 0;
}
A {
        color: #000000;
}
A:link {
    color: #000000
}
A:visited {
    color: #000000
}
A:hover {
    color: #3333FF
}
A:active {
    color: #000000
}
.gal_nadpis {
        font-weight: bold;
        font-size: 17px;
        text-align: center;
        margin: 5px;
}
.gal_cara {
        border: 1px dashed #000000;
}
.gal_inline {
   display: inline;
}
.gal_tl {
    background-color: #808080;
    color: #FFFFFF;
    font-family: Verdana,Arial,Helvetica;
    font-size: 11px;
    font-weight: bold;
    text-align: center;
    border: 1px solid #000000;
    cursor: hand;
}
.gal_tlodkaz {
    background-color: #808080;
    color: #FFFFFF;
    font-family: Verdana,Arial,Helvetica;
    font-size: 11px;
    font-weight: bold;
    text-align: center;
    border: 1px solid #000000;
    cursor: hand;
    padding: 2px;
    text-decoration: none;
}
.gal_tlodkaz:link {
    color: #FFFFFF;
}
.gal_tlodkaz:visited {
    color: #FFFFFF;
}
.gal_tlodkaz:hover {
    color: #FFFFFF;
}
.gal_tlodkaz:active {
    color: #FFFFFF;
}
.komz {
    color: #000000;
    font-family: "verdana","arial";
    font-size: 11px;
    font-weight: normal
}
.komlink {
    color: #000000;
    font-family: "verdana","arial";
    font-size: 13px;
    font-weight: normal
}
.komhlav {
    color: #000000;
    padding: 4px;
    font-family: "verdana","arial";
    font-size: 11px;
    font-weight: normal
}
.komtext {
    color: #000000;
    background-color: #EEEEEE;
    padding: 3px;
    border: 1px solid #000000;
    font-family: "verdana","arial";
    font-size: 11px;
    font-weight: normal
}
.komtabdiv {
   border: 1px dashed #000000;
   width: 480px;
   padding: 5px;
   margin: auto;
}
.komtabdiv_hodn {
   border-top: 0px;
   border-bottom: 0px;
   border-right: 1px;
   border-left: 1px;
   border-style: dashed;
   border-color: #000000;
   width: 480px;
   padding: 5px;
   margin: auto;
}
.gal_obr_souv {
 border: 1px solid #000000;
 margin: 3px;
}
.obrazecek {
 border: 1px solid #000000;
 margin-left: auto;
 margin-right: auto; 
}
.neviditelne {
 display: none;
}
.klikni {
 cursor: pointer;
}
.textbox {
 border: 1px solid #000000;
 font-size: 12px;
}
.textpole {
 border: 1px solid #000000;
 font-size: 12px;
}
h1 {
 position: absolute;
 top: 3px;
 right: 10px;
 font-size: 13px;
}
.galerie {
 font-size: 13px;
 position: absolute;
 top: 3px;
 left: 10px;
}
.zahlavi {
 height: 35px;
 width: 100%;
 background: url("gal_sablony_cz/sipky/obrazek_ukaz_horni.gif");
 padding-top: 3px;
 text-align: center;
}
</style>

<script>
function zobrazSkryj(idecko){
el=document.getElementById(idecko).style; 
el.display=(el.display == 'block')?'none':'block';
}
</script>


 </head>
 <body>

 <h1><*picture_show_media_caption*></h1>
 
<div class="zahlavi">
 <div class="galerie">Galerie: <b><a href="gallery.php?akce=galerie_ukaz&amp;galerie_id=<*picture_show_media_gallery_id*>"><*picture_show_media_gallery_title*></a></b></div>
 <*obrazek_prvni*>
 <*obrazek_predchozi*>
 <*obrazek_aktpozice*> / <*obrazek_celkem*>
 <*obrazek_nasledujici*>
 <*obrazek_posledni*>
</div>




<img class="obrazecek" src="<*picture_show_media_file*>" width="<*picture_show_media_width*>" height="<*picture_show_media_height*>" alt="<*picture_show_media_description*>" title="<*picture_show_media_description*>">

<div><*picture_show_media_description*></div><br><br>

<div class="<*trida_neviditelne*>" id="ovladaci_panel">
 <div class="komtabdiv"><*pocet_zobrazeni*></div>
 <div class="komtabdiv_hodn" ><*hodnoceni*></div>
 <div class="komtabdiv"><*komentare*></div>
</div>

<div onclick="zobrazSkryj('ovladaci_panel')" class="klikni" >Zobrazit / Skrýt ovládací panel</div>



 </body>
</html>
