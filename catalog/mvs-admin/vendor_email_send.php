<?php

/*
  $Id: vendor_email_send.php,v 1.0 2005/05/12 22:50:52 hpdl Exp $
  for use with Vendors_Auto_Email and MVS by Craig Garrison Sr.(craig@blucollarsales.com) and Jim Keebaugh
  $Loc: /catalog/admin/ $
  $Mod: MVS V1.2 2009/02/28 JCK/CWG $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require ('includes/application_top.php');

  $debug = 'no';
  $debug_preview = 'no';
  $debug_arrive = 'no';
  $debug_sent = 'no';

  if ($_GET['vID'] != ''){ $vendors_id = $_GET['vID']; }else{ $vendors_id = $_POST['vID']; }
  if ($vendors_id == ''){ 
  	if ($_GET['vendors_id'] != ''){
		$vendors_id = $_GET['vendors_id']; 	
	}else{ 
  		$vendors_id = $_POST['vendors_id']; 
  	}
  }
  
  if ($_GET['oID'] != ''){ $oID = $_GET['oID']; }else{ $oID = $_POST['oID']; }
  if ($_GET['vOS'] != ''){ $vOS = $vendor_order_sent = $_GET['vOS']; }else{ $vOS = $vendor_order_sent = $_POST['vOS']; }  
  
  //print $vendors_id.' - '.$oID .' - '.$vendor_order_sent;
  
  if ($debug == 'yes') {
    echo 'The vendor post data: ' . $vendors_id . ' ' . $oID . ' ' . $vendor_order_sent . '<br>';
  }

  $error = false;
  if (isset ($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'send_vendor_email')) {

    $email = stripslashes($HTTP_POST_VARS['email']);
    $the_email = stripslashes($HTTP_POST_VARS['the_email']);
    $the_contact = stripslashes($HTTP_POST_VARS['the_contact']);
    $oID = stripslashes($HTTP_POST_VARS['order_number']);
    $the_name = stripslashes($HTTP_POST_VARS['the_name']);
    $vendors_id = $HTTP_POST_VARS['vID'];
	
    if ($debug_sent == 'yes') {
      echo 'All the posted data is here: <br>' . (int) $vendors_id . '<br>' . $the_email . '<br>' . $the_contact . '<br>' . $oID . '<br>' . $the_name . '<br>' . $email;
      echo 'All the action: <br>' . $HTTP_GET_VARS['action'];
    }
    if ($HTTP_GET_VARS['action'] == 'send_vendor_email') {
      
	  if (tep_mail($the_name, $the_email, EMAIL_TEXT_ORDER_NUMBER . ' ' . $oID, $email . '<br>', STORE_NAME, STORE_OWNER_EMAIL_ADDRESS) ){
		$vendor_order_sent = 'yes';
	  }
	  			
      if ( $vendor_order_sent == 'yes') {
        tep_db_query ("update " . TABLE_ORDERS_SHIPPING . " set vendor_order_sent = 'yes' where orders_id = '" . (int) $oID . "'  and vendors_id = '" . (int) $vendors_id . "'");
      } else {
        tep_db_query ("update " . TABLE_ORDERS_SHIPPING . " set vendor_order_sent = 'no' where orders_id = '" . (int) $oID . "'  and vendors_id = '" . (int) $vendors_id . "'");
      }      
      
      $messageStack->add('success', 'Email Sent');
      tep_redirect(tep_href_link(FILENAME_VENDORS_EMAIL_SEND, 'action=success' . '&oID=' . $oID . '&contact=' . $the_contact));
    } else {
      $error = true;

      $messageStack->add('contact', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
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
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php

  require_once (DIR_WS_INCLUDES . 'header.php');
?>
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
        <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
        <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
     <!--       <td class="pageHeading" align="left"><?php echo '<a href="' . tep_href_link(FILENAME_ORDERS_VENDORS, '&vID=' . $vendors_id) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>  -->
      </tr>
      <tr>
        <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td colspan="3"><?php echo tep_draw_separator(); ?></td>
          </tr>
          <tr>
            <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
 <?php

  $index2 = 0;
  //let's get the Vendors and
  //find out what shipping methods the customer chose
  $vendor_data_query = tep_db_query("select v.vendors_id, v.vendors_name, v.vendors_email, v.vendors_contact, v.vendor_add_info, v.vendor_street, v.vendor_city, v.vendor_state, v.vendors_zipcode, v.vendor_country, v.account_number, v.vendors_status_send, os.shipping_module, os.shipping_method, os.shipping_cost, os.shipping_tax, os.vendor_order_sent from " . TABLE_VENDORS . " v,  " . TABLE_ORDERS_SHIPPING . " os where v.vendors_id=os.vendors_id and v.vendors_id='" . $vendors_id . "' and os.orders_id='" . (int) $oID . "'");
  while ($vendor_order = tep_db_fetch_array($vendor_data_query)) {
    $vendor_products[$index2] = array (
      'Vid' => $vendor_order['vendors_id'],
      'Vname' => $vendor_order['vendors_name'],
      'Vemail' => $vendor_order['vendors_email'],
      'Vcontact' => $vendor_order['vendors_contact'],
      'Vaccount' => $vendor_order['account_number'],
      'Vstreet' => $vendor_order['vendor_street'],
      'Vcity' => $vendor_order['vendor_city'],
      'Vstate' => $vendor_order['vendor_state'],
      'Vzipcode' => $vendor_order['vendors_zipcode'],
      'Vcountry' => $vendor_order['vendor_country'],
      'Vaccount' => $vendor_order['account_number'],
      'Vinstructions' => $vendor_order['vendor_add_info'],
      'Vmodule' => $vendor_order['shipping_module'],
      'Vmethod' => $vendor_order['shipping_method']
    );
    if ($debug == 'yes') {
      echo 'The vendor query: ' . $vendor_order['vendors_id'] . '<br>';
    }

    $index = 0;
    $vendor_orders_products_query = tep_db_query("select o.orders_id, o.orders_products_id, o.products_model, o.products_id, o.products_quantity, o.products_name, p.vendors_id,  p.vendors_prod_comments, p.vendors_prod_id, p.vendors_product_price from " . TABLE_ORDERS_PRODUCTS . " o, " . TABLE_PRODUCTS . " p where p.vendors_id='" . (int) $vendors_id . "' and o.products_id=p.products_id and o.orders_id='" . $oID . "' order by o.products_name");
    while ($vendor_orders_products = tep_db_fetch_array($vendor_orders_products_query)) {
      $vendor_products[$index2]['vendor_orders_products'][$index] = array (
        'Pqty' => $vendor_orders_products['products_quantity'],
        'Pname' => $vendor_orders_products['products_name'],
        'Pmodel' => $vendor_orders_products['products_model'],
        'Pprice' => $vendor_orders_products['products_price'],
        'Pvendor_name' => $vendor_orders_products['vendors_name'],
        'Pcomments' => $vendor_orders_products['vendors_prod_comments'],
        'PVprod_id' => $vendor_orders_products['vendors_prod_id'],
        'PVprod_price' => $vendor_orders_products['vendors_product_price'],
        'spacer' => '-'
      );

      if ($debug == 'yes') {
        echo 'The products query: ' . $vendor_orders_products['products_name'] . '<br>';
      }
      $subindex = 0;
      $vendor_attributes_query = tep_db_query("select products_options, products_options_values, options_values_price, price_prefix from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . (int) $oID . "' and orders_products_id = '" . (int) $vendor_orders_products['orders_products_id'] . "'");
      if (tep_db_num_rows($vendor_attributes_query)) {
        while ($vendor_attributes = tep_db_fetch_array($vendor_attributes_query)) {
          $vendor_products[$index2]['vendor_orders_products'][$index]['vendor_attributes'][$subindex] = array (
            'option' => $vendor_attributes['products_options'],
            'value' => $vendor_attributes['products_options_values'],
            'prefix' => $vendor_attributes['price_prefix'],
            'price' => $vendor_attributes['options_values_price']
          );

          $subindex++;
        }
      }
      $index++;
    }
    $index2++;
    // let's build the email
    // Get the delivery address
    $delivery_address_query = tep_db_query("select distinct delivery_company, delivery_name, delivery_street_address, delivery_city, delivery_state, delivery_postcode from " . TABLE_ORDERS . " where orders_id='" . $oID . "'");
    $vendor_delivery_address_list = tep_db_fetch_array($delivery_address_query);

    if ($debug == 'yes') {
      echo 'The number of vendors: ' . sizeof($vendor_products) . '<br>';
    }
    $email = '';
    for ($l = 0, $m = sizeof($vendor_products); $l < $m; $l++) {
      $vendor_country = tep_get_country_name($vendor_products[$l]['Vcountry']);
      $order_number = $oID;
      $vendors_id = $vendors_id;
      $the_email = $vendor_products[$l]['Vemail'];
      $the_name = $vendor_products[$l]['Vname'];
      $the_contact = $vendor_products[$l]['Vcontact'];
      $email = '<b>To: ' . $the_contact . '  <br>' . $the_name . '<br>' . $the_email . '<br>' .
      $vendor_products[$l]['Vstreet'] . '<br>' .
      $vendor_products[$l]['Vcity'] . ', ' .
      $vendor_products[$l]['Vstate'] . '  ' .
      $vendor_products[$l]['Vzipcode'] . ' ' . $vendor_country . '<br>' . '<br>' . EMAIL_SEPARATOR . '<br>' . 'Special Comments or Instructions:  ' . $vendor_products[$l]['Vinstructions'] . '<br>' . '<br>' . EMAIL_SEPARATOR . '<br>' . 'From: ' . STORE_OWNER . '<br>' . STORE_NAME_ADDRESS . '<br>' . 'Accnt #: ' . $vendor_products[$l]['Vaccount'] . '<br>' . EMAIL_SEPARATOR . '<br>' . EMAIL_TEXT_ORDER_NUMBER . ' ' . $oID . '<br>' . EMAIL_SEPARATOR . '<br>' . '<br>' . EMAIL_SEPARATOR . '<br> Shipping Method: ' . $vendor_products[$l]['Vmodule'] . ' -- ' . $vendor_products[$l]['Vmethod'] . '<br>' . EMAIL_SEPARATOR . '<br>' . '<br>Dropship deliver to:<br>' .
      $vendor_delivery_address_list['delivery_company'] . '<br>' .
      $vendor_delivery_address_list['delivery_name'] . '<br>' .
      $vendor_delivery_address_list['delivery_street_address'] . '<br>' .
      $vendor_delivery_address_list['delivery_city'] . ', ' .
      $vendor_delivery_address_list['delivery_state'] . ' ' . $vendor_delivery_address_list['delivery_postcode'] . '<br><br>';
      $email .= '<table width="100%" border=1 cellspacing="0" cellpadding="3">
                 <tr><td>Qty:</td><td>Product Name:</td><td>Item Code/Number:</td><td>Product Model:</td><td>Per Unit Price:</td><td>Item Comments: </td></tr>';
      for ($i = 0, $n = sizeof($vendor_products[$l]['vendor_orders_products']); $i < $n; $i++) {
        $product_attribs = '';
        if (isset ($vendor_products[$l]['vendor_orders_products'][$i]['vendor_attributes']) && (sizeof($vendor_products[$l]['vendor_orders_products'][$i]['vendor_attributes']) > 0)) {
          $product_attribs .= '&nbsp;&nbsp;&nbsp;<i>Options<br>';
          for ($j = 0, $k = sizeof($vendor_products[$l]['vendor_orders_products'][$i]['vendor_attributes']); $j < $k; $j++) {
            $product_attribs .= '&nbsp;&nbsp;&nbsp;' . $vendor_products[$l]['vendor_orders_products'][$i]['vendor_attributes'][$j]['option'] . ': ' . $vendor_products[$l]['vendor_orders_products'][$i]['vendor_attributes'][$j]['value'] . '<br>';
          }
        }
        $email .= '<tr><td>&nbsp;' . $vendor_products[$l]['vendor_orders_products'][$i]['Pqty'] .
                  '</td><td>&nbsp;' . $vendor_products[$l]['vendor_orders_products'][$i]['Pname'] . '<br>' . $product_attribs .
                  '</td><td>&nbsp;' . $vendor_products[$l]['vendor_orders_products'][$i]['PVprod_id'] .
                  '</td><td>&nbsp;' . $vendor_products[$l]['vendor_orders_products'][$i]['Pmodel'] .
                  '</td><td>&nbsp;' . $vendor_products[$l]['vendor_orders_products'][$i]['PVprod_price'] . '</td><td>' .
        $vendor_products[$l]['vendor_orders_products'][$i]['Pcomments'] . '</b></td></tr>';

      }
    }
    $email = $email . '</table><br><HR><br>';

    if ($debug == 'yes') {
      echo 'The $email(including headers:<br>Vendor Email Addy' . $the_email . '<br>Vendor Name' . $the_name . '<br>Vendor Contact' . $the_contact . '<br>Body--<br>' . $email . '<br>';
    }
  }

  if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'success')) {
?>
            </td>
          </tr>
          <tr>
            <td class="main"><?php echo '<b>Congratulations!  The email has been sent to <big>' . $_GET['contact'] . ' </b></big><br>For order number <b>' . $oID . '</b>';?></td>
            <td class="pageHeading" align="left">
			<?php echo tep_draw_button(IMAGE_BACK, 'triangle-1-w', tep_href_link(FILENAME_ORDERS,'&action=edit&oID=' . $oID ) ); ?>
                     
            </td>
           </tr>
   	         
<?php

  }
   	 
  if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'preview')) {  
?>
            </td>
          </tr>
          <tr><?php echo tep_draw_form('mail', FILENAME_VENDORS_EMAIL_SEND, 'action=send_vendor_email'); ?>
            <td><table border="0" width="100%" cellpadding="0" cellspacing="2">
              <tr>
                <td class="main"><?php echo 'The email will look like this: <br>'; ?></td>
                <td align="center">
				<?php echo tep_draw_button(IMAGE_BACK, 'triangle-1-w', tep_href_link(FILENAME_VENDORS_EMAIL_SEND, '&vID=' . $_GET['vID'] . '&oID=' . $oID . '&vOS=' . $vOS) ); ?>
                <?php echo tep_draw_button(IMAGE_CANCEL, 'close', tep_href_link(FILENAME_ORDERS, 'action=edit&oID=' . $oID)); ?>
                </td>
              </tr>
              <tr>
                <td colspan="3"><?php echo tep_draw_separator(); ?></td>
              </tr>
<?php if ($debug == 'yes') { ?>
              <tr>
                <td colspan="3"><?php echo $order_number . $the_email . $the_name . $the_contact; ?></td>
              </tr>
<?php

  }
  $email = stripslashes($HTTP_POST_VARS['email']);
  echo '<tr><td><br>' . $email . '</tr>';
?>
              <tr>
                <td colspan="3"><?php echo tep_draw_separator(); ?></td>
              </tr>
              <tr>
                <td class="main"><br><br>  <?php echo tep_draw_hidden_field ('email', $email);?>
                  <?php echo tep_draw_hidden_field('order_number', stripslashes($order_number));?>
                  <?php echo tep_draw_hidden_field('the_email',  stripslashes($the_email));?>
                  <?php echo tep_draw_hidden_field('the_name', stripslashes($the_name));?>
                  <?php echo tep_draw_hidden_field('the_contact', stripslashes($the_contact));?>
                  <?php echo tep_draw_hidden_field('vID', stripslashes($vID));?></td>
              </tr>
              <tr>
                <td align="right"><?php echo tep_draw_button(IMAGE_SEND_EMAIL, 'mail-closed', null, 'primary'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></form></td>
      <tr>
<?php } else { ?>
<?php if ($HTTP_GET_VARS['action'] != 'success'){ ?>

      <tr><?php echo tep_draw_form('mail', FILENAME_VENDORS_EMAIL_SEND, 'action=preview' . '&vID=' . $vendors_id . '&oID=' . $oID . '&vOS=' . $vOS); ?>
        <td><table border="0" width="100%" cellpadding="0" cellspacing="2">
          <tr>
            <td class="main"><?php echo 'The body of the email will look like this, this is what your Vendor will see when they open the email: <br>';?></td>
            <td align="center"><?php echo tep_draw_button(IMAGE_BACK, 'triangle-1-w', tep_href_link(FILENAME_ORDERS, 'action=edit&oID=' . $oID)); ?></td>
          </tr>
          <tr>
            <td colspan="3"><?php echo tep_draw_separator(); ?></td>
          </tr>
<?php  echo '<tr><td><br>' . stripslashes($email); ?></td></tr>
          <tr>
            <td class="main"><?php echo 'You may edit the email here: <br><br>'; ?></td>
          </tr>
          <tr>
            <td colspan="3"><?php echo tep_draw_separator(); ?></td>
          </tr>
          <tr>
            <td class="main"><br><br><p>***** Please note that the email is formatted with HTML. You can <b>ONLY</b> edit the <b>BODY</b> of the email here. You <b>CANNOT</b> edit the address the email will go to. <br><br>***** You may edit the HTML or just the text within the tags. Be sure to maintain  the HTML tags, if you are not sure what to change and what not to, don't mess it up, do a little research and find out the basics of HTML.</p><br>  <?php echo tep_draw_textarea_field('email', 'soft', '120', '25', stripslashes($email)); ?></td>
          </tr>
          <tr>
            <td align="right"><?php echo tep_draw_hidden_field('vID', $vID);?>
              <?php echo tep_draw_hidden_field('oID', $oID);?>
              <?php echo tep_draw_hidden_field('vOS', $vOS);?>
              <?php echo  tep_draw_button(IMAGE_PREVIEW, 'document', null, 'primary'); ?></td>
          </tr>
        </table></td>
      </tr>
    </table></form></td>
<?php

  }
}
?>

<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require_once(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require_once(DIR_WS_INCLUDES . 'application_bottom.php'); ?>