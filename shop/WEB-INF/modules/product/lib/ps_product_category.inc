<?php
/*
 * The ps_product_attribute class
 *
 * Copyright (c) Edikon Corporation.  All rights reserved.
 * Distributed under the phpShop Public License (pSPL) Version 1.0.
 *
 * $Id: ps_product_category.inc,v 1.1.1.1 2004/07/27 14:58:59 pablo Exp $
 *
****************************************************************************
*
* CLASS DESCRIPTION
*
* ps_product_category
*
* The class is
*
* propeties:
*
* methods:
*
*
*************************************************************************/
class ps_product_category {
  var $classname = "ps_user";

  /**************************************************************************
  ** name: validate_add()
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/
  function validate_add(&$d) {

     if (!$d["category_name"]) {
       $d["error"] = "ERROR:  You must enter a name for the category.";
       return False;
     }
   $db = new ps_DB;
   $cat_url = strtolower($d["category_name"]);
   $cat_url = str_replace(" ", "",$cat_url);
     if($d["parent_category_id"] != "0") {
       $q  = "SELECT category_url FROM category ";
       $q .= "WHERE category_id='" . $d["parent_category_id"] . "'";
       $db->query($q);
       $db->next_record();
       $cat_url = $db->f("category_url") . "_" . $cat_url;
     }
   $q  = "SELECT category_name FROM category ";
   $q .= "WHERE category_url='$cat_url'";
   $db->query($q);
     if ($db->next_record()) {
       $d["error"] = "ERROR: Category already exists ";
       return False;
     }
   $d["category_url"] = $cat_url;
   return True;
 }
  /**************************************************************************
  ** name: validate_delete()
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/
  function validate_delete(&$d) {

    $db = new ps_DB;

    if (!$d["category_id"]) {
      $d["error"] = "ERROR:  Please select a category to delete.";
      return False;
    }

    // Check for children
    $q  = "SELECT * from category_xref where category_parent_id='";
    $q .= $d["category_id"] . "'";
    $db->query($q);
    if ($db->next_record()) {
      $d["error"]  = "ERROR: This category has children. ";
      $d["error"] .= "Please delete children first.";
      return False;
    }

    // Check for products assigned to this category
    $q  = "SELECT * from product_category_xref where category_id='";
    $q .= $d["category_id"] . "'";
    $db->query($q);
    if ($db->next_record()) {
      $d["error"]  = "ERROR: This category has products. ";
      $d["error"] .= "Please delete the products first.";
      return False;
    }
    return True;
  }

  /**************************************************************************
  ** name: validate_update()
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/
  function validate_update(&$d) {

     if (!$d["category_name"]) {
       $d["error"] = "ERROR:  You must enter a name for the category.";
       return False;
     }
     elseif ($d["category_id"] == $d["category_parent_id"]) {
       $d["error"] = "ERROR:  Category parent cannot be same category.";
       return False;
     }

   $db = new ps_DB;
   $cat_url = strtolower($d["category_name"]);
   $cat_url = str_replace(" ", "",$cat_url);
     if($d["category_parent_id"] != "0") {
       $q  = "SELECT category_url FROM category ";
       $q .= "WHERE category_id='" . $d["category_parent_id"] . "'";
       $db->query($q);
       $db->next_record();
       $cat_url = $db->f("category_url") . "_" . $cat_url;
     }
   $q  = "SELECT category_id, category_name FROM category ";
   $q .= "WHERE category_url='$cat_url'";
   $db->query($q);
     if ($db->next_record() && $db->f("category_id") != $d["category_id"]) {
       $d["error"] = "ERROR: Category already exists ";
       return False;
     }
   $d["category_url"] = $cat_url;
     return True;
 }

  /**************************************************************************
  ** name: add()
  ** created by: pablo
  ** description: creates a new category record and a category_xref record
  ** with the appropriate parent and child ids
  ** parameters:
  ** returns:
  ***************************************************************************/
  function add(&$d) {
    global $ps_vendor_id;
    $hash_secret="PHPShopIsCool";
    $db = new ps_DB;
    $timestamp = time();

    if ($this->validate_add($d)) {
      $category_id = md5(uniqid($hash_secret));

      $q = "INSERT INTO category (category_id, vendor_id, category_name, category_url,";
      $q .= "category_publish, category_description, category_flypage, cdate, mdate) ";
      $q .= "VALUES ('";
      $q .= $category_id . "','";
      $q .= $ps_vendor_id . "','";
      $q .= $d["category_name"] . "','";
      $q .= $d["category_url"] . "','";
      if ($d["category_publish"] != "Y") {
	$d["category_publish"] = "N";
      }
      $q .= $d["category_publish"] . "','";
      $q .= $d["category_description"] . "','";
      $q .= $d["category_flypage"] . "','";
      $q .= $timestamp . "','";
      $q .= $timestamp . "')";
      $db->query($q);
      $db->next_record();

      $q = "INSERT INTO category_xref ";
      $q .= "(category_parent_id, category_child_id) ";
      $q .= "VALUES ('";
      $q .= $d["parent_category_id"] . "','";
      $q .= $category_id . "')";
      $db->query($q);
      $db->next_record();

      return $category_id;
    }
    else {
      return False;
    }

  }

  /**************************************************************************
  ** name: update()
  ** created by: pablo
  ** description: updates category information
  ** parameters:
  ** returns:
  ***************************************************************************/
  function update(&$d) {
    $db = new ps_DB;
	global $ps_vendor_id;
    $timestamp = time();

    if ($this->validate_update($d)) {
      $q = "UPDATE category SET ";
      //$q .= "order='" .$d["order"] . "',";
      $q .= "category_id='" . $d["category_id"];
      $q .= "',category_name='" . $d["category_name"];
      if ($d["category_publish"] != "Y") {
	     $d["category_publish"] = "N";
      }
      $q .= "',category_url='" . $d["category_url"];
      //$q .= "',order='" . $d["order"];
      $q .= "',category_publish='" . $d["category_publish"];
      $q .= "',category_description='" . $d["category_description"];
      $q .= "',category_flypage='" . $d["category_flypage"];
      $q .= "', mdate='$timestamp";
      $q .= "' WHERE category_id='" . $d["category_id"] . "' ";
      $q .= "AND vendor_id='$ps_vendor_id' ";
      $db->query($q);
      $db->next_record();

      /*
      ** Update category x-reference table with parent-child relationship
      */
      $q = "UPDATE category_xref SET ";
      $q .= "category_parent_id='" . $d["category_parent_id"];
      $q .= "' WHERE category_child_id='" . $d["category_id"] . "'";

      $db->query($q);
      $db->next_record();

      return True;
    }
    else {
      return False;
    }
  }

  /**************************************************************************
  ** name: delete()
  ** created by: pablo
  ** description: Should delete a category and and categories under it.
  ** parameters:
  ** returns:
  ***************************************************************************/
  function delete(&$d) {
    $db = new ps_DB;

    if (!$this->validate_delete($d)) {
      return False;
    }

    $q = "DELETE FROM category WHERE category_id='" . $d["category_id"] . "'";
    $db->query($q);

    $q  = "DELETE FROM category_xref ";
    $q .= "WHERE category_child_id='" . $d["category_id"] . "'";
    $db->query($q);

    return True;
  }


  /**************************************************************************
  ** name: traverse_tree_down()
  ** created by:
  ** description: It goes through the category table and establishes
  **              the category tree based on the parent-child relationships
  **              defnied in the category_xref table.
  **              This is VERY recursive...
  **
  ** parameters:
  ** returns:
  ***************************************************************************/
  function traverse_tree_down($class="",$category_id=0, $level=0) {
    static $ibg = 0;
    global $sess,$ps_vendor_id;
    $db = new ps_DB;
    $class = "maintext";

    $level++;

    $q = "SELECT * from category,category_xref ";
    $q .= "WHERE category_xref.category_parent_id='";
    $q .= $category_id . "' AND ";
    $q .= "category.category_id=category_xref.category_child_id ";
    $q .= "AND category.vendor_id = $ps_vendor_id ";
    $db->query($q);
    while ($db->next_record()) {
      $product_count = $this->product_count($db->f("category_child_id"));
      if ($ibg++ % 2)
          $bgcolor=SEARCH_COLOR_1;
      else
          $bgcolor=SEARCH_COLOR_2;
      echo "<TABLE CELLSPACING=0 CELLPADDING=4 BORDER=0 WIDTH=100%>";
      echo "<TR BGCOLOR=$bgcolor><TD CLASS=$class>";
      for ($i=0;$i<$level;$i++) {
        echo "&nbsp;&nbsp;&nbsp;";
      }
      echo "&raquo;($level)&nbsp;";
      $url  = SECUREURL . "?page=product/product_category_form";
      $url .= "&category_id=" . $db->f("category_child_id");
      $url .= "&category_parent_id=" . $db->f("category_parent_id");
      echo "<A class=\"bLink\" HREF=" . $sess->url($url) . ">";
      echo $db->f("category_name") . "</A>:";
      echo "&nbsp;&nbsp;" . $db->f("category_description");
      echo "</TD><TD CLASS=$class ALIGN=right>($product_count)<A class=\"bLink\" HREF=";
      $sess->purl(SECUREURL . "?page=product/product_list&category_id=" . $db->f("category_child_id"));
      echo ">Products</A>\n</TD></TR></TABLE>\n";
      $this->traverse_tree_down($class, $db->f("category_child_id"), $level);
    }
  }


  /**************************************************************************
  ** name: get_product_count()
  ** created by:
  ** description: Calculates and returns number of products in given category
  **
  ** parameters:
  ** returns:
  ***************************************************************************/

  function product_count($category_id) {
    global $ps_vendor_id;

    $db = new ps_DB;

    $count  = "SELECT count(*) as num_rows FROM product,product_category_xref, category WHERE ";
    $q  = "product.vendor_id = '$ps_vendor_id' ";
    $q .= "AND product_category_xref.category_id='$category_id' ";
    $q .= "AND category.category_id=product_category_xref.category_id ";
    $q .= "AND product.product_id=product_category_xref.product_id ";
    $q .= "AND product.product_parent_id='' ";
    $q .= "ORDER BY product_publish DESC,product_name ";
    $count .= $q;
    $db->query($count);
    $db->next_record();
    return $db->f("num_rows");
  }

  /**************************************************************************
  ** name: traverse_tree_up()
  ** created by:
  ** description: It goes through the category table and establishes
  **              the category tree based on the parent-child relationships
  **              defnied in the category_xref table.
  **              This is VERY recursive...
  ** parameters:
  ** returns:
  ***************************************************************************/
  function traverse_tree_up($category_id, $level=0) {
    global $ps_vendor_id;
    $db = new ps_DB;

    $level++;
    $q = "SELECT * from category, category_xref ";
    $q .= "WHERE category_xref.category_child_id=' ";
    $q .= "$category_id' AND ";
    $q .= "category.category_id=category_xref.category_parent_id ";
    $q .= "AND category.vendor_id = $ps_vendor_id ";
    $db->query($q);
    while ($db->next_record()) {
      if ($level == 1) {
	echo "<OPTION SELECTED VALUE=" . $db->f("category_child_id");
      }
      else {
	echo "<OPTION VALUE=" . $db->f("category_child_id");
      }
      echo ">" . $db->f("category_name") . "</OPTION>";

      $this->traverse_tree_up($db->f("category_parent_id"), $level);
    }
  }

  /***************************************************************************
  ** name: list_category()
  ** created by: pablo
  ** description: list all categories and has the category with the given
  **              product_id preselected.
  ** parameters:
  ** returns:
  ***************************************************************************/
  function list_category($product_id="",$category_id="") {
    $db = new ps_DB;
    $name = "category_id";

    echo "<SELECT NAME=$name>\n";

    if ($product_id and !$category_id) {
      $q = "SELECT * from product_category_xref WHERE product_id='$product_id'";
      $db->query($q);
      $db->next_record();
      if (!$db->f("category_id")) {
        echo "<OPTION VALUE=\"0\">Please Select</OPTION>\n";
      }
      $this->list_tree($db->f("category_id"));
    }
    elseif ($category_id) {
      $this->list_tree($category_id);
    }
    else {
      echo "<OPTION VALUE=\"0\">Please Select</OPTION>\n";
      $this->list_tree();
    }

    echo "</SELECT>\n";

    return True;
  }


  /***************************************************************************
  ** name: list_parents()
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/
  function list_parents($category_id) {
    $db = new ps_DB;

    echo "<SELECT NAME=$name>\n";
    echo "<OPTION VALUE=\"0\">Please Select</OPTION>\n";
    $this->traverse_tree_up($category_id);
    echo "</SELECT>\n";
  }


  /**************************************************************************
  ** name: print_child_list()
  ** created by: pablo
  ** description: creates a bulleted of the childen of this category if they
  **              exist
  ** parameters: category_id
  ** returns:
  ***************************************************************************/
  function print_child_list($category_id) {
    global $sess,$ps_vendor_id;
    global $modulename;
    $db = new ps_DB;

    $q = "SELECT * FROM category,category_xref ";
    $q .= "WHERE category_xref.category_parent_id='$category_id' ";
    $q .= "AND category.category_id=category_xref.category_child_id ";
    $q .= "AND category.vendor_id='$ps_vendor_id' ";
    $q .= "AND category.category_publish='Y'";
    $db->query($q);
    while($db->next_record()) {
      echo "&nbsp;&nbsp;&nbsp;&raquo; <A HREF=";
      $sess->purl(SECUREURL . "?page=shop/browse&category_id=" . $db->f("category_id"));
      echo ">";
      $db->p("category_name");
      echo "</A><BR>";
    }


  }


  function print_subcategory($category_id) {
    global $sess,$ps_vendor_id;
    global $modulename;
    $db = new ps_DB;

    $q = "SELECT * FROM category,category_xref ";
    $q .= "WHERE category_xref.category_parent_id='$category_id' ";
    $q .= "AND category.category_id=category_xref.category_child_id ";
    $q .= "AND category.vendor_id='$ps_vendor_id' ";
    $q .= "AND category.category_publish='Y'";
    $db->query($q);
    while($db->next_record()) {
      echo "<TR BGCOLOR=\"#FFFFFF\">";
      echo "<TD>";
      echo "<A HREF=";
      $sess->purl(SECUREURL . "?page=shop/browse&category_id=" .
                                $db->f("category_child_id"));
      echo ">";
      echo "<FONT FACE=\"Verdana, Helvetica, Arial\" ";
      echo "SIZE=\"-1\">";
      $db->p("category_name");
      echo "</FONT></A>";
      echo "</TD></TR>";
    }


  }



  /**************************************************************************
  ** name: list_all()
  ** created by:
  ** description:
  ** parameters:
  ** returns:
  ***************************************************************************/
  function list_all($name, $category_id) {

    $db = new ps_DB;

    $q  = "SELECT * from category_xref ";
    $q .= "WHERE category_child_id='$category_id'";
    $db->query($q);
    $db->next_record();
    $category_id=$db->f("category_parent_id");

    echo "<SELECT NAME=$name>\n";
    echo "<OPTION VALUE=\"0\">Default-Top Level</OPTION>\n";
    $this->list_tree($category_id);
    echo "</SELECT>\n";
  }


  /**************************************************************************
  ** name: list_tree()
  ** created by:
  ** description: Creates OPTIONS fields for a select
  ** parameters:
  ** returns:
  ***************************************************************************/
  function list_tree($category_id="", $cid="0", $level=0) {
    global $ps_vendor_id;
    $db = new ps_DB;

    $level++;

    $q = "SELECT * from category,category_xref ";
    $q .= "WHERE category_xref.category_parent_id='";
    $q .= $cid . "' AND ";
    $q .= "category.category_id=category_xref.category_child_id ";
    $q .= "AND category.vendor_id = $ps_vendor_id ";
    $db->query($q);

    while ($db->next_record()) {
      if ($db->f("category_child_id") !== $cid) {
	if ($db->f("category_child_id") == $category_id) {
	  echo "<OPTION SELECTED VALUE=" . $db->f("category_child_id") . ">\n";
	}
	else {
	  echo "<OPTION VALUE=" . $db->f("category_child_id") . ">\n";
	}
      }
      else {
          echo "<OPTION VALUE=" . $db->f("category_child_id") . ">\n";
      }
      for ($i=0;$i<$level;$i++) {
        echo "&#151";
      }
      echo "|$level|";
      echo "&nbsp;" . $db->f("category_name") . "</OPTION>";
      $this->list_tree($category_id, $db->f("category_child_id"), $level);
    }
  }

  /**************************************************************************
  ** name: get_name()
  ** created by: jep
  ** description: Returns the category name.
  ** parameters:
  ** returns:
  ***************************************************************************/
  function get_name($product_id) {
    $db = new ps_DB;

    $q = "SELECT category_name FROM category,product_category_xref ";
    $q .= "WHERE product_id='$product_id' ";
    $q .= "AND category.category_id = product_category_xref.category_id ";
    $db->query($q);

    $db->next_record();

    return $db->f("category_name");
  }
  

/*
Creates navigation list of categgories

*/

  function navigation_list($category_id) {
    global $sess;
    $db = new ps_DB;
    $dbt = new ps_DB;
    static $i=0;

    $q = "SELECT * from category, category_xref WHERE ";
    $q .= "category_xref.category_child_id='$category_id' ";
    $q .= "AND category.category_id='$category_id'";
    $db->query($q);
    $db->next_record();
	$category_url = $db->f("category_url");
    if ($db->f("category_parent_id")) {
      $link .= "<A HREF=";
      $link .= $sess->url(URL .
			  "?page=shop/browse&category=$category_url");
      $link .= ">";
      $link .= $db->f("category_name");
      $link .= "</A>";
      $category_list[$i++] = " > " . $link;
      $this->navigation_list($db->f("category_parent_id"));
    }
    else {
      $link = "<A HREF=";
      $link .= $sess->url(URL .
			  "?page=shop/browse&category=$category_url");
      $link .= ">";
      $link .= $db->f("category_name");
      $link .= "</A>";
      $category_list[$i++] = $link;

    }
    while (list($key, $value) = each($category_list)) {
        echo "$value";
    }

    return True;
  }


}
?>
