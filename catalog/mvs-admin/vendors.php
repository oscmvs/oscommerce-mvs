<?php
/*
  $Id: vendors.php,v 1.20 2006/03/25 by Craig Garrison Sr www.blucollarsales.com for MVS V1.0 2006/03/25 JCK/CWG
  $Loc: /catalog/admin/ $
  $Mod: MVS V1.2 2009/02/28 JCK/CWG $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce
*/

  require ('includes/application_top.php');

  $action = (isset ($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');

  $error = false;
  $processed = false;

  if (tep_not_null($action)) {
    switch ($action) {
      case 'update' :
      case 'insert' :

        $vendors_id = tep_db_prepare_input($HTTP_GET_VARS['vendors_id']);
        $vendors_name = tep_db_prepare_input($HTTP_POST_VARS['vendors_name']);
        $vendors_contact = tep_db_prepare_input($HTTP_POST_VARS['vendors_contact']);
        $vendors_phone1 = tep_db_prepare_input($HTTP_POST_VARS['vendors_phone1']);
        $vendors_phone2 = tep_db_prepare_input($HTTP_POST_VARS['vendors_phone2']);
        $vendors_email = tep_db_prepare_input($HTTP_POST_VARS['vendors_email']);
        $vendors_send_email = tep_db_prepare_input($HTTP_POST_VARS['vendors_send_email']);
        $vendors_status_send = tep_db_prepare_input($HTTP_POST_VARS['vendors_status_send']);
        $vendor_street = tep_db_prepare_input($HTTP_POST_VARS['vendor_street']);
        $vendor_add2 = tep_db_prepare_input($HTTP_POST_VARS['vendor_add2']);
        $vendor_city = tep_db_prepare_input($HTTP_POST_VARS['vendor_city']);
        $vendor_state = tep_db_prepare_input($HTTP_POST_VARS['vendor_state']);
        $vendors_zipcode = tep_db_prepare_input($HTTP_POST_VARS['vendors_zipcode']);
        $vendor_country = tep_db_prepare_input($HTTP_POST_VARS['vendor_country']);
        $account_number = tep_db_prepare_input($HTTP_POST_VARS['account_number']);
        $vendors_url = tep_db_prepare_input($HTTP_POST_VARS['vendors_url']);
        $vendor_add_info = tep_db_prepare_input($HTTP_POST_VARS['vendor_add_info']);
        $handling_charge = tep_db_prepare_input($HTTP_POST_VARS['handling_charge']);
        $handling_per_box = tep_db_prepare_input($HTTP_POST_VARS['handling_per_box']);
        $tare_weight = tep_db_prepare_input($HTTP_POST_VARS['tare_weight']);
        $percent_tare_weight = tep_db_prepare_input($HTTP_POST_VARS['percent_tare_weight']);
        $max_box_weight = tep_db_prepare_input($HTTP_POST_VARS['max_box_weight']);
        $zones = tep_db_prepare_input($HTTP_POST_VARS['zones']);

        $sql_data_array = array (
          'vendors_id' => $vendors_id,
          'vendors_name' => $vendors_name,
          'vendors_contact' => $vendors_contact,
          'vendors_phone1' => $vendors_phone1,
          'vendors_phone2' => $vendors_phone2,
          'vendors_email' => $vendors_email,
          'vendors_send_email' => $vendors_send_email,
          'vendors_status_send' => $vendors_status_send,
          'vendor_street' => $vendor_street,
          'vendor_add2' => $vendor_add2,
          'vendor_city' => $vendor_city,
          'vendor_state' => $vendor_state,
          'vendors_zipcode' => $vendors_zipcode,
          'vendor_country' => $vendor_country,
          'account_number' => $account_number,
          'vendors_url' => $vendors_url,
          'vendor_add_info' => $vendor_add_info,
          'handling_charge' => $handling_charge,
          'handling_per_box' => $handling_per_box,
          'tare_weight' => $tare_weight,
          'percent_tare_weight' => $percent_tare_weight,
          'max_box_weight' => $max_box_weight,
          'zones' => $zones
        );

        $HTTP_POST_VARS['vendors_image'] = $vendors_image_name;
        if (isset ($vendors_image_name) && tep_not_null($vendors_image_name) && ($vendors_image_name != 'none')) {
          $HTTP_POST_VARS['vendors_image'] = $vendors_image_name;
          $sql_data_array['vendors_image'] = tep_db_prepare_input($HTTP_POST_VARS['vendors_image']);
        } elseif (isset ($vInfo->vendors_image)) {
          $HTTP_POST_VARS['vendors_image'] = $vInfo->vendors_image;
          $sql_data_array['vendors_image'] = tep_db_prepare_input($HTTP_POST_VARS['vendors_image']);
        }
        //add delete image function
        if ($HTTP_POST_VARS['delete_vendors_image'] == 'yes') {
          unlink(DIR_FS_CATALOG_IMAGES . $HTTP_POST_VARS['vendors_image']);
          $sql_data_array['vendors_image'] = tep_db_prepare_input($HTTP_POST_VARS['null']);
          $messageStack->add_session(WARNING_VENDOR_IMAGE_DELTED, 'warning');
        }
        //end delete image function
        if ($action == 'update') {
          $messageStack->add_session($vendors_name . '  ' . SUCCESS_VENDOR_UPDATED_SUCCESSFULLY, 'success');
        }
        if ($action == 'insert') {
          $messageStack->add_session($vendors_name . '  ' . SUCCESS_VENDOR_CREATED_SUCCESSFULLY, 'success');
        }
        tep_db_perform(TABLE_VENDORS, $sql_data_array, 'update', "vendors_id = '" . $vendors_id . "'");

        $vInfo = new objectInfo($HTTP_POST_VARS);

        // copy vendor image only if modified
        $vendors_image = new upload('vendors_image');
        $vendors_image->set_destination(DIR_FS_CATALOG_IMAGES);
        if ($vendors_image->parse() && $vendors_image->save()) {
          $vendors_image_name = $vendors_image->filename;
          $messageStack->add_session($vendor_name . SUCCESS_VENDOR_IMAGE_UPLOADED, 'success');
        } else {
          $vendors_image_name = (isset ($HTTP_POST_VARS['previous_vendors_image']) ? $HTTP_POST_VARS['previous_vendors_image'] : '');
          $messageStack->add_session(WARNING_VENDOR_IMAGE_NOUPLOAD, 'warning');
        }
        if ($action == 'insert') {
          tep_db_perform(TABLE_VENDORS, $sql_data_array);
          $vendors_id = tep_db_insert_id();

          tep_redirect(tep_href_link(FILENAME_VENDORS, tep_get_all_get_params(array ('vendors_id', 'action')) . 'vendors_id=' . $vendors_id));
        } else {
          tep_redirect(tep_href_link(FILENAME_VENDORS, tep_get_all_get_params(array ('vendors_id', 'action')) . 'vendors_id=' . $vendors_id));
        }

        break;

      case 'setflag' :
        if (($HTTP_GET_VARS['flag'] == '0') || ($HTTP_GET_VARS['flag'] == '1')) {
          if (isset ($HTTP_GET_VARS['vendors_id'])) {
            tep_set_vendor_email($HTTP_GET_VARS['vendors_id'], $HTTP_GET_VARS['flag']);
          }

        }
        tep_redirect(tep_href_link(FILENAME_VENDORS, tep_get_all_get_params(array ('vendors_id', 'action'))));
        break;

      case 'deleteconfirm' :
        $vendors_id = tep_db_prepare_input($HTTP_GET_VARS['vendors_id']);
        $vendor_deleted = false;
        $products_deleted = false;

        $vendor_name_query = tep_db_query("select vendors_name from " . TABLE_VENDORS . " where vendors_id = '" . (int) $vendors_id . "'");
        while ($vendors_name = tep_db_fetch_array($vendor_name_query)) {
          $vendor_name = $vendors_name['vendors_name'];
        }
        if (isset ($HTTP_POST_VARS['delete_products'])) {
          $products_query = tep_db_query("select products_id from " . TABLE_PRODUCTS . " where vendors_id = '" . (int) $vendors_id . "'");
          while ($products = tep_db_fetch_array($products_query)) {
            $deleted_products = count($products);
            tep_db_query("delete from " . TABLE_PRODUCTS . " where products_id = '" . (int) $products['products_id'] . "'");
            tep_db_query("delete from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int) $products['products_id'] . "'");
            tep_db_query("delete from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . (int) $products['products_id'] . "'");
            tep_db_query("delete from " . TABLE_PRODUCTS_NOTIFICATIONS . " where products_id = '" . (int) $products['products_id'] . "'");
           // tep_db_query("delete from " . TABLE_PRODUCTS_STOCK . " where products_id = '" . (int) $products['products_id'] . "'");
            tep_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int) $products['products_id'] . "'");
            $products_deleted = true;
          }
        }

        tep_db_query("delete from " . TABLE_VENDORS . " where vendors_id = '" . (int) $vendors_id . "'");
        $vendor_deleted = true;

        if ($vendor_deleted == true) {
          $messageStack->add_session($vendor_name . SUCCESS_VENDOR_DELETED, 'success');
        } else {
          $messageStack->add_session(WARNING_VENDOR_NOT_DELETED, 'warning');
        }
        if ($products_deleted == true) {
          $messageStack->add_session($deleted_products . SUCCESS_PRODUCTS_DELETED, 'success');
        } else {
          $messageStack->add_session(WARNING_PRODUCTS_NOT_DELETED, 'warning');
        }
        tep_redirect(tep_href_link(FILENAME_VENDORS, tep_get_all_get_params(array ('vendors_id', 'action'))));
        break;

      case 'new' :
        $vendors_id = tep_db_prepare_input($HTTP_GET_VARS['vendors_id']);
        $vendors_name = tep_db_prepare_input($HTTP_POST_VARS['vendors_name']);
        $vendors_contact = tep_db_prepare_input($HTTP_POST_VARS['vendors_contact']);
        $vendors_phone1 = tep_db_prepare_input($HTTP_POST_VARS['vendors_phone1']);
        $vendors_phone2 = tep_db_prepare_input($HTTP_POST_VARS['vendors_phone2']);
        $vendors_email = tep_db_prepare_input($HTTP_POST_VARS['vendors_email']);
        $vendors_send_email = tep_db_prepare_input($HTTP_POST_VARS['vendors_send_email']);
        $vendors_status_send = tep_db_prepare_input($HTTP_POST_VARS['vendors_status_send']);
        $vendor_street = tep_db_prepare_input($HTTP_POST_VARS['vendor_street']);
        $vendor_add2 = tep_db_prepare_input($HTTP_POST_VARS['vendor_add2']);
        $vendor_city = tep_db_prepare_input($HTTP_POST_VARS['vendor_city']);
        $vendor_state = tep_db_prepare_input($HTTP_POST_VARS['vendor_state']);
        $vendors_zipcode = tep_db_prepare_input($HTTP_POST_VARS['vendors_zipcode']);
        $vendor_country = tep_db_prepare_input($HTTP_POST_VARS['vendor_country']);
        $vendors_image = tep_db_prepare_input($HTTP_POST_VARS['vendors_image']);
        $account_number = tep_db_prepare_input($HTTP_POST_VARS['account_number']);
        $vendors_url = tep_db_prepare_input($HTTP_POST_VARS['vendors_url']);
        $vendor_add_info = tep_db_prepare_input($HTTP_POST_VARS['vendor_add_info']);
        $handling_charge = tep_db_prepare_input($HTTP_POST_VARS['handling_charge']);
        $handling_per_box = tep_db_prepare_input($HTTP_POST_VARS['handling_per_box']);
        $tare_weight = tep_db_prepare_input($HTTP_POST_VARS['tare_weight']);
        $percent_tare_weight = tep_db_prepare_input($HTTP_POST_VARS['percent_tare_weight']);
        $max_box_weight = tep_db_prepare_input($HTTP_POST_VARS['max_box_weight']);
        $zones = tep_db_prepare_input($HTTP_POST_VARS['zones']);

      default:

        switch ($vInfo->vendors_send_email) {
          case '0' :
            $in_status = false;
            $out_status = true;
            break;
          case '1' :
            $in_status = true;
            $out_status = false;
          default :
            $in_status = false;
            $out_status = true;
        }
        
        if ($action != 'new') {

          $vendors_query = tep_db_query("select vendors_id, vendors_name, vendors_email, vendors_contact, vendors_phone1, vendors_phone2, vendors_email, vendors_send_email, vendors_status_send, vendor_street, vendor_add2, vendor_city, vendor_state, vendors_zipcode, vendor_country, vendor_country, vendors_image, account_number, vendors_url, vendor_add_info,  handling_charge, handling_per_box, tare_weight, percent_tare_weight, max_box_weight, zones from " . TABLE_VENDORS . " where vendors_id = '" . (int) $HTTP_GET_VARS['vendors_id'] . "'");
          $vendor_data = tep_db_fetch_array($vendors_query);
          $vInfo = new objectInfo($vendor_data);

          $vendors_image_name = $vInfo->vendors_image;

        }
    }
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
<body><!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php

  if ($action == 'edit' || $action == 'update' || $action == 'new' || $action == 'insert' || $action == 'setflag') {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading">
<?php

    if ($action == 'insert') {
      echo HEADING_TITLE_ADD;
    } else {
      echo HEADING_TITLE;
    }
?>
            </td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
       <tr>
<?php

    $form_action = (isset ($HTTP_GET_VARS['vendors_id'])) ? 'update' : 'insert';

    echo tep_draw_form('vendor_form', FILENAME_VENDORS, tep_get_all_get_params(array ('action')) . (($action == 'new' || $action == 'insert') ? 'action=insert' : 'action=update'), 'post', 'enctype="multipart/form-data"') . tep_draw_hidden_field('vendors_id', $vInfo->vendors_id);
?>
        <td class="formAreaTitle"><?php echo TEXT_BOX_ADMIN; ?></td>
      </tr>
      <tr>
        <td class="formArea"><table border="1" cellspacing="2" cellpadding="2">
        <tr>
            <td class="main"><?php echo TEXT_VENDORS_ID; ?></td>
            <td class="main">
<?php

    echo $vInfo->vendors_id;
?>
            </td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_VENDORS_NAME; ?></td>
            <td class="main"><?php echo tep_draw_input_field ('vendors_name', $vInfo->vendors_name, 'maxlength="64"'); ?></td>
         </tr>
<?php

    $orders_statuses = array ();
    $orders_status_array = array ();
    $orders_status_query = tep_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . (int) $languages_id . "'");
    while ($orders_status = tep_db_fetch_array($orders_status_query)) {
      $orders_statuses[] = array (
        'id' => $orders_status['orders_status_id'],
        'text' => $orders_status['orders_status_name']
      );
      $orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
    }
?>
         <tr>
           <td class="main"><?php echo TEXT_VENDORS_STATUS_EMAIL; ?></td>
           <td class="main"> <?php echo tep_draw_separator('pixel_trans.gif', '1', '15') . tep_draw_pull_down_menu('vendors_status_send', $orders_statuses, $vInfo->vendors_status_send); ?></td>
         </tr>
         <tr>
           <td class="main"><?php echo TEXT_VENDORS_SEND_EMAIL; ?></td>
           <td class="main">
<?php

    if ($vInfo->vendors_send_email == '0') {
      $in_status = false;
      $out_status = true;
    } elseif ($vInfo->vendors_send_email == '1') {
      $in_status = true;
      $out_status = false;
    }
    echo tep_draw_separator('pixel_trans.gif', '1', '15') . tep_draw_radio_field('vendors_send_email', '1', $in_status) . '&nbsp;' . TEXT_SEND_ORDER_EMAIL_YES . '&nbsp;' . tep_draw_radio_field('vendors_send_email', '0', $out_status) . '&nbsp;' . TEXT_SEND_ORDER_EMAIL_NO;
?>
           </td>
         </tr>
         <tr>
           <td class="main"><?php echo TEXT_VENDORS_CONTACT; ?></td>
           <td class="main"><?php echo tep_draw_input_field('vendors_contact', $vInfo->vendors_contact, 'maxlength="64"'); ?></td>
         </tr>
         <tr>
           <td class="main"><?php echo TEXT_VENDORS_PHONE1; ?></td>
           <td class="main"><?php echo tep_draw_input_field('vendors_phone1', $vInfo->vendors_phone1, 'maxlength="64"'); ?></td>
         </tr>
         <tr>
           <td class="main"><?php echo TEXT_VENDORS_PHONE2; ?></td>
           <td class="main"><?php echo tep_draw_input_field('vendors_phone2', $vInfo->vendors_phone2, 'maxlength="64"');?></td>
         </tr>
         <tr>
           <td class="main"><?php echo TEXT_VENDORS_EMAIL; ?></td>
           <td class="main"><?php echo tep_draw_input_field('vendors_email', $vInfo->vendors_email, 'maxlength="64"');?></td>
         <tr>
           <td class="main"><?php echo TEXT_VENDOR_STREET; ?></td>
           <td class="main"><?php echo tep_draw_input_field('vendor_street', $vInfo->vendor_street, 'maxlength="64"'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_VENDOR_ADDRESS_2; ?></td>
            <td class="main"><?php echo tep_draw_input_field('vendor_add2', $vInfo->vendor_add2, 'maxlength="64"'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_VENDOR_CITY; ?></td>
            <td class="main"><?php echo tep_draw_input_field('vendor_city', $vInfo->vendor_city, 'maxlength="64"'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_VENDOR_STATE; ?></td>
            <td class="main"><?php echo tep_draw_input_field('vendor_state', $vInfo->vendor_state, 'maxlength="32"'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_VENDORS_ZIPCODE; ?></td>
            <td class="main"><?php echo tep_draw_input_field('vendors_zipcode', $vInfo->vendors_zipcode, 'maxlength="32"'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_VENDOR_COUNTRY; ?></td>
            <td class="main"><?php echo tep_get_country_list('vendor_country', $vInfo->vendor_country); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_VENDORS_URL; ?></td>
            <td class="main"><?php echo tep_draw_input_field('vendors_url', $vInfo->vendors_url, 'maxlength="96"'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_ACCOUNT_NUMBER; ?></td>
            <td class="main"><?php echo tep_draw_input_field('account_number', $vInfo->account_number, 'maxlength="64"'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_VENDOR_ADD_INFO; ?></td>
            <td class="main"><?php echo tep_draw_textarea_field('vendor_add_info', 'soft', '60', '3', (isset ($vendor_add_info) ? $vendor_add_info : $vInfo->vendor_add_info)); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_HANDLING; ?></td>
            <td class="main"><?php echo tep_draw_input_field('handling_charge', $vInfo->handling_charge, 'maxlength="32"'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_HANDLING_PER_BOX; ?></td>
            <td class="main"><?php echo tep_draw_input_field('handling_per_box', $vInfo->handling_per_box, 'maxlength="32"'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_TARE_WEIGHT; ?></td>
            <td class="main"><?php echo tep_draw_input_field('tare_weight', $vInfo->tare_weight, 'maxlength="32"'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_PERCENT_TARE_WEIGHT; ?></td>
            <td class="main"><?php echo tep_draw_input_field('percent_tare_weight', $vInfo->percent_tare_weight, 'maxlength="32"'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_MAX_BOX_WEIGHT; ?></td>
            <td class="main"><?php echo tep_draw_input_field('max_box_weight', $vInfo->max_box_weight, 'maxlength="32"');?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_ZONES; ?></td>
            <td class="main"><?php echo tep_draw_input_field ('zones', $vInfo->zones, 'maxlength="32"') . '&nbsp;&nbsp;&nbsp;' . TEXT_ZONES_EXPLAIN; ?></td>
          </tr>
          <tr>
            <td class="main" align="top"><?php echo TEXT_VENDORS_IMAGE; ?>
            <td class="main">
<?php 
    echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_file_field('vendors_image') . tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . $vInfo->vendors_image . tep_draw_hidden_field('previous_vendors_image', $vInfo->vendors_image);
    //  echo tep_draw_hidden_field('vendors_image', stripslashes($vendors_image_name));

    echo tep_draw_separator('pixel_trans.gif', '24', '15') . tep_draw_checkbox_field('delete_vendors_image', 'yes', false) . TEXT_DELETE_IMAGE;
    echo '<br>' . tep_info_image($vInfo->vendors_image, $vInfo->vendors_image, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);
?>
            </td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td align="right" class="main">
<?php

    if ($action == 'new') {
		echo tep_draw_button(IMAGE_SAVE, 'disk', null, 'primary') . tep_draw_button(IMAGE_CANCEL, 'close', tep_href_link(FILENAME_VENDORS, tep_get_all_get_params(array('action'))));  	  
    } else {
		echo tep_draw_button(IMAGE_SAVE, 'disk', null, 'primary') . tep_draw_button(IMAGE_CANCEL, 'close', tep_href_link(FILENAME_VENDORS, tep_get_all_get_params(array('action'))));
    }        
?>
        </td>
      </tr></form>
<?php

    } else {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_VENDORS; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CONTACT; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_SEND_EMAIL; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php

    $search = '';
    if (isset ($HTTP_GET_VARS['search']) && tep_not_null($HTTP_GET_VARS['search'])) {
      $keywords = tep_db_input(tep_db_prepare_input($HTTP_GET_VARS['search']));
      $search = "where v.vendors_name like '%" . $keywords . "%' or v.vendors_url like '%" . $keywords . "%' or v.vendors_contact like '%" . $keywords . "%' or vendors_email like '%" . $keywords . "%'";
    }
    $vendors_content_query_raw = "select vendors_id, vendors_name, vendors_email, vendors_contact, vendors_phone1, vendors_phone2, vendors_email, vendors_send_email, vendors_status_send, vendor_street, vendor_add2, vendor_city, vendor_state, vendors_zipcode, vendor_country, vendor_country, vendors_image, account_number, vendors_url, vendor_add_info, handling_charge, handling_per_box, tare_weight, max_box_weight from " . TABLE_VENDORS . " order by vendors_id";
    $vendors_content_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $vendors_content_query_raw, $vendors_content_query_numrows);
    $vendors_query = tep_db_query($vendors_content_query_raw);
    while ($vendor_data = tep_db_fetch_array($vendors_query)) {
      if ((!isset ($HTTP_GET_VARS['vendors_id']) || (isset ($HTTP_GET_VARS['vendors_id']) && ($HTTP_GET_VARS['vendors_id'] == $vendor_data['vendors_id']))) && !isset ($vInfo)) {
        $vInfo_array = $vendor_data;
        $vInfo = new objectInfo($vInfo_array);
      }
      if (isset ($vInfo) && is_object($vInfo) && ($vendor_data['vendors_id'] == $vInfo->vendors_id)) {
        echo '          <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_VENDORS, tep_get_all_get_params(array ('vendors_id', 'action')) . 'vendors_id=' . $vInfo->vendors_id . '&action=edit') . '\'">' . "\n";
      } else {
        echo '          <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_VENDORS, tep_get_all_get_params(array ('vendors_id')) . 'vendors_id=' . $vendor_data['vendors_id']) . '\'">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo $vendor_data['vendors_name']; ?></td>
                <td class="dataTableContent"><?php echo $vendor_data['vendors_contact']; ?></td>
                <td class="dataTableContent" align="center">
<?php

      if ($vendor_data['vendors_send_email'] == '1') {
        echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_VENDORS, 'action=setflag&flag=0&vendors_id=' . $vendor_data['vendors_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      } else {
        echo '<a href="' . tep_href_link(FILENAME_VENDORS, 'action=setflag&flag=1&vendors_id=' . $vendor_data['vendors_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
      }
?>
                </td>
                <td class="dataTableContent" align="right"><?php if (isset($vInfo) && is_object($vInfo) && ($vendor_data['vendors_id'] == $vInfo->vendors_id)) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_VENDORS, tep_get_all_get_params(array('vendors_id')) . 'vendors_id=' . $vendor_data['vendors_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php

    }
?>
              <tr>
                <td colspan="4"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $vendors_content_split->display_count($vendors_content_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_VENDORS); ?></td>
                    <td class="smallText" align="right"><?php echo $vendors_content_split->display_links($vendors_content_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'vendors_id'))); ?></td>
                  </tr>

                  <tr>
                    <td align="right" colspan="2"><?php echo tep_draw_button(IMAGE_INSERT, 'plus', tep_href_link(FILENAME_VENDORS, tep_get_all_get_params(array('vendors_id', 'action')) . 'action=new')); ?></td>
                  </tr>

                </table></td>
              </tr>
            </table></td>
<?php

    $heading = array ();
    $contents = array ();

    switch ($action) {
      case 'confirm' :
        $heading[] = array ('text' => '<b>' . TEXT_INFO_HEADING_DELETE_VENDOR . '</b>');

        $count_products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " where vendors_id = '" . (int) $vInfo->vendors_id . "'");
        while ($count_products = tep_db_fetch_array($count_products_query)) {
          $num_products = $count_products['total'];
        }
        $contents = array ('form' => tep_draw_form('vendor_delete', FILENAME_VENDORS, tep_get_all_get_params(array ('vendors_id', 'action')) . 'vendors_id=' . $vInfo->vendors_id . '&action=deleteconfirm'));
        $contents[] = array ( 'text' => '<b>CAUTION!! ' . $num_products . ' products are assigned to this vendor! <br><br></b>' . TEXT_DELETE_INTRO . '<br><br><b>' . $vInfo->vendors_name . ' ' . $vInfo->vendors_contact . '</b>');
        $contents[] = array ('text' => tep_draw_checkbox_field('delete_products', $vendors_id, false) . '&nbsp; Delete Products?');
        $contents[] = array (
          'align' => 'center',
          'text' => '<br>' . tep_draw_button(IMAGE_DELETE, 'trash', null, 'primary').
		  					tep_draw_button(IMAGE_CANCEL, 'close', tep_href_link(FILENAME_VENDORS, 'vendors_id=' . $vInfo->vendors_id . '&page='. $_GET['page'])));
        break;

      default :
        if (isset ($vInfo) && is_object($vInfo)) {
          $count_products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " where vendors_id = '" . (int) $vInfo->vendors_id . "'");
          while ($count_products = tep_db_fetch_array($count_products_query)) {
            $num_products = $count_products['total'];
          }
          $vendors_orders_count_query = tep_db_query("select count(*) as total from " . TABLE_ORDERS_SHIPPING . " where vendors_id='" . (int) $vInfo->vendors_id . "'");
          if ($vendors_orders_count = tep_db_fetch_array($vendors_orders_count_query)) {
            $num_orders = $vendors_orders_count['total'];
          } else {
            $num_orders = 'no';
          }
          $heading[] = array ('text' => '<b>' . $vInfo->vendors_name . ' ' . $vInfo->vendors_contact . '</b>');

          $contents[] = array (
            'align' => 'center',
            'text' => tep_draw_button(IMAGE_EDIT, 'document', tep_href_link(FILENAME_VENDORS, tep_get_all_get_params(array('vendors_id', 'action')) . 'vendors_id=' . $vInfo->vendors_id . '&action=edit')) .			
					  tep_draw_button(IMAGE_DELETE, 'trash', tep_href_link(FILENAME_VENDORS, tep_get_all_get_params(array('vendors_id', 'action')) . 'vendors_id=' . $vInfo->vendors_id . '&action=confirm')).'<br>'.					  
					  tep_draw_button(IMAGE_SHIPPING, 'document', tep_href_link(FILENAME_VENDOR_MODULES . '?set=shipping&vendors_id=' . $vInfo->vendors_id)).
					  tep_draw_button(IMAGE_ORDERS, 'cart', tep_href_link(FILENAME_ORDERS_VENDORS . '?vendors_id=' . $vInfo->vendors_id)).					
				 	  '<br><br>There are <b>' . $num_orders . '</b> orders for this Vendor.<br>This Vendor has <b>' . $num_products . '</b> products.<br>Click <a href="' . tep_href_link(FILENAME_PRODS_VENDORS, '&vendors_id=' . $vInfo->vendors_id) . '"><b>here </b></a>for Products Report<br>'
            );

        }
        break;
    }
    if ((tep_not_null($heading)) && (tep_not_null($contents))) {
      echo '            <td width="25%" valign="top">' . "\n";

      $box = new box;
      echo $box->infoBox($heading, $contents);

      echo '            </td>' . "\n";
    }
?>
          </tr>
        </table></td>
      </tr>
<?php

  }
?>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>