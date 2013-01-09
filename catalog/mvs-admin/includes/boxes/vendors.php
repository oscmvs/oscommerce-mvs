<?php
/*
  $Id: vendors.php,v 1.21 2003/07/09 01:18:53 hpdl Exp $
  $Loc: /catalog/admin/includes/boxes/ $
  $Mod: MVS V1.2 2009/02/28 JCK/CWG $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Released under the GNU General Public License
*/
$cl_box_groups[] = array(
    'heading' => BOX_HEADING_VENDORS,
    'apps' => array(
      array(
        'code' => FILENAME_VENDORS,
        'title' => BOX_VENDORS,
        'link' => tep_href_link(FILENAME_VENDORS)
      ),
      array(
        'code' => FILENAME_PRODS_VENDORS,
        'title' =>  BOX_VENDORS_REPORTS_PROD,
        'link' => tep_href_link(FILENAME_PRODS_VENDORS)
      ),
      array(
        'code' => FILENAME_ORDERS_VENDORS,
        'title' => BOX_VENDORS_ORDERS,
        'link' => tep_href_link(FILENAME_ORDERS_VENDORS)
      ),
      array(
        'code' => FILENAME_MOVE_VENDORS,
        'title' =>  BOX_MOVE_VENDOR_PRODS ,
        'link' => tep_href_link(FILENAME_MOVE_VENDORS)
      ),
     
    )
  );

?>