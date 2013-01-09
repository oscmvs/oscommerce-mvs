<?php

/*
  $ID: move_vendor_prods.php (for use with MVS) by Craig Garrison Sr, BluCollar Sales
  $Loc: /catalog/admin/ $
  $Mod: MVS V1.2 2009/02/28 JCK/CWG $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require_once ('includes/application_top.php');
//echo $action;
  
  //if ($action == 'update') {

if($_REQUEST['action']=='update')
{
	//	echo "sucessfully";	  
    $count_products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " where vendors_id = '" . (int) $delete_vendors_id . "'");
	
    while ($count_products = tep_db_fetch_array($count_products_query)) {
      $num_products = $count_products['total'];
    }
	//update http post variables
	$new_vendors_id=$HTTP_POST_VARS['new_vendors_id'];
	$delete_vendors_id=$HTTP_POST_VARS['delete_vendors_id'];
    $update_query = "update " . TABLE_PRODUCTS . " SET vendors_id = '" . $new_vendors_id . "' where vendors_id = '" . $delete_vendors_id . "';";
    $update_result = tep_db_query($update_query);
	
	
//	echo($update_query);
  
  }
  if($update_result>0)
  {
	//header('location:move_vendor_prods.php?action=edit') ; 
  }

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<meta name="robots" content="noindex,nofollow">
<title><?php echo TITLE; ?></title>
<base href="<?php echo HTTP_SERVER . DIR_WS_ADMIN; ?>" />
<!--[if IE]><script type="text/javascript" src="<?php echo tep_catalog_href_link('ext/flot/excanvas.min.js'); ?>"></script><![endif]-->
<link rel="stylesheet" type="text/css" href="<?php echo tep_catalog_href_link('ext/jquery/ui/redmond/jquery-ui-1.8.22.css'); ?>">
<script type="text/javascript" src="<?php echo tep_catalog_href_link('ext/jquery/jquery-1.8.0.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo tep_catalog_href_link('ext/jquery/ui/jquery-ui-1.8.22.min.js'); ?>"></script>

<script type="text/javascript">
// fix jQuery 1.8.0 and jQuery UI 1.8.22 bug with dialog buttons; http://bugs.jqueryui.com/ticket/8484
if ( $.attrFn ) { $.attrFn.text = true; }
</script>

<?php
  if (tep_not_null(JQUERY_DATEPICKER_I18N_CODE)) {
?>
<script type="text/javascript" src="<?php echo tep_catalog_href_link('ext/jquery/ui/i18n/jquery.ui.datepicker-' . JQUERY_DATEPICKER_I18N_CODE . '.js'); ?>"></script>
<script type="text/javascript">
$.datepicker.setDefaults($.datepicker.regional['<?php echo JQUERY_DATEPICKER_I18N_CODE; ?>']);
</script>
<?php
  }
?>

<script type="text/javascript" src="<?php echo tep_catalog_href_link('ext/flot/jquery.flot.js'); ?>"></script>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script type="text/javascript" src="includes/general.js"></script>
</head>
<body>
<!-- header //-->
<?php require_once (DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require_once (DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
        </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <?php
		  if($_REQUEST['action']=='update')
		  {
			echo '<span style="color:red">Records moved sucessfully</span>';  
		  }
		  ?>
          
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
<?php

  if ($action == 'update') {
    $vendor_name_deleted = tep_db_query("select vendors_name from " . TABLE_VENDORS . " where vendors_id = '" . $delete_vendors_id . "'");
    while ($vendor_deleted = tep_db_fetch_array($vendor_name_deleted)) {
      $deleted_vendor = $vendor_deleted['vendors_name'];
    }
    
    $vendor_name_moved = tep_db_query("select vendors_name from " . TABLE_VENDORS . " where vendors_id = '" . $new_vendors_id . "'");
    while ($vendor_moved = tep_db_fetch_array($vendor_name_moved)) {
      $moved_vendor = $vendor_moved['vendors_name'];
    }
    
    if ($update_result) {
?>
      <tr>
        <td class="messageStackSuccess" align="left">
      </tr>
<?php
      // echo '<br><b>The new Vendor\'s name:  ' . $moved_vendor;
      echo '<br><b>' . $num_products . '</b> products were moved from <b>' . $deleted_vendor . '</b> to <b>' . $moved_vendor . '</b>.<br> You can Go <a href="' . tep_href_link(FILENAME_MOVE_VENDORS) . '"><b>Back and start</b></a> again OR Go <a href="' . tep_href_link(FILENAME_VENDORS) . '"><b>Back To Vendors List</b></a>';
    } else {
?>
      <tr>
        <td class="messageStackError" align="left">
      </tr>
<?php
      echo '<br><b>NO</b> products were moved from <b>' . $deleted_vendor . '</b> to <b>' . $moved_vendor . '</b>.<br> You should Go <a href="' . tep_href_link(FILENAME_MOVE_VENDORS) . '"><b>Back and start</b></a> over OR Go <a href="' . tep_href_link(FILENAME_VENDORS) . '"><b>Back To Vendors List</b></a>';
    }
?>
<?php 
  } elseif ($action == '') { 
?>
      <tr>
        <td class="main" align="left"><?php echo '<a href="' . tep_href_link(FILENAME_VENDORS) . '"><b>Go Back To Vendors List</a>';?></tr>
      <tr>
        <td class="main" align="left"><?php echo 'Select the vendors you plan to work with.'; ?>
      </tr>
      <tr bgcolor="#FF0000">
        <td class="main" align="left"><?php echo '<b>This action is not easily reversible, and clicking the update button will perform this action immediately, there is no turning back.</b>'; ?>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
<?php
    echo tep_draw_form ('move_vendor_form', FILENAME_MOVE_VENDORS, tep_get_all_get_params( array ('action') ) . 'action=update', 'post');

    $vendors_array = array (array ('id' => '1',
                                   'text' => 'NONE'
                                  )
                           );
    $vendors_query = tep_db_query ("select vendors_id, 
                                           vendors_name 
                                    from " . TABLE_VENDORS . " 
                                    order by vendors_name
                                 ");
    while ($vendors = tep_db_fetch_array ($vendors_query) ) {
      $vendors_array[] = array ('id' => $vendors['vendors_id'],
                                'text' => $vendors['vendors_name']
                               );
    }
?>
                <td class="main" align="left"><?php echo TEXT_VENDOR_CHOOSE_MOVE . ' -->  '; ?><?php echo tep_draw_pull_down_menu('delete_vendors_id', $vendors_array);?></td>
              </tr>
              <tr>
                <td class="main" align="left"><br><?php echo TEXT_VENDOR_CHOOSE_MOVE_TO . ' -->  '; ?><?php echo tep_draw_pull_down_menu('new_vendors_id', $vendors_array);?></td>
              </tr>
              <tr>
                <td><br><?php echo tep_image_submit ('button_update.gif', 'SUBMIT') . ' <a href="' . tep_href_link (FILENAME_MOVE_VENDORS, tep_get_all_get_params (array ('action') ) ) .'">' . tep_image_button ('button_cancel.gif', IMAGE_CANCEL) . '</a>';  ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
<?php 
  } 
?>
      <tr>
        <td colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="smallText" valign="top"><?php //echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS);?></td>
            <td class="smallText" align="right"><?php //echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page'].$vendors_id);?></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>

<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php');  ?>