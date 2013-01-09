<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

  $oID = tep_db_prepare_input($HTTP_GET_VARS['oID']);
  $orders_query = tep_db_query("select orders_id from " . TABLE_ORDERS . " where orders_id = '" . (int)$oID . "'");

  include(DIR_WS_CLASSES . 'order.php');
  $order = new order($oID);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<body>

<!-- body_text //-->
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td class="pageHeading"><?php echo nl2br(STORE_NAME_ADDRESS); ?></td>
        <td class="pageHeading" align="right"><?php echo tep_image(DIR_WS_CATALOG_IMAGES . 'store_logo.png', STORE_NAME); ?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td colspan="2"><?php echo tep_draw_separator(); ?></td>
      </tr>
      <tr>
        <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><strong><?php echo ENTRY_SOLD_TO; ?></strong></td>
          </tr>
          <tr>
            <td class="main"><?php echo tep_address_format($order->customer['format_id'], $order->billing, 1, '', '<br />'); ?></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo $order->customer['telephone']; ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo '<a href="mailto:' . $order->customer['email_address'] . '"><u>' . $order->customer['email_address'] . '</u></a>'; ?></td>
          </tr>
        </table></td>
        <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><strong><?php echo ENTRY_SHIP_TO; ?></strong></td>
          </tr>
          <tr>
            <td class="main"><?php echo tep_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br />'); ?></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
    <td><table border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td class="main"><strong><?php echo ENTRY_PAYMENT_METHOD; ?></strong></td>
        <td class="main"><?php echo $order->info['payment_method']; ?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <?php 
// Start MVS
  if (tep_not_null($order->orders_shipping_id)) {  
?>
    <td><table border="1" width="100%" cellspacing="0" cellpadding="2">
      <tr class="dataTableHeadingRow">
        <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_PRODUCTS_VENDOR; ?></td>
        <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
        <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_VENDORS_SHIP; ?></td>
        <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_SHIPPING_METHOD; ?></td>
        <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_SHIPPING_COST; ?></td>
        <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
        <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_TAX; ?></td>
        <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_PRICE_EXCLUDING_TAX; ?></td>
        <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_PRICE_INCLUDING_TAX; ?></td>
        <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_TOTAL_EXCLUDING_TAX; ?></td>
        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_INCLUDING_TAX; ?></td>
      </tr>
<?php
    $package_num = sizeof ($order->products);
    $box_num = $l + 1;
    echo '      <tr>' . "\n" .
         '        <td class="dataTableContent" colspan=11>There will be <b>at least ' . $package_num . '</b><br>packages shipped.</td>' . "\n" .
         '      </tr>' . "\n";
    for ($l=0, $m=sizeof($order->products); $l<$m; $l++) {
      echo '      <tr class="dataTableRow">' . "\n" .
           '        <td class="dataTableContent" valign="center">Shipment Number ' . $box_num++ . '</td>' . "\n" .
           '        <td class="dataTableContent" valign="center" align="center">' . $order->products[$l]['spacer'] . '</td>' . "\n" .
           '        <td class="dataTableContent" valign="center" align="center">' . $order->products[$l]['Vmodule'] . '</td>' . "\n" .
           '        <td class="dataTableContent" valign="center" align="center">' . $order->products[$l]['Vmethod'] . '</td>' . "\n" .
           '        <td class="dataTableContent" valign="center" align="center">' . $order->products[$l]['Vcost'] . '</td>' . "\n" .
           '        <td class="dataTableContent" valign="center" align="center">' . $order->products[$l]['spacer'] . '</td>' . "\n" .
           '        <td class="dataTableContent" valign="center" align="center">ship tax<br>' . $order->products[$l]['Vship_tax'] . '</td>' . "\n" .
           '        <td class="dataTableContent" valign="center" align="center">' . $order->products[$l]['spacer'] . '</td>' . "\n" .
           '        <td class="dataTableContent" valign="center" align="center">' . $order->products[$l]['spacer'] . '</td>' . "\n" .
           '        <td class="dataTableContent" valign="center" align="center">' . $order->products[$l]['spacer'] . '</td>' . "\n" .
           '        <td class="dataTableContent" valign="center" align="center">' . $order->products[$l]['spacer'] . '</td>' . "\n" .
           '      </tr>' . "\n";
      for ($i=0, $n=sizeof($order->products[$l]['orders_products']); $i<$n; $i++) {
        echo '      <tr>' . "\n" .
             '        <td class="dataTableContent" valign="center" align="right">' . $order->products[$l]['orders_products'][$i]['qty'] . '&nbsp;x</td>' . "\n" .
             '        <td class="dataTableContent" valign="center" align="left">' . $order->products[$l]['orders_products'][$i]['name'] . "\n";

        if (isset ($order->products[$l]['orders_products'][$i]['attributes']) && (sizeof ($order->products[$l]['orders_products'][$i]['attributes']) > 0)) {
          for ($j = 0, $k = sizeof($order->products[$l]['orders_products'][$i]['attributes']); $j < $k; $j++) {
            echo '          <br><nobr><small>&nbsp;<i> - ' . $order->products[$l]['orders_products'][$i]['attributes'][$j]['option'] . ': ' . $order->products[$l]['orders_products'][$i]['attributes'][$j]['value'];
            if ($order->products[$l]['orders_products'][$i]['attributes'][$j]['price'] != '0') echo ' (' . $order->products[$l]['orders_products'][$i]['attributes'][$j]['prefix'] . $currencies->format($order->products[$l]['orders_products'][$i]['attributes'][$j]['price'] * $order->products[$l]['orders_products'][$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . ')';
            echo '</i></small></nobr>';
          } 
        }

        echo '        <td class="dataTableContent" valign="center" align="center">' . $order->products[$l]['orders_products'][$i]['spacer'] . '</td>' . "\n" .
             '        <td class="dataTableContent" valign="center" align="center">' . $order->products[$l]['orders_products'][$i]['spacer'] . '</td>' . "\n" .
             '        <td class="dataTableContent" valign="center" align="center">' . $order->products[$l]['orders_products'][$i]['spacer'] . '</td>' . "\n" .
             '        <td class="dataTableContent" valign="center" align="center">' . $order->products[$l]['orders_products'][$i]['model'] . '</td>' . "\n" .
             '        <td class="dataTableContent" align="center" valign="center">' . tep_display_tax_value($order->products[$l]['orders_products'][$i]['tax']) . '%</td>' . "\n" .
             '        <td class="dataTableContent" align="center" valign="center"><b>' . $currencies->format($order->products[$l]['orders_products'][$i]['final_price'], true, $order->info['currency'], $order->info['currency_value']) . '</b></td>' . "\n" .
             '        <td class="dataTableContent" align="center" valign="center"><b>' . $currencies->format(tep_add_tax($order->products[$l]['orders_products'][$i]['final_price'], $order->products[$l]['orders_products'][$i]['tax']), true, $order->info['currency'], $order->info['currency_value']) . '</b></td>' . "\n" .
             '        <td class="dataTableContent" align="center" valign="center"><b>' . $currencies->format($order->products[$l]['orders_products'][$i]['final_price'] * $order->products[$l]['orders_products'][$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</b></td>' . "\n" .
             '        <td class="dataTableContent" align="right" valign="center"><b>' .  $currencies->format(tep_add_tax($order->products[$l]['orders_products'][$i]['final_price'], $order->products[$l]['orders_products'][$i]['tax']) * $order->products[$l]['orders_products'][$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</b></td>' . "\n";
        echo '      </tr>';
      } //for ($i=0, $n=sizeof($order->products
    }
?>
          <tr>
            <td align="right" colspan="12"><table border="0" cellspacing="0" cellpadding="2">
<?php
  } else {   
// End MVS 
?>
  <tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr class="dataTableHeadingRow">
        <td class="dataTableHeadingContent" colspan="2"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
        <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TAX; ?></td>
        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_EXCLUDING_TAX; ?></td>
        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_INCLUDING_TAX; ?></td>
        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_EXCLUDING_TAX; ?></td>
        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_INCLUDING_TAX; ?></td>
      </tr>
<?php
    for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
      echo '      <tr class="dataTableRow">' . "\n" .
           '        <td class="dataTableContent" valign="top" align="right">' . $order->products[$i]['qty'] . '&nbsp;x</td>' . "\n" .
           '        <td class="dataTableContent" valign="top">' . $order->products[$i]['name'];

      if (isset($order->products[$i]['attributes']) && (($k = sizeof($order->products[$i]['attributes'])) > 0)) {
        for ($j = 0; $j < $k; $j++) {
          echo '<br /><nobr><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'];
          if ($order->products[$i]['attributes'][$j]['price'] != '0') echo ' (' . $order->products[$i]['attributes'][$j]['prefix'] . $currencies->format($order->products[$i]['attributes'][$j]['price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . ')';
          echo '</i></small></nobr>';
        }
      }

      echo '        </td>' . "\n" .
           '        <td class="dataTableContent" valign="top">' . $order->products[$i]['model'] . '</td>' . "\n";
      echo '        <td class="dataTableContent" align="right" valign="top">' . tep_display_tax_value($order->products[$i]['tax']) . '%</td>' . "\n" .
           '        <td class="dataTableContent" align="right" valign="top"><strong>' . $currencies->format($order->products[$i]['final_price'], true, $order->info['currency'], $order->info['currency_value']) . '</strong></td>' . "\n" .
           '        <td class="dataTableContent" align="right" valign="top"><strong>' . $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax'], true), true, $order->info['currency'], $order->info['currency_value']) . '</strong></td>' . "\n" .
           '        <td class="dataTableContent" align="right" valign="top"><strong>' . $currencies->format($order->products[$i]['final_price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</strong></td>' . "\n" .
           '        <td class="dataTableContent" align="right" valign="top"><strong>' . $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax'], true) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</strong></td>' . "\n";
      echo '      </tr>' . "\n";
    }
?>
      <tr>
        <td align="right" colspan="8"><table border="0" cellspacing="0" cellpadding="2">
        <?php
// MVS
  }
?>
<?php
  for ($i = 0, $n = sizeof($order->totals); $i < $n; $i++) {
    echo '          <tr>' . "\n" .
         '            <td align="right" class="smallText">' . $order->totals[$i]['title'] . '</td>' . "\n" .
         '            <td align="right" class="smallText">' . $order->totals[$i]['text'] . '</td>' . "\n" .
         '          </tr>' . "\n";
  }
?>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<!-- body_text_eof //-->

<br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
