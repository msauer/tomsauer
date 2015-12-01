<?php
$file = "feed/feed.xml";
$fh = fopen($file, 'w');

//connection
define("DB_HOST","localhost");
define("DB_NAME","cyklosauer_cz");
define("DB_USER","cyklosauer.cz");
define("DB_PWD","dreauelt");
require("WEB-INF/db/db_mysql.inc");
$db = new ps_DB;
require_once("WEB-INF/modules/product/lib/ps_product.inc");
$ps_product = new ps_product;
require_once("WEB-INF/modules/product/lib/ps_product.inc");
$ps_product = new ps_product;
require_once("WEB-INF/modules/product/lib/ps_product_category.inc");
$ps_product_category = new ps_product_category;
require_once("WEB-INF/modules/product/lib/ps_product_attribute.inc");
$ps_product_attribute = new ps_product_attribute;

//query
$q  = "SELECT * FROM product";
$db->query($q);
$db->next_record();

//write
$head = "<?xml version=\"1.0\" encoding=\"utf-8\"?><SHOP xmlns=\"http://www.zbozi.cz/ns/offer/1.0\">";
$footer = "</SHOP>";

fputs($fh, "$head\n");
while ($db->next_record()) {
    fputs($fh, "<SHOPITEM>\n");

    $s = $db->get("product_name");
    $towrite .= $s . '<br>';
    fputs($fh, "<PRODUCTNAME>$s</PRODUCTNAME>\n");
    fputs($fh, "<DESCRIPTION>". strip_tags(html_entity_decode($db->get("product_s_desc"), ENT_NOQUOTES , "UTF-8"))."</DESCRIPTION>\n");
    $url = "<URL>http://www.cyklosauer.cz/shop/?page=shop/flypage&amp;product_id=". $db->get("product_id") ."&amp;</URL>\n";
    fputs($fh, $url);
    fputs($fh, "<ITEM_TYPE>new</ITEM_TYPE>\n");
    fputs($fh, "<DELIVERY_DATE>0</DELIVERY_DATE>\n");
    fputs($fh, "<IMGURL>http://www.cyklosauer.cz/shop/images/shop/product/". $db->get("product_thumb_image") . "</IMGURL>\n");
    $price = $ps_product->get_price($db->get("product_id"));
    $form_price = number_format($price["product_price"],0,',','');
    fputs($fh, "<PRICE_VAT>". $form_price ."</PRICE_VAT>\n");

    fputs($fh, "<CATEGORYTEXT>Sport | Jízdní kola</CATEGORYTEXT>\n");
    fputs($fh, "\n");

    fputs($fh, "</SHOPITEM>\n");
}


echo $towrite;
fputs($fh, "\n$footer\n");
fclose($fh);
?>
